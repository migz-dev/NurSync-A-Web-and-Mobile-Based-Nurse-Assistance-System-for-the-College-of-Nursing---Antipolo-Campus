{{-- resources/views/student/clinical_experiences/index.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Clinical Experience Stories · NurSync (SN)</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; }

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
  {{-- Sidebar (Student) --}}
  @include('partials.sidebar', ['active' => 'clinical_experiences'])

  {{-- Main content --}}
  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-8 py-12 space-y-6">

      {{-- Page heading --}}
      <header class="flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
            <i data-lucide="stethoscope" class="h-4 w-4"></i>
          </span>
          <div>
            <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
              Clinical Experience Stories
            </h1>
            <p class="text-[13px] text-slate-500 mt-1">
              Learn from real clinical stories shared by your Clinical Instructors, translated into lessons for practice.
            </p>
          </div>
        </div>
      </header>

      @php
        /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|\App\Models\ClinicalExperience[] $experiences */

        // Support both paginator or plain collection
        if ($experiences instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) {
            $list = $experiences->getCollection();
        } else {
            $list = $experiences ?? collect();
        }

        $wardOptions = $list->pluck('ward')->filter()->unique()->sort()->values();
        $statusOptions = collect(['draft','published','archived'])
            ->filter(fn($s) => $list->contains('status', $s));

        $statusChip = function (string $status) {
          return match ($status) {
            'published' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'draft'     => 'bg-slate-50 text-slate-700 border-slate-200',
            'archived'  => 'bg-amber-50 text-amber-800 border-amber-200',
            default     => 'bg-slate-50 text-slate-700 border-slate-200',
          };
        };

        $wardChip = function (?string $ward) {
          return match ($ward) {
            'CHN', 'Community Health Nursing' => 'bg-emerald-50 text-emerald-700',
            'OB', 'Obstetrics'                => 'bg-pink-50 text-pink-700',
            'DR', 'Delivery Room'             => 'bg-pink-50 text-pink-700',
            'PEDIA', 'Pediatrics'             => 'bg-sky-50 text-sky-700',
            'MS', 'Medical-Surgical'          => 'bg-slate-50 text-slate-700',
            'ICU'                             => 'bg-rose-50 text-rose-700',
            'ONCO', 'Oncology'                => 'bg-fuchsia-50 text-fuchsia-700',
            'GERIA', 'Geriatric'              => 'bg-amber-50 text-amber-700',
            'ORTHO', 'Orthopedics'            => 'bg-cyan-50 text-cyan-700',
            'PSYCH', 'Psychiatric'            => 'bg-violet-50 text-violet-700',
            'ER', 'Emergency Room'            => 'bg-orange-50 text-orange-700',
            'OR', 'Operating Room'            => 'bg-green-50 text-green-700',
            'MEDICINE', 'Medicine Ward'       => 'bg-indigo-50 text-indigo-700',
            'SURGERY', 'Surgery Ward'         => 'bg-red-50 text-red-700',
            default                           => 'bg-slate-50 text-slate-700',
          };
        };

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

      {{-- Filters --}}
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center flex-1">
            {{-- Search --}}
            <div class="relative flex-1 sm:w-64 lg:w-80">
              <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
              <input id="searchBox" type="text"
                     placeholder="Search by title, ward, or key lesson…"
                     class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" />
            </div>

            {{-- Ward filter --}}
            <select id="wardSelect"
                    class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
              <option value="all">All wards</option>
              @foreach($wardOptions as $w)
                <option value="{{ $w }}">{{ $w }}</option>
              @endforeach
            </select>

            {{-- Status filter (kept for design consistency, typically only "Published" for students) --}}
            @if($statusOptions->isNotEmpty())
              <select id="statusSelect"
                      class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                <option value="all">All statuses</option>
                @foreach($statusOptions as $s)
                  <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                @endforeach
              </select>
            @else
              <input type="hidden" id="statusSelect" value="all">
            @endif
          </div>
        </div>
      </div>

      {{-- Empty state --}}
      @if($list->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
          <p class="text-sm text-slate-500">
            There are no published clinical experience stories available yet.
            Once your Clinical Instructors share their reflections, you’ll see them here.
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
                <span class="h-6 w-20 rounded-full bg-slate-100"></span>
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
        @foreach($list as $exp)
          @php
            $wardLabel = $exp->ward;
            $status    = $exp->status ?? 'draft';

            $themes = [];
            if (!empty($exp->key_takeaways)) $themes[] = 'Key lessons';
            if (mb_strlen((string) $exp->story) > 800) $themes[] = 'Long-form story';
            if (($exp->attachments_count ?? 0) > 0) $themes[] = 'With media';

            $keywords = \Illuminate\Support\Str::of(
                ($exp->title ?? '') . ' ' .
                ($exp->summary ?? '') . ' ' .
                ($wardLabel ?? '') . ' ' .
                ($exp->key_takeaways ?? '') . ' ' .
                $status
            )->lower();
          @endphp

          <article
            class="js-card flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow opacity-0"
            data-ward="{{ $wardLabel }}"
            data-status="{{ $status }}"
            data-keywords="{{ $keywords }}"
          >
            {{-- Header --}}
            <header class="flex items-start justify-between gap-4">
              <div class="flex-1">
                {{-- Fixed-height block --}}
                <div class="min-h-[90px] flex flex-col gap-1.5">
                  <h2 class="text-[16px] font-semibold text-slate-900 leading-snug flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50">
                      <i data-lucide="stethoscope" class="h-5 w-5 text-slate-700"></i>
                    </span>
                    {{ $exp->title }}
                  </h2>

                  {{-- Summary --}}
                  <p class="text-[13px] text-slate-600 line-clamp-3">
                    @if(!empty($exp->summary))
                      {{ $exp->summary }}
                    @else
                      A real clinical situation, described by your CI and turned into a reflection for your learning.
                    @endif
                  </p>

                  {{-- Chips row --}}
                  <div class="mt-1.5 flex flex-wrap items-center gap-2.5 text-[12px]">

                    @if ($wardLabel)
                      <span class="inline-flex items-center rounded-full {{ $wardChip($wardLabel) }} px-2.5 py-0.5">
                        <i data-lucide="hospital" class="h-3 w-3 mr-1"></i>
                        {{ $wardLabel }}
                      </span>
                    @endif

                    @if(isset($exp->attachments_count) && $exp->attachments_count > 0)
                      <span class="inline-flex items-center rounded-full bg-slate-50 text-slate-700 px-2.5 py-0.5">
                        <i data-lucide="image" class="h-3 w-3 mr-1"></i>
                        {{ $exp->attachments_count }} media
                      </span>
                    @endif

                  </div>
                </div>
              </div>

              {{-- Status pill (usually Published for students) --}}
              <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[11px] font-medium {{ $statusChip($status) }}">
                <i data-lucide="{{ $status === 'published' ? 'check-circle-2' : ($status === 'draft' ? 'file-pen-line' : 'archive') }}"
                   class="h-3 w-3 mr-1"></i>
                {{ ucfirst($status) }}
              </span>
            </header>

            {{-- Meta row --}}
            <div class="mt-4 flex items-center justify-between text-[12px] text-slate-500">
              <span class="flex items-center gap-1.5">
                <i data-lucide="mic" class="h-3 w-3"></i>
                Shared by your Clinical Instructor
              </span>
              <span class="whitespace-nowrap">
                Updated {{ $exp->updated_at?->diffForHumans() ?? '—' }}
              </span>
            </div>

            {{-- Themes row --}}
            @if(!empty($themes))
              <div class="mt-3 flex flex-wrap items-center gap-2 text-[12px]">
                @foreach($themes as $t)
                  <span class="rounded-full border px-2.5 py-0.5 {{ $tagClass($t) }}">
                    {{ $t }}
                  </span>
                @endforeach
              </div>
            @endif

            {{-- Bottom action bar (view-only) --}}
            <div class="mt-auto pt-5 border-t border-slate-100 flex items-center gap-2">
              <a href="{{ route('student.experiences.show', $exp) }}"
                 class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 py-2 text-[13px] font-medium text-slate-800 hover:bg-slate-50">
                <i data-lucide="book-open" class="h-4 w-4"></i>
                Read Story
              </a>
            </div>
          </article>
        @endforeach
      </div>

      {{-- Pager (client-side) --}}
      <div id="pagerShell" class="mt-4 flex items-center justify-between text-[12px] text-slate-500">
        <div id="pagerSummary">Showing 0–0 of 0 stories</div>
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

<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();

  const debounce = (fn, ms = 350) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

  const searchBox    = document.getElementById('searchBox');
  const wardSelect   = document.getElementById('wardSelect');
  const statusSelect = document.getElementById('statusSelect');
  const cardsGrid    = document.getElementById('cardsGrid');
  const skeletonGrid = document.getElementById('skeletonGrid');
  const cards        = [...document.querySelectorAll('.js-card')];

  let wardFilter   = 'all';
  let statusFilter = statusSelect ? (statusSelect.value || 'all') : 'all';
  const pageSize   = 12;
  let currentPage  = 1;

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
      const cardWard   = card.dataset.ward || '';
      const cardStatus = card.dataset.status || '';
      const keywords   = (card.dataset.keywords || '');

      const okWard   = (wardFilter === 'all')   || (cardWard === wardFilter);
      const okStatus = (statusFilter === 'all') || (cardStatus === statusFilter);
      const okSearch = !needle || keywords.includes(needle);

      return okWard && okStatus && okSearch;
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

    // hide all
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
    pagerSummary.textContent = `Showing ${humanStart}–${humanEnd} of ${total} stories`;

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
        btn.addEventListener('click', () => { currentPage = page; renderWithSkeleton(); });
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

  function renderWithSkeleton() {
    showSkeleton(true);
    const delay = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 220;
    setTimeout(() => { renderPage(); showSkeleton(false); }, delay);
  }

  searchBox?.addEventListener('input', debounce(() => {
    currentPage = 1;
    renderWithSkeleton();
  }, 350));

  wardSelect?.addEventListener('change', () => {
    wardFilter = wardSelect.value;
    currentPage = 1;
    renderWithSkeleton();
  });

  statusSelect?.addEventListener('change', () => {
    statusFilter = statusSelect.value || 'all';
    currentPage = 1;
    renderWithSkeleton();
  });

  btnPrev?.addEventListener('click', () => {
    if (currentPage > 1) {
      currentPage--;
      renderWithSkeleton();
    }
  });

  btnNext?.addEventListener('click', () => {
    currentPage++;
    renderWithSkeleton();
  });

  document.addEventListener('DOMContentLoaded', () => {
    renderWithSkeleton();
  });
</script>
</body>
</html>
