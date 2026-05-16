<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Student Nurse • Nursing References · NurSync</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family:'Poppins', ui-sans-serif, system-ui, sans-serif; }

    /* Smooth card entrance (same as CI / Return Demo) */
    @keyframes slide-in-up {
      from { transform: translateY(10px); opacity: 0; }
      to   { transform: translateY(0);     opacity: 1; }
    }
    .animate-card-in {
      animation: slide-in-up .35s ease-out both;
      will-change: transform, opacity;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Sidebar (Student Nurse) --}}
  @include('partials.sidebar', ['active' => 'nursing_references'])

  {{-- Main content --}}
  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-8 py-12 space-y-6">

      {{-- Page heading --}}
      <header class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
            <i data-lucide="book-open-check" class="h-4 w-4"></i>
          </span>
          <div>
            <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
              Nursing References
            </h1>
            <p class="text-[13px] text-slate-500 mt-1">
              Curated evidence-based sites and tools used by nurses in clinical and academic settings.
            </p>
          </div>
        </div>
      </header>

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

      {{-- Filters --}}
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
        <form class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" onsubmit="return false;">
          <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-80">
              <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
              <input id="nr-filter-q" type="text" value="{{ request('q') }}"
                     placeholder="Search title / source / description"
                     class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300">
            </div>

            <select id="nr-filter-category"
                    class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
              <option value="">All categories</option>
              @foreach ($categories as $c)
                <option value="{{ $c }}" @selected(request('category') == $c)>{{ $c }}</option>
              @endforeach
            </select>

            <select id="nr-filter-source"
                    class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
              <option value="">All sources</option>
              @foreach ($sources as $s)
                <option value="{{ $s }}" @selected(request('source') == $s)>{{ $s }}</option>
              @endforeach
            </select>

            <button id="nr-btn-filter" type="button"
                    class="rounded-xl border border-slate-200 bg-white text-[13px] px-3 py-2.5 hover:bg-slate-50">
              Filter
            </button>
          </div>
        </form>
      </div>

      {{-- Cards --}}
      <div class="bg-transparent">
        {{-- Skeleton grid (shown while loading) --}}
        <div id="nr-skeleton" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" aria-hidden="true">
          @for ($i = 0; $i < 6; $i++)
            <div class="rounded-2xl border border-slate-200 bg-white p-4">
              <div class="animate-pulse">
                <div class="flex items-start justify-between gap-3">
                  <div class="flex items-center gap-3">
                    <span class="h-8 w-8 rounded-xl bg-slate-200"></span>
                    <div class="space-y-2">
                      <div class="h-3 w-40 bg-slate-200 rounded"></div>
                      <div class="h-3 w-24 bg-slate-100 rounded"></div>
                    </div>
                  </div>
                  <span class="h-6 w-20 rounded-full bg-slate-100"></span>
                </div>
                <div class="mt-3 space-y-2">
                  <div class="h-3 w-full bg-slate-100 rounded"></div>
                  <div class="h-3 w-5/6 bg-slate-100 rounded"></div>
                </div>
                <div class="mt-3 flex gap-2">
                  <span class="h-5 w-16 rounded-full bg-slate-100"></span>
                  <span class="h-5 w-20 rounded-full bg-slate-100"></span>
                </div>
              </div>
            </div>
          @endfor
        </div>

        {{-- Real cards --}}
        <div id="nr-cards-wrapper" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3 hidden">
          @include('student.nursing_references._cards', ['items' => $items])
        </div>

        @include('student.nursing_references._pager', ['items' => $items])
      </div>
    </div>
  </section>
</main>

@include('partials.admin-footer') {{-- or student-footer if you have one --}}

<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>

