@include('faculty.chartings.parts.head', ['title' => 'Vital Signs'])
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Vital Signs · NurSync (CI)</title>
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

        {{-- LEFT: Title + description --}}
        <div>
          <h1 class="text-[28px] font-extrabold tracking-tight text-slate-900">
            Vital Signs
          </h1>
          <p class="mt-1 text-xs text-slate-500 max-w-md">
            Review recorded vital signs, anthropometrics, BMI, and BSA measurements for this patient.
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

      @if ($records->isEmpty())
        @include('faculty.chartings._empty-state')
      @else
        <div class="space-y-3">
          @foreach ($records as $row)
            @php
              $bp = $row->bp ?? (($row->bp_systolic && $row->bp_diastolic) ? ($row->bp_systolic.'/'.$row->bp_diastolic) : null);

              $bmi = $row->bmi;
              $bsa = $row->bsa_m2;
              $bmiCat = $row->bmi_category;

              // Color classes for BMI classification chip
              $bmiPillBase = 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-[11px] font-semibold';
              $bmiPillClass = $bmiPillBase . ' border-slate-200 bg-slate-50 text-slate-500';

              if ($bmiCat === 'Underweight') {
                  $bmiPillClass = $bmiPillBase . ' border-sky-200 bg-sky-50 text-sky-800';
              } elseif ($bmiCat === 'Healthy weight') {
                  $bmiPillClass = $bmiPillBase . ' border-emerald-200 bg-emerald-50 text-emerald-800';
              } elseif ($bmiCat === 'Overweight') {
                  $bmiPillClass = $bmiPillBase . ' border-amber-200 bg-amber-50 text-amber-800';
              } elseif ($bmiCat === 'Obesity') {
                  $bmiPillClass = $bmiPillBase . ' border-rose-200 bg-rose-50 text-rose-800';
              }
            @endphp

            <details
              class="group rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow animate-card-in">

              {{-- SUMMARY --}}
              <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4">
                <div class="flex items-start gap-3">
                  <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50">
                    <i data-lucide="activity" class="h-4 w-4 text-rose-600"></i>
                  </span>

                  <div>
                    {{-- Date + condensed vitals line --}}
                    <div class="flex flex-wrap items-center gap-2">
                      <span class="text-sm font-semibold text-slate-900">
                        {{ \Carbon\Carbon::parse($row->taken_at)->format('M d, Y · h:ia') }}
                      </span>

                      {{-- Quick vitals string --}}
                      <span class="text-xs text-slate-500">
                        @if(!is_null($row->temp_c)) Temp {{ $row->temp_c }}°C · @endif
                        @if(!is_null($row->heart_rate_bpm)) HR {{ $row->heart_rate_bpm }} · @endif
                        @if(!is_null($row->resp_rate_cpm)) RR {{ $row->resp_rate_cpm }} · @endif
                        @if($bp) BP {{ $bp }} · @endif
                        @if(!is_null($row->spo2_pct)) SpO₂ {{ $row->spo2_pct }}% @endif
                      </span>
                    </div>

                    {{-- BMI + BSA (summary chips) --}}
                    @if($bmiCat)
                      <div class="mt-1 flex flex-wrap items-center gap-2">
                        <span class="{{ $bmiPillClass }}">
                          BMI {{ number_format($bmi, 2) }} · {{ $bmiCat }}
                        </span>

                        @if($bsa)
                          <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2.5 py-0.5 text-[11px] font-medium text-slate-600">
                            BSA {{ number_format($bsa, 2) }} m²
                          </span>
                        @endif
                      </div>
                    @endif

                    {{-- 1-line remarks preview --}}
                    @if($row->remarks)
                      <div class="mt-1 text-xs text-slate-500 line-clamp-1">
                        {{ $row->remarks }}
                      </div>
                    @endif
                  </div>
                </div>

                <i data-lucide="chevron-down"
                   class="h-5 w-5 text-slate-400 group-open:rotate-180 transition-transform duration-200"></i>
              </summary>

              {{-- DETAILS --}}
              <div class="px-5 pb-5 pt-3 border-t border-slate-100 text-sm text-slate-700 space-y-3 bg-slate-50/60">

                {{-- Vitals grid --}}
                <div class="grid sm:grid-cols-2 gap-3">
                  <div><span class="font-semibold">Temperature:</span> {{ $row->temp_c ?? '—' }} °C</div>
                  <div><span class="font-semibold">Heart Rate:</span> {{ $row->heart_rate_bpm ?? '—' }} bpm</div>
                  <div><span class="font-semibold">Resp. Rate:</span> {{ $row->resp_rate_cpm ?? '—' }} cpm</div>
                  <div><span class="font-semibold">Blood Pressure:</span> {{ $bp ?? '—' }}</div>
                  <div><span class="font-semibold">SpO₂:</span> {{ $row->spo2_pct ?? '—' }}%</div>
                  <div><span class="font-semibold">Pain Score:</span> {{ $row->pain_score ?? '—' }}</div>

                  {{-- Anthropometrics --}}
                  <div>
                    <span class="font-semibold">Height:</span>
                    {{ !is_null($row->height_cm) ? $row->height_cm.' cm' : '—' }}
                  </div>

                  <div>
                    <span class="font-semibold">Weight:</span>
                    {{ !is_null($row->weight_kg) ? $row->weight_kg.' kg' : '—' }}
                  </div>

                  <div>
                    <span class="font-semibold">BMI:</span>
                    {{ !is_null($bmi) ? number_format($bmi, 2) : '—' }} kg/m²
                  </div>

                  <div>
                    <span class="font-semibold">BMI Category:</span>
                    @if($bmiCat)
                      <span class="ml-1 {{ $bmiPillClass }}">{{ $bmiCat }}</span>
                    @else
                      —
                    @endif
                  </div>

                  <div>
                    <span class="font-semibold">BSA:</span>
                    {{ !is_null($bsa) ? number_format($bsa, 2).' m²' : '—' }}
                  </div>

                  <div class="sm:col-span-2">
                    <span class="font-semibold">Remarks:</span>
                    {{ $row->remarks ?? '—' }}
                  </div>
                </div>

                {{-- Footer meta (same vibe as Nurse’s Notes) --}}
                <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                  <p class="text-xs text-slate-500">
                    Taken at
                    <span class="font-medium text-slate-700">
                      {{ \Carbon\Carbon::parse($row->taken_at)->format('M d, Y · h:ia') }}
                    </span>
                  </p>
                  @if($bmiCat)
                    <p class="text-xs text-slate-400">
                      BMI classification:
                      <span class="font-semibold text-slate-600">{{ $bmiCat }}</span>
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
