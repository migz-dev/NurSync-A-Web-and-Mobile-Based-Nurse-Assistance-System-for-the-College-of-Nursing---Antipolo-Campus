{{-- resources/views/admin/admin-announcements.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Admin • Announcements · NurSync</title>
      <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style> body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; } </style>
</head>
<body class="min-h-screen bg-slate-50">

  <main class="min-h-screen flex">
    {{-- Sidebar --}}
    @include('partials.admin-sidebar', ['active' => 'announcements'])

    {{-- Main --}}
    <section class="flex-1 min-w-0">
      {{-- Header --}}
      <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-900 text-white shadow-sm">
              <i data-lucide="bell" class="h-4 w-4"></i>
            </div>
            <div>
              <h1 class="text-[15px] sm:text-[16px] font-semibold leading-tight">Announcements</h1>
              <p class="text-[12px] text-slate-500 -mt-0.5">Create and manage announcements for students and faculty.</p>
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
                <input type="text" placeholder="Search title, content..."
                       class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300">
              </div>

              <select class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                <option value="">Audience: All</option>
                <option>Students</option>
                <option>Faculty</option>
              </select>

              <select class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                <option value="">Status: All</option>
                <option selected>Published</option>
                <option>Draft</option>
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
                  <th class="px-4 py-3">Title</th>
                  <th class="px-4 py-3">Audience</th>
                  <th class="px-4 py-3">Status</th>
                  <th class="px-4 py-3">Pinned</th>
                  <th class="px-4 py-3">Scheduled</th>
                  <th class="px-4 py-3 text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-200">
                {{-- Row 1 --}}
                <tr class="hover:bg-slate-50">
                  <td class="px-4 py-3">
                    <input type="checkbox" class="rounded border-slate-300">
                  </td>
                  <td class="px-4 py-3">
                    <div class="font-medium text-slate-900">Orientation Week Updates</div>
                    <div class="text-[12px] text-slate-500">Published Sep 10, 2025</div>
                  </td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 text-indigo-700 px-2 py-1 text-[12px] font-medium">
                      <i data-lucide="users" class="h-3.5 w-3.5"></i> Students
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 text-emerald-700 px-2 py-1 text-[12px] font-medium">
                      <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Published
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 text-amber-700 px-2 py-1 text-[12px] font-medium">
                      <i data-lucide="pin" class="h-3.5 w-3.5"></i> Yes
                    </span>
                  </td>
                  <td class="px-4 py-3 text-slate-700">Sep 10 → Sep 17</td>
                  <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-1.5">
                      {{-- Read (Blue) --}}
                      <button type="button" class="inline-flex items-center justify-center rounded-lg bg-blue-600 text-white p-2 hover:bg-blue-700" data-modal-target="modalView">
                        <i data-lucide="eye" class="h-4 w-4"></i>
                      </button>
                      {{-- Update (Yellow) --}}
                      <button type="button" class="inline-flex items-center justify-center rounded-lg bg-yellow-400 text-slate-900 p-2 hover:brightness-95" data-modal-target="modalEdit">
                        <i data-lucide="pencil" class="h-4 w-4"></i>
                      </button>
                      {{-- Archive (Orange) --}}
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
                    <div class="font-medium text-slate-900">Faculty Meeting Notice</div>
                    <div class="text-[12px] text-slate-500">Draft saved Sep 14, 2025</div>
                  </td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-purple-50 text-purple-700 px-2 py-1 text-[12px] font-medium">
                      <i data-lucide="graduation-cap" class="h-3.5 w-3.5"></i> Faculty
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 text-slate-700 px-2 py-1 text-[12px] font-medium">
                      <span class="h-2 w-2 rounded-full bg-slate-400"></span> Draft
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 text-slate-600 px-2 py-1 text-[12px] font-medium">
                      <i data-lucide="pin-off" class="h-3.5 w-3.5"></i> No
                    </span>
                  </td>
                  <td class="px-4 py-3 text-slate-700">—</td>
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

                {{-- Row 3 --}}
                <tr class="hover:bg-slate-50">
                  <td class="px-4 py-3"><input type="checkbox" class="rounded border-slate-300"></td>
                  <td class="px-4 py-3">
                    <div class="font-medium text-slate-900">Maintenance Window</div>
                    <div class="text-[12px] text-slate-500">Published Sep 21, 2025</div>
                  </td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-sky-50 text-sky-700 px-2 py-1 text-[12px] font-medium">
                      <i data-lucide="megaphone" class="h-3.5 w-3.5"></i> Students & Faculty
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 text-emerald-700 px-2 py-1 text-[12px] font-medium">
                      <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Published
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 text-slate-600 px-2 py-1 text-[12px] font-medium">
                      <i data-lucide="pin-off" class="h-3.5 w-3.5"></i> No
                    </span>
                  </td>
                  <td class="px-4 py-3 text-slate-700">Sep 22, 10:00–12:00</td>
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

          {{-- Footer actions / pagination --}}
          <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 border-t border-slate-200 bg-slate-50">
            <div class="flex items-center gap-2">
              <select class="rounded-xl border-slate-200 text-sm py-2 px-2.5">
                <option>Bulk action</option>
                <option>Archive selected</option>
                <option>Publish selected</option>
                <option>Unpublish selected</option>
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
          <h3 class="text-[15px] font-semibold">Create announcement</h3>
          <button class="p-2 rounded-lg hover:bg-slate-100" data-modal-close>
            <i data-lucide="x" class="h-5 w-5"></i>
          </button>
        </div>

        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div class="sm:col-span-2">
            <label class="text-[12px] text-slate-600">Title</label>
            <input class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm" placeholder="Enter title">
          </div>
          <div>
            <label class="text-[12px] text-slate-600">Audience</label>
            <select class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm">
              <option>Students</option>
              <option>Faculty</option>
              <option>Students & Faculty</option>
            </select>
          </div>
          <div>
            <label class="text-[12px] text-slate-600">Status</label>
            <select class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm">
              <option selected>Published</option>
              <option>Draft</option>
            </select>
          </div>
          <div class="sm:col-span-2">
            <label class="text-[12px] text-slate-600">Body</label>
            <textarea rows="5" class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm" placeholder="Type announcement text..."></textarea>
          </div>
          <div>
            <label class="text-[12px] text-slate-600">Start (optional)</label>
            <input type="datetime-local" class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm">
          </div>
          <div>
            <label class="text-[12px] text-slate-600">End (optional)</label>
            <input type="datetime-local" class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm">
          </div>
          <div class="sm:col-span-2 flex items-center gap-2 pt-1">
            <input id="createPinned" type="checkbox" class="rounded border-slate-300">
            <label for="createPinned" class="text-sm text-slate-700">Pin this announcement</label>
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

  {{-- View --}}
  <div id="modalView" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative mx-auto mt-24 w-full max-w-xl">
      <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between">
          <h3 class="text-[15px] font-semibold">Announcement</h3>
          <button class="p-2 rounded-lg hover:bg-slate-100" data-modal-close>
            <i data-lucide="x" class="h-5 w-5"></i>
          </button>
        </div>
        <div class="mt-3 space-y-2 text-sm">
          <div class="font-semibold text-slate-900">Orientation Week Updates</div>
          <div class="text-[12px] text-slate-500">Students • Sep 10–17</div>
          <p class="text-slate-700 leading-6">
            Welcome to the new term! Please review your schedules in the portal. Orientation activities run all week...
          </p>
        </div>
        <div class="mt-5 flex items-center justify-end">
          <button class="px-3 py-2 rounded-xl text-[13px] bg-blue-600 text-white hover:bg-blue-700 inline-flex items-center gap-2" data-modal-close>
            <i data-lucide="check" class="h-4 w-4"></i> Close
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Edit --}}
  <div id="modalEdit" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative mx-auto mt-24 w-full max-w-2xl">
      <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between">
          <h3 class="text-[15px] font-semibold">Edit announcement</h3>
          <button class="p-2 rounded-lg hover:bg-slate-100" data-modal-close>
            <i data-lucide="x" class="h-5 w-5"></i>
          </button>
        </div>

        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div class="sm:col-span-2">
            <label class="text-[12px] text-slate-600">Title</label>
            <input class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm" value="Orientation Week Updates">
          </div>
          <div>
            <label class="text-[12px] text-slate-600">Audience</label>
            <select class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm">
              <option selected>Students</option>
              <option>Faculty</option>
              <option>Students & Faculty</option>
            </select>
          </div>
          <div>
            <label class="text-[12px] text-slate-600">Status</label>
            <select class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm">
              <option selected>Published</option>
              <option>Draft</option>
            </select>
          </div>
          <div class="sm:col-span-2">
            <label class="text-[12px] text-slate-600">Body</label>
            <textarea rows="5" class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm">Welcome to the new term! Please review your schedules…</textarea>
          </div>
          <div>
            <label class="text-[12px] text-slate-600">Start (optional)</label>
            <input type="datetime-local" class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm">
          </div>
          <div>
            <label class="text-[12px] text-slate-600">End (optional)</label>
            <input type="datetime-local" class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm">
          </div>
          <div class="sm:col-span-2 flex items-center gap-2 pt-1">
            <input id="editPinned" type="checkbox" class="rounded border-slate-300" checked>
            <label for="editPinned" class="text-sm text-slate-700">Pin this announcement</label>
          </div>
        </div>

        <div class="mt-5 flex items-center justify-end gap-2">
          <button class="px-3 py-2 rounded-xl text-[13px] border border-slate-200 bg-white hover:bg-slate-50" data-modal-close>Cancel</button>
          <button class="px-3 py-2 rounded-xl text-[13px] bg-yellow-400 text-slate-900 hover:brightness-95 inline-flex items-center gap-2">
            <i data-lucide="save" class="h-4 w-4"></i> Save changes
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Archive --}}
  <div id="modalArchive" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative mx-auto mt-24 w-full max-w-md">
      <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-5">
        <div class="flex items-center gap-3">
          <div class="h-9 w-9 rounded-xl bg-orange-100 text-orange-700 inline-flex items-center justify-center">
            <i data-lucide="alert-triangle" class="h-5 w-5"></i>
          </div>
          <div>
            <h3 class="text-[15px] font-semibold">Archive this announcement?</h3>
            <p class="text-[13px] text-slate-600">It will be hidden from users but kept for records.</p>
          </div>
          <button class="ml-auto p-2 rounded-lg hover:bg-slate-100" data-modal-close>
            <i data-lucide="x" class="h-5 w-5"></i>
          </button>
        </div>
        <div class="mt-5 flex items-center justify-end gap-2">
          <button class="px-3 py-2 rounded-xl text-[13px] border border-slate-200 bg-white hover:bg-slate-50" data-modal-close>Cancel</button>
          <button class="px-3 py-2 rounded-xl text-[13px] bg-orange-500 text-white hover:bg-orange-600 inline-flex items-center gap-2">
            <i data-lucide="archive" class="h-4 w-4"></i> Archive
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Icons + tiny modal toggles --}}
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
