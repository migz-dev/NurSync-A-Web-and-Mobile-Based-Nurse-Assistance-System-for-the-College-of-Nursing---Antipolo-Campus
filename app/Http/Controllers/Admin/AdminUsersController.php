<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class AdminUsersController extends Controller
{
    /**
     * Role pill styles (shared by Users + Archives pages)
     */
    private array $roleStyles = [
        'Clinical Instructor' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'icon' => 'graduation-cap'],
        'Student Nurse'       => ['bg' => 'bg-sky-50',    'text' => 'text-sky-700',    'icon' => 'book-open'],
        'Admin'               => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'icon' => 'shield'],
    ];

    /** Optional columns (autodetected) */
    private bool $facultyHasArchivedAt;
    private bool $studentsHasArchivedAt;
    private bool $facultyHasNurseType;
    private bool $adminsHasArchivedAt;

    public function __construct()
    {
        $this->facultyHasArchivedAt  = Schema::hasColumn('faculty', 'archived_at');
        $this->studentsHasArchivedAt = Schema::hasColumn('students', 'archived_at');
        $this->facultyHasNurseType   = Schema::hasColumn('faculty', 'nurse_type');
        $this->adminsHasArchivedAt   = Schema::hasColumn('admins', 'archived_at'); // usually not present, but safe to check
    }

    /**
     * GET /admin/users
     * Active/All (non-archived) list for CIs + Students.
     */
    public function index(Request $request)
    {
        $perPage = (int) ($request->integer('per_page') ?: 10);
        $page    = (int) max(1, $request->integer('page') ?: 1);

        // --- Clinical Instructors (exclude archived if column exists)
        $ciColumns = ['faculty_id','full_name as name','email','profile_image','status','created_at'];
        if ($this->facultyHasNurseType) {
            $ciColumns[] = 'nurse_type';
        }

        $cis = DB::table('faculty')
            ->select($ciColumns)
            ->when($this->facultyHasArchivedAt, fn($q) => $q->whereNull('archived_at'))
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($r) {
                $status = $r->status === 'approved' ? 'Active' : ucfirst($r->status ?? 'Inactive');
                $avatar = $r->profile_image ? Storage::url($r->profile_image) : null;
                return (object)[
                    'id'          => $r->faculty_id,
                    'name'        => $r->name,
                    'email'       => $r->email,
                    'role'        => 'Clinical Instructor',
                    'status'      => $status,
                    'created_at'  => $r->created_at,
                    'avatar_url'  => $avatar,
                    'nurse_type'  => property_exists($r, 'nurse_type') ? ($r->nurse_type ?? null) : null,
                ];
            });

        // --- Students (exclude archived)
        $studentsQuery = DB::table('students as s')
            ->leftJoin('users as u', 'u.email', '=', 's.email')
            ->select(['s.student_number','s.full_name as name','s.email','s.is_active','s.created_at','u.avatar_path']);

        if ($this->studentsHasArchivedAt) {
            $studentsQuery->whereNull('s.archived_at');
        } else {
            $studentsQuery->where('s.is_active', 1);
        }

        $students = $studentsQuery
            ->orderByDesc('s.created_at')
            ->get()
            ->map(function ($r) {
                $status = ($r->is_active ?? 0) ? 'Active' : 'Inactive';
                $avatar = $r->avatar_path ? Storage::url($r->avatar_path) : null;
                return (object)[
                    'id'          => $r->student_number,
                    'name'        => $r->name,
                    'email'       => $r->email,
                    'role'        => 'Student Nurse',
                    'status'      => $status,
                    'created_at'  => $r->created_at,
                    'avatar_url'  => $avatar,
                    'nurse_type'  => null,
                ];
            });

        // Merge, sort, paginate
        $all   = $cis->merge($students)->sortByDesc('created_at')->values();
        $total = $all->count();
        $items = $all->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path'  => url()->current(),
            'query' => $request->query(),
        ]);

        return view('admin.admin-users', [
            'usersPage'  => $paginator,
            'roleStyles' => $this->roleStyles,
        ]);
    }

    /**
     * GET /admin/users/admins
     * Dedicated Admins table (list all admins).
     */
    public function admins(Request $request)
    {
        $perPage = (int) ($request->integer('per_page') ?: 10);
        $page    = (int) max(1, $request->integer('page') ?: 1);
        $q       = trim((string) $request->get('q', ''));
        $status  = (string) $request->get('status', ''); // '', 'active', 'inactive', 'archived' (if column exists)

        $adminsQuery = DB::table('admins')
            ->select(['id','full_name as name','email','profile_image','is_active','created_at']
                + ($this->adminsHasArchivedAt ? ['archived_at'] : [])
            );

        // Search
        if ($q !== '') {
            $adminsQuery->where(function ($qq) use ($q) {
                $qq->where('full_name', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%");
            });
        }

        // Status filter
        if ($status !== '') {
            if ($this->adminsHasArchivedAt && $status === 'archived') {
                $adminsQuery->whereNotNull('archived_at');
            } elseif ($status === 'active') {
                $adminsQuery->where('is_active', 1)->when($this->adminsHasArchivedAt, fn($qq) => $qq->whereNull('archived_at'));
            } elseif ($status === 'inactive') {
                $adminsQuery->where('is_active', 0)->when($this->adminsHasArchivedAt, fn($qq) => $qq->whereNull('archived_at'));
            }
        } else {
            // default: exclude archived if column exists
            if ($this->adminsHasArchivedAt) {
                $adminsQuery->whereNull('archived_at');
            }
        }

        $rows = $adminsQuery->orderByDesc('created_at')->get();

        $mapped = $rows->map(function ($r) {
            $avatar = $r->profile_image ? Storage::url($r->profile_image) : null;
            $status = ($this->adminsHasArchivedAt && !empty($r->archived_at))
                ? 'Archived'
                : (($r->is_active ?? 0) ? 'Active' : 'Inactive');

            return (object)[
                'id'          => $r->id,
                'name'        => $r->name,
                'email'       => $r->email,
                'role'        => 'Admin',
                'status'      => $status,
                'created_at'  => $r->created_at,
                'avatar_url'  => $avatar,
                'nurse_type'  => null,
                'archived_at' => $this->adminsHasArchivedAt ? ($r->archived_at ?? null) : null,
            ];
        });

        // Paginate (manual)
        $total = $mapped->count();
        $items = $mapped->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path'  => url()->current(),
            'query' => $request->query(),
        ]);

        return view('admin.users.admins', [
            'adminsPage' => $paginator,
            'roleStyles' => $this->roleStyles,
            'q'          => $q,
            'status'     => $status,
        ]);
    }

    /**
     * GET /admin/users/archives
     * Archived list — mirrors your archives page table.
     */
    public function archives(Request $request)
    {
        $perPage = (int) ($request->integer('per_page') ?: 10);
        $page    = (int) max(1, $request->integer('page') ?: 1);

        // Archived CIs (require archived_at)
        $ciColumns = ['faculty_id','full_name as name','email','profile_image','status','created_at','archived_at'];
        if ($this->facultyHasNurseType) {
            $ciColumns[] = 'nurse_type';
        }

        $archivedCis = DB::table('faculty')
            ->select($ciColumns)
            ->whereNotNull('archived_at')
            ->orderByDesc('archived_at')
            ->get()
            ->map(function ($r) {
                $avatar = $r->profile_image ? Storage::url($r->profile_image) : null;
                return (object)[
                    'id'          => $r->faculty_id,
                    'name'        => $r->name,
                    'email'       => $r->email,
                    'role'        => 'Clinical Instructor',
                    'status'      => 'Archived',
                    'created_at'  => $r->created_at,
                    'archived_at' => $r->archived_at,
                    'avatar_url'  => $avatar,
                    'nurse_type'  => property_exists($r, 'nurse_type') ? ($r->nurse_type ?? null) : null,
                ];
            });

        // Archived Students
        $archivedStudents = DB::table('students as s')
            ->leftJoin('users as u', 'u.email', '=', 's.email')
            ->select(['s.student_number','s.full_name as name','s.email','s.is_active','s.created_at','u.avatar_path','s.archived_at'])
            ->whereNotNull('s.archived_at')
            ->orderByDesc('s.archived_at')
            ->get()
            ->map(function ($r) {
                $avatar = $r->avatar_path ? Storage::url($r->avatar_path) : null;
                return (object)[
                    'id'          => $r->student_number,
                    'name'        => $r->name,
                    'email'       => $r->email,
                    'role'        => 'Student Nurse',
                    'status'      => 'Archived',
                    'created_at'  => $r->created_at,
                    'archived_at' => $r->archived_at,
                    'avatar_url'  => $avatar,
                    'nurse_type'  => null,
                ];
            });

        // Archived Admins (only if table has archived_at)
        $archivedAdmins = collect();
        if ($this->adminsHasArchivedAt) {
            $archivedAdmins = DB::table('admins')
                ->select(['id','full_name as name','email','profile_image','is_active','created_at','archived_at'])
                ->whereNotNull('archived_at')
                ->orderByDesc('archived_at')
                ->get()
                ->map(function ($r) {
                    $avatar = $r->profile_image ? Storage::url($r->profile_image) : null;
                    return (object)[
                        'id'          => $r->id,
                        'name'        => $r->name,
                        'email'       => $r->email,
                        'role'        => 'Admin',
                        'status'      => 'Archived',
                        'created_at'  => $r->created_at,
                        'archived_at' => $r->archived_at,
                        'avatar_url'  => $avatar,
                        'nurse_type'  => null,
                    ];
                });
        }

        // Merge and paginate
        $all   = $archivedCis->merge($archivedStudents)->merge($archivedAdmins)
                 ->sortByDesc(fn($r) => $r->archived_at ?: $r->created_at)->values();
        $total = $all->count();
        $items = $all->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path'  => url()->current(),
            'query' => $request->query(),
        ]);

        return view('admin-archives.admin-users-archives', [
            'archivedUsersPage' => $paginator,
            'roleStyles'        => $this->roleStyles,
        ]);
    }

    /**
     * GET /admin/users/{id}
     * JSON for modal VIEW (tries CI, then Student, then Admin).
     */
    public function show($id)
    {
        // CI
        $ci = DB::table('faculty')->where('faculty_id', $id)->first();
        if ($ci) {
            $avatar = $ci->profile_image ? Storage::url($ci->profile_image) : null;
            return response()->json([
                'id'          => $ci->faculty_id,
                'name'        => $ci->full_name,
                'email'       => $ci->email,
                'role'        => 'Clinical Instructor',
                'status'      => $ci->status === 'archived' ? 'Archived' : ($ci->status === 'approved' ? 'Active' : ucfirst($ci->status ?? 'Inactive')),
                'created_at'  => $ci->created_at,
                'archived_at' => $this->facultyHasArchivedAt ? ($ci->archived_at ?? null) : null,
                'avatar_url'  => $avatar,
                'nurse_type'  => $this->facultyHasNurseType ? ($ci->nurse_type ?? null) : null,
            ]);
        }

        // Student
        $st = DB::table('students as s')
            ->leftJoin('users as u', 'u.email', '=', 's.email')
            ->select(['s.student_number','s.full_name','s.email','s.is_active','s.created_at','u.avatar_path'])
            ->when($this->studentsHasArchivedAt, fn($q) => $q->addSelect('s.archived_at'))
            ->where('s.student_number', $id)
            ->first();

        if ($st) {
            $avatar = $st->avatar_path ? Storage::url($st->avatar_path) : null;
            return response()->json([
                'id'          => $st->student_number,
                'name'        => $st->full_name,
                'email'       => $st->email,
                'role'        => 'Student Nurse',
                'status'      => ($st->is_active ?? 0) ? 'Active' : 'Archived',
                'created_at'  => $st->created_at,
                'archived_at' => $this->studentsHasArchivedAt ? ($st->archived_at ?? null) : null,
                'avatar_url'  => $avatar,
                'nurse_type'  => null,
            ]);
        }

        // Admin
        $ad = DB::table('admins')->where('id', $id)->first();
        if ($ad) {
            $avatar = $ad->profile_image ? Storage::url($ad->profile_image) : null;
            $status = ($this->adminsHasArchivedAt && !empty($ad->archived_at))
                ? 'Archived'
                : (($ad->is_active ?? 0) ? 'Active' : 'Inactive');

            return response()->json([
                'id'          => $ad->id,
                'name'        => $ad->full_name,
                'email'       => $ad->email,
                'role'        => 'Admin',
                'status'      => $status,
                'created_at'  => $ad->created_at,
                'archived_at' => $this->adminsHasArchivedAt ? ($ad->archived_at ?? null) : null,
                'avatar_url'  => $avatar,
                'nurse_type'  => null,
            ]);
        }

        abort(404);
    }

    /**
     * (Optional) Full page view — if you want /admin/users/{id}/view
     */
    public function view($id)
    {
        $ci = DB::table('faculty')->where('faculty_id', $id)->first();
        if ($ci) {
            $user = (object)[
                'id'         => $ci->faculty_id,
                'name'       => $ci->full_name,
                'email'      => $ci->email,
                'role'       => 'Clinical Instructor',
                'status'     => $ci->status === 'approved' ? 'Active' : ucfirst($ci->status ?? 'Inactive'),
                'created_at' => $ci->created_at,
                'avatar_url' => $ci->profile_image ? Storage::url($ci->profile_image) : null,
                'nurse_type' => $this->facultyHasNurseType ? ($ci->nurse_type ?? null) : null,
            ];
            return view('admin.users.view', compact('user'));
        }

        $st = DB::table('students')->where('student_number', $id)->first();
        if ($st) {
            $user = (object)[
                'id'         => $st->student_number,
                'name'       => $st->full_name,
                'email'      => $st->email,
                'role'       => 'Student Nurse',
                'status'     => ($st->is_active ?? 0) ? 'Active' : 'Inactive',
                'created_at' => $st->created_at,
                'avatar_url' => null,
                'nurse_type' => null,
            ];
            return view('admin.users.view', compact('user'));
        }

        $ad = DB::table('admins')->where('id', $id)->first();
        if ($ad) {
            $user = (object)[
                'id'         => $ad->id,
                'name'       => $ad->full_name,
                'email'      => $ad->email,
                'role'       => 'Admin',
                'status'     => ($ad->is_active ?? 0) ? 'Active' : 'Inactive',
                'created_at' => $ad->created_at,
                'avatar_url' => $ad->profile_image ? Storage::url($ad->profile_image) : null,
                'nurse_type' => null,
            ];
            return view('admin.users.view', compact('user'));
        }

        abort(404);
    }

    /**
     * (Optional) Edit page — if you want /admin/users/{id}/edit
     */
    public function edit($id)
    {
        $ci = DB::table('faculty')->where('faculty_id', $id)->first();
        if ($ci) {
            $user = (object)[
                'id'         => $ci->faculty_id,
                'name'       => $ci->full_name,
                'email'      => $ci->email,
                'role'       => 'Clinical Instructor',
                'status'     => $ci->status,
                'nurse_type' => $this->facultyHasNurseType ? ($ci->nurse_type ?? null) : null,
            ];
            return view('admin.users.edit', compact('user'));
        }

        $st = DB::table('students')->where('student_number', $id)->first();
        if ($st) {
            $user = (object)[
                'id'     => $st->student_number,
                'name'   => $st->full_name,
                'email'  => $st->email,
                'role'   => 'Student Nurse',
                'status' => ($st->is_active ?? 0) ? 'Active' : 'Inactive',
            ];
            return view('admin.users.edit', compact('user'));
        }

        $ad = DB::table('admins')->where('id', $id)->first();
        if ($ad) {
            $user = (object)[
                'id'     => $ad->id,
                'name'   => $ad->full_name,
                'email'  => $ad->email,
                'role'   => 'Admin',
                'status' => ($ad->is_active ?? 0) ? 'Active' : 'Inactive',
            ];
            return view('admin.users.edit', compact('user'));
        }

        abort(404);
    }

    /**
     * POST /admin/users/{id}
     * Update via modal (AJAX) or page form.
     */
    public function update(Request $request, $id)
    {
        // Try CI first
        $ci = DB::table('faculty')->where('faculty_id', $id)->first();
        if ($ci) {
            $rules = [
                'name'   => ['required','string','max:255'],
                'email'  => ['required','email','max:255'],
                'status' => ['nullable', Rule::in(['approved','pending','rejected','archived'])],
            ];
            if ($this->facultyHasNurseType) {
                $rules['nurse_type'] = ['nullable','string','max:100'];
            }

            $data = $request->validate($rules);

            $update = [
                'full_name'  => $data['name'],
                'email'      => $data['email'],
                'status'     => $data['status'] ?? $ci->status,
                'updated_at' => now(),
            ];
            if ($this->facultyHasNurseType && array_key_exists('nurse_type', $data)) {
                $update['nurse_type'] = $data['nurse_type'];
            }

            DB::table('faculty')->where('faculty_id', $id)->update($update);

            return $request->expectsJson()
                ? response()->json(['ok' => true, 'message' => 'Clinical Instructor updated.'])
                : back()->with('flash_success', 'Clinical Instructor updated.');
        }

        // Try Student next
        $st = DB::table('students')->where('student_number', $id)->first();
        if ($st) {
            $data = $request->validate([
                'name'   => ['required','string','max:255'],
                'email'  => ['required','email','max:255'],
                'status' => ['nullable', Rule::in(['Active','Inactive','Archived'])],
            ]);

            // map human status → is_active (Archived treated as Inactive for students table)
            $isActive = $st->is_active;
            if (isset($data['status'])) {
                $isActive = $data['status'] === 'Active' ? 1 : 0;
            }

            $update = [
                'full_name'  => $data['name'],
                'email'      => $data['email'],
                'is_active'  => $isActive,
                'updated_at' => now(),
            ];

            if ($this->studentsHasArchivedAt) {
                $update['archived_at'] = $isActive ? null : ($st->archived_at ?? null); // don’t auto-archive here
            }

            DB::table('students')->where('student_number', $id)->update($update);

            return $request->expectsJson()
                ? response()->json(['ok' => true, 'message' => 'Student updated.'])
                : back()->with('flash_success', 'Student updated.');
        }

        // Try Admin
        $ad = DB::table('admins')->where('id', $id)->first();
        if ($ad) {
            $data = $request->validate([
                'name'   => ['required','string','max:255'],
                'email'  => ['required','email','max:255'],
                'status' => ['nullable', Rule::in(['Active','Inactive','Archived'])],
            ]);

            $isActive = $ad->is_active;
            if (isset($data['status'])) {
                if ($data['status'] === 'Archived' && $this->adminsHasArchivedAt) {
                    // handled below via archived_at
                    $isActive = 0;
                } else {
                    $isActive = $data['status'] === 'Active' ? 1 : 0;
                }
            }

            $update = [
                'full_name'  => $data['name'],
                'email'      => $data['email'],
                'is_active'  => $isActive,
                'updated_at' => now(),
            ];
            if ($this->adminsHasArchivedAt && ($data['status'] ?? null) === 'Archived') {
                $update['archived_at'] = now();
            }

            DB::table('admins')->where('id', $id)->update($update);

            return $request->expectsJson()
                ? response()->json(['ok' => true, 'message' => 'Admin updated.'])
                : back()->with('flash_success', 'Admin updated.');
        }

        return $request->expectsJson()
            ? response()->json(['ok' => false, 'message' => 'Record not found.'], 404)
            : back()->with('flash_error', 'Record not found.');
    }

    /**
     * POST /admin/users/{id}/restore
     */
    public function restore($id)
    {
        // CI
        $ci = DB::table('faculty')->where('faculty_id', $id)->first();
        if ($ci) {
            $payload = ['status' => 'approved'];
            if ($this->facultyHasArchivedAt) $payload['archived_at'] = null;

            DB::table('faculty')->where('faculty_id', $id)->update($payload);
            return back()->with('flash_success', 'Clinical Instructor restored.');
        }

        // Student
        $st = DB::table('students')->where('student_number', $id)->first();
        if ($st) {
            $payload = ['is_active' => 1];
            if ($this->studentsHasArchivedAt) $payload['archived_at'] = null;

            DB::table('students')->where('student_number', $id)->update($payload);
            return back()->with('flash_success', 'Student restored.');
        }

        // Admin
        $ad = DB::table('admins')->where('id', $id)->first();
        if ($ad) {
            $payload = ['is_active' => 1];
            if ($this->adminsHasArchivedAt) $payload['archived_at'] = null;

            DB::table('admins')->where('id', $id)->update($payload);
            return back()->with('flash_success', 'Admin restored.');
        }

        return back()->with('flash_error', 'Record not found.');
    }

    /**
     * DELETE /admin/users/{id}
     */
    public function destroy($id)
    {
        $deleted = DB::table('faculty')->where('faculty_id', $id)->delete();
        if ($deleted) return back()->with('flash_success', 'Clinical Instructor permanently deleted.');

        $deleted = DB::table('students')->where('student_number', $id)->delete();
        if ($deleted) return back()->with('flash_success', 'Student permanently deleted.');

        $deleted = DB::table('admins')->where('id', $id)->delete();
        if ($deleted) return back()->with('flash_success', 'Admin permanently deleted.');

        return back()->with('flash_error', 'Record not found.');
    }

    /**
     * POST /admin/users/{id}/archive
     */
    public function archive($id)
    {
        $now = now();

        // CI
        $ci = DB::table('faculty')->where('faculty_id', $id)->first();
        if ($ci) {
            if (!$this->facultyHasArchivedAt) {
                return response()->json(['ok' => false, 'message' => 'archived_at column missing on faculty.'], 422);
            }
            DB::table('faculty')->where('faculty_id', $id)->update([
                'archived_at' => $now,
            ]);
            return response()->json(['ok' => true, 'message' => 'User archived (CI).']);
        }

        // Student
        $st = DB::table('students')->where('student_number', $id)->first();
        if ($st) {
            if (!$this->studentsHasArchivedAt) {
                return response()->json(['ok' => false, 'message' => 'archived_at column missing on students.'], 422);
            }
            DB::table('students')->where('student_number', $id)->update([
                'archived_at' => $now,
                'is_active'   => 0,
            ]);
            return response()->json(['ok' => true, 'message' => 'User archived (Student).']);
        }

        // Admin
        $ad = DB::table('admins')->where('id', $id)->first();
        if ($ad) {
            if (!$this->adminsHasArchivedAt) {
                // fallback: just deactivate
                DB::table('admins')->where('id', $id)->update([
                    'is_active'  => 0,
                    'updated_at' => $now,
                ]);
                return response()->json(['ok' => true, 'message' => 'Admin deactivated (no archived_at column).']);
            }

            DB::table('admins')->where('id', $id)->update([
                'archived_at' => $now,
                'is_active'   => 0,
            ]);
            return response()->json(['ok' => true, 'message' => 'User archived (Admin).']);
        }

        return response()->json(['ok' => false, 'message' => 'Record not found.'], 404);
    }

    /* -----------------------------------------------------------------
       Helper (not used externally)
       ----------------------------------------------------------------- */
    protected function archiveNow(): string
    {
        return Carbon::now()->toDateTimeString();
    }
}
