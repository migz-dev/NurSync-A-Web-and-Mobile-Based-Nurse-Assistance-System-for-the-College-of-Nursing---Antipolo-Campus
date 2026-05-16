{{-- resources/views/faculty/instructor/skills/index.blade.php --}}
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Skill Mastery Checklists · NurSync (CI)</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }

    /* Smooth card entrance after skeleton hides */
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
    @include('partials.instructor-sidebar', ['active' => 'skill_mastery'])

    {{-- Main content --}}
    <section class="flex-1 min-w-0">
      <div class="container mx-auto px-8 py-12 space-y-6">

        {{-- Page heading --}}
        <header class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
              <i data-lucide="check-square" class="h-4 w-4"></i>
            </span>
            <div>
              <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
                Skill Mastery Checklists (CI)
              </h1>
              <p class="text-[13px] text-slate-500 mt-1">
                Curate “how nurses really do it” step-by-step, with safety notes, equipment, and teaching points for
                your students.
              </p>
            </div>
          </div>

          {{-- New Checklist (desktop) --}}
          <div class="hidden sm:flex">
            <a href="{{ route('faculty.instructor.skills.create') }}"
              class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-3.5 py-2.5 text-[13px] font-medium text-white shadow-sm hover:bg-emerald-700">
              <i data-lucide="plus" class="h-4 w-4"></i>
              <span>New Checklist</span>
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
          /** @var \Illuminate\Support\Collection|\App\Models\SkillMasteryChecklist[] $checklists */
          $checklists = $checklists ?? collect();

          // 🚫 Never show archived here
          $list = $checklists->reject(fn($c) => ($c->status ?? 'draft') === 'archived');

          // Areas (can mirror wards; using skill_area)
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

          // Area badge color (reuse ward colors)
          $areaChip = function (?string $area) {
            return match ($area) {
              'Community Health (CHN)' => 'bg-emerald-50 text-emerald-700',
              'OB Ward', 'Delivery Room (DR)', 'Nursery' => 'bg-pink-50 text-pink-700',
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
          $tagClass = function (string $t) use ($tagPalette) {
            $i = abs(crc32(mb_strtolower($t))) % count($tagPalette);
            return $tagPalette[$i];
          };

          // Icon guesser based on skill title
          $pickIcon = function ($c) {
            $t = strtolower(($c->slug ?? '') . ' ' . ($c->title ?? '') . ' ' . ($c->category ?? ''));
            return str_contains($t, 'iv') || str_contains($t, 'cannula') || str_contains($t, 'venipuncture') ? 'syringe'
              : (str_contains($t, 'wound') || str_contains($t, 'dressing') ? 'bandage'
                : (str_contains($t, 'catheter') || str_contains($t, 'foley') ? 'activity'
                  : (str_contains($t, 'tube') || str_contains($t, 'feeding') ? 'utensils-crossed'
                    : (str_contains($t, 'vital') || str_contains($t, 'bp') || str_contains($t, 'temperature') ? 'activity'
                      : (str_contains($t, 'med') || str_contains($t, 'drug') ? 'pill'
                        : 'check-square')))));
          };
        @endphp

        {{-- Filters --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center w-full flex-1">
              {{-- Search --}}
              <div class="relative flex-1 sm:w-64 lg:w-80">
                <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
                <input id="searchBox" type="text" placeholder="Search skills by name, category, steps, tags, or area…"
                  class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" />
              </div>
              {{-- Area (ward) --}}
              <select id="wardSelect"
                class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                <option value="all">All areas</option>
                @foreach($areas as $a)
                  <option value="{{ $a }}">{{ $a }}</option>
                @endforeach
              </select>
            </div>

            {{-- Mobile New button --}}
            <div class="flex sm:hidden">
              <a href="{{ route('faculty.instructor.skills.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-3.5 py-2.5 text-[13px] font-medium text-white shadow-sm hover:bg-emerald-700 w-full justify-center">
                <i data-lucide="plus" class="h-4 w-4"></i>
                <span>New Checklist</span>
              </a>
            </div>
          </div>
        </div>

        {{-- Empty state --}}
        @if($list->isEmpty())
          <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
            <p class="text-sm text-slate-500">
              No skill mastery checklists yet. Click <span class="font-semibold">New Checklist</span> to create your first
              one.
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
            @foreach($list as $c)
              @php
                $tags = $c->relationLoaded('tags')
                  ? $c->tags->pluck('name')->all()
                  : (array) ($c->tags_json ?? []); // fallback if ever you add tags_json

                $status = $c->status ?? 'draft';
                $statusClasses = $status === 'published'
                  ? 'bg-emerald-50 text-emerald-700'
                  : 'bg-slate-50 text-slate-700';
                $statusIcon = $status === 'published' ? 'lock' : 'lock-open';
                $statusLabel = ucfirst($status);

                $icon = $pickIcon($c);
                $area = $c->skill_area ?? '';
                $category = $c->category ?? 'General Skill';

                $stepsCount = $c->relationLoaded('steps') ? $c->steps->count() : null;
                $equipmentCount = $c->relationLoaded('equipment') ? $c->equipment->count() : null;

                $keywords = \Illuminate\Support\Str::of(
                  ($c->title . ' ' . ($c->summary ?? '') . ' ' . ($category ?? '') . ' ' . implode(' ', $tags) . ' ' . ($c->safety_notes ?? '') . ' ' . $area)
                )->lower();
              @endphp

              <article
                class="js-card flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-shadow opacity-0"
                data-ward="{{ $area }}" data-has-pdf="0" data-keywords="{{ $keywords }}">
                {{-- Header (large, fixed text block so chips align) --}}
                <header class="flex items-start justify-between gap-4">
                  <div class="flex-1">
                    {{-- Fixed-height title + summary + chip row --}}
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
                      @endif

                      {{-- Chip row (area / category / status) --}}
                      <div class="mt-1.5 flex flex-wrap items-center gap-2.5 text-[12px]">
                        @if ($area)
                          <span
                            class="inline-flex items-center rounded-full {{ $areaChip($area) }} px-2.5 py-0.5 text-[11px]">
                            <i data-lucide="map-pin" class="h-3 w-3 mr-1"></i>
                            {{ $area }}
                          </span>
                        @endif

                        <span
                          class="inline-flex items-center rounded-full bg-slate-50 text-slate-700 border border-slate-200 px-2.5 py-0.5 text-[11px]">
                          <i data-lucide="layers" class="h-3 w-3 mr-1"></i>
                          {{ $category }}
                        </span>

                        <span
                          class="inline-flex items-center rounded-full {{ $statusClasses }} px-2.5 py-0.5 text-[11px] font-medium">
                          <i data-lucide="{{ $statusIcon }}" class="h-3 w-3 mr-1"></i>
                          {{ $statusLabel }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <i data-lucide="check-square" class="h-5 w-5"></i>
                  </span>
                </header>

                {{-- Meta row --}}
                <div class="mt-4 flex items-center justify-between text-[12px] text-slate-500">
                  <span class="flex flex-wrap items-center gap-2">
                    @if(!is_null($stepsCount))
                      <span class="inline-flex items-center gap-1.5">
                        <i data-lucide="list-ordered" class="h-3 w-3"></i>
                        {{ $stepsCount }} steps
                      </span>
                    @endif

                    @if(!is_null($equipmentCount))
                      <span class="inline-flex items-center gap-1.5">
                        <i data-lucide="stethoscope" class="h-3 w-3"></i>
                        {{ $equipmentCount }} equipment
                      </span>
                    @endif

                    <span class="inline-flex items-center gap-1.5">
                      <i data-lucide="{{ $statusIcon }}" class="h-3 w-3"></i>
                      {{ $statusLabel }}
                    </span>
                  </span>

                  <span class="whitespace-nowrap">
                    Updated {{ $c->updated_at?->diffForHumans() ?? '—' }}
                  </span>
                </div>

                {{-- Tags --}}
                @if(!empty($tags))
                  <div class="mt-3 flex flex-wrap items-center gap-2 text-[12px]">
                    @foreach($tags as $t)
                      <span class="rounded-full border px-2.5 py-0.5 {{ $tagClass($t) }}">
                        {{ $t }}
                      </span>
                    @endforeach
                  </div>
                @endif

                {{-- Actions (sticky bottom) --}}
                <div class="mt-auto pt-5 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
                  <a href="{{ route('faculty.instructor.skills.show', $c->slug) }}"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 py-2 text-[13px] font-medium text-slate-800 hover:bg-slate-50">
                    <i data-lucide="eye" class="h-4 w-4"></i>
                    Open Checklist
                  </a>

                  <a href="{{ route('faculty.instructor.skills.edit', $c->slug) }}"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-amber-200 px-3.5 py-2 text-[13px] font-medium text-amber-800 hover:bg-amber-50">
                    <i data-lucide="edit-3" class="h-4 w-4"></i>
                    Edit
                  </a>
                </div>
              </article>

            @endforeach
          </div>

          {{-- Pager (client-side) --}}
          <div id="pagerShell" class="mt-4 flex items-center justify-between text-[12px] text-slate-500">
            <div id="pagerSummary">Showing 0–0 of 0 skills</div>
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

  @includeIf('partials.faculty-footer')
  @includeWhen(!View::exists('partials.faculty-footer'), 'partials.student-footer')

  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    // Icons
    lucide.createIcons();

    // Debounce
    const debounce = (fn, ms = 350) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

    // Elements
    const q = document.getElementById('searchBox');
    const wardSelect = document.getElementById('wardSelect'); // used as "area" selector
    const cardsGrid = document.getElementById('cardsGrid');
    const skeletonGrid = document.getElementById('skeletonGrid');
    const cards = [...document.querySelectorAll('.js-card')];

    // State
    let ward = 'all', pdfOnly = false;
    const pageSize = 12;
    let currentPage = 1;

    // Pager
    const pagerShell = document.getElementById('pagerShell');
    const pagerSummary = document.getElementById('pagerSummary');
    const pageButtons = document.getElementById('pageButtons');
    const btnPrev = document.getElementById('btnPrev');
    const btnNext = document.getElementById('btnNext');

    // Helpers
    function showSkeleton(show) {
      if (!skeletonGrid || !cardsGrid) return;
      skeletonGrid.classList.toggle('hidden', !show);
      cardsGrid.classList.toggle('hidden', show);
    }

    function getFilteredCards() {
      const needle = (q?.value || '').toLowerCase().trim();
      return cards.filter(card => {
        const okWard = (ward === 'all') || (card.dataset.ward === ward);
        const okPdf = !pdfOnly || card.dataset.hasPdf === '1';
        const okSearch = !needle || (card.dataset.keywords || '').includes(needle);
        return okWard && okPdf && okSearch;
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
      const filtered = getFilteredCards();
      const total = filtered.length;
      const totalPages = Math.max(1, Math.ceil(total / pageSize));
      if (currentPage > totalPages) currentPage = totalPages;

      // Hide all
      cards.forEach(c => { c.style.display = 'none'; });

      // Current slice
      const startIdx = (currentPage - 1) * pageSize;
      const endIdx = Math.min(startIdx + pageSize, total);
      const slice = [];
      for (let i = startIdx; i < endIdx; i++) {
        filtered[i].style.display = '';
        filtered[i].classList.remove('animate-card-in');
        filtered[i].classList.add('opacity-0');
        slice.push(filtered[i]);
      }

      // Summary & controls
      const humanStart = total === 0 ? 0 : startIdx + 1;
      const humanEnd = endIdx;
      pagerSummary.textContent = `Showing ${humanStart}–${humanEnd} of ${total} skills`;

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
        if (start > 2) pageButtons.appendChild(Object.assign(document.createElement('span'), {
          textContent: '…',
          className: 'px-1 text-slate-400 select-none'
        }));
      }
      for (let p = start; p <= end; p++) pageButtons.appendChild(makeBtn(String(p), p, currentPage === p));
      if (end < totalPages) {
        if (end < totalPages - 1) pageButtons.appendChild(Object.assign(document.createElement('span'), {
          textContent: '…',
          className: 'px-1 text-slate-400 select-none'
        }));
        pageButtons.appendChild(makeBtn(String(totalPages), totalPages, currentPage === totalPages));
      }
    }

    function scrollPagerIntoView() {
      const rect = pagerShell.getBoundingClientRect();
      const vh = window.innerHeight || document.documentElement.clientHeight;
      const fullyVisible = rect.top >= 0 && rect.bottom <= vh;
      if (!fullyVisible) pagerShell.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Render with skeleton for smoothness
    function renderWithSkeleton() {
      showSkeleton(true);
      const delay = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 220;
      setTimeout(() => { renderPage(); showSkeleton(false); }, delay);
    }

    // Bindings
    q?.addEventListener('input', debounce(() => { currentPage = 1; renderWithSkeleton(); }, 350));
    wardSelect?.addEventListener('change', () => {
      ward = wardSelect.value;
      currentPage = 1;
      renderWithSkeleton();
    });
    document.getElementById('btnPrev')?.addEventListener('click', () => {
      if (currentPage > 1) { currentPage--; renderWithSkeleton(); scrollPagerIntoView(); }
    });
    document.getElementById('btnNext')?.addEventListener('click', () => {
      currentPage++; renderWithSkeleton(); scrollPagerIntoView();
    });

    // First paint
    document.addEventListener('DOMContentLoaded', () => { renderWithSkeleton(); });
  </script>
</body>

</html>