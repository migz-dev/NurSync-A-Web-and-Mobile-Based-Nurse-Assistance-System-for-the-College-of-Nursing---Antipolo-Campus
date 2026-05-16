<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Return Demos · NurSync</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
  <main class="min-h-screen flex">
    {{-- Sidebar --}}
    @php($active = 'rd')
    @include('partials.sidebar')

    {{-- Main content --}}
    <section class="flex-1">
      <div class="container mx-auto px-8 py-12">
        <div class="space-y-6">

          {{-- Header --}}
          <header>
            <h1 class="text-[32px] font-extrabold tracking-tight text-slate-900">
              Return Demos
            </h1>
            <p class="mt-2 text-sm text-slate-500">
              View your assigned return demonstration schedule, track results, and prepare with guides.
            </p>
          </header>

          {{-- Tabs (Schedule / Results) --}}
          <div class="flex items-center gap-2 border-b border-slate-200/70">
            <a href="#" class="px-4 py-2 text-sm font-medium text-slate-900 border-b-2 border-slate-900">
              Schedule
            </a>
            <a href="#" class="px-4 py-2 text-sm font-medium text-slate-500 hover:text-slate-900">
              Results
            </a>
          </div>

          {{-- Upcoming sessions (cards) --}}
          <div class="grid gap-6 md:grid-cols-2">
            <article class="rounded-xl border border-slate-200/70 bg-white p-5">
              <div class="flex items-start justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Vital Signs Demo</h3>
                <span
                  class="rounded-full bg-emerald-100 text-emerald-700 text-[11px] font-semibold px-2 py-1">Scheduled</span>
              </div>
              <p class="mt-1 text-sm text-slate-600">
                Clinical Instructor: <span class="font-medium">Prof. Angeles</span>
              </p>
              <div class="mt-3 flex items-center gap-5 text-xs text-slate-500">
                <span class="inline-flex items-center gap-1">
                  <i data-lucide="calendar" class="h-3.5 w-3.5"></i> Oct 15, 2025
                </span>
                <span class="inline-flex items-center gap-1">
                  <i data-lucide="clock" class="h-3.5 w-3.5"></i> 9:00 AM
                </span>
                <span class="inline-flex items-center gap-1">
                  <i data-lucide="map-pin" class="h-3.5 w-3.5"></i> Skills Lab 2
                </span>
              </div>
              <div class="mt-4">
                <a href="/student/procedures/vital-signs"
                  class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                  Review Procedure
                </a>
              </div>
            </article>

            <article class="rounded-xl border border-slate-200/70 bg-white p-5">
              <div class="flex items-start justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Hand Hygiene Demo</h3>
                <span
                  class="rounded-full bg-amber-100 text-amber-700 text-[11px] font-semibold px-2 py-1">Pending</span>
              </div>
              <p class="mt-1 text-sm text-slate-600">
                Clinical Instructor: <span class="font-medium">Prof. Alipio</span>
              </p>
              <div class="mt-3 flex items-center gap-5 text-xs text-slate-500">
                <span class="inline-flex items-center gap-1">
                  <i data-lucide="calendar" class="h-3.5 w-3.5"></i> Oct 20, 2025
                </span>
                <span class="inline-flex items-center gap-1">
                  <i data-lucide="clock" class="h-3.5 w-3.5"></i> 2:00 PM
                </span>
                <span class="inline-flex items-center gap-1">
                  <i data-lucide="map-pin" class="h-3.5 w-3.5"></i> Skills Lab 1
                </span>
              </div>
              <div class="mt-4">
                <a href="/student/procedures/hand-hygiene"
                  class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                  Review Procedure
                </a>
              </div>
            </article>
          </div>

          {{-- Footer note --}}
          <div class="rounded-xl border border-slate-200/70 bg-white p-5">
            <div class="flex items-start gap-3">
              <i data-lucide="info" class="h-5 w-5 text-slate-500 mt-0.5"></i>
              <p class="text-[13px] leading-6 text-slate-600">
                Return Demo schedules are for <span class="font-medium">training purposes only</span>.
                Always check announcements for changes or updates from your Clinical Instructor.
              </p>
            </div>
          </div>

        </div>
      </div>
    </section>
  </main>

  

  @include('partials.student-footer')

  <script src="https://unpkg.com/lucide@latest"></script>
  <script> lucide.createIcons(); </script>
</body>

</html>