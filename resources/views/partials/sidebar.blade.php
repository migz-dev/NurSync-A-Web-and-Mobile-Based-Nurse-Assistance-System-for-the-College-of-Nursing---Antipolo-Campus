{{-- resources/views/partials/sidebar.blade.php --}}
@php
  use Illuminate\Support\Facades\Route;

  /* ---------- Safe route helper ---------- */
  $r = function (string $name, string $fallback) {
    return Route::has($name) ? route($name) : url($fallback);
  };

  /* ---------- Active tab (derive if not passed) ---------- */
  $active = $active ?? '';

  if ($active === '') {
    if (request()->routeIs('student.dashboard') || request()->routeIs('student.home')) {
      $active = 'dashboard';



    } elseif (request()->routeIs('student.return_demo*') || request()->is('student/return-demo*')) {
      $active = 'return_demo';

    } elseif (
      request()->routeIs('student.nursing_references*') ||
      request()->is('student/nursing-references*') ||
      request()->is('student/knowledge-hub*')
    ) {
      // Nursing References (student)
      $active = 'nursing_references';

    } elseif (
      request()->routeIs('student.emergency*') ||
      request()->is('student/emergency-protocols*')
    ) {
      $active = 'emergency_protocols';

    } elseif (
      request()->routeIs('student.drugs*') ||
      request()->is('student/drug-guide*')
    ) {
      $active = 'drug_guide';

    } elseif (
      request()->routeIs('student.board_exam*') ||
      request()->is('student/board-exam-practice*')
    ) {
      // New: Board Exam Practice
      $active = 'board_exam_practice';

    } elseif (
      request()->routeIs('student.assessment*') ||
      request()->is('student/assessment-guides*')
    ) {
      $active = 'assessment_guides';

    } elseif (
      request()->routeIs('student.skills*') ||
      request()->is('student/skill-checklists*')
    ) {
      $active = 'skill_checklists';

    } elseif (
      request()->routeIs('student.wards*') ||
      request()->is('student/ward-orientation*')
    ) {
      $active = 'ward_orientation';

    } elseif (
      request()->routeIs('student.competencies*') ||
      request()->is('student/competency-requirements*')
    ) {
      $active = 'competency_requirements';

    } elseif (
      request()->routeIs('student.experiences*') ||
      request()->is('student/clinical-experiences*')
    ) {
      $active = 'clinical_experiences';
    }
  }

  /* ---------- Link styles ---------- */
  $activeLink   = 'flex items-center gap-3 rounded-2xl px-4 py-3 text-white bg-[#009b56] shadow-sm transition-colors duration-150';
  $inactiveLink = 'flex items-center gap-3 rounded-xl px-3 py-2 text-slate-700 hover:bg-slate-100 transition-colors duration-150';

  /* ---------- User display ---------- */
  $u            = auth()->user();
  $avatarUrl    = $u?->avatar_url ?? null;
  $initials     = $u?->initials ?? 'SN';
  $displayName  = $u?->display_name ?? 'Student Nurse';
  $displayEmail = $u?->display_email ?? 'student@example.com';

  /* ---------- URLs ---------- */
  $settingsUrl = $r('student.settings', '/student/settings');
@endphp

<aside class="w-[280px] bg-white border-r border-slate-200/70 flex flex-col">
  <div class="p-4 flex-1">
    <!-- Brand row -->
    <div class="flex items-center gap-2 mb-4">
      <svg class="h-5 w-5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
        <path d="m15 19-7-7 7-7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
      <div class="text-[17px] font-semibold text-slate-900">NurSync — Nurse Assistance</div>
    </div>

    <!-- Profile -->
    <div class="flex items-center gap-3 p-3 rounded-2xl border border-slate-200/70">
      @if($avatarUrl)
        <img src="{{ $avatarUrl }}" alt="Profile photo" class="h-10 w-10 rounded-full object-cover" />
      @else
        <div
          class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-[12px] font-semibold text-slate-700">
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
          <i data-lucide="user-cog" class="h-4 w-4"></i>
          <span>Settings</span>
        </a>

        @auth
          <form method="POST" action="{{ route('logout') }}" class="contents">
            @csrf
            <button type="submit" class="flex items-center gap-2 hover:text-slate-900">
              <i data-lucide="log-out" class="h-4 w-4"></i>
              <span>Log out</span>
            </button>
          </form>
        @endauth
      </div>
    </div>

    <div class="-mx-4 my-3 h-px bg-slate-200/70"></div>

    <!-- Nav -->
    <nav class="mt-4 space-y-2">
      {{-- DASHBOARD --}}
      <a href="{{ $r('student.dashboard', '/student') }}"
         class="{{ $active === 'dashboard' ? $activeLink : $inactiveLink }}"
         aria-current="{{ $active === 'dashboard' ? 'page' : 'false' }}">
        <i data-lucide="layout-dashboard"
           class="h-5 w-5 {{ $active === 'dashboard' ? 'text-white' : 'text-gray-500' }}"></i>
        <span class="text-[14px]">Dashboard</span>
      </a>
      {{-- RETURN DEMO PROCEDURES --}}
      <a href="{{ $r('student.return_demo.index', '/student/return-demo') }}"
         class="{{ $active === 'return_demo' ? $activeLink : $inactiveLink }}"
         aria-current="{{ $active === 'return_demo' ? 'page' : 'false' }}">
        <i data-lucide="library"
           class="h-5 w-5 {{ $active === 'return_demo' ? 'text-white' : 'text-gray-500' }}"></i>
        <span class="text-[14px]">Return Demo Procedures</span>
      </a>

      {{-- NURSING REFERENCES --}}
      <a href="{{ $r('student.nursing_references.index', '/student/nursing-references') }}"
         class="{{ $active === 'nursing_references' ? $activeLink : $inactiveLink }}"
         aria-current="{{ $active === 'nursing_references' ? 'page' : 'false' }}">
        <i data-lucide="book-open-check"
           class="h-5 w-5 {{ $active === 'nursing_references' ? 'text-white' : 'text-gray-500' }}"></i>
        <span class="text-[14px]">Nursing References</span>
      </a>

      <div class="mt-3 mb-1 text-[11px] font-semibold uppercase tracking-wide text-slate-400">
        Clinical Knowledge
      </div>

      {{-- EMERGENCY PROTOCOLS --}}
      <a href="{{ $r('student.emergency.index', '/student/emergency-protocols') }}"
         class="{{ $active === 'emergency_protocols' ? $activeLink : $inactiveLink }}"
         aria-current="{{ $active === 'emergency_protocols' ? 'page' : 'false' }}">
        <i data-lucide="shield-alert"
           class="h-5 w-5 {{ $active === 'emergency_protocols' ? 'text-white' : 'text-gray-500' }}"></i>
        <span class="text-[14px]">Emergency Protocols</span>
      </a>

      {{-- DRUG GUIDE --}}
      <a href="{{ $r('student.drugs.index', '/student/drug-guide') }}"
         class="{{ $active === 'drug_guide' ? $activeLink : $inactiveLink }}"
         aria-current="{{ $active === 'drug_guide' ? 'page' : 'false' }}">
        <i data-lucide="pill"
           class="h-5 w-5 {{ $active === 'drug_guide' ? 'text-white' : 'text-gray-500' }}"></i>
        <span class="text-[14px]">Drug Guide</span>
      </a>
      <div class="mt-3 mb-1 text-[11px] font-semibold uppercase tracking-wide text-slate-400">
        Skills & Assessment
      </div>

      {{-- ASSESSMENT GUIDES --}}
      <a href="{{ $r('student.assessment.index', '/student/assessment-guides') }}"
         class="{{ $active === 'assessment_guides' ? $activeLink : $inactiveLink }}"
         aria-current="{{ $active === 'assessment_guides' ? 'page' : 'false' }}">
        <i data-lucide="clipboard-list"
           class="h-5 w-5 {{ $active === 'assessment_guides' ? 'text-white' : 'text-gray-500' }}"></i>
        <span class="text-[14px]">Assessment Guides</span>
      </a>

      {{-- SKILL MASTERY CHECKLISTS --}}
      <a href="{{ $r('student.skills.index', '/student/skill-checklists') }}"
         class="{{ $active === 'skill_checklists' ? $activeLink : $inactiveLink }}"
         aria-current="{{ $active === 'skill_checklists' ? 'page' : 'false' }}">
        <i data-lucide="check-circle-2"
           class="h-5 w-5 {{ $active === 'skill_checklists' ? 'text-white' : 'text-gray-500' }}"></i>
        <span class="text-[14px]">Skill Mastery Checklists</span>
      </a>

      {{-- WARD ORIENTATION --}}
      <a href="{{ $r('student.wards.index', '/student/ward-orientation') }}"
         class="{{ $active === 'ward_orientation' ? $activeLink : $inactiveLink }}"
         aria-current="{{ $active === 'ward_orientation' ? 'page' : 'false' }}">
        <i data-lucide="map"
           class="h-5 w-5 {{ $active === 'ward_orientation' ? 'text-white' : 'text-gray-500' }}"></i>
        <span class="text-[14px]">Ward Orientation</span>
      </a>

      {{-- CLINICAL EXPERIENCES (Student view) --}}
      <a href="{{ $r('student.experiences.index', '/student/clinical-experiences') }}"
         class="{{ $active === 'clinical_experiences' ? $activeLink : $inactiveLink }}"
         aria-current="{{ $active === 'clinical_experiences' ? 'page' : 'false' }}">
        <i data-lucide="stethoscope"
           class="h-5 w-5 {{ $active === 'clinical_experiences' ? 'text-white' : 'text-gray-500' }}"></i>
        <span class="text-[14px]">Clinical Experiences</span>
      </a>

      {{-- COMPETENCY REQUIREMENTS --}}
      <a href="{{ $r('student.competencies.index', '/student/competency-requirements') }}"
         class="{{ $active === 'competency_requirements' ? $activeLink : $inactiveLink }}"
         aria-current="{{ $active === 'competency_requirements' ? 'page' : 'false' }}">
        <i data-lucide="badge-check"
           class="h-5 w-5 {{ $active === 'competency_requirements' ? 'text-white' : 'text-gray-500' }}"></i>
        <span class="text-[14px]">Competency Requirements</span>
      </a>
    </nav>
  </div>
</aside>
