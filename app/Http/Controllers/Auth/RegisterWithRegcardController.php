<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Smalot\PdfParser\Parser;
use Throwable;

class RegisterWithRegcardController extends Controller
{
    public function store(Request $req)
    {
        $req->validate([
            'full_name'    => ['required','string','max:150'],
            'email'        => ['required','string','email','max:191', Rule::unique('users','email')],
            'student_no'   => ['required','string','max:20', Rule::unique('students','student_number')],
            'password'     => ['required','string','min:8','confirmed'],
            'regcard_file' => ['required','file','mimes:pdf,jpg,jpeg,png','max:8192'],
        ]);

        $term = DB::table('academic_terms')->where('is_current', 1)->first();
        if (!$term) {
            return back()->withErrors(['register' => 'We could not complete verification at this time. Please try again later.'])->withInput();
        }

        // --- 1) TEMP save the uploaded file; extract text & parse meta ---
        $upload  = $req->file('regcard_file');
        $ext     = strtolower($upload->getClientOriginalExtension());
        $orig    = $upload->getClientOriginalName();

        $tmpDir      = 'tmp_regcards';
        $tmpFilename = uniqid('rc_', true).'.'.$ext;
        Storage::disk('local')->putFileAs($tmpDir, $upload, $tmpFilename);
        $absTmpPath  = Storage::disk('local')->path("$tmpDir/$tmpFilename");

        try {
            $text = $this->extractText($absTmpPath, $ext);
            $meta = $this->parseMeta($text);

            // --- DEBUG (temporary) ---
            Log::info('REGCARD_META', [
                'meta'       => $meta,
                'term'       => [
                    'semester' => (int)$term->semester,
                    'ay_start' => (int)$term->ay_start,
                    'ay_end'   => (int)$term->ay_end,
                ],
                'file'       => ['name' => $orig, 'ext' => $ext],
                'text_first' => mb_substr($text ?? '', 0, 600),
            ]);
            // --- /DEBUG ---

            // Compute term checks only if AY+Sem were actually detected in the PDF
            $hasTerm = !empty($meta['semester']) && !empty($meta['ay_start']) && !empty($meta['ay_end']);
            $termOk  = $hasTerm
                && (int)$meta['semester'] === (int)$term->semester
                && (int)$meta['ay_start'] === (int)$term->ay_start
                && (int)$meta['ay_end']   === (int)$term->ay_end;

            $yearOk = in_array((int)$meta['year'], [2,3,4], true);

            // PASS RULE:
            // - Must be BSN and year 2–4, and
            // - If AY/Sem were present in the PDF, they must match current term.
            $passes = ($meta['is_bsn'] === true) && $yearOk && ($termOk || !$hasTerm);

            if (!$passes) {
                return back()
                    ->withErrors(['register' => 'We could not complete verification at this time. Please try again later.'])
                    ->withInput();
            }
        } finally {
            // Cleanup temp file
            try { @unlink($absTmpPath); } catch (\Throwable $e) {}
        }

        // --- 2) Create user + student + file record atomically, then activate ---
        try {
            DB::beginTransaction();

            $userId = DB::table('users')->insertGetId([
                'name'       => (string) $req->input('full_name'),
                'email'      => (string) $req->input('email'),
                'password'   => Hash::make((string) $req->input('password')),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $studentId = DB::table('students')->insertGetId([
                'student_number' => (string) $req->input('student_no'),
                'full_name'      => (string) $req->input('full_name'),
                'program'        => 'Bachelor of Science in Nursing',
                'year_level'     => (int) ($meta['year'] ?? 0),
                'section'        => null,
                'email'          => (string) $req->input('email'),
                'is_active'      => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // Store reg-card PERMANENTLY on the PUBLIC disk
            $dir        = "regcards/{$term->code}";
            $filename   = (string) $req->input('student_no') . '.' . $ext;
            Storage::disk('public')->putFileAs($dir, $upload, $filename);
            $publicPath = $dir.'/'.$filename;
            $abs        = Storage::disk('public')->path($publicPath);
            $sha256     = hash_file('sha256', $abs);

            $fileId = DB::table('regcard_files')->insertGetId([
                'student_id'        => $studentId,
                'term_id'           => $term->id,
                'original_filename' => $orig,
                'storage_path'      => $publicPath,
                'mime_type'         => $upload->getMimeType(),
                'size_bytes'        => $upload->getSize(),
                'sha256'            => $sha256,
                'uploaded_at'       => now(),
            ]);

            DB::table('student_semester_statuses')->updateOrInsert(
                ['student_id' => $studentId, 'term_id' => $term->id],
                [
                    'status'          => 'active',
                    'reason'          => null,
                    'regcard_file_id' => $fileId,
                    'ocr_json'        => json_encode($meta),
                    'validated_by'    => null,
                    'validated_at'    => now(),
                    'expires_at'      => now()->addMonths(4),
                    'updated_at'      => now(),
                    'created_at'      => now(),
                ]
            );

            DB::table('validation_audit_log')->insert([
                'student_id' => $studentId,
                'term_id'    => $term->id,
                'action'     => 'auto_approved',
                'actor_type' => 'system',
                'actor_id'   => null,
                'details'    => json_encode(['file_id'=>$fileId,'phase'=>'register','checks'=>'bsn+year+term']),
                'created_at' => now(),
            ]);

            DB::commit();

            Auth::loginUsingId($userId);
            return redirect('/student/dashboard')->with('success', 'Welcome to NurSync.');
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors(['register' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    /**
     * Pure-PHP text extraction (no exec/shell).
     * - PDF: Smalot\PdfParser
     * - Images: no OCR (fail-closed)
     */
    private function extractText(string $absPath, string $ext): string
    {
        if (strtolower($ext) === 'pdf') {
            try {
                $parser = new Parser();
                $pdf    = $parser->parseFile($absPath);
                $text   = $pdf->getText();
                return is_string($text) ? trim($text) : '';
            } catch (\Throwable $e) {
                // soft fail; keep it empty so checks fail-closed
                return '';
            }
        }
        return '';
    }

    /**
     * Parse useful metadata from the reg-card text.
     * Returns: ['is_bsn'=>bool,'year'=>int|null,'semester'=>1|2|null,'ay_start'=>int|null,'ay_end'=>int|null]
     */
    private function parseMeta(?string $text): array
    {
        $text = $text ?? '';
        $hay  = mb_strtolower($text);

        // Program checks (cover common variants)
        $isBsn =
            strpos($hay, 'bachelor of science in nursing') !== false ||
            preg_match('/\bbsn\b/i', $text) === 1 ||
            preg_match('/\bbs\s*nurs/i', $text) === 1 ||   // "BS Nursing"
            strpos($hay, 'college of nursing') !== false;

        // Semester variants
        $semester = null;
        if (preg_match('/\b(First|Second)\s+Semester\b/i', $text, $m)) {
            $semester = strtolower($m[1]) === 'first' ? 1 : 2;
        } elseif (preg_match('/\b(1st|2nd)\s*Sem(?:ester)?\b/i', $text, $m)) {
            $semester = (strpos(strtolower($m[1]), '1') !== false) ? 1 : 2;
        } elseif (preg_match('/\b(?:Sem|Semester|Term)\s*[:\-]?\s*(1|2)\b/i', $text, $m)) {
            $semester = (int)$m[1];
        }

        // Academic Year variants
        $ayStart = null; $ayEnd = null;
        if (preg_match('/\b(?:AY|A\.?Y\.?|Academic\s*Year)\s*[:\-]?\s*(\d{4})\s*[-–—]\s*(\d{4})\b/i', $text, $m)) {
            $ayStart = (int)$m[1];
            $ayEnd   = (int)$m[2];
        }

        // Year level variants
        $year = null;
        if (preg_match('/Year\s*Level\s*[:\-]?\s*(First|Second|Third|Fourth)\s*Year/i', $text, $m)
            || preg_match('/\b(First|Second|Third|Fourth)\s*Year\b/i', $text, $m)) {
            $map = ['first'=>1,'second'=>2,'third'=>3,'fourth'=>4];
            $year = $map[strtolower($m[1])] ?? null;
        } elseif (preg_match('/\b([1-4])(?:st|nd|rd|th)?\s*(?:Year|Yr)\b/i', $text, $m)) {
            $year = (int)$m[1];
        } elseif (preg_match('/\bYr\s*[:\-]?\s*([1-4])\b/i', $text, $m)) {
            $year = (int)$m[1];
        }

        return [
            'is_bsn'   => (bool) $isBsn,
            'year'     => $year,
            'semester' => $semester,
            'ay_start' => $ayStart,
            'ay_end'   => $ayEnd,
        ];
    }
}
