{{-- resources/views/faculty/chartings/allergies-index.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Allergies & Reactions · NurSync (CI)</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
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
  {{-- Sidebar --}}
  @include('partials.faculty-sidebar', ['active' => 'chartings'])

  {{-- Main --}}
  <section class="flex-1">
    <div class="container mx-auto px-8 py-10 space-y-6">

      {{-- HEADER (title + description on LEFT, Back button on RIGHT) --}}
      <header class="flex items-center justify-between gap-3">
        {{-- LEFT: Icon + title + description --}}
        <div class="flex items-center gap-3">
          <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-red-50">
            <i data-lucide="alert-triangle" class="h-5 w-5 text-red-600"></i>
          </span>
          <div>
            <h1 class="text-[22px] sm:text-[24px] font-extrabold tracking-tight text-slate-900">
              Allergies &amp; Reactions
            </h1>
            <p class="text-[12px] text-slate-500 max-w-md">
              Log and review allergens, reactions, severity, and management for this patient.
            </p>
          </div>
        </div>

        {{-- RIGHT: Back button --}}
        <a href="{{ route('faculty.chartings.patient', $patient) }}"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-300 text-slate-700 px-4 py-2 text-sm hover:bg-slate-50 transition">
          <i data-lucide="arrow-left" class="h-4 w-4"></i>
          <span>Back to Patient</span>
        </a>
      </header>

      {{-- Patient banner --}}
      @include('faculty.chartings._patient-banner')

      {{-- Records list --}}
      @if($records->isEmpty())
        @include('faculty.chartings._empty-state', [
          'icon' => 'alert-triangle',
          'title' => 'No allergy records yet',
          'message' => 'Use “New record” from the patient hub to document allergies and adverse reactions.'
        ])
      @else
        <section aria-label="Allergy & reaction records" class="space-y-3">
          @foreach($records as $row)
            @php
              /** @var \App\Models\ChartingAllergy $row */
              $date = $row->date_observed ?? $row->created_at;
              $dateLabel = $date
                  ? \Carbon\Carbon::parse($date)->format('M d, Y')
                  : 'Date not set';

              $severity = trim((string) $row->severity);
              $severityLabel = $severity ?: 'Unspecified';
              $severityColor = 'bg-slate-100 text-slate-700';

              if (strcasecmp($severity, 'Mild') === 0) {
                  $severityColor = 'bg-emerald-50 text-emerald-700';
              } elseif (strcasecmp($severity, 'Moderate') === 0) {
                  $severityColor = 'bg-amber-50 text-amber-700';
              } elseif (strcasecmp($severity, 'Severe') === 0) {
                  $severityColor = 'bg-orange-50 text-orange-700';
              } elseif (strcasecmp($severity, 'Anaphylaxis') === 0) {
                  $severityColor = 'bg-red-50 text-red-700';
              }
            @endphp

            <details
              class="group rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow animate-card-in">

              <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4">
                <div class="flex items-start gap-3 min-w-0">
                  <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-red-50">
                    <i data-lucide="alert-triangle" class="h-4 w-4 text-red-600"></i>
                  </span>
                  <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                      <p class="text-sm font-semibold text-slate-900 truncate">
                        {{ $row->allergen ?? 'Unspecified allergen' }}
                      </p>
                      <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-semibold {{ $severityColor }}">
                        {{ $severityLabel }}
                      </span>
                    </div>
                    <p class="mt-0.5 text-[11px] text-slate-500">
                      {{ $dateLabel }}
                    </p>
                    @if($row->reaction)
                      <p class="mt-1 text-xs text-slate-500 line-clamp-1">
                        {{ $row->reaction }}
                      </p>
                    @endif
                  </div>
                </div>

                <i data-lucide="chevron-down"
                   class="h-5 w-5 shrink-0 text-slate-400 transition-transform duration-200 group-open:rotate-180"></i>
              </summary>

              {{-- DETAILS --}}
              <div class="px-5 pb-5 pt-3 border-t border-slate-100 text-sm text-slate-700 space-y-3 bg-slate-50/60">
                <div class="grid gap-3 md:grid-cols-2">
                  <div class="space-y-1">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                      Allergen / Trigger
                    </p>
                    <p class="text-sm text-slate-900">
                      {{ $row->allergen ?? 'Unspecified' }}
                    </p>
                  </div>

                  <div class="space-y-1">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                      Severity
                    </p>
                    <p class="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-semibold {{ $severityColor }}">
                      {{ $severityLabel }}
                    </p>
                  </div>

                  <div class="space-y-1">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                      Date observed
                    </p>
                    <p class="text-sm text-slate-900">
                      {{ $dateLabel }}
                    </p>
                  </div>
                </div>

                @if($row->reaction)
                  <div class="space-y-1">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                      Reaction
                    </p>
                    <p class="text-sm text-slate-800 whitespace-pre-line">
                      {{ $row->reaction }}
                    </p>
                  </div>
                @endif

                @if($row->action_taken)
                  <div class="space-y-1">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                      Actions taken
                    </p>
                    <p class="text-sm text-slate-800 whitespace-pre-line">
                      {{ $row->action_taken }}
                    </p>
                  </div>
                @endif

                @if($row->notes)
                  <div class="space-y-1">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                      Notes
                    </p>
                    <p class="text-sm text-slate-800 whitespace-pre-line">
                      {{ $row->notes }}
                    </p>
                  </div>
                @endif

                {{-- Footer meta --}}
                <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                  <p class="text-xs text-slate-500">
                    Recorded:
                    <span class="font-medium text-slate-700">
                      {{ $dateLabel }}
                    </span>
                  </p>
                  @if($severityLabel)
                    <p class="text-xs text-slate-400">
                      Severity:
                      <span class="font-semibold text-slate-600">
                        {{ $severityLabel }}
                      </span>
                    </p>
                  @endif
                </div>
              </div>
            </details>
          @endforeach
        </section>
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
