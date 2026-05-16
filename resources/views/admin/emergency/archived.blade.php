{{-- resources/views/admin/emergency/archived.blade.php --}}
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Admin • Emergency Protocol Archives · NurSync</title>
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
    @include('partials.admin-sidebar', ['active' => 'emergency_protocols'])

    <section class="flex-1 min-w-0">
      {{-- Sticky header --}}
      <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-800 text-white shadow-sm">
              <i data-lucide="archive" class="h-4 w-4"></i>
            </div>
            <div>
              <h1 class="text-[15px] sm:text-[16px] font-semibold leading-tight">
                Emergency Protocol Archives
              </h1>
              <p class="text-[12px] text-slate-500 -mt-0.5">
                View and manage archived emergency algorithms. Restore or permanently delete as needed.
              </p>
            </div>
          </div>

          <div class="flex items-center gap-2">
            {{-- Back to active list --}}
            <a href="{{ route('admin.emergency_protocols.index') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white text-[13px] font-medium text-slate-700 px-3 py-2 shadow-sm hover:bg-slate-50 active:scale-[.99]">
              <i data-lucide="arrow-left" class="h-4 w-4"></i>
              <span>Back to list</span>
            </a>
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

        @php
          $filters = $filters ?? [];
        @endphp

        {{-- SKELETON: filters + table --}}
        <div id="epSkeleton" aria-hidden="true" class="space-y-4">
          {{-- Skeleton filters --}}
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
            <div class="animate-pulse flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
              <div class="flex flex-col gap-3 md:flex-row md:items-center w-full md:w-auto flex-wrap">
                <div class="h-10 w-full md:w-80 bg-slate-100 rounded-xl"></div>
                <div class="h-10 w-40 bg-slate-100 rounded-xl"></div>
                <div class="h-10 w-40 bg-slate-100 rounded-xl"></div>
                <div class="h-10 w-40 bg-slate-100 rounded-xl"></div>
                <div class="h-10 w-44 bg-slate-100 rounded-xl"></div>
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
        <div id="epReal" class="space-y-6 hidden">
          {{-- Filters --}}
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
            <form class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between" onsubmit="return false;">

              {{-- Left block: search + dropdowns --}}
              <div class="flex flex-col gap-3 md:flex-row md:items-center w-full md:w-auto flex-wrap">

                {{-- Search --}}
                <div class="relative flex-1 min-w-[220px] md:w-80">
                  <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
                  <input
                    id="ep-filter-q"
                    type="text"
                    value="{{ $filters['q'] ?? '' }}"
                    placeholder="Search title / summary / ward / category"
                    class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300"
                  >
                </div>

                {{-- Category --}}
                <select
                  id="ep-filter-category"
                  class="min-w-[130px] shrink-0 rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                  <option value="">All categories</option>
                  @foreach ($categories as $c)
                    <option value="{{ $c }}" @selected(($filters['category'] ?? '') == $c)>{{ $c }}</option>
                  @endforeach
                </select>

                {{-- Ward --}}
                <select
                  id="ep-filter-ward"
                  class="min-w-[130px] shrink-0 rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                  <option value="">All wards</option>
                  @foreach ($wards as $w)
                    <option value="{{ $w }}" @selected(($filters['ward'] ?? '') == $w)>{{ $w }}</option>
                  @endforeach
                </select>

                {{-- Severity --}}
                <select
                  id="ep-filter-severity"
                  class="min-w-[130px] shrink-0 rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                  <option value="">All severities</option>
                  @foreach ($severities as $sev)
                    <option value="{{ $sev }}" @selected(($filters['severity'] ?? '') == $sev)>{{ $sev }}</option>
                  @endforeach
                </select>

                {{-- Status: locked to Archived --}}
                <select
                  id="ep-filter-status"
                  class="min-w-[170px] shrink-0 rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300"
                  disabled>
                  <option value="archived" selected>Archived only</option>
                </select>
              </div>

              {{-- Right block: Filter button --}}
              <div class="flex items-center gap-3 justify-end">
                <button
                  id="ep-btn-filter"
                  type="button"
                  class="rounded-xl border border-slate-200 bg-white text-[13px] px-3 py-2.5 hover:bg-slate-50">
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
                    <th class="px-4 py-3">Protocol / Owner</th>
                    <th class="px-4 py-3">Category / Ward</th>
                    <th class="px-4 py-3">Severity / Status</th>
                    <th class="px-4 py-3">Updated</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                  </tr>
                </thead>

                <tbody id="ep-table" class="divide-y divide-slate-200">
                  {{-- rows partial will add js-ep-row + opacity-0 --}}
                  @include('admin.emergency._rows', ['protocols' => $protocols])
                </tbody>
              </table>
            </div>

            @include('admin.emergency._pager', ['protocols' => $protocols])
          </div>
        </div> {{-- #epReal --}}
      </div>
    </section>
  </main>

  @include('partials.admin-footer')

  <script src="https://unpkg.com/lucide@latest"></script>
  <script> if (window.lucide?.createIcons) lucide.createIcons(); </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // Helpers
    const epqs = s => document.querySelector(s);
    const epqsa = s => Array.from(document.querySelectorAll(s));
    const epDebounce = (fn, ms = 300) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); } };

    // NOTE: point this to the archived route
    const EP_INDEX_URL = '{{ route('admin.emergency_protocols.archived') }}';

    const epSkeleton = epqs('#epSkeleton');
    const epReal = epqs('#epReal');

    // Row animation (same as other admin pages)
    function epAnimateRows(scope = document) {
      const rows = epqsa('.js-ep-row', scope);
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
    function epRevealWithAnimation() {
      if (epSkeleton) epSkeleton.classList.add('hidden');
      if (epReal) epReal.classList.remove('hidden');
      epAnimateRows();
      if (window.lucide?.createIcons) lucide.createIcons();
    }

    async function epFetchList(params = {}) {
      const url = new URL(EP_INDEX_URL, window.location.origin);
      const q = params.q ?? (epqs('#ep-filter-q')?.value || '');
      const cat = params.cat ?? (epqs('#ep-filter-category')?.value || '');
      const ward = params.ward ?? (epqs('#ep-filter-ward')?.value || '');
      const sev = params.sev ?? (epqs('#ep-filter-severity')?.value || '');
      // Hard-lock status to archived even if select is disabled
      const stat = 'archived';
      const fac = params.fac ?? (epqs('#ep-filter-faculty')?.value || '');
      const page = params.page ?? '';
      const per = params.per ?? (epqs('#ep-filter-per')?.value || '');

      if (q) url.searchParams.set('q', q);
      if (cat) url.searchParams.set('category', cat);
      if (ward) url.searchParams.set('ward', ward);
      if (sev) url.searchParams.set('severity', sev);
      if (stat) url.searchParams.set('status', stat);
      if (fac) url.searchParams.set('faculty_id', fac);
      if (page) url.searchParams.set('page', page);
      if (per) url.searchParams.set('per', per);

      const tbody = epqs('#ep-table');
      const pager = epqs('#ep-pager');

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
          data.pager || `<div id="ep-pager" class="flex items-center justify-end px-4 py-3 border-t border-slate-200 bg-slate-50"></div>`;

        if (window.lucide?.createIcons) lucide.createIcons();
        epBindPager();
        epBindDelete();
        epAnimateRows(); // animate freshly loaded rows
      } catch (e) {
        if (tbody) tbody.innerHTML =
          `<tr><td colspan="5" class="px-4 py-8 text-center text-red-600">Failed to load.</td></tr>`;
      }
    }

    function epBindFilters() {
      epqs('#ep-filter-q')?.addEventListener('input', epDebounce(() => epFetchList({ page: 1 }), 300));
      epqs('#ep-filter-category')?.addEventListener('change', () => epFetchList({ page: 1 }));
      epqs('#ep-filter-ward')?.addEventListener('change', () => epFetchList({ page: 1 }));
      epqs('#ep-filter-severity')?.addEventListener('change', () => epFetchList({ page: 1 }));
      // status is locked to archived, so no change listener
      epqs('#ep-filter-faculty')?.addEventListener('change', () => epFetchList({ page: 1 }));
      epqs('#ep-filter-per')?.addEventListener('change', () => epFetchList({ page: 1 }));
      epqs('#ep-btn-filter')?.addEventListener('click', () => epFetchList({ page: 1 }));
    }

    function epBindPager() {
      epqsa('.ep-page').forEach(btn => {
        if (btn.dataset.bound === '1') return;
        btn.dataset.bound = '1';
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          const p = e.currentTarget.getAttribute('data-page');
          if (p) epFetchList({ page: p });
        }, { once: true });
      });
    }

    function epBindDelete() {
      epqsa('.ep-delete-form').forEach(form => {
        if (form.dataset.bound === '1') return;
        form.dataset.bound = '1';
        form.dataset.deleting = '0';

        form.addEventListener('submit', function (e) {
          e.preventDefault();
          if (this.dataset.deleting === '1') return;

          const name = this.dataset.title || 'this protocol';
          const proceed = () => { this.dataset.deleting = '1'; this.submit(); };

          if (window.Swal) {
            Swal.fire({
              title: 'Delete protocol?',
              html: `Are you sure you want to delete <b>${name}</b>? This cannot be undone.`,
              icon: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Yes, delete',
              cancelButtonText: 'Cancel',
              reverseButtons: true,
              focusCancel: true,
              confirmButtonColor: '#dc2626',
              cancelButtonColor: '#334155'
            }).then(result => { if (result.isConfirmed) proceed(); else this.dataset.deleting = '0'; });
          } else {
            if (confirm(`Delete ${name}? This cannot be undone.`)) proceed(); else this.dataset.deleting = '0';
          }
        });
      });
    }

    document.addEventListener('DOMContentLoaded', () => {
      const prefersReduced = window.matchMedia &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      const delay = prefersReduced ? 0 : 220;

      // skeleton -> real reveal
      setTimeout(epRevealWithAnimation, delay);

      epBindFilters();
      epBindPager();
      epBindDelete();
      // initial rows will animate on reveal via epAnimateRows()
    });
  </script>

</body>

</html>
