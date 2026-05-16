<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class FacultyLoginController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        // Attempt using the faculty guard only
        if (! Auth::guard('faculty')->attempt($credentials, $remember)) {
            return back()
                ->withErrors(['email' => 'Invalid email or password.'])
                ->onlyInput('email');
        }

        // Rotate the session ID to prevent fixation
        $request->session()->regenerate();

        // Only approved faculty can proceed
        $faculty = Auth::guard('faculty')->user();
        $status  = strtolower($faculty->status ?? 'pending');

        if ($status !== 'approved') {
            // Clear remember-me cookie for this guard
            if (method_exists(Auth::guard('faculty'), 'getRecallerName')) {
                Cookie::queue(Cookie::forget(Auth::guard('faculty')->getRecallerName()));
            }

            // Log out only the faculty guard
            Auth::guard('faculty')->logout();

            // Invalidate the whole session only if no other guards are still active
            $otherGuardsActive = Auth::guard('web')->check() || Auth::guard('admin')->check();
            if (! $otherGuardsActive) {
                $request->session()->invalidate();
            }
            $request->session()->regenerateToken();

            $msg = $status === 'rejected'
                ? 'Your faculty account has been rejected. Please contact the administrator.'
                : 'Your faculty account is pending approval.';

            // Or redirect to a pending page:
            // return redirect()->route('faculty.pending')->withErrors(['email' => $msg])->onlyInput('email');

            return back()->withErrors(['email' => $msg])->onlyInput('email');
        }

        // Honor intended URL only if it's under /faculty (and not the login/register pages)
        $intended = $request->session()->pull('url.intended');
        $path     = $intended ? parse_url($intended, PHP_URL_PATH) : null;

        if ($path
            && Str::startsWith($path, '/faculty')
            && ! Str::startsWith($path, '/faculty/login')
            && ! Str::startsWith($path, '/faculty/register')
        ) {
            return redirect()->to($intended);
        }

        // Default landing for faculty after login:
return redirect()->route('faculty.dashboard');
    }

    public function destroy(Request $request)
    {
        // Clear remember-me cookie for this guard
        if (method_exists(Auth::guard('faculty'), 'getRecallerName')) {
            Cookie::queue(Cookie::forget(Auth::guard('faculty')->getRecallerName()));
        }

        // Log out only the faculty guard
        Auth::guard('faculty')->logout();

        // Only kill the whole session if this was the last/only logged-in guard
        $otherGuardsActive = Auth::guard('web')->check() || Auth::guard('admin')->check();
        if (! $otherGuardsActive) {
            $request->session()->invalidate();
        }

        $request->session()->regenerateToken();

        // Route back to faculty login (keeps student/admin logged in if active)
        return redirect()->route('faculty.login');
    }
}