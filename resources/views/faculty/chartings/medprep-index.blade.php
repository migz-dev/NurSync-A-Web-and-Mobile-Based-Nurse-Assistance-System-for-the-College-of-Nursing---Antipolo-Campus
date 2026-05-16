{{-- resources/views/faculty/chartings/medprep-index.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Medication Preparation · NurSync (CI)</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family:'Poppins',ui-sans-serif,system-ui;
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
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50">
              <i data-lucide="syringe" class="h-5 w-5 text-emerald-600"></i>
            </span>
            <h1 class="text-[26px] sm:text-[28px] font-extrabold tracking-tight text-slate-900">
              Medication Preparation Checklist
            </h1>
          </div>
          <p class="mt-1 text-xs text-slate-500 max-w-md">
            Review prepared medications, dose calculations, safety checks, and co-signs completed before administration.
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
              $preparedAt = \Carbon\Carbon::parse($row->prepared_at)->format('M d, Y · h:ia');
              $drugName   = $row->drug_name ?? null;

              // Small one-line summary
              $dose  = trim((string)($row->dose ?? ''));
              $route = trim((string)($row->route ?? ''));
              $txt   = '';
              if ($dose || $route) {
                  $txt = 'Dose: '.($dose ?: '—');
                  if ($route) {
                      $txt .= ' • Route: '.$route;
                  }
              } elseif (!empty($row->remarks)) {
                  $txt = \Illuminate\Support\Str::limit($row->remarks, 90);
              }

              $checks = [
                'checks_5rights'     => '5 Rights checked',
                'expiry_check'       => 'Expiry date checked',
                'allergies_checked'  => 'Allergies verified',
                'label_complete'     => 'Label complete (name/dose/time)',
              ];
            @endphp

            <details
              class="group rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow animate-card-in">

              {{-- SUMMARY --}}
              <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4">
                <div class="flex items-start gap-3">
                  <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50">
                    <i data-lucide="syringe" class="h-4 w-4 text-emerald-600"></i>
                  </span>

                  <div>
                    <div class="flex flex-wrap items-center gap-2">
                      <span class="text-sm font-semibold text-slate-900">
                        {{ $preparedAt }}
                      </span>

                      @if(!empty($drugName))
                        <span class="text-xs text-slate-700 font-medium">
                          {{ $drugName }}
                        </span>
                      @endif
                    </div>

                    <div class="mt-1 text-xs text-slate-500 line-clamp-1">
                      {{ $txt ?: 'No quick summary recorded for this preparation.' }}
                    </div>
                  </div>
                </div>

                <i data-lucide="chevron-down"
                   class="h-5 w-5 text-slate-400 group-open:rotate-180 transition-transform duration-200"></i>
              </summary>

              {{-- DETAILS --}}
              <div class="px-5 pb-5 pt-3 border-t border-slate-100 text-sm text-slate-700 space-y-4 bg-slate-50/60">

                {{-- Medication core info --}}
                <div class="grid gap-3 md:grid-cols-2">
                  @if(!empty($row->drug_name))
                    <p>
                      <span class="font-semibold text-slate-900">Medication:</span>
                      <span class="ml-1">{{ $row->drug_name }}</span>
                    </p>
                  @endif

                  @if(!empty($row->dose))
                    <p>
                      <span class="font-semibold text-slate-900">Ordered Dose:</span>
                      <span class="ml-1">{{ $row->dose }}</span>
                    </p>
                  @endif

                  @if(!empty($row->dose_calculated))
                    <p>
                      <span class="font-semibold text-slate-900">Calculated Dose:</span>
                      <span class="ml-1">{{ $row->dose_calculated }}</span>
                    </p>
                  @endif

                  @if(!empty($row->route))
                    <p>
                      <span class="font-semibold text-slate-900">Route:</span>
                      <span class="ml-1">{{ $row->route }}</span>
                    </p>
                  @endif
                </div>

                {{-- Preparation details --}}
                @if(!empty($row->dilution) ||
                    !empty($row->total_volume_ml) ||
                    !empty($row->concentration))
                  <div class="space-y-1">
                    <h3 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
                      Preparation Details
                    </h3>
                    <div class="grid gap-3 md:grid-cols-3">
                      @if(!empty($row->dilution))
                        <p>
                          <span class="font-semibold text-slate-900">Dilution:</span>
                          <span class="ml-1">{{ $row->dilution }}</span>
                        </p>
                      @endif

                      @if(!empty($row->total_volume_ml))
                        <p>
                          <span class="font-semibold text-slate-900">Total Volume:</span>
                          <span class="ml-1">{{ $row->total_volume_ml }} mL</span>
                        </p>
                      @endif

                      @if(!empty($row->concentration))
                        <p>
                          <span class="font-semibold text-slate-900">Concentration:</span>
                          <span class="ml-1">{{ $row->concentration }}</span>
                        </p>
                      @endif
                    </div>
                  </div>
                @endif

                {{-- Safety checks block --}}
                @if(
                  isset($row->checks_5rights) ||
                  isset($row->expiry_check) ||
                  isset($row->allergies_checked) ||
                  isset($row->label_complete) ||
                  !empty($row->cosign_by)
                )
                  <div class="space-y-2">
                    <h3 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
                      Safety &amp; Verification
                    </h3>
                    <div class="flex flex-wrap gap-2">
                      @foreach($checks as $field => $label)
                        @if(isset($row->{$field}))
                          @php
                            $ok = (bool) $row->{$field};
                          @endphp
                          <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px]
                                         {{ $ok ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                                : 'bg-slate-50 text-slate-600 border border-slate-200' }}">
                            <i data-lucide="{{ $ok ? 'check-circle-2' : 'circle' }}" class="h-3.5 w-3.5"></i>
                            {{ $label }}
                          </span>
                        @endif
                      @endforeach

                      @if(!empty($row->cosign_by))
                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px]
                                       bg-sky-50 text-sky-700 border border-sky-200">
                          <i data-lucide="user-check" class="h-3.5 w-3.5"></i>
                          Co-signed by: {{ $row->cosign_by }}
                        </span>
                      @endif
                    </div>
                  </div>
                @endif

                {{-- Extra notes --}}
                @if(!empty($row->remarks))
                  <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <span class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Notes / Special Instructions
                    </span>
                    <p class="text-sm text-slate-800 whitespace-pre-line">
                      {{ $row->remarks }}
                    </p>
                  </div>
                @endif

                {{-- Footer meta (aligned with other chartings pages) --}}
                <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                  <p class="text-xs text-slate-500">
                    Prepared:
                    <span class="font-medium text-slate-700">
                      {{ $preparedAt }}
                    </span>
                  </p>

                  @if(!empty($row->cosign_by))
                    <p class="text-xs text-slate-400">
                      Co-signed by:
                      <span class="font-semibold text-slate-600">
                        {{ $row->cosign_by }}
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
