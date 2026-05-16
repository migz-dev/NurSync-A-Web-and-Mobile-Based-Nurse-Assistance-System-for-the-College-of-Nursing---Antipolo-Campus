<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email:rfc'], // friendlier than rfc,dns in local dev
            'password' => ['required', 'string'],
            'remember' => ['nullable'],
        ]);

        $remember = $request->boolean('remember');

        // student ('web') guard only
        if (
            !Auth::guard('web')->attempt(
                ['email' => $credentials['email'], 'password' => $credentials['password']],
                $remember
            )
        ) {
            return back()
                ->withErrors(['email' => 'Invalid email or password.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::guard('web')->user();
        $term = DB::table('academic_terms')->where('is_current', 1)->first();

        // resolve student id from students table when available
        $studentId = $user->id;
        if (Schema::hasTable('students')) {
            $studentRow = DB::table('students')->where('email', $user->email)->first();
            if ($studentRow && isset($studentRow->id)) {
                $studentId = (int) $studentRow->id;
            }
        }

        if ($term && $studentId) {
            $row = DB::table('student_semester_statuses')
                ->where(['student_id' => $studentId, 'term_id' => $term->id])
                ->first();

            if (!$row) {
                // Build a payload that only includes columns that actually exist.
                $payload = [
                    'student_id' => $studentId,
                    'term_id' => $term->id,
                    'status' => 'pending_review',
                ];

                if (Schema::hasColumn('student_semester_statuses', 'regcard_file_id')) {
                    $payload['regcard_file_id'] = null;
                }
                if (Schema::hasColumn('student_semester_statuses', 'submitted_at')) {
                    $payload['submitted_at'] = null;
                }
                if (Schema::hasColumn('student_semester_statuses', 'validated_at')) {
                    $payload['validated_at'] = null;
                }
                if (Schema::hasColumn('student_semester_statuses', 'created_at')) {
                    $payload['created_at'] = now();
                }
                if (Schema::hasColumn('student_semester_statuses', 'updated_at')) {
                    $payload['updated_at'] = now();
                }

                DB::table('student_semester_statuses')->insert($payload);

                // fabricate a minimal row for logic below
                $row = (object) [
                    'status' => 'pending_review',
                    'expires_at' => null,
                ];
            }

            $expired = isset($row->expires_at) && $row->expires_at && now()->greaterThan($row->expires_at);

            if ($row->status === 'active' && !$expired) {
                // Honor intended only if it's a /student page
                $intended = $request->session()->pull('url.intended');
                $path = $intended ? parse_url($intended, PHP_URL_PATH) : null;

                if ($path && Str::startsWith($path, '/student')) {
                    return redirect()->to($intended);
                }

                // Default landing: Student Dashboard
                return redirect()->route('student.dashboard');
            }

            // not active for current term → revalidate
            return redirect()
                ->route('student.regcard.revalidate')
                ->with('status', 'Your account requires revalidation for the current term.')
                ->with('error', 'Your account requires revalidation for the current term.');
        }

        // no current term or no student id
        return redirect()
            ->route('student.regcard.revalidate')
            ->with('status', 'Your account requires revalidation for the current term.')
            ->with('error', 'Your account requires revalidation for the current term.');
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $otherGuardsActive = Auth::guard('faculty')->check() || Auth::guard('admin')->check();

        if (!$otherGuardsActive) {
            $request->session()->invalidate();
        }

        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
