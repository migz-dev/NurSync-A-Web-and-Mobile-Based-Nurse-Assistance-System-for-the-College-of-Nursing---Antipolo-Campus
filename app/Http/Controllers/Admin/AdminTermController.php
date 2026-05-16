<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminTermController extends Controller
{
    public function change(Request $r)
    {
        $data = $r->validate([
            'school_year' => ['required','regex:/^\d{4}\-\d{4}$/'], // e.g. 2026-2027
            'semester'    => ['required','in:1,2,S'],               // 'S' = Summer
        ]);

        [$start, $end] = explode('-', $data['school_year']);

        // DB requires numeric semester (1=1st, 2=2nd, 3=Summer)
        $semCode  = $data['semester'];                                  // '1'|'2'|'S'
        $semInt   = $semCode === '1' ? 1 : ($semCode === '2' ? 2 : 3);  // 1|2|3
        $semLabel = $semInt === 1 ? '1st Semester' : ($semInt === 2 ? '2nd Semester' : 'Summer Term');

        // Stable code + human label
        $codeKey  = "{$start}-{$end}_{$semInt}";        // e.g., 2026-2027_1
        $termName = "AY {$start}-{$end} {$semLabel}";

        // Optional dates (only used if columns exist)
        $startDate = null; $endDate = null;
        if ($semInt === 1) { $startDate = "{$start}-08-01"; $endDate = "{$start}-12-31"; }
        if ($semInt === 2) { $startDate = "{$end}-01-15";   $endDate = "{$end}-05-31"; }
        if ($semInt === 3) { $startDate = "{$end}-06-01";   $endDate = "{$end}-07-31"; }

        $now = now();
        $studentTable = Schema::hasTable('students') ? 'students' : 'users';

        DB::transaction(function () use ($start, $end, $semInt, $semLabel, $codeKey, $termName, $startDate, $endDate, $now, $studentTable) {

            // 1) Clear current flag
            if (Schema::hasColumn('academic_terms','is_current')) {
                DB::table('academic_terms')->update(['is_current' => 0]);
            } elseif (Schema::hasColumn('academic_terms','active')) {
                DB::table('academic_terms')->update(['active' => 0]);
            }

            // 2) Find (by code if available, else name)
            $q = DB::table('academic_terms');
            if (Schema::hasColumn('academic_terms','code')) {
                $q->where('code', $codeKey);
            } else {
                $q->where('name', $termName);
            }
            $termId = (int) $q->value('id');

            // 2b) Build payload safely against existing columns
            $termPayload = function () use ($start, $end, $semInt, $semLabel, $codeKey, $termName, $startDate, $endDate, $now) {
                $p = [];
                if (Schema::hasColumn('academic_terms','code'))       $p['code']       = $codeKey;
                if (Schema::hasColumn('academic_terms','name'))       $p['name']       = $termName;
                if (Schema::hasColumn('academic_terms','semester'))   $p['semester']   = $semInt;    // numeric!
                if (Schema::hasColumn('academic_terms','term'))       $p['term']       = $semLabel;  // optional text field
                if (Schema::hasColumn('academic_terms','ay_start'))   $p['ay_start']   = (int) $start;
                if (Schema::hasColumn('academic_terms','ay_end'))     $p['ay_end']     = (int) $end;
                if (Schema::hasColumn('academic_terms','year_start')) $p['year_start'] = (int) $start;
                if (Schema::hasColumn('academic_terms','year_end'))   $p['year_end']   = (int) $end;
                if (Schema::hasColumn('academic_terms','start_date')) $p['start_date'] = $startDate;
                if (Schema::hasColumn('academic_terms','end_date'))   $p['end_date']   = $endDate;
                if (Schema::hasColumn('academic_terms','is_current')) $p['is_current'] = 1;
                if (Schema::hasColumn('academic_terms','active'))     $p['active']     = 1;
                if (Schema::hasColumn('academic_terms','created_at')) $p['created_at'] = $now;
                if (Schema::hasColumn('academic_terms','updated_at')) $p['updated_at'] = $now;
                return $p;
            };

            if ($termId) {
                $update = $termPayload();
                unset($update['created_at']);
                DB::table('academic_terms')->where('id', $termId)->update($update);
            } else {
                $termId = DB::table('academic_terms')->insertGetId($termPayload());
            }

            // 3) Seed/refresh per-student statuses -> pending_review
            $students = DB::table($studentTable)->select('id')->cursor();
            foreach ($students as $s) {
                $where  = ['student_id' => $s->id, 'term_id' => $termId];
                $values = ['status' => 'pending_review'];
                if (Schema::hasColumn('student_semester_statuses','regcard_file_id')) $values['regcard_file_id'] = null;
                if (Schema::hasColumn('student_semester_statuses','submitted_at'))    $values['submitted_at']    = null;
                if (Schema::hasColumn('student_semester_statuses','validated_at'))    $values['validated_at']    = null;
                if (Schema::hasColumn('student_semester_statuses','expires_at'))      $values['expires_at']      = null;
                if (Schema::hasColumn('student_semester_statuses','updated_at'))      $values['updated_at']      = $now;
                if (Schema::hasColumn('student_semester_statuses','created_at'))      $values['created_at']      = $now;

                DB::table('student_semester_statuses')->updateOrInsert($where, $values);
            }
        });

        return back()->with('success', "New term set to <strong>{$termName}</strong>. All students are now <em>Pending Review</em>.");
    }
}
