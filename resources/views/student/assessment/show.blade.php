<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>{{ $guide->title }} · Assessment Guide · NurSync</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; }
  </style>
</head>
<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Student sidebar --}}
  @include('partials.sidebar', ['active' => 'assessment_guides'])

  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-6 lg:px-10 py-10 space-y-6">

      {{-- Header --}}
      <header class="flex items-start justify-between gap-4">
        <div class="flex items-start gap-3">
          <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
            <i data-lucide="clipboard-list" class="h-5 w-5"></i>
          </span>
          <div>
            <h1 class="text-[22px] sm:text-[24px] font-extrabold tracking-tight text-slate-900">
              {{ $guide->title }}
            </h1>
            @if($guide->summary)
              <p class="mt-1 text-[13px] text-slate-600">
                {{ $guide->summary }}
              </p>
            @endif
            <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px] text-slate-500">
              <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5">
                <i data-lucide="check-circle-2" class="h-3 w-3"></i>
                Published guide
              </span>
              @if($guide->updated_at)
                <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5">
                  <i data-lucide="clock" class="h-3 w-3"></i>
                  Updated {{ $guide->updated_at->diffForHumans() }}
                </span>
              @endif
            </div>
          </div>
        </div>

        <a href="{{ route('student.assessment.index') }}"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-[13px] font-medium text-slate-700 hover:bg-slate-50">
          <i data-lucide="arrow-left" class="h-4 w-4"></i>
          Back to guides
        </a>
      </header>

      {{-- Content --}}
      <div class="grid gap-6 lg:grid-cols-2">
        {{-- Left: Evaluation + documentation --}}
        <div class="space-y-6">
          @if($guide->content_rubric)
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
              <h2 class="text-sm font-semibold text-slate-900 flex items-center gap-2 mb-2">
                <i data-lucide="clipboard-check" class="h-4 w-4 text-emerald-600"></i>
                How nurses are evaluated in the field
              </h2>
              <p class="text-[13px] leading-relaxed text-slate-700 whitespace-pre-line">
                {{ $guide->content_rubric }}
              </p>
            </section>
          @endif

          @if($guide->content_documentation)
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
              <h2 class="text-sm font-semibold text-slate-900 flex items-center gap-2 mb-2">
                <i data-lucide="file-pen-line" class="h-4 w-4 text-emerald-600"></i>
                DAR / SOAP / PIE documentation
              </h2>
              <p class="text-[13px] leading-relaxed text-slate-700 whitespace-pre-line">
                {{ $guide->content_documentation }}
              </p>
            </section>
          @endif
        </div>

        {{-- Right: tips + mistakes --}}
        <div class="space-y-6">
          @if($guide->content_tips)
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
              <h2 class="text-sm font-semibold text-slate-900 flex items-center gap-2 mb-2">
                <i data-lucide="lightbulb" class="h-4 w-4 text-amber-500"></i>
                Tips from practicing nurses
              </h2>
              <p class="text-[13px] leading-relaxed text-slate-700 whitespace-pre-line">
                {{ $guide->content_tips }}
              </p>
            </section>
          @endif

          @if($guide->content_mistakes)
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
              <h2 class="text-sm font-semibold text-slate-900 flex items-center gap-2 mb-2">
                <i data-lucide="alert-triangle" class="h-4 w-4 text-rose-500"></i>
                Common mistakes & unsafe practices
              </h2>
              <p class="text-[13px] leading-relaxed text-slate-700 whitespace-pre-line">
                {{ $guide->content_mistakes }}
              </p>
            </section>
          @endif
        </div>
      </div>

    </div>
  </section>
</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>
</body>
</html>
