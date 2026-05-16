{{-- resources/views/partials/admin-dashboard-main.blade.php --}}
<section class="flex-1 px-6 lg:px-8 py-10">
  <!-- Header -->
  <div class="flex items-center justify-between">
    <div class="flex items-center gap-3">
      <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-green-50 text-green-600">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
          <path d="M12 3l7 4v5c0 5-3.5 9-7 9s-7-4-7-9V7l7-4Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M9.5 12.5l1.75 1.75L15 10.5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </span>
      <div>
        <h1 class="text-2xl font-bold">Admin Dashboard</h1>
        <p class="text-[13px] text-slate-500 mt-0.5">Welcome, Administrator</p>
      </div>
    </div>
  </div>

  {{-- STATS – SKELETON --}}
  <div id="statsSkeleton" aria-hidden="true" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mt-6">
    @for ($i = 0; $i < 6; $i++)
      <div class="rounded-2xl border border-slate-200 bg-white p-5">
        <div class="animate-pulse space-y-3">
          <div class="h-3 w-32 bg-slate-200 rounded"></div>
          <div class="h-7 w-20 bg-slate-100 rounded mt-2"></div>
          <div class="h-3 w-40 bg-slate-100 rounded mt-2"></div>
        </div>
      </div>
    @endfor
  </div>

  {{-- STATS – REAL --}}
  <div id="statsReal" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mt-6 hidden">

    {{-- Total Student Nurses --}}
    <div class="js-dash-card rounded-2xl border border-slate-200 bg-white p-5">
      <div class="flex items-start justify-between">
        <div>
          <div class="text-[13px] font-medium text-slate-700">Total Student Nurses</div>
          <div class="mt-2 text-3xl font-bold text-green-600">
            {{ number_format($totalStudents ?? 0) }}
          </div>
          <p class="mt-1 text-[12px] text-slate-500">Across all year levels</p>
        </div>
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-green-50 text-green-600">
          <i data-lucide="graduation-cap" class="h-5 w-5"></i>
        </span>
      </div>
    </div>

    {{-- Total Clinical Instructors --}}
    <div class="js-dash-card rounded-2xl border border-slate-200 bg-white p-5">
      <div class="flex items-start justify-between">
        <div>
          <div class="text-[13px] font-medium text-slate-700">Total Clinical Instructors</div>
          <div class="mt-2 text-3xl font-bold text-blue-600">
            {{ number_format($totalFaculty ?? 0) }}
          </div>
          <p class="mt-1 text-[12px] text-slate-500">Active teaching staff</p>
        </div>
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
          <i data-lucide="user-check" class="h-5 w-5"></i>
        </span>
      </div>
    </div>

    {{-- Total Admins --}}
    <div class="js-dash-card rounded-2xl border border-slate-200 bg-white p-5">
      <div class="flex items-start justify-between">
        <div>
          <div class="text-[13px] font-medium text-slate-700">Total Admins</div>
          <div class="mt-2 text-3xl font-bold text-purple-600">
            {{ number_format($totalAdmins ?? 0) }}
          </div>
          <p class="mt-1 text-[12px] text-slate-500">System administrators</p>
        </div>
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
          <i data-lucide="shield" class="h-5 w-5"></i>
        </span>
      </div>
    </div>

    {{-- Pending Faculty Approvals --}}
    <div class="js-dash-card rounded-2xl border border-slate-200 bg-white p-5">
      <div class="flex items-start justify-between">
        <div>
          <div class="text-[13px] font-medium text-slate-700">Pending Faculty Approvals</div>
          <div class="mt-2 text-3xl font-bold text-amber-600">
            {{ number_format($pendingApprovals ?? 0) }}
          </div>
          <p class="mt-1 text-[12px] text-slate-500">Awaiting admin review</p>
        </div>
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
          <i data-lucide="clock" class="h-5 w-5"></i>
        </span>
      </div>
    </div>

    {{-- Total Procedures --}}
    <div class="js-dash-card rounded-2xl border border-slate-200 bg-white p-5">
      <div class="flex items-start justify-between">
        <div>
          <div class="text-[13px] font-medium text-slate-700">Total Procedures</div>
          <div class="mt-2 text-3xl font-bold text-emerald-600">
            {{ number_format($totalProcedures ?? 0) }}
          </div>
          <p class="mt-1 text-[12px] text-slate-500">In the Procedures Library</p>
        </div>
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
          <i data-lucide="stethoscope" class="h-5 w-5"></i>
        </span>
      </div>
    </div>

    {{-- Total Drugs --}}
    <div class="js-dash-card rounded-2xl border border-slate-200 bg-white p-5">
      <div class="flex items-start justify-between">
        <div>
          <div class="text-[13px] font-medium text-slate-700">Total Drugs</div>
          <div class="mt-2 text-3xl font-bold text-rose-600">
            {{ number_format($totalDrugs ?? 0) }}
          </div>
          <p class="mt-1 text-[12px] text-slate-500">Registered in Drug Database</p>
        </div>
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
          <i data-lucide="pill" class="h-5 w-5"></i>
        </span>
      </div>
    </div>

  </div>

  {{-- QUICK ACTIONS – SKELETON --}}
  <div id="quickSkeleton" aria-hidden="true" class="mt-8 rounded-2xl border border-slate-200 bg-white p-5">
    <div class="animate-pulse space-y-4">
      <div class="h-4 w-32 bg-slate-200 rounded"></div>
      <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @for ($i = 0; $i < 4; $i++)
          <div class="rounded-xl border border-slate-100 px-4 py-3">
            <div class="flex items-center gap-2">
              <span class="h-8 w-8 rounded-xl bg-slate-200"></span>
              <div class="space-y-2 flex-1">
                <div class="h-3 w-24 bg-slate-200 rounded"></div>
                <div class="h-3 w-20 bg-slate-100 rounded"></div>
              </div>
            </div>
          </div>
        @endfor
      </div>
    </div>
  </div>

  {{-- QUICK ACTIONS – REAL --}}
  <div id="quickReal" class="mt-8 rounded-2xl border border-slate-200 bg-white p-5 js-dash-card hidden">
    <div class="flex items-center justify-between">
      <h2 class="text-[15px] font-semibold text-slate-800">Quick Actions</h2>
    </div>
    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
      <a href="{{ route('admin.users.index') }}"
         class="js-dash-card rounded-xl border border-slate-200 px-4 py-3 text-[13px] font-medium hover:bg-slate-50 flex items-center gap-2">
        <i data-lucide="users" class="h-4 w-4 text-slate-700"></i>
        <span>Manage Users</span>
      </a>

      <a href="{{ route('admin.faculty.approvals') }}"
         class="js-dash-card rounded-xl border border-slate-200 px-4 py-3 text-[13px] font-medium hover:bg-slate-50 flex items-center gap-2">
        <i data-lucide="user-check" class="h-4 w-4 text-emerald-600"></i>
        <span>Review Faculty Approvals</span>
      </a>

      <a href="{{ route('admin.procedures.index') }}"
         class="js-dash-card rounded-xl border border-slate-200 px-4 py-3 text-[13px] font-medium hover:bg-slate-50 flex items-center gap-2">
        <i data-lucide="stethoscope" class="h-4 w-4 text-sky-600"></i>
        <span>Procedures Library</span>
      </a>

      <a href="{{ route('admin.drug_guide.index') }}"
         class="js-dash-card rounded-xl border border-slate-200 px-4 py-3 text-[13px] font-medium hover:bg-slate-50 flex items-center gap-2">
        <i data-lucide="pill" class="h-4 w-4 text-rose-600"></i>
        <span>Drug Guide</span>
      </a>
    </div>
  </div>

  {{-- CORE SYSTEM MODULES --}}
  <div class="mt-8">
    <div class="flex items-center justify-between">
      <h2 class="text-[15px] font-semibold text-slate-800">Core System Modules</h2>
    </div>

    {{-- CORE – SKELETON --}}
    <div id="coreSkeleton" aria-hidden="true" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      @for ($i = 0; $i < 3; $i++)
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
          <div class="animate-pulse space-y-3">
            <div class="flex items-start justify-between gap-3">
              <div class="flex items-center gap-2">
                <span class="h-8 w-8 rounded-xl bg-slate-200"></span>
                <div class="space-y-2">
                  <div class="h-3 w-32 bg-slate-200 rounded"></div>
                  <div class="h-3 w-24 bg-slate-100 rounded"></div>
                </div>
              </div>
              <span class="h-5 w-20 rounded-full bg-slate-100"></span>
            </div>
            <div class="h-3 w-full bg-slate-100 rounded"></div>
            <div class="h-3 w-2/3 bg-slate-100 rounded"></div>
            <div class="mt-3 flex gap-2">
              <span class="h-3 w-24 rounded bg-slate-100"></span>
              <span class="h-3 w-28 rounded bg-slate-100"></span>
            </div>
          </div>
        </div>
      @endfor
    </div>

    {{-- CORE – REAL --}}
    <div id="coreReal" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 hidden">

      {{-- Users & Roles --}}
      <a href="{{ route('admin.users.index') }}"
         class="js-dash-card block rounded-2xl border border-slate-200 bg-white p-5 hover:bg-slate-50">
        <div class="flex items-start justify-between">
          <div class="flex items-center gap-2">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
              <i data-lucide="users" class="h-4 w-4"></i>
            </span>
            <div>
              <div class="text-[13px] font-semibold text-slate-800">Users & Roles</div>
              <div class="text-[12px] text-slate-500">Students · CI · Admins</div>
            </div>
          </div>
          <span class="inline-flex items-center rounded-full bg-slate-50 text-slate-700 border border-slate-100 px-2.5 py-1 text-[11px] font-medium">
            Access control
          </span>
        </div>
        <div class="mt-3 text-[12px] text-slate-500 line-clamp-2">
          Manage accounts, roles, and activation status across the entire NurSync ecosystem.
        </div>
      </a>

      {{-- Faculty & Approvals --}}
      <a href="{{ route('admin.faculty.approvals') }}"
         class="js-dash-card block rounded-2xl border border-slate-200 bg-white p-5 hover:bg-slate-50">
        <div class="flex items-start justify-between">
          <div class="flex items-center gap-2">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
              <i data-lucide="badge-check" class="h-4 w-4"></i>
            </span>
            <div>
              <div class="text-[13px] font-semibold text-slate-800">Faculty Approvals</div>
              <div class="text-[12px] text-slate-500">Verification & onboarding</div>
            </div>
          </div>
          <span class="inline-flex items-center rounded-full bg-amber-50 text-amber-700 border border-amber-100 px-2.5 py-1 text-[11px] font-medium">
            {{ number_format($pendingApprovals ?? 0) }} pending
          </span>
        </div>
        <div class="mt-3 text-[12px] text-slate-500 line-clamp-2">
          Review and approve clinical instructors to ensure only verified faculty can teach and chart.
        </div>
      </a>

      {{-- Clinical Libraries --}}
      <a href="{{ route('admin.procedures.index') }}"
         class="js-dash-card block rounded-2xl border border-slate-200 bg-white p-5 hover:bg-slate-50">
        <div class="flex items-start justify-between">
          <div class="flex items-center gap-2">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
              <i data-lucide="library" class="h-4 w-4"></i>
            </span>
            <div>
              <div class="text-[13px] font-semibold text-slate-800">Clinical Libraries</div>
              <div class="text-[12px] text-slate-500">Procedures · Equipment · Drugs</div>
            </div>
          </div>
          <span class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 px-2.5 py-1 text-[11px] font-medium">
            Centralized content
          </span>
        </div>
        <div class="mt-3 text-[12px] text-slate-500 line-clamp-2">
          Curate procedure guides and drug information for use by faculty and students.
        </div>
      </a>

    </div>
  </div>
</section>
