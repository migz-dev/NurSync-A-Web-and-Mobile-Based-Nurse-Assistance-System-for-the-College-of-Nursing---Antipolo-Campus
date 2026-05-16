{{-- resources/views/admin/admin-return-demo-skills.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Admin • Return Demo Skills · NurSync</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; }

    /* Row entrance animation (same vibe as Admin Users / Procedures) */
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
  @include('partials.admin-sidebar', ['active' => 'resources'])

  <section class="flex-1 min-w-0">
    {{-- Header --}}
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-900 text-white shadow-sm">
            <i data-lucide="clipboard-check" class="h-4 w-4"></i>
          </div>
          <div>
            <h1 class="text-[15px] sm:text-[16px] font-semibold leading-tight">Return Demo Skills</h1>
            <p class="text-[12px] text-slate-500 -mt-0.5">Manage and review skills for return demonstrations.</p>
          </div>
        </div>

        <div class="flex items-center gap-2">
          {{-- Add (opens modal to pick from Procedures) --}}
          <button type="button"
                  data-modal-open="#modalPickProcedure"
                  class="inline-flex items-center gap-2 rounded-xl bg-green-600 text-white px-3 py-2 text-[13px] font-medium shadow hover:bg-green-700 active:scale-[.99]">
            <i data-lucide="plus" class="h-4 w-4"></i>
            <span>Add</span>
          </button>

          {{-- Return Demo Skills (you are here) --}}
          <a href="{{ route('admin.return_demo.skills.index') }}"
             class="inline-flex items-center gap-2 rounded-xl bg-amber-500 text-white px-3 py-2 text-[13px] font-medium shadow hover:bg-amber-600 active:scale-[.99]">
            <i data-lucide="clipboard-check" class="h-4 w-4"></i>
            <span>Return Demo Skills</span>
          </a>
        </div>
      </div>
    </header>

    @php $skills = $skills ?? null; @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">

      {{-- Flash --}}
      @if (session('ok'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
          {{ session('ok') }}
        </div>
      @endif
      @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
          {{ $errors->first() }}
        </div>
      @endif

      @php
        $wards = [
          'Community Health (CHN)','Delivery Room (DR)','Disaster Response / Community Field','Emergency Room (ER)',
          'Endocrine Unit','ICU','Isolation Unit','Medical-Surgical (MS)','Neurology Unit','Nursery','OB Ward','Oncology',
          'Operating Room (OR)','Pediatrics (PEDIA)','Psychiatric (PSYCH)','Trauma Unit',
        ];
        sort($wards, SORT_NATURAL | SORT_FLAG_CASE);
      @endphp

      {{-- SKELETON: filters + table --}}
      <div id="skillsSkeleton" aria-hidden="true" class="space-y-4">
        {{-- Skeleton filters --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
          <div class="animate-pulse flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
            <div class="flex items-center gap-3 w-full sm:w-auto">
              <div class="h-10 w-full sm:w-72 bg-slate-100 rounded-xl"></div>
              <div class="h-10 w-40 bg-slate-100 rounded-xl hidden sm:block"></div>
              <div class="h-10 w-44 bg-slate-100 rounded-xl hidden sm:block"></div>
            </div>
            <div class="flex items-center gap-2">
              <div class="h-8 w-24 bg-slate-100 rounded-xl"></div>
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
                  <div class="flex-1 space-y-1">
                    <div class="h-3 w-64 bg-slate-100 rounded"></div>
                    <div class="h-3 w-40 bg-slate-100 rounded"></div>
                  </div>
                </div>
              @endfor
            </div>
          </div>
        </div>
      </div>

      {{-- REAL CONTENT (hidden until skeleton is done) --}}
      <div id="skillsReal" class="space-y-6 hidden">

        {{-- Filters --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
          <form class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" onsubmit="return false;">
            <div class="flex flex-1 flex-col sm:flex-row gap-3 sm:items-center">
              <div class="relative flex-1 sm:w-72">
                <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
                <input id="filter-q" type="text" value="{{ request('q') }}" placeholder="Search skill name…"
                       class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300">
              </div>

              <select id="filter-status"
                      class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                <option value="">All statuses</option>
                <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                <option value="published" @selected(request('status') === 'published')>Published</option>
              </select>

              <select id="filter-ward"
                      class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                <option value="">All wards/areas</option>
                @foreach($wards as $w)
                  <option value="{{ $w }}" @selected(request('ward') === $w)>{{ $w }}</option>
                @endforeach
              </select>
            </div>
          </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-slate-50 border-b border-slate-200">
                <tr class="text-left text-slate-600">
                  <th class="px-4 py-3">Skill Name</th>
                  <th class="px-4 py-3">Wards</th>
                  <th class="px-4 py-3">Status</th>
                  <th class="px-4 py-3">Created</th>
                </tr>
              </thead>
              <tbody id="skills-table" class="divide-y divide-slate-200">
                @if ($skills)
                  @php $rendered = 0; @endphp
                  @foreach ($skills as $s)
                    @if (!empty($s->is_archived) && (int) $s->is_archived === 1)
                      @continue
                    @endif
                    @php $rendered++; @endphp
                    {{-- animated row --}}
                    <tr class="hover:bg-slate-50 js-skill-row opacity-0">
                      <td class="px-4 py-3 font-medium text-slate-900">{{ $s->title ?: '—' }}</td>
                      <td class="px-4 py-3 text-slate-700">{{ $s->clinical_wards ?: '—' }}</td>
                      <td class="px-4 py-3">
                        @if ($s->status === 'published')
                          <span class="inline-flex items-center gap-1.5 rounded-lg bg-green-50 text-green-700 px-2 py-1 text-[12px] font-medium">
                            <i data-lucide="check-circle" class="h-3.5 w-3.5"></i> Published
                          </span>
                        @else
                          <span class="inline-flex items-center gap-1.5 rounded-lg bg-yellow-50 text-yellow-700 px-2 py-1 text-[12px] font-medium">
                            <i data-lucide="clock" class="h-3.5 w-3.5"></i> Draft
                          </span>
                        @endif
                      </td>
                      <td class="px-4 py-3 text-slate-700">{{ optional($s->created_at)->format('M d, Y') ?: '—' }}</td>
                    </tr>
                  @endforeach

                  @if ($rendered === 0)
                    <tr>
                      <td colspan="4" class="px-4 py-8 text-center text-slate-500">No skills found.</td>
                    </tr>
                  @endif
                @else
                  {{-- Fallback if JS disabled --}}
                  <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-slate-500">Loading…</td>
                  </tr>
                @endif
              </tbody>
            </table>
          </div>

          {{-- Pagination --}}
          @if ($skills instanceof \Illuminate\Contracts\Pagination\Paginator)
            @php
              $cur = $skills->currentPage();
              $last = max(1, $skills->lastPage());
              $window = 3; $half = intdiv($window - 1, 2);
              $from = max(1, $cur - $half); $to = min($last, $from + $window - 1);
              $from = max(1, $to - $window + 1);
            @endphp
            <div id="skills-pager" class="flex items-center justify-end px-4 py-3 border-t border-slate-200 bg-slate-50">
              <nav class="flex items-center gap-1">
                @if ($skills->onFirstPage())
                  <button class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white opacity-50 cursor-not-allowed">
                    <i data-lucide="chevron-left" class="h-4 w-4"></i>
                  </button>
                @else
                  <button data-page="{{ $skills->currentPage() - 1 }}"
                          class="js-page h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
                    <i data-lucide="chevron-left" class="h-4 w-4"></i>
                  </button>
                @endif

                @for ($i = $from; $i <= $to; $i++)
                  @if ($i === $cur)
                    <span class="h-9 w-9 inline-flex items-center justify-center rounded-lg bg-slate-900 text-white">{{ $i }}</span>
                  @else
                    <button data-page="{{ $i }}"
                            class="js-page h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
                      {{ $i }}
                    </button>
                  @endif
                @endfor

                @if ($cur >= $last)
                  <button class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white opacity-50 cursor-not-allowed">
                    <i data-lucide="chevron-right" class="h-4 w-4"></i>
                  </button>
                @else
                  <button data-page="{{ $skills->currentPage() + 1 }}"
                          class="js-page h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
                    <i data-lucide="chevron-right" class="h-4 w-4"></i>
                  </button>
                @endif
              </nav>
            </div>
          @else
            <div id="skills-pager" class="flex items-center justify-end px-4 py-3 border-t border-slate-200 bg-slate-50"></div>
          @endif
        </div>
      </div>
    </div>
  </section>
</main>

{{-- Footer --}}
@include('partials.admin-footer')

{{-- Picker Modal (partial) --}}
@include('admin.return_demo_skills._modal-pick-procedure')

<script src="https://unpkg.com/lucide@latest"></script>
<script> if (window.lucide?.createIcons) lucide.createIcons(); </script>

<script>
  // --- helpers
  const qs = s => document.querySelector(s);
  const qsa = s => Array.from(document.querySelectorAll(s));
  const debounce = (fn, ms = 300) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); } };

  const INDEX_URL = "{{ route('admin.return_demo.skills.index') }}";

  const skeleton = qs('#skillsSkeleton');
  const realWrap = qs('#skillsReal');

  // Row animation
  function animateRows(scope = document) {
    const rows = qsa('.js-skill-row', scope);
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

  function revealSkillsWithAnimation() {
    if (skeleton) skeleton.classList.add('hidden');
    if (realWrap) realWrap.classList.remove('hidden');
    animateRows();
    if (window.lucide?.createIcons) lucide.createIcons();
  }

  async function fetchList(params = {}) {
    const url = new URL(INDEX_URL, window.location.origin);
    const q = params.q ?? (qs('#filter-q')?.value || '');
    const status = params.status ?? (qs('#filter-status')?.value || '');
    const ward = params.ward ?? (qs('#filter-ward')?.value || '');
    const page = params.page ?? '';

    if (q) url.searchParams.set('q', q);
    if (status) url.searchParams.set('status', status);
    if (ward) url.searchParams.set('ward', ward);
    if (page) url.searchParams.set('page', page);
    url.searchParams.set('archived', '0'); // active list only

    const table = qs('#skills-table');
    const pager = qs('#skills-pager');
    if (table) {
      table.innerHTML = `<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">Loading…</td></tr>`;
    }

    try {
      const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      if (!res.ok) throw new Error('Fetch failed');
      const data = await res.json();

      if (table) table.innerHTML = data.rows || '';
      if (pager) pager.innerHTML = data.pager || '';

      if (window.lucide?.createIcons) lucide.createIcons();
      bindPager();
      animateRows(); // animate freshly loaded rows
    } catch {
      if (table) {
        table.innerHTML = `<tr><td colspan="4" class="px-4 py-8 text-center text-red-600">Failed to load list.</td></tr>`;
      }
    }
  }
  window.fetchList = fetchList; // for modal to refresh after import

  function bindFilters() {
    const $q = qs('#filter-q');
    const $status = qs('#filter-status');
    const $ward = qs('#filter-ward');
    if ($q) $q.addEventListener('input', debounce(() => fetchList({ page: 1 }), 300));
    if ($status) $status.addEventListener('change', () => fetchList({ page: 1 }));
    if ($ward) $ward.addEventListener('change', () => fetchList({ page: 1 }));
  }

  function bindPager() {
    const $pager = qs('#skills-pager');
    if (!$pager) return;
    $pager.querySelectorAll('.js-page').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const page = e.currentTarget.getAttribute('data-page');
        if (page) fetchList({ page });
      }, { once: true });
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    const prefersReduced = window.matchMedia &&
      window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const delay = prefersReduced ? 0 : 220;
    setTimeout(revealSkillsWithAnimation, delay);

    bindFilters();
    bindPager();

    @if (!$skills)
      fetchList({ page: 1 });
    @else
      // animate server-rendered rows on first load
      animateRows();
    @endif
  });
</script>

{{-- Modal toggler (open/close + ESC + overlay click + focus trap + auto-load) --}}
<script>
  (function () {
    const qs = s => document.querySelector(s);
    const qsa = s => Array.from(document.querySelectorAll(s));
    const isHidden = el => el?.classList.contains('hidden');
    const hide = el => el?.classList.add('hidden');
    const show = el => el?.classList.remove('hidden');

    // focus trap scoped to modal
    function trapFocus(modal) {
      const focusables = qsa('a[href],button,textarea,input,select,[tabindex]:not([tabindex="-1"])')
        .filter(el => modal.contains(el) && !el.hasAttribute('disabled') && el.getAttribute('aria-hidden') !== 'true');
      const first = focusables[0], last = focusables[focusables.length - 1];
      function onKey(e) {
        if (e.key !== 'Tab') return;
        if (!focusables.length) return;
        if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
      }
      modal._trapHandler = onKey;
      document.addEventListener('keydown', onKey);
      first?.focus();
    }
    function untrapFocus(modal) {
      if (modal?._trapHandler) {
        document.removeEventListener('keydown', modal._trapHandler);
        delete modal._trapHandler;
      }
    }

    document.addEventListener('click', (e) => {
      const opener = e.target.closest('[data-modal-open]');
      if (opener) {
        e.preventDefault();
        const sel = opener.getAttribute('data-modal-open');
        const modal = qs(sel);
        if (modal) {
          show(modal);
          modal.style.zIndex = '60';
          if (window.lucide?.createIcons) lucide.createIcons();
          trapFocus(modal);

          // auto-load procedures for the picker when opened (if defined)
          if (typeof window._pp_reload === 'function') {
            try { window._pp_reload(); } catch (_) {}
          }
        }
        return;
      }

      const closer = e.target.closest('[data-modal-close]');
      if (closer) {
        e.preventDefault();
        const modal = closer.closest('[id^="modal"]');
        if (modal) { hide(modal); untrapFocus(modal); }
        return;
      }

      // overlay clicks (element must have data-modal-overlay)
      const overlay = e.target.matches('[data-modal-overlay]') ? e.target : null;
      if (overlay) {
        const modal = overlay.closest('[id^="modal"]');
        if (modal) { hide(modal); untrapFocus(modal); }
      }
    });

    // ESC to close topmost visible modal
    document.addEventListener('keydown', (e) => {
      if (e.key !== 'Escape') return;
      const openModals = qsa('[id^="modal"]:not(.hidden)');
      const top = openModals.pop();
      if (top) { hide(top); untrapFocus(top); }
    });
  })();
</script>
</body>
</html>
