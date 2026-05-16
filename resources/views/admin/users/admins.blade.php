{{-- resources/views/admin/users/admins.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Admin • Admins · NurSync</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }

    /* Row entrance animation (same vibe as admin users) */
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
          <div class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-sm">
            <i data-lucide="shield" class="h-4 w-4"></i>
          </div>
          <div>
            <h1 class="text-[15px] sm:text-[16px] font-semibold leading-tight">Admins</h1>
            <p class="text-[12px] text-slate-500 -mt-0.5">View and manage system administrator accounts.</p>
          </div>
        </div>

        {{-- Primary actions --}}
        <div class="flex items-center gap-2">
          {{-- Back to Users --}}
          <a href="{{ route('admin.users.index') }}"
             class="inline-flex items-center gap-2 rounded-xl bg-slate-700 text-white px-3 py-2 text-[13px] font-medium shadow hover:bg-slate-800 active:scale-[.99]">
            <i data-lucide="users" class="h-4 w-4"></i>
            <span>All Users</span>
          </a>
        </div>
      </div>
    </header>

    {{-- Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">

      {{-- SKELETON: filters + table --}}
      <div id="adminsSkeleton" aria-hidden="true" class="space-y-4">
        {{-- Skeleton filters --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
          <div class="animate-pulse flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
            <div class="flex items-center gap-3 w-full sm:w-auto">
              <div class="h-10 w-full sm:w-72 bg-slate-100 rounded-xl"></div>
              <div class="h-10 w-40 bg-slate-100 rounded-xl hidden sm:block"></div>
            </div>
            <div class="flex items-center gap-2">
              <div class="h-8 w-20 bg-slate-100 rounded-xl"></div>
            </div>
          </div>
        </div>

        {{-- Skeleton table card --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
          <div class="animate-pulse space-y-3">
            <div class="h-4 w-40 bg-slate-100 rounded"></div>
            <div class="space-y-2 mt-3">
              @for ($i = 0; $i < 4; $i++)
                <div class="flex items-center gap-3">
                  <div class="h-9 w-9 rounded-xl bg-slate-100"></div>
                  <div class="flex-1 space-y-1">
                    <div class="h-3 w-48 bg-slate-100 rounded"></div>
                    <div class="h-3 w-32 bg-slate-100 rounded"></div>
                  </div>
                  <div class="h-8 w-24 bg-slate-100 rounded-lg ml-auto"></div>
                </div>
              @endfor
            </div>
          </div>
        </div>
      </div>

      {{-- REAL content (hidden until skeleton is done) --}}
      <div id="adminsReal" class="space-y-6 hidden">

        {{-- Filters / tools --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
          <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
            <div class="flex items-center gap-3 w-full sm:w-auto">
              <div class="relative flex-1 sm:w-72">
                <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
                <input id="filter-q" type="text" placeholder="Search name, email..."
                       value="{{ $q ?? '' }}"
                       class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300">
              </div>

              {{-- Status only (Active/Inactive/Archived) --}}
              <select id="filter-status"
                      class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                <option value="" {{ ($status ?? '')==='' ? 'selected' : '' }}>All status</option>
                <option value="active" {{ ($status ?? '')==='active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ ($status ?? '')==='inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="archived" {{ ($status ?? '')==='archived' ? 'selected' : '' }}>Archived</option>
              </select>
            </div>
            <div class="flex items-center gap-2"></div>
          </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-slate-50 border-b border-slate-200">
                <tr class="text-left text-slate-600">
                  <th class="px-4 py-3 w-10"><input type="checkbox" class="rounded border-slate-300"></th>
                  <th class="px-4 py-3">Name</th>
                  <th class="px-4 py-3">Email</th>
                  <th class="px-4 py-3">Role</th>
                  <th class="px-4 py-3">Status</th>
                  <th class="px-4 py-3">Created</th>
                  <th class="px-4 py-3 text-right">Actions</th>
                </tr>
              </thead>

              <tbody id="users-tbody" class="divide-y divide-slate-200">
              @forelse ($adminsPage as $a)
                @php
                  $isActive = strtolower($a->status) === 'active';
                  $pill = ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'icon' => 'shield']; // Admin pill
                @endphp
                {{-- animate rows --}}
                <tr class="hover:bg-slate-50 js-admin-row opacity-0">
                  <td class="px-4 py-3"><input type="checkbox" class="rounded border-slate-300"></td>

                  {{-- Name + avatar --}}
                  <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                      @if ($a->avatar_url)
                        <img src="{{ $a->avatar_url }}" alt="{{ $a->name }}" class="h-9 w-9 rounded-xl object-cover">
                      @else
                        <div class="h-9 w-9 rounded-xl bg-slate-100 flex items-center justify-center">
                          <i data-lucide="user" class="h-4 w-4 text-slate-500"></i>
                        </div>
                      @endif
                      <div>
                        <div class="font-medium text-slate-900">{{ $a->name }}</div>
                        <div class="text-[12px] text-slate-500">ID: {{ $a->id }}</div>
                      </div>
                    </div>
                  </td>

                  {{-- Email --}}
                  <td class="px-4 py-3 text-slate-700">{{ $a->email ?? '—' }}</td>

                  {{-- Role pill --}}
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1.5 rounded-lg {{ $pill['bg'] }} {{ $pill['text'] }} px-2 py-1 text-[12px] font-medium">
                      <i data-lucide="{{ $pill['icon'] }}" class="h-3.5 w-3.5"></i> Admin
                    </span>
                  </td>

                  {{-- Status pill --}}
                  <td class="px-4 py-3">
                    @if ($isActive)
                      <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 text-emerald-700 px-2 py-1 text-[12px] font-medium">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Active
                      </span>
                    @elseif (strtolower($a->status) === 'archived')
                      <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 text-slate-600 px-2 py-1 text-[12px] font-medium">
                        <span class="h-2 w-2 rounded-full bg-slate-400"></span> Archived
                      </span>
                    @else
                      <span class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 text-amber-700 px-2 py-1 text-[12px] font-medium">
                        <span class="h-2 w-2 rounded-full bg-amber-500"></span> Inactive
                      </span>
                    @endif
                  </td>

                  {{-- Created --}}
                  <td class="px-4 py-3 text-slate-700">
                    {{ \Illuminate\Support\Carbon::parse($a->created_at)->format('M d, Y') }}
                  </td>

                  {{-- Actions --}}
                  <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-1.5">
                      {{-- VIEW --}}
                      <button type="button"
                              class="inline-flex items-center justify-center rounded-lg bg-blue-600 text-white p-2 hover:bg-blue-700 js-view-user"
                              data-user-id="{{ $a->id }}" data-modal-target="modalRead">
                        <i data-lucide="eye" class="h-4 w-4"></i>
                      </button>

                      {{-- EDIT --}}
                      <button type="button"
                              class="inline-flex items-center justify-center rounded-lg bg-yellow-400 text-slate-900 p-2 hover:brightness-95 js-edit-user"
                              data-user-id="{{ $a->id }}" data-modal-target="modalUpdate">
                        <i data-lucide="pencil" class="h-4 w-4"></i>
                      </button>

                      {{-- ARCHIVE --}}
                      <button type="button"
                              class="inline-flex items-center justify-center rounded-lg bg-orange-500 text-white p-2 hover:bg-orange-600"
                              data-modal-target="modalArchive" data-user-id="{{ $a->id }}" data-user-name="{{ $a->name }}">
                        <i data-lucide="archive" class="h-4 w-4"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="px-4 py-8 text-center text-slate-500">No admins found.</td>
                </tr>
              @endforelse
              </tbody>
            </table>
          </div>

          {{-- Footer / pagination --}}
          <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 border-t border-slate-200 bg-slate-50">
            <div class="flex items-center gap-2"></div>

            @php
              $cur = $adminsPage->currentPage();
              $last = max(1, $adminsPage->lastPage());
              $window = 3; $half = intdiv($window - 1, 2);
              $from = max(1, $cur - $half); $to = min($last, $from + $window - 1);
              $from = max(1, $to - $window + 1);
            @endphp

            <nav id="users-pagination" class="flex items-center gap-1">
              {{-- Prev --}}
              @if ($adminsPage->onFirstPage())
                <button class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white opacity-50 cursor-not-allowed">
                  <i data-lucide="chevron-left" class="h-4 w-4"></i>
                </button>
              @else
                <a href="{{ $adminsPage->previousPageUrl() }}"
                   class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
                  <i data-lucide="chevron-left" class="h-4 w-4"></i>
                </a>
              @endif

              {{-- Numbers --}}
              @for ($i = $from; $i <= $to; $i++)
                @if ($i === $cur)
                  <span class="h-9 w-9 inline-flex items-center justify-center rounded-lg bg-slate-900 text-white">{{ $i }}</span>
                @else
                  <a href="{{ $adminsPage->url($i) }}"
                     class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
                    {{ $i }}
                  </a>
                @endif
              @endfor

              {{-- Next --}}
              @if ($cur >= $last)
                <button class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white opacity-50 cursor-not-allowed">
                  <i data-lucide="chevron-right" class="h-4 w-4"></i>
                </button>
              @else
                <a href="{{ $adminsPage->nextPageUrl() }}"
                   class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
                  <i data-lucide="chevron-right" class="h-4 w-4"></i>
                </a>
              @endif
            </nav>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

{{-- Reuse your modals --}}
@push('modals')
  @include('admin.users._modals')
@endpush
@stack('modals')

@include('partials.admin-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(() => {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const qs  = (s, root=document) => root.querySelector(s);
  const qsa = (s, root=document) => Array.from(root.querySelectorAll(s));
  const debounce = (fn, ms=350) => { let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), ms);} };

  const INDEX_URL        = "{{ route('admin.users.admins') }}";
  const SHOW_URL_TMPL    = @json(route('admin.users.show', ':id'));
  const UPDATE_URL_TMPL  = @json(route('admin.users.update', ':id'));
  const ARCHIVE_URL_TMPL = @json(route('admin.users.archive', ['id' => 'REPLACE_ID']));

  if (window.lucide) lucide.createIcons();

  const text = (sel, val)=>{ const el=qs(sel); if (el) el.textContent = (val ?? '—'); };
  const setv  = (sel, val)=>{ const el=qs(sel); if (el) el.value = (val ?? ''); };
  const tmpl  = (url,id)=> url.replace(':id', encodeURIComponent(id));

  const skeleton = qs('#adminsSkeleton');
  const realWrap = qs('#adminsReal');

  // Row animation (same pattern as other admin pages)
  function animateRows(scope = document) {
    const rows = qsa('.js-admin-row', scope);
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

  // Reveal real content after skeleton
  function revealWithAnimation() {
    if (skeleton) skeleton.classList.add('hidden');
    if (realWrap) realWrap.classList.remove('hidden');

    animateRows();
    if (window.lucide) lucide.createIcons();
  }

  document.addEventListener('DOMContentLoaded', () => {
    const prefersReduced = window.matchMedia &&
      window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const delay = prefersReduced ? 0 : 220;
    setTimeout(revealWithAnimation, delay);
  });

  // Modal open/close
  qsa('[data-modal-target]').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      qs('#'+btn.getAttribute('data-modal-target'))?.classList.remove('hidden');
      if (window.lucide) lucide.createIcons();
    });
  });
  qsa('[data-modal-close]').forEach(btn=>{
    btn.addEventListener('click', ()=> btn.closest('.fixed.inset-0')?.classList.add('hidden'));
  });
  qsa('.fixed.inset-0').forEach(modal=>{
    modal.addEventListener('click', e=>{ if (e.target===modal) modal.classList.add('hidden'); });
  });

  // Fetch one admin
  async function fetchUser(id){
    const url = tmpl(SHOW_URL_TMPL, id);
    const res = await fetch(url, { headers: { 'Accept':'application/json' }});
    if (!res.ok) throw new Error('Failed to load admin #'+id);
    return await res.json();
  }

  // Bind view/edit
  function bindView(scope=document){
    qsa('.js-view-user', scope).forEach(btn=>{
      if (btn._bView) return; btn._bView = true;
      btn.addEventListener('click', async ()=>{
        const id = btn.getAttribute('data-user-id');
        try{
          const u = await fetchUser(id);
          const avatar = qs('#view_avatar');
          if (avatar) avatar.src = u.avatar_url || '{{ asset('placeholder-avatar.png') }}';
          text('#view_name',   u.name);
          text('#view_email',  u.email);
          text('#view_role',   u.role);
          text('#view_status', u.status);
          text('#view_created',u.created_at);
          const nt = qs('#view_nurse_type'); if (nt) nt.textContent = '—';

          const archWrap = qs('#view_archived_wrap');
          if (archWrap){
            if (u.archived_at){ archWrap.classList.remove('hidden'); text('#view_archived', u.archived_at); }
            else archWrap.classList.add('hidden');
          }
          if (window.lucide) lucide.createIcons();
        }catch(e){
          Swal.fire({icon:'error', title:'Unable to load', text:e.message || 'Try again.'});
        }
      });
    });
  }

  function bindEdit(scope=document){
    qsa('.js-edit-user', scope).forEach(btn=>{
      if (btn._bEdit) return; btn._bEdit = true;
      btn.addEventListener('click', async ()=>{
        const id = btn.getAttribute('data-user-id');
        try{
          const u = await fetchUser(id);
          setv('#edit_id', u.id);
          setv('#edit_name', u.name);
          setv('#edit_email', u.email);
          if (qs('#edit_role')) setv('#edit_role', u.role);
          if (qs('#edit_status')) setv('#edit_status', u.status);
          const form = qs('#edit_form'); if (form) form.action = tmpl(UPDATE_URL_TMPL, u.id);
          if (window.lucide) lucide.createIcons();
        }catch(e){
          Swal.fire({icon:'error', title:'Unable to load for edit', text:e.message || 'Try again.'});
        }
      });
    });
  }

  const editForm = qs('#edit_form');
  if (editForm){
    editForm.addEventListener('submit', async (e)=>{
      e.preventDefault();
      const fd = new FormData(editForm);
      try{
        const res = await fetch(editForm.action, {
          method:'POST',
          headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'},
          body:fd
        });
        if (!res.ok) throw new Error((await res.json()).message || 'Update failed');
        qs('#modalUpdate')?.classList.add('hidden');
        Swal.fire({icon:'success', title:'Saved', timer:1200, showConfirmButton:false});
        await fetchList();
      }catch(err){
        Swal.fire({icon:'error', title:'Save failed', text:err.message || 'Please try again.'});
      }
    });
  }

  // Archive flow
  const modalArchive = qs('#modalArchive');
  const archiveBtn   = qs('#archiveConfirmBtn');
  let currentArchiveId = null, currentArchiveRow = null;

  qsa('[data-modal-target="modalArchive"]').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      currentArchiveId = btn.getAttribute('data-user-id');
      currentArchiveRow = btn.closest('tr');
    });
  });

  archiveBtn?.addEventListener('click', async ()=>{
    if (!currentArchiveId) return;
    try{
      const url = ARCHIVE_URL_TMPL.replace('REPLACE_ID', encodeURIComponent(currentArchiveId));
      const res = await fetch(url, {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},
        body:JSON.stringify({})
      });
      if (!res.ok) throw new Error((await res.json()).message || 'Failed to archive.');
      modalArchive?.classList.add('hidden');
      if (currentArchiveRow) currentArchiveRow.remove();
      Swal.fire({icon:'success', title:'Archived', timer:1500, showConfirmButton:false});
      await fetchList();
      currentArchiveId = null; currentArchiveRow = null;
    }catch(e){
      Swal.fire({icon:'error', title:'Archive failed', text:e.message || 'Something went wrong.'});
    }
  });

  // AJAX list refresh
  function currentParams(overrides={}) {
    const q = qs('#filter-q')?.value || '';
    const status = qs('#filter-status')?.value || '';
    const page = overrides.page ?? '';
    const url = new URL(INDEX_URL, window.location.origin);
    if (q) url.searchParams.set('q', q);
    if (status) url.searchParams.set('status', status);
    if (page) url.searchParams.set('page', page);
    return url;
  }

  async function fetchList(overrides={}) {
    const url = currentParams(overrides);
    const res = await fetch(url.toString(), { headers:{'X-Requested-With':'XMLHttpRequest'} });
    if (!res.ok) return;
    const html = await res.text();
    const doc = new DOMParser().parseFromString(html, 'text/html');
    const newTbody = doc.querySelector('#users-tbody');
    const newPag   = doc.querySelector('#users-pagination');

    if (newTbody && qs('#users-tbody')) qs('#users-tbody').innerHTML = newTbody.innerHTML;
    if (newPag && qs('#users-pagination')) qs('#users-pagination').innerHTML = newPag.innerHTML;

    window.history.replaceState({}, '', url);
    rebind();
    animateRows(); // animate freshly loaded rows
    if (window.lucide) lucide.createIcons();
  }

  function bindPagination(scope=document){
    qsa('#users-pagination a[href]', scope).forEach(a=>{
      if (a._bPag) return; a._bPag = true;
      a.addEventListener('click', (e)=>{
        e.preventDefault();
        const page = (new URL(a.href)).searchParams.get('page') || '1';
        fetchList({ page }).catch(console.error);
      });
    });
  }

  function rebind(scope=document){
    bindView(scope);
    bindEdit(scope);
    bindPagination(scope);
  }

  const onSearch = debounce(()=> fetchList().catch(console.error), 350);
  qs('#filter-q')?.addEventListener('input', onSearch);
  qs('#filter-status')?.addEventListener('change', ()=> fetchList().catch(console.error));

  // Initial wiring
  rebind();
})();
</script>
</body>
</html>
