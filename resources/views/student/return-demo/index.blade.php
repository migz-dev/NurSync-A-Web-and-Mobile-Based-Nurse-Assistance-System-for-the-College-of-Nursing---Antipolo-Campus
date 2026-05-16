{{-- resources/views/student/return-demo/index.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Return Demo Procedures · NurSync (Student)</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }

    /* Smooth card entrance after skeleton hides (same as CI Procedures Library) */
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
  @include('partials.sidebar', ['active' => 'return_demo'])

  {{-- Main content --}}
  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-8 py-12 space-y-6">

      {{-- Page heading (mirrors CI Procedures Library style) --}}
      <header class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
            <i data-lucide="graduation-cap" class="h-4 w-4"></i>
          </span>
          <div>
            <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
              Return Demo Procedures
            </h1>
            <p class="text-[13px] text-slate-500 mt-1">
              Review your assigned return demonstration procedures with step-by-step guides, safety notes, and required materials.
            </p>
          </div>
        </div>
      </header>

      @php
        /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $skills */
        $skills = $skills ?? collect();
        $q      = $q      ?? request('q');
        $ward   = $ward   ?? request('ward', 'all');

        // If controller didn’t pass wards, derive from data
        $wards = isset($wards) && !empty($wards)
          ? $wards
          : collect($skills)->pluck('clinical_wards')->filter()->unique()->values();
      @endphp

      {{-- Search + filters (styled like CI Procedures filter card) --}}
      <form id="filtersForm" method="GET"
            class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center w-full flex-1">
            {{-- Search --}}
            <div class="relative flex-1 sm:w-64 lg:w-80">
              <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
              <input
                type="text"
                name="q"
                value="{{ old('q', (string) $q) }}"
                placeholder="Search return demos by name, tags, or clinical area…"
                class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm bg-slate-50 placeholder:text-slate-400
                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-300" />
            </div>

            {{-- Ward / Clinical Area --}}
            <select name="ward"
                    class="rounded-xl border-slate-200 text-sm py-2.5 px-3 bg-white focus:ring-2 focus:ring-slate-300">
              <option value="all" @selected(($ward ?? 'all') === 'all')>All areas</option>
              @foreach($wards as $w)
                <option value="{{ $w }}" @selected(($ward ?? '') === $w)>{{ $w }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </form>

      {{-- Skeleton grid (shows first, then cards fade/slide in) --}}
      <div id="skeletonGrid" aria-hidden="true" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @for ($i = 0; $i < 9; $i++)
          <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="animate-pulse">
              <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                  <span class="h-8 w-8 rounded-xl bg-slate-200"></span>
                  <div class="space-y-2">
                    <div class="h-3 w-40 bg-slate-200 rounded"></div>
                    <div class="h-3 w-24 bg-slate-100 rounded"></div>
                  </div>
                </div>
                <span class="h-6 w-24 rounded-full bg-slate-100"></span>
              </div>
              <div class="mt-3 space-y-2">
                <div class="h-3 w-full bg-slate-100 rounded"></div>
                <div class="h-3 w-5/6 bg-slate-100 rounded"></div>
              </div>
              <div class="mt-3 flex gap-2">
                <span class="h-5 w-16 rounded-full bg-slate-100"></span>
                <span class="h-5 w-20 rounded-full bg-slate-100"></span>
                <span class="h-5 w-12 rounded-full bg-slate-100"></span>
              </div>
              <div class="mt-4 flex gap-2">
                <span class="h-8 w-24 rounded-xl bg-slate-100"></span>
                <span class="h-8 w-24 rounded-xl bg-slate-100"></span>
                <span class="h-8 w-16 rounded-xl bg-slate-100"></span>
              </div>
            </div>
          </div>
        @endfor
      </div>

      {{-- Results (AJAX target) --}}
      <div id="cardsGrid" class="hidden">
        @include('student.return-demo._cards', ['skills' => $skills])
      </div>

      {{-- Pager (AJAX target) --}}
      <div id="pagerShell" class="mt-4">
        @include('student.return-demo._pager', ['skills' => $skills])
      </div>

    </div>
  </section>
</main>
@include('partials.admin-footer') 
<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();

  (function () {
    const form       = document.getElementById('filtersForm');
    const qInput     = form.querySelector('input[name="q"]');
    const wardSelect = form.querySelector('select[name="ward"]');
    const cardsGrid  = document.getElementById('cardsGrid');
    const pagerShell = document.getElementById('pagerShell');
    const skeletonGrid = document.getElementById('skeletonGrid');

    // Helpers
    const debounce = (fn, ms = 350) => {
      let t;
      return (...a) => {
        clearTimeout(t);
        t = setTimeout(() => fn(...a), ms);
      };
    };

    function buildUrl(params = {}) {
      const url = new URL(window.location.href);

      if (params.page) url.searchParams.set('page', params.page);
      else url.searchParams.delete('page');

      if (params.q !== undefined)    url.searchParams.set('q', params.q);
      if (params.ward !== undefined) url.searchParams.set('ward', params.ward);

      return url.toString();
    }

    function showSkeleton(show) {
      if (!skeletonGrid || !cardsGrid) return;
      skeletonGrid.classList.toggle('hidden', !show);
      cardsGrid.classList.toggle('hidden', show);
    }

    function getCards() {
      // Animate direct children inside the grid (matches CI behavior)
      return Array.from(cardsGrid.children || []);
    }

    function animateCards() {
      const cards = getCards();
      if (!cards.length) return;

      cards.forEach((el, idx) => {
        el.classList.remove('animate-card-in');
        el.classList.add('opacity-0');
        el.style.animationDelay = `${Math.min(idx, 6) * 40}ms`;
      });

      requestAnimationFrame(() => {
        cards.forEach(el => {
          el.classList.add('animate-card-in');
          el.classList.remove('opacity-0');
        });
      });
    }

    function scrollToResultsTop() {
      const rect = cardsGrid.getBoundingClientRect();
      const top = rect.top + window.scrollY - 16;
      window.scrollTo({ top, behavior: 'smooth' });
    }

    async function load(url) {
      try {
        showSkeleton(true);

        const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
        if (!res.ok) {
          showSkeleton(false);
          return;
        }

        const data = await res.json();
        cardsGrid.innerHTML = data.list;
        pagerShell.innerHTML = data.pager;

        // re-init lucide icons for new content
        if (window.lucide) window.lucide.createIcons();

        const delay = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 220;
        setTimeout(() => {
          showSkeleton(false);
          animateCards();
          scrollToResultsTop();
        }, delay);
      } catch (e) {
        console.error(e);
        showSkeleton(false);
      }
    }

    const applyFilters = debounce(() => {
      const url = buildUrl({
        q: qInput.value || '',
        ward: wardSelect.value || 'all',
        page: 1
      });
      history.pushState({}, '', url);
      load(url);
    }, 350);

    // Typing search (debounced)
    qInput.addEventListener('input', applyFilters);

    // Ward change (immediate)
    wardSelect.addEventListener('change', () => {
      const url = buildUrl({
        q: qInput.value || '',
        ward: wardSelect.value || 'all',
        page: 1
      });
      history.pushState({}, '', url);
      load(url);
    });

    // Intercept pagination clicks (event delegation)
    pagerShell.addEventListener('click', (e) => {
      const a = e.target.closest('a');
      if (!a || !a.href) return;
      e.preventDefault();
      history.pushState({}, '', a.href);
      load(a.href);
    });

    // Back/forward
    window.addEventListener('popstate', () => load(window.location.href));

    // Initial entrance: show skeleton briefly then animate cards
    document.addEventListener('DOMContentLoaded', () => {
      const delay = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 220;
      setTimeout(() => {
        showSkeleton(false);
        animateCards();
      }, delay);
    });
  })();
</script>
</body>
</html>
