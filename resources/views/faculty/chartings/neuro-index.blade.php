{{-- resources/views/faculty/chartings/neuro-index.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Neurological Observation · NurSync (CI)</title>
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
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50">
              <i data-lucide="brain" class="h-5 w-5 text-violet-600"></i>
            </span>
            <h1 class="text-[26px] sm:text-[28px] font-extrabold tracking-tight text-slate-900">
              Neurological Observation
            </h1>
          </div>
          <p class="mt-1 text-xs text-slate-500 max-w-md">
            Track GCS, pupillary response, limb power, and focused neuro findings over time for this patient.
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

      {{-- Empty state vs list --}}
      @if($records->isEmpty())
        @include('faculty.chartings._empty-state')
      @else
        <div class="space-y-3">
          @foreach($records as $row)
            @php
              $observedAt = \Carbon\Carbon::parse($row->observed_at ?? $row->assessed_at)->format('M d, Y · h:ia');
              $gcsTotal   = $row->gcs_total ?? null;

              $summary = '';
              if (!empty($row->neuro_findings)) {
                $summary = \Illuminate\Support\Str::limit($row->neuro_findings, 90);
              } elseif (!empty($row->pupils)) {
                $summary = 'Pupils: ' . \Illuminate\Support\Str::limit($row->pupils, 60);
              } elseif (!empty($row->motor_status)) {
                $summary = 'Motor: ' . \Illuminate\Support\Str::limit($row->motor_status, 60);
              }
            @endphp

            <details
              class="group rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow animate-card-in">

              {{-- Accordion header --}}
              <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4">
                <div class="flex items-start gap-3">
                  <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-violet-50">
                    <i data-lucide="brain" class="h-4 w-4 text-violet-600"></i>
                  </span>

                  <div>
                    <div class="flex flex-wrap items-center gap-2">
                      <span class="text-sm font-semibold text-slate-900">
                        {{ $observedAt }}
                      </span>

                      {{-- GCS pill, if available --}}
                      @if(!is_null($gcsTotal))
                        <span class="inline-flex items-center rounded-md bg-violet-50 px-2 py-0.5 text-xs font-semibold text-violet-700">
                          GCS: {{ $gcsTotal }}
                        </span>
                      @endif
                    </div>

                    {{-- One-line neuro summary --}}
                    <div class="mt-1 text-xs text-slate-500 line-clamp-1">
                      {{ $summary ?: 'No quick neurological summary recorded for this entry.' }}
                    </div>
                  </div>
                </div>

                <i data-lucide="chevron-down"
                   class="h-5 w-5 text-slate-400 group-open:rotate-180 transition-transform duration-200"></i>
              </summary>

              {{-- Accordion body --}}
              <div class="px-5 pb-5 pt-3 border-t border-slate-100 text-sm text-slate-700 space-y-4 bg-slate-50/60">

                {{-- Glasgow Coma Scale --}}
                @if(!is_null($row->gcs_eye ?? null) ||
                    !is_null($row->gcs_verbal ?? null) ||
                    !is_null($row->gcs_motor ?? null) ||
                    !is_null($row->gcs_total ?? null))
                  <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <h3 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Glasgow Coma Scale (GCS)
                    </h3>
                    <p class="text-sm text-slate-800">
                      @if(!is_null($row->gcs_eye ?? null))
                        <span class="font-semibold">Eye (E):</span> {{ $row->gcs_eye }}
                      @endif
                      @if(!is_null($row->gcs_verbal ?? null))
                        <span class="ml-3 font-semibold">Verbal (V):</span> {{ $row->gcs_verbal }}
                      @endif
                      @if(!is_null($row->gcs_motor ?? null))
                        <span class="ml-3 font-semibold">Motor (M):</span> {{ $row->gcs_motor }}
                      @endif
                      @if(!is_null($row->gcs_total ?? null))
                        <span class="ml-3 font-semibold">Total:</span> {{ $row->gcs_total }}
                      @endif
                    </p>
                  </div>
                @endif

                {{-- Pupils --}}
                @if(!empty($row->pupils) ||
                    !empty($row->pupils_left) ||
                    !empty($row->pupils_right))
                  <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <h3 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Pupillary Response
                    </h3>
                    <div class="mt-1 space-y-1 text-sm text-slate-800">
                      @if(!empty($row->pupils))
                        <p>{{ $row->pupils }}</p>
                      @endif
                      @if(!empty($row->pupils_left) || !empty($row->pupils_right))
                        <p>
                          @if(!empty($row->pupils_left))
                            <span class="font-semibold">Left:</span> {{ $row->pupils_left }}
                          @endif
                          @if(!empty($row->pupils_right))
                            <span class="ml-3 font-semibold">Right:</span> {{ $row->pupils_right }}
                          @endif
                        </p>
                      @endif
                    </div>
                  </div>
                @endif

                {{-- Motor / Limb power --}}
                @if(!empty($row->motor_status) ||
                    !empty($row->limb_power_upper_left) ||
                    !empty($row->limb_power_upper_right) ||
                    !empty($row->limb_power_lower_left) ||
                    !empty($row->limb_power_lower_right))
                  <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <h3 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Motor &amp; Limb Power
                    </h3>
                    <div class="mt-1 space-y-1 text-sm text-slate-800">
                      @if(!empty($row->motor_status))
                        <p>{{ $row->motor_status }}</p>
                      @endif

                      @if(!empty($row->limb_power_upper_left) ||
                          !empty($row->limb_power_upper_right) ||
                          !empty($row->limb_power_lower_left) ||
                          !empty($row->limb_power_lower_right))
                        <p>
                          @if(!empty($row->limb_power_upper_left))
                            <span class="font-semibold">UL (L):</span> {{ $row->limb_power_upper_left }}
                          @endif
                          @if(!empty($row->limb_power_upper_right))
                            <span class="ml-3 font-semibold">UL (R):</span> {{ $row->limb_power_upper_right }}
                          @endif
                          @if(!empty($row->limb_power_lower_left))
                            <span class="ml-3 font-semibold">LL (L):</span> {{ $row->limb_power_lower_left }}
                          @endif
                          @if(!empty($row->limb_power_lower_right))
                            <span class="ml-3 font-semibold">LL (R):</span> {{ $row->limb_power_lower_right }}
                          @endif
                        </p>
                      @endif
                    </div>
                  </div>
                @endif

                {{-- Specific neuro findings --}}
                @if(!empty($row->neuro_findings))
                  <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <h3 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Neurological Findings
                    </h3>
                    <p class="mt-1 text-sm text-slate-800 whitespace-pre-line">
                      {{ $row->neuro_findings }}
                    </p>
                  </div>
                @endif

                {{-- Interventions --}}
                @if(!empty($row->interventions))
                  <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <h3 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Interventions &amp; Actions
                    </h3>
                    <p class="mt-1 text-sm text-slate-800 whitespace-pre-line">
                      {{ $row->interventions }}
                    </p>
                  </div>
                @endif

                {{-- Notes --}}
                @if(!empty($row->neuro_notes))
                  <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <h3 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Additional Notes
                    </h3>
                    <p class="mt-1 text-sm text-slate-800 whitespace-pre-line">
                      {{ $row->neuro_notes }}
                    </p>
                  </div>
                @endif

                {{-- Footer meta --}}
                <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                  <p class="text-xs text-slate-500">
                    Observed:
                    <span class="font-medium text-slate-700">
                      {{ $observedAt }}
                    </span>
                  </p>
                  @if(!is_null($gcsTotal))
                    <p class="text-xs text-slate-400">
                      GCS total:
                      <span class="font-semibold text-slate-600">
                        {{ $gcsTotal }}
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
<script>try{lucide.createIcons()}catch(e){}</script>

</body>
</html>
