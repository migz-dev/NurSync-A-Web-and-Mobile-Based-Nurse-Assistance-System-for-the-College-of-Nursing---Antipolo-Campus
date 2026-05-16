<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Medication Admin Record · NurSync (CI)</title>
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
            Medication Admin Record
          </h1>
          <p class="mt-1 text-xs text-slate-500 max-w-md">
            Review scheduled and administered medications, including doses, routes, indications, and remarks.
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
              $statusRaw = $row->status ?? '';
              $status    = $statusRaw ? ucfirst($statusRaw) : '—';

              $statusBase  = 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-[11px] font-semibold';
              $statusClass = $statusBase.' border-slate-200 bg-slate-50 text-slate-600';

              if ($statusRaw === 'given') {
                  $statusClass = $statusBase.' border-emerald-200 bg-emerald-50 text-emerald-800';
              } elseif ($statusRaw === 'missed') {
                  $statusClass = $statusBase.' border-rose-200 bg-rose-50 text-rose-800';
              } elseif ($statusRaw === 'held') {
                  $statusClass = $statusBase.' border-amber-200 bg-amber-50 text-amber-800';
              }

              $sched = $row->scheduled_time
                  ? \Carbon\Carbon::parse($row->scheduled_time)->format('M d, h:ia')
                  : null;
              $given = $row->administered_at
                  ? \Carbon\Carbon::parse($row->administered_at)->format('M d, h:ia')
                  : null;
            @endphp

            <details
              class="group rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow animate-card-in">

              {{-- SUMMARY --}}
              <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4">
                <div class="flex items-start gap-3">
                  <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50">
                    <i data-lucide="pill" class="h-4 w-4 text-emerald-600"></i>
                  </span>

                  <div>
                    {{-- Drug name + dose / route / freq + status chip --}}
                    <div class="flex flex-wrap items-center gap-2">
                      <span class="text-sm font-semibold text-slate-900">
                        {{ $row->drug_name }}
                      </span>

                      <span class="text-xs text-slate-500">
                        {{ $row->dose }}
                        @if($row->route) · {{ $row->route }} @endif
                        @if($row->frequency) · {{ $row->frequency }} @endif
                      </span>

                      {{-- Status chip --}}
                      <span class="{{ $statusClass }}">
                        <span class="h-1.5 w-1.5 rounded-full mr-1.5
                          @if($statusRaw === 'given') bg-emerald-500
                          @elseif($statusRaw === 'missed') bg-rose-500
                          @elseif($statusRaw === 'held') bg-amber-500
                          @else bg-slate-500
                          @endif
                        "></span>
                        {{ $status }}
                      </span>
                    </div>

                    {{-- Schedule + given line --}}
                    <div class="mt-1 text-xs text-slate-500">
                      Scheduled:
                      <span class="font-medium text-slate-700">
                        {{ $sched ?? '—' }}
                      </span>
                      <span class="mx-1 text-slate-300">•</span>
                      Given:
                      <span class="font-medium text-slate-700">
                        {{ $given ?? '—' }}
                      </span>
                    </div>
                  </div>
                </div>

                <i data-lucide="chevron-down"
                   class="h-5 w-5 text-slate-400 group-open:rotate-180 transition-transform duration-200"></i>
              </summary>

              {{-- DETAILS --}}
              <div class="px-5 pb-5 pt-3 border-t border-slate-100 text-sm text-slate-700 space-y-3 bg-slate-50/60">

                <div class="grid sm:grid-cols-2 gap-3">
                  <div>
                    <span class="font-semibold">Indication:</span>
                    {{ $row->indication ?? '—' }}
                  </div>

                  <div>
                    <span class="font-semibold">Given By:</span>
                    {{ $row->given_by ?? '—' }}
                  </div>

                  <div class="sm:col-span-2">
                    <span class="font-semibold">Remarks:</span>
                    {{ $row->remarks ?? '—' }}
                  </div>
                </div>

                {{-- Footer meta (same vibe as other chartings) --}}
                <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                  <p class="text-xs text-slate-500">
                    Last updated
                    <span class="font-medium text-slate-700">
                      @if($row->administered_at)
                        {{ \Carbon\Carbon::parse($row->administered_at)->format('M d, Y · h:ia') }}
                      @elseif($row->scheduled_time)
                        {{ \Carbon\Carbon::parse($row->scheduled_time)->format('M d, Y · h:ia') }}
                      @else
                        —
                      @endif
                    </span>
                  </p>
                  <p class="text-xs text-slate-400">
                    Administration status:
                    <span class="font-semibold text-slate-600">{{ $status }}</span>
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
