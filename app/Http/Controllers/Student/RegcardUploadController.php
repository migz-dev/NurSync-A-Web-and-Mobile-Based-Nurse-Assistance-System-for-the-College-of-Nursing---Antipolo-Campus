<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class RegcardUploadController extends Controller
{
    public function store(Request $req)
    {
        // 1) Resolve current student + current term
        $userEmail = optional($req->user())->email;
        $student   = DB::table('students')->where('email', $userEmail)->first();
        if (!$student) {
            return back()->with('error', 'We could not complete verification at this time. Please try again later.');
        }

        $term = DB::table('academic_terms')->where('is_current', 1)->first();
        if (!$term) {
            return back()->with('error', 'We could not complete verification at this time. Please try again later.');
        }

        // 2) Validate upload
        $data = $req->validate([
            'file' => ['required','file','mimes:pdf,jpg,jpeg,png','max:8192'],
        ]);
        $file = $data['file'];
        $ext  = strtolower($file->getClientOriginalExtension());
        $orig = $file->getClientOriginalName();
        $mime = $file->getMimeType();
        $size = $file->getSize();

        // 3) Gate on a TEMP copy: BSN + Year(2–4) + AY/Sem must match current term
        $tmpDir      = 'tmp_regcards';
        $tmpFilename = uniqid('rc_', true).'.'.$ext;
        $tmpPath     = Storage::disk('local')->putFileAs($tmpDir, $file, $tmpFilename);
        $absTmpPath  = Storage::disk('local')->path($tmpDir.'/'.$tmpFilename);

        try {
            $text = $this->extractText($absTmpPath, $ext);
            $meta = $this->parseMeta($text);

            $termOk = $meta['semester'] === (int)$term->semester
                && $meta['ay_start'] !== null && $meta['ay_end'] !== null
                && (int)$meta['ay_start'] === (int)$term->ay_start
                && (int)$meta['ay_end']   === (int)$term->ay_end;

            $yearOk = in_array((int)$meta['year'], [2,3,4], true);

            if (!($meta['is_bsn'] && $yearOk && $termOk)) {
                // Generic reject; do NOT store permanently
                DB::table('student_semester_statuses')->updateOrInsert(
                    ['student_id' => $student->id, 'term_id' => $term->id],
                    [
                        'status'     => 'rejected',
                        'reason'     => null, // keep generic
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

                DB::table('validation_audit_log')->insert([
                    'student_id' => $student->id,
                    'term_id'    => $term->id,
                    'action'     => 'auto_rejected',
                    'actor_type' => 'system',
                    'actor_id'   => null,
                    'details'    => json_encode(['cause' => 'gate_failed', 'at' => 'revalidate_upload']),
                    'created_at' => now(),
                ]);

                return back()->with('error', 'We could not complete verification at this time. Please try again later.');
            }
        } finally {
            // Always delete temp
            try { @unlink($absTmpPath); } catch (\Throwable $e) {}
        }

        // 4) Proceed (all checks passed): save permanently + replace previous + activate with expiry
        try {
            DB::beginTransaction();

            $dir        = "regcards/{$term->code}";
            $filename   = $student->student_number.'.'.$ext;
            $publicPath = $dir.'/'.$filename;

            // store to public disk
            Storage::disk('public')->putFileAs($dir, $file, $filename);
            $abs    = Storage::disk('public')->path($publicPath);
            $sha256 = hash_file('sha256', $abs);

            // previous file (mark as replaced)
            $prevId = DB::table('regcard_files')
                ->where('student_id', $student->id)
                ->where('term_id', $term->id)
                ->whereNull('replaced_by_id')
                ->orderByDesc('uploaded_at')
                ->value('id');

            $fileId = DB::table('regcard_files')->insertGetId([
                'student_id'        => $student->id,
                'term_id'           => $term->id,
                'original_filename' => $orig,
                'storage_path'      => $publicPath,   // relative path; use asset('storage/'.$path)
                'mime_type'         => $mime,
                'size_bytes'        => $size,
                'sha256'            => $sha256,
                'uploaded_at'       => now(),
                'replaced_by_id'    => null,
            ]);

            if ($prevId) {
                DB::table('regcard_files')->where('id', $prevId)->update([
                    'replaced_by_id' => $fileId,
                ]);
            }

            // Activate and set 4-month expiry; keep parsed meta
            DB::table('student_semester_statuses')->updateOrInsert(
                ['student_id' => $student->id, 'term_id' => $term->id],
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
                'student_id' => $student->id,
                'term_id'    => $term->id,
                'action'     => 'auto_approved',
                'actor_type' => 'system',
                'actor_id'   => null,
                'details'    => json_encode([
                    'file_id' => $fileId,
                    'replaced_previous' => (bool)$prevId,
                    'checks'  => 'bsn+year+term'
                ]),
                'created_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Upload received. Your account is now active for the current semester.');

        } catch (Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Upload failed. Please try again.');
        }
    }

    /**
     * Best-effort text extraction for the gate.
     * PDF: try 'pdftotext'; fallback to raw bytes (text layer).
     * Images: returns empty (no OCR) → fail-closed.
     */
    private function extractText(string $absPath, string $ext): string
    {
        $ext = strtolower($ext);

        if ($ext === 'pdf') {
            $pdftotext = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'pdftotext.exe' : 'pdftotext';
            @exec("{$pdftotext} -v", $out, $ok);
            if ($ok === 0) {
                $txtFile = $absPath.'.txt';
                @exec("{$pdftotext} -layout -nopgbrk ".escapeshellarg($absPath).' '.escapeshellarg($txtFile));
                if (is_file($txtFile)) {
                    $txt = @file_get_contents($txtFile) ?: '';
                    @unlink($txtFile);
                    return $txt;
                }
            }
            $bytes = @file_get_contents($absPath) ?: '';
            $bytes = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]+/u', ' ', $bytes);
            return $bytes ?? '';
        }

        // jpg/png branch: no OCR (yet)
        return '';
    }

    /**
     * Parse reg-card metadata.
     * Returns: ['is_bsn'=>bool,'year'=>int|null,'semester'=>1|2|null,'ay_start'=>int|null,'ay_end'=>int|null]
     */
    private function parseMeta(?string $text): array
    {
        $text = $text ?? '';
        $hay  = mb_strtolower($text);

        $isBsn = (strpos($hay, 'bachelor of science in nursing') !== false) || (preg_match('/\bbsn\b/i', $text) === 1);

        $semester = null;
        if (preg_match('/\b(First|Second)\s+Semester\b/i', $text, $m)) {
            $semester = strtolower($m[1]) === 'first' ? 1 : 2;
        }

        $ayStart = null; $ayEnd = null;
        if (preg_match('/AY\s*(\d{4})\s*[-–]\s*(\d{4})/i', $text, $m)) {
            $ayStart = (int) $m[1];
            $ayEnd   = (int) $m[2];
        }

        $year = null;
        if (preg_match('/Year\s*Level\s*:\s*(First|Second|Third|Fourth)\s+Year/i', $text, $m)
            || preg_match('/\b(First|Second|Third|Fourth)\s+Year\b/i', $text, $m)
            || preg_match('/\b([2-4])(?:st|nd|rd|th)?\s*Year\b/i', $text, $m)) {
            $map = ['first'=>1,'second'=>2,'third'=>3,'fourth'=>4];
            $key = strtolower($m[1]);
            $year = is_numeric($key) ? (int)$key : ($map[$key] ?? null);
        }

        return [
            'is_bsn'   => $isBsn,
            'year'     => $year,
            'semester' => $semester,
            'ay_start' => $ayStart,
            'ay_end'   => $ayEnd,
        ];
    }
}
