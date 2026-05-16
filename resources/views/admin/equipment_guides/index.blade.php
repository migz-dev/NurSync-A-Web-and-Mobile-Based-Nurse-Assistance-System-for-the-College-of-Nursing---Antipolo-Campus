{{-- resources/views/admin/equipment-guides/index.blade.php --}}
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Admin • Equipment Guides · NurSync</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    body {
      font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }

    /* Row entrance animation (aligned with admin procedures page) */
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
  @include('partials.admin-sidebar', ['active' => 'equipment-guides'])

  <section class="flex-1 min-w-0">
    {{-- Sticky header --}}
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-900 text-white shadow-sm">
            <i data-lucide="wrench" class="h-4 w-4"></i>
          </div>
          <div>
            <h1 class="text-[15px] sm:text-[16px] font-semibold leading-tight">Equipment Guides</h1>
            <p class="text-[12px] text-slate-500 -mt-0.5">
              Browse and manage nursing equipment and instruments.
            </p>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <a href="{{ route('admin.equipment_guide.create') }}"
             class="inline-flex items-center gap-2 rounded-xl bg-green-600 text-white px-3 py-2 text-[13px] font-medium shadow hover:bg-green-700 active:scale-[.99]">
            <i data-lucide="plus" class="h-4 w-4"></i>
            <span>Create</span>
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

      {{-- SKELETON: filters + table (mirrors admin procedures page) --}}
      <div id="egSkeleton" aria-hidden="true" class="space-y-4">
        {{-- Skeleton filters --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
          <div class="animate-pulse flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
            <div class="flex items-center gap-3 w-full sm:w-auto">
              <div class="h-10 w-full sm:w-80 bg-slate-100 rounded-xl"></div>
              <div class="h-10 w-40 bg-slate-100 rounded-xl hidden sm:block"></div>
              <div class="h-10 w-40 bg-slate-100 rounded-xl hidden sm:block"></div>
              <div class="h-10 w-24 bg-slate-100 rounded-xl hidden sm:block"></div>
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

      {{-- REAL CONTENT (hidden until skeleton is done) --}}
      <div id="egReal" class="space-y-6 hidden">
        {{-- Filters --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
          <form class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" onsubmit="return false;">
            <div class="flex items-center gap-3 w-full sm:w-auto">
              <div class="relative flex-1 sm:w-80">
                <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
                <input id="filter-q" type="text" value="{{ request('q') }}"
                       placeholder="Search item / uses / related tasks"
                       class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300">
              </div>

              <select id="filter-category"
                      class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                <option value="">All categories</option>
                @foreach ($categories as $c)
                  <option value="{{ $c }}" @selected(request('category') == $c)>{{ $c }}</option>
                @endforeach
              </select>

              <select id="filter-ward"
                      class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                <option value="">All ward scopes</option>
                @foreach ($wards as $w)
                  <option value="{{ $w }}" @selected(request('ward') == $w)>{{ $w }}</option>
                @endforeach
              </select>

              <button id="btn-filter" type="button"
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
                  <th class="px-4 py-3">Item / Uses</th>
                  <th class="px-4 py-3">Category</th>
                  <th class="px-4 py-3">Ward Scope</th>
                  <th class="px-4 py-3 text-right">Actions</th>
                </tr>
              </thead>

              <tbody id="eg-table" class="divide-y divide-slate-200">
                {{-- IMPORTANT: in _rows.blade.php, make each row like:
                    <tr class="js-eg-row opacity-0"> ... </tr>
                 --}}
                @include('admin.equipment_guides._rows', ['items' => $items])
              </tbody>
            </table>
          </div>

          @include('admin.equipment_guides._pager', ['items' => $items])
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
  const qs  = s => document.querySelector(s);
  const qsa = s => Array.from(document.querySelectorAll(s));
  const debounce = (fn, ms = 300) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); } };
  const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  const INDEX_URL = "{{ route('admin.equipment_guide.index') }}";

  const skeleton = qs('#egSkeleton');
  const realWrap = qs('#egReal');

  // Row animation (same pattern as admin procedures page)
  function animateRows(scope = document) {
    const rows = qsa('.js-eg-row', scope);
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
  function revealEquipmentWithAnimation() {
    if (skeleton) skeleton.classList.add('hidden');
    if (realWrap) realWrap.classList.remove('hidden');
    animateRows();
    if (window.lucide?.createIcons) lucide.createIcons();
  }

  async function fetchList(params = {}) {
    const url  = new URL(INDEX_URL, window.location.origin);
    const q    = params.q    ?? (qs('#filter-q')?.value || '');
    const cat  = params.cat  ?? (qs('#filter-category')?.value || '');
    const ward = params.ward ?? (qs('#filter-ward')?.value || '');
    const page = params.page ?? '';
    const per  = params.per  ?? ''; // optional, if you want to pass per-page

    if (q)    url.searchParams.set('q', q);
    if (cat)  url.searchParams.set('category', cat);
    if (ward) url.searchParams.set('ward', ward);
    if (page) url.searchParams.set('page', page);
    if (per)  url.searchParams.set('per_page', per);

    const tbody = qs('#eg-table');
    const pager = qs('#eg-pager');

    if (tbody) {
      tbody.innerHTML =
        `<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">Loading…</td></tr>`;
    }
    if (pager) pager.innerHTML = '';

    try {
      const res = await fetch(url.toString(), {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      });
      if (!res.ok) throw new Error('Network error');

      const data = await res.json();

      if (tbody) {
        tbody.innerHTML = data.rows ||
          `<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No results.</td></tr>`;
      }

      if (pager) {
        pager.outerHTML = data.pager ||
          `<div id="eg-pager" class="flex items-center justify-end px-4 py-3 border-t border-slate-200 bg-slate-50"></div>`;
      }

      if (window.lucide?.createIcons) lucide.createIcons();
      bindPager();
      bindDelete();
      animateRows(); // animate freshly loaded rows
    } catch (e) {
      if (tbody) {
        tbody.innerHTML =
          `<tr><td colspan="4" class="px-4 py-8 text-center text-red-600">Failed to load.</td></tr>`;
      }
    }
  }

  function bindFilters() {
    qs('#filter-q')       ?.addEventListener('input',  debounce(() => fetchList({ page: 1 }), 300));
    qs('#filter-category')?.addEventListener('change', () => fetchList({ page: 1 }));
    qs('#filter-ward')    ?.addEventListener('change', () => fetchList({ page: 1 }));
    qs('#btn-filter')     ?.addEventListener('click',  () => fetchList({ page: 1 }));
  }

  function bindPager() {
    qsa('.js-page').forEach(btn => {
      if (btn.dataset.bound === '1') return;
      btn.dataset.bound = '1';
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const p = e.currentTarget.getAttribute('data-page');
        if (p) fetchList({ page: p });
      }, { once: true });
    });
  }

  // AJAX delete with SweetAlert + list refresh
  function bindDelete() {
    qsa('.js-delete-form').forEach(form => {
      if (form.dataset.bound === '1') return;
      form.dataset.bound = '1';
      form.dataset.deleting = '0';

      form.addEventListener('submit', function (e) {
        e.preventDefault();
        const f = this;
        if (f.dataset.deleting === '1') return;

        const name = f.dataset.title || 'this item';

        const doDelete = async () => {
          f.dataset.deleting = '1';
          try {
            const fd = new FormData(f);
            const res = await fetch(f.action, {
              method: 'POST',
              headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken(),
                'Accept': 'application/json'
              },
              body: fd
            });

            if (res.ok) {
              if (window.Swal) {
                Swal.fire({
                  icon: 'success',
                  title: 'Deleted',
                  timer: 1100,
                  showConfirmButton: false
                });
              }
              await fetchList(); // refresh list after deletion (rows will re-animate)
            } else {
              if (window.Swal) {
                Swal.fire({ icon: 'error', title: 'Delete failed' });
              } else {
                alert('Delete failed.');
              }
            }
          } catch (err) {
            if (window.Swal) {
              Swal.fire({ icon: 'error', title: 'Network error' });
            } else {
              alert('Network error.');
            }
          } finally {
            f.dataset.deleting = '0';
          }
        };

        if (window.Swal) {
          Swal.fire({
            title: 'Delete equipment?',
            html: `Are you sure you want to delete <b>${name}</b>? This cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            focusCancel: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#334155'
          }).then(result => {
            if (result.isConfirmed) doDelete();
          });
        } else {
          if (confirm(`Delete ${name}? This cannot be undone.`)) {
            doDelete();
          }
        }
      });
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    const prefersReduced = window.matchMedia &&
      window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const delay = prefersReduced ? 0 : 220;

    // Skeleton -> real reveal (matches admin procedures page)
    setTimeout(revealEquipmentWithAnimation, delay);

    bindFilters();
    bindPager();
    bindDelete();
    // Initial rows (Blade-rendered) will animate on reveal via animateRows()
  });
</script>

</body>
</html>
