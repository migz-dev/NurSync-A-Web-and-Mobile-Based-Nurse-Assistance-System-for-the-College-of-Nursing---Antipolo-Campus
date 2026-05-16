<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // ensure signed-in
    }

    /**
     * POST /student/settings/profile
     * - Update name
     * - Optionally upload/replace avatar
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'], // 2MB
        ]);

        // Update basic profile
        $user->name = $validated['name'];

        // Handle avatar upload (optional)
        if ($request->hasFile('avatar')) {
            // Remove old file if present
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            // Store new file in /storage/app/public/avatars
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_path = $path;
        }

        $user->save();

        return back()->with('ok', 'Profile updated.');
    }

    /**
     * DELETE /student/settings/avatar
     * - Remove current avatar (fallback to initials)
     */
    public function removeAvatar(Request $request)
    {
        $user = Auth::user();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->avatar_path = null;
            $user->save();
        }

        return back()->with('ok', 'Profile photo removed.');
    }

    /**
     * POST /student/settings/password
     * - Update password (requires current password)
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('ok', 'Password updated.');
    }
}
