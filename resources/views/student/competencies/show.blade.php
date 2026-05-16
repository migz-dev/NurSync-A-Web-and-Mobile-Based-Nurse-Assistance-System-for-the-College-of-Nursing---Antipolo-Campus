{{-- resources/views/student/competencies/show.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>{{ $item->title }} · Competency Requirement · NurSync</title>

  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body { font-family:'Poppins',ui-sans-serif,system-ui,sans-serif }

    /* Same slide-in animation used on index page */
    @keyframes slide-in-up {
      from { transform: translateY(10px); opacity: 0; }
      to   { transform: translateY(0);     opacity: 1; }
    }
    .animate-card-in {
      animation: slide-in-up .35s ease-out both;
      will-change: transform, opacity;
    }
  </style>
</head>
<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  @include('partials.sidebar', ['active' => 'competency_requirements'])

  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-6 lg:px-10 py-10 space-y-6">

      {{-- Skeleton loader (same feel as index page) --}}
      <div id="skeletonShow" aria-hidden="true" class="space-y-6">
        {{-- Header skeleton --}}
        <div class="flex items-start justify-between gap-4">
          <div class="space-y-3">
            <div class="h-6 w-32 rounded-full bg-slate-200 animate-pulse"></div>
            <div class="h-7 w-64 rounded bg-slate-200 animate-pulse"></div>
            <div class="flex gap-2 mt-1">
              <div class="h-6 w-28 rounded-full bg-slate-200 animate-pulse"></div>
              <div class="h-6 w-40 rounded-full bg-slate-100 animate-pulse"></div>
            </div>
          </div>
          <div class="h-8 w-8 rounded-xl bg-slate-200 animate-pulse"></div>
        </div>

        {{-- Body skeleton --}}
        <div class="grid gap-6 lg:grid-cols-3">
          <div class="lg:col-span-2 space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
              <div class="h-4 w-48 rounded bg-slate-200 mb-3 animate-pulse"></div>
              <div class="space-y-2">
                <div class="h-3 w-full rounded bg-slate-100 animate-pulse"></div>
                <div class="h-3 w-5/6 rounded bg-slate-100 animate-pulse"></div>
                <div class="h-3 w-4/6 rounded bg-slate-100 animate-pulse"></div>
              </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5">
              <div class="h-4 w-56 rounded bg-slate-200 mb-3 animate-pulse"></div>
              <div class="space-y-2">
                <div class="h-3 w-full rounded bg-slate-100 animate-pulse"></div>
                <div class="h-3 w-5/6 rounded bg-slate-100 animate-pulse"></div>
                <div class="h-3 w-3/5 rounded bg-slate-100 animate-pulse"></div>
              </div>
            </div>

            {{-- Nurse explanations skeleton block (optional feel) --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
              <div class="h-4 w-40 rounded bg-slate-200 mb-3 animate-pulse"></div>
              <div class="space-y-2">
                <div class="h-3 w-full rounded bg-slate-100 animate-pulse"></div>
                <div class="h-3 w-4/5 rounded bg-slate-100 animate-pulse"></div>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
              <div class="h-4 w-52 rounded bg-slate-200 mb-3 animate-pulse"></div>
              <div class="space-y-2">
                <div class="h-3 w-full rounded bg-slate-100 animate-pulse"></div>
                <div class="h-3 w-5/6 rounded bg-slate-100 animate-pulse"></div>
                <div class="h-3 w-3/4 rounded bg-slate-100 animate-pulse"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Actual content (hidden first, then animated in) --}}
      <div id="contentShow" class="space-y-6 opacity-0 hidden">
        {{-- Header --}}
        <header class="flex items-start justify-between gap-4">
          <div>
            <button type="button"
                    onclick="window.location.href='{{ route('student.competencies.index') }}'"
                    class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs text-slate-700 hover:bg-slate-50 mb-3">
              <i data-lucide="chevron-left" class="h-3 w-3"></i>
              Back to list
            </button>

            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
              {{ $item->title }}
            </h1>

            <div class="mt-2 flex flex-wrap gap-2 items-center">
              @if($item->category)
                <span class="inline-flex items-center rounded-full bg-slate-900 text-white text-[11px] px-3 py-1">
                  {{ $item->category->title }}
                </span>
              @endif
              <span class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 text-[11px] px-3 py-1 border border-emerald-100">
                View-only competency standard
              </span>
            </div>
          </div>

          <i data-lucide="badge-check" class="h-8 w-8 text-emerald-500"></i>
        </header>

        <div class="grid gap-6 lg:grid-cols-3">
          {{-- Left: description & reason --}}
          <div class="lg:col-span-2 space-y-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-5">
              <h2 class="text-sm font-semibold text-slate-900 mb-2">
                What this competency means in practice
              </h2>

              @if($item->description)
                <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                  {{ $item->description }}
                </p>
              @else
                <p class="text-sm text-slate-500">
                  Your instructor hasn’t added a detailed description yet.
                </p>
              @endif
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5">
              <h2 class="text-sm font-semibold text-slate-900 mb-2">
                Why nurses must master this competency
              </h2>

              @if($item->reason)
                <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                  {{ $item->reason }}
                </p>
              @else
                <p class="text-sm text-slate-500">
                  No explanation provided yet.
                </p>
              @endif
            </section>

            @if($item->explanations && $item->explanations->count())
              <section class="rounded-2xl border border-slate-200 bg-white p-5 space-y-4">
                <div class="flex items-center gap-2">
                  <i data-lucide="sparkles" class="h-4 w-4 text-amber-500"></i>
                  <h2 class="text-sm font-semibold text-slate-900">
                    Nurse explanations
                  </h2>
                </div>

                <p class="text-[11px] text-slate-500">
                  These are written in the voice of your instructors or practicing nurses so you understand how this looks at the bedside.
                </p>

                <div class="space-y-3">
                  @foreach($item->explanations as $exp)
                    <article class="rounded-xl border border-slate-100 bg-slate-50/80 px-4 py-3">
                      @if($exp->title)
                        <h3 class="text-xs font-semibold text-slate-800 mb-1">
                          {{ $exp->title }}
                        </h3>
                      @endif
                      <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                        {{ $exp->content }}
                      </p>
                    </article>
                  @endforeach
                </div>
              </section>
            @endif
          </div>

          {{-- Right: compact “impact” card --}}
          <aside class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
              <h2 class="text-sm font-semibold text-slate-900 mb-2">
                How this affects your future as a nurse
              </h2>
              <p class="text-xs text-slate-600 leading-relaxed">
                Competencies like this often appear in:
              </p>
              <ul class="mt-2 space-y-1.5 text-xs text-slate-700 list-disc list-inside">
                <li>RLE performance evaluation</li>
                <li>OSCE / return demonstrations</li>
                <li>Staff nurse probation and orientation</li>
                <li>Patient safety and quality indicators</li>
              </ul>
              <p class="mt-3 text-[11px] text-slate-500">
                Use these requirements as a checklist of what you should be confident doing before graduation.
              </p>
            </div>
          </aside>
        </div>
      </div> {{-- /contentShow --}}
    </div>
  </section>
</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();

  const skeletonShow = document.getElementById('skeletonShow');
  const contentShow  = document.getElementById('contentShow');

  function toggleSkeleton(show) {
    if (!skeletonShow || !contentShow) return;
    skeletonShow.classList.toggle('hidden', !show);
    contentShow.classList.toggle('hidden', show);
  }

  document.addEventListener('DOMContentLoaded', () => {
    // Start with skeleton visible (already default), then fade/slide in content
    const delay = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 250;

    setTimeout(() => {
      toggleSkeleton(false);
      // Apply animation
      contentShow.classList.remove('opacity-0');
      contentShow.classList.add('animate-card-in');
    }, delay);
  });
</script>
</body>
</html>
