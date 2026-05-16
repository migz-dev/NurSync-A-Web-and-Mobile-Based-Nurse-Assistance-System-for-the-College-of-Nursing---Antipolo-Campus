<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * GET /admin/admins
     * List admins with search & status filter.
     */
    public function index(Request $request)
    {
        $q      = trim((string) $request->input('q', ''));
        $status = $request->input('status'); // "1", "0" or null

        $admins = Admin::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('full_name', 'like', "%{$q}%")
                       ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('is_active', (int) $status);
            })
            ->orderByDesc('created_at');

        $adminsPage = $admins->paginate(10)->withQueryString();

        // View matches the page you just created
        return view('admin.admin-admins', compact('adminsPage', 'q', 'status'));
    }

    /**
     * GET /admin/admins/create
     * Show create form (scaffold view later).
     */
    public function create()
    {
        return view('admin.admins.create');
    }

    /**
     * POST /admin/admins
     * Create a new admin.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'     => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:admins,email'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'], // expects password_confirmation
            'is_active'     => ['nullable', 'boolean'],
            'profile_image' => ['nullable', 'image', 'max:2048'], // 2MB
        ]);

        $admin = new Admin();
        $admin->full_name     = $data['full_name'];
        $admin->email         = $data['email'];
        $admin->password_hash = Hash::make($data['password']);
        $admin->is_active     = (int) ($data['is_active'] ?? 1);
        $admin->save();

        // Handle avatar upload (optional)
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store(
                'avatars/admins/' . $admin->id,
                'public'
            );
            $admin->profile_image = 'storage/' . $path; // so asset() works out of the box
            $admin->save();
        }

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin created successfully.');
    }

    /**
     * GET /admin/admins/{admin}
     * Show details.
     */
    public function show(Admin $admin)
    {
        return view('admin.admins.show', compact('admin'));
    }

    /**
     * GET /admin/admins/{admin}/edit
     * Show edit form.
     */
    public function edit(Admin $admin)
    {
        return view('admin.admins.edit', compact('admin'));
    }

    /**
     * PUT /admin/admins/{admin}
     * Update admin (password optional).
     */
    public function update(Request $request, Admin $admin)
    {
        $data = $request->validate([
            'full_name'     => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', Rule::unique('admins', 'email')->ignore($admin->id)],
            'password'      => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active'     => ['nullable', 'boolean'],
            'profile_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $admin->full_name = $data['full_name'];
        $admin->email     = $data['email'];
        if (!empty($data['password'])) {
            $admin->password_hash = Hash::make($data['password']);
        }
        if (isset($data['is_active'])) {
            $admin->is_active = (int) $data['is_active'];
        }
        $admin->save();

        // Avatar upload / replace
        if ($request->hasFile('profile_image')) {
            // delete old file if it lives in storage
            if ($admin->profile_image && str_starts_with($admin->profile_image, 'storage/')) {
                $oldRel = Str::after($admin->profile_image, 'storage/');
                Storage::disk('public')->delete($oldRel);
            }

            $path = $request->file('profile_image')->store(
                'avatars/admins/' . $admin->id,
                'public'
            );
            $admin->profile_image = 'storage/' . $path;
            $admin->save();
        }

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin updated successfully.');
    }

    /**
     * DELETE /admin/admins/{admin}
     * Delete admin. Returns JSON for SweetAlert fetch; redirects otherwise.
     */
    public function destroy(Request $request, Admin $admin)
    {
        // Optional: prevent self-delete if you have current admin guard logic
        // if (auth('admin')->id() === $admin->id) { ... }

        // delete avatar directory
        if ($admin->profile_image && str_starts_with($admin->profile_image, 'storage/')) {
            $dir = 'avatars/admins/' . $admin->id;
            Storage::disk('public')->deleteDirectory($dir);
        }

        $admin->delete();

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Admin deleted.']);
        }
        return redirect()->route('admin.admins.index')->with('success', 'Admin deleted.');
    }
}
