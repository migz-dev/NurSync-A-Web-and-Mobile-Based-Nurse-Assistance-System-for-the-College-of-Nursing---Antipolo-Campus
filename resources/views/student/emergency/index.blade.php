{{-- resources/views/student/emergency/index.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Emergency Protocols · NurSync (Student)</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family:'Poppins', ui-sans-serif, system-ui, sans-serif; }

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
  @include('partials.sidebar', ['active' => 'emergency_protocols'])

  {{-- Main content --}}
  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-8 py-12 space-y-6">

      {{-- Page heading --}}
      <header class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-red-50 text-red-600">
            <i data-lucide="alert-triangle" class="h-4 w-4"></i>
          </span>
          <div>
            <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
              Emergency Protocols
            </h1>
            <p class="text-[13px] text-slate-500 mt-1">
              Quick-access algorithms for high-risk emergencies — for reference and simulation practice.
            </p>
          </div>
        </div>

        {{-- Right side: no "New Protocol" button for students --}}
        <div class="hidden sm:flex">
          <span class="inline-flex items-center rounded-xl bg-slate-900/90 px-3.5 py-2.5 text-[12px] font-medium text-white shadow-sm">
            <i data-lucide="graduation-cap" class="h-4 w-4 mr-1.5"></i>
            For learning & review only
          </span>
        </div>
      </header>

      {{-- Flash (just in case you reuse for messages) --}}
      @if (session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
          {{ session('success') }}
        </div>
      @endif

      {{-- Filters --}}
      @php
        $filters = $filters ?? [];

        $allWards = $wards ?? [
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
      @endphp

      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3">
        <form id="ep-filters-form"
              method="GET"
              action="{{ route('student.emergency.index') }}"
              class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

          {{-- Left: search + dropdowns --}}
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center w-full flex-1">

            {{-- Search --}}
            <div class="relative flex-1 sm:w-64 lg:w-80">
              <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
              <input
                id="ep-filter-q"
                type="text"
                name="q"
                value="{{ $filters['q'] ?? '' }}"
                placeholder="Search title / summary / category"
                class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300"
              >
            </div>

            {{-- Category --}}
            <select
              id="ep-filter-category"
              name="category"
              class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
              <option value="">All categories</option>
              @foreach ($categories as $c)
                <option value="{{ $c }}" @selected(($filters['category'] ?? '') == $c)>{{ $c }}</option>
              @endforeach
            </select>

            {{-- Severity --}}
            <select
              id="ep-filter-severity"
              name="severity"
              class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
              <option value="">All severities</option>
              @foreach ($severities as $sev)
                <option value="{{ $sev }}" @selected(($filters['severity'] ?? '') == $sev)>{{ $sev }}</option>
              @endforeach
            </select>

            {{-- Ward --}}
            <select
              id="ep-filter-ward"
              name="ward"
              class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
              <option value="">All wards</option>
              @foreach ($allWards as $w)
                <option value="{{ $w }}" @selected(($filters['ward'] ?? '') == $w)>{{ $w }}</option>
              @endforeach
            </select>
          </div>

          {{-- Right: per-page + Apply --}}
          <div class="flex items-center gap-3 justify-end">
            <select
              id="ep-filter-per"
              name="per"
              class="rounded-xl border-slate-200 text-sm py-2 px-3 focus:ring-2 focus:ring-slate-300">
              @foreach ([6, 12, 24, 48] as $perOpt)
                <option value="{{ $perOpt }}" @selected(($filters['per'] ?? 12) == $perOpt)>
                  {{ $perOpt }} / page
                </option>
              @endforeach
            </select>

            <button type="submit"
                    class="rounded-xl border border-slate-200 bg-white text-[13px] px-3 py-2.5 hover:bg-slate-50">
              Apply
            </button>
          </div>
        </form>

        {{-- Mobile helper pill instead of "New" button --}}
        <div class="flex sm:hidden">
          <div class="inline-flex items-center gap-2 rounded-xl bg-slate-900/90 px-3.5 py-2.5 text-[12px] font-medium text-white w-full justify-center">
            <i data-lucide="graduation-cap" class="h-4 w-4"></i>
            <span>Study reference only</span>
          </div>
        </div>
      </div>

      {{-- Cards + skeleton --}}
      <div class="bg-transparent">
        @if ($protocols->isEmpty())
          <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
            <p class="text-sm text-slate-500">
              No emergency protocols found.
              <br>
              <span class="text-[12px] text-slate-400">
                Try adjusting your filters, or ask your Clinical Instructor which protocols to review.
              </span>
            </p>
          </div>
        @else

          {{-- Skeleton grid --}}
          <div id="ep-skeletonGrid" aria-hidden="true" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
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

{{-- Cards grid (hidden until skeleton finishes) --}}
<div id="ep-cardsGrid" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3 hidden">
  @foreach ($protocols as $p)
    @php
      $sev = $p->severity ?? 'Critical';
      $sevBg = $sevText = '';
      if ($sev === 'Critical') {
          $sevBg = 'bg-red-50';    $sevText = 'text-red-700';
      } elseif ($sev === 'Moderate') {
          $sevBg = 'bg-amber-50';  $sevText = 'text-amber-700';
      } else {
          $sevBg = 'bg-sky-50';    $sevText = 'text-sky-700';
      }

      $status = $p->status ?? 'draft';
      $statusClasses = match ($status) {
          'published' => 'bg-emerald-50 text-emerald-700',
          'archived'  => 'bg-slate-100 text-slate-600',
          default     => 'bg-slate-50 text-slate-700',
      };
    @endphp

    <article
      class="js-card flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow opacity-0"
    >
      {{-- Header (large with fixed text block height) --}}
      <header class="flex items-start justify-between gap-4">
        <div class="flex-1">
          <div class="min-h-[110px] flex flex-col gap-1.5">
            <h2 class="text-[16px] font-semibold text-slate-900 leading-snug">
              {{ $p->title }}
            </h2>

            <p class="text-[13px] text-slate-600 line-clamp-3">
              {{ $p->summary ?? 'No summary provided.' }}
            </p>

            <div class="mt-1.5 flex flex-wrap items-center gap-2.5 text-[12px]">
              @if ($p->category)
                <span class="inline-flex items-center rounded-full bg-slate-50 border border-slate-200 px-2.5 py-0.5 text-slate-700">
                  <i data-lucide="layers" class="h-3 w-3 mr-1"></i>
                  {{ $p->category }}
                </span>
              @endif

              @if ($p->ward)
                <span class="inline-flex items-center rounded-full bg-sky-50 border border-sky-100 px-2.5 py-0.5 text-sky-700">
                  <i data-lucide="hospital" class="h-3 w-3 mr-1"></i>
                  {{ $p->ward }}
                </span>
              @endif

              <span class="inline-flex items-center rounded-full {{ $sevBg }} px-2.5 py-0.5 text-[11px] font-medium {{ $sevText }}">
                <i data-lucide="activity" class="h-3 w-3 mr-1"></i>
                {{ $p->severity ?? 'Critical' }}
              </span>

              <span class="inline-flex items-center rounded-full {{ $statusClasses }} px-2.5 py-0.5 text-[11px] font-medium">
                {{ ucfirst($status) }}
              </span>
            </div>
          </div>
        </div>

        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-600">
          <i data-lucide="alert-triangle" class="h-5 w-5"></i>
        </span>
      </header>

      {{-- Meta row --}}
      <div class="mt-4 flex items-center justify-between text-[12px] text-slate-500">
        <span class="flex items-center gap-1.5">
          <i data-lucide="eye" class="h-3 w-3"></i>
          <span>{{ $p->view_count }} views</span>
        </span>
        <span class="whitespace-nowrap">
          Updated {{ $p->updated_at?->diffForHumans() ?? '—' }}
        </span>
      </div>

      {{-- Student action: open guide only --}}
      <div class="mt-auto pt-5 border-t border-slate-100 flex items-center justify-between gap-2.5">
        <a href="{{ route('student.emergency.show', $p->slug) }}"
           class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 py-2 text-[13px] font-medium text-slate-800 hover:bg-slate-50">
          <i data-lucide="eye" class="h-4 w-4"></i>
          Open Guide
        </a>

        <span class="inline-flex items-center gap-1 text-[11px] text-slate-500">
          <i data-lucide="stethoscope" class="h-3 w-3"></i>
          Practice with your CI
        </span>
      </div>
    </article>
  @endforeach
</div>


          {{-- Pager --}}
          <div class="mt-4 flex items-center justify-between text-[12px] text-slate-500" id="ep-pager">
            <div>
              Showing
              <span class="font-medium">{{ $protocols->firstItem() }}</span>
              –
              <span class="font-medium">{{ $protocols->lastItem() }}</span>
              of
              <span class="font-medium">{{ $protocols->total() }}</span>
              protocols
            </div>
            <div class="flex items-center gap-1">
              {{ $protocols->onEachSide(1)->links() }}
            </div>
          </div>
        @endif
      </div>
    </div>
  </section>
</main>

@include('partials.student-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();

  const skeletonGrid = document.getElementById('ep-skeletonGrid');
  const cardsGrid    = document.getElementById('ep-cardsGrid');

  function showSkeleton(show) {
    if (!skeletonGrid || !cardsGrid) return;
    skeletonGrid.classList.toggle('hidden', !show);
    cardsGrid.classList.toggle('hidden', show);
  }

  function animateCards() {
    const cards = [...document.querySelectorAll('#ep-cardsGrid .js-card')];
    cards.forEach((el, idx) => {
      el.style.animationDelay = `${Math.min(idx, 6) * 40}ms`;
      el.classList.remove('opacity-0');
      el.classList.add('animate-card-in');
    });
  }

  function runInitialAnimation() {
    if (!skeletonGrid || !cardsGrid) return;
    showSkeleton(true);
    const delay = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 220;
    setTimeout(() => {
      showSkeleton(false);
      requestAnimationFrame(animateCards);
    }, delay);
  }

  const epDebounce = (fn, ms = 400) => {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...args), ms);
    };
  };

  document.addEventListener('DOMContentLoaded', () => {
    runInitialAnimation();

    const form = document.getElementById('ep-filters-form');
    if (!form) return;

    const autoSubmit = () => form.submit();

    const q        = document.getElementById('ep-filter-q');
    const category = document.getElementById('ep-filter-category');
    const severity = document.getElementById('ep-filter-severity');
    const ward     = document.getElementById('ep-filter-ward');
    const per      = document.getElementById('ep-filter-per');

    if (q) {
      q.addEventListener('input', epDebounce(autoSubmit, 400));
    }
    [category, severity, ward, per].forEach(el => {
      if (!el) return;
      el.addEventListener('change', autoSubmit);
    });
  });
</script>

</body>
</html>
