<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>{{ $orientation->title ?? 'Ward Orientation' }} · NurSync (Student)</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style> body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; } </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  @include('partials.sidebar', ['active' => 'ward_orientation'])

  <section class="flex-1">
    <div class="container mx-auto px-8 py-10 space-y-6">
      <header class="flex items-start justify-between gap-4">
        <div>
          <button onclick="history.back()" type="button"
                  class="inline-flex items-center gap-2 text-[12px] text-slate-500 hover:text-slate-700 mb-2">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            Back to Ward Orientation
          </button>

          <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 flex items-center gap-2">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
              <i data-lucide="map" class="h-5 w-5"></i>
            </span>
            {{ $orientation->title ?: ($orientation->ward_label . ' · Ward Orientation') }}
          </h1>

          <p class="mt-2 text-sm text-slate-600">
            {{ $orientation->summary ?? "Student nurse–friendly orientation for the {$orientation->ward_label}." }}
          </p>

          <div class="mt-3 flex flex-wrap items-center gap-2 text-[11px]">
            @if($orientation->ward_label)
              <span class="inline-flex items-center rounded-full bg-slate-900 text-white px-2.5 py-0.5">
                <i data-lucide="hospital" class="h-3 w-3 mr-1"></i>
                {{ $orientation->ward_label }}
              </span>
            @endif
            @if($orientation->estimated_watch_minutes)
              <span class="inline-flex items-center rounded-full bg-slate-100 text-slate-700 px-2.5 py-0.5">
                <i data-lucide="clock" class="h-3 w-3 mr-1"></i>
                ~{{ $orientation->estimated_watch_minutes }} minutes
              </span>
            @endif
          </div>
        </div>
      </header>

      <div class="grid gap-6 lg:grid-cols-2">
        {{-- Left column --}}
        <div class="space-y-4">
          @if($orientation->culture_text)
            <section class="rounded-2xl bg-white border border-slate-200 p-4">
              <h2 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                <i data-lucide="hand-heart" class="h-4 w-4 text-emerald-600"></i>
                Ward culture & expectations
              </h2>
              <p class="mt-2 text-sm text-slate-700 whitespace-pre-line">{{ $orientation->culture_text }}</p>
            </section>
          @endif

          @if($orientation->routines_text)
            <section class="rounded-2xl bg-white border border-slate-200 p-4">
              <h2 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                <i data-lucide="calendar-clock" class="h-4 w-4 text-emerald-600"></i>
                Daily routines
              </h2>
              <p class="mt-2 text-sm text-slate-700 whitespace-pre-line">{{ $orientation->routines_text }}</p>
            </section>
          @endif

          @if($orientation->patient_cases_text)
            <section class="rounded-2xl bg-white border border-slate-200 p-4">
              <h2 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                <i data-lucide="activity" class="h-4 w-4 text-emerald-600"></i>
                Typical patient cases
              </h2>
              <p class="mt-2 text-sm text-slate-700 whitespace-pre-line">{{ $orientation->patient_cases_text }}</p>
            </section>
          @endif
        </div>

        {{-- Right column --}}
        <div class="space-y-4">
          @if($orientation->workload_text)
            <section class="rounded-2xl bg-white border border-slate-200 p-4">
              <h2 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                <i data-lucide="layout-list" class="h-4 w-4 text-emerald-600"></i>
                How nurses manage workload
              </h2>
              <p class="mt-2 text-sm text-slate-700 whitespace-pre-line">{{ $orientation->workload_text }}</p>
            </section>
          @endif

          @if($orientation->emergencies_text)
            <section class="rounded-2xl bg-white border border-slate-200 p-4">
              <h2 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                <i data-lucide="alert-triangle" class="h-4 w-4 text-emerald-600"></i>
                Common emergencies & responses
              </h2>
              <p class="mt-2 text-sm text-slate-700 whitespace-pre-line">{{ $orientation->emergencies_text }}</p>
            </section>
          @endif

          @if($orientation->layout_locations_text || $orientation->tips_text)
            <section class="rounded-2xl bg-white border border-slate-200 p-4 space-y-4">
              @if($orientation->layout_locations_text)
                <div>
                  <h2 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                    <i data-lucide="map-pin" class="h-4 w-4 text-emerald-600"></i>
                    Layout, locations & who to approach
                  </h2>
                  <p class="mt-2 text-sm text-slate-700 whitespace-pre-line">{{ $orientation->layout_locations_text }}</p>
                </div>
              @endif

              @if($orientation->tips_text)
                <div>
                  <h2 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                    <i data-lucide="sparkles" class="h-4 w-4 text-emerald-600"></i>
                    Practical tips for student nurses
                  </h2>
                  <p class="mt-2 text-sm text-slate-700 whitespace-pre-line">{{ $orientation->tips_text }}</p>
                </div>
              @endif
            </section>
          @endif
        </div>
      </div>
    </div>
  </section>
</main>

@includeIf('partials.student-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>
</body>
</html>
