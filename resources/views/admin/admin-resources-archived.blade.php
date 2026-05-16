{{-- resources/views/admin/admin-resources-archived.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Admin • Archived Procedures · NurSync</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    body { font-family:'Poppins', ui-sans-serif, system-ui, sans-serif; }

    /* Smooth row entrance animation (same vibe as other admin tables) */
    @keyframes slide-in-up {
      from { transform: translateY(6px); opacity: 0; }
      to   { transform: translateY(0);   opacity: 1; }
    }

    .animate-row-in {
      animation: slide-in-up .28s ease-out both;
      will-change: transform, opacity;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  @include('partials.admin-sidebar', ['active' => 'resources'])

  <section class="flex-1 min-w-0">
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-900 text-white shadow-sm">
            <i data-lucide="archive" class="h-4 w-4"></i>
          </div>
          <div>
            <h1 class="text-[15px] sm:text-[16px] font-semibold leading-tight">Archived Procedures</h1>
            <p class="text-[12px] text-slate-500 -mt-0.5">View, restore, or permanently delete archived procedures.</p>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <a href="{{ route('admin.procedures.index') }}"
             class="inline-flex items-center gap-2 rounded-xl bg-slate-800 text-white px-3 py-2 text-[13px] font-medium shadow hover:bg-slate-900 active:scale-[.99]">
            <i data-lucide="list" class="h-4 w-4"></i>
            <span>Back to Active</span>
          </a>
        </div>
      </div>
    </header>

    @php $procedures = $procedures ?? null; @endphp

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
          'Community Health (CHN)','Delivery Room (DR)','Disaster Response / Community Field',
          'Emergency Room (ER)','Endocrine Unit','ICU','Isolation Unit','Medical-Surgical (MS)',
          'Neurology Unit','Nursery','OB Ward','Oncology','Operating Room (OR)','Pediatrics (PEDIA)',
          'Psychiatric (PSYCH)','Trauma Unit',
        ];
        sort($wards, SORT_NATURAL | SORT_FLAG_CASE);
      @endphp

      {{-- SKELETON (filters + table) --}}
      <div id="procSkeleton" aria-hidden="true" class="space-y-4">
        {{-- Skeleton filters --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
          <div class="animate-pulse flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
            <div class="flex items-center gap-3 w-full sm:w-auto">
              <div class="h-10 w-full sm:w-72 bg-slate-100 rounded-xl"></div>
              <div class="h-10 w-40 bg-slate-100 rounded-xl hidden sm:block"></div>
              <div class="h-10 w-44 bg-slate-100 rounded-xl hidden sm:block"></div>
            </div>
            <div class="flex items-center gap-2">
              <div class="h-8 w-24 bg-slate-100 rounded-xl"></div>
            </div>
          </div>
        </div>

        {{-- Skeleton table --}}
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

      {{-- REAL CONTENT (hidden until skeleton finishes) --}}
      <div id="procReal" class="space-y-6 hidden">

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
                  <option value="{{ $w }}">{{ $w }}</option>
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
                  <th class="px-4 py-3">Archived</th>
                  <th class="px-4 py-3">Archived By</th>
                  <th class="px-4 py-3 text-right">Actions</th>
                </tr>
              </thead>

              <tbody id="proc-table" class="divide-y divide-slate-200">
                @if ($procedures)
                  @forelse ($procedures as $p)
                    {{-- animated row --}}
                    <tr class="hover:bg-slate-50 js-proc-row opacity-0">
                      <td class="px-4 py-3 font-medium text-slate-900">{{ $p->title ?: '—' }}</td>
                      <td class="px-4 py-3 text-slate-700">{{ $p->clinical_wards ?: '—' }}</td>

                      <td class="px-4 py-3">
                        @if ($p->status === 'published')
                          <span class="inline-flex items-center gap-1.5 rounded-lg bg-green-50 text-green-700 px-2 py-1 text-[12px] font-medium">
                            <i data-lucide="check-circle" class="h-3.5 w-3.5"></i> Published
                          </span>
                        @else
                          <span class="inline-flex items-center gap-1.5 rounded-lg bg-yellow-50 text-yellow-700 px-2 py-1 text-[12px] font-medium">
                            <i data-lucide="clock" class="h-3.5 w-3.5"></i> Draft
                          </span>
                        @endif
                      </td>

                      <td class="px-4 py-3 text-slate-700">
                        {{ optional($p->archived_at)->format('M d, Y') ?: '—' }}
                      </td>

                      <td class="px-4 py-3 text-slate-700">
                        {{ optional($p->adminArchiver)->full_name ?? '—' }}
                      </td>

                      <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1.5">
                          {{-- View --}}
                          <a href="{{ route('admin.procedures.show', $p) }}"
                             class="inline-flex items-center justify-center rounded-lg bg-blue-600 text-white p-2 hover:bg-blue-700"
                             title="View">
                            <i data-lucide="eye" class="h-4 w-4"></i>
                          </a>

                          {{-- Restore --}}
                          <button type="button"
                                  class="inline-flex items-center justify-center rounded-lg bg-emerald-600 text-white p-2 hover:bg-emerald-700"
                                  data-action="restore"
                                  data-procedure-id="{{ $p->id }}"
                                  data-procedure-title="{{ $p->title }}"
                                  data-url="{{ route('admin.procedures.restore', $p) }}"
                                  title="Restore">
                            <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                          </button>

                          {{-- Delete permanently --}}
                          <button type="button"
                                  class="inline-flex items-center justify-center rounded-lg bg-red-600 text-white p-2 hover:bg-red-700"
                                  data-action="delete"
                                  data-procedure-id="{{ $p->id }}"
                                  data-procedure-title="{{ $p->title }}"
                                  data-url="{{ route('admin.procedures.destroy', $p) }}"
                                  title="Delete permanently">
                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="px-4 py-8 text-center text-slate-500">No archived procedures found.</td>
                    </tr>
                  @endforelse
                @else
                  <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-slate-500">Loading…</td>
                  </tr>
                @endif
              </tbody>
            </table>
          </div>

          @if ($procedures instanceof \Illuminate\Contracts\Pagination\Paginator)
            @php
              $cur = $procedures->currentPage();
              $last = max(1, $procedures->lastPage());
              $window = 3; $half = intdiv($window - 1, 2);
              $from = max(1, $cur - $half);
              $to = min($last, $from + $window - 1);
              $from = max(1, $to - $window + 1);
            @endphp

            <div id="proc-pager" class="flex items-center justify-end px-4 py-3 border-t border-slate-200 bg-slate-50">
              <nav class="flex items-center gap-1">
                @if ($procedures->onFirstPage())
                  <button class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white opacity-50 cursor-not-allowed">
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
                    <span class="h-9 w-9 inline-flex items-center justify-center rounded-lg bg-slate-900 text-white">{{ $i }}</span>
                  @else
                    <button data-page="{{ $i }}"
                            class="js-page h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
                      {{ $i }}
                    </button>
                  @endif
                @endfor

                @if ($cur >= $last)
                  <button class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white opacity-50 cursor-not-allowed">
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
            <div id="proc-pager" class="flex items-center justify-end px-4 py-3 border-t border-slate-200 bg-slate-50"></div>
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
  const qs = s => document.querySelector(s);
  const qsa = s => Array.from(document.querySelectorAll(s));
  const debounce = (fn, ms = 300) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); } };
  const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const jsonHeaders = () => ({ 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf() });

  const PROC_BASE = "{{ url('/admin/procedures') }}";
  const ENDPOINTS = {
    restore: id => `${PROC_BASE}/${id}/restore`,
    destroy: id => `${PROC_BASE}/${id}`,
  };

  const INDEX_URL = "{{ route('admin.procedures.index', ['archived' => 1]) }}";

  const skeleton = qs('#procSkeleton');
  const realWrap = qs('#procReal');

  function animateRows(scope = document) {
    const rows = qsa('.js-proc-row', scope);
    if (!rows.length) return;

    const prefersReduced = window.matchMedia &&
      window.matchMedia('(prefers-reduced-motion: reduce)').matches;

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

  function revealProcWithAnimation() {
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
    url.searchParams.set('archived', '1');

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
      bindRestore();
      bindDelete();
      bindPager();
      animateRows();
    } catch {
      if (table) {
        table.innerHTML = `<tr><td colspan="6" class="px-4 py-8 text-center text-red-600">Failed to load list.</td></tr>`;
      }
    }
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

  function bindRestore() {
    qsa('[data-action="restore"]').forEach(btn => {
      if (btn._boundRestore) return;
      btn._boundRestore = true;

      btn.addEventListener('click', async () => {
        if (btn.dataset.busy === '1') return;
        const id = btn.dataset.procedureId;
        const title = btn.dataset.procedureTitle || 'this procedure';
        const url = btn.dataset.url || ENDPOINTS.restore(id);

        const result = await Swal.fire({
          title: 'Restore this procedure?',
          html: `Restore <b>${title}</b> and all of its steps?`,
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Yes, restore',
          cancelButtonText: 'Cancel',
          reverseButtons: true,
          focusCancel: true,
          confirmButtonColor: '#16a34a',
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
            Swal.fire({ icon: 'success', title: 'Restored', timer: 1100, showConfirmButton: false });
            await fetchList();
          } else {
            Swal.fire({ icon: 'error', title: 'Restore failed' });
          }
        } catch {
          Swal.fire({ icon: 'error', title: 'Network error' });
        } finally {
          delete btn.dataset.busy;
        }
      });
    });
  }

  function bindDelete() {
    qsa('[data-action="delete"]').forEach(btn => {
      if (btn._boundDelete) return;
      btn._boundDelete = true;

      btn.addEventListener('click', async () => {
        if (btn.dataset.busy === '1') return;
        const id = btn.dataset.procedureId;
        const title = btn.dataset.procedureTitle || 'this procedure';
        const url = btn.dataset.url || ENDPOINTS.destroy(id);

        const result = await Swal.fire({
          title: 'Permanently delete?',
          html: `This will permanently remove <b>${title}</b>.<br><small>This action cannot be undone.</small>`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, delete',
          cancelButtonText: 'Cancel',
          reverseButtons: true,
          focusCancel: true,
          confirmButtonColor: '#dc2626',
          cancelButtonColor: '#334155'
        });
        if (!result.isConfirmed) return;

        try {
          btn.dataset.busy = '1';
          const res = await fetch(url, {
            method: 'POST',
            headers: jsonHeaders(),
            body: new URLSearchParams({ _method: 'DELETE' })
          });

          if (res.ok) {
            Swal.fire({ icon: 'success', title: 'Deleted', timer: 1100, showConfirmButton: false });
            await fetchList();
          } else {
            Swal.fire({ icon: 'error', title: 'Delete failed' });
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
    const prefersReduced = window.matchMedia &&
      window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const delay = prefersReduced ? 0 : 220;

    setTimeout(revealProcWithAnimation, delay);

    bindRestore();
    bindDelete();
    bindPager();

    const $q = qs('#filter-q');
    const $status = qs('#filter-status');
    const $ward = qs('#filter-ward');
    const refetch = () => fetchList({ page: 1 });

    if ($q) $q.addEventListener('input', debounce(refetch, 300));
    if ($status) $status.addEventListener('change', refetch);
    if ($ward) $ward.addEventListener('change', refetch);

    @if (!$procedures)
      fetchList({ page: 1 });
    @else
      // Animate the server-rendered rows on first load
      animateRows();
    @endif
  });
</script>
</body>
</html>
