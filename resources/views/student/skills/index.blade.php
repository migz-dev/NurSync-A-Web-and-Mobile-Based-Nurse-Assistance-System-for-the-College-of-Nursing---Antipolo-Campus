<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Skill Mastery Checklists · NurSync</title>

  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; }

    /* Smooth card entrance after skeleton hides (same as CI Procedures) */
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
  @include('partials.sidebar', ['active' => 'skill_checklists'])

  {{-- Main content --}}
  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-8 py-12 space-y-6">

      {{-- Page heading --}}
      <header class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
            <i data-lucide="check-circle-2" class="h-4 w-4"></i>
          </span>
          <div>
            <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
              Skill Mastery Checklists
            </h1>
            <p class="text-[13px] text-slate-500 mt-1">
              View how real nurses perform core clinical skills step-by-step. All checklists are read-only — like shadowing at the bedside.
            </p>
          </div>
        </div>
      </header>

      @php
        /** @var \Illuminate\Support\Collection|\App\Models\SkillMasteryChecklist[] $checklists */
        $checklists = $checklists ?? collect();

        // Only published should come from controller, but just in case:
        $list = $checklists->filter(fn($c) => ($c->status ?? 'draft') === 'published');

        // Wards (same palette as CI)
        $wards = [
          'Community Health (CHN)','OB Ward','Delivery Room (DR)','Nursery','Pediatrics (PEDIA)',
          'Medical-Surgical (MS)','ICU','Oncology','Isolation Unit','Endocrine Unit','Neurology Unit',
          'Psychiatric (PSYCH)','Emergency Room (ER)','Operating Room (OR)','Trauma Unit','Disaster Response / Community Field',
        ];

        // Dynamic categories from data (fallback if empty)
        $categories = $list->pluck('category')->filter()->unique()->values()->all();
        if (empty($categories)) {
            $categories = [
                'Vital Signs',
                'Medication Administration',
                'IV Therapy',
                'Wound Care',
                'Catheterization',
                'Tube Feeding',
                'OR / DR Skills',
                'ICU Routines',
                'Emergency / ER Skills',
                'Assessment & Monitoring',
                'Other',
            ];
        }

        // Ward badge color
        $wardChip = function (?string $ward) {
          return match ($ward) {
            'Community Health (CHN)' => 'bg-emerald-50 text-emerald-700',
            'OB Ward','Delivery Room (DR)','Nursery' => 'bg-pink-50 text-pink-700',
            'Pediatrics (PEDIA)' => 'bg-sky-50 text-sky-700',
            'Medical-Surgical (MS)' => 'bg-slate-50 text-slate-700',
            'ICU' => 'bg-rose-50 text-rose-700',
            'Oncology' => 'bg-fuchsia-50 text-fuchsia-700',
            'Isolation Unit' => 'bg-amber-50 text-amber-700',
            'Endocrine Unit' => 'bg-cyan-50 text-cyan-700',
            'Neurology Unit' => 'bg-indigo-50 text-indigo-700',
            'Psychiatric (PSYCH)' => 'bg-violet-50 text-violet-700',
            'Emergency Room (ER)' => 'bg-orange-50 text-orange-700',
            'Operating Room (OR)' => 'bg-green-50 text-green-700',
            'Trauma Unit' => 'bg-red-50 text-red-700',
            'Disaster Response / Community Field' => 'bg-yellow-50 text-yellow-700',
            default => 'bg-slate-50 text-slate-700',
          };
        };

        // Colorful tag pills (stable hashing → consistent color per tag)
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

        // Icon guesser based on category/title
        $pickIcon = function($c) {
          $t = strtolower(($c->category ?? '') . ' ' . ($c->title ?? ''));
          return str_contains($t,'vital')      ? 'activity'
               : (str_contains($t,'med') ||
                  str_contains($t,'drug') ||
                  str_contains($t,'iv')   ? 'pill'
               : (str_contains($t,'wound') ||
                  str_contains($t,'dressing') ? 'bandage'
               : (str_contains($t,'catheter') ? 'droplets'
               : (str_contains($t,'tube')     ? 'tube'
               : (str_contains($t,'or') ||
                  str_contains($t,'dr')       ? 'scalpel'
               : 'stethoscope')))));
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
                     placeholder="Search skills by name, category, ward, or safety notes…"
                     class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" />
            </div>

            {{-- Category --}}
            <select id="categorySelect"
                    class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
              <option value="all">All categories</option>
              @foreach($categories as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
              @endforeach
            </select>

            {{-- Ward --}}
            <select id="wardSelect"
                    class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
              <option value="all">All wards</option>
              @foreach($wards as $w)
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
            No published skill mastery checklists are available yet.
            Once your Clinical Instructors publish their checklists, they will appear here for viewing.
          </p>
        </div>
      @else

      {{-- Skeleton grid (loading shimmer) --}}
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
              </div>
            </div>
          </div>
        @endfor
      </div>

      {{-- Cards grid --}}
      <div id="cardsGrid" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3 hidden">
        @foreach($list as $c)
          @php
            $tags      = $c->relationLoaded('tags') ? (array) $c->tags->pluck('name')->all() : [];
            $ward      = $c->skill_area ?? '';
            $category  = $c->category ?? 'General Skill';
            $stepsCount = $c->relationLoaded('steps') ? $c->steps->count() : ($c->steps_count ?? 0);
            $equipCount = $c->relationLoaded('equipment') ? $c->equipment->count() : ($c->equipment_count ?? 0);

            $icon  = $pickIcon($c);

            // keywords for search
            $keywords = \Illuminate\Support\Str::of(
              ($c->title.' '.($c->summary ?? '').' '.($c->pre_procedure ?? '').' '.($c->post_procedure ?? '').' '.($c->safety_notes ?? '').' '.$ward.' '.$category.' '.implode(' ', $tags))
            )->lower();
          @endphp

<article
  class="js-card flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow opacity-0"
  data-ward="{{ $ward }}"
  data-category="{{ $category }}"
  data-keywords="{{ $keywords }}"
>
  {{-- Header (large, with fixed-height text block so chips align) --}}
  <header class="flex items-start justify-between gap-4">
    <div class="flex-1">
      {{-- Fixed-height title + summary + primary chips --}}
      <div class="min-h-[90px] flex flex-col gap-1.5">
        <h2 class="text-[16px] font-semibold text-slate-900 leading-snug flex items-center gap-3">
          <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50">
            <i data-lucide="{{ $icon }}" class="h-5 w-5 text-slate-700"></i>
          </span>
          {{ $c->title }}
        </h2>

        @if(!empty($c->summary))
          <p class="text-[13px] text-slate-600 line-clamp-3">
            {{ $c->summary }}
          </p>
        @else
          <p class="text-[13px] text-slate-600 line-clamp-3">
            A skill checklist to guide safe and competent performance of this procedure.
          </p>
        @endif

        {{-- Primary chips (category / ward / published) --}}
        <div class="mt-1.5 flex flex-wrap items-center gap-2.5 text-[12px]">
          {{-- Category pill --}}
          <span class="inline-flex items-center rounded-full bg-slate-50 text-slate-700 border border-slate-200 px-2.5 py-0.5">
            <i data-lucide="layers" class="h-3 w-3 mr-1"></i>
            {{ $category }}
          </span>

          {{-- Ward chip --}}
          @if($ward)
            <span class="inline-flex items-center rounded-full {{ $wardChip($ward) }} px-2.5 py-0.5">
              <i data-lucide="hospital" class="h-3 w-3 mr-1"></i>
              {{ $ward }}
            </span>
          @endif

          {{-- Always published for students --}}
          <span class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 px-2.5 py-0.5 font-medium border border-emerald-200">
            <i data-lucide="lock" class="h-3 w-3 mr-1"></i>
            Published · View-only
          </span>
        </div>
      </div>
    </div>

    {{-- Icon bubble --}}
    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
      <i data-lucide="check-circle-2" class="h-5 w-5"></i>
    </span>
  </header>

  {{-- Meta row (steps, equipment, updated) --}}
  <div class="mt-4 flex items-center justify-between text-[12px] text-slate-500">
    <span class="flex flex-wrap items-center gap-3">
      <span class="inline-flex items-center gap-1.5">
        <i data-lucide="list-ordered" class="h-3 w-3"></i>
        {{ $stepsCount }} steps
      </span>
      <span class="inline-flex items-center gap-1.5">
        <i data-lucide="toolbox" class="h-3 w-3"></i>
        {{ $equipCount }} equipment
      </span>
    </span>
    <span class="whitespace-nowrap">
      Updated {{ $c->updated_at?->diffForHumans() ?? '—' }}
    </span>
  </div>

  {{-- Tag chips row (secondary themes) --}}
  @if(!empty($tags))
    <div class="mt-3 flex flex-wrap items-center gap-2 text-[12px]">
      @foreach($tags as $t)
        <span class="rounded-full border px-2.5 py-0.5 {{ $tagClass($t) }}">
          {{ $t }}
        </span>
      @endforeach
    </div>
  @endif

  {{-- Sticky bottom action bar --}}
  <div class="mt-auto pt-5 border-t border-slate-100">
    <a href="{{ route('student.skills.show', $c->slug) }}"
       class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 py-2 text-[13px] font-medium text-slate-800 hover:bg-slate-50">
      <i data-lucide="eye" class="h-4 w-4"></i>
      Open Checklist
    </a>
  </div>
</article>

        @endforeach
      </div>

      {{-- Pager (client-side) --}}
      <div id="pagerShell" class="mt-4 flex items-center justify-between text-[12px] text-slate-500">
        <div id="pagerSummary">Showing 0–0 of 0 checklists</div>
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

    </div>
  </section>
</main>

@includeIf('partials.student-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script>
  // Icons
  lucide.createIcons();

  // Debounce helper
  const debounce = (fn, ms = 350) => {
    let t;
    return (...a) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...a), ms);
    };
  };

  // Elements
  const q             = document.getElementById('searchBox');
  const wardSelect    = document.getElementById('wardSelect');
  const categorySelect= document.getElementById('categorySelect');
  const cardsGrid     = document.getElementById('cardsGrid');
  const skeletonGrid  = document.getElementById('skeletonGrid');
  const cards         = [...document.querySelectorAll('.js-card')];

  // State
  let ward      = 'all';
  let category  = 'all';
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
    const needle = (q?.value || '').toLowerCase().trim();
    return cards.filter(card => {
      const okWard = (ward === 'all') || (card.dataset.ward === ward);
      const okCategory = (category === 'all') || (card.dataset.category === category);
      const okSearch = !needle || (card.dataset.keywords || '').includes(needle);
      return okWard && okCategory && okSearch;
    });
  }

  // Animate visible slice with gentle stagger
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

    // Hide all
    cards.forEach(c => {
      c.style.display = 'none';
    });

    // Current slice
    const startIdx = (currentPage - 1) * pageSize;
    const endIdx   = Math.min(startIdx + pageSize, total);
    const slice    = [];

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
    pagerSummary.textContent = `Showing ${humanStart}–${humanEnd} of ${total} checklists`;

    btnPrev.disabled = (currentPage <= 1);
    btnNext.disabled = (currentPage >= totalPages);
    buildPageButtons(totalPages);
    pagerShell.style.display = totalPages <= 1 ? 'none' : '';

    // Play entrance animation
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
      btn.type  = 'button';
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
        span.className   = 'px-1 text-slate-400 select-none';
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
        span.className   = 'px-1 text-slate-400 select-none';
        pageButtons.appendChild(span);
      }
      pageButtons.appendChild(makeBtn(String(totalPages), totalPages, currentPage === totalPages));
    }
  }

  function scrollPagerIntoView() {
    const rect = pagerShell.getBoundingClientRect();
    const vh   = window.innerHeight || document.documentElement.clientHeight;
    const fullyVisible = rect.top >= 0 && rect.bottom <= vh;
    if (!fullyVisible) {
      pagerShell.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
  }

  // Render with skeleton for smoothness
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

  wardSelect?.addEventListener('change', () => {
    ward = wardSelect.value;
    currentPage = 1;
    renderWithSkeleton();
  });

  categorySelect?.addEventListener('change', () => {
    category = categorySelect.value;
    currentPage = 1;
    renderWithSkeleton();
  });

  document.getElementById('btnPrev')?.addEventListener('click', () => {
    if (currentPage > 1) {
      currentPage--;
      renderWithSkeleton();
      scrollPagerIntoView();
    }
  });

  document.getElementById('btnNext')?.addEventListener('click', () => {
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
