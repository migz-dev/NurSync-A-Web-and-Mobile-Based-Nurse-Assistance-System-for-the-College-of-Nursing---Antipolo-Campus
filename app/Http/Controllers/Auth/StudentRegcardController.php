<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\{AcademicTerm, Student, RegcardFile, StudentSemesterStatus};
use App\Services\RegcardValidator;

class StudentRegcardController extends Controller
{
    public function show(Request $r)
    {
        return view('auth.studentregcard-revalidate');
    }

    public function store(Request $r)
    {
        $r->validate([
            'regcard_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'birthdate'    => 'nullable|date',
        ]);

        $user = auth('web')->user();
        abort_if(!$user, 403, 'Not authenticated');

        /** @var \App\Models\Student $student */
        $student   = Student::where('email', $user->email)->firstOrFail();
        $studentId = (int) $student->id;

        // Make sure the validator has a number to compare
        $student->student_number = $student->student_number
            ?? $student->student_no
            ?? $student->studno
            ?? $student->studentid
            ?? ($user->student_number ?? '');

        /** @var \App\Models\AcademicTerm $term */
        $term = AcademicTerm::where('is_current', 1)->firstOrFail();

        $termCodeForPath = ($term->code ?? null)
            ?: ($term->name ?? null)
            ?: ((isset($term->ay_start, $term->ay_end, $term->semester))
                ? "{$term->ay_start}-{$term->ay_end}_{$term->semester}"
                : "term_{$term->id}");
        $termCodeForPath = preg_replace('/[^\w\-\.]+/u', '_', $termCodeForPath);

        $fileUpload = $r->file('regcard_file');
        $dir        = "regcards/{$termCodeForPath}/{$studentId}";
        $path       = $fileUpload->store($dir, 'public');

        $file = RegcardFile::create([
            'student_id'        => $studentId,
            'term_id'           => $term->id,
            'original_filename' => $fileUpload->getClientOriginalName(),
            'storage_path'      => $path,
            'mime_type'         => $fileUpload->getClientMimeType(),
            'size_bytes'        => $fileUpload->getSize(),
            'sha256'            => hash_file('sha256', $fileUpload->getRealPath()),
        ]);

        $status = StudentSemesterStatus::firstOrCreate(
            ['student_id' => $studentId, 'term_id' => $term->id],
            ['status' => 'pending_review']
        );

        $status->regcard_file_id = $file->id;

        if (Schema::hasColumn('student_semester_statuses', 'submitted_at')) {
            $status->submitted_at = now();
        }

        // Auto-validate
        $result = app(RegcardValidator::class)->validate($path, $student, $term);

        if (($result['ok'] ?? false) === true) {
            $status->status = 'active';

            if (Schema::hasColumn('student_semester_statuses', 'validated_at')) {
                $status->validated_at = now();
            }

            // ✅ write ONLY admin id (FK -> admins.id). Otherwise NULL.
            if (Schema::hasColumn('student_semester_statuses', 'validated_by')) {
                $adminId = Auth::guard('admin')->id();
                $status->validated_by = $adminId ? (int) $adminId : null;
            }

            if (Schema::hasColumn('student_semester_statuses', 'ocr_json') && isset($result['ocr'])) {
                $status->ocr_json = json_encode($result['ocr']);
            }

            if (Schema::hasColumn('student_semester_statuses', 'reason')) {
                $status->reason = null; // clear previous failure reason
            }

            $status->save();

            $termLabel = $term->name ?? ($term->code ?? 'the current term');
            return redirect()->to('/student/dashboard')
                ->with('success', "✅ Verified and reactivated for {$termLabel}.");
        }

        // Failed auto-validation
        $status->status = 'needs_correction';
        if (Schema::hasColumn('student_semester_statuses', 'reason') && isset($result['reason'])) {
            $status->reason = $result['reason'];
        }
        $status->save();

        return back()->withErrors([
            'regcard_file' => $result['reason']
                ?? 'Validation failed. Please upload a clear BSN reg card (2nd/3rd/4th Year, First Semester, correct AY).',
        ])->withInput();
    }
}
