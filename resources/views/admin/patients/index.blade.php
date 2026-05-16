{{-- resources/views/admin/patients/index.blade.php --}}
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Admin • Patient Data · NurSync</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    body {
      font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }

    /* Row entrance animation (same pattern as other admin pages) */
    @keyframes slide-in-up {
      from {
        transform: translateY(6px);
        opacity: 0;
      }

      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .animate-row-in {
      animation: slide-in-up .28s ease-out both;
      will-change: transform, opacity;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
  <main class="min-h-screen flex">
    @include('partials.admin-sidebar', ['active' => 'patient_data'])

    @php
      $filters     = $filters ?? [];
      $onArchives  = request()->routeIs('admin.patient_data.archived');
    @endphp

    <section class="flex-1 min-w-0">
      {{-- Sticky header --}}
      <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-sm">
              <i data-lucide="id-card" class="h-4 w-4"></i>
            </div>
            <div>
              <h1 class="text-[15px] sm:text-[16px] font-semibold leading-tight">
                {{ $onArchives ? 'Patient Data Archives' : 'Patient Data' }}
              </h1>
              <p class="text-[12px] text-slate-500 -mt-0.5">
                {{ $onArchives
                    ? 'Viewing archived patient records across all clinical instructors.'
                    : 'View and archive patient records across all clinical instructors.' }}
              </p>
            </div>
          </div>

          <div class="flex items-center gap-2">
            @if (! $onArchives)
              {{-- Go to Archives --}}
              <a href="{{ route('admin.patient_data.archived') }}"
                 class="inline-flex items-center gap-2 rounded-xl bg-slate-700 text-white px-3 py-2 text-[13px] font-medium shadow hover:bg-slate-800 active:scale-[.99]">
                <i data-lucide="archive" class="h-4 w-4"></i>
                <span>Archives</span>
              </a>
            @else
              {{-- Back to main list --}}
              <a href="{{ route('admin.patient_data.index') }}"
                 class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 text-white px-3 py-2 text-[13px] font-medium shadow hover:bg-emerald-700 active:scale-[.99]">
                <i data-lucide="list" class="h-4 w-4"></i>
                <span>Back to Patient List</span>
              </a>
            @endif
          </div>
        </div>
      </header>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">
        {{-- Flash --}}
        @if (session('success'))
          <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
          </div>
        @endif
        @if ($errors->any())
          <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ $errors->first() }}
          </div>
        @endif

        {{-- SKELETON: filters + table --}}
        <div id="apSkeleton" aria-hidden="true" class="space-y-4">
          {{-- Skeleton filters --}}
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
            <div class="animate-pulse flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
              <div class="flex flex-col gap-3 md:flex-row md:items-center w-full md:w-auto flex-wrap">
                <div class="h-10 w-full md:w-80 bg-slate-100 rounded-xl"></div>
                <div class="h-10 w-40 bg-slate-100 rounded-xl"></div>
                <div class="h-10 w-40 bg-slate-100 rounded-xl"></div>
                <div class="h-10 w-40 bg-slate-100 rounded-xl"></div>
              </div>
              <div class="flex items-center justify-end">
                <div class="h-9 w-24 bg-slate-100 rounded-xl"></div>
              </div>
            </div>
          </div>

          {{-- Skeleton table --}}
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
            <div class="animate-pulse space-y-3">
              <div class="h-4 w-40 bg-slate-100 rounded"></div>
              <div class="space-y-2 mt-3">
                @for ($i = 0; $i < 5; $i++)
                  <div class="flex items-center gap-3">
                    <div class="flex-1 space-y-1">
                      <div class="h-3 w-64 bg-slate-100 rounded"></div>
                      <div class="h-3 w-40 bg-slate-100 rounded"></div>
                    </div>
                    <div class="h-7 w-24 bg-slate-100 rounded-lg ml-auto"></div>
                  </div>
                @endfor
              </div>
            </div>
          </div>
        </div>

        {{-- REAL CONTENT (hidden until skeleton is done) --}}
        <div id="apReal" class="space-y-6 hidden">
          {{-- Filters --}}
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
            <form class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between" onsubmit="return false;">

              {{-- Left block: search + dropdowns --}}
              <div class="flex flex-col gap-3 md:flex-row md:items-center w-full md:w-auto flex-wrap">

                {{-- Search --}}
                <div class="relative flex-1 min-w-[220px] md:w-80">
                  <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
                  <input
                    id="ap-filter-q"
                    type="text"
                    value="{{ $filters['q'] ?? '' }}"
                    placeholder="Search name / MRN / ward / attending"
                    class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300"
                  >
                </div>

                {{-- Ward --}}
                <select
                  id="ap-filter-ward"
                  class="min-w-[150px] shrink-0 rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300"
                >
                  <option value="">All wards</option>
                  @foreach ($wards as $w)
                    <option value="{{ $w }}" @selected(($filters['ward'] ?? '') == $w)>{{ $w }}</option>
                  @endforeach
                </select>

                {{-- Status --}}
                <select
                  id="ap-filter-status"
                  class="min-w-[150px] shrink-0 rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300"
                >
                  <option value="">All statuses</option>
                  <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                  <option value="discharged" @selected(($filters['status'] ?? '') === 'discharged')>Discharged</option>
                  <option value="archived" @selected(($filters['status'] ?? '') === 'archived')>Archived</option>
                </select>

                {{-- Optional: per-page --}}
                @isset($perPageOptions)
                  <select
                    id="ap-filter-per"
                    class="min-w-[110px] shrink-0 rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300"
                  >
                    @foreach ($perPageOptions as $opt)
                      <option value="{{ $opt }}" @selected(($filters['per'] ?? null) == $opt)>
                        {{ $opt }} / page
                      </option>
                    @endforeach
                  </select>
                @endisset
              </div>

              {{-- Right block: Filter button --}}
              <div class="flex items-center gap-3 justify-end">
                <button
                  id="ap-btn-filter"
                  type="button"
                  class="rounded-xl border border-slate-200 bg-white text-[13px] px-3 py-2.5 hover:bg-slate-50"
                >
                  Filter
                </button>
              </div>
            </form>
          </div>

          {{-- Table --}}
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
              <table class="min-w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                  <tr class="text-left text-slate-600">
                    <th class="px-4 py-3">Patient / Identifier</th>
                    <th class="px-4 py-3">Ward / Bed</th>
                    <th class="px-4 py-3">Attending / CI</th>
                    <th class="px-4 py-3">Admission / Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                  </tr>
                </thead>

                <tbody id="ap-table" class="divide-y divide-slate-200">
                  {{-- rows partial will add js-ap-row + opacity-0 --}}
                  @include('admin.patients._rows', ['patients' => $patients])
                </tbody>
              </table>
            </div>

            @include('admin.patients._pager', ['patients' => $patients])
          </div>
        </div> {{-- #apReal --}}
      </div>
    </section>
  </main>

  @include('partials.admin-footer')

  <script src="https://unpkg.com/lucide@latest"></script>
  <script> if (window.lucide?.createIcons) lucide.createIcons(); </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // Helpers
    const apqs  = s => document.querySelector(s);
    const apqsa = s => Array.from(document.querySelectorAll(s));
    const apDebounce = (fn, ms = 300) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); } };

    // Use correct base URL depending on whether we're on main list or archives
    const AP_INDEX_URL = '{{ $onArchives ? route('admin.patient_data.archived') : route('admin.patient_data.index') }}';

    const apSkeleton = apqs('#apSkeleton');
    const apReal     = apqs('#apReal');

    // Row animation (same as other admin pages)
    function apAnimateRows(scope = document) {
      const rows = apqsa('.js-ap-row', scope);
      const prefersReduced = window.matchMedia &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;

      if (!rows.length) return;

      if (prefersReduced) {
        rows.forEach(r => r.classList.remove('opacity-0'));
        return;
      }

      rows.forEach((row, idx) => {
        row.style.animationDelay = `${Math.min(idx, 10) * 25}ms`;
        requestAnimationFrame(() => {
          row.classList.add('animate-row-in');
          row.classList.remove('opacity-0');
        });
      });
    }

    // Skeleton -> real reveal
    function apRevealWithAnimation() {
      if (apSkeleton) apSkeleton.classList.add('hidden');
      if (apReal) apReal.classList.remove('hidden');
      apAnimateRows();
      if (window.lucide?.createIcons) lucide.createIcons();
    }

    async function apFetchList(params = {}) {
      const url  = new URL(AP_INDEX_URL, window.location.origin);
      const q    = params.q      ?? (apqs('#ap-filter-q')?.value || '');
      const ward = params.ward   ?? (apqs('#ap-filter-ward')?.value || '');
      const stat = params.status ?? (apqs('#ap-filter-status')?.value || '');
      const page = params.page   ?? '';
      const per  = params.per    ?? (apqs('#ap-filter-per')?.value || '');

      if (q)    url.searchParams.set('q', q);
      if (ward) url.searchParams.set('ward', ward);
      if (stat) url.searchParams.set('status', stat);
      if (page) url.searchParams.set('page', page);
      if (per)  url.searchParams.set('per', per);

      const tbody = apqs('#ap-table');
      const pager = apqs('#ap-pager');

      if (tbody) tbody.innerHTML =
        `<tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">Loading…</td></tr>`;
      if (pager) pager.innerHTML = '';

      try {
        const res = await fetch(url.toString(), {
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error('Network error');

        const data = await res.json();

        if (tbody) tbody.innerHTML =
          data.rows || `<tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">No results.</td></tr>`;

        if (pager) pager.outerHTML =
          data.pager || `<div id="ap-pager" class="flex items-center justify-end px-4 py-3 border-t border-slate-200 bg-slate-50"></div>`;

        if (window.lucide?.createIcons) lucide.createIcons();
        apBindPager();
        apBindArchive();
        apAnimateRows();
      } catch (e) {
        if (tbody) tbody.innerHTML =
          `<tr><td colspan="5" class="px-4 py-8 text-center text-red-600">Failed to load.</td></tr>`;
      }
    }

    function apBindFilters() {
      apqs('#ap-filter-q')?.addEventListener('input', apDebounce(() => apFetchList({ page: 1 }), 300));
      apqs('#ap-filter-ward')?.addEventListener('change', () => apFetchList({ page: 1 }));
      apqs('#ap-filter-status')?.addEventListener('change', () => apFetchList({ page: 1 }));
      apqs('#ap-filter-per')?.addEventListener('change', () => apFetchList({ page: 1 }));
      apqs('#ap-btn-filter')?.addEventListener('click', () => apFetchList({ page: 1 }));
    }

    function apBindPager() {
      apqsa('.ap-page').forEach(btn => {
        if (btn.dataset.bound === '1') return;
        btn.dataset.bound = '1';
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          const p = e.currentTarget.getAttribute('data-page');
          if (p) apFetchList({ page: p });
        }, { once: true });
      });
    }

    // Archive confirmation (SweetAlert)
    function apBindArchive() {
      apqsa('.ap-archive-form').forEach(form => {
        if (form.dataset.bound === '1') return;
        form.dataset.bound = '1';
        form.dataset.archiving = '0';

        form.addEventListener('submit', function (e) {
          e.preventDefault();
          if (this.dataset.archiving === '1') return;

          const name = this.dataset.title || 'this patient record';
          const proceed = () => { this.dataset.archiving = '1'; this.submit(); };

          if (window.Swal) {
            Swal.fire({
              title: 'Archive patient record?',
              html: `Are you sure you want to archive <b>${name}</b>? You can restore this later from Archives.`,
              icon: 'question',
              showCancelButton: true,
              confirmButtonText: 'Yes, archive',
              cancelButtonText: 'Cancel',
              reverseButtons: true,
              focusCancel: true,
              confirmButtonColor: '#0f766e',
              cancelButtonColor: '#334155'
            }).then(result => {
              if (result.isConfirmed) proceed(); else this.dataset.archiving = '0';
            });
          } else {
            if (confirm(`Archive ${name}? You can restore this later from Archives.`)) {
              proceed();
            } else {
              this.dataset.archiving = '0';
            }
          }
        });
      });
    }

    document.addEventListener('DOMContentLoaded', () => {
      const prefersReduced = window.matchMedia &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      const delay = prefersReduced ? 0 : 220;

      setTimeout(apRevealWithAnimation, delay);

      apBindFilters();
      apBindPager();
      apBindArchive(); // initial bind for archive buttons
    });
  </script>

</body>
</html>
