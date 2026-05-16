{{-- resources/views/student/board_exam/index.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Board Exam Practice · NurSync (Student)</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; }

    /* Smooth card entrance after skeleton hides (same as CI Question Bank) */
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
  {{-- Sidebar – Student --}}
  @include('partials.sidebar', ['active' => 'board_exam_practice'])

  {{-- Main content --}}
  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-8 py-12 space-y-6">

      {{-- Page heading --}}
      <header class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
            <i data-lucide="graduation-cap" class="h-4 w-4"></i>
          </span>
          <div>
            <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
              Board Exam Practice
            </h1>
            <p class="text-[13px] text-slate-500 mt-1">
              Take NLE-style practice exams curated by your Clinical Instructors. Retake sets, track your scores, and focus on wards and topics that need more review.
            </p>
          </div>
        </div>

        {{-- Scores shortcut (desktop) --}}
        <div class="hidden sm:flex">
          <a href="{{ route('student.board_exam.results.index') }}"
             class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-3.5 py-2.5 text-[13px] font-medium text-white shadow-sm hover:bg-slate-800">
            <i data-lucide="award" class="h-4 w-4"></i>
            <span>View All Scores</span>
          </a>
        </div>
      </header>

      {{-- Flash messages --}}
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
        /**
         * @var \Illuminate\Support\Collection $examSets
         * Each item is expected to be an object/array with at least:
         *  - id or exam_id       (for route binding)
         *  - exam_title
         *  - faculty_name
         *  - question_count
         *  - primary_category (optional)
         *  - categories_label (optional)
         *  - difficulty_label (optional)
         *  - dominant_difficulty (optional, slug easy/moderate/difficult)
         *  - attempt_status (not_started/in_progress/completed)
         *  - last_score_percent (optional)
         *  - attempts_count (optional)
         *  - last_taken_human (optional)
         */

        $examSets = $examSets ?? collect();

        $filters = $filters ?? [
          'q'          => '',
          'category'   => 'all',
          'difficulty' => 'all',
          'status'     => 'all',
        ];

        $currentSearch     = $filters['q']          ?? '';
        $currentCategory   = $filters['category']   ?? 'all';
        $currentDifficulty = $filters['difficulty'] ?? 'all';
        $currentStatus     = $filters['status']     ?? 'all';

        // Attempt status filter (student-side)
        $statusOptions = collect(['all', 'not_started', 'in_progress', 'completed']);

        // Difficulty options
        $difficultyOptions = collect(['all', 'easy', 'moderate', 'difficult']);

        // Static ward / area list (align with CI side)
        $areas = [
          'Community Health (CHN)',
          'OB Ward',
          'Delivery Room (DR)',
          'Nursery',
          'Pediatrics (PEDIA)',
          'Medical-Surgical (MS)',
          'ICU',
          'Oncology',
          'Isolation Unit',
          'Endocrine Unit',
          'Neurology Unit',
          'Psychiatric (PSYCH)',
          'Emergency Room (ER)',
          'Operating Room (OR)',
          'Trauma Unit',
          'Disaster Response / Community Field',
        ];

        $categories = collect($areas);

        // Attempt status chip styling (student-side)
        $statusChipClasses = function (?string $status) {
          return match ($status) {
            'completed'   => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            'in_progress' => 'bg-sky-50 text-sky-700 border border-sky-200',
            'not_started' => 'bg-slate-50 text-slate-700 border-slate-200',
            default       => 'bg-slate-50 text-slate-700 border-slate-200',
          };
        };

        // Difficulty chip styling
        $difficultyClasses = function (?string $difficulty) {
          return match ($difficulty) {
            'easy'      => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'moderate'  => 'bg-amber-50 text-amber-800 border-amber-200',
            'difficult' => 'bg-rose-50 text-rose-700 border-rose-200',
            default     => 'bg-slate-50 text-slate-700 border-slate-200',
          };
        };
      @endphp

      {{-- Filters (mirrors CI layout) --}}
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center w-full flex-1">
            {{-- Search --}}
            <div class="relative flex-1 sm:w-64 lg:w-80">
              <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
              <input id="searchBox" type="text"
                     placeholder="Search practice exams by title, ward, or CI…"
                     value="{{ $currentSearch }}"
                     class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" />
            </div>

            {{-- Status filter (attempt status) --}}
            <select id="statusSelect"
                    class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
              @foreach($statusOptions as $status)
                @php
                  $label = match($status) {
                    'not_started' => 'Not started',
                    'in_progress' => 'In progress',
                    'completed'   => 'Completed',
                    default       => 'All statuses',
                  };
                @endphp
                <option value="{{ $status }}" {{ $currentStatus === $status ? 'selected' : '' }}>
                  {{ $label }}
                </option>
              @endforeach
            </select>

            {{-- Category filter (ward/area) --}}
            <select id="categorySelect"
                    class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
              <option value="all" {{ $currentCategory === 'all' ? 'selected' : '' }}>All wards / areas</option>
              @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ (string)$currentCategory === (string)$cat ? 'selected' : '' }}>
                  {{ $cat }}
                </option>
              @endforeach
            </select>

            {{-- Difficulty filter --}}
            <select id="difficultySelect"
                    class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
              @foreach($difficultyOptions as $d)
                <option value="{{ $d }}" {{ $currentDifficulty === $d ? 'selected' : '' }}>
                  {{ $d === 'all' ? 'All difficulty levels' : ucfirst($d) }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Mobile Scores button --}}
          <div class="flex sm:hidden">
            <a href="{{ route('student.board_exam.results.index') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-3.5 py-2.5 text-[13px] font-medium text-white shadow-sm hover:bg-slate-800 w-full justify-center">
              <i data-lucide="award" class="h-4 w-4"></i>
              <span>View All Scores</span>
            </a>
          </div>
        </div>

        {{-- Hint line --}}
        <p class="text-[11px] text-slate-500 mt-1">
          Tip: Filter by status to see exams you haven’t started yet, or retake completed sets to improve your mastery.
        </p>
      </div>

      {{-- Empty state --}}
      @if($examSets->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
          <p class="text-sm text-slate-500">
            No board exam practice sets are available yet.
            Once your Clinical Instructors publish exam sets, they will automatically appear here.
          </p>
        </div>
      @else

      {{-- Skeleton grid (same as CI) --}}
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

      {{-- Cards grid (mirroring CI layout, but student data + Start Exam only) --}}
      <div id="cardsGrid" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3 hidden">
        @foreach($examSets as $set)
          @php
            // Use numeric ID for the route `{exam}` (whereNumber)
            $examId         = $set->id ?? $set->exam_id ?? null;

            $examTitle       = $set->exam_title      ?? 'Untitled exam set';
            $facultyName     = $set->faculty_name    ?? 'Clinical Instructor';
            $questionCount   = $set->question_count  ?? 0;
            $primaryCategory = $set->primary_category ?? null;
            $categoriesLabel = $set->categories_label ?? $primaryCategory ?? 'Multi-ward mix';

            $difficultyLabel = $set->difficulty_label ?? 'Mixed difficulty';
            $dominantDiff    = strtolower($set->dominant_difficulty ?? '');
            $difficultyChip  = $difficultyClasses($dominantDiff ?: null);

            $attemptStatus   = $set->attempt_status ?? 'not_started'; // not_started / in_progress / completed
            $statusLabel     = match ($attemptStatus) {
              'completed'   => 'Completed',
              'in_progress' => 'In progress',
              default       => 'Not started',
            };

            $statusClasses   = $statusChipClasses($attemptStatus);

            $lastScorePercent = $set->last_score_percent ?? null;
            $attemptsCount    = $set->attempts_count ?? 0;
            $lastTakenHuman   = $set->last_taken_human ?? null;

            $keywords = \Illuminate\Support\Str::of(
              ($examTitle ?? '') . ' ' .
              ($categoriesLabel ?? '') . ' ' .
              ($facultyName ?? '')
            )->lower();

            // Always "Start Exam" per your requirement
            $actionLabel = 'Start Exam';
          @endphp

          <article
            class="js-card flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow opacity-0"
            data-status="{{ $attemptStatus }}"
            data-category="{{ $categoriesLabel }}"
            data-difficulty="{{ $dominantDiff }}"
            data-keywords="{{ $keywords }}"
          >
            {{-- Header --}}
            <header class="flex items-start justify-between gap-4">
              <div class="flex-1">
                <div class="min-h-[96px] flex flex-col gap-1.5">
                  {{-- Exam title chip (similar to CI examTitle chip) --}}
                  <div class="mb-1 flex flex-wrap items-center gap-2 text-[11px]">
                    <span class="inline-flex items-center rounded-full border border-sky-200 bg-sky-50 px-2.5 py-0.5 text-sky-700">
                      <i data-lucide="notebook-text" class="h-3 w-3 mr-1"></i>
                      {{ $examTitle }}
                    </span>
                  </div>

                  {{-- Main title + CI/ward row --}}
                  <h2 class="text-[15px] font-semibold text-slate-900 leading-snug flex items-start gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 mr-1">
                      <i data-lucide="brain" class="h-5 w-5 text-slate-700"></i>
                    </span>
                    <span class="flex-1">
                      <span class="block text-[13px] text-slate-500 mb-0.5">
                        {{ $facultyName }}
                      </span>
                      <span class="block text-[13px] text-slate-500">
                        {{ $categoriesLabel }}
                      </span>
                    </span>
                  </h2>

                  {{-- Chips row --}}
                  <div class="mt-1.5 flex flex-wrap items-center gap-2.5 text-[11px]">
                    {{-- Attempt status --}}
                    <span class="inline-flex items-center rounded-full {{ $statusClasses }} px-2.5 py-0.5 font-medium">
                      <i data-lucide="circle-dot" class="h-3 w-3 mr-1"></i>
                      {{ $statusLabel }}
                    </span>

                    {{-- Question count --}}
                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 bg-slate-50 text-slate-700 border-slate-200">
                      <i data-lucide="list-ordered" class="h-3 w-3 mr-1"></i>
                      {{ $questionCount }} {{ \Illuminate\Support\Str::plural('question', $questionCount) }}
                    </span>

                    {{-- Difficulty --}}
                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 {{ $difficultyChip }} text-[11px] font-medium">
                      <i data-lucide="activity" class="h-3 w-3 mr-1"></i>
                      {{ $difficultyLabel }}
                    </span>
                  </div>
                </div>
              </div>

              {{-- Icon bubble (same as CI) --}}
              <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                <i data-lucide="check-circle-2" class="h-5 w-5"></i>
              </span>
            </header>

            {{-- Meta --}}
            <div class="mt-4 flex items-center justify-between text-[12px] text-slate-500">
              <span class="flex flex-wrap items-center gap-2">
                @if(!is_null($lastScorePercent))
                  <span class="inline-flex items-center gap-1.5">
                    <i data-lucide="award" class="h-3 w-3"></i>
                    Last score: <span class="font-semibold">{{ number_format($lastScorePercent, 1) }}%</span>
                  </span>
                @else
                  <span class="inline-flex items-center gap-1.5">
                    <i data-lucide="sparkles" class="h-3 w-3"></i>
                    First time set
                  </span>
                @endif

                @if($attemptsCount > 0)
                  <span class="inline-flex items-center gap-1.5">
                    <i data-lucide="history" class="h-3 w-3"></i>
                    {{ $attemptsCount }} {{ \Illuminate\Support\Str::plural('attempt', $attemptsCount) }}
                  </span>
                @endif
              </span>

              <span class="whitespace-nowrap">
                @if($lastTakenHuman)
                  Last taken {{ $lastTakenHuman }}
                @else
                  Not taken yet
                @endif
              </span>
            </div>

            {{-- Actions (ONLY Start Exam) --}}
            <div class="mt-auto pt-5 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
              @if($examId)
                <a href="{{ route('student.board_exam.start', $examId) }}"
                   class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3.5 py-2 text-[13px] font-medium text-white shadow-sm hover:bg-emerald-700">
                  <i data-lucide="play-circle" class="h-4 w-4"></i>
                  <span>{{ $actionLabel }}</span>
                </a>
              @endif
            </div>
          </article>
        @endforeach
      </div>

      {{-- Pager (client-side) --}}
      <div id="pagerShell" class="mt-4 flex items-center justify-between text-[12px] text-slate-500">
        <div id="pagerSummary">Showing 0–0 of 0 exams</div>
        <div class="flex items-center gap-1">
          <button id="btnPrev"
                  class="rounded-lg border px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:hover:bg-transparent"
                  disabled>
            ‹ Prev
          </button>
          <div id="pageButtons" class="flex items-center gap-1"></div>
          <button id="btnNext"
                  class="rounded-lg border px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:hover:bg-transparent"
                  disabled>
            Next ›
          </button>
        </div>
      </div>

      @endif {{-- /empty check --}}

    </div>
  </section>
</main>

@includeIf('partials.student-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script>
  // Icons
  lucide.createIcons();

  // Debounce helper
  const debounce = (fn, ms = 350) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

  // Elements
  const searchBox        = document.getElementById('searchBox');
  const statusSelect     = document.getElementById('statusSelect');
  const categorySelect   = document.getElementById('categorySelect');
  const difficultySelect = document.getElementById('difficultySelect');
  const cardsGrid        = document.getElementById('cardsGrid');
  const skeletonGrid     = document.getElementById('skeletonGrid');
  const cards            = [...document.querySelectorAll('.js-card')];

  // State
  let statusFilter     = statusSelect ? statusSelect.value : 'all';
  let categoryFilter   = categorySelect ? categorySelect.value : 'all';
  let difficultyFilter = difficultySelect ? difficultySelect.value : 'all';
  const pageSize = 12;
  let currentPage = 1;

  // Pager elements
  const pagerShell   = document.getElementById('pagerShell');
  const pagerSummary = document.getElementById('pagerSummary');
  const pageButtons  = document.getElementById('pageButtons');
  const btnPrev      = document.getElementById('btnPrev');
  const btnNext      = document.getElementById('btnNext');

  function showSkeleton(show) {
    if (!skeletonGrid || !cardsGrid) return;
    skeletonGrid.classList.toggle('hidden', !show);
    cardsGrid.classList.toggle('hidden', show);
  }

  function getFilteredCards() {
    const needle = (searchBox?.value || '').toLowerCase().trim();

    return cards.filter(card => {
      const cardStatus     = card.dataset.status || 'not_started';
      const cardCategory   = card.dataset.category || '';
      const cardDifficulty = card.dataset.difficulty || '';

      const okStatus = (statusFilter === 'all') || (cardStatus === statusFilter);
      const okCat    = (categoryFilter === 'all') || (String(cardCategory).includes(String(categoryFilter)));
      const okDiff   = (difficultyFilter === 'all') || (cardDifficulty === difficultyFilter);
      const okSearch = !needle || (card.dataset.keywords || '').includes(needle);

      return okStatus && okCat && okDiff && okSearch;
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
    const filtered   = getFilteredCards();
    const total      = filtered.length;
    const totalPages = Math.max(1, Math.ceil(total / pageSize));
    if (currentPage > totalPages) currentPage = totalPages;

    cards.forEach(c => { c.style.display = 'none'; });

    const startIdx = (currentPage - 1) * pageSize;
    const endIdx   = Math.min(startIdx + pageSize, total);
    const slice    = [];

    for (let i = startIdx; i < endIdx; i++) {
      filtered[i].style.display = '';
      filtered[i].classList.remove('animate-card-in');
      filtered[i].classList.add('opacity-0');
      slice.push(filtered[i]);
    }

    const humanStart = total === 0 ? 0 : startIdx + 1;
    const humanEnd   = endIdx;
    pagerSummary.textContent = `Showing ${humanStart}–${humanEnd} of ${total} exams`;

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
    let end   = Math.min(totalPages, start + windowSize - 1);
    start     = Math.max(1, Math.min(start, Math.max(1, totalPages - windowSize + 1)));

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
    const vh   = window.innerHeight || document.documentElement.clientHeight;
    const fullyVisible = rect.top >= 0 && rect.bottom <= vh;
    if (!fullyVisible) pagerShell.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  function renderWithSkeleton() {
    showSkeleton(true);
    const delay = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 220;
    setTimeout(() => { renderPage(); showSkeleton(false); }, delay);
  }

  // Event bindings
  searchBox?.addEventListener('input', debounce(() => {
    currentPage = 1;
    renderWithSkeleton();
  }, 350));

  statusSelect?.addEventListener('change', () => {
    statusFilter = statusSelect.value || 'all';
    currentPage = 1;
    renderWithSkeleton();
  });

  categorySelect?.addEventListener('change', () => {
    categoryFilter = categorySelect.value || 'all';
    currentPage = 1;
    renderWithSkeleton();
  });

  difficultySelect?.addEventListener('change', () => {
    difficultyFilter = difficultySelect.value || 'all';
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

  document.addEventListener('DOMContentLoaded', () => {
    renderWithSkeleton();
  });
</script>
</body>
</html>
