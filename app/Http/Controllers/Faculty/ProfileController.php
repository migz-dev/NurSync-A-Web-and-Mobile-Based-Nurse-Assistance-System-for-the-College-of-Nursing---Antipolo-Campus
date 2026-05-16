<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;

class ProfileController extends Controller
{
    public function edit()
    {
        $me = Auth::guard('faculty')->user();
        return view('faculty.profile', compact('me'));
    }

    public function update(Request $request)
    {
        $me = Auth::guard('faculty')->user();

        $data = $request->validate([
            'profile_image' => [
                'nullable',
                File::image()->types(['jpg','jpeg','png','webp'])->max(4 * 1024), // 4MB
            ],
            'remove_image' => ['nullable','boolean'],
        ]);

        // Remove current image if requested
        if ($request->boolean('remove_image') && $me->profile_image) {
            // stored as 'storage/...' public path; actual disk path is 'public/...'
            $publicPath = $me->profile_image;                 // e.g. storage/faculty_avatars/abc.jpg
            $diskPath   = preg_replace('#^storage/#', '', $publicPath); // faculty_avatars/abc.jpg
            Storage::disk('public')->delete($diskPath);
            $me->profile_image = null;
        }

        // Handle new upload
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');

            // delete old file if exists
            if ($me->profile_image) {
                $old = preg_replace('#^storage/#', '', $me->profile_image);
                Storage::disk('public')->delete($old);
            }

            $path = $file->store('faculty_avatars', 'public'); // e.g. faculty_avatars/xyz.png
            $me->profile_image = 'storage/'.$path;             // public URL via storage:link
        }

        $me->save();

        return redirect()
            ->route('faculty.profile.edit')
            ->with('success', 'Profile updated.');
    }
}
