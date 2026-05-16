<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminSettingsController extends Controller
{
    /**
     * Settings page — provides profile display vars + $faculties and $admins for the Add-Admin UI.
     */
    public function edit(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $displayName  = $admin->display_name ?? $admin->full_name ?? $admin->name ?? 'Admin';
        $displayEmail = $admin->email ?? '';
        $imagePath    = $admin->profile_image; // correct column
        $avatarUrl    = $imagePath ? Storage::url($imagePath) : null;

        // Build initials
        $parts    = preg_split('/\s+/', trim($displayName)) ?: [];
        $initials = mb_strtoupper(collect($parts)->filter()->map(fn ($p) => mb_substr($p, 0, 1))->join(''));

        // ---- Add-Admin data ----
        // Exclude faculty who are already admins (by email)
        $adminEmails = Admin::pluck('email')->all();

        $faculties = Faculty::query()
            ->when(!empty($adminEmails), fn ($q) => $q->whereNotIn('email', $adminEmails))
            // optional quick search via ?q=
            ->when($request->filled('q'), function ($q) use ($request) {
                $needle = '%' . trim($request->q) . '%';
                $q->where(function ($qq) use ($needle) {
                    $qq->where('full_name', 'like', $needle)
                       ->orWhere('email', 'like', $needle)
                       ->orWhere('faculty_id', 'like', $needle);
                });
            })
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'email', 'faculty_id']);

        $admins = Admin::orderBy('full_name')
            ->get(['id', 'full_name', 'email', 'is_active', 'profile_image', 'created_at']);

        return view('admin.admin-settings', compact(
            'displayName',
            'displayEmail',
            'initials',
            'avatarUrl',
            'faculties',
            'admins'
        ));
    }

    /**
     * Upload avatar (kept as-is; writes to admins.profile_image).
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $admin = Auth::guard('admin')->user();

        // store on public disk, e.g. storage/app/public/...
        $path = $request->file('avatar')->store("avatars/admins/{$admin->id}", 'public');

        DB::table('admins')->where('id', $admin->id)->update([
            'profile_image' => $path, // correct column
            'updated_at'    => now(),
        ]);

        return back()->with('success', 'Profile photo updated.');
    }

    /**
     * Remove avatar (kept as-is; clears admins.profile_image).
     */
    public function removeAvatar(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        // delete existing file if present
        if (!empty($admin->profile_image)) { // correct column
            Storage::disk('public')->delete($admin->profile_image);
        }

        DB::table('admins')->where('id', $admin->id)->update([
            'profile_image' => null, // correct column
            'updated_at'    => now(),
        ]);

        return back()->with('success', 'Profile photo removed.');
    }

    /**
     * Promote a Faculty to Admin.
     * Route: POST /admin/settings/add-admin  (name: admin.settings.add-admin)
     */
    public function addAdmin(Request $request)
    {
        $request->validate([
            // NOTE: adjust table name if your schema differs; project memory uses table "faculty"
            'faculty_id'       => ['required', Rule::exists('faculty', 'id')],
            'current_password' => ['required', 'string'],
        ]);

        /** @var \App\Models\Admin $actor */
        $actor = Auth::guard('admin')->user();

        // Verify actor's password against admins.password_hash
        if (! Hash::check($request->current_password, $actor->getAuthPassword())) {
            return back()
                ->withErrors(['current_password' => 'Your password is incorrect.'])
                ->withInput();
        }

        $faculty = Faculty::findOrFail($request->faculty_id);

        // Prevent duplicates by email
        if (Admin::where('email', $faculty->email)->exists()) {
            return back()->with('error', 'This faculty is already an Admin.');
        }

        DB::transaction(function () use ($faculty) {
            Admin::create([
                'full_name'     => $faculty->full_name,
                'email'         => $faculty->email,
                'profile_image' => $faculty->profile_image ?? null,
                // Copy existing hashed password from faculty table (already hashed)
                'password_hash' => $faculty->password,
                'is_active'     => 1,
            ]);
        });

        // Optionally: log activity here

        return redirect()->route('admin.settings')
            ->with('success', "{$faculty->full_name} is now an Admin.");
    }

    /**
     * Remove an Admin (cannot remove self).
     * Route: DELETE /admin/settings/admins/{admin}  (name: admin.settings.remove-admin)
     */
    public function removeAdmin(Admin $admin)
    {
        $currentId = Auth::guard('admin')->id();
        if ($admin->id === $currentId) {
            return back()->with('error', 'You cannot remove your own Admin account here.');
        }

        $admin->delete();

        return back()->with('success', 'Admin removed successfully.');
    }
}
