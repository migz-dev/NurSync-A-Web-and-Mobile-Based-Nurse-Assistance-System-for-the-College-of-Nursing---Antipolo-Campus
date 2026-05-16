{{-- resources/views/faculty/chartings/pain-index.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Pain Assessment · NurSync (CI)</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family:'Poppins', ui-sans-serif, system-ui;
    }

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
  @include('partials.faculty-sidebar', ['active' => 'chartings'])

  <section class="flex-1">
    <div class="container mx-auto px-8 py-10 space-y-6">

      {{-- HEADER (title + description on the LEFT, Back button on the RIGHT) --}}
      <div class="flex items-center justify-between gap-3">
        {{-- LEFT: Icon + title + description --}}
        <div>
          <div class="flex items-center gap-2">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-pink-50">
              <i data-lucide="thermometer" class="h-5 w-5 text-pink-600"></i>
            </span>
            <h1 class="text-[26px] sm:text-[28px] font-extrabold tracking-tight text-slate-900">
              Pain Assessment
            </h1>
          </div>
          <p class="mt-1 text-xs text-slate-500 max-w-md">
            Review structured pain assessments including score, location, quality, factors, and response to interventions.
          </p>
        </div>

        {{-- RIGHT: Back button --}}
        <a href="{{ route('faculty.chartings.patient', $patient) }}"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-300 text-slate-700 px-4 py-2 text-sm hover:bg-slate-50 transition">
          <i data-lucide="arrow-left" class="h-4 w-4"></i>
          <span>Back to Patient</span>
        </a>
      </div>

      {{-- Patient banner --}}
      @include('faculty.chartings._patient-banner')

      @if($records->isEmpty())
        @include('faculty.chartings._empty-state')
      @else
        <div class="space-y-3">
          @foreach($records as $row)
            @php
              $score = $row->pain_score ?? null;
              $scale = $row->scale_name ?? $row->scale ?? null; // depends on your columns

              $summary = '';
              if (!empty($row->location)) {
                  $summary = 'Location: '.$row->location;
              } elseif (!empty($row->quality)) {
                  $summary = 'Quality: '.$row->quality;
              } elseif (!empty($row->notes)) {
                  $summary = \Illuminate\Support\Str::limit($row->notes, 90);
              }
            @endphp

            <details
              class="group rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow animate-card-in">

              <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4">
                <div class="flex items-start gap-3">
                  <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-pink-50">
                    <i data-lucide="thermometer" class="h-4 w-4 text-pink-600"></i>
                  </span>
                  <div>
                    <div class="flex flex-wrap items-center gap-2 text-sm font-semibold text-slate-900">
                      <span>
                        {{ \Carbon\Carbon::parse($row->assessed_at)->format('M d, Y · h:ia') }}
                      </span>

                      @if(!is_null($score))
                        <span class="inline-flex items-center rounded-md bg-pink-50 px-2 py-0.5 text-xs font-semibold text-pink-700">
                          Pain: {{ $score }}/10
                        </span>
                      @endif

                      @if(!empty($scale))
                        <span class="inline-flex items-center rounded-md bg-slate-50 px-2 py-0.5 text-[11px] font-medium text-slate-700">
                          {{ $scale }}
                        </span>
                      @endif
                    </div>

                    <div class="mt-1 text-xs text-slate-500 line-clamp-1">
                      {{ $summary ?: 'No quick summary recorded for this assessment.' }}
                    </div>
                  </div>
                </div>

                <i data-lucide="chevron-down"
                   class="h-5 w-5 text-slate-400 group-open:rotate-180 transition-transform duration-200"></i>
              </summary>

              <div class="px-5 pb-5 pt-3 border-t border-slate-100 text-sm text-slate-700 space-y-4 bg-slate-50/60">

                {{-- Core pain data --}}
                <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
                  @if(!is_null($row->pain_score))
                    <p>
                      <span class="font-semibold text-slate-900">Pain Score (0–10):</span>
                      <span class="ml-1">{{ $row->pain_score }}</span>
                    </p>
                  @endif

                  @if(!empty($row->location))
                    <p>
                      <span class="font-semibold text-slate-900">Location:</span>
                      <span class="ml-1">{{ $row->location }}</span>
                    </p>
                  @endif

                  @if(!empty($row->quality))
                    <p>
                      <span class="font-semibold text-slate-900">Quality:</span>
                      <span class="ml-1">{{ $row->quality }}</span>
                    </p>
                  @endif

                  @if(!empty($row->onset))
                    <p>
                      <span class="font-semibold text-slate-900">Onset:</span>
                      <span class="ml-1">{{ $row->onset }}</span>
                    </p>
                  @endif

                  @if(!empty($row->duration))
                    <p>
                      <span class="font-semibold text-slate-900">Duration:</span>
                      <span class="ml-1">{{ $row->duration }}</span>
                    </p>
                  @endif

                  @if(!empty($row->frequency))
                    <p>
                      <span class="font-semibold text-slate-900">Frequency:</span>
                      <span class="ml-1">{{ $row->frequency }}</span>
                    </p>
                  @endif
                </div>

                {{-- Aggravating / relieving / associated --}}
                @if(
                  !empty($row->aggravating_factors) ||
                  !empty($row->relieving_factors) ||
                  !empty($row->associated_symptoms)
                )
                  <div class="grid gap-3 md:grid-cols-3">
                    @if(!empty($row->aggravating_factors))
                      <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                          Aggravating Factors
                        </h3>
                        <p class="mt-1 whitespace-pre-line">{{ $row->aggravating_factors }}</p>
                      </div>
                    @endif

                    @if(!empty($row->relieving_factors))
                      <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                          Relieving Factors
                        </h3>
                        <p class="mt-1 whitespace-pre-line">{{ $row->relieving_factors }}</p>
                      </div>
                    @endif

                    @if(!empty($row->associated_symptoms))
                      <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                          Associated Symptoms
                        </h3>
                        <p class="mt-1 whitespace-pre-line">{{ $row->associated_symptoms }}</p>
                      </div>
                    @endif
                  </div>
                @endif

                {{-- Functional impact --}}
                @if(!empty($row->effect_on_function))
                  <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                      Effect on Function / Activities
                    </h3>
                    <p class="mt-1 whitespace-pre-line">{{ $row->effect_on_function }}</p>
                  </div>
                @endif

                {{-- Interventions + response --}}
                @if(!empty($row->interventions) || !empty($row->response))
                  <div class="grid gap-3 md:grid-cols-2">
                    @if(!empty($row->interventions))
                      <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                          Interventions
                        </h3>
                        <p class="mt-1 whitespace-pre-line">{{ $row->interventions }}</p>
                      </div>
                    @endif

                    @if(!empty($row->response))
                      <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                          Response / Re-assessment
                        </h3>
                        <p class="mt-1 whitespace-pre-line">{{ $row->response }}</p>
                      </div>
                    @endif
                  </div>
                @endif

                {{-- Extra notes --}}
                @if(!empty($row->notes))
                  <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                      Additional Notes
                    </h3>
                    <p class="mt-1 whitespace-pre-line">{{ $row->notes }}</p>
                  </div>
                @endif

                {{-- Footer meta --}}
                <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                  <p class="text-xs text-slate-500">
                    Assessed:
                    <span class="font-medium text-slate-700">
                      {{ \Carbon\Carbon::parse($row->assessed_at)->format('M d, Y · h:ia') }}
                    </span>
                  </p>
                  @if(!is_null($score))
                    <p class="text-xs text-slate-400">
                      Pain score:
                      <span class="font-semibold text-slate-600">
                        {{ $score }}/10
                      </span>
                    </p>
                  @endif
                </div>
              </div>
            </details>
          @endforeach
        </div>
      @endif

    </div>
  </section>
</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
  try { lucide.createIcons(); } catch (e) {}
</script>
</body>
</html>
