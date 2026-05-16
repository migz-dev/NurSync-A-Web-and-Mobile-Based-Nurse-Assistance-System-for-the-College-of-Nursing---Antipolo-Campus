<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class NewPasswordController extends Controller
{
    /**
     * Show the reset password form (link from email).
     */
    public function create(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Handle the actual password reset.
     */
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        // Determine the correct password broker
        $broker = $this->resolveBroker($request->email);

        // Perform reset
        $status = Password::broker($broker)->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),

            function ($user, $password) {
                // Update password + reset token
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        // Password successfully reset
        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('status', 'Your password has been successfully updated.');
        }

        // Error (invalid token, invalid email, etc.)
        return back()->withErrors([
            'email' => __($status),
        ]);
    }


    /**
     * Determine which account type (student / faculty / admin)
     * should handle this password reset request.
     */
    private function resolveBroker(string $email): string
    {
        // Faculty account?
        if (\App\Models\Faculty::where('email', $email)->exists()) {
            return 'faculty';
        }

        // Admin account?
        if (\App\Models\Admin::where('email', $email)->exists()) {
            return 'admins';
        }

        // Default → Student
        return 'users';
    }
}
