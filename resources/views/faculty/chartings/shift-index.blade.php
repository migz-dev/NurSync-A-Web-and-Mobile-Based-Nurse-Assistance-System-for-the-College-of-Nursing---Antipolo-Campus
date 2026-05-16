<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Shift Handover · NurSync (CI)</title>
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
            Shift Handover
          </h1>
          <p class="mt-1 text-xs text-slate-500 max-w-md">
            Review key points, pending tasks, and important remarks passed between outgoing and incoming shifts.
          </p>
        </div>

        {{-- RIGHT: Back button --}}
        <a href="{{ route('faculty.chartings.patient', $patient) }}"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-300 text-slate-700 px-4 py-2 text-sm hover:bg-slate-50 transition">
          <i data-lucide="arrow-left" class="h-4 w-4"></i>
          <span>Back to Patient</span>
        </a>
      </div>

      @include('faculty.chartings._patient-banner')

      @if($records->isEmpty())
        @include('faculty.chartings._empty-state')
      @else
        <div class="space-y-3">
          @foreach($records as $row)
            @php
              $shiftDate = $row->shift_date
                  ? \Carbon\Carbon::parse($row->shift_date)->format('M d, Y')
                  : null;

              $fromShift = $row->from_shift ?: null;
              $toShift   = $row->to_shift ?: null;
            @endphp

            <details
              class="group rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow animate-card-in">

              {{-- SUMMARY --}}
              <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4">
                <div class="flex items-start gap-3">
                  <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-teal-50">
                    <i data-lucide="swap-horizontal" class="h-4 w-4 text-teal-600"></i>
                  </span>

                  <div>
                    {{-- Date + shift labels --}}
                    <div class="flex flex-wrap items-center gap-2">
                      <span class="text-sm font-semibold text-slate-900">
                        {{ $shiftDate ?? 'Handover' }}
                      </span>

                      @if($fromShift || $toShift)
                        <span class="text-[11px] font-medium text-slate-500">
                          {{ $fromShift ?? '' }}
                          @if($toShift)
                            → {{ $toShift }}
                          @endif
                        </span>
                      @endif
                    </div>

                    {{-- Summary line --}}
                    @if($row->summary)
                      <div class="mt-1 text-xs text-slate-500 line-clamp-1">
                        {{ $row->summary }}
                      </div>
                    @endif
                  </div>
                </div>

                <i data-lucide="chevron-down"
                   class="h-5 w-5 text-slate-400 group-open:rotate-180 transition-transform duration-200"></i>
              </summary>

              {{-- DETAILS --}}
              <div class="px-5 pb-5 pt-3 border-t border-slate-100 text-sm text-slate-700 space-y-3 bg-slate-50/60">

                <div class="grid md:grid-cols-2 gap-3">
                  {{-- Key points --}}
                  <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <span class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Key Points
                    </span>
                    <p class="text-sm text-slate-800 whitespace-pre-line">
                      {{ $row->key_points ?? '—' }}
                    </p>
                  </div>

                  {{-- Tasks --}}
                  <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <span class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Tasks / Follow-ups
                    </span>
                    <p class="text-sm text-slate-800 whitespace-pre-line">
                      {{ $row->tasks ?? '—' }}
                    </p>
                  </div>

                  {{-- Remarks (full width) --}}
                  <div class="md:col-span-2 rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <span class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Remarks
                    </span>
                    <p class="text-sm text-slate-800 whitespace-pre-line">
                      {{ $row->remarks ?? '—' }}
                    </p>
                  </div>
                </div>

                {{-- Footer meta --}}
                <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                  <p class="text-xs text-slate-500">
                    Shift date:
                    <span class="font-medium text-slate-700">
                      {{ $shiftDate ?? '—' }}
                    </span>
                  </p>

                  @if($fromShift || $toShift)
                    <p class="text-xs text-slate-400">
                      Handover:
                      <span class="font-semibold text-slate-600">
                        {{ $fromShift ?? '—' }}
                        @if($toShift)
                          → {{ $toShift }}
                        @endif
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
