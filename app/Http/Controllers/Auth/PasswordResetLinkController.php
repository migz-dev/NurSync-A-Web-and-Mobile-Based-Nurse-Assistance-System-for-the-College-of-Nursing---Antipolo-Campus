<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    /**
     * Show the "Forgot Password" page.
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle "Send Reset Link" request (Students + Faculty).
     */
    public function store(Request $request)
    {
        // Validate email
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Determine which broker to use
        // Students → broker: users
        // Faculty  → broker: faculty
        // Admin (optional later) → broker: admins
        $email = $request->email;

        // Decide the broker based on the email domain or database lookup
        // Here we check faculty table first
        $broker = $this->resolveBroker($email);

        // Send reset link
        $status = Password::broker($broker)->sendResetLink(
            ['email' => $email]
        );

        // Success
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'A reset link has been sent to your email.');
        }

        // Error
        return back()->withErrors([
            'email' => __($status),
        ]);
    }

    /**
     * Determine the correct password broker.
     *
     * @param string $email
     * @return string
     */
    private function resolveBroker(string $email): string
    {
        // 🔍 Check faculty email
        if (\App\Models\Faculty::where('email', $email)->exists()) {
            return 'faculty';
        }

        // 🔍 Check admin (optional)
        if (\App\Models\Admin::where('email', $email)->exists()) {
            return 'admins';
        }

        // Default → student
        return 'users';
    }
}
