{{-- resources/views/student/competencies/index.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Competency Requirements · NurSync</title>

  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; }

    /* Smooth card entrance after skeleton hides (same as CI Competencies / Ward Orientation / Procedures) */
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
  {{-- Student sidebar --}}
  @include('partials.sidebar', ['active' => 'competency_requirements'])

  {{-- Main content --}}
  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-8 py-12 space-y-6">

      @php
        /** @var \Illuminate\Support\Collection|\App\Models\CompetencyItem[] $items */
        $items      = $items      ?? collect();
        $categories = $categories ?? collect();
        $filters    = $filters    ?? ['q' => '', 'category_id' => 'all'];

        $currentSearch = $filters['q'] ?? '';
        $currentCatId  = $filters['category_id'] ?? 'all';

        // Small palette for theme tags (same vibe as CI side)
        $tagPalette = [
          'bg-emerald-50 text-emerald-700 border-emerald-200',
          'bg-sky-50 text-sky-700 border-sky-200',
          'bg-amber-50 text-amber-800 border-amber-200',
          'bg-violet-50 text-violet-700 border-violet-200',
          'bg-rose-50 text-rose-700 border-rose-200',
          'bg-indigo-50 text-indigo-700 border-indigo-200',
          'bg-teal-50 text-teal-700 border-teal-200',
        ];
        $tagClass = function(string $t) use ($tagPalette) {
          $i = abs(crc32(mb_strtolower($t))) % count($tagPalette);
          return $tagPalette[$i];
        };
      @endphp

      {{-- Page heading --}}
      <header class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
            <i data-lucide="badge-check" class="h-4 w-4"></i>
          </span>
          <div>
            <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
              Competency Requirements
            </h1>
            <p class="text-[13px] text-slate-500 mt-1">
              See what real-life nursing competencies your instructors expect you to master before graduation.
            </p>
          </div>
        </div>

        {{-- Right side helper (view-only hint) --}}
        <div class="hidden sm:flex">
          <span class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-[12px] font-medium text-slate-600">
            <i data-lucide="eye" class="h-4 w-4 text-slate-500"></i>
            View-only — set by your instructors
          </span>
        </div>
      </header>

      {{-- Filters (CI-style card, but student-focused) --}}
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center w-full flex-1">
            {{-- Search --}}
            <div class="relative flex-1 sm:w-64 lg:w-80">
              <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
              <input
                id="searchBox"
                type="text"
                placeholder="Search by competency, category, or reason…"
                value="{{ $currentSearch }}"
                class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300"
              />
            </div>

            {{-- Category filter --}}
            <select
              id="categorySelect"
              class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300"
            >
              <option value="all" {{ $currentCatId === 'all' ? 'selected' : '' }}>All categories</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ (string)$currentCatId === (string)$cat->id ? 'selected' : '' }}>
                  {{ $cat->title }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Helper chip on mobile --}}
          <div class="flex sm:hidden">
            <span class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-[12px] font-medium text-slate-600">
              <i data-lucide="eye" class="h-4 w-4 text-slate-500"></i>
              View competencies only
            </span>
          </div>
        </div>

        {{-- Tiny hint text --}}
        <p class="text-[11px] text-slate-500">
          Tip: Use this as a checklist of what you should be confident doing in the ward before you graduate.
        </p>
      </div>

      {{-- Empty state --}}
      @if($items->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
          <p class="text-sm text-slate-500">
            No competency requirements have been published for you yet.
            Your instructors may still be setting these up.
          </p>
        </div>
      @else

        {{-- Skeleton grid (same pattern as CI index) --}}
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
                  <span class="h-6 w-6 rounded-full bg-slate-100"></span>
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
                <div class="mt-4 h-8 w-32 rounded-xl bg-slate-100"></div>
              </div>
            </div>
          @endfor
        </div>

        {{-- Cards grid (student view-only, large card style) --}}
        <div id="cardsGrid" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3 hidden">
          @foreach($items as $c)
            @php
              $cat = $c->category?->title ?? 'Uncategorized';

              // Theme tags
              $themes = [];
              if (!empty($c->reason))                 $themes[] = 'Why this matters';
              if (!empty($c->description))            $themes[] = 'What this skill covers';
              if (!empty($c->required_cases ?? null)) $themes[] = 'Required cases';
              $themes[] = 'Real-world nursing';

              $keywords = \Illuminate\Support\Str::of(
                  ($c->title ?? '') . ' ' .
                  ($c->description ?? '') . ' ' .
                  ($c->reason ?? '') . ' ' .
                  ($cat ?? '')
              )->lower();
            @endphp

            <article
              class="js-card flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm opacity-0 transition-shadow"
              data-category-id="{{ $c->category_id ?? '' }}"
              data-keywords="{{ $keywords }}"
            >
              {{-- Header (large with fixed block so chips align) --}}
              <header class="flex items-start justify-between gap-4">
                <div class="flex-1">
                  {{-- Fixed height text + primary chips --}}
                  <div class="min-h-[90px] flex flex-col gap-1.5">
                    <div class="flex items-center gap-3">
                      <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50">
                        <i data-lucide="clipboard-check" class="h-5 w-5 text-slate-700"></i>
                      </span>
                      <h2 class="text-[16px] font-semibold text-slate-900 leading-snug line-clamp-2">
                        {{ $c->title }}
                      </h2>
                    </div>

                    {{-- Reason / description --}}
                    @if(!empty($c->reason))
                      <p class="mt-1 text-[13px] text-slate-600 line-clamp-3">
                        {{ $c->reason }}
                      </p>
                    @elseif(!empty($c->description))
                      <p class="mt-1 text-[13px] text-slate-600 line-clamp-3">
                        {{ $c->description }}
                      </p>
                    @else
                      <p class="mt-1 text-[13px] text-slate-600 line-clamp-3">
                        Competency required for safe, independent nursing practice in real clinical settings.
                      </p>
                    @endif

                    {{-- Primary chips row (category + student focus) --}}
                    <div class="mt-1.5 flex flex-wrap items-center gap-2.5 text-[12px]">
                      {{-- Category --}}
                      <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 bg-slate-50 text-slate-700 border-slate-200">
                        <i data-lucide="layers" class="h-3 w-3 mr-1"></i>
                        {{ $cat }}
                      </span>

                      {{-- Student focus --}}
                      <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-emerald-700 font-medium">
                        <i data-lucide="graduation-cap" class="h-3 w-3 mr-1"></i>
                        For student nurses
                      </span>
                    </div>
                  </div>
                </div>

                {{-- Icon bubble --}}
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                  <i data-lucide="user-nurse" class="h-5 w-5"></i>
                </span>
              </header>

              {{-- Meta row --}}
              <div class="mt-4 flex items-center justify-between text-[12px] text-slate-500">
                <span class="inline-flex items-center gap-1.5">
                  <i data-lucide="stethoscope" class="h-3 w-3"></i>
                  Clinical competency
                </span>
                <span class="whitespace-nowrap">
                  Updated {{ $c->updated_at?->diffForHumans() ?? '—' }}
                </span>
              </div>

              {{-- Themes row (chips) --}}
              @if(!empty($themes))
                <div class="mt-3 flex flex-wrap items-center gap-2 text-[12px]">
                  @foreach($themes as $t)
                    <span class="rounded-full border px-2.5 py-0.5 {{ $tagClass($t) }}">
                      {{ $t }}
                    </span>
                  @endforeach
                </div>
              @endif

              {{-- Bottom action bar (no hover on card, just button) --}}
              <div class="mt-auto pt-5 border-t border-slate-100 flex items-center justify-between gap-2.5">
                <a href="{{ route('student.competencies.show', $c->id) }}"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 py-2 text-[13px] font-medium text-slate-800 hover:bg-slate-50">
                  <i data-lucide="eye" class="h-4 w-4"></i>
                  View Competency
                </a>

                <span class="inline-flex items-center gap-1 text-[11px] text-slate-500">
                  <i data-lucide="info" class="h-3 w-3"></i>
                  See breakdown & real examples
                </span>
              </div>
            </article>
          @endforeach
        </div>

        {{-- Pager (client-side, same pattern as CI) --}}
        <div id="pagerShell" class="mt-4 flex items-center justify-between text-[12px] text-slate-500">
          <div id="pagerSummary">Showing 0–0 of 0 competencies</div>
          <div class="flex items-center gap-1">
            <button
              id="btnPrev"
              class="rounded-lg border px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:hover:bg-transparent"
              disabled
            >
              ‹ Prev
            </button>
            <div id="pageButtons" class="flex items-center gap-1"></div>
            <button
              id="btnNext"
              class="rounded-lg border px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:hover:bg-transparent"
              disabled
            >
              Next ›
            </button>
          </div>
        </div>

      @endif {{-- /empty check --}}
    </div>
  </section>
</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
  // Icons
  lucide.createIcons();

  // Debounce helper
  const debounce = (fn, ms = 350) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

  // Elements
  const q = document.getElementById('searchBox');
  const categorySelect = document.getElementById('categorySelect');
  const cardsGrid = document.getElementById('cardsGrid');
  const skeletonGrid = document.getElementById('skeletonGrid');
  const cards = [...document.querySelectorAll('.js-card')];

  // State
  let categoryFilter = categorySelect ? categorySelect.value : 'all';
  const pageSize = 12;
  let currentPage = 1;

  // Pager elements
  const pagerShell = document.getElementById('pagerShell');
  const pagerSummary = document.getElementById('pagerSummary');
  const pageButtons = document.getElementById('pageButtons');
  const btnPrev = document.getElementById('btnPrev');
  const btnNext = document.getElementById('btnNext');

  function showSkeleton(show) {
    if (!skeletonGrid || !cardsGrid) return;
    skeletonGrid.classList.toggle('hidden', !show);
    cardsGrid.classList.toggle('hidden', show);
  }

  function getFilteredCards() {
    const needle = (q?.value || '').toLowerCase().trim();

    return cards.filter(card => {
      const cardCatId  = card.dataset.categoryId || '';
      const okCat      = (categoryFilter === 'all') || (String(cardCatId) === String(categoryFilter));
      const okSearch   = !needle || (card.dataset.keywords || '').includes(needle);
      return okCat && okSearch;
    });
  }

  function animateVisibleSlice(visibleCards) {
    visibleCards.forEach((el, idx) => {
      el.style.animationDelay = `${Math.min(idx, 6) * 40}ms`;
      el.classList.remove('opacity-0');
      el.classList.add('animate-card-in');
    });
  }

  function renderPage() {
    const filtered = getFilteredCards();
    const total = filtered.length;
    const totalPages = Math.max(1, Math.ceil(total / pageSize));
    if (currentPage > totalPages) currentPage = totalPages;

    cards.forEach(c => { c.style.display = 'none'; });

    const startIdx = (currentPage - 1) * pageSize;
    const endIdx = Math.min(startIdx + pageSize, total);
    const slice = [];

    for (let i = startIdx; i < endIdx; i++) {
      filtered[i].style.display = '';
      filtered[i].classList.remove('animate-card-in');
      filtered[i].classList.add('opacity-0');
      slice.push(filtered[i]);
    }

    const humanStart = total === 0 ? 0 : startIdx + 1;
    const humanEnd = endIdx;
    pagerSummary.textContent = `Showing ${humanStart}–${humanEnd} of ${total} competencies`;

    btnPrev.disabled = (currentPage <= 1);
    btnNext.disabled = (currentPage >= totalPages);
    buildPageButtons(totalPages);
    pagerShell.style.display = totalPages <= 1 ? 'none' : '';
    requestAnimationFrame(() => animateVisibleSlice(slice));
  }

  function buildPageButtons(totalPages) {
    pageButtons.innerHTML = '';
    const windowSize = 5;
    let start = Math.max(1, currentPage - Math.floor(windowSize / 2));
    let end = Math.min(totalPages, start + windowSize - 1);
    start = Math.max(1, Math.min(start, Math.max(1, totalPages - windowSize + 1)));

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
        btn.addEventListener('click', () => { currentPage = page; renderWithSkeleton(); scrollPagerIntoView(); });
      }
      return btn;
    };

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
    const vh = window.innerHeight || document.documentElement.clientHeight;
    const fullyVisible = rect.top >= 0 && rect.bottom <= vh;
    if (!fullyVisible) pagerShell.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  function renderWithSkeleton() {
    showSkeleton(true);
    const delay = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 220;
    setTimeout(() => { renderPage(); showSkeleton(false); }, delay);
  }

  q?.addEventListener('input', debounce(() => {
    currentPage = 1;
    renderWithSkeleton();
  }, 350));

  categorySelect?.addEventListener('change', () => {
    categoryFilter = categorySelect.value || 'all';
    currentPage = 1;
    renderWithSkeleton();
  });

  document.getElementById('btnPrev')?.addEventListener('click', () => {
    if (currentPage > 1) { currentPage--; renderWithSkeleton(); scrollPagerIntoView(); }
  });

  document.getElementById('btnNext')?.addEventListener('click', () => {
    currentPage++; renderWithSkeleton(); scrollPagerIntoView();
  });

  document.addEventListener('DOMContentLoaded', () => {
    renderWithSkeleton();
  });
</script>
</body>
</html>
