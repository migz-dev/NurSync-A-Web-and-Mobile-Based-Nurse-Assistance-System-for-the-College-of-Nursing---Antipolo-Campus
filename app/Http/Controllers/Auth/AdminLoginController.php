<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required'],
        ]);

        $ok = Auth::guard('admin')->attempt(
            [
                'email'    => $credentials['email'],
                'password' => $credentials['password'],
                // 'role'   => 'admin', // uncomment if using a single users table with roles
            ],
            $request->boolean('remember')
        );

        if (! $ok) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        // Prevent cross-guard redirects (e.g., /faculty/dashboard)
        $request->session()->forget('url.intended');

        return redirect()->route('admin.dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ✅ Send back to landing (index.blade.php)
        return redirect()->route('home');
    }
}
