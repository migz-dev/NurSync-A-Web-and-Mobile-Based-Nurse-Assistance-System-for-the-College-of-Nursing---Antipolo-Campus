{{-- resources/views/admin/admin-return-demo.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Admin • Return Demo · NurSync</title>
      <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style> body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; } </style>
</head>
<body class="min-h-screen bg-slate-50">

  <main class="min-h-screen flex">
    {{-- Sidebar --}}
    @include('partials.admin-sidebar', ['active' => 'return-demo'])

    {{-- Main --}}
    <section class="flex-1 min-w-0">
      {{-- Header --}}
      <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-900 text-white shadow-sm">
              <i data-lucide="clipboard-check" class="h-4 w-4"></i>
            </div>
            <div>
              <h1 class="text-[15px] sm:text-[16px] font-semibold leading-tight">Return Demo</h1>
              <p class="text-[12px] text-slate-500 -mt-0.5">Schedule and track return demonstration assessments.</p>
            </div>
          </div>

          {{-- Primary action --}}
          <div class="flex items-center gap-2">
            <button type="button"
              class="inline-flex items-center gap-2 rounded-xl bg-green-600 text-white px-3 py-2 text-[13px] font-medium shadow hover:bg-green-700 active:scale-[.99]"
              data-modal-target="modalCreate">
              <i data-lucide="plus" class="h-4 w-4"></i>
              <span>Create</span>
            </button>
          </div>
        </div>
      </header>

      {{-- Content --}}
      <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">

        {{-- Filters / tools --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
          <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
            <div class="flex items-center gap-3 w-full sm:w-auto">
              <div class="relative flex-1 sm:w-72">
                <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
                <input type="text" placeholder="Search student, CI, skill..."
                       class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300">
              </div>

              <select class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                <option value="">All skills</option>
                <option>IV Insertion</option>
                <option>Vital Signs</option>
                <option>Wound Dressing</option>
              </select>

              <select class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                <option value="">All status</option>
                <option selected>Scheduled</option>
                <option>Completed</option>
                <option>Failed</option>
                <option>Archived</option>
              </select>
            </div>

            <div class="flex items-center gap-2">
              <button class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 px-3 py-2 text-[13px] hover:bg-slate-50">
                <i data-lucide="download" class="h-4 w-4"></i>
                Export
              </button>
              <button class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 px-3 py-2 text-[13px] hover:bg-slate-50">
                <i data-lucide="settings-2" class="h-4 w-4"></i>
                Columns
              </button>
            </div>
          </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-slate-50 border-b border-slate-200">
                <tr class="text-left text-slate-600">
                  <th class="px-4 py-3 w-10">
                    <input type="checkbox" class="rounded border-slate-300">
                  </th>
                  <th class="px-4 py-3">Student</th>
                  <th class="px-4 py-3">Offering</th>
                  <th class="px-4 py-3">Skill / Procedure</th>
                  <th class="px-4 py-3">CI</th>
                  <th class="px-4 py-3">Date</th>
                  <th class="px-4 py-3">Time</th>
                  <th class="px-4 py-3">Status</th>
                  <th class="px-4 py-3 text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-200">
                {{-- Row 1 --}}
                <tr class="hover:bg-slate-50">
                  <td class="px-4 py-3"><input type="checkbox" class="rounded border-slate-300"></td>
                  <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                      <div class="h-9 w-9 rounded-xl bg-slate-100 flex items-center justify-center">
                        <i data-lucide="user" class="h-4 w-4 text-slate-500"></i>
                      </div>
                      <div>
                        <div class="font-medium text-slate-900">Alexa Domingo</div>
                        <div class="text-[12px] text-slate-500">ID: 100236</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-3 text-slate-700">NURS101</td>
                  <td class="px-4 py-3 text-slate-700">Vital Signs</td>
                  <td class="px-4 py-3 text-slate-700">Joseph S. Alipio</td>
                  <td class="px-4 py-3 text-slate-700">Sep 30, 2025</td>
                  <td class="px-4 py-3 text-slate-700">10:00–10:30</td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 text-amber-700 px-2 py-1 text-[12px] font-medium">
                      <span class="h-2 w-2 rounded-full bg-amber-500"></span> Scheduled
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-1.5">
                      <button type="button" class="inline-flex items-center justify-center rounded-lg bg-blue-600 text-white p-2 hover:bg-blue-700" data-modal-target="modalView">
                        <i data-lucide="eye" class="h-4 w-4"></i>
                      </button>
                      <button type="button" class="inline-flex items-center justify-center rounded-lg bg-yellow-400 text-slate-900 p-2 hover:brightness-95" data-modal-target="modalEdit">
                        <i data-lucide="pencil" class="h-4 w-4"></i>
                      </button>
                      <button type="button" class="inline-flex items-center justify-center rounded-lg bg-orange-500 text-white p-2 hover:bg-orange-600" data-modal-target="modalArchive">
                        <i data-lucide="archive" class="h-4 w-4"></i>
                      </button>
                    </div>
                  </td>
                </tr>

                {{-- Row 2 --}}
                <tr class="hover:bg-slate-50">
                  <td class="px-4 py-3"><input type="checkbox" class="rounded border-slate-300"></td>
                  <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                      <div class="h-9 w-9 rounded-xl bg-slate-100 flex items-center justify-center">
                        <i data-lucide="user" class="h-4 w-4 text-slate-500"></i>
                      </div>
                      <div>
                        <div class="font-medium text-slate-900">Miguel Caluya</div>
                        <div class="text-[12px] text-slate-500">ID: 100234</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-3 text-slate-700">COMM210</td>
                  <td class="px-4 py-3 text-slate-700">Wound Dressing</td>
                  <td class="px-4 py-3 text-slate-700">Amparo M. Angeles</td>
                  <td class="px-4 py-3 text-slate-700">Sep 29, 2025</td>
                  <td class="px-4 py-3 text-slate-700">13:00–13:45</td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 text-emerald-700 px-2 py-1 text-[12px] font-medium">
                      <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Completed
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-1.5">
                      <button type="button" class="inline-flex items-center justify-center rounded-lg bg-blue-600 text-white p-2 hover:bg-blue-700" data-modal-target="modalView">
                        <i data-lucide="eye" class="h-4 w-4"></i>
                      </button>
                      <button type="button" class="inline-flex items-center justify-center rounded-lg bg-yellow-400 text-slate-900 p-2 hover:brightness-95" data-modal-target="modalEdit">
                        <i data-lucide="pencil" class="h-4 w-4"></i>
                      </button>
                      <button type="button" class="inline-flex items-center justify-center rounded-lg bg-orange-500 text-white p-2 hover:bg-orange-600" data-modal-target="modalArchive">
                        <i data-lucide="archive" class="h-4 w-4"></i>
                      </button>
                    </div>
                  </td>
                </tr>

              </tbody>
            </table>
          </div>

          {{-- Footer / pagination --}}
          <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 border-t border-slate-200 bg-slate-50">
            <div class="flex items-center gap-2">
              <select class="rounded-xl border-slate-200 text-sm py-2 px-2.5">
                <option>Bulk action</option>
                <option>Archive selected</option>
                <option>Mark completed</option>
                <option>Mark failed</option>
              </select>
              <button class="inline-flex items-center gap-2 rounded-xl bg-slate-900 text-white px-3 py-2 text-[13px] font-medium hover:bg-black/90">
                <i data-lucide="play" class="h-4 w-4"></i> Apply
              </button>
            </div>

            <nav class="flex items-center gap-1">
              <button class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
                <i data-lucide="chevron-left" class="h-4 w-4"></i>
              </button>
              <button class="h-9 w-9 inline-flex items-center justify-center rounded-lg bg-slate-900 text-white">1</button>
              <button class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">2</button>
              <button class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">3</button>
              <button class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
                <i data-lucide="chevron-right" class="h-4 w-4"></i>
              </button>
            </nav>
          </div>
        </div>
      </div>
    </section>
  </main>

  {{-- Shared footer --}}
  @include('partials.admin-footer')

  {{-- ===== Modals (static UI) ===== --}}
  {{-- Create --}}
  <div id="modalCreate" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative mx-auto mt-24 w-full max-w-2xl">
      <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between">
          <h3 class="text-[15px] font-semibold">Create return demo</h3>
          <button class="p-2 rounded-lg hover:bg-slate-100" data-modal-close>
            <i data-lucide="x" class="h-5 w-5"></i>
          </button>
        </div>
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="text-[12px] text-slate-600">Student</label>
            <input class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm" placeholder="Search/select student">
          </div>
          <div>
            <label class="text-[12px] text-slate-600">Offering</label>
            <input class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm" placeholder="e.g., NURS101">
          </div>
          <div>
            <label class="text-[12px] text-slate-600">Skill / Procedure</label>
            <input class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm" placeholder="e.g., Vital Signs">
          </div>
          <div>
            <label class="text-[12px] text-slate-600">Clinical Instructor</label>
            <input class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm" placeholder="e.g., Joseph S. Alipio">
          </div>
          <div>
            <label class="text-[12px] text-slate-600">Date</label>
            <input type="date" class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm">
          </div>
          <div>
            <label class="text-[12px] text-slate-600">Time</label>
            <input type="time" class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm">
          </div>
          <div class="sm:col-span-2">
            <label class="text-[12px] text-slate-600">Notes (optional)</label>
            <textarea rows="4" class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm" placeholder="Additional instructions…"></textarea>
          </div>
        </div>
        <div class="mt-5 flex items-center justify-end gap-2">
          <button class="px-3 py-2 rounded-xl text-[13px] border border-slate-200 bg-white hover:bg-slate-50" data-modal-close>Cancel</button>
          <button class="px-3 py-2 rounded-xl text-[13px] bg-green-600 text-white hover:bg-green-700 inline-flex items-center gap-2">
            <i data-lucide="check" class="h-4 w-4"></i> Create
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- You can copy View/Edit/Archive modals from Courses page if you want them here as well --}}

  {{-- Icons + toggles --}}
  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    lucide.createIcons();
    document.querySelectorAll('[data-modal-target]').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-modal-target');
        document.getElementById(id)?.classList.remove('hidden');
        lucide.createIcons();
      });
    });
    document.querySelectorAll('[data-modal-close]').forEach(btn => {
      btn.addEventListener('click', () => btn.closest('.fixed.inset-0')?.classList.add('hidden'));
    });
    document.querySelectorAll('.fixed.inset-0').forEach(modal => {
      modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.add('hidden'); });
    });
  </script>
</body>
</html>
