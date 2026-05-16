<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Board Exam Question Bank · NurSync (CI)</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; }

    /* Smooth card entrance after skeleton hides (same as Ward Orientation / Procedures / Assessment Guides / Competency) */
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
  {{-- Sidebar – CI Instructor Mode --}}
  @include('partials.instructor-sidebar', ['active' => 'board_exam_bank'])

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
              Board Exam Question Bank
            </h1>
            <p class="text-[13px] text-slate-500 mt-1">
              Build and refine your own NLE-style questions with rationales, categories, and difficulty levels to train student nurses.
            </p>
          </div>
        </div>

        {{-- New Exam Set (desktop) --}}
        <div class="hidden sm:flex">
          <a href="{{ route('faculty.instructor.board_exam.create') }}"
             class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-3.5 py-2.5 text-[13px] font-medium text-white shadow-sm hover:bg-emerald-700">
            <i data-lucide="plus" class="h-4 w-4"></i>
            <span>New Exam Set</span>
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
        /** @var \Illuminate\Support\Collection|\App\Models\BoardExamQuestion[]|\Illuminate\Pagination\LengthAwarePaginator $questions */

        // If paginator, use underlying collection for client-side pagination
        $questionsCollection = ($questions ?? collect()) instanceof \Illuminate\Pagination\LengthAwarePaginator
          ? $questions->getCollection()
          : ($questions ?? collect());

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

        // Status options
        $statusOptions = collect(['all', 'draft', 'published', 'archived']);

        // Difficulty options
        $difficultyOptions = collect(['all', 'easy', 'moderate', 'difficult']);

        // Static ward / area list (matches ENUM / create page)
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

        // Status chip styling
        $statusChipClasses = function (?string $status) {
          return match ($status) {
            'published' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            'draft'     => 'bg-slate-50 text-slate-700 border border-slate-200',
            'archived'  => 'bg-rose-50 text-rose-700 border-rose-200',
            default     => 'bg-slate-50 text-slate-700 border-slate-200',
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

      {{-- Filters --}}
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center w-full flex-1">
            {{-- Search --}}
            <div class="relative flex-1 sm:w-64 lg:w-80">
              <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
              <input id="searchBox" type="text"
                     placeholder="Search questions by stem, exam title, category, or rationale…"
                     value="{{ $currentSearch }}"
                     class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" />
            </div>

            {{-- Status filter --}}
            <select id="statusSelect"
                    class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
              @foreach($statusOptions as $status)
                <option value="{{ $status }}" {{ $currentStatus === $status ? 'selected' : '' }}>
                  {{ $status === 'all' ? 'All statuses' : ucfirst($status) }}
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

          {{-- Mobile New button --}}
          <div class="flex sm:hidden">
            <a href="{{ route('faculty.instructor.board_exam.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-3.5 py-2.5 text-[13px] font-medium text-white shadow-sm hover:bg-emerald-700 w-full justify-center">
              <i data-lucide="plus" class="h-4 w-4"></i>
              <span>New Exam Set</span>
            </a>
          </div>
        </div>

        {{-- Hint line --}}
        <p class="text-[11px] text-slate-500 mt-1">
          Tip: Tag your questions by ward/area and difficulty so student practice sets can be more targeted.
        </p>
      </div>

      {{-- Empty state --}}
      @if($questionsCollection->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
          <p class="text-sm text-slate-500">
            No board exam questions yet.
            Click <span class="font-semibold">New Exam Set</span> to start building your NLE-style item bank.
          </p>
        </div>
      @else

      {{-- Skeleton grid --}}
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

      {{-- Cards grid --}}
      <div id="cardsGrid" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3 hidden">
        @foreach($questionsCollection as $qItem)
          @php
            $status      = $qItem->status ?? 'draft';
            $statusLabel = ucfirst($status);
            $statusClasses = $statusChipClasses($status);

            $category   = $qItem->category ?: 'Uncategorized';
            $difficulty = $qItem->difficulty ?: 'easy';
            $difficultyLabel = ucfirst($difficulty);
            $difficultyChip  = $difficultyClasses($difficulty);

            $examTitle  = $qItem->exam_title ?: 'Untitled exam set';

            $keywords = \Illuminate\Support\Str::of(
              ($qItem->question_text ?? '') . ' ' .
              ($qItem->rationale ?? '') . ' ' .
              ($category ?? '') . ' ' .
              ($examTitle ?? '')
            )->lower();

            $shortStem = \Illuminate\Support\Str::limit(trim($qItem->question_text ?? ''), 140);
          @endphp

          <article
            class="js-card flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow opacity-0"
            data-status="{{ $status }}"
            data-category="{{ $category }}"
            data-difficulty="{{ $difficulty }}"
            data-keywords="{{ $keywords }}"
          >
            {{-- Header --}}
            <header class="flex items-start justify-between gap-4">
              <div class="flex-1">
                <div class="min-h-[96px] flex flex-col gap-1.5">
                  {{-- Exam title chip --}}
                  <div class="mb-1 flex flex-wrap items-center gap-2 text-[11px]">
                    <span class="inline-flex items-center rounded-full border border-sky-200 bg-sky-50 px-2.5 py-0.5 text-sky-700">
                      <i data-lucide="notebook-text" class="h-3 w-3 mr-1"></i>
                      {{ $examTitle }}
                    </span>
                  </div>

                  <h2 class="text-[15px] font-semibold text-slate-900 leading-snug flex items-start gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 mr-1">
                      <i data-lucide="brain" class="h-5 w-5 text-slate-700"></i>
                    </span>
                    <span class="line-clamp-3">{{ $shortStem }}</span>
                  </h2>

                  {{-- Chips row --}}
                  <div class="mt-1.5 flex flex-wrap items-center gap-2.5 text-[11px]">
                    {{-- Status --}}
                    <span class="inline-flex items-center rounded-full {{ $statusClasses }} px-2.5 py-0.5 font-medium">
                      <i data-lucide="circle-dot" class="h-3 w-3 mr-1"></i>
                      {{ $statusLabel }}
                    </span>

                    {{-- Category (ward / area) --}}
                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 bg-slate-50 text-slate-700 border-slate-200">
                      <i data-lucide="map" class="h-3 w-3 mr-1"></i>
                      {{ $category }}
                    </span>

                    {{-- Difficulty --}}
                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 {{ $difficultyChip }} text-[11px] font-medium">
                      <i data-lucide="activity" class="h-3 w-3 mr-1"></i>
                      {{ $difficultyLabel }}
                    </span>
                  </div>
                </div>
              </div>

              {{-- Icon bubble --}}
              <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                <i data-lucide="check-circle-2" class="h-5 w-5"></i>
              </span>
            </header>

            {{-- Meta --}}
            <div class="mt-4 flex items-center justify-between text-[12px] text-slate-500">
              <span class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1.5">
                  <i data-lucide="edit-3" class="h-3 w-3"></i>
                  4-choice NLE-style item
                </span>
                @if($qItem->rationale)
                  <span class="inline-flex items-center gap-1.5">
                    <i data-lucide="book-open" class="h-3 w-3"></i>
                    With rationale
                  </span>
                @endif
              </span>
              <span class="whitespace-nowrap">
                Updated {{ $qItem->updated_at?->diffForHumans() ?? '—' }}
              </span>
            </div>

            {{-- Actions --}}
            <div class="mt-auto pt-5 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
              {{-- Open / View --}}
              <a href="{{ route('faculty.instructor.board_exam.show', $qItem->id) }}"
                 class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 py-2 text-[13px] font-medium text-slate-800 hover:bg-slate-50">
                <i data-lucide="eye" class="h-4 w-4"></i>
                Open
              </a>

              {{-- Edit --}}
              <a href="{{ route('faculty.instructor.board_exam.edit', $qItem->id) }}"
                 class="inline-flex items-center gap-1.5 rounded-xl border border-amber-200 px-3.5 py-2 text-[13px] font-medium text-amber-800 hover:bg-amber-50">
                <i data-lucide="edit-3" class="h-4 w-4"></i>
                Edit
              </a>

              {{-- Archive --}}
              <form method="POST"
                    action="{{ route('faculty.instructor.board_exam.archive', $qItem->id) }}"
                    onsubmit="return confirm('Archive this question? You can still re-publish it later.');">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 py-2 text-[13px] font-medium text-slate-700 hover:bg-slate-50">
                  <i data-lucide="archive" class="h-4 w-4"></i>
                  Archive
                </button>
              </form>
            </div>
          </article>
        @endforeach
      </div>

      {{-- Pager (client-side) --}}
      <div id="pagerShell" class="mt-4 flex items-center justify-between text-[12px] text-slate-500">
        <div id="pagerSummary">Showing 0–0 of 0 questions</div>
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

@includeIf('partials.faculty-footer')
@includeWhen(!View::exists('partials.faculty-footer'), 'partials.student-footer')

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
      const cardStatus     = card.dataset.status || 'draft';
      const cardCategory   = card.dataset.category || '';
      const cardDifficulty = card.dataset.difficulty || 'easy';

      const okStatus = (statusFilter === 'all') || (cardStatus === statusFilter);
      const okCat    = (categoryFilter === 'all') || (String(cardCategory) === String(categoryFilter));
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
    pagerSummary.textContent = `Showing ${humanStart}–${humanEnd} of ${total} questions`;

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
