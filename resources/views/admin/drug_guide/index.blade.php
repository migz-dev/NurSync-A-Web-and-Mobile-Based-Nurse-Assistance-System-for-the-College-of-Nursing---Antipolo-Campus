{{-- resources/views/admin/drug_guide/index.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Admin • Drug Guide · NurSync</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    body { font-family:'Poppins',ui-sans-serif,system-ui,sans-serif; }

    /* Row entrance animation (aligned with other admin pages) */
    @keyframes slide-in-up {
      from { transform: translateY(6px); opacity: 0; }
      to   { transform: translateY(0);  opacity: 1; }
    }

    .animate-row-in {
      animation: slide-in-up .28s ease-out both;
      will-change: transform, opacity;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  @include('partials.admin-sidebar', ['active' => 'drug_guide'])

  <section class="flex-1 min-w-0">
    {{-- Header --}}
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-900 text-white shadow-sm">
            <i data-lucide="pill" class="h-4 w-4"></i>
          </div>
          <div>
            <h1 class="text-[15px] sm:text-[16px] font-semibold leading-tight">Drug Guide</h1>
            <p class="text-[12px] text-slate-500 -mt-0.5">Browse and manage approved drug products.</p>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <a href="{{ route('admin.drug_guide.create') }}"
             class="inline-flex items-center gap-2 rounded-xl bg-green-600 text-white px-3 py-2 text-[13px] font-medium shadow hover:bg-green-700 active:scale-[.99]">
            <i data-lucide="plus" class="h-4 w-4"></i>
            <span>Create</span>
          </a>
        </div>
      </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">

      @php
        // Safety defaults so Blade never errors
        $forms          = $forms          ?? collect();
        $cats           = $cats           ?? collect();
        $mfgs           = $mfgs           ?? collect();
        $classGroups    = $classGroups    ?? [];
        $packagingTypes = $packagingTypes ?? [];
      @endphp

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

      {{-- SKELETON: filters + summary + table --}}
      <div id="dgSkeleton" aria-hidden="true" class="space-y-4">
        {{-- Skeleton filters --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
          <div class="animate-pulse space-y-3">
            <div class="h-9 w-full bg-slate-100 rounded-xl"></div>
            <div class="flex flex-wrap gap-3">
              <div class="h-9 w-40 bg-slate-100 rounded-xl"></div>
              <div class="h-9 w-40 bg-slate-100 rounded-xl"></div>
              <div class="h-9 w-40 bg-slate-100 rounded-xl"></div>
              <div class="h-9 w-24 bg-slate-100 rounded-xl"></div>
            </div>
            <div class="flex flex-wrap gap-3 pt-1">
              <div class="h-8 w-32 bg-slate-100 rounded-xl"></div>
              <div class="h-8 w-24 bg-slate-100 rounded-xl"></div>
              <div class="h-8 w-32 bg-slate-100 rounded-xl"></div>
              <div class="h-8 w-24 bg-slate-100 rounded-xl ml-auto"></div>
            </div>
          </div>
        </div>

        {{-- Skeleton summary --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
          <div class="animate-pulse flex items-center justify-between gap-4">
            <div class="space-y-2">
              <div class="h-3 w-40 bg-slate-100 rounded"></div>
              <div class="h-3 w-56 bg-slate-100 rounded"></div>
            </div>
            <div class="h-8 w-32 bg-slate-100 rounded-xl"></div>
          </div>
        </div>

        {{-- Skeleton table --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
          <div class="animate-pulse space-y-3">
            <div class="h-4 w-48 bg-slate-100 rounded"></div>
            <div class="space-y-2 mt-3">
              @for ($i = 0; $i < 5; $i++)
                <div class="flex items-center gap-3">
                  <div class="flex-1 space-y-1">
                    <div class="h-3 w-72 bg-slate-100 rounded"></div>
                    <div class="h-3 w-52 bg-slate-100 rounded"></div>
                  </div>
                  <div class="h-7 w-24 bg-slate-100 rounded-lg ml-auto"></div>
                </div>
              @endfor
            </div>
          </div>
        </div>
      </div>

      {{-- REAL CONTENT (hidden until skeleton is done) --}}
      <div id="dgReal" class="space-y-6 hidden">

        {{-- Filters --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
          <form class="grid gap-3 sm:grid-cols-12 items-center" onsubmit="return false;">
            {{-- Search --}}
            <div class="sm:col-span-5">
              <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
                <input id="filter-q" type="text" value="{{ $q ?? request('q') }}"
                       placeholder="Search brand / substance / reg. no. / manufacturer"
                       class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300">
              </div>
            </div>

            {{-- Dosage form --}}
            <div class="sm:col-span-2">
              <select id="filter-form"
                      class="w-full rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                <option value="">All forms</option>
                @foreach ($forms as $f)
                  <option value="{{ $f->id }}" @selected(($form ?? request('form')) == $f->id)>{{ $f->name }}</option>
                @endforeach
              </select>
            </div>

            {{-- Category --}}
            <div class="sm:col-span-2">
              <select id="filter-cat"
                      class="w-full rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                <option value="">All categories</option>
                @foreach ($cats as $c)
                  <option value="{{ $c->id }}" @selected(($cat ?? request('cat')) == $c->id)>{{ $c->name }}</option>
                @endforeach
              </select>
            </div>

            {{-- Manufacturer --}}
            <div class="sm:col-span-2">
              <select id="filter-mfg"
                      class="w-full rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                <option value="">All manufacturers</option>
                @foreach ($mfgs as $m)
                  <option value="{{ $m->id }}" @selected(($mfg ?? request('mfg')) == $m->id)>{{ $m->name }}</option>
                @endforeach
              </select>
            </div>

            {{-- Buttons --}}
            <div class="sm:col-span-1 flex gap-2 justify-end">
              <button id="btn-filter" type="button"
                      class="rounded-xl border border-slate-200 bg-white text-[13px] px-3 py-2.5 hover:bg-slate-50">
                Apply
              </button>
            </div>

            {{-- Extra filters row (drug class + packaging) --}}
            <div class="sm:col-span-12 flex flex-wrap items-center gap-3 pt-1">
              {{-- Drug class --}}
              <div>
                <select id="filter-class"
                        class="rounded-xl border-slate-200 text-sm py-2 px-3 focus:ring-2 focus:ring-slate-300">
                  @php $dcVal = $drugClass ?? request('drug_class'); @endphp
                  <option value="">All drug classes</option>
                  @foreach ($classGroups as $g)
                    <option value="{{ $g }}" @selected($dcVal === $g)>{{ $g }}</option>
                  @endforeach
                </select>
              </div>

              {{-- Packaging type --}}
              <div>
                <select id="filter-packaging"
                        class="rounded-xl border-slate-200 text-sm py-2 px-3 focus:ring-2 focus:ring-slate-300">
                  @php $pkgVal = $packaging ?? request('packaging_type'); @endphp
                  <option value="">All packaging types</option>
                  @foreach ($packagingTypes as $p)
                    <option value="{{ $p }}" @selected($pkgVal === $p)>{{ $p }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            {{-- Row 2: sort + per page --}}
            <div class="sm:col-span-12 flex flex-wrap items-center gap-3 pt-1">
              <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500">Sort:</span>
                <select id="filter-sort"
                        class="rounded-xl border-slate-200 text-sm py-2 px-3 focus:ring-2 focus:ring-slate-300">
                  @php $sortVal = $sort ?? request('sort','brand_name'); @endphp
                  <option value="brand_name" @selected($sortVal==='brand_name')>Brand name</option>
                  <option value="registration_number" @selected($sortVal==='registration_number')>Registration #</option>
                  <option value="issued_at" @selected($sortVal==='issued_at')>Issued date</option>
                  <option value="expires_at" @selected($sortVal==='expires_at')>Expiry date</option>
                  <option value="created_at" @selected($sortVal==='created_at')>Date added</option>
                </select>

                <select id="filter-dir"
                        class="rounded-xl border-slate-200 text-sm py-2 px-3 focus:ring-2 focus:ring-slate-300">
                  @php $dirVal = $dir ?? request('dir','asc'); @endphp
                  <option value="asc" @selected($dirVal==='asc')>Asc</option>
                  <option value="desc" @selected($dirVal==='desc')>Desc</option>
                </select>
              </div>

              <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500">Per page:</span>
                @php $perVal = (int)($per ?? request('per', 10)); @endphp
                <select id="filter-per"
                        class="rounded-xl border-slate-200 text-sm py-2 px-3 focus:ring-2 focus:ring-slate-300">
                  @foreach ([10,20,30,50,100] as $pp)
                    <option value="{{ $pp }}" @selected($perVal === $pp)>{{ $pp }}</option>
                  @endforeach
                </select>
              </div>

              <button id="btn-reset" type="button"
                      class="ml-auto inline-flex items-center gap-2 rounded-xl bg-slate-700 text-white px-3 py-2 text-[13px] shadow hover:bg-slate-800">
                <i data-lucide="rotate-ccw" class="h-4 w-4"></i><span>Reset</span>
              </button>
            </div>
          </form>
        </div>

        {{-- Summary --}}
        <div id="dg-summary">
          @include('admin.drug_guide._summary', ['products' => $products])
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-slate-50 border-b border-slate-200">
                <tr class="text-left text-slate-600">
                  <th class="px-4 py-3">Brand / Substance</th>
                  <th class="px-4 py-3">Category</th>
                  <th class="px-4 py-3">Manufacturer</th>
                  <th class="px-4 py-3">Reg. #</th>
                  <th class="px-4 py-3">Issued</th>
                  <th class="px-4 py-3">Expires</th>
                  <th class="px-4 py-3 text-right">Actions</th>
                </tr>
              </thead>
              <tbody id="dg-table" class="divide-y divide-slate-200">
                {{-- rows get class="js-dg-row opacity-0" in _rows partial --}}
                @include('admin.drug_guide._rows', ['products' => $products])
              </tbody>
            </table>
          </div>

          @include('admin.drug_guide._pager', ['products' => $products])
        </div>

      </div> {{-- #dgReal --}}
    </div>
  </section>
</main>

@include('partials.admin-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script> if (window.lucide?.createIcons) lucide.createIcons(); </script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  // ---------- helpers ----------
  const qs  = s => document.querySelector(s);
  const qsa = s => Array.from(document.querySelectorAll(s));
  const debounce = (fn, ms = 300) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); } };

  const INDEX_URL = "{{ route('admin.drug_guide.index') }}";

  const dgSkeleton = qs('#dgSkeleton');
  const dgReal     = qs('#dgReal');

  // Row animation
  function animateRows(scope = document) {
    const rows = qsa('.js-dg-row', scope);
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

  function revealDrugGuideWithAnimation() {
    if (dgSkeleton) dgSkeleton.classList.add('hidden');
    if (dgReal) dgReal.classList.remove('hidden');
    animateRows();
    if (window.lucide?.createIcons) lucide.createIcons();
  }

  function readFilters() {
    return {
      q:           qs('#filter-q')?.value || '',
      form:        qs('#filter-form')?.value || '',
      cat:         qs('#filter-cat')?.value || '',
      mfg:         qs('#filter-mfg')?.value || '',
      drug_class:  qs('#filter-class')?.value || '',
      packaging_type: qs('#filter-packaging')?.value || '',
      sort:        qs('#filter-sort')?.value || 'brand_name',
      dir:         qs('#filter-dir')?.value || 'asc',
      per:         qs('#filter-per')?.value || ''
    };
  }

  async function fetchList(params = {}) {
    const f = { ...readFilters(), ...params };
    const url = new URL(INDEX_URL, window.location.origin);
    if (f.q)              url.searchParams.set('q', f.q);
    if (f.form)           url.searchParams.set('form', f.form);
    if (f.cat)            url.searchParams.set('cat', f.cat);
    if (f.mfg)            url.searchParams.set('mfg', f.mfg);
    if (f.drug_class)     url.searchParams.set('drug_class', f.drug_class);
    if (f.packaging_type) url.searchParams.set('packaging_type', f.packaging_type);
    if (f.sort)           url.searchParams.set('sort', f.sort);
    if (f.dir)            url.searchParams.set('dir', f.dir);
    if (f.per)            url.searchParams.set('per', f.per);
    if (f.page)           url.searchParams.set('page', f.page);

    const tbody   = qs('#dg-table');
    const pager   = qs('#dg-pager');
    const summary = qs('#dg-summary');

    if (tbody)   tbody.innerHTML   = `<tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">Loading…</td></tr>`;
    if (pager)   pager.innerHTML   = '';
    if (summary) summary.innerHTML = '';

    try {
      const res = await fetch(url.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
      });
      if (!res.ok) throw new Error('Network error');

      const data = await res.json();
      if (tbody)   tbody.innerHTML   = data.rows    || `<tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">No results.</td></tr>`;
      if (pager)   pager.outerHTML   = data.pager   || `<div id="dg-pager" class="flex items-center justify-end px-4 py-3 border-t border-slate-200 bg-slate-50"></div>`;
      if (summary) summary.innerHTML = data.summary || '';

      if (window.lucide?.createIcons) lucide.createIcons();
      bindPager();
      bindDelete();
      animateRows();
    } catch (e) {
      if (tbody) tbody.innerHTML = `<tr><td colspan="7" class="px-4 py-8 text-center text-red-600">Failed to load.</td></tr>`;
    }
  }

  function bindFilters() {
    qs('#filter-q')       ?.addEventListener('input',  debounce(() => fetchList({ page: 1 }), 300));
    qs('#filter-form')    ?.addEventListener('change', () => fetchList({ page: 1 }));
    qs('#filter-cat')     ?.addEventListener('change', () => fetchList({ page: 1 }));
    qs('#filter-mfg')     ?.addEventListener('change', () => fetchList({ page: 1 }));
    qs('#filter-class')   ?.addEventListener('change', () => fetchList({ page: 1 }));
    qs('#filter-packaging')?.addEventListener('change', () => fetchList({ page: 1 }));
    qs('#filter-sort')    ?.addEventListener('change', () => fetchList({ page: 1 }));
    qs('#filter-dir')     ?.addEventListener('change', () => fetchList({ page: 1 }));
    qs('#filter-per')     ?.addEventListener('change', () => fetchList({ page: 1 }));
    qs('#btn-filter')     ?.addEventListener('click',  () => fetchList({ page: 1 }));
    qs('#btn-reset')      ?.addEventListener('click',  () => {
      [
        '#filter-q',
        '#filter-form',
        '#filter-cat',
        '#filter-mfg',
        '#filter-class',
        '#filter-packaging',
        '#filter-sort',
        '#filter-dir',
        '#filter-per',
      ].forEach(id => {
        const el = qs(id);
        if (!el) return;
        if (id === '#filter-sort') el.value = 'brand_name';
        else if (id === '#filter-dir') el.value = 'asc';
        else if (id === '#filter-per') el.value = '10';
        else el.value = '';
      });
      fetchList({ page: 1 });
    });
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

  function bindDelete() {
    qsa('.js-delete-form').forEach(form => {
      if (form.dataset.bound === '1') return;
      form.dataset.bound = '1';
      form.dataset.deleting = '0';

      form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (this.dataset.deleting === '1') return;

        const name = this.dataset.title || 'this product';
        const proceed = () => { this.dataset.deleting = '1'; this.submit(); };

        if (window.Swal) {
          Swal.fire({
            title: 'Delete drug product?',
            html: `Are you sure you want to delete <b>${name}</b>? This cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            focusCancel: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#334155'
          }).then(r => { if (r.isConfirmed) proceed(); else this.dataset.deleting = '0'; });
        } else {
          if (confirm(`Delete ${name}? This cannot be undone.`)) proceed();
          else this.dataset.deleting = '0';
        }
      });
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    const prefersReduced = window.matchMedia &&
      window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const delay = prefersReduced ? 0 : 220;

    setTimeout(revealDrugGuideWithAnimation, delay);

    bindFilters();
    bindPager();
    bindDelete();
  });
</script>
</body>
</html>
