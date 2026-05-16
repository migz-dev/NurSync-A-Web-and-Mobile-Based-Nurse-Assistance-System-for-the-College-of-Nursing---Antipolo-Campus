<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Ward Orientation · NurSync (Student)</title>

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
  {{-- Sidebar (student) --}}
  @include('partials.sidebar', ['active' => 'ward_orientation'])

  {{-- Main content --}}
  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-8 py-12 space-y-6">

      {{-- Page heading --}}
      <header class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
            <i data-lucide="map" class="h-4 w-4"></i>
          </span>
          <div>
            <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
              Ward Orientation
            </h1>
            <p class="text-[13px] text-slate-500 mt-1">
              Listen to how nurses describe their own wards so you know what to expect before your duty.
            </p>
          </div>
        </div>
      </header>

      @php
        /** @var \Illuminate\Support\Collection|\App\Models\WardOrientation[] $orientations */
        $orientations = $orientations ?? collect();

        $list = $orientations;

        $wardOptions = $list->pluck('ward_label')->filter()->unique()->sort()->values();

        $wardChip = function (?string $ward) {
          return match ($ward) {
            'Community Health Nursing' => 'bg-emerald-50 text-emerald-700',
            'Obstetrics'               => 'bg-pink-50 text-pink-700',
            'Delivery Room'            => 'bg-pink-50 text-pink-700',
            'Pediatrics'               => 'bg-sky-50 text-sky-700',
            'Medical-Surgical'         => 'bg-slate-50 text-slate-700',
            'ICU'                      => 'bg-rose-50 text-rose-700',
            'Oncology'                 => 'bg-fuchsia-50 text-fuchsia-700',
            'Geriatric'                => 'bg-amber-50 text-amber-700',
            'Orthopedics'              => 'bg-cyan-50 text-cyan-700',
            'Psychiatric'              => 'bg-violet-50 text-violet-700',
            'Emergency Room'           => 'bg-orange-50 text-orange-700',
            'Operating Room'           => 'bg-green-50 text-green-700',
            'Medicine Ward'            => 'bg-indigo-50 text-indigo-700',
            'Surgery Ward'             => 'bg-red-50 text-red-700',
            default                    => 'bg-slate-50 text-slate-700',
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
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center w-full flex-1">
            {{-- Search --}}
            <div class="relative flex-1 sm:w-64 lg:w-80">
              <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
              <input id="searchBox" type="text"
                     placeholder="Search by ward, topic, or key phrase…"
                     class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" />
            </div>
            {{-- Ward --}}
            <select id="wardSelect"
                    class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
              <option value="all">All wards</option>
              @foreach($wardOptions as $w)
                <option value="{{ $w }}">{{ $w }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      {{-- Empty state --}}
      @if($list->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
          <p class="text-sm text-slate-500">
            No ward orientations available yet. Your clinical instructors will soon add narrated guides here.
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
        @foreach($list as $o)
          @php
            $wardLabel = $o->ward_label;
            $length = $o->estimated_watch_minutes;
            $themes = [];

            if (!empty($o->culture_text))          $themes[] = 'Ward culture';
            if (!empty($o->routines_text))         $themes[] = 'Daily routines';
            if (!empty($o->patient_cases_text))    $themes[] = 'Typical cases';
            if (!empty($o->workload_text))         $themes[] = 'Workload management';
            if (!empty($o->emergencies_text))      $themes[] = 'Emergencies';
            if (!empty($o->layout_locations_text)) $themes[] = 'Layout & locations';
            if (!empty($o->tips_text))             $themes[] = 'Practical tips';

            $keywords = \Illuminate\Support\Str::of(
                ($o->title ?? '') . ' ' .
                ($o->summary ?? '') . ' ' .
                ($wardLabel ?? '') . ' ' .
                ($o->culture_text ?? '') . ' ' .
                ($o->routines_text ?? '') . ' ' .
                ($o->patient_cases_text ?? '') . ' ' .
                ($o->workload_text ?? '') . ' ' .
                ($o->emergencies_text ?? '') . ' ' .
                ($o->tips_text ?? '')
            )->lower();
          @endphp

<article
  class="js-card flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow opacity-0"
  data-ward="{{ $wardLabel }}"
  data-keywords="{{ $keywords }}"
>
  {{-- Header with fixed title+summary block --}}
  <header class="flex items-start justify-between gap-4">
    <div class="flex-1">

      {{-- FIXED HEIGHT so tags align across all cards --}}
      <div class="min-h-[90px] flex flex-col gap-1.5">
        <h2 class="text-[16px] font-semibold text-slate-900 leading-snug flex items-center gap-3">
          <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50">
            <i data-lucide="map" class="h-5 w-5 text-slate-700"></i>
          </span>
          {{ $o->title ?: ($wardLabel . ' · Ward Orientation') }}
        </h2>

        {{-- Summary --}}
        <p class="text-[13px] text-slate-600 line-clamp-3">
          @if(!empty($o->summary))
            {{ $o->summary }}
          @else
            A quick guide to what it's like working in the {{ strtolower($wardLabel ?? 'ward') }} as a nurse.
          @endif
        </p>

        {{-- CHIPS row (ward + duration) --}}
        <div class="mt-1.5 flex flex-wrap items-center gap-2.5 text-[12px]">

          @if ($wardLabel)
            <span class="inline-flex items-center rounded-full {{ $wardChip($wardLabel) }} px-2.5 py-0.5">
              <i data-lucide="hospital" class="h-3 w-3 mr-1"></i>
              {{ $wardLabel }}
            </span>
          @endif

          @if($length)
            <span class="inline-flex items-center rounded-full bg-slate-50 text-slate-700 px-2.5 py-0.5">
              <i data-lucide="clock" class="h-3 w-3 mr-1"></i>
              ~{{ $length }} mins
            </span>
          @endif

        </div>
      </div>
    </div>

    {{-- Icon --}}
    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
      <i data-lucide="user-nurse" class="h-5 w-5"></i>
    </span>
  </header>

  {{-- Meta row --}}
  <div class="mt-4 flex items-center justify-between text-[12px] text-slate-500">
    <span class="flex items-center gap-1.5">
      <i data-lucide="mic" class="h-3 w-3"></i>
      Nurse-led orientation
    </span>
    <span class="whitespace-nowrap">
      Updated {{ $o->updated_at?->diffForHumans() ?? '—' }}
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

  {{-- Bottom action bar --}}
  <div class="mt-auto pt-5 border-t border-slate-100">
    <a href="{{ route('student.wards.show', $o) }}"
       class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 py-2 text-[13px] font-medium text-slate-800 hover:bg-slate-50">
      <i data-lucide="eye" class="h-4 w-4"></i>
      Open Orientation
    </a>
  </div>
</article>

        @endforeach
      </div>

      {{-- Pager --}}
      <div id="pagerShell" class="mt-4 flex items-center justify-between text-[12px] text-slate-500">
        <div id="pagerSummary">Showing 0–0 of 0 orientations</div>
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
  lucide.createIcons();

  const debounce = (fn, ms = 350) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

  const q = document.getElementById('searchBox');
  const wardSelect = document.getElementById('wardSelect');
  const cardsGrid = document.getElementById('cardsGrid');
  const skeletonGrid = document.getElementById('skeletonGrid');
  const cards = [...document.querySelectorAll('.js-card')];

  let ward = 'all';
  const pageSize = 12;
  let currentPage = 1;

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
      const okWard = (ward === 'all') || (card.dataset.ward === ward);
      const okSearch = !needle || (card.dataset.keywords || '').includes(needle);
      return okWard && okSearch;
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
    pagerSummary.textContent = `Showing ${humanStart}–${humanEnd} of ${total} orientations`;

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
    for (let p = start; p <= end; p++) pageButtons.appendChild(makeBtn(String(p), p, currentPage === p));
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

  q?.addEventListener('input', debounce(() => { currentPage = 1; renderWithSkeleton(); }, 350));
  wardSelect?.addEventListener('change', () => { ward = wardSelect.value; currentPage = 1; renderWithSkeleton(); });

  btnPrev?.addEventListener('click', () => {
    if (currentPage > 1) { currentPage--; renderWithSkeleton(); }
  });
  btnNext?.addEventListener('click', () => {
    currentPage++; renderWithSkeleton();
  });

  document.addEventListener('DOMContentLoaded', () => { renderWithSkeleton(); });
</script>
</body>
</html>