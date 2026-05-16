@php
  // Active detection (with dashboard)
  $active = $active ?? '';
  if ($active === '') {
    if (request()->routeIs('faculty.dashboard'))
      $active = 'dashboard';
    elseif (request()->routeIs('faculty.chartings.*'))     // moved up as core
      $active = 'chartings';
    elseif (request()->routeIs('faculty.procedures.*'))
      $active = 'procedures';
    elseif (request()->routeIs('faculty.drug_guide.*'))
      $active = 'drug_guide';
    elseif (request()->routeIs('faculty.equipment_guides.*'))
      $active = 'equipment_guides';
    elseif (request()->routeIs('faculty.emergency.*'))     // NEW
      $active = 'emergency';                               // NEW
    elseif (request()->routeIs('faculty.knowledge_hub.*')) // NEW
      $active = 'knowledge_hub';                           // NEW
    elseif (request()->routeIs('faculty.calculators.*'))
      $active = 'calculators';
    elseif (request()->routeIs('faculty.simulations.*'))   // NEW
      $active = 'simulations';                             // NEW
    elseif (request()->routeIs('faculty.settings'))
      $active = 'settings';
  }

  $activeLink = 'flex items-center rounded-2xl px-4 py-3 text-white bg-[#009b56]';
  $inactiveLink = 'flex items-center gap-3 rounded-xl px-3 py-2 text-slate-700 hover:bg-slate-100';

  /** @var \App\Models\Faculty|null $u */
  $u = auth('faculty')->user();
  $avatar = $u?->avatar_url;
  $initials = $u?->initials ?? ($u?->name ? strtoupper(mb_substr($u->name, 0, 1)) : 'F');
  $displayName = $u?->name ?: 'Faculty User';
  $displayEmail = $u?->email ?: 'faculty@sys.test.ph';

  $r = fn($name, $fallback) => \Illuminate\Support\Facades\Route::has($name) ? route($name) : url($fallback);

  $navItems = [
    // OVERVIEW
    ['key' => 'dashboard', 'icon' => 'layout-dashboard', 'label' => 'Dashboard', 'href' => $r('faculty.dashboard', '/faculty/dashboard')],

    // CLINICAL WORK (NEW)
    ['key' => 'chartings', 'icon' => 'clipboard-list', 'label' => 'Patient and Task', 'href' => $r('faculty.chartings.index', '/faculty/chartings')],

    // KNOWLEDGE & TOOLS
    ['key' => 'procedures', 'icon' => 'stethoscope', 'label' => 'Procedure Guides', 'href' => $r('faculty.procedures.index', '/faculty/procedures')],
    ['key' => 'drug_guide', 'icon' => 'pill', 'label' => 'Drug Guide', 'href' => $r('faculty.drug_guide.index', '/faculty/drug-guide')],
    ['key' => 'emergency', 'icon' => 'alert-triangle', 'label' => 'Emergency Protocols', 'href' => $r('faculty.emergency.index', '/faculty/emergency-protocols')],
    ['key' => 'knowledge_hub', 'icon' => 'book-open-check', 'label' => 'Nursing References', 'href' => $r('faculty.knowledge_hub.index', '/faculty/knowledge-hub')],

    // SIMULATION & REPORTS (NEW)
    // (you can add simulations/reports items here later)
  ];
@endphp



{{-- ===== Mobile header bar (brand + burger) ===== --}}
<header class="md:hidden fixed top-0 inset-x-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200">
  <div class="h-12 px-4 flex items-center justify-between">
    <div class="flex items-center gap-2">
      <img src="/assets/images/CON_LOGO.png" class="h-5 w-5 rounded-md" alt="NurSync">
      <span class="text-[16px] font-semibold text-slate-900">NurSync</span>
    </div>

    <button id="mobMenuBtn" class="inline-flex items-center justify-center h-9 w-9 rounded-xl hover:bg-slate-100"
      aria-haspopup="true" aria-expanded="false" aria-controls="mobMenuPanel">
      <i data-lucide="menu" class="h-5 w-5 text-slate-700"></i>
      <span class="sr-only">Open menu</span>
    </button>
  </div>

  {{-- Flyout menu --}}
  <div id="mobMenuPanel"
    class="hidden absolute right-2 top-12 w-56 rounded-2xl border border-slate-200 bg-white shadow-lg p-2">
    <div class="px-3 py-2 text-[12px] text-slate-500">Account</div>
    <div class="px-2 pb-2">
      <div class="flex items-center gap-3 p-2 rounded-xl border border-slate-200">
        @if($avatar)
          <img src="{{ $avatar }}" alt="Profile" class="h-8 w-8 rounded-full object-cover border border-slate-200">
        @else
          <div
            class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-[11px] font-semibold text-slate-700 border border-slate-200">
            {{ $initials }}
          </div>
        @endif
        <div class="min-w-0">
          <div class="text-[13px] font-semibold text-slate-800 leading-tight truncate">{{ $displayName }}</div>
          <div class="text-[11px] text-slate-500 truncate">{{ $displayEmail }}</div>
        </div>
      </div>
    </div>

    <div class="my-1 h-px bg-slate-200/70"></div>

    <div class="space-y-1 px-1 pb-1">
      <a href="{{ $r('faculty.settings', '/faculty/settings') }}"
        class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-50 text-[14px]">
        <i data-lucide="settings" class="h-5 w-5 text-slate-600"></i>
        <span>Settings</span>
      </a>

      <form method="POST" action="{{ $r('faculty.logout', '/faculty/logout') }}">
        @csrf
        <button type="submit"
          class="w-full text-left flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-50 text-[14px]">
          <i data-lucide="log-out" class="h-5 w-5 text-slate-600"></i>
          <span>Log out</span>
        </button>
      </form>
    </div>
  </div>

  {{-- Click-away overlay --}}
  <div id="mobMenuOverlay" class="hidden fixed inset-0 z-[-1]"></div>
</header>

{{-- ===== Desktop sidebar (unchanged layout, no dashboard) ===== --}}
<aside class="hidden md:flex w-[280px] bg-white border-r border-slate-200 flex-col sticky top-0 h-screen">
  <div class="p-4 flex-1 overflow-y-auto">
    <div class="flex items-center gap-2 mb-4">
      <img src="/assets/images/CON_LOGO.png" class="h-5 w-5 rounded-md" alt="NurSync">
      <div class="text-[17px] font-semibold text-slate-900">NurSync</div>
    </div>

    <div class="flex items-center gap-3 p-3 rounded-2xl border border-slate-200">
      @if($avatar)
        <img src="{{ $avatar }}" alt="Profile" class="h-10 w-10 rounded-full object-cover border border-slate-200">
      @else
        <div
          class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-[12px] font-semibold text-slate-700 border border-slate-200">
          {{ $initials }}
        </div>
      @endif
      <div class="min-w-0">
        <div class="text-[13px] font-semibold text-slate-800 leading-tight truncate" title="{{ $displayName }}">
          {{ $displayName }}
        </div>
        <div class="text-[11px] text-slate-500 truncate" title="{{ $displayEmail }}">{{ $displayEmail }}</div>
      </div>
    </div>

    <div class="-mx-4 my-3 h-px bg-slate-200/70"></div>

    <div class="flex justify-center text-[13px] text-slate-600 mt-3">
      <div class="flex items-center gap-6">
        <a href="{{ $r('faculty.settings', '/faculty/settings') }}"
          class="flex items-center gap-2 hover:text-slate-900">
          <i data-lucide="settings" class="h-4 w-4"></i><span>Settings</span>
        </a>
        <form method="POST" action="{{ $r('faculty.logout', '/faculty/logout') }}" class="contents">
          @csrf
          <button type="submit" class="flex items-center gap-2 hover:text-slate-900">
            <i data-lucide="log-out" class="h-4 w-4"></i><span>Log out</span>
          </button>
        </form>
      </div>
    </div>

    <div class="-mx-4 my-3 h-px bg-slate-200/70"></div>

    <nav class="mt-4 space-y-2">
      @foreach($navItems as $it)
        @php $is = $active === $it['key']; @endphp
        <a href="{{ $it['href'] }}" class="{{ $is ? $activeLink : $inactiveLink }}"
          aria-current="{{ $is ? 'page' : 'false' }}">
          <span class="flex items-center gap-3">
            <i data-lucide="{{ $it['icon'] }}" class="h-5 w-5 {{ $is ? 'text-white' : 'text-gray-500' }}"></i>
            <span class="text-[14px]">{{ $it['label'] }}</span>
          </span>
        </a>
      @endforeach
    </nav>
  </div>
</aside>

{{-- ===== Mobile bottom nav (5+ items; will wrap if many) ===== --}}
<nav
  class="md:hidden fixed bottom-0 inset-x-0 z-40 border-t border-slate-200 bg-white/95 backdrop-blur supports-[padding:max(0px)]:[padding-bottom:env(safe-area-inset-bottom)]">
  <ul class="grid grid-cols-5">
    @foreach($navItems as $it)
      @php $is = $active === $it['key']; @endphp
      <li>
        <a href="{{ $it['href'] }}"
          class="relative flex items-center justify-center py-2.5 text-slate-500 hover:text-slate-700 {{ $is ? 'text-[#009b56]' : '' }}"
          aria-current="{{ $is ? 'page' : 'false' }}" aria-label="{{ $it['label'] }}">
          @if($is)
            <span class="absolute -top-px h-0.5 w-8 rounded-full bg-[#009b56]"></span>
          @endif
          <i data-lucide="{{ $it['icon'] }}" class="h-5 w-5"></i>
          <span class="sr-only">{{ $it['label'] }}</span>
        </a>
      </li>
    @endforeach
  </ul>
</nav>

{{-- ===== Tiny JS to toggle mobile menu ===== --}}
<script>
  (function () {
    const btn = document.getElementById('mobMenuBtn');
    const panel = document.getElementById('mobMenuPanel');
    const overlay = document.getElementById('mobMenuOverlay');

    if (!btn || !panel || !overlay) return;

    function openMenu() {
      panel.classList.remove('hidden');
      overlay.classList.remove('hidden');
      btn.setAttribute('aria-expanded', 'true');
    }
    function closeMenu() {
      panel.classList.add('hidden');
      overlay.classList.add('hidden');
      btn.setAttribute('aria-expanded', 'false');
    }

    btn.addEventListener('click', () => {
      const isOpen = panel.classList.contains('hidden') === false;
      isOpen ? closeMenu() : openMenu();
    });
    overlay.addEventListener('click', closeMenu);
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeMenu();
    });

    const btnNewPatient = document.getElementById('btnNewPatient');
    const modalNew = document.getElementById('modalNewPatient');

    function openNew() { modalNew?.classList.remove('hidden'); }
    function closeNew() { modalNew?.classList.add('hidden'); }

    btnNewPatient?.addEventListener('click', (e) => { e.preventDefault(); openNew(); });
    modalNew?.addEventListener('click', (e) => {
      if (e.target.matches('[data-modal-close]')) closeNew();
    });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeNew(); });
  })();
</script>