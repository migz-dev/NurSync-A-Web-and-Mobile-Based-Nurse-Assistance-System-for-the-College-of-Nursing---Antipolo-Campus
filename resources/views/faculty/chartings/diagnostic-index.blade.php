{{-- resources/views/faculty/chartings/diagnostic-index.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Diagnostic Results · NurSync (CI)</title>
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
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-fuchsia-50">
              <i data-lucide="flask-conical" class="h-5 w-5 text-fuchsia-600"></i>
            </span>
            <h1 class="text-[26px] sm:text-[28px] font-extrabold tracking-tight text-slate-900">
              Diagnostic Results Log
            </h1>
          </div>
          <p class="mt-1 text-xs text-slate-500 max-w-md">
            View recorded diagnostic and laboratory results with nursing interpretation, plans, and follow-up.
          </p>
        </div>

        {{-- RIGHT: Back button --}}
        <a href="{{ route('faculty.chartings.patient', $patient) }}"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-300 text-slate-700 px-4 py-2 text-sm hover:bg-slate-50 transition">
          <i data-lucide="arrow-left" class="h-4 w-4"></i>
          <span>Back to Patient</span>
        </a>
      </div>

      {{-- Patient banner (shared partial) --}}
      @include('faculty.chartings._patient-banner')

      @if($records->isEmpty())
        @include('faculty.chartings._empty-state')
      @else
        <div class="space-y-3">
          @foreach($records as $row)
            <details
              class="group rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow animate-card-in">

              <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4">
                <div class="flex items-start gap-3">
                  <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-fuchsia-50">
                    <i data-lucide="flask-conical" class="h-4 w-4 text-fuchsia-600"></i>
                  </span>
                  <div>
                    <div class="text-sm font-semibold text-slate-900">
                      {{ \Carbon\Carbon::parse($row->collected_at)->format('M d, Y · h:ia') }}

                      @if(!empty($row->result_type))
                        <span
                          class="ml-2 inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">
                          {{ strtoupper($row->result_type) }}
                        </span>
                      @endif

                      @if(!empty($row->test_name))
                        <span class="ml-2 text-xs text-slate-600">
                          — {{ $row->test_name }}
                        </span>
                      @endif
                    </div>

                    <div class="mt-1 text-xs text-slate-500 line-clamp-1">
                      @php
                        $summary = $row->key_values_summary
                          ?? $row->interpretation
                          ?? $row->notes;
                      @endphp
                      {{ $summary ? \Illuminate\Support\Str::limit($summary, 90) : 'No summary entered.' }}
                    </div>
                  </div>
                </div>

                <i data-lucide="chevron-down"
                   class="h-5 w-5 text-slate-400 group-open:rotate-180 transition-transform duration-200"></i>
              </summary>

              <div class="px-5 pb-5 pt-3 border-t border-slate-100 text-sm text-slate-700 space-y-3 bg-slate-50/60">
                @if(!empty($row->test_name) || !empty($row->result_type))
                  <div class="grid gap-3 md:grid-cols-2">
                    @if(!empty($row->test_name))
                      <p>
                        <span class="font-semibold text-slate-900">Test / Study:</span>
                        <span class="ml-1">{{ $row->test_name }}</span>
                      </p>
                    @endif
                    @if(!empty($row->result_type))
                      <p>
                        <span class="font-semibold text-slate-900">Type:</span>
                        <span class="ml-1">{{ ucfirst($row->result_type) }}</span>
                      </p>
                    @endif
                  </div>
                @endif

                @if(!empty($row->key_values_summary))
                  <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <span class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Key Values / Summary
                    </span>
                    <p class="text-sm text-slate-800 whitespace-pre-line">
                      {{ $row->key_values_summary }}
                    </p>
                  </div>
                @endif

                @if(!empty($row->interpretation))
                  <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <span class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Nursing Interpretation
                    </span>
                    <p class="text-sm text-slate-800 whitespace-pre-line">
                      {{ $row->interpretation }}
                    </p>
                  </div>
                @endif

                @if(!empty($row->plan))
                  <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <span class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Plan / Actions
                    </span>
                    <p class="text-sm text-slate-800 whitespace-pre-line">
                      {{ $row->plan }}
                    </p>
                  </div>
                @endif

                @if(!empty($row->follow_up))
                  <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <span class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Follow-up / Re-check
                    </span>
                    <p class="text-sm text-slate-800 whitespace-pre-line">
                      {{ $row->follow_up }}
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
                    Collected / logged
                    <span class="font-medium text-slate-700">
                      {{ \Carbon\Carbon::parse($row->collected_at)->format('M d, Y · h:ia') }}
                    </span>
                  </p>
                  @if(!empty($row->result_type))
                    <p class="text-xs text-slate-400">
                      Result type:
                      <span class="font-semibold text-slate-600">
                        {{ ucfirst($row->result_type) }}
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
