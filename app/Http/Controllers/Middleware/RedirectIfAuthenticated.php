<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * If the user is already authenticated, redirect them away
     * from guest-only pages (login/register).
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user    = Auth::guard($guard)->user();

                // Try to resolve student + current term to decide the best landing
                $student = DB::table('students')->where('email', $user->email)->first();
                $term    = DB::table('academic_terms')->where('is_current', 1)->first();

                if ($student && $term) {
                    $ss = DB::table('student_semester_statuses')
                        ->where(['student_id' => $student->id, 'term_id' => $term->id])
                        ->first();

                    $expired = $ss && $ss->expires_at && now()->greaterThan($ss->expires_at);

                    if ($ss && $ss->status === 'active' && ! $expired) {
                        return redirect()->route('student.dashboard');
                    }

                    return redirect()
                        ->route('student.regcard.revalidate')
                        ->with('error', 'Access requires verification for the current term.');
                }

                // Fallback: let the student gate sort it out
                return redirect()->route('student.dashboard');
            }
        }

        return $next($request);
    }
}