<script>
  // --- helpers (mirrors faculty page JS)
  const nr_qs  = s => document.querySelector(s);
  const nr_qsa = s => Array.from(document.querySelectorAll(s));
  const nr_debounce = (fn, ms = 300) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); } };

  const NR_INDEX_URL = "{{ route('student.nursing_references.index') }}";

  function nrShowSkeleton(show) {
    const skel  = nr_qs('#nr-skeleton');
    const cards = nr_qs('#nr-cards-wrapper');
    if (!skel || !cards) return;
    skel.classList.toggle('hidden', !show);
    cards.classList.toggle('hidden', show);
  }

  function nrAnimateCards() {
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReduced) return;

    const cards = nr_qsa('#nr-cards-wrapper .js-nr-card, #nr-cards-wrapper > *');
    cards.forEach((el, idx) => {
      el.classList.remove('animate-card-in');
      el.style.opacity = '0';
      el.style.animationDelay = `${Math.min(idx, 6) * 40}ms`;
      void el.offsetWidth; // force reflow
      el.classList.add('animate-card-in');
    });
  }

  async function nrFetchList(params = {}) {
    const url   = new URL(NR_INDEX_URL, window.location.origin);
    const q     = params.q    ?? (nr_qs('#nr-filter-q')?.value || '');
    const cat   = params.cat  ?? (nr_qs('#nr-filter-category')?.value || '');
    const src   = params.src  ?? (nr_qs('#nr-filter-source')?.value || '');
    const page  = params.page ?? '';
    const per   = params.per  ?? '';

    if (q)    url.searchParams.set('q', q);
    if (cat)  url.searchParams.set('category', cat);
    if (src)  url.searchParams.set('source', src);
    if (page) url.searchParams.set('page', page);
    if (per)  url.searchParams.set('per_page', per);

    const cards = nr_qs('#nr-cards-wrapper');
    const pager = nr_qs('#nr-pager');

    // Show skeleton first
    nrShowSkeleton(true);
    if (pager) pager.innerHTML = '';

    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const delay = prefersReduced ? 0 : 220;

    try {
      const res = await fetch(url.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
      });
      if (!res.ok) throw new Error('Network error');

      const data = await res.json();
      if (cards) {
        cards.innerHTML = data.rows || `<div class="col-span-full px-4 py-8 text-center text-slate-500">No results.</div>`;
      }
      if (pager && data.pager) {
        pager.outerHTML = data.pager;
      } else if (pager && !data.pager) {
        pager.outerHTML = `<div id="nr-pager" class="flex items-center justify-end px-4 py-3 text-xs text-slate-500"></div>`;
      }

      if (window.lucide?.createIcons) lucide.createIcons();
      nrBindPager();

      // Small delay so skeleton is visible, then show cards + animate
      setTimeout(() => {
        nrShowSkeleton(false);
        nrAnimateCards();
      }, delay);
    } catch (e) {
      if (cards) {
        cards.innerHTML = `<div class="col-span-full px-4 py-8 text-center text-red-600">Failed to load.</div>`;
      }
      nrShowSkeleton(false);
    }
  }

  function nrBindFilters() {
    nr_qs('#nr-filter-q')       ?.addEventListener('input',  nr_debounce(() => nrFetchList({ page: 1 }), 300));
    nr_qs('#nr-filter-category')?.addEventListener('change', () => nrFetchList({ page: 1 }));
    nr_qs('#nr-filter-source')  ?.addEventListener('change', () => nrFetchList({ page: 1 }));
    nr_qs('#nr-btn-filter')     ?.addEventListener('click',  () => nrFetchList({ page: 1 }));
  }

  function nrBindPager() {
    nr_qsa('.nr-page').forEach(btn => {
      if (btn.dataset.bound === '1') return;
      btn.dataset.bound = '1';
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const p = e.currentTarget.getAttribute('data-page');
        if (p) nrFetchList({ page: p });
      });
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    nrBindFilters();
    nrBindPager();

    // On first load: skeleton first, then cards + animation
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const delay = prefersReduced ? 0 : 220;

    setTimeout(() => {
      nrShowSkeleton(false); // hide skeleton, show cards
      nrAnimateCards();      // then animate cards in
    }, delay);
  });
</script>

</body>
</html>
