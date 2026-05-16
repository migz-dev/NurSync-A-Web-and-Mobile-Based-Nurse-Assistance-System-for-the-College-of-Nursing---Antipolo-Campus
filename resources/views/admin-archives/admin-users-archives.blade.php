{{-- resources/views/admin/admin-archives/admin-users-archives.blade.php --}}
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Admin • Archived Users · NurSync</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }

    /* Same animation vibe as admin users list */
    @keyframes slide-in-up {
      from {
        transform: translateY(6px);
        opacity: 0;
      }

      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .animate-row-in {
      animation: slide-in-up .28s ease-out both;
      will-change: transform, opacity;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
  <main class="min-h-screen flex">
    {{-- Sidebar (keep "users" active group) --}}
    @include('partials.admin-sidebar', ['active' => 'users'])

    {{-- Main --}}
    <section class="flex-1 min-w-0">
      {{-- Header --}}
      <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-900 text-white shadow-sm">
              <i data-lucide="archive" class="h-4 w-4"></i>
            </div>
            <div>
              <h1 class="text-[15px] sm:text-[16px] font-semibold leading-tight">Archived Users</h1>
              <p class="text-[12px] text-slate-500 -mt-0.5">Restorable accounts and records.</p>
            </div>
          </div>

          {{-- Primary actions --}}
          <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.index') }}"
              class="inline-flex items-center gap-2 rounded-xl bg-slate-700 text-white px-3 py-2 text-[13px] font-medium shadow hover:bg-slate-800 active:scale-[.99]">
              <i data-lucide="users" class="h-4 w-4"></i>
              <span>Back to Users</span>
            </a>
          </div>
        </div>
      </header>

      {{-- Content --}}
      <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">

        {{-- Filters / tools --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
          <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
            <div class="flex items-center gap-3 w-full sm:w-auto">
              <div class="relative flex-1 sm:w-64">
                <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
                <input type="text" placeholder="Search name, email..."
                  class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300">
              </div>

              <select class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                <option value="">All roles</option>
                <option>Student</option>
                <option>Faculty</option>
                <option>Admin</option>
              </select>

              {{-- Status fixed to Archived --}}
              <select class="rounded-xl border-slate-200 text-sm py-2.5 px-3 bg-slate-50 text-slate-500" disabled>
                <option selected>Archived</option>
              </select>
            </div>

            <div class="flex items-center gap-2">
              <button
                class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 px-3 py-2 text-[13px] hover:bg-slate-50">
                <i data-lucide="download" class="h-4 w-4"></i> Export
              </button>
              <button
                class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 px-3 py-2 text-[13px] hover:bg-slate-50">
                <i data-lucide="settings-2" class="h-4 w-4"></i> Columns
              </button>
            </div>
          </div>
        </div>

        {{-- Table card --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

          {{-- Skeleton loader (like admin users) --}}
          <div id="archivesSkeleton" class="p-4 border-b border-slate-200 space-y-3" aria-hidden="true">
            @for ($i = 0; $i < 6; $i++)
              <div class="flex items-center gap-4 animate-pulse">
                <div class="h-4 w-4 rounded bg-slate-200"></div>
                <div class="flex-1 space-y-2">
                  <div class="h-3 w-1/3 bg-slate-200 rounded"></div>
                  <div class="h-3 w-1/4 bg-slate-100 rounded"></div>
                </div>
                <div class="hidden md:flex gap-2">
                  <div class="h-3 w-24 bg-slate-100 rounded"></div>
                  <div class="h-3 w-20 bg-slate-100 rounded"></div>
                </div>
                <div class="h-3 w-16 bg-slate-100 rounded"></div>
              </div>
            @endfor
          </div>

          {{-- Actual table --}}
          <div class="overflow-x-auto hidden" id="archivesTableWrap">
            <table class="min-w-full text-sm">
              <thead class="bg-slate-50 border-b border-slate-200">
                <tr class="text-left text-slate-600">
                  <th class="px-4 py-3 w-10">
                    <input type="checkbox" class="rounded border-slate-300">
                  </th>
                  <th class="px-4 py-3">Name</th>
                  <th class="px-4 py-3">Email</th>
                  <th class="px-4 py-3">Role</th>
                  <th class="px-4 py-3">Archived</th>
                  <th class="px-4 py-3 text-right">Actions</th>
                </tr>
              </thead>

              <tbody class="divide-y divide-slate-200" id="archives-tbody">
                @forelse ($archivedUsersPage as $u)
                  @php $pill = $roleStyles[$u->role] ?? $roleStyles['Student']; @endphp
                  <tr class="hover:bg-slate-50 js-archive-row opacity-0" data-row-id="{{ $u->id }}">
                    <td class="px-4 py-3">
                      <input type="checkbox" class="rounded border-slate-300">
                    </td>

                    {{-- Name + avatar --}}
                    <td class="px-4 py-3">
                      <div class="flex items-center gap-3">
                        @if ($u->avatar_url)
                          <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="h-9 w-9 rounded-xl object-cover">
                        @else
                          <div class="h-9 w-9 rounded-xl bg-slate-100 flex items-center justify-center">
                            <i data-lucide="user" class="h-4 w-4 text-slate-500"></i>
                          </div>
                        @endif
                        <div>
                          <div class="font-medium text-slate-900">{{ $u->name }}</div>
                          <div class="text-[12px] text-slate-500">ID: {{ $u->id }}</div>
                        </div>
                      </div>
                    </td>

                    {{-- Email --}}
                    <td class="px-4 py-3 text-slate-700">
                      {{ $u->email ?? '—' }}
                    </td>

                    {{-- Role pill --}}
                    <td class="px-4 py-3">
                      <span
                        class="inline-flex items-center gap-1.5 rounded-lg {{ $pill['bg'] }} {{ $pill['text'] }} px-2 py-1 text-[12px] font-medium">
                        <i data-lucide="{{ $pill['icon'] }}" class="h-3.5 w-3.5"></i> {{ $u->role }}
                      </span>
                    </td>

                    {{-- Archived date --}}
                    <td class="px-4 py-3 text-slate-700">
                      @php
                        $archived = $u->archived_at ?? null;
                        $fallback = $u->created_at ?? null;
                      @endphp

                      @if ($archived)
                        {{ \Illuminate\Support\Carbon::parse($archived)->format('M d, Y') }}
                      @elseif ($fallback)
                        {{ \Illuminate\Support\Carbon::parse($fallback)->format('M d, Y') }}
                      @else
                        —
                      @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3">
                      <div class="flex items-center justify-end gap-1.5">

                        {{-- VIEW --}}
                        <a href="{{ route('admin.users.show', $u->id) }}"
                          class="inline-flex items-center justify-center rounded-lg bg-blue-600 text-white p-2 hover:bg-blue-700"
                          aria-label="View">
                          <i data-lucide="eye" class="h-4 w-4"></i>
                        </a>

                        {{-- RESTORE (SweetAlert) --}}
                        <button type="button"
                          class="js-restore inline-flex items-center justify-center rounded-lg bg-emerald-600 text-white p-2 hover:bg-emerald-700"
                          data-user-id="{{ $u->id }}" data-restore-url="{{ route('admin.users.restore', $u->id) }}"
                          aria-label="Restore">
                          <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                        </button>

                        {{-- DELETE (SweetAlert) --}}
                        <button type="button"
                          class="js-delete inline-flex items-center justify-center rounded-lg bg-rose-600 text-white p-2 hover:bg-rose-700"
                          data-user-id="{{ $u->id }}" data-destroy-url="{{ route('admin.users.destroy.post', $u->id) }}"
                          aria-label="Delete">
                          <i data-lucide="trash-2" class="h-4 w-4"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-slate-500">No archived users found.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          {{-- Footer actions / pagination --}}
          <div id="archivesFooter"
            class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 border-t border-slate-200 bg-slate-50 hidden">
            <div class="flex items-center gap-2">
              <select class="rounded-xl border-slate-200 text-sm py-2 px-2.5">
                <option>Bulk action</option>
                <option>Restore selected</option>
                <option>Delete selected</option>
              </select>
              <button
                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 text-white px-3 py-2 text-[13px] font-medium hover:bg-black/90">
                <i data-lucide="play" class="h-4 w-4"></i> Apply
              </button>
            </div>

            @php
              $cur = $archivedUsersPage->currentPage();
              $last = max(1, $archivedUsersPage->lastPage());
              $window = 3;
              $half = intdiv($window - 1, 2);
              $from = max(1, $cur - $half);
              $to = min($last, $from + $window - 1);
              $from = max(1, $to - $window + 1);
            @endphp

            <nav class="flex items-center gap-1">
              {{-- Prev --}}
              @if ($archivedUsersPage->onFirstPage())
                <button
                  class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white opacity-50 cursor-not-allowed">
                  <i data-lucide="chevron-left" class="h-4 w-4"></i>
                </button>
              @else
                <a href="{{ $archivedUsersPage->previousPageUrl() }}"
                  class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
                  <i data-lucide="chevron-left" class="h-4 w-4"></i>
                </a>
              @endif

              {{-- Numbers --}}
              @for ($i = $from; $i <= $to; $i++)
                @if ($i === $cur)
                  <span
                    class="h-9 w-9 inline-flex items-center justify-center rounded-lg bg-slate-900 text-white">{{ $i }}</span>
                @else
                  <a href="{{ $archivedUsersPage->url($i) }}"
                    class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
                    {{ $i }}
                  </a>
                @endif
              @endfor

              {{-- Next --}}
              @if ($cur >= $last)
                <button
                  class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white opacity-50 cursor-not-allowed">
                  <i data-lucide="chevron-right" class="h-4 w-4"></i>
                </button>
              @else
                <a href="{{ $archivedUsersPage->nextPageUrl() }}"
                  class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
                  <i data-lucide="chevron-right" class="h-4 w-4"></i>
                </a>
              @endif
            </nav>
          </div>
        </div>
      </div>
      {{-- Footer --}}
    </section>
  </main>

  {{-- Footer (shared) --}}
  @include('partials.admin-footer')

  {{-- Icons --}}
  <script src="https://unpkg.com/lucide@latest"></script>
  <script> lucide.createIcons(); </script>

  <script>
    (() => {
      const qsa = (s, root = document) => Array.from(root.querySelectorAll(s));
      const qs = (s, root = document) => root.querySelector(s);

      const skeleton = qs('#archivesSkeleton');
      const tableWrap = qs('#archivesTableWrap');
      const footerBar = qs('#archivesFooter');

      function animateRows(scope = document) {
        const rows = qsa('.js-archive-row', scope);
        const prefersReduced = window.matchMedia &&
          window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (!rows.length) return;

        if (prefersReduced) {
          rows.forEach(r => r.classList.remove('opacity-0'));
          return;
        }

        rows.forEach((row, idx) => {
          row.style.animationDelay = `${Math.min(idx, 10) * 25}ms`;
          requestAnimationFrame(() => {
            row.classList.add('animate-row-in');
            row.classList.remove('opacity-0');
          });
        });
      }

      function showSkeleton() {
        if (skeleton) skeleton.classList.remove('hidden');
        if (tableWrap) tableWrap.classList.add('hidden');
        if (footerBar) footerBar.classList.add('hidden');
      }

      function hideSkeleton() {
        if (skeleton) skeleton.classList.add('hidden');
        if (tableWrap) tableWrap.classList.remove('hidden');
        if (footerBar) footerBar.classList.remove('hidden');
        animateRows();
        if (window.lucide) lucide.createIcons();
      }

      const prefersReduced = window.matchMedia &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      const initialDelay = prefersReduced ? 0 : 220;

      // We start with skeleton visible by default; just reveal table after a tiny delay
      setTimeout(hideSkeleton, initialDelay);
    })();
  </script>
  <script>
    // resources/js/admin-users-archives.js
    // Archived Users — Restore & Delete with SweetAlert2 (no modals)

    document.addEventListener('DOMContentLoaded', () => {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

      const request = async (url, method = 'GET', body = null) => {
        const res = await fetch(url, {
          method,
          headers: {
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json',
            ...(body ? { 'Content-Type': 'application/json' } : {})
          },
          credentials: 'same-origin',
          body: body ? JSON.stringify(body) : null
        });
        let payload = null;
        try { payload = await res.json(); } catch { }
        if (!res.ok) {
          // Common Laravel CSRF response
          if (res.status === 419) throw new Error('Session expired. Please refresh the page and try again.');
          throw new Error(payload?.message || `Request failed (${res.status})`);
        }
        return payload;
      };

      const toast = (icon, title, text = '') => {
        if (window.Swal) {
          window.Swal.fire({ icon, title, text, timer: 1400, showConfirmButton: false });
        } else {
          alert(`${title}${text ? '\n' + text : ''}`);
        }
      };

      const setBusy = (btn, busy) => {
        if (!btn) return;
        btn.disabled = !!busy;
        btn.setAttribute('aria-busy', busy ? 'true' : 'false');
      };

      // Restore handler (POST)
      document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.js-restore');
        if (!btn) return;

        const row = btn.closest('tr');
        const id = btn.dataset.userId || row?.dataset.rowId;
        const url = btn.dataset.restoreUrl;
        if (!url || !id) return;

        let confirmed = true;
        if (window.Swal) {
          const res = await Swal.fire({
            icon: 'question',
            title: 'Restore this user?',
            text: 'The account will be moved back to active users.',
            showCancelButton: true,
            confirmButtonText: 'Yes, restore',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#059669' // emerald-600
          });
          confirmed = res.isConfirmed;
        } else {
          confirmed = confirm('Restore this user?');
        }
        if (!confirmed) return;

        try {
          setBusy(btn, true);
          await request(url, 'POST');
          (document.querySelector(`tr[data-row-id="${CSS.escape(id)}"]`) || row)?.remove();
          toast('success', 'Restored');
        } catch (err) {
          toast('error', 'Restore failed', err.message || 'Something went wrong.');
        } finally {
          setBusy(btn, false);
        }
      });

      // Delete handler (POST — your route doesn’t accept DELETE)
      document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.js-delete');
        if (!btn) return;

        const row = btn.closest('tr');
        const id = btn.dataset.userId || row?.dataset.rowId;
        const url = btn.dataset.destroyUrl;
        if (!url || !id) return;

        let confirmed = true;
        if (window.Swal) {
          const res = await Swal.fire({
            icon: 'warning',
            title: 'Delete this user permanently?',
            text: 'This action cannot be undone.',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#e11d48' // rose-600
          });
          confirmed = res.isConfirmed;
        } else {
          confirmed = confirm('Delete this user permanently? This cannot be undone.');
        }
        if (!confirmed) return;

        try {
          setBusy(btn, true);
          // Use POST (matches your route’s allowed methods)
          await request(url, 'POST');
          (document.querySelector(`tr[data-row-id="${CSS.escape(id)}"]`) || row)?.remove();
          toast('success', 'Deleted', 'The user has been permanently deleted.');
        } catch (err) {
          toast('error', 'Delete failed', err.message || 'Something went wrong.');
        } finally {
          setBusy(btn, false);
        }
      });
    });
  </script>
</body>

</html>