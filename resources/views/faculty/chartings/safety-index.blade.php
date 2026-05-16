{{-- resources/views/faculty/chartings/safety-index.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Safety & Fall Risk · NurSync (CI)</title>
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
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-yellow-50">
              <i data-lucide="shield-check" class="h-5 w-5 text-yellow-600"></i>
            </span>
            <h1 class="text-[26px] sm:text-[28px] font-extrabold tracking-tight text-slate-900">
              Safety &amp; Fall Risk Checklist
            </h1>
          </div>
          <p class="mt-1 text-xs text-slate-500 max-w-md">
            Review bedside safety checks, fall-risk scores, and interventions implemented for this shift.
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

      {{-- Empty State --}}
      @if($records->isEmpty())
        @include('faculty.chartings._empty-state')
      @else
        <div class="space-y-3">
          @foreach($records as $row)
            @php
              $score = $row->fall_score ?? null;

              $summary = '';
              if (!empty($row->safety_notes)) {
                $summary = \Illuminate\Support\Str::limit($row->safety_notes, 90);
              } elseif (!empty($row->environmental_checks)) {
                $summary = 'Environment checked for clutter and hazards.';
              }

              $checks = [
                'bed_low'                  => 'Bed in lowest position',
                'bed_rails_up'             => 'Bed rails up (x2)',
                'call_light_within_reach'  => 'Call light within reach',
                'non_skid_footwear'        => 'Non-skid footwear applied',
                'environmental_checks'     => 'Environment clutter-free',
                'id_band_checked'          => 'ID band checked',
                'medications_reviewed'     => 'Medications reviewed for fall-risk',
              ];
            @endphp

            <details
              class="group rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow animate-card-in">

              {{-- Accordion Header --}}
              <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4">
                <div class="flex items-start gap-3">
                  <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-yellow-50">
                    <i data-lucide="shield-check" class="h-4 w-4 text-yellow-600"></i>
                  </span>

                  <div>
                    <div class="flex flex-wrap items-center gap-2">
                      <span class="text-sm font-semibold text-slate-900">
                        {{ \Carbon\Carbon::parse($row->checked_at)->format('M d, Y · h:ia') }}
                      </span>

                      @if(!is_null($score))
                        <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-0.5 text-xs font-semibold text-yellow-700">
                          Fall Risk: {{ $score }}
                        </span>
                      @endif
                    </div>

                    {{-- One-line quick summary --}}
                    <div class="mt-1 text-xs text-slate-500 line-clamp-1">
                      {{ $summary ?: 'No quick summary recorded for this shift.' }}
                    </div>
                  </div>
                </div>

                <i data-lucide="chevron-down"
                   class="h-5 w-5 text-slate-400 group-open:rotate-180 transition-transform duration-200"></i>
              </summary>

              {{-- Accordion Body --}}
              <div class="px-5 pb-5 pt-3 border-t border-slate-100 text-sm text-slate-700 space-y-4 bg-slate-50/60">

                {{-- Fall Score --}}
                @if(!is_null($row->fall_score))
                  <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <span class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Fall Risk Score
                    </span>
                    <p class="text-sm text-slate-800">
                      {{ $row->fall_score }}
                    </p>
                  </div>
                @endif

                {{-- Safety Checklist Items --}}
                <div class="space-y-2">
                  <h3 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
                    Checklist
                  </h3>
                  <div class="flex flex-wrap gap-2">
                    @foreach($checks as $field => $label)
                      @if(isset($row->{$field}))
                        @php $ok = (bool)$row->{$field}; @endphp
                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px]
                                     {{ $ok ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                            : 'bg-slate-50 text-slate-600 border border-slate-200' }}">
                          <i data-lucide="{{ $ok ? 'check-circle-2' : 'circle' }}" class="h-3.5 w-3.5"></i>
                          {{ $label }}
                        </span>
                      @endif
                    @endforeach
                  </div>
                </div>

                {{-- Assistive Devices --}}
                @if(!empty($row->assistive_devices))
                  <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <h3 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Assistive Devices
                    </h3>
                    <p class="text-sm text-slate-800 whitespace-pre-line">
                      {{ $row->assistive_devices }}
                    </p>
                  </div>
                @endif

                {{-- Interventions --}}
                @if(!empty($row->interventions))
                  <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <h3 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Interventions Implemented
                    </h3>
                    <p class="text-sm text-slate-800 whitespace-pre-line">
                      {{ $row->interventions }}
                    </p>
                  </div>
                @endif

                {{-- Notes --}}
                @if(!empty($row->safety_notes))
                  <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <h3 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Additional Notes
                    </h3>
                    <p class="text-sm text-slate-800 whitespace-pre-line">
                      {{ $row->safety_notes }}
                    </p>
                  </div>
                @endif

                {{-- Footer meta --}}
                <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                  <p class="text-xs text-slate-500">
                    Checked:
                    <span class="font-medium text-slate-700">
                      {{ \Carbon\Carbon::parse($row->checked_at)->format('M d, Y · h:ia') }}
                    </span>
                  </p>
                  @if(!is_null($score))
                    <p class="text-xs text-slate-400">
                      Fall risk score:
                      <span class="font-semibold text-slate-600">
                        {{ $score }}
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
