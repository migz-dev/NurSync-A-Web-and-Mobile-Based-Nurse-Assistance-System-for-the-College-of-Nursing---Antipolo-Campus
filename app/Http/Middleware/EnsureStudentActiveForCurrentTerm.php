<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnsureStudentActiveForCurrentTerm
{
    public function handle(Request $request, Closure $next)
    {
        // 0) Always allow the revalidation page itself (avoid redirect loops)
        if (
            $request->routeIs('student.regcard.revalidate') ||
            $request->is('student/regcard/revalidate') ||
            $request->is('student/regcard/revalidate/*')
        ) {
            return $next($request);
        }

        // 1) Must be authenticated (student guard or default)
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // 2) Resolve student + current term
        $student = DB::table('students')->where('email', $user->email)->first();
        $term    = DB::table('academic_terms')->where('is_current', 1)->first();

        if (!$student || !$term) {
            return redirect()->route('student.regcard.revalidate')
                ->with('status', 'Access requires verification for the current term.')
                ->with('error',  'Access requires verification for the current term.');
        }

        // 3) Check per-term status
        $row = DB::table('student_semester_statuses')
            ->where(['student_id' => $student->id, 'term_id' => $term->id])
            ->first();

        $expired = $row && $row->expires_at && now()->greaterThan($row->expires_at);

        // Not active (or no row yet) → force revalidation
        if (!$row || $row->status !== 'active' || $expired) {
            return redirect()->route('student.regcard.revalidate')
                ->with('status', 'Your account requires revalidation for the current term.')
                ->with('error',  'Your account requires revalidation for the current term.');
        }

        // 4) Good to go
        return $next($request);
    }
}