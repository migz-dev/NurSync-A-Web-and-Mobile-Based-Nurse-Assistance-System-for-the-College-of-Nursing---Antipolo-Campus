{{-- resources/views/faculty/chartings.blade.php --}}
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Chartings · Patients · NurSync (CI)</title>

  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }

    /* Smooth card entrance after skeleton hides */
    @keyframes slide-in-up {
      from {
        transform: translateY(10px);
        opacity: 0;
      }

      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .animate-card-in {
      animation: slide-in-up .35s ease-out both;
      will-change: transform, opacity;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
  <main class="min-h-screen flex">
    {{-- Sidebar --}}
    @include('partials.faculty-sidebar', ['active' => 'chartings'])

    {{-- Main --}}
    <section class="flex-1">
      <div class="container mx-auto px-8 py-12 space-y-6">

        {{-- Page heading --}}
        <header class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
              <i data-lucide="clipboard-list" class="h-4 w-4"></i>
            </span>
            <div>
              <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
                Chartings · Patients & Tasks
              </h1>
              <p class="text-[13px] text-slate-500 mt-1">
                Manage active and discharged patients, open their chartings, and keep bedside tasks organized.
              </p>
            </div>
          </div>

          {{-- Quick actions (desktop) --}}
          <div class="hidden sm:flex items-center gap-2">
            <a href="#" id="btnNewPatient"
               class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-3.5 py-2.5 text-[13px] font-medium text-white shadow-sm hover:bg-emerald-700">
              <i data-lucide="plus-circle" class="h-4 w-4" aria-hidden="true"></i>
              <span>New Patient</span>
            </a>

            <a href="{{ route('faculty.chartings.archives.index') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-orange-500 text-white px-3.5 py-2.5 text-[13px] font-medium shadow-sm hover:bg-orange-600">
              <i data-lucide="archive" class="h-4 w-4"></i>
              <span>Archives</span>
            </a>
          </div>
        </header>

        {{-- Flash messages --}}
        @if (session('success'))
          <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 px-4 py-3 text-sm">
            {{ session('success') }}
          </div>
        @endif

        {{-- Search + dropdown filters --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 space-y-3 shadow-sm">
          <div class="grid gap-3 md:grid-cols-3">
            {{-- Search --}}
            <div class="relative">
              <i data-lucide="search" class="absolute left-3 top-3.5 h-4 w-4 text-slate-400" aria-hidden="true"></i>
              <input id="searchBox" type="text" placeholder="Search (name, MRN, unit, attending)…"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-9 pr-3 py-2.5 text-sm placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-200"
                aria-label="Search patients" />
            </div>

            {{-- Unit / Ward --}}
            <div>
              <label for="unitFilter" class="sr-only">Unit/Ward</label>
              <select id="unitFilter"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-200">
                <option value="all">All Units/Wards</option>
                <option value="ms">Medical Ward (MS)</option>
                <option value="sr">Surgical Ward (SR)</option>
                <option value="medsurg">Medical–Surgical Ward</option>
                <!-- Critical Care -->
                <option value="icu">Intensive Care Unit (ICU)</option>
                <option value="ccu">Coronary Care Unit (CCU)</option>
                <option value="nicu">Neonatal ICU (NICU)</option>
                <option value="picu">Pediatric ICU (PICU)</option>
                <!-- Maternal & Child -->
                <option value="ob">Obstetrics Ward (OB)</option>
                <option value="dr">Delivery Room (DR)</option>
                <option value="pedia">Pediatric Ward (PEDIA)</option>
                <option value="nursery">Newborn/Nursery Unit</option>
                <!-- Emergency & Operating -->
                <option value="er">Emergency Room (ER)</option>
                <option value="or">Operating Room (OR)</option>
                <option value="recovery">PACU / Recovery</option>
                <!-- Specialized & Long-Term -->
                <option value="onco">Oncology Unit</option>
                <option value="orthopedics">Orthopedic Ward</option>
                <option value="neuro">Neurology Unit</option>
                <option value="psych">Psychiatric Ward</option>
                <option value="geriatrics">Geriatric Ward</option>
                <option value="rehab">Rehabilitation Unit</option>
                <option value="dialysis">Dialysis Unit</option>
                <option value="burn">Burn Unit</option>
                <!-- Community & Misc -->
                <option value="chn">Community Health Nursing (CHN)</option>
                <option value="dn">Delivery/Neonatal (DN)</option>
                <option value="fncp">Family/Community NCP (FNCP)</option>
                <option value="triage">Triage Area</option>
                <option value="isolation">Isolation Ward</option>
                <option value="infect">Infection Control Unit</option>
              </select>
            </div>

            {{-- Status --}}
            <div>
              <label for="statusFilter" class="sr-only">Status</label>
              <select id="statusFilter"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-200">
                <option value="all">All Statuses</option>
                <option value="active">Active</option>
                <option value="discharged">Discharged</option>
              </select>
            </div>
          </div>

          {{-- Quick actions (mobile) --}}
          <div class="flex sm:hidden gap-2 pt-1">
            <a href="#" id="btnNewPatientMobile"
               class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-3.5 py-2.5 text-[13px] font-medium text-white shadow-sm hover:bg-emerald-700">
              <i data-lucide="plus-circle" class="h-4 w-4" aria-hidden="true"></i>
              <span>New Patient</span>
            </a>
            <a href="{{ route('faculty.chartings.archives.index') }}"
               class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-orange-500 text-white px-3.5 py-2.5 text-[13px] font-medium shadow-sm hover:bg-orange-600">
              <i data-lucide="archive" class="h-4 w-4"></i>
              <span>Archives</span>
            </a>
          </div>
        </div>

        {{-- Skeleton grid (loading style) --}}
        <div id="skeletonGrid" aria-hidden="true" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          @for ($i = 0; $i < 9; $i++)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
              <div class="animate-pulse space-y-3">
                <div class="flex items-start justify-between gap-3">
                  <div class="flex items-center gap-3">
                    <span class="h-9 w-9 rounded-xl bg-slate-200"></span>
                    <div class="space-y-2">
                      <div class="h-3 w-40 bg-slate-200 rounded"></div>
                      <div class="h-3 w-24 bg-slate-100 rounded"></div>
                    </div>
                  </div>
                  <span class="h-5 w-20 rounded-full bg-slate-100"></span>
                </div>
                <div class="h-3 w-full bg-slate-100 rounded"></div>
                <div class="h-3 w-2/3 bg-slate-100 rounded"></div>
                <div class="mt-2 flex gap-2">
                  <span class="h-8 w-24 rounded-xl bg-slate-100"></span>
                  <span class="h-8 w-24 rounded-xl bg-slate-100"></span>
                  <span class="h-8 w-20 rounded-xl bg-slate-100"></span>
                </div>
              </div>
            </div>
          @endfor
        </div>

        {{-- Patients grid (from DB) --}}
        <div id="cardsGrid" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 hidden">
          @php
            // Ward badge color (borrowed from Procedures Library)
            $wardChip = function (?string $ward) {
              return match ($ward) {
                'Community Health (CHN)' => 'bg-emerald-50 text-emerald-700',
                'OB Ward', 'Obstetrics Ward (OB)', 'Delivery Room (DR)', 'Newborn/Nursery Unit', 'Nursery' => 'bg-pink-50 text-pink-700',
                'Pediatric Ward (PEDIA)', 'Pediatrics (PEDIA)' => 'bg-sky-50 text-sky-700',
                'Medical Ward (MS)', 'Surgical Ward (SR)', 'Medical–Surgical Ward', 'Medical-Surgical (MS)' => 'bg-slate-50 text-slate-700',
                'ICU', 'Intensive Care Unit (ICU)' => 'bg-rose-50 text-rose-700',
                'Oncology Unit', 'Oncology' => 'bg-fuchsia-50 text-fuchsia-700',
                'Isolation Ward', 'Isolation Unit' => 'bg-amber-50 text-amber-700',
                'Endocrine Unit' => 'bg-cyan-50 text-cyan-700',
                'Neurology Unit' => 'bg-indigo-50 text-indigo-700',
                'Psychiatric Ward', 'Psychiatric (PSYCH)' => 'bg-violet-50 text-violet-700',
                'Emergency Room (ER)' => 'bg-orange-50 text-orange-700',
                'Operating Room (OR)' => 'bg-green-50 text-green-700',
                'Trauma Unit', 'Triage Area' => 'bg-red-50 text-red-700',
                'Geriatric Ward' => 'bg-yellow-50 text-yellow-700',
                default => 'bg-slate-50 text-slate-700',
              };
            };
          @endphp

          @forelse ($patients as $p)
            @php
              $name = $p->display_name;
              $mrn = $p->hospital_no ?: '—';
              $age = !is_null($p->age) ? (int) $p->age : ($p->dob ? \Carbon\Carbon::parse($p->dob)->age : null);
              $sex = $p->sex ?: 'U';
              $unit = strtolower($p->ward ?? '');
              $status = strtolower($p->status ?? 'active');

              $statusClass = match ($status) {
                'active' => 'bg-emerald-50 text-emerald-700',
                'discharged' => 'bg-blue-50 text-blue-700',
                'archived' => 'bg-orange-50 text-orange-700',
                default => 'bg-slate-50 text-slate-700',
              };

              $unitLabel = $p->ward ?: 'Unassigned unit';
              $unitBed = ($p->ward ?: '—') . ' — ' . ($p->bed_no ?: '—');
              $attending = $p->attending_physician ?: '—';
              $keywords = strtolower(trim("{$name} {$mrn} {$p->ward} {$attending} {$sex} {$p->bed_no}"));

              $wardChipClass = $wardChip($p->ward);
              $admittedAt = $p->admission_date ? \Carbon\Carbon::parse($p->admission_date)->format('M d, Y H:i') : null;
              $updatedAt = $p->updated_at?->diffForHumans();
            @endphp
<article id="patient-card-{{ $p->id }}"
  class="js-card flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow opacity-0"
  data-patient-id="{{ $p->id }}"
  data-status="{{ $status }}"
  data-unit="{{ $unit }}"
  data-keywords="{{ $keywords }}">

  {{-- Header --}}
  <header class="flex items-start justify-between gap-4">
    <div>
      <h2 class="text-[15px] sm:text-[17px] font-semibold text-slate-900 leading-snug flex items-center gap-3">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-50">
          <i data-lucide="user-round" class="h-5 w-5 text-slate-700" aria-hidden="true"></i>
        </span>
        <span>{{ $name }}</span>
      </h2>
      <p class="mt-1.5 text-[13px] text-slate-600">
        MRN <span class="font-semibold text-slate-800">{{ $mrn }}</span>
        <span class="mx-1 text-slate-300">•</span>
        @if($age !== null)
          {{ $age }} yrs /
        @endif
        {{ $sex }}
      </p>

      <div class="mt-2.5 flex flex-wrap items-center gap-2.5">
        @if($unitLabel)
          <span class="inline-flex items-center rounded-full {{ $wardChipClass }} px-2.5 py-0.5 text-[12px]">
            <i data-lucide="hospital" class="h-3.5 w-3.5 mr-1"></i>
            {{ $unitLabel }}
          </span>
        @endif

        <span class="inline-flex items-center rounded-full {{ $statusClass }} px-2.5 py-0.5 text-[12px] font-semibold">
          {{ ucfirst($status) }}
        </span>
      </div>
    </div>

    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
      <i data-lucide="clipboard-list" class="h-5 w-5"></i>
    </span>
  </header>

  {{-- Meta info --}}
  <div class="mt-4 flex items-start justify-between gap-4 text-[12px] text-slate-500">
    <div class="space-y-1.5">
      <div>
        <span class="font-medium text-slate-700">Unit/Bed:</span>
        <span>{{ $unitBed }}</span>
      </div>
      <div>
        <span class="font-medium text-slate-700">Attending:</span>
        <span>{{ $attending }}</span>
      </div>
    </div>
    <div class="text-right space-y-1.5">
      @if($admittedAt)
        <div>
          <span class="font-medium text-slate-700">Admitted:</span>
          <span>{{ $admittedAt }}</span>
        </div>
      @endif
      <div>
        <span class="font-medium text-slate-700">Updated:</span>
        <span>{{ $updatedAt ?? '—' }}</span>
      </div>
    </div>
  </div>

  {{-- Actions --}}
  <div class="mt-5 pt-3.5 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
    <a href="{{ route('faculty.chartings.patient', $p->id) }}"
      class="inline-flex items-center gap-1.5 rounded-xl border border-purple-200 px-3.5 py-2 text-[13px] font-medium text-purple-700 hover:bg-purple-50"
      aria-label="Open patient records">
      <i data-lucide="file-text" class="h-4 w-4" aria-hidden="true"></i>
      Records
    </a>

    <button type="button"
      class="inline-flex items-center gap-1.5 rounded-xl border border-amber-200 text-amber-800 px-3.5 py-2 text-[13px] font-medium hover:bg-amber-50"
      data-edit-open
      data-edit-action="{{ route('faculty.chartings.patients.update', $p->id) }}"
      data-hospital_no="{{ $p->hospital_no }}"
      data-last_name="{{ $p->last_name }}"
      data-first_name="{{ $p->first_name }}"
      data-middle_name="{{ $p->middle_name }}"
      data-suffix="{{ $p->suffix }}"
      data-sex="{{ $p->sex }}"
      data-dob="{{ $p->dob?->format('Y-m-d') }}"
      data-age="{{ $p->age }}"
      data-contact_no="{{ $p->contact_no }}"
      data-address="{{ $p->address }}"
      data-attending_physician="{{ $p->attending_physician }}"
      data-admitting_diagnosis="{{ $p->admitting_diagnosis }}"
      data-ward="{{ strtolower($p->ward ?? '') }}"
      data-bed_no="{{ $p->bed_no }}"
      data-admission_date="{{ $p->admission_date ? \Carbon\Carbon::parse($p->admission_date)->format('Y-m-d\TH:i') : '' }}"
      data-status="{{ $p->status }}"
      data-notes="{{ $p->notes }}">
      <i data-lucide="edit-3" class="h-4 w-4"></i>
      Edit
    </button>

    <button type="button"
      class="inline-flex items-center gap-1.5 rounded-xl border border-orange-200 text-orange-800 px-3.5 py-2 text-[13px] font-medium hover:bg-orange-50"
      data-archive-action="{{ route('faculty.chartings.patients.archive', $p->id) }}"
      data-archive-method="PATCH"
      data-patient-id="{{ $p->id }}"
      data-patient-name="{{ $name }}">
      <i data-lucide="archive" class="h-4 w-4" aria-hidden="true"></i>
      Archive
    </button>
  </div>
</article>

          @empty
            <div class="col-span-full">
              <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500">
                No patients yet.
              </div>
            </div>
          @endforelse
        </div>

        {{-- Empty state when filters hide all cards --}}
        <div id="emptyFilterState" class="hidden">
          <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500">
            No matches found. Try adjusting your search or filters.
          </div>
        </div>

        {{-- Pager (client-side) --}}
        <div id="pagerShell" class="mt-4 flex items-center justify-between text-[12px] text-slate-500 hidden">
          <div id="pagerSummary">Showing 0–0 of 0 patients</div>
          <div class="flex items-center gap-1">
            <button id="btnPrev"
              class="rounded-lg border px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:hover:bg-transparent"
              disabled>‹ Prev</button>
            <div id="pageButtons" class="flex items-center gap-1"></div>
            <button id="btnNext"
              class="rounded-lg border px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:hover:bg-transparent"
              disabled>Next ›</button>
          </div>
        </div>

      </div>
    </section>
  </main>

  {{-- Footer --}}
  @include('partials.faculty-footer')

  {{-- New Patient Modal (Large Rectangle) --}}
  @include('faculty.chartings._modal-new-patient')
  @include('faculty.chartings._modal-edit-patient')

  {{-- Scripts --}}
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // Icons
    try { lucide.createIcons(); } catch (_) { }

    // Debounce helper (for search)
    const debounce = (fn, ms = 350) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

    // --- Filtering + pagination + skeleton + empty-state ---
    const q          = document.getElementById('searchBox');
    const unitSel    = document.getElementById('unitFilter');
    const statusSel  = document.getElementById('statusFilter');
    const emptyBox   = document.getElementById('emptyFilterState');
    const cardsGrid  = document.getElementById('cardsGrid');
    const skeletonGrid = document.getElementById('skeletonGrid');
    const cards      = [...document.querySelectorAll('.js-card')];

    const pagerShell   = document.getElementById('pagerShell');
    const pagerSummary = document.getElementById('pagerSummary');
    const pageButtons  = document.getElementById('pageButtons');
    const btnPrev      = document.getElementById('btnPrev');
    const btnNext      = document.getElementById('btnNext');

    const pageSize = 12;
    let currentPage = 1;

    function showSkeleton(show) {
      if (!skeletonGrid || !cardsGrid) return;
      skeletonGrid.classList.toggle('hidden', !show);
      cardsGrid.classList.toggle('hidden', show);
    }

    function getFilteredCards() {
      const needle     = (q?.value || '').toLowerCase().trim();
      const wantUnit   = (unitSel?.value || 'all').toLowerCase();
      const wantStatus = (statusSel?.value || 'all').toLowerCase();

      return cards.filter(card => {
        const kw     = (card.dataset.keywords || '').toLowerCase();
        const unit   = (card.dataset.unit || '').toLowerCase();
        const status = (card.dataset.status || '').toLowerCase();

        const textOk   = !needle || kw.includes(needle);
        const unitOk   = wantUnit === 'all' || unit.includes(wantUnit) || kw.includes(wantUnit);
        const statusOk = wantStatus === 'all' || status === wantStatus;

        return textOk && unitOk && statusOk;
      });
    }

    function animateVisibleSlice(visibleCards) {
      visibleCards.forEach((el, idx) => {
        el.style.animationDelay = `${Math.min(idx, 6) * 40}ms`;
        el.classList.remove('opacity-0');
        el.classList.add('animate-card-in');
      });
    }

    function buildPageButtons(totalPages) {
      pageButtons.innerHTML = '';
      const windowSize = 5;
      let start = Math.max(1, currentPage - Math.floor(windowSize / 2));
      let end   = Math.min(totalPages, start + windowSize - 1);
      start     = Math.max(1, Math.min(start, Math.max(1, totalPages - windowSize + 1)));

      const makeBtn = (label, page, isActive = false, disabled = false) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = label;
        btn.className = [
          'rounded-lg px-3 py-1.5 text-sm font-medium',
          isActive ? 'bg-slate-900 text-white' : 'border text-slate-700 hover:bg-slate-50',
          disabled ? 'opacity-50 cursor-not-allowed' : ''
        ].join(' ');
        btn.disabled = disabled;
        if (!disabled && !isActive) {
          btn.addEventListener('click', () => {
            currentPage = page;
            renderWithSkeleton();
            scrollPagerIntoView();
          });
        }
        return btn;
      };

      if (totalPages <= 1) return;

      if (start > 1) {
        pageButtons.appendChild(makeBtn('1', 1, currentPage === 1));
        if (start > 2) {
          const span = document.createElement('span');
          span.textContent = '…';
          span.className = 'px-1 text-slate-400 select-none';
          pageButtons.appendChild(span);
        }
      }

      for (let p = start; p <= end; p++) {
        pageButtons.appendChild(makeBtn(String(p), p, currentPage === p));
      }

      if (end < totalPages) {
        if (end < totalPages - 1) {
          const span = document.createElement('span');
          span.textContent = '…';
          span.className = 'px-1 text-slate-400 select-none';
          pageButtons.appendChild(span);
        }
        pageButtons.appendChild(makeBtn(String(totalPages), totalPages, currentPage === totalPages));
      }
    }

    function scrollPagerIntoView() {
      const rect = pagerShell.getBoundingClientRect();
      const vh   = window.innerHeight || document.documentElement.clientHeight;
      const fullyVisible = rect.top >= 0 && rect.bottom <= vh;
      if (!fullyVisible) pagerShell.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function renderPage() {
      const filtered = getFilteredCards();
      const total    = filtered.length;
      const totalPages = Math.max(1, Math.ceil(total / pageSize));
      if (currentPage > totalPages) currentPage = totalPages;

      // Hide all cards
      cards.forEach(c => {
        c.style.display = 'none';
      });

      // Slice for current page
      const startIdx = (currentPage - 1) * pageSize;
      const endIdx   = Math.min(startIdx + pageSize, total);
      const slice    = [];

      for (let i = startIdx; i < endIdx; i++) {
        const card = filtered[i];
        card.style.display = '';
        card.classList.remove('animate-card-in');
        card.classList.add('opacity-0');
        slice.push(card);
      }

      // Empty state toggle
      const hasAnyCard = cards.length > 0;
      const anyVisible = total > 0;
      emptyBox.classList.toggle('hidden', !hasAnyCard || anyVisible);

      // Pager update
      const humanStart = total === 0 ? 0 : startIdx + 1;
      const humanEnd   = endIdx;
      pagerSummary.textContent = `Showing ${humanStart}–${humanEnd} of ${total} patients`;

      btnPrev.disabled = (currentPage <= 1);
      btnNext.disabled = (currentPage >= totalPages);

      pagerShell.classList.toggle('hidden', totalPages <= 1 || total === 0);
      buildPageButtons(totalPages);

      // Animate
      requestAnimationFrame(() => animateVisibleSlice(slice));
    }

    function renderWithSkeleton() {
      showSkeleton(true);
      const delay = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 220;
      setTimeout(() => {
        renderPage();
        showSkeleton(false);
      }, delay);
    }

    // Make render() still exist for archive code
    function render() {
      renderWithSkeleton();
    }

    // Bindings
    q?.addEventListener('input', debounce(() => { currentPage = 1; renderWithSkeleton(); }, 350));
    unitSel?.addEventListener('change', () => { currentPage = 1; renderWithSkeleton(); });
    statusSel?.addEventListener('change', () => { currentPage = 1; renderWithSkeleton(); });

    btnPrev?.addEventListener('click', () => {
      if (currentPage > 1) {
        currentPage--;
        renderWithSkeleton();
        scrollPagerIntoView();
      }
    });

    btnNext?.addEventListener('click', () => {
      currentPage++;
      renderWithSkeleton();
      scrollPagerIntoView();
    });

    document.addEventListener('DOMContentLoaded', () => {
      renderWithSkeleton();
    });

    // --- New Patient Modal (both desktop & mobile triggers) ---
    const btnNewPatient       = document.getElementById('btnNewPatient');
    const btnNewPatientMobile = document.getElementById('btnNewPatientMobile');
    const modalNew            = document.getElementById('modalNewPatient');

    function openNew() { modalNew?.classList.remove('hidden'); }
    function closeNew() { modalNew?.classList.add('hidden'); }

    btnNewPatient?.addEventListener('click', e => { e.preventDefault(); openNew(); });
    btnNewPatientMobile?.addEventListener('click', e => { e.preventDefault(); openNew(); });

    modalNew?.addEventListener('click', e => {
      if (e.target.matches('[data-modal-close]')) closeNew();
    });

    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') closeNew();
    });

    // --- Archive via SweetAlert2 (AJAX + fade out) ---
    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('[data-archive-action]');
      if (!btn) return;

      const patientName = btn.getAttribute('data-patient-name') || 'this patient';
      const action      = btn.getAttribute('data-archive-action');
      const method      = (btn.getAttribute('data-archive-method') || 'PATCH').toUpperCase();
      const csrf        = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const card        = btn.closest('.js-card');

      const confirm = await Swal.fire({
        title: 'Archive patient?',
        html: `You are about to archive <b>${patientName}</b>.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Archive',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#f97316',
        reverseButtons: true,
        focusCancel: true
      });
      if (!confirm.isConfirmed) return;

      btn.disabled = true;

      try {
        const res = await fetch(action, {
          method,
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({})
        });

        const contentType = res.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) {
          // Non-AJAX redirect fallback — reload to reflect the change
          return location.reload();
        }

        const data = await res.json();
        if (!res.ok || !data?.success) throw new Error(data?.message || 'Archive failed.');

        // Fade out -> remove -> re-render filters/empty state
        if (card) {
          card.style.transition = 'opacity .2s ease, transform .2s ease';
          card.style.opacity = '0';
          card.style.transform = 'scale(.98)';
          setTimeout(() => {
            card.remove();
            const idx = cards.indexOf(card);
            if (idx !== -1) cards.splice(idx, 1);
            render();
          }, 200);
        }

        Swal.fire({
          icon: 'success',
          title: 'Archived',
          text: `${patientName} has been archived.`,
          timer: 1400,
          showConfirmButton: false
        });
      } catch (err) {
        Swal.fire({ icon: 'error', title: 'Archive failed', text: err?.message || 'Please try again.' });
        btn.disabled = false;
      }
    });
  </script>

  <script>
    (function () {
      const modal = document.getElementById('modalEditPatient');
      const form  = document.getElementById('editPatientForm');

      // Map data-* -> input ids
      const map = {
        hospital_no: 'edit_hospital_no',
        last_name: 'edit_last_name',
        first_name: 'edit_first_name',
        middle_name: 'edit_middle_name',
        suffix: 'edit_suffix',
        sex: 'edit_sex',
        dob: 'edit_dob',
        age: 'edit_age',
        contact_no: 'edit_contact_no',
        address: 'edit_address',
        attending_physician: 'edit_attending_physician',
        admitting_diagnosis: 'edit_admitting_diagnosis',
        ward: 'edit_ward',
        bed_no: 'edit_bed_no',
        admission_date: 'edit_admission_date',
        status: 'edit_status',
        notes: 'edit_notes',
      };

      function openEdit(btn) {
        const action = btn.getAttribute('data-edit-action') || '';
        form.setAttribute('action', action);

        Object.keys(map).forEach(key => {
          const el = document.getElementById(map[key]);
          if (!el) return;
          const val = btn.getAttribute(`data-${key}`);
          if (el.tagName === 'SELECT') {
            el.value = (val ?? '').toString();
          } else if (el.tagName === 'TEXTAREA') {
            el.value = val ?? '';
          } else {
            el.value = val ?? '';
          }
        });

        modal.classList.remove('hidden');
      }

      function closeEdit() {
        modal.classList.add('hidden');
      }

      document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-edit-open]');
        if (trigger) {
          e.preventDefault();
          openEdit(trigger);
        }
        if (e.target && e.target.hasAttribute('data-modal-close')) {
          closeEdit();
        }
      });

      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeEdit();
      });
    })();
  </script>

</body>

</html>
