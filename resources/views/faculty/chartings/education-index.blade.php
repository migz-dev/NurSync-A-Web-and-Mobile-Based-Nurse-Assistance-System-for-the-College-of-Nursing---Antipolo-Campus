{{-- resources/views/faculty/chartings/education-index.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Patient Education · NurSync (CI)</title>
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
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50">
              <i data-lucide="graduation-cap" class="h-5 w-5 text-amber-600"></i>
            </span>
            <h1 class="text-[26px] sm:text-[28px] font-extrabold tracking-tight text-slate-900">
              Patient Education Record
            </h1>
          </div>
          <p class="mt-1 text-xs text-slate-500 max-w-md">
            View documented teaching sessions, patient/family responses, and follow-up plans for education provided.
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
              $taughtAt = \Carbon\Carbon::parse($row->taught_at)->format('M d, Y · h:ia');
              $topic    = $row->topic ?? null;

              $summary = $row->notes
                ?? $row->response
                ?? $row->next_plan;
            @endphp

            <details
              class="group rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow animate-card-in">

              {{-- SUMMARY --}}
              <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4">
                <div class="flex items-start gap-3">
                  <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50">
                    <i data-lucide="graduation-cap" class="h-4 w-4 text-amber-600"></i>
                  </span>

                  <div>
                    <div class="flex flex-wrap items-center gap-2">
                      <span class="text-sm font-semibold text-slate-900">
                        {{ $taughtAt }}
                      </span>

                      @if(!empty($topic))
                        <span class="text-xs text-slate-700 font-medium">
                          {{ $topic }}
                        </span>
                      @endif
                    </div>

                    <div class="mt-1 text-xs text-slate-500 line-clamp-1">
                      {{ $summary ? \Illuminate\Support\Str::limit($summary, 90) : 'No notes recorded for this teaching session.' }}
                    </div>
                  </div>
                </div>

                <i data-lucide="chevron-down"
                   class="h-5 w-5 text-slate-400 group-open:rotate-180 transition-transform duration-200"></i>
              </summary>

              {{-- DETAILS --}}
              <div class="px-5 pb-5 pt-3 border-t border-slate-100 text-sm text-slate-700 space-y-3 bg-slate-50/60">

                <div class="grid gap-3 md:grid-cols-2">
                  @if(!empty($row->topic))
                    <p>
                      <span class="font-semibold text-slate-900">Topic:</span>
                      <span class="ml-1">{{ $row->topic }}</span>
                    </p>
                  @endif

                  @if(!empty($row->recipient))
                    <p>
                      <span class="font-semibold text-slate-900">Recipient:</span>
                      <span class="ml-1">{{ $row->recipient }}</span>
                    </p>
                  @endif

                  @if(!empty($row->method))
                    <p>
                      <span class="font-semibold text-slate-900">Method:</span>
                      <span class="ml-1">{{ $row->method }}</span>
                    </p>
                  @endif

                  @if(!empty($row->understanding_level))
                    <p>
                      <span class="font-semibold text-slate-900">Understanding Level:</span>
                      <span class="ml-1">{{ $row->understanding_level }}</span>
                    </p>
                  @endif
                </div>

                @if(!empty($row->materials_used))
                  <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <span class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Materials Used
                    </span>
                    <p class="text-sm text-slate-800 whitespace-pre-line">
                      {{ $row->materials_used }}
                    </p>
                  </div>
                @endif

                @if(!empty($row->response))
                  <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <span class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Patient / Family Response
                    </span>
                    <p class="text-sm text-slate-800 whitespace-pre-line">
                      {{ $row->response }}
                    </p>
                  </div>
                @endif

                @if(!empty($row->next_plan))
                  <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <span class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Reinforcement / Next Plan
                    </span>
                    <p class="text-sm text-slate-800 whitespace-pre-line">
                      {{ $row->next_plan }}
                    </p>
                  </div>
                @endif

                @if(!empty($row->notes))
                  <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <span class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Additional Notes
                    </span>
                    <p class="text-sm text-slate-800 whitespace-pre-line">
                      {{ $row->notes }}
                    </p>
                  </div>
                @endif

                {{-- Footer meta --}}
                <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                  <p class="text-xs text-slate-500">
                    Session time:
                    <span class="font-medium text-slate-700">
                      {{ $taughtAt }}
                    </span>
                  </p>
                  @if(!empty($row->recipient))
                    <p class="text-xs text-slate-400">
                      Recipient:
                      <span class="font-semibold text-slate-600">
                        {{ $row->recipient }}
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
