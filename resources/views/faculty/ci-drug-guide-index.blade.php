{{-- resources/views/faculty/drug-guide.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Drug Guide · NurSync (CI)</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; }

    /* Smooth card entrance after skeleton hides (same as Procedures Library) */
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
  {{-- Sidebar --}}
  @include('partials.faculty-sidebar', ['active' => 'drug_guide'])

  {{-- Main content --}}
  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-8 py-12 space-y-6">

      {{-- Page heading (mirrors CI Procedures) --}}
      <header class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
            <i data-lucide="pill" class="h-4 w-4"></i>
          </span>
          <div>
            <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
              Drug Guide (CI)
            </h1>
            <p class="text-[13px] text-slate-500 mt-1">
              Point-of-care monographs with nursing responsibilities, dosing, interactions, and safety notes.
            </p>
          </div>
        </div>

        {{-- New Drug (desktop) --}}
        <div class="hidden sm:flex">
          @if($canManage ?? false)
            <a href="{{ route('faculty.drug_guide.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-3.5 py-2.5 text-[13px] font-medium text-white shadow-sm hover:bg-emerald-700">
              <i data-lucide="plus" class="h-4 w-4"></i>
              <span>New Drug</span>
            </a>
          @endif
        </div>
      </header>

      {{-- Flash messages (same style as CI Procedures) --}}
      @if (session('ok'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
          {{ session('ok') }}
        </div>
      @endif
      @if (session('err'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
          {{ session('err') }}
        </div>
      @endif

      @php
        /**
         * $drugs is expected to be a plain Collection/array here
         * (controller: Drug::all() or similar) so we can do client-side paging.
         */
        $drugs = $drugs ?? collect();
        $list  = collect($drugs);

        $classes = $filters['classes'] ?? [];
        $routes  = $filters['routes']  ?? [];
        $ages    = $filters['ages']    ?? [];

        // Colorful tag pills (for safety/warning chips etc.)
        $tagPalette = [
          'bg-emerald-50 text-emerald-700 border-emerald-200',
          'bg-sky-50 text-sky-700 border-sky-200',
          'bg-amber-50 text-amber-800 border-amber-200',
          'bg-violet-50 text-violet-700 border-violet-200',
          'bg-rose-50 text-rose-700 border-rose-200',
          'bg-indigo-50 text-indigo-700 border-indigo-200',
          'bg-teal-50 text-teal-700 border-teal-200',
          'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200',
          'bg-lime-50 text-lime-700 border-lime-200',
        ];
        $tagClass = function(string $t) use ($tagPalette) {
          $i = abs(crc32(mb_strtolower($t))) % count($tagPalette);
          return $tagPalette[$i];
        };
      @endphp

      {{-- Filters (same shell as CI Procedures, adapted to drug fields) --}}
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center w-full flex-1">
            {{-- Search --}}
            <div class="relative flex-1 sm:w-64 lg:w-80">
              <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
              <input id="searchBox" type="text"
                     placeholder="Search drugs by generic, brand, class, or warning…"
                     class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" />
            </div>

            {{-- Class --}}
            <select id="classSelect"
                    class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
              <option value="all">All classes</option>
              @foreach($classes as $c)
                <option value="{{ $c }}">{{ $c }}</option>
              @endforeach
            </select>

            {{-- Route --}}
            <select id="routeSelect"
                    class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
              <option value="all">All routes</option>
              @foreach($routes as $r)
                <option value="{{ $r }}">{{ $r }}</option>
              @endforeach
            </select>

            {{-- Age group --}}
            <select id="ageSelect"
                    class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
              <option value="all">All ages</option>
              @foreach($ages as $a)
                <option value="{{ $a }}">{{ ucfirst($a) }}</option>
              @endforeach
            </select>
          </div>

          {{-- Mobile New Drug button --}}
          <div class="flex sm:hidden">
            @if($canManage ?? false)
              <a href="{{ route('faculty.drug_guide.create') }}"
                 class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-3.5 py-2.5 text-[13px] font-medium text-white shadow-sm hover:bg-emerald-700 w-full justify-center">
                <i data-lucide="plus" class="h-4 w-4"></i>
                <span>New Drug</span>
              </a>
            @endif
          </div>
        </div>
      </div>

      {{-- Info strip (optional, kept from your original) --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-5">
        <div class="grid gap-6 sm:grid-cols-3">
          <div class="flex items-start gap-3">
            <i data-lucide="notebook-pen" class="h-5 w-5 text-slate-600 mt-0.5"></i>
            <div>
              <div class="text-sm font-semibold text-slate-900">Curate Monographs</div>
              <div class="text-xs text-slate-500">Indications, dosing, contraindications, nursing responsibilities.</div>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <i data-lucide="pill" class="h-5 w-5 text-slate-600 mt-0.5"></i>
            <div>
              <div class="text-sm font-semibold text-slate-900">Interactions &amp; Safety</div>
              <div class="text-xs text-slate-500">Major/minor interactions, high-alert flags, and monitoring.</div>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <i data-lucide="shield-check" class="h-5 w-5 text-slate-600 mt-0.5"></i>
            <div>
              <div class="text-sm font-semibold text-slate-900">Publish &amp; Keep Updated</div>
              <div class="text-xs text-slate-500">Show last-reviewed date; maintain source references.</div>
            </div>
          </div>
        </div>
      </div>

      {{-- Empty state (same style as Procedures) --}}
      @if($list->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
          <p class="text-sm text-slate-500">
            No drugs found. Use <span class="font-semibold">New Drug</span> to curate your first monograph.
          </p>
        </div>
      @else

      {{-- Skeleton grid (copied pattern from Procedures Library) --}}
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

      {{-- Cards grid (same structure/animation as Procedures cards) --}}
      <div id="cardsGrid" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3 hidden">
        @foreach($list as $d)
          @php
            $generic   = $d['generic'] ?? '';
            $brands    = $d['brands']  ?? [];
            $class     = $d['class']   ?? null;
            $warns     = $d['warnings'] ?? [];
            $updated   = $d['updated_at'] ?? null;
            $highAlert = ($d['high_alert'] ?? false) === true;
            $route     = $d['route'] ?? null;
            $ageGroup  = $d['age_group'] ?? null; // optional

            $keywords = \Illuminate\Support\Str::of(
              trim(
                $generic . ' ' .
                implode(' ', (array) $brands) . ' ' .
                ($class ?? '') . ' ' .
                ($route ?? '') . ' ' .
                ($ageGroup ?? '') . ' ' .
                implode(' ', (array) $warns)
              )
            )->lower();
          @endphp

          <article
            class="js-card flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:shadow-md transition-shadow opacity-0"
            data-class="{{ $class }}"
            data-route="{{ $route }}"
            data-age="{{ $ageGroup }}"
            data-keywords="{{ $keywords }}"
          >
            <header class="flex items-start justify-between gap-2">
              <div>
                <h2 class="text-[14px] font-semibold text-slate-900 leading-snug flex items-center gap-2">
                  <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-50">
                    <i data-lucide="pill" class="h-4 w-4 text-slate-700"></i>
                  </span>
                  {{ $generic ?: 'Unnamed drug' }}
                </h2>

                @if($class || !empty($brands))
                  <p class="mt-1 text-[12px] text-slate-600 line-clamp-3">
                    @if($class)
                      <span class="font-medium">Class:</span> {{ $class }}
                    @endif
                    @if($class && !empty($brands)) · @endif
                    @if(!empty($brands))
                      <span class="font-medium">Brands:</span> {{ implode(', ', $brands) }}
                    @endif
                  </p>
                @endif

                <div class="mt-2 flex flex-wrap items-center gap-2">
                  @if($route)
                    <span class="inline-flex items-center rounded-full bg-sky-50 text-sky-700 px-2 py-0.5 text-[11px]">
                      <i data-lucide="syringe" class="h-3 w-3 mr-1"></i>
                      {{ $route }}
                    </span>
                  @endif

                  @if($ageGroup)
                    <span class="inline-flex items-center rounded-full bg-slate-50 text-slate-700 px-2 py-0.5 text-[11px]">
                      <i data-lucide="users" class="h-3 w-3 mr-1"></i>
                      {{ ucfirst($ageGroup) }}
                    </span>
                  @endif
                </div>
              </div>

              <div class="flex flex-col items-end gap-1">
                @if($highAlert)
                  <span class="inline-flex items-center rounded-full bg-rose-50 text-rose-700 px-2 py-0.5 text-[11px] font-medium">
                    <i data-lucide="alert-triangle" class="h-3 w-3 mr-1"></i>
                    High-alert
                  </span>
                @endif
                @if(!empty($warns))
                  <span class="inline-flex items-center rounded-full bg-amber-50 text-amber-700 px-2 py-0.5 text-[11px] font-medium">
                    <i data-lucide="shield" class="h-3 w-3 mr-1"></i>
                    Safety notes
                  </span>
                @endif
              </div>
            </header>

            {{-- Warning chips --}}
            @if(!empty($warns))
              <div class="mt-3 flex flex-wrap items-center gap-2 text-[11px]">
                @foreach($warns as $w)
                  <span class="rounded-full border px-2 py-0.5 {{ $tagClass($w) }}">{{ $w }}</span>
                @endforeach
              </div>
            @endif

            {{-- Actions (same sticky bottom pattern as Procedures) --}}
            <div class="mt-auto pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2">
              <a href="{{ route('faculty.drug_guide.show', $d['id']) }}"
                 class="inline-flex items-center gap-1 rounded-xl border border-slate-200 px-3 py-1.5 text-[12px] font-medium text-slate-800 hover:bg-slate-50">
                <i data-lucide="eye" class="h-3 w-3"></i>
                Open Monograph
              </a>
              @if($canManage ?? false)
                <a href="{{ route('faculty.drug_guide.edit', $d['id']) }}"
                   class="inline-flex items-center gap-1 rounded-xl border border-amber-200 px-3 py-1.5 text-[12px] font-medium text-amber-800 hover:bg-amber-50">
                  <i data-lucide="edit-3" class="h-3 w-3"></i>
                  Edit
                </a>
              @endif
            </div>

            {{-- Meta row --}}
            <div class="mt-3 flex items-center justify-between text-[11px] text-slate-500">
              <span class="inline-flex items-center gap-1">
                <i data-lucide="calendar-clock" class="h-3 w-3"></i>
                @if($updated)
                  Updated {{ \Illuminate\Support\Carbon::parse($updated)->diffForHumans() }}
                @else
                  Updated —
                @endif
              </span>
            </div>
          </article>
        @endforeach
      </div>

      {{-- Pager (client-side, same code style as Procedures) --}}
      <div id="pagerShell" class="mt-4 flex items-center justify-between text-[12px] text-slate-500">
        <div id="pagerSummary">Showing 0–0 of 0 drugs</div>
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

      @endif {{-- /empty check --}}

      {{-- Footer note --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-5">
        <div class="flex items-start gap-3">
          <i data-lucide="info" class="h-5 w-5 text-slate-500 mt-0.5"></i>
          <p class="text-[13px] leading-6 text-slate-600">
            Educational reference for on-campus training. Verify doses against institutional policy.
            No real patient data is stored.
          </p>
        </div>
      </div>

    </div>
  </section>
</main>

@includeIf('partials.faculty-footer')
@includeWhen(!View::exists('partials.faculty-footer'), 'partials.student-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script>
  // Icons
  lucide.createIcons();

  // Debounce (same pattern as Procedures page)
  const debounce = (fn, ms = 350) => {
    let t;
    return (...a) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...a), ms);
    };
  };

  // Elements
  const q           = document.getElementById('searchBox');
  const classSelect = document.getElementById('classSelect');
  const routeSelect = document.getElementById('routeSelect');
  const ageSelect   = document.getElementById('ageSelect');
  const cardsGrid   = document.getElementById('cardsGrid');
  const skeletonGrid = document.getElementById('skeletonGrid');
  const cards       = [...document.querySelectorAll('.js-card')];

  // State
  let classVal = 'all';
  let routeVal = 'all';
  let ageVal   = 'all';
  const pageSize = 12;
  let currentPage = 1;

  // Pager
  const pagerShell   = document.getElementById('pagerShell');
  const pagerSummary = document.getElementById('pagerSummary');
  const pageButtons  = document.getElementById('pageButtons');
  const btnPrev      = document.getElementById('btnPrev');
  const btnNext      = document.getElementById('btnNext');

  // Helpers
  function showSkeleton(show) {
    if (!skeletonGrid || !cardsGrid) return;
    skeletonGrid.classList.toggle('hidden', !show);
    cardsGrid.classList.toggle('hidden', show);
  }

  function getFilteredCards() {
    const needle = (q?.value || '').toLowerCase().trim();
    return cards.filter(card => {
      const okClass = (classVal === 'all') || (card.dataset.class === classVal);
      const okRoute = (routeVal === 'all') || (card.dataset.route === routeVal);
      const okAge   = (ageVal   === 'all') || (card.dataset.age === ageVal);
      const okSearch = !needle || (card.dataset.keywords || '').includes(needle);
      return okClass && okRoute && okAge && okSearch;
    });
  }

  // Animate visible slice with gentle stagger (same as Procedures)
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

    // Hide all
    cards.forEach(c => {
      c.style.display = 'none';
    });

    // Current slice
    const startIdx = (currentPage - 1) * pageSize;
    const endIdx = Math.min(startIdx + pageSize, total);
    const slice = [];
    for (let i = startIdx; i < endIdx; i++) {
      const card = filtered[i];
      if (!card) continue;
      card.style.display = '';
      card.classList.remove('animate-card-in');
      card.classList.add('opacity-0');
      slice.push(card);
    }

    // Summary & controls
    const humanStart = total === 0 ? 0 : startIdx + 1;
    const humanEnd   = endIdx;
    pagerSummary.textContent = `Showing ${humanStart}–${humanEnd} of ${total} drugs`;

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
        btn.addEventListener('click', () => {
          currentPage = page;
          renderWithSkeleton();
          scrollPagerIntoView();
        });
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
    if (!fullyVisible) {
      pagerShell.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
  }

  // Render with skeleton for smoothness (same timing as Procedures)
  function renderWithSkeleton() {
    showSkeleton(true);
    const delay = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 220;
    setTimeout(() => {
      renderPage();
      showSkeleton(false);
    }, delay);
  }

  // Bindings
  q?.addEventListener('input', debounce(() => {
    currentPage = 1;
    renderWithSkeleton();
  }, 350));

  classSelect?.addEventListener('change', () => {
    classVal = classSelect.value || 'all';
    currentPage = 1;
    renderWithSkeleton();
  });

  routeSelect?.addEventListener('change', () => {
    routeVal = routeSelect.value || 'all';
    currentPage = 1;
    renderWithSkeleton();
  });

  ageSelect?.addEventListener('change', () => {
    ageVal = ageSelect.value || 'all';
    currentPage = 1;
    renderWithSkeleton();
  });

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

  // First paint
  document.addEventListener('DOMContentLoaded', () => {
    renderWithSkeleton();
  });
</script>
</body>
</html>
