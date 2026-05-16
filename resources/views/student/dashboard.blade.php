<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Student Nurse · NurSync – Nurse Assistance</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style> body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; } </style>
</head>
<body class="min-h-screen bg-slate-50">
  <main class="min-h-screen flex">
    {{-- Sidebar (unchanged / final) --}}
    @php($active = 'dashboard')
    @include('partials.sidebar')

    {{-- Main content --}}
    <section class="flex-1">
      <div class="container mx-auto px-8 py-12">
        <div class="space-y-6">
          <!-- Header -->
          <header>
            <div class="flex items-center gap-3">
              <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                <i data-lucide="stethoscope" class="h-4 w-4"></i>
              </span>
              <h1 class="text-[28px] font-extrabold leading-tight tracking-tight text-slate-900">
                My Dashboard
              </h1>
            </div>
            <p class="mt-2 text-sm text-slate-500">
              View and manage your Return Demo activities, results, and learning resources.
              <span class="font-medium text-slate-700">View-only access.</span>
            </p>
          </header>

          <!-- Top metrics -->
          <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-2xl border border-slate-200/70 bg-white p-5">
              <div class="text-[13px] font-medium text-slate-700 flex items-center gap-2">
                <i data-lucide="calendar-clock" class="h-4 w-4 text-slate-500"></i> Assigned Sessions
              </div>
              <div class="mt-2 text-3xl font-bold">0</div>
              <p class="mt-1 text-[12px] text-slate-500">Upcoming Return Demos</p>
            </div>

            <div class="rounded-2xl border border-slate-200/70 bg-white p-5">
              <div class="text-[13px] font-medium text-slate-700 flex items-center gap-2">
                <i data-lucide="trophy" class="h-4 w-4 text-slate-500"></i> Highest Score
              </div>
              <div class="mt-2 text-3xl font-bold">0%</div>
              <p class="mt-1 text-[12px] text-slate-500">Best performance</p>
            </div>

            <div class="rounded-2xl border border-slate-200/70 bg-white p-5">
              <div class="text-[13px] font-medium text-slate-700 flex items-center gap-2">
                <i data-lucide="target" class="h-4 w-4 text-slate-500"></i> Average Score
              </div>
              <div class="mt-2 text-3xl font-bold">0%</div>
              <p class="mt-1 text-[12px] text-slate-500">Across published results</p>
            </div>
          </div>

          <!-- Empty state (always shown in static UI) -->
          <div class="rounded-2xl border border-slate-200/70 bg-white p-10">
            <div class="mx-auto max-w-md text-center">
              <div class="mx-auto mb-4 inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                <i data-lucide="ribbon" class="h-6 w-6 text-slate-600"></i>
              </div>
              <h2 class="text-lg font-semibold text-slate-900">No activities yet</h2>
              <p class="mt-1 text-sm text-slate-500">
                When your Clinical Instructor assigns a Return Demo or publishes a result, it will appear here.
              </p>
              <div class="mt-6">
                <a href="/student/rd/schedule"
                   class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:opacity-95">
                  <i data-lucide="calendar-days" class="h-4 w-4"></i>
                  View Schedule
                </a>
              </div>
            </div>
          </div>

          <!-- Quick links -->
          <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <a href="/student/rd/schedule" class="rounded-2xl border border-slate-200/70 bg-white p-5 hover:bg-slate-50 transition">
              <div class="flex items-center gap-2 text-[13px] font-medium text-slate-700">
                <i data-lucide="calendar-days" class="h-4 w-4 text-slate-500"></i> Return Demo Schedule
              </div>
              <p class="mt-1 text-[12px] text-slate-500">Dates, skill, CI & location</p>
            </a>

            <a href="/student/rd/results" class="rounded-2xl border border-slate-200/70 bg-white p-5 hover:bg-slate-50 transition">
              <div class="flex items-center gap-2 text-[13px] font-medium text-slate-700">
                <i data-lucide="file-check-2" class="h-4 w-4 text-slate-500"></i> Published Results
              </div>
              <p class="mt-1 text-[12px] text-slate-500">Final scores & printable sheet</p>
            </a>

            <a href="/student/procedures" class="rounded-2xl border border-slate-200/70 bg-white p-5 hover:bg-slate-50 transition">
              <div class="flex items-center gap-2 text-[13px] font-medium text-slate-700">
                <i data-lucide="library" class="h-4 w-4 text-slate-500"></i> Procedures Library
              </div>
              <p class="mt-1 text-[12px] text-slate-500">Step-by-step guides & videos</p>
            </a>

            <a href="/student/notifications" class="rounded-2xl border border-slate-200/70 bg-white p-5 hover:bg-slate-50 transition">
              <div class="flex items-center gap-2 text-[13px] font-medium text-slate-700">
                <i data-lucide="bell-ring" class="h-4 w-4 text-slate-500"></i> Notifications
              </div>
              <p class="mt-1 text-[12px] text-slate-500">Session changes & announcements</p>
            </a>
          </div>

          <!-- Helpful resources -->
          <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <a href="/student/feedback" class="rounded-2xl border border-slate-200/70 bg-white p-5 hover:bg-slate-50 transition">
              <div class="flex items-center gap-2 text-[13px] font-medium text-slate-700">
                <i data-lucide="message-square" class="h-4 w-4 text-slate-500"></i> Feedback (View Status)
              </div>
              <p class="mt-1 text-[12px] text-slate-500">Track submitted feedback responses</p>
            </a>

            <a href="/student/tools" class="rounded-2xl border border-slate-200/70 bg-white p-5 hover:bg-slate-50 transition">
              <div class="flex items-center gap-2 text-[13px] font-medium text-slate-700">
                <i data-lucide="tool" class="h-4 w-4 text-slate-500"></i> Training Tools
              </div>
              <p class="mt-1 text-[12px] text-slate-500">Timers & checklists (simulation)</p>
            </a>

            <a href="/student/settings" class="rounded-2xl border border-slate-200/70 bg-white p-5 hover:bg-slate-50 transition">
              <div class="flex items-center gap-2 text-[13px] font-medium text-slate-700">
                <i data-lucide="settings" class="h-4 w-4 text-slate-500"></i> Settings
              </div>
              <p class="mt-1 text-[12px] text-slate-500">Profile & preferences</p>
            </a>
          </div>

          <!-- Campus notice -->
          <div class="rounded-2xl border border-slate-200/70 bg-white p-5">
            <div class="flex items-start gap-3">
              <i data-lucide="shield-alert" class="h-5 w-5 text-slate-500 mt-0.5"></i>
              <p class="text-[13px] leading-6 text-slate-600">
                <span class="font-semibold text-slate-800">Note:</span> This Nurse Assistance System is for campus training and
                simulation. No real patient data is stored. All student access is <span class="font-medium">view-only</span>.
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
