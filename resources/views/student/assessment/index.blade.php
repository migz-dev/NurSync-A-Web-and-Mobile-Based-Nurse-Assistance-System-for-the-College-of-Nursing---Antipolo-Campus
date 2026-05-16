{{-- resources/views/student/assessment/index.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Assessment Guides · NurSync</title>

  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body { font-family:'Poppins', ui-sans-serif, system-ui, sans-serif; }

    @keyframes slide-in-up {
      from { transform: translateY(10px); opacity:0; }
      to   { transform: translateY(0);   opacity:1; }
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
  @include('partials.sidebar', ['active' => 'assessment_guides'])

  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-6 lg:px-10 py-10 space-y-6">

      {{-- Header --}}
      <header class="space-y-1">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
            <i data-lucide="clipboard-list" class="h-5 w-5"></i>
          </span>
          <div>
            <h1 class="text-[22px] sm:text-[24px] font-extrabold tracking-tight text-slate-900">
              Assessment Guides
            </h1>
            <p class="text-[13px] text-slate-500 mt-1">
              Learn how real nurses are evaluated in the field, how to write DAR / SOAP / PIE properly,
              and what safe vs unsafe practice looks like.
            </p>
          </div>
        </div>
      </header>

      @php
        $guides  = $guides ?? collect();
        $filters = $filters ?? ['q' => ''];
        $currentSearch = $filters['q'] ?? '';

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
        $tagClass = function (string $t) use ($tagPalette) {
          $i = abs(crc32(mb_strtolower($t))) % count($tagPalette);
          return $tagPalette[$i];
        };
      @endphp

      {{-- Search bar --}}
      <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4">
        <div class="relative max-w-xl">
          <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
          <input id="searchBox" type="text"
                 placeholder="Search guides by title, rubric, or documentation tips…"
                 value="{{ $currentSearch }}"
                 class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" />
        </div>
      </div>

      {{-- Empty state --}}
      @if ($guides->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
          <p class="text-sm text-slate-500">
            No assessment guides are available yet. Your clinical instructors will publish guides here soon.
          </p>
        </div>
      @else

      {{-- Skeleton loading --}}
      <div id="skeletonGrid" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" aria-hidden="true">
        @for ($i = 0; $i < 9; $i++)
          <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="animate-pulse space-y-3">
              <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                  <span class="h-8 w-8 rounded-xl bg-slate-200"></span>
                  <div class="space-y-2">
                    <div class="h-3 w-40 bg-slate-200 rounded"></div>
                    <div class="h-3 w-24 bg-slate-100 rounded"></div>
                  </div>
                </div>
                <span class="h-6 w-16 rounded-full bg-slate-100"></span>
              </div>
              <div class="h-3 w-full bg-slate-100 rounded"></div>
              <div class="h-3 w-5/6 bg-slate-100 rounded"></div>
              <div class="flex gap-2 pt-1">
                <span class="h-5 w-20 rounded-full bg-slate-100"></span>
                <span class="h-5 w-16 rounded-full bg-slate-100"></span>
              </div>
              <div class="flex gap-2 pt-2">
                <span class="h-8 w-24 rounded-xl bg-slate-100"></span>
              </div>
            </div>
          </div>
        @endfor
      </div>

      {{-- Cards --}}
      <div id="cardsGrid" class="hidden grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach($guides as $g)
          @php
            $themes = [];
            if (!empty($g->content_rubric))        $themes[] = 'How nurses are evaluated';
            if (!empty($g->content_documentation)) $themes[] = 'DAR / SOAP / PIE';
            if (!empty($g->content_tips))          $themes[] = 'Tips from practice';
            if (!empty($g->content_mistakes))      $themes[] = 'Common mistakes';

            $keywords = \Illuminate\Support\Str::of(
                ($g->title ?? '') . ' ' .
                ($g->summary ?? '') . ' ' .
                ($g->content_rubric ?? '') . ' ' .
                ($g->content_documentation ?? '') . ' ' .
                ($g->content_tips ?? '') . ' ' .
                ($g->content_mistakes ?? '')
            )->lower();
          @endphp

<article
  class="js-card flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow opacity-0"
  data-keywords="{{ $keywords }}"
>
  {{-- Header (large, with fixed title+summary height so tag row aligns) --}}
  <header class="flex items-start justify-between gap-4">
    <div class="flex-1">
      {{-- Fixed-height title + summary block --}}
      <div class="min-h-[90px] flex flex-col gap-1.5">
        <h2 class="text-[16px] font-semibold text-slate-900 leading-snug">
          {{ $g->title }}
        </h2>
        <p class="text-[13px] text-slate-600 line-clamp-3">
          {{ $g->summary ?: 'Understand what competent nursing practice looks like during real clinical duties.' }}
        </p>

        {{-- Chips row: “Published” + tags, aligned across cards --}}
        <div class="mt-1.5 flex flex-wrap items-center gap-2.5 text-[12px]">
          <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-0.5">
            <i data-lucide="check-circle-2" class="h-3 w-3"></i>
            Published guide
          </span>

          @if(!empty($g->tags))
            @foreach($g->tags as $t)
              <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 {{ $tagClass($t) }}">
                {{ $t }}
              </span>
            @endforeach
          @endif
        </div>
      </div>
    </div>

    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
      <i data-lucide="user-nurse" class="h-5 w-5"></i>
    </span>
  </header>

  {{-- Meta row (views / updated) --}}
  <div class="mt-4 flex items-center justify-between text-[12px] text-slate-500">
    <span class="inline-flex items-center gap-1.5">
      <i data-lucide="stethoscope" class="h-3 w-3"></i>
      CI evaluation perspective
    </span>
    <span class="whitespace-nowrap">
      Updated {{ $g->updated_at?->diffForHumans() ?? '—' }}
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

  {{-- Action bar pinned to bottom --}}
  <div class="mt-auto pt-5 border-t border-slate-100">
    <a href="{{ route('student.assessment.show', $g) }}"
       class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 py-2 text-[13px] font-medium text-slate-800 hover:bg-slate-50">
      <i data-lucide="eye" class="h-4 w-4"></i>
      Open Guide
    </a>
  </div>
</article>


        @endforeach
      </div>

      @endif
    </div>
  </section>
</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();

  const debounce = (fn, ms = 350) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

  const q           = document.getElementById('searchBox');
  const cardsGrid   = document.getElementById('cardsGrid');
  const skeletonGrid= document.getElementById('skeletonGrid');
  const cards       = [...document.querySelectorAll('.js-card')];

  const pageSize    = 12;
  let currentPage   = 1;

  function showSkeleton(show) {
    if (!skeletonGrid || !cardsGrid) return;
    skeletonGrid.classList.toggle('hidden', !show);
    cardsGrid.classList.toggle('hidden', show);
  }

  function getFilteredCards() {
    const needle = (q?.value || '').toLowerCase().trim();
    return cards.filter(card => {
      const okSearch = !needle || (card.dataset.keywords || '').includes(needle);
      return okSearch;
    });
  }

  function animateVisibleSlice(slice) {
    slice.forEach((el, idx) => {
      el.style.animationDelay = `${Math.min(idx, 6) * 40}ms`;
      el.classList.remove('opacity-0');
      el.classList.add('animate-card-in');
    });
  }

  function renderPage() {
    const filtered = getFilteredCards();
    const total    = filtered.length;
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

    requestAnimationFrame(() => animateVisibleSlice(slice));
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

  document.addEventListener('DOMContentLoaded', () => {
    renderWithSkeleton();
  });
</script>
</body>
</html>
