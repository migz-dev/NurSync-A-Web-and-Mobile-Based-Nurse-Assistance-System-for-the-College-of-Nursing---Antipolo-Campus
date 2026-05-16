{{-- resources/views/partials/admin-sidebar.blade.php --}}
@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;

    /* ---------- Active tab detection (NAS scope • ADMIN) ---------- */
    $active = $active ?? '';

    if ($active === '') {
        if (request()->routeIs('admin.dashboard')) {
            $active = 'dashboard';
        } elseif (request()->routeIs('admin.users*')) {
            $active = 'users';
        } elseif (request()->routeIs('admin.faculty.approvals')) {
            $active = 'faculty-approvals';
        } elseif (request()->routeIs('admin.resources*')) {
            $active = 'resources';
        } elseif (request()->routeIs('admin.equipment_guide.*')) {
            $active = 'equipment-guides';
        } elseif (request()->routeIs('admin.drug_guide.*')) {
            $active = 'drug_guide';
        } elseif (request()->routeIs('admin.emergency_protocols.*')) {   // Emergency protocols
            $active = 'emergency_protocols';
        } elseif (request()->routeIs('admin.nursing_references.*')) {    // Nursing references
            $active = 'nursing_references';
        } elseif (request()->routeIs('admin.patient_data*')) {           // Patient Data
            $active = 'patient_data';
        } elseif (request()->routeIs('admin.skill_mastery.*')) {         // Skill Mastery
            $active = 'skill_mastery';
        } elseif (request()->routeIs('admin.assessment_guides.*')) {     // Assessment Guides
            $active = 'assessment_guides';
        } elseif (request()->routeIs('admin.ward_orientation.*')) {      // Ward Orientation
            $active = 'ward_orientation';
        } elseif (request()->routeIs('admin.competency_requirements.*')) { // Competency Requirements
            $active = 'competency_requirements';
        } elseif (request()->routeIs('admin.settings')) {
            $active = 'settings';
        }
    }

    /* ---------- Styles ---------- */
    $activeLink   = 'flex items-center rounded-2xl px-4 py-3 text-white bg-[#009b56]';
    $inactiveLink = 'flex items-center gap-3 rounded-xl px-3 py-2 text-slate-700 hover:bg-slate-100';

    /* ---------- Admin display ---------- */
    $admin        = Auth::guard('admin')->user();
    $displayName  = $admin->full_name ?? $admin->name ?? 'Administrator';
    $displayEmail = $admin->email ?? 'admin@example.com';

    // Build initials from full name
    $parts     = preg_split('/\s+/', trim($displayName)) ?: [];
    $initials  = strtoupper(collect($parts)->filter()->map(fn($p) => mb_substr($p, 0, 1))->join(''));

    // Avatar URL from admins.profile_image (nullable)
    $imagePath = $admin?->profile_image;
    $avatarUrl = $imagePath ? Storage::url($imagePath) : null;

    /* ---------- Safe route helper (fallback to URL) ---------- */
    $href = function (string $name, string $fallback) {
        return Route::has($name) ? route($name) : url($fallback);
    };

    $logoutUrl   = Route::has('admin.logout') ? route('admin.logout') : url('/admin/logout');
    $settingsUrl = $href('admin.settings', '/admin/settings');
@endphp

<aside class="w-[280px] bg-white border-r border-slate-200 flex flex-col">
    <div class="p-4 flex-1">
        <!-- Brand row -->
        <div class="flex items-center gap-2 mb-4">
            <svg class="h-5 w-5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 aria-hidden="true">
                <path d="m15 19-7-7 7-7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <div class="text-[17px] font-semibold text-slate-900">NurSync Admin</div>
        </div>

        <!-- Profile -->
        <div class="flex items-center gap-3 p-3 rounded-2xl border border-slate-200 mx-3 mb-4">
            @if (!empty($avatarUrl))
                <img src="{{ $avatarUrl }}" alt="{{ $displayName }}" class="h-10 w-10 rounded-full object-cover">
            @else
                <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-[12px] font-semibold text-slate-700">
                    {{ $initials }}
                </div>
            @endif

            <div class="min-w-0">
                <div class="text-[13px] font-semibold text-slate-800 leading-tight truncate">{{ $displayName }}</div>
                <div class="text-[11px] text-slate-500 truncate">{{ $displayEmail }}</div>
            </div>
        </div>

        <div class="-mx-4 my-3 h-px bg-slate-200/70"></div>

        <!-- Settings / Logout -->
        <div class="flex justify-center text-[13px] text-slate-600 mt-3">
            <div class="flex items-center gap-6">
                <a href="{{ $settingsUrl }}" class="flex items-center gap-2 hover:text-slate-900">
                    <i data-lucide="settings" class="h-4 w-4"></i>
                    <span>Settings</span>
                </a>

                <form method="POST" action="{{ $logoutUrl }}" class="contents">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 hover:text-slate-900">
                        <i data-lucide="log-out" class="h-4 w-4"></i>
                        <span>Log out</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="-mx-4 my-3 h-px bg-slate-200/70"></div>

        <!-- Nav -->
        <nav class="mt-4 space-y-2">
            <!-- Core -->
            <a href="{{ $href('admin.dashboard', '/admin/dashboard') }}"
               class="{{ $active === 'dashboard' ? $activeLink : $inactiveLink }}"
               aria-current="{{ $active === 'dashboard' ? 'page' : 'false' }}">
                <span class="flex items-center gap-3">
                    <i data-lucide="layout-dashboard"
                       class="h-5 w-5 {{ $active === 'dashboard' ? 'text-white' : 'text-gray-500' }}"></i>
                    <span class="text-[14px]">Dashboard</span>
                </span>
            </a>

            <a href="{{ $href('admin.users.index', '/admin/users') }}"
               class="{{ $active === 'users' ? $activeLink : $inactiveLink }}"
               aria-current="{{ $active === 'users' ? 'page' : 'false' }}">
                <span class="flex items-center gap-3">
                    <i data-lucide="users"
                       class="h-5 w-5 {{ $active === 'users' ? 'text-white' : 'text-gray-500' }}"></i>
                    <span class="text-[14px]">Users</span>
                </span>
            </a>

            <!-- NAS Modules -->
            <a href="{{ $href('admin.resources.index', '/admin/resources') }}"
               class="{{ $active === 'resources' ? $activeLink : $inactiveLink }}"
               aria-current="{{ $active === 'resources' ? 'page' : 'false' }}">
                <span class="flex items-center gap-3">
                    <i data-lucide="folder-open"
                       class="h-5 w-5 {{ $active === 'resources' ? 'text-white' : 'text-gray-500' }}"></i>
                    <span class="text-[14px]">Procedures Guide</span>
                </span>
            </a>

            <a href="{{ $href('admin.drug_guide.index', '/admin/drug-guide') }}"
               class="{{ $active === 'drug_guide' ? $activeLink : $inactiveLink }}"
               aria-current="{{ $active === 'drug_guide' ? 'page' : 'false' }}">
                <span class="flex items-center gap-3">
                    <i data-lucide="pill"
                       class="h-5 w-5 {{ $active === 'drug_guide' ? 'text-white' : 'text-gray-500' }}"></i>
                    <span class="text-[14px]">Drug Guide</span>
                </span>
            </a>

            <!-- Emergency Protocols -->
            <a href="{{ $href('admin.emergency_protocols.index', '/admin/emergency-protocols') }}"
               class="{{ $active === 'emergency_protocols' ? $activeLink : $inactiveLink }}"
               aria-current="{{ $active === 'emergency_protocols' ? 'page' : 'false' }}">
                <span class="flex items-center gap-3">
                    <i data-lucide="alert-triangle"
                       class="h-5 w-5 {{ $active === 'emergency_protocols' ? 'text-white' : 'text-gray-500' }}"></i>
                    <span class="text-[14px]">Emergency Protocols</span>
                </span>
            </a>

            <!-- Nursing References -->
            <a href="{{ $href('admin.nursing_references.index', '/admin/nursing-references') }}"
               class="{{ $active === 'nursing_references' ? $activeLink : $inactiveLink }}"
               aria-current="{{ $active === 'nursing_references' ? 'page' : 'false' }}">
                <span class="flex items-center gap-3">
                    <i data-lucide="book-open-check"
                       class="h-5 w-5 {{ $active === 'nursing_references' ? 'text-white' : 'text-gray-500' }}"></i>
                    <span class="text-[14px]">Nursing References</span>
                </span>
            </a>
        </nav>
    </div>
</aside>
