<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>My Schedule · NurSync – Nurse Assistance</title>

  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style> body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; } </style>
</head>
<body class="min-h-screen bg-slate-50">
  <main class="min-h-screen flex">
    {{-- Sidebar --}}
    @php($active = 'schedule')
    @include('partials.sidebar')

    {{-- Main content --}}
    <section class="flex-1 px-8 py-10">
      <!-- Header -->
      <div class="flex items-center gap-3">
        <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-slate-100 text-slate-700">
          <i data-lucide="calendar" class="h-5 w-5"></i>
        </span>
        <h1 class="text-2xl font-bold">My Schedule</h1>
      </div>
      <p class="text-[13px] text-slate-500 mt-1">
        View your assigned Return Demo sessions. <span class="font-medium text-slate-700">View-only access.</span>
      </p>

      {{-- Filters (client-side only for now) --}}
      <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-end">
        <div class="flex-1">
          <label class="block text-[12px] text-slate-600 mb-1">Search</label>
          <input id="srch" type="text" placeholder="Search skill, CI, room..."
                 class="w-full rounded-xl border px-3 py-2 text-[14px] outline-none focus:ring-2 focus:ring-slate-300">
        </div>
        <div>
          <label class="block text-[12px] text-slate-600 mb-1">Status</label>
          <select id="status" class="rounded-xl border px-3 py-2 text-[14px]">
            <option value="">All</option>
            <option value="upcoming">Upcoming</option>
            <option value="done">Completed</option>
          </select>
        </div>
      </div>

      {{-- Table --}}
      <div class="mt-4 rounded-2xl border bg-white overflow-hidden">
        <div class="px-5 py-3 border-b bg-slate-50/50 text-[12px] text-slate-600">Assigned Sessions</div>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-slate-50">
              <tr class="text-left text-slate-600">
                <th class="px-5 py-3">Date</th>
                <th class="px-5 py-3">Time</th>
                <th class="px-5 py-3">Skill</th>
                <th class="px-5 py-3">Clinical Instructor</th>
                <th class="px-5 py-3">Lab / Room</th>
                <th class="px-5 py-3">Status</th>
              </tr>
            </thead>
            <tbody id="schedBody" class="divide-y">
              {{-- Placeholder rows; replace with real data later --}}
              <tr data-status="upcoming">
                <td class="px-5 py-3 whitespace-nowrap">—</td>
                <td class="px-5 py-3 whitespace-nowrap">—</td>
                <td class="px-5 py-3">Basic IV Insertion</td>
                <td class="px-5 py-3">—</td>
                <td class="px-5 py-3">—</td>
                <td class="px-5 py-3">
                  <span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2 py-0.5 text-[12px]">Upcoming</span>
                </td>
              </tr>
              <tr data-status="done">
                <td class="px-5 py-3 whitespace-nowrap">—</td>
                <td class="px-5 py-3 whitespace-nowrap">—</td>
                <td class="px-5 py-3">Medication Administration</td>
                <td class="px-5 py-3">—</td>
                <td class="px-5 py-3">—</td>
                <td class="px-5 py-3">
                  <span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[12px]">Completed</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        {{-- Empty state (hidden until JS shows it) --}}
        <div id="emptyState" class="hidden px-5 py-8 text-center text-[13px] text-slate-500">
          No sessions match your filters.
        </div>
      </div>

      {{-- Quick links --}}
      <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <a href="{{ \Illuminate\Support\Facades\Route::has('student.rd.results') ? route('student.rd.results') : url('/student/rd/results') }}"
           class="rounded-2xl border bg-white p-5 hover:bg-slate-50 transition">
          <div class="flex items-center gap-2 text-[13px] font-medium text-slate-700">
            <i data-lucide="file-check-2" class="h-4 w-4 text-slate-500"></i> View Published Results
          </div>
          <p class="mt-1 text-[12px] text-slate-500">See scores and rubric breakdown</p>
        </a>

        <a href="{{ \Illuminate\Support\Facades\Route::has('student.procedures.index') ? route('student.procedures.index') : url('/student/procedures') }}"
           class="rounded-2xl border bg-white p-5 hover:bg-slate-50 transition">
          <div class="flex items-center gap-2 text-[13px] font-medium text-slate-700">
            <i data-lucide="library" class="h-4 w-4 text-slate-500"></i> Procedures Library
          </div>
          <p class="mt-1 text-[12px] text-slate-500">Step-by-step guides & videos</p>
        </a>
      </div>
    </section>
  </main>

  @include('partials.student-footer')

  <script src="https://unpkg.com/lucide@latest"></script>
  <script> lucide.createIcons(); </script>
  <script>
    // Tiny client-side filter demo (remove or replace when you bind real data)
    const q = document.getElementById('srch');
    const status = document.getElementById('status');
    const rows = [...document.querySelectorAll('#schedBody tr')];
    const empty = document.getElementById('emptyState');

    function applyFilters(){
      const term = (q.value || '').toLowerCase();
      const st = status.value;
      let shown = 0;
      rows.forEach(tr => {
        const hay = tr.innerText.toLowerCase();
        const okTerm = !term || hay.includes(term);
        const okStatus = !st || tr.dataset.status === st;
        const show = okTerm && okStatus;
        tr.style.display = show ? '' : 'none';
        if (show) shown++;
      });
      empty.classList.toggle('hidden', shown !== 0);
    }
    q.addEventListener('input', applyFilters);
    status.addEventListener('change', applyFilters);
    applyFilters();
  </script>
</body>
</html>
