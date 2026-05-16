<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /**
     * Update faculty (CI) profile info — name, nurse type, and avatar.
     */
    public function updateProfile(Request $request)
    {
        $user = auth('faculty')->user();

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'nurse_type'  => ['nullable', 'string', 'max:100'],
            'avatar'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Update name (maps to full_name via mutator)
        $user->name = $data['name'];

        // Update nurse_type if provided
        if (array_key_exists('nurse_type', $data)) {
            $user->nurse_type = $data['nurse_type'];
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old local avatar file if exists
            if (!empty($user->profile_image) && !preg_match('#^https?://#i', $user->profile_image)) {
                $old = str_starts_with($user->profile_image, 'avatars/')
                    ? $user->profile_image
                    : 'avatars/' . $user->profile_image;
                Storage::disk('public')->delete($old);
            }

            // Store new avatar in /storage/app/public/avatars
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->profile_image = $path;
        }

        $user->save();

        return back()->with('ok', 'Profile updated successfully.');
    }

    /**
     * Remove the faculty's avatar image.
     */
    public function removeAvatar(Request $request)
    {
        $user = auth('faculty')->user();

        if (!empty($user->profile_image) && !preg_match('#^https?://#i', $user->profile_image)) {
            $old = str_starts_with($user->profile_image, 'avatars/')
                ? $user->profile_image
                : 'avatars/' . $user->profile_image;
            Storage::disk('public')->delete($old);
        }

        $user->profile_image = null;
        $user->save();

        return back()->with('ok', 'Profile photo removed.');
    }

    /**
     * Update faculty (CI) password securely.
     */
    public function updatePassword(Request $request)
    {
        $user = auth('faculty')->user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        // Validate current password
        if (!Hash::check($request->input('current_password'), $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Your current password is incorrect.'])
                ->withInput();
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return back()->with('ok', 'Password updated successfully.');
    }
}