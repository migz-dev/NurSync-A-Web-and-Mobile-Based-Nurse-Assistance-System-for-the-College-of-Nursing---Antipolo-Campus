@include('faculty.chartings._frame-open', ['title' => 'Treatment / Procedure']) {{-- optional --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Treatment / Procedure · NurSync (CI)</title>
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
            Treatment / Procedure
          </h1>
          <p class="mt-1 text-xs text-slate-500 max-w-md">
            Review documented treatments and bedside procedures, including indications, notes, and outcomes.
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
              $performedAt = $row->performed_at
                  ? \Carbon\Carbon::parse($row->performed_at)->format('M d, Y · h:ia')
                  : null;
              $performedBy = $row->performed_by ?: null;
            @endphp

            <details
              class="group rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow animate-card-in">

              {{-- SUMMARY --}}
              <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4">
                <div class="flex items-start gap-3">
                  <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-orange-50">
                    <i data-lucide="stethoscope" class="h-4 w-4 text-orange-600"></i>
                  </span>

                <div>
                  <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-semibold text-slate-900">
                      {{ $row->procedure_name ?? 'Treatment / Procedure' }}
                    </span>

                    @if($performedAt)
                      <span class="text-[11px] font-medium text-slate-500">
                        Performed {{ $performedAt }}
                      </span>
                    @endif

                    @if($performedBy)
                      <span class="text-[11px] font-medium text-slate-500">
                        • By {{ $performedBy }}
                      </span>
                    @endif
                  </div>

                  @if($row->indication)
                    <div class="mt-1 text-xs text-slate-500 line-clamp-1">
                      Indication: {{ $row->indication }}
                    </div>
                  @endif
                </div>
                </div>

                <i data-lucide="chevron-down"
                   class="h-5 w-5 text-slate-400 group-open:rotate-180 transition-transform duration-200"></i>
              </summary>

              {{-- DETAILS --}}
              <div class="px-5 pb-5 pt-3 border-t border-slate-100 text-sm text-slate-700 space-y-3 bg-slate-50/60">

                <div class="grid md:grid-cols-3 gap-3">
                  <div class="md:col-span-2 rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                    <span class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                      Notes
                    </span>
                    <p class="text-sm text-slate-800 whitespace-pre-line">
                      {{ $row->notes ?? '—' }}
                    </p>
                  </div>

                  <div class="space-y-3">
                    <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                      <span class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                        Outcome
                      </span>
                      <p class="text-sm text-slate-800 whitespace-pre-line">
                        {{ $row->outcome ?? '—' }}
                      </p>
                    </div>

                    <div class="rounded-lg bg-white px-3 py-2.5 border border-slate-200">
                      <span class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">
                        Complications
                      </span>
                      <p class="text-sm text-slate-800 whitespace-pre-line">
                        {{ $row->complications ?? '—' }}
                      </p>
                    </div>
                  </div>
                </div>

                {{-- Footer meta (same pattern as other chartings) --}}
                <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                  <p class="text-xs text-slate-500">
                    Performed
                    <span class="font-medium text-slate-700">
                      {{ $performedAt ?? '—' }}
                    </span>
                  </p>
                  @if($performedBy)
                    <p class="text-xs text-slate-400">
                      Performed by:
                      <span class="font-semibold text-slate-600">{{ $performedBy }}</span>
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
