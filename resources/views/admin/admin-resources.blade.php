{{-- resources/views/admin/admin-resources.blade.php --}}
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Admin • Procedures · NurSync</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    body {
      font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }

    /* Row entrance animation (same vibe as admin users) */
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

    /* TABLE PAGE TRANSITION (when moving between pages) */
    @keyframes table-page-in {
      from {
        opacity: 0;
        transform: translateY(4px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animate-table-page-in {
      animation: table-page-in .18s ease-out both;
      will-change: opacity, transform;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
  <main class="min-h-screen flex">
    {{-- Sidebar --}}
    @include('partials.admin-sidebar', ['active' => 'resources'])

    <section class="flex-1 min-w-0">
      {{-- Header --}}
      <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-900 text-white shadow-sm">
              <i data-lucide="stethoscope" class="h-4 w-4"></i>
            </div>
            <div>
              <h1 class="text-[15px] sm:text-[16px] font-semibold leading-tight">Procedures Library</h1>
              <p class="text-[12px] text-slate-500 -mt-0.5">
                Manage and review procedures created by Clinical Instructors.
              </p>
            </div>
          </div>

          <div class="flex items-center gap-2">
            {{-- Create --}}
            <a href="{{ route('admin.procedures.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-green-600 text-white px-3 py-2 text-[13px] font-medium shadow hover:bg-green-700 active:scale-[.99]">
              <i data-lucide="plus" class="h-4 w-4"></i>
              <span>Create</span>
            </a>
            {{-- Return Demo Skills --}}
            <a href="{{ route('admin.return_demo.skills.index') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-amber-500 text-white px-3 py-2 text-[13px] font-medium shadow hover:bg-amber-600 active:scale-[.99]">
              <i data-lucide="clipboard-check" class="h-4 w-4"></i>
              <span>Return Demo Skills</span>
            </a>

            {{-- Archives --}}
            <a href="{{ route('admin.procedures.archived') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-slate-700 text-white px-3 py-2 text-[13px] font-medium shadow hover:bg-slate-800 active:scale-[.99]">
              <i data-lucide="archive" class="h-4 w-4"></i>
              <span>Archives</span>
            </a>
          </div>

        </div>
      </header>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">
        {{-- Flash --}}
        @if (session('ok'))
          <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('ok') }}
          </div>
        @endif
        @if ($errors->any())
          <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ $errors->first() }}
          </div>
        @endif

        @php
          $wards = [
            'Community Health (CHN)',
            'Delivery Room (DR)',
            'Disaster Response / Community Field',
            'Emergency Room (ER)',
            'Endocrine Unit',
            'ICU',
            'Isolation Unit',
            'Medical-Surgical (MS)',
            'Neurology Unit',
            'Nursery',
            'OB Ward',
            'Oncology',
            'Operating Room (OR)',
            'Pediatrics (PEDIA)',
            'Psychiatric (PSYCH)',
            'Trauma Unit',
          ];
          sort($wards, SORT_NATURAL | SORT_FLAG_CASE);
          $procedures = $procedures ?? null;
        @endphp

        {{-- SKELETON: filters + table --}}
        <div id="proceduresSkeleton" aria-hidden="true" class="space-y-4">
          {{-- Skeleton filters --}}
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
            <div class="animate-pulse flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
              <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="h-10 w-full sm:w-72 bg-slate-100 rounded-xl"></div>
                <div class="h-10 w-44 bg-slate-100 rounded-xl hidden sm:block"></div>
                <div class="h-10 w-48 bg-slate-100 rounded-xl hidden sm:block"></div>
              </div>
              <div class="flex items-center gap-2">
                <div class="h-8 w-20 bg-slate-100 rounded-xl"></div>
              </div>
            </div>
          </div>

          {{-- Skeleton table card --}}
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
            <div class="animate-pulse space-y-3">
              <div class="h-4 w-40 bg-slate-100 rounded"></div>
              <div class="space-y-2 mt-3">
                @for ($i = 0; $i < 4; $i++)
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
        <div id="proceduresReal" class="space-y-6 hidden">

          {{-- Filters --}}
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
            <form class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" onsubmit="return false;">
              <div class="flex flex-1 flex-col sm:flex-row gap-3 sm:items-center">
                <div class="relative flex-1 sm:w-72">
                  <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
                  <input id="filter-q" type="text" value="{{ request('q') }}" placeholder="Search procedure name..."
                    class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300">
                </div>

                <select id="filter-status"
                  class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                  <option value="">All statuses</option>
                  <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                  <option value="published" @selected(request('status') === 'published')>Published</option>
                </select>

                <select id="filter-ward"
                  class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                  <option value="">All wards/areas</option>
                  @foreach($wards as $w)
                    <option value="{{ $w }}" @selected(request('ward') === $w)>{{ $w }}</option>
                  @endforeach
                </select>
              </div>
            </form>
          </div>

          {{-- Table --}}
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
              <table class="min-w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                  <tr class="text-left text-slate-600">
                    <th class="px-4 py-3">Procedure Name</th>
                    <th class="px-4 py-3">Wards</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Created</th>
                    <th class="px-4 py-3">Created By</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                  </tr>
                </thead>

                <tbody id="proc-table" class="divide-y divide-slate-200">
                  @if ($procedures)
                    @php $rendered = 0; @endphp
                    @foreach ($procedures as $p)
                      @if (!empty($p->is_archived) && (int) $p->is_archived === 1)
                        @continue {{-- hide archived from active page on first render --}}
                      @endif
                      @php $rendered++; @endphp

                      {{-- animated row --}}
                      <tr class="hover:bg-slate-50 js-proc-row opacity-0">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $p->title ?: '—' }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $p->clinical_wards ?: '—' }}</td>

                        <td class="px-4 py-3">
                          @if ($p->status === 'published')
                            <span
                              class="inline-flex items-center gap-1.5 rounded-lg bg-green-50 text-green-700 px-2 py-1 text-[12px] font-medium">
                              <i data-lucide="check-circle" class="h-3.5 w-3.5"></i> Published
                            </span>
                          @else
                            <span
                              class="inline-flex items-center gap-1.5 rounded-lg bg-yellow-50 text-yellow-700 px-2 py-1 text-[12px] font-medium">
                              <i data-lucide="clock" class="h-3.5 w-3.5"></i> Draft
                            </span>
                          @endif
                        </td>

                        <td class="px-4 py-3 text-slate-700">{{ optional($p->created_at)->format('M d, Y') ?: '—' }}</td>

                        <td class="px-4 py-3 text-slate-700">
                          @if ($p->created_by_admin)
                            {{ optional($p->adminCreator)->full_name ?? '—' }}
                          @elseif ($p->created_by)
                            {{ optional($p->author)->full_name ?? '—' }}
                          @else
                            —
                          @endif
                        </td>

                        <td class="px-4 py-3">
                          <div class="flex items-center justify-end gap-1.5">
                            <a href="{{ route('admin.procedures.show', $p) }}"
                              class="inline-flex items-center justify-center rounded-lg bg-blue-600 text-white p-2 hover:bg-blue-700"
                              title="View">
                              <i data-lucide="eye" class="h-4 w-4"></i>
                            </a>

                            <a href="{{ route('admin.procedures.edit', $p) }}"
                              class="inline-flex items-center justify-center rounded-lg bg-yellow-400 text-slate-900 p-2 hover:brightness-95"
                              title="Edit">
                              <i data-lucide="pencil" class="h-4 w-4"></i>
                            </a>

                            {{-- Archive --}}
                            <button type="button"
                              class="inline-flex items-center justify-center rounded-lg bg-orange-500 text-white p-2 hover:bg-orange-600"
                              data-action="archive" data-procedure-id="{{ $p->id }}" data-procedure-title="{{ $p->title }}"
                              data-url="{{ route('admin.procedures.archive', $p) }}">
                              <i data-lucide="archive" class="h-4 w-4"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    @endforeach

                    @if ($rendered === 0)
                      <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">No procedures found.</td>
                      </tr>
                    @endif
                  @else
                    {{-- Fallback if JS disabled --}}
                    <tr>
                      <td colspan="6" class="px-4 py-8 text-center text-slate-500">Loading…</td>
                    </tr>
                  @endif
                </tbody>
              </table>
            </div>

            {{-- Pagination --}}
            @if ($procedures instanceof \Illuminate\Contracts\Pagination\Paginator)
              @php
                $cur = $procedures->currentPage();
                $last = max(1, $procedures->lastPage());
                $window = 3;
                $half = intdiv($window - 1, 2);
                $from = max(1, $cur - $half);
                $to = min($last, $from + $window - 1);
                $from = max(1, $to - $window + 1);
              @endphp

              <div id="proc-pager" class="flex items-center justify-end px-4 py-3 border-t border-slate-200 bg-slate-50">
                <nav class="flex items-center gap-1">
                  @if ($procedures->onFirstPage())
                    <button
                      class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white opacity-50 cursor-not-allowed">
                      <i data-lucide="chevron-left" class="h-4 w-4"></i>
                    </button>
                  @else
                    <button data-page="{{ $procedures->currentPage() - 1 }}"
                      class="js-page h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
                      <i data-lucide="chevron-left" class="h-4 w-4"></i>
                    </button>
                  @endif

                  @for ($i = $from; $i <= $to; $i++)
                    @if ($i === $cur)
                      <span
                        class="h-9 w-9 inline-flex items-center justify-center rounded-lg bg-slate-900 text-white">{{ $i }}</span>
                    @else
                      <button data-page="{{ $i }}"
                        class="js-page h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
                        {{ $i }}
                      </button>
                    @endif
                  @endfor

                  @if ($cur >= $last)
                    <button
                      class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white opacity-50 cursor-not-allowed">
                      <i data-lucide="chevron-right" class="h-4 w-4"></i>
                    </button>
                  @else
                    <button data-page="{{ $procedures->currentPage() + 1 }}"
                      class="js-page h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
                      <i data-lucide="chevron-right" class="h-4 w-4"></i>
                    </button>
                  @endif
                </nav>
              </div>
            @else
              <div id="proc-pager" class="flex items-center justify-end px-4 py-3 border-t border-slate-200 bg-slate-50">
              </div>
            @endif
          </div>
        </div>
      </div>
    </section>
  </main>

  @include('partials.admin-footer')

  <script src="https://unpkg.com/lucide@latest"></script>
  <script> if (window.lucide?.createIcons) lucide.createIcons(); </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // --- helpers
    const qs = s => document.querySelector(s);
    const qsa = s => Array.from(document.querySelectorAll(s));
    const debounce = (fn, ms = 300) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); } };
    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const jsonHeaders = () => ({ 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf() });

    const PROC_BASE = "{{ url('/admin/procedures') }}";
    const ENDPOINTS = {
      archive: id => `${PROC_BASE}/${id}/archive`,
      restore: id => `${PROC_BASE}/${id}/restore`,
    };

    const INDEX_URL = "{{ route('admin.procedures.index') }}";

    const skeleton = qs('#proceduresSkeleton');
    const realWrap = qs('#proceduresReal');

    // Row animation (same pattern as other admin pages)
    function animateRows(scope = document) {
      const rows = qsa('.js-proc-row', scope);
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

    // Reveal real content after skeleton
    function revealProceduresWithAnimation() {
      if (skeleton) skeleton.classList.add('hidden');
      if (realWrap) realWrap.classList.remove('hidden');
      animateRows();
      if (window.lucide?.createIcons) lucide.createIcons();
    }

    async function fetchList(params = {}) {
      const url = new URL(INDEX_URL, window.location.origin);
      const q = params.q ?? (qs('#filter-q')?.value || '');
      const status = params.status ?? (qs('#filter-status')?.value || '');
      const ward = params.ward ?? (qs('#filter-ward')?.value || '');
      const page = params.page ?? '';

      if (q) url.searchParams.set('q', q);
      if (status) url.searchParams.set('status', status);
      if (ward) url.searchParams.set('ward', ward);
      if (page) url.searchParams.set('page', page);

      // force ACTIVE list (never include archived in this page)
      url.searchParams.set('archived', '0');

      const table = qs('#proc-table');
      const pager = qs('#proc-pager');
      if (table) {
        table.innerHTML = `<tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">Loading…</td></tr>`;
      }

      try {
        const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!res.ok) throw new Error('Fetch failed');
        const data = await res.json();

        if (table) table.innerHTML = data.rows || '';
        if (pager) pager.innerHTML = data.pager || '';

        if (window.lucide?.createIcons) lucide.createIcons();
        bindArchive();
        bindPager();
        animateRows(); // animate freshly loaded rows

        // --- TABLE PAGE ANIMATION (every time page changes) ---
        if (table) {
          const prefersReduced = window.matchMedia &&
            window.matchMedia('(prefers-reduced-motion: reduce)').matches;

          if (!prefersReduced) {
            table.classList.remove('animate-table-page-in');
            // force reflow so animation restarts
            void table.offsetWidth;
            table.classList.add('animate-table-page-in');
          } else {
            table.classList.remove('animate-table-page-in');
          }
        }
      } catch {
        if (table) {
          table.innerHTML = `<tr><td colspan="6" class="px-4 py-8 text-center text-red-600">Failed to load list.</td></tr>`;
        }
      }
    }

    function bindFilters() {
      const $q = qs('#filter-q');
      const $status = qs('#filter-status');
      const $ward = qs('#filter-ward');
      const $btn = qs('#btn-filter');

      if ($q) $q.addEventListener('input', debounce(() => fetchList({ page: 1 }), 300));
      if ($status) $status.addEventListener('change', () => fetchList({ page: 1 }));
      if ($ward) $ward.addEventListener('change', () => fetchList({ page: 1 }));
      if ($btn) $btn.addEventListener('click', () => fetchList({ page: 1 }));
    }

    function bindPager() {
      const $pager = qs('#proc-pager');
      if (!$pager) return;
      $pager.querySelectorAll('.js-page').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          const page = e.currentTarget.getAttribute('data-page');
          if (page) fetchList({ page });
        }, { once: true });
      });
    }

    // ARCHIVE (orange button)
    function bindArchive() {
      qsa('[data-action="archive"]').forEach(btn => {
        if (btn._boundArchive === true) return;
        btn._boundArchive = true;

        btn.addEventListener('click', async () => {
          if (btn.dataset.busy === '1') return;
          const id = btn.dataset.procedureId;
          const title = btn.dataset.procedureTitle || 'this procedure';
          const url = btn.dataset.url || ENDPOINTS.archive(id);
          const row = btn.closest('tr');

          const result = await Swal.fire({
            title: 'Archive this procedure?',
            html: `You are about to archive <b>${title}</b>.<br><small>All its steps will be archived as well.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, archive it',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            focusCancel: true,
            confirmButtonColor: '#d97706',
            cancelButtonColor: '#334155'
          });
          if (!result.isConfirmed) return;

          try {
            btn.dataset.busy = '1';
            const res = await fetch(url, {
              method: 'POST',
              headers: jsonHeaders(),
              body: new URLSearchParams({ _method: 'PATCH' })
            });

            if (res.ok) {
              if (row) row.remove();
              Swal.fire({ icon: 'success', title: 'Archived', timer: 1100, showConfirmButton: false });
              await fetchList();
            } else {
              Swal.fire({ icon: 'error', title: 'Archive failed' });
            }
          } catch {
            Swal.fire({ icon: 'error', title: 'Network error' });
          } finally {
            delete btn.dataset.busy;
          }
        });
      });
    }

    document.addEventListener('DOMContentLoaded', () => {
      // Skeleton -> real reveal
      const prefersReduced = window.matchMedia &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      const delay = prefersReduced ? 0 : 220;
      setTimeout(revealProceduresWithAnimation, delay);

      bindFilters();
      bindPager();
      bindArchive();

      // If server didn't send $procedures (Route::view case), load via AJAX
      @if (!$procedures)
        fetchList({ page: 1 });
      @else
        // ensure initial rows animate on blade-rendered list
        animateRows();
      @endif
    });
  </script>
</body>

</html>
