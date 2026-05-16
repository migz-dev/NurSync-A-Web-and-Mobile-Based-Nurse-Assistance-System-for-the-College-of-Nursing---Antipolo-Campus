@include('faculty.chartings.parts.scaffold', ['title' => 'Intake & Output']) {{-- optional --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Intake & Output · NurSync (CI)</title>
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
            Intake &amp; Output
          </h1>
          <p class="mt-1 text-xs text-slate-500 max-w-md">
            Review fluid intake, losses, and net balance to monitor the patient’s overall fluid status.
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
              $intake  = $row->intake_ml ?? 0;
              $output  = $row->output_ml ?? 0;
              $balance = $row->balance_ml ?? ($intake - $output);

              $balanceTextClass = 'text-slate-600';
              if ($balance < 0) {
                  $balanceTextClass = 'text-rose-600';
              } elseif ($balance > 0) {
                  $balanceTextClass = 'text-emerald-600';
              }
            @endphp

            <details
              class="group rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow animate-card-in">

              {{-- SUMMARY --}}
              <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4">
                <div class="flex items-start gap-3">
                  <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-cyan-50">
                    <i data-lucide="droplet" class="h-4 w-4 text-cyan-600"></i>
                  </span>

                  <div>
                    {{-- Date + quick line --}}
                    <div class="flex flex-wrap items-center gap-2">
                      <span class="text-sm font-semibold text-slate-900">
                        {{ \Carbon\Carbon::parse($row->logged_at)->format('M d, Y · h:ia') }}
                      </span>

                      <span class="text-xs text-slate-500">
                        Intake {{ $intake }} mL ·
                        Output {{ $output }} mL ·
                        Balance
                        <span class="{{ $balanceTextClass }}">
                          {{ $balance }} mL
                        </span>
                      </span>
                    </div>

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

                {{-- I&O grid --}}
                <div class="grid sm:grid-cols-2 gap-3">
                  <div>
                    <span class="font-semibold">Oral:</span>
                    {{ $row->intake_oral_ml ?? 0 }} mL
                  </div>

                  <div>
                    <span class="font-semibold">IV:</span>
                    {{ $row->intake_iv_ml ?? 0 }} mL
                  </div>

                  <div>
                    <span class="font-semibold">NG:</span>
                    {{ $row->intake_ng_ml ?? 0 }} mL
                  </div>

                  <div>
                    <span class="font-semibold">Urine:</span>
                    {{ $row->output_urine_ml ?? 0 }} mL
                  </div>

                  <div>
                    <span class="font-semibold">Stool:</span>
                    {{ $row->output_stool_ml ?? 0 }} mL
                  </div>

                  <div>
                    <span class="font-semibold">Emesis:</span>
                    {{ $row->output_emesis_ml ?? 0 }} mL
                  </div>

                  <div>
                    <span class="font-semibold">Drain:</span>
                    {{ $row->output_drain_ml ?? 0 }} mL
                  </div>

                  <div class="sm:col-span-2">
                    <span class="font-semibold">Remarks:</span>
                    {{ $row->remarks ?? '—' }}
                  </div>
                </div>

                {{-- Footer meta, same vibe as Nurse’s Notes --}}
                <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                  <p class="text-xs text-slate-500">
                    Logged at
                    <span class="font-medium text-slate-700">
                      {{ \Carbon\Carbon::parse($row->logged_at)->format('M d, Y · h:ia') }}
                    </span>
                  </p>
                  <p class="text-xs text-slate-400">
                    Net fluid balance:
                    <span class="font-semibold {{ $balanceTextClass }}">{{ $balance }} mL</span>
                  </p>
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
