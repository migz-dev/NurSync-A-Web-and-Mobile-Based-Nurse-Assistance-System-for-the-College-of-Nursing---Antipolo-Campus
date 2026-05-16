{{-- resources/views/admin/admin-users.blade.php --}}
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Admin • Users · NurSync</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }

    /* Row entrance animation (same vibe as dashboards) */
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
    {{-- Sidebar --}}
    @include('partials.admin-sidebar', ['active' => 'users'])

    {{-- Main --}}
    <section class="flex-1 min-w-0">
      {{-- Header --}}
      <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-900 text-white shadow-sm">
              <i data-lucide="users" class="h-4 w-4"></i>
            </div>
            <div>
              <h1 class="text-[15px] sm:text-[16px] font-semibold leading-tight">Users</h1>
              <p class="text-[12px] text-slate-500 -mt-0.5">
                Manage student, faculty, and admin accounts.
              </p>
            </div>
          </div>

          {{-- Primary actions --}}
          <div class="flex items-center gap-2">
            {{-- Archives button --}}
            <a href="{{ route('admin.users.archives') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-slate-700 text-white px-3 py-2 text-[13px] font-medium shadow hover:bg-slate-800 active:scale-[.99]">
              <i data-lucide="archive" class="h-4 w-4"></i>
              <span>Archives</span>
            </a>

            {{-- Approvals button --}}
            <a href="{{ route('admin.faculty.approvals') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 text-white px-3 py-2 text-[13px] font-medium shadow hover:bg-indigo-700 active:scale-[.99]">
              <i data-lucide="user-check" class="h-4 w-4"></i>
              <span>Pending Approvals</span>
            </a>

            {{-- New Admin button --}}
            <a href="{{ route('admin.users.admins') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 text-white px-3 py-2 text-[13px] font-medium shadow hover:bg-emerald-700 active:scale-[.99]">
              <i data-lucide="shield" class="h-4 w-4"></i>
              <span>Admins</span>
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
                <input id="filter-q" type="text" placeholder="Search name, email..."
                       class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300">
              </div>

              <select id="filter-role"
                      class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                <option value="">All roles</option>
                <option value="Student Nurse">Student</option>
                <option value="Clinical Instructor">Faculty</option>
                <option value="Admin">Admin</option>
              </select>

              <select id="filter-status"
                      class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                <option value="">All status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Pending">Pending</option>
                <option value="Rejected">Rejected</option>
                <option value="Archived">Archived</option>
              </select>
            </div>

            <div class="flex items-center gap-2">
              {{-- per-page selector placeholder --}}
            </div>
          </div>
        </div>

        {{-- Table card --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

          {{-- Skeleton loader --}}
          <div id="usersSkeleton" class="p-4 border-b border-slate-200 space-y-3" aria-hidden="true">
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

          {{-- Real table --}}
          <div class="overflow-x-auto hidden" id="usersTableWrap">
            <table class="min-w-full text-sm">
              <thead class="bg-slate-50 border-b border-slate-200">
              <tr class="text-left text-slate-600">
                <th class="px-4 py-3 w-10">
                  <input type="checkbox" class="rounded border-slate-300">
                </th>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Role</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Created</th>
                <th class="px-4 py-3 text-right">Actions</th>
              </tr>
              </thead>

              <tbody id="users-tbody" class="divide-y divide-slate-200">
              @forelse ($usersPage as $u)
                @php
                  $isActive = strtolower($u->status) === 'active';
                  $pill = $roleStyles[$u->role] ?? $roleStyles['Student'];
                @endphp
                {{-- add js-user-row + opacity-0 for animation --}}
                <tr class="hover:bg-slate-50 js-user-row opacity-0">
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

                  {{-- Status pill --}}
                  <td class="px-4 py-3">
                    @if ($isActive)
                      <span
                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 text-emerald-700 px-2 py-1 text-[12px] font-medium">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Active
                      </span>
                    @elseif (strtolower($u->status) === 'pending')
                      <span
                        class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 text-amber-700 px-2 py-1 text-[12px] font-medium">
                        <span class="h-2 w-2 rounded-full bg-amber-500"></span> Pending
                      </span>
                    @elseif (strtolower($u->status) === 'rejected')
                      <span
                        class="inline-flex items-center gap-1.5 rounded-lg bg-rose-50 text-rose-700 px-2 py-1 text-[12px] font-medium">
                        <span class="h-2 w-2 rounded-full bg-rose-500"></span> Rejected
                      </span>
                    @else
                      <span
                        class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 text-slate-600 px-2 py-1 text-[12px] font-medium">
                        <span class="h-2 w-2 rounded-full bg-slate-400"></span> {{ $u->status }}
                      </span>
                    @endif
                  </td>

                  {{-- Created date --}}
                  <td class="px-4 py-3 text-slate-700">
                    {{ \Illuminate\Support\Carbon::parse($u->created_at)->format('M d, Y') }}
                  </td>

                  {{-- Actions --}}
                  <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-1.5">
                      <!-- VIEW -->
                      <button type="button"
                              class="inline-flex items-center justify-center rounded-lg bg-blue-600 text-white p-2 hover:bg-blue-700 js-view-user"
                              data-user-id="{{ $u->id }}" data-modal-target="modalRead">
                        <i data-lucide="eye" class="h-4 w-4"></i>
                      </button>

                      <!-- EDIT -->
                      <button type="button"
                              class="inline-flex items-center justify-center rounded-lg bg-yellow-400 text-slate-900 p-2 hover:brightness-95 js-edit-user"
                              data-user-id="{{ $u->id }}" data-modal-target="modalUpdate">
                        <i data-lucide="pencil" class="h-4 w-4"></i>
                      </button>

                      <!-- ARCHIVE -->
                      <button type="button"
                              class="inline-flex items-center justify-center rounded-lg bg-orange-500 text-white p-2 hover:bg-orange-600"
                              data-modal-target="modalArchive" data-user-id="{{ $u->id }}" data-user-name="{{ $u->name }}">
                        <i data-lucide="archive" class="h-4 w-4"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="px-4 py-8 text-center text-slate-500">No users found.</td>
                </tr>
              @endforelse
              </tbody>

            </table>
          </div>

          {{-- Footer actions / pagination --}}
          <div
            id="usersFooter"
            class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 border-t border-slate-200 bg-slate-50 hidden">
            <div class="flex items-center gap-2">
              {{-- placeholder for bulk actions --}}
            </div>

            @php
              $cur = $usersPage->currentPage();
              $last = max(1, $usersPage->lastPage());
              $window = 3;
              $half = intdiv($window - 1, 2);
              $from = max(1, $cur - $half);
              $to = min($last, $from + $window - 1);
              $from = max(1, $to - $window + 1);
            @endphp

            <nav id="users-pagination" class="flex items-center gap-1">
              {{-- Prev --}}
              @if ($usersPage->onFirstPage())
                <button
                  class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white opacity-50 cursor-not-allowed">
                  <i data-lucide="chevron-left" class="h-4 w-4"></i>
                </button>
              @else
                <a href="{{ $usersPage->previousPageUrl() }}"
                   class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
                  <i data-lucide="chevron-left" class="h-4 w-4"></i>
                </a>
              @endif

              {{-- Numbered buttons (window of 3) --}}
              @for ($i = $from; $i <= $to; $i++)
                @if ($i === $cur)
                  <span
                    class="h-9 w-9 inline-flex items-center justify-center rounded-lg bg-slate-900 text-white">{{ $i }}</span>
                @else
                  <a href="{{ $usersPage->url($i) }}"
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
                <a href="{{ $usersPage->nextPageUrl() }}"
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

  {{-- Push modal partials to the "modals" stack --}}
  @push('modals')
    @include('admin.users._modals')
  @endpush

  {{-- Render the stack here IF your layout doesn’t already do it. Safe to keep. --}}
  @stack('modals')

  {{-- Footer (shared) --}}
  @include('partials.admin-footer')

  {{-- Icons + logic --}}
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    (() => {
      // -------------------------
      // Setup & tiny utilities
      // -------------------------
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const qsa = (s, root = document) => Array.from(root.querySelectorAll(s));
      const qs = (s, root = document) => root.querySelector(s);
      const text = (sel, val) => { const el = qs(sel); if (el) el.textContent = (val ?? '—'); };
      const setv = (sel, val) => { const el = qs(sel); if (el) el.value = (val ?? ''); };
      const tmpl = (urlTemplate, id) => urlTemplate.replace(':id', encodeURIComponent(id));
      const debounce = (fn, ms = 350) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); } };

      // skeleton + wrappers
      const skeleton = qs('#usersSkeleton');
      const tableWrap = qs('#usersTableWrap');
      const footerBar = qs('#usersFooter');

      // Your route templates (Blade will render these)
      const INDEX_URL = "{{ route('admin.users.index') }}";
      const SHOW_URL_TMPL = @json(route('admin.users.show', ':id'));
      const UPDATE_URL_TMPL = @json(route('admin.users.update', ':id'));
      const ARCHIVE_URL_TMPL = @json(route('admin.users.archive', ['id' => 'REPLACE_ID']));

      if (window.lucide) lucide.createIcons();

      // -------------------------
      // Generic modals
      // -------------------------
      qsa('[data-modal-target]').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.getAttribute('data-modal-target');
          qs('#' + id)?.classList.remove('hidden');
          if (window.lucide) lucide.createIcons();
        });
      });
      qsa('[data-modal-close]').forEach(btn => {
        btn.addEventListener('click', () => btn.closest('.fixed.inset-0')?.classList.add('hidden'));
      });
      qsa('.fixed.inset-0').forEach(modal => {
        modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.add('hidden'); });
      });

      // -------------------------
      // Fetch user (JSON)
      // -------------------------
      async function fetchUser(id) {
        const url = tmpl(SHOW_URL_TMPL, id);
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) {
          const err = await res.json().catch(() => ({}));
          throw new Error(err.message || `Failed to load user #${id}`);
        }
        return await res.json();
      }

      // VIEW modal
      function bindViewButtons(scope = document) {
        qsa('.js-view-user', scope).forEach(btn => {
          if (btn._boundView) return;
          btn._boundView = true;
          btn.addEventListener('click', async () => {
            const id = btn.getAttribute('data-user-id');
            try {
              const u = await fetchUser(id);

              const avatar = qs('#view_avatar');
              if (avatar) avatar.src = u.avatar_url || '{{ asset('placeholder-avatar.png') }}';

              text('#view_name', u.name);
              text('#view_email', u.email);
              text('#view_role', u.role);
              text('#view_status', u.status);
              text('#view_created', u.created_at);
              text('#view_nurse_type', u.nurse_type);

              const archWrap = qs('#view_archived_wrap');
              if (archWrap) {
                if (u.archived_at) {
                  archWrap.classList.remove('hidden');
                  text('#view_archived', u.archived_at);
                } else {
                  archWrap.classList.add('hidden');
                }
              }

              if (window.lucide) lucide.createIcons();
            } catch (e) {
              Swal.fire({ icon: 'error', title: 'Unable to load', text: e.message || 'Please try again.' });
              console.error(e);
            }
          });
        });
      }

      // EDIT modal
      function bindEditButtons(scope = document) {
        qsa('.js-edit-user', scope).forEach(btn => {
          if (btn._boundEdit) return;
          btn._boundEdit = true;
          btn.addEventListener('click', async () => {
            const id = btn.getAttribute('data-user-id');
            try {
              const u = await fetchUser(id);

              setv('#edit_id', u.id);
              setv('#edit_name', u.name);
              setv('#edit_email', u.email);
              if (qs('#edit_role')) setv('#edit_role', u.role);
              if (qs('#edit_status')) setv('#edit_status', u.status);
              if (qs('#edit_nurse_type')) setv('#edit_nurse_type', u.nurse_type || '');

              const form = qs('#edit_form');
              if (form) form.action = tmpl(UPDATE_URL_TMPL, u.id);

              if (window.lucide) lucide.createIcons();
            } catch (e) {
              Swal.fire({ icon: 'error', title: 'Unable to load for edit', text: e.message || 'Please try again.' });
              console.error(e);
            }
          });
        });
      }

      const editForm = qs('#edit_form');
      if (editForm) {
        editForm.addEventListener('submit', async (e) => {
          e.preventDefault();
          const form = e.currentTarget;
          const fd = new FormData(form);

          try {
            const res = await fetch(form.action, {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
              body: fd
            });

            if (!res.ok) {
              const err = await res.json().catch(() => ({}));
              throw new Error(err.message || 'Update failed');
            }

            qs('#modalUpdate')?.classList.add('hidden');
            Swal.fire({ icon: 'success', title: 'Saved', timer: 1200, showConfirmButton: false });

            await fetchList();
          } catch (err) {
            Swal.fire({ icon: 'error', title: 'Save failed', text: err.message || 'Please try again.' });
            console.error(err);
          }
        });
      }

      // ARCHIVE flow
      const modalArchive = qs('#modalArchive');
      const archiveBtn = qs('#archiveConfirmBtn');
      let currentArchiveId = null;
      let currentArchiveRow = null;

      function bindArchiveButtons(scope = document) {
        qsa('[data-modal-target="modalArchive"]', scope).forEach(btn => {
          if (btn._boundArchive) return;
          btn._boundArchive = true;
          btn.addEventListener('click', () => {
            currentArchiveId = btn.getAttribute('data-user-id');
            currentArchiveRow = btn.closest('tr');
          });
        });
      }

      archiveBtn?.addEventListener('click', async () => {
        if (!currentArchiveId) return;

        try {
          const url = ARCHIVE_URL_TMPL.replace('REPLACE_ID', encodeURIComponent(currentArchiveId));
          const res = await fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrf,
              'Accept': 'application/json'
            },
            body: JSON.stringify({})
          });

          if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.message || 'Failed to archive user.');
          }

          modalArchive?.classList.add('hidden');
          if (currentArchiveRow) currentArchiveRow.remove();

          Swal.fire({
            icon: 'success',
            title: 'Archived',
            text: 'The user has been archived.',
            timer: 1500,
            showConfirmButton: false
          });

          await fetchList();

          currentArchiveId = null;
          currentArchiveRow = null;

        } catch (e) {
          Swal.fire({ icon: 'error', title: 'Archive failed', text: e.message || 'Something went wrong.' });
        }
      });

      // -------------------------
      // Animation for rows
      // -------------------------
      function animateRows(scope = document) {
        const rows = qsa('.js-user-row', scope);
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
      }

      // -------------------------
      // AJAX list (filters + pagination)
      // -------------------------
      function currentParams(overrides = {}) {
        const q = qs('#filter-q')?.value || '';
        const role = qs('#filter-role')?.value || '';
        const status = qs('#filter-status')?.value || '';
        const page = overrides.page ?? '';

        const url = new URL(INDEX_URL, window.location.origin);
        if (q) url.searchParams.set('q', q);
        if (role) url.searchParams.set('role', role);
        if (status) url.searchParams.set('status', status);
        if (page) url.searchParams.set('page', page);

        return url;
      }

      async function fetchList(overrides = {}) {
        showSkeleton();

        const url = currentParams(overrides);
        const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!res.ok) throw new Error('Failed to fetch list');
        const html = await res.text();

        const doc = new DOMParser().parseFromString(html, 'text/html');
        const newTbody = doc.querySelector('#users-tbody');
        const newPag = doc.querySelector('#users-pagination');

        if (newTbody && qs('#users-tbody')) {
          qs('#users-tbody').innerHTML = newTbody.innerHTML;
        }
        if (newPag && qs('#users-pagination')) {
          qs('#users-pagination').innerHTML = newPag.innerHTML;
        }

        window.history.replaceState({}, '', url);

        rebindRowActions();
        if (window.lucide) lucide.createIcons();

        hideSkeleton(); // reveal & animate
      }

      function bindPaginationIntercept(scope = document) {
        qsa('#users-pagination a[href]', scope).forEach(a => {
          if (a._boundPag) return;
          a._boundPag = true;
          a.addEventListener('click', (e) => {
            e.preventDefault();
            const url = new URL(a.href);
            const page = url.searchParams.get('page') || '1';
            fetchList({ page }).catch(console.error);
          });
        });
      }

      function rebindRowActions(scope = document) {
        bindViewButtons(scope);
        bindEditButtons(scope);
        bindArchiveButtons(scope);
        bindPaginationIntercept(scope);
      }

      const onSearch = debounce(() => fetchList().catch(console.error), 350);
      qs('#filter-q')?.addEventListener('input', onSearch);
      qs('#filter-role')?.addEventListener('change', () => fetchList().catch(console.error));
      qs('#filter-status')?.addEventListener('change', () => fetchList().catch(console.error));

      // Initial bind + initial animation after a tiny delay
      rebindRowActions();

      const prefersReduced = window.matchMedia &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      const initialDelay = prefersReduced ? 0 : 220;

      setTimeout(() => {
        hideSkeleton();
      }, initialDelay);

    })();
  </script>

</body>

</html>
