{{-- resources/views/admin/admin-settings.blade.php --}}
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Admin • Settings · NurSync</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }

    /* Settings cards entrance (same vibe as other admin pages) */
    @keyframes slide-in-up {
      from {
        transform: translateY(8px);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .animate-card-in {
      animation: slide-in-up .32s ease-out both;
      will-change: transform, opacity;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">

  <main class="min-h-screen flex">
    {{-- Sidebar (optional highlight) --}}
    @include('partials.admin-sidebar', ['active' => 'settings'])

    {{-- Main --}}
    <section class="flex-1 min-w-0">
      {{-- Header --}}
      <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-900 text-white shadow-sm">
              <i data-lucide="settings" class="h-4 w-4"></i>
            </div>
            <div>
              <h1 class="text-[15px] sm:text-[16px] font-semibold leading-tight">Admin Settings</h1>
              <p class="text-[12px] text-slate-500 -mt-0.5">
                Update your profile, security, and notification preferences.
              </p>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <button type="button"
              class="inline-flex items-center gap-2 rounded-xl bg-slate-100 text-slate-700 px-3 py-2 text-[13px] hover:bg-slate-200"
              data-modal-target="modalDiscard">
              <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
              <span>Discard</span>
            </button>
            <button type="button"
              class="inline-flex items-center gap-2 rounded-xl bg-green-600 text-white px-3 py-2 text-[13px] font-medium shadow hover:bg-green-700"
              data-modal-target="modalSave">
              <i data-lucide="save" class="h-4 w-4"></i>
              <span>Save Changes</span>
            </button>
          </div>
        </div>
      </header>

      {{-- Content --}}
      <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6">

        {{-- Skeleton loader --}}
        <div id="settings-skeleton" class="space-y-5 animate-pulse">
          {{-- Profile skeleton --}}
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-start gap-4">
              <div class="h-16 w-16 rounded-2xl bg-slate-200"></div>
              <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-2">
                  <div class="h-3 w-24 bg-slate-200 rounded"></div>
                  <div class="h-9 w-full bg-slate-100 rounded-xl"></div>
                </div>
                <div class="space-y-2">
                  <div class="h-3 w-24 bg-slate-200 rounded"></div>
                  <div class="h-9 w-full bg-slate-100 rounded-xl"></div>
                </div>
                <div class="space-y-2">
                  <div class="h-3 w-20 bg-slate-200 rounded"></div>
                  <div class="h-9 w-full bg-slate-100 rounded-xl"></div>
                </div>
                <div class="space-y-2">
                  <div class="h-3 w-28 bg-slate-200 rounded"></div>
                  <div class="h-9 w-full bg-slate-100 rounded-xl"></div>
                </div>
              </div>
            </div>
          </div>

          {{-- A couple of generic card skeletons --}}
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center gap-3 mb-4">
              <div class="h-9 w-9 rounded-xl bg-slate-200"></div>
              <div class="space-y-2">
                <div class="h-3 w-32 bg-slate-200 rounded"></div>
                <div class="h-3 w-40 bg-slate-100 rounded"></div>
              </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div class="h-9 w-full bg-slate-100 rounded-xl"></div>
              <div class="h-9 w-full bg-slate-100 rounded-xl"></div>
              <div class="h-9 w-full bg-slate-100 rounded-xl"></div>
            </div>
          </div>

          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center gap-3 mb-4">
              <div class="h-9 w-9 rounded-xl bg-slate-200"></div>
              <div class="space-y-2">
                <div class="h-3 w-40 bg-slate-200 rounded"></div>
                <div class="h-3 w-48 bg-slate-100 rounded"></div>
              </div>
            </div>
            <div class="h-20 w-full bg-slate-100 rounded-xl"></div>
          </div>
        </div>

        {{-- Real content (hidden until skeleton finishes) --}}
        <div id="settings-content" class="space-y-6 hidden">

          {{-- Profile --}}
          <div class="js-setting-card opacity-0 bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-start gap-4">
              {{-- Avatar (image if available, otherwise initials) --}}
              <div class="relative">
                @if (!empty($avatarUrl))
                  <img src="{{ $avatarUrl }}" alt="{{ $displayName }}" class="h-16 w-16 rounded-2xl object-cover">
                @else
                  <div class="h-16 w-16 rounded-2xl bg-slate-100 flex items-center justify-center">
                    <span class="text-slate-600 font-semibold">{{ $initials }}</span>
                  </div>
                @endif

                {{-- Change avatar trigger (uses your existing modal) --}}
                <button type="button"
                  class="absolute -bottom-2 -right-2 h-8 w-8 rounded-xl bg-white border border-slate-200 shadow inline-flex items-center justify-center hover:bg-slate-50"
                  data-modal-target="modalAvatar">
                  <i data-lucide="camera" class="h-4 w-4"></i>
                </button>
              </div>

              {{-- Profile fields (bound to display vars) --}}
              <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <label class="text-[12px] text-slate-600">Full name</label>
                  <input class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm"
                         value="{{ $displayName }}"
                         placeholder="Full name">
                </div>

                <div>
                  <label class="text-[12px] text-slate-600">Display name</label>
                  <input class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm" placeholder="Optional">
                </div>

                <div>
                  <label class="text-[12px] text-slate-600">Email</label>
                  <div class="relative">
                    <input type="email" value="{{ $displayEmail }}"
                      class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm bg-slate-50 text-slate-600 cursor-not-allowed"
                      disabled>
                    <i data-lucide="lock" class="absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400"></i>
                  </div>
                  <p class="mt-1 text-[11px] text-slate-500">
                    Email is managed by the system and cannot be changed.
                  </p>
                </div>

                <div>
                  <label class="text-[12px] text-slate-600">Phone (optional)</label>
                  <input class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm"
                         placeholder="+63 9xx xxx xxxx">
                </div>
              </div>
            </div>
          </div>

          {{-- Security --}}
          <div class="js-setting-card opacity-0 bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
            <div class="flex items-center gap-3">
              <div class="h-9 w-9 rounded-xl bg-indigo-100 text-indigo-700 inline-flex items-center justify-center">
                <i data-lucide="shield" class="h-5 w-5"></i>
              </div>
              <div>
                <h2 class="text-[14px] font-semibold">Security</h2>
                <p class="text-[12px] text-slate-500 -mt-0.5">Manage password and 2FA for your account.</p>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div class="sm:col-span-1">
                <label class="text-[12px] text-slate-600">Current password</label>
                <input type="password"
                       class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm"
                       placeholder="••••••••">
              </div>
              <div>
                <label class="text-[12px] text-slate-600">New password</label>
                <input type="password"
                       class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm"
                       placeholder="••••••••">
              </div>
              <div>
                <label class="text-[12px] text-slate-600">Confirm new password</label>
                <input type="password"
                       class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm"
                       placeholder="••••••••">
              </div>
            </div>
          </div>



          {{-- Add Another Admin (functional) --}}
          <div class="js-setting-card opacity-0 bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
            <div class="flex items-center gap-3">
              <div class="h-9 w-9 rounded-xl bg-green-100 text-green-700 inline-flex items-center justify-center">
                <i data-lucide="user-plus" class="h-5 w-5"></i>
              </div>
              <div>
                <h2 class="text-[14px] font-semibold">Add Another Admin</h2>
                <p class="text-[12px] text-slate-500 -mt-0.5">
                  Assign administrative access to another faculty member. They will share the same privileges as you.
                </p>
              </div>
            </div>

            @if (session('success'))
              <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-[13px] text-emerald-800 mb-2">
                {{ session('success') }}
              </div>
            @endif

            @if (session('error'))
              <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-[13px] text-rose-800 mb-2">
                {{ session('error') }}
              </div>
            @endif

            <form id="addAdminForm" method="POST" action="{{ route('admin.settings.add-admin') }}" class="space-y-4">
              @csrf
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <!-- Select Faculty (dynamic) -->
                <div class="sm:col-span-2">
                  <label class="text-[12px] text-slate-600">Faculty Member</label>
                  <div class="relative">
                    <select name="faculty_id" class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm pr-9">
                      <option value="" disabled {{ old('faculty_id') ? '' : 'selected' }}>Select faculty…</option>
                      @forelse($faculties as $f)
                        <option value="{{ $f->id }}" @selected(old('faculty_id')==$f->id)>
                          {{ $f->full_name }} — {{ $f->faculty_id }}
                        </option>
                      @empty
                        <option value="" disabled>No eligible faculty found</option>
                      @endforelse
                    </select>
                    <i data-lucide="chevron-down"
                       class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400"></i>
                  </div>
                  @error('faculty_id')
                    <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                  @else
                    <p class="mt-1 text-[11px] text-slate-500">Choose a faculty member to promote as another Admin.</p>
                  @enderror
                </div>

                <!-- Security confirmation -->
                <div>
                  <label class="text-[12px] text-slate-600">Your password</label>
                  <input
                    type="password"
                    name="current_password"
                    class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm"
                    placeholder="••••••••"
                    autocomplete="current-password">
                  @error('current_password')
                    <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                  @else
                    <p class="mt-1 text-[11px] text-slate-500">We’ll verify before adding another Admin.</p>
                  @enderror
                </div>
              </div>

              <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-[13px] text-amber-800">
                <div class="flex items-start gap-2">
                  <i data-lucide="alert-circle" class="h-4 w-4 mt-0.5"></i>
                  <div>
                    <div class="font-medium">Note</div>
                    <ul class="list-disc pl-5 space-y-1 mt-1">
                      <li>Additional Admins have the same full control as you.</li>
                      <li>Limit this privilege to trusted faculty members.</li>
                      <li>This action will be logged by the system.</li>
                    </ul>
                  </div>
                </div>
              </div>

              <div class="flex items-center justify-end">
                <button type="submit"
                  class="inline-flex items-center gap-2 rounded-xl bg-green-600 text-white px-3 py-2 text-[13px] font-medium shadow hover:bg-green-700">
                  <i data-lucide="check-circle" class="h-4 w-4"></i>
                  <span>Review & Confirm Add</span>
                </button>
              </div>
            </form>
          </div>

          {{-- Current Admins list (optional management) --}}
          @if(isset($admins))
            <div class="js-setting-card opacity-0 bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4 mt-5">
              <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-xl bg-slate-100 text-slate-700 inline-flex items-center justify-center">
                  <i data-lucide="shield" class="h-5 w-5"></i>
                </div>
                <h2 class="text-[14px] font-semibold">Current Admins</h2>
              </div>

              <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                  <thead>
                    <tr class="text-left text-slate-500">
                      <th class="py-2 pr-3">Name</th>
                      <th class="py-2 pr-3">Email</th>
                      <th class="py-2 pr-3">Active</th>
                      <th class="py-2 pr-3">Added</th>
                      <th class="py-2 pr-3"></th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    @foreach($admins as $adm)
                      <tr>
                        <td class="py-2 pr-3">{{ $adm->full_name }}</td>
                        <td class="py-2 pr-3 text-slate-600">{{ $adm->email }}</td>
                        <td class="py-2 pr-3">
                          <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px]
                            {{ $adm->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-50 text-slate-600 border border-slate-200' }}">
                            {{ $adm->is_active ? 'Active' : 'Inactive' }}
                          </span>
                        </td>
                        <td class="py-2 pr-3 text-slate-500">{{ $adm->created_at }}</td>
                        <td class="py-2 pr-3 text-right">
                          @if ($adm->id !== auth('admin')->id())
                            <form method="POST"
                                  action="{{ route('admin.settings.remove-admin', $adm->id) }}"
                                  onsubmit="return confirm('Remove this Admin?');" class="inline">
                              @csrf
                              @method('DELETE')
                              <button class="inline-flex items-center gap-2 rounded-xl border border-rose-200 text-rose-700 px-3 py-1.5 hover:bg-rose-50">
                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                                <span>Remove</span>
                              </button>
                            </form>
                          @else
                            <span class="text-[12px] text-slate-400">This is you</span>
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          @endif

          {{-- Change School Year / Term --}}
          <div class="js-setting-card opacity-0 bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
            <div class="flex items-center gap-3">
              <div class="h-9 w-9 rounded-xl bg-sky-100 text-sky-700 inline-flex items-center justify-center">
                <i data-lucide="calendar" class="h-5 w-5"></i>
              </div>
              <div>
                <h2 class="text-[14px] font-semibold">Change School Year / Term</h2>
                <p class="text-[12px] text-slate-500 -mt-0.5">
                  Starting a new term sets all student accounts to <strong>Pending Review</strong> until they upload
                  their reg card.
                </p>
              </div>
            </div>

            <form id="changeTermForm" method="POST" action="{{ route('admin.term.change') }}"
                  class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              @csrf

              <div>
                <label class="text-[12px] text-slate-600">School Year</label>
                <div class="relative">
                  <select name="school_year" required
                          class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm pr-9">
                    <option value="" selected disabled>Select year</option>
                    <option value="2026-2027">2026–2027</option>
                    <option value="2027-2028">2027–2028</option>
                    <option value="2028-2029">2028–2029</option>
                  </select>
                  <i data-lucide="chevron-down"
                     class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400"></i>
                </div>
              </div>

              <div>
                <label class="text-[12px] text-slate-600">Semester / Term</label>
                <div class="relative">
                  <select name="semester" required
                          class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm pr-9">
                    <option value="" selected disabled>Select term</option>
                    <option value="1">1st Semester</option>
                    <option value="2">2nd Semester</option>
                    <option value="S">Summer Term</option>
                  </select>
                  <i data-lucide="chevron-down"
                     class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400"></i>
                </div>
              </div>

              <div class="flex items-end">
                <button type="submit"
                  class="w-full inline-flex items-center gap-2 rounded-xl bg-sky-600 text-white px-3 py-2 text-[13px] font-medium shadow hover:bg-sky-700"
                  id="btnApplyTerm">
                  <i data-lucide="refresh-ccw" class="h-4 w-4"></i>
                  <span>Apply</span>
                </button>
              </div>
            </form>

            <script>
              document.getElementById('changeTermForm')?.addEventListener('submit', function (e) {
                const sy  = this.school_year?.value || '';
                const sem = this.semester?.value || '';
                const semText = sem === '1'
                  ? '1st Semester'
                  : sem === '2'
                    ? '2nd Semester'
                    : 'Summer Term';

                if (!confirm(
                  `Start new term for AY ${sy} — ${semText}?\n\nThis will set ALL student accounts to Pending Review.`
                )) {
                  e.preventDefault();
                }
              });
            </script>
          </div>

        </div>{{-- /#settings-content --}}
      </div> {{-- /outer content container --}}
    </section>
  </main>

  {{-- Shared footer --}}
  @include('partials.admin-footer')

  {{-- ===== Modals (static only) ===== --}}
  {{-- Save --}}
  <div id="modalSave" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative mx-auto mt-24 w-full max-w-md">
      <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-5">
        <div class="flex items-center gap-3">
          <div class="h-9 w-9 rounded-xl bg-green-100 text-green-700 inline-flex items-center justify-center">
            <i data-lucide="check-circle" class="h-5 w-5"></i>
          </div>
          <div>
            <h3 class="text-[15px] font-semibold">Save changes?</h3>
            <p class="text-[13px] text-slate-600">Your profile and preferences will be updated.</p>
          </div>
          <button class="ml-auto p-2 rounded-lg hover:bg-slate-100" data-modal-close>
            <i data-lucide="x" class="h-5 w-5"></i>
          </button>
        </div>
        <div class="mt-5 flex items-center justify-end gap-2">
          <button class="px-3 py-2 rounded-xl text-[13px] border border-slate-200 bg-white hover:bg-slate-50"
                  data-modal-close>
            Cancel
          </button>
          <button
            class="px-3 py-2 rounded-xl text-[13px] bg-green-600 text-white hover:bg-green-700 inline-flex items-center gap-2">
            <i data-lucide="save" class="h-4 w-4"></i> Save
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Discard --}}
  <div id="modalDiscard" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative mx-auto mt-24 w-full max-w-md">
      <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-5">
        <div class="flex items-center gap-3">
          <div class="h-9 w-9 rounded-xl bg-slate-100 text-slate-700 inline-flex items-center justify-center">
            <i data-lucide="rotate-ccw" class="h-5 w-5"></i>
          </div>
          <div>
            <h3 class="text-[15px] font-semibold">Discard unsaved changes?</h3>
            <p class="text-[13px] text-slate-600">This will revert the fields in this page.</p>
          </div>
          <button class="ml-auto p-2 rounded-lg hover:bg-slate-100" data-modal-close>
            <i data-lucide="x" class="h-5 w-5"></i>
          </button>
        </div>
        <div class="mt-5 flex items-center justify-end gap-2">
          <button class="px-3 py-2 rounded-xl text-[13px] border border-slate-200 bg-white hover:bg-slate-50"
                  data-modal-close>
            Cancel
          </button>
          <button
            class="px-3 py-2 rounded-xl text-[13px] bg-slate-900 text-white hover:bg-black/90 inline-flex items-center gap-2"
            data-modal-close>
            <i data-lucide="check" class="h-4 w-4"></i> Discard
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Avatar (functional, no nested forms) --}}
  <div id="modalAvatar" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative mx-auto mt-24 w-full max-w-md">
      <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between">
          <h3 class="text-[15px] font-semibold">Change avatar</h3>
          <button class="p-2 rounded-lg hover:bg-slate-100" data-modal-close>
            <i data-lucide="x" class="h-5 w-5"></i>
          </button>
        </div>

        {{-- Upload form --}}
        <form method="POST"
              action="{{ route('admin.settings.avatar.upload') }}"
              enctype="multipart/form-data"
              id="avatarUploadForm"
              class="mt-4 space-y-3 text-sm">
          @csrf

          {{-- Preview --}}
          <div class="flex items-center gap-3">
            <div class="h-12 w-12 rounded-xl bg-slate-100 overflow-hidden flex items-center justify-center">
              @if (!empty($avatarUrl))
                <img id="avatarPreview"
                     src="{{ $avatarUrl }}"
                     class="h-full w-full object-cover"
                     alt="Avatar preview">
              @else
                <img id="avatarPreview"
                     src=""
                     class="hidden h-full w-full object-cover"
                     alt="Avatar preview">
                <i id="avatarPreviewIcon" data-lucide="user" class="h-5 w-5 text-slate-400"></i>
              @endif
            </div>
            <div class="min-w-0">
              <div class="text-[13px] font-medium text-slate-800 leading-tight truncate">
                {{ $displayName ?? 'Admin' }}
              </div>
              <div class="text-[11px] text-slate-500 truncate">
                PNG/JPG up to 2MB. Square images look best.
              </div>
            </div>
          </div>

          {{-- File input --}}
          <input type="file" name="avatar" accept="image/*"
                 class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm"
                 required>

          @error('avatar')
            <p class="text-[12px] text-rose-600">{{ $message }}</p>
          @enderror

          <div class="mt-4 flex items-center justify-end gap-2">
            <button type="button"
              class="px-3 py-2 rounded-xl text-[13px] border border-slate-200 bg-white hover:bg-slate-50"
              data-modal-close>Cancel</button>
            <button type="submit"
              class="px-3 py-2 rounded-xl text-[13px] bg-green-600 text-white hover:bg-green-700 inline-flex items-center gap-2">
              <i data-lucide="check" class="h-4 w-4"></i> Upload
            </button>
          </div>
        </form>

        {{-- Separate "Remove" form (NOT nested) --}}
        @if (!empty($avatarUrl))
          <form method="POST"
                action="{{ route('admin.settings.avatar.remove') }}"
                class="mt-3 flex justify-end"
                onsubmit="return confirm('Remove current photo?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-[12px] text-slate-500 hover:text-rose-600">
              Remove current photo
            </button>
          </form>
        @endif
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
        if (window.lucide?.createIcons) lucide.createIcons();
      });
    });

    document.querySelectorAll('[data-modal-close]').forEach(btn => {
      btn.addEventListener('click', () => {
        btn.closest('.fixed.inset-0')?.classList.add('hidden');
      });
    });

    document.querySelectorAll('.fixed.inset-0').forEach(modal => {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.add('hidden');
      });
    });
  </script>

  {{-- Tiny preview script --}}
  <script>
    (function () {
      const form = document.getElementById('avatarUploadForm');
      if (!form) return;
      const input = form.querySelector('input[type="file"][name="avatar"]');
      const img   = document.getElementById('avatarPreview');
      const icon  = document.getElementById('avatarPreviewIcon');

      input?.addEventListener('change', (e) => {
        const file = e.target.files?.[0];
        if (!file) return;
        const url = URL.createObjectURL(file);
        if (img) {
          img.src = url;
          img.classList.remove('hidden');
        }
        if (icon) icon.classList.add('hidden');
      });
    })();
  </script>

  {{-- Settings cards animation + skeleton swap --}}
  <script>
    function animateSettingCards() {
      const cards     = document.querySelectorAll('.js-setting-card');
      const skeleton  = document.getElementById('settings-skeleton');
      const content   = document.getElementById('settings-content');

      if (content) content.classList.remove('hidden');
      if (skeleton) skeleton.classList.add('hidden');

      if (!cards.length) return;

      const prefersReduced = window.matchMedia &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;

      if (prefersReduced) {
        cards.forEach(card => card.classList.remove('opacity-0'));
        return;
      }

      cards.forEach((card, idx) => {
        card.style.animationDelay = `${Math.min(idx, 6) * 40}ms`;
        requestAnimationFrame(() => {
          card.classList.add('animate-card-in');
          card.classList.remove('opacity-0');
        });
      });
    }

    document.addEventListener('DOMContentLoaded', () => {
      const prefersReduced = window.matchMedia &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      const delay = prefersReduced ? 0 : 260; // tiny extra delay so skeleton is visible briefly
      setTimeout(animateSettingCards, delay);
    });
  </script>

</body>
</html>
