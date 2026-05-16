{{-- resources/views/faculty/equipment-guides/index.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <title>Equipment Guides · NurSync (CI)</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    body { font-family:'Poppins', ui-sans-serif, system-ui, sans-serif; }

    /* Smooth card entrance after skeleton hides (same as CI Procedures) */
    @keyframes slide-in-up {
      from { transform: translateY(10px); opacity: 0; }
      to   { transform: translateY(0);     opacity: 1; }
    }
    .animate-card-in {
      animation: slide-in-up .35s ease-out both;
      will-change: transform, opacity;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Sidebar --}}
  @include('partials.faculty-sidebar', ['active' => 'equipment_guides'])

  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-8 py-12 space-y-6">

      {{-- Page heading (mirror Procedures / Drug Guide style) --}}
      <header class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
            <i data-lucide="wrench" class="h-4 w-4"></i>
          </span>
          <div>
            <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
              Equipment Guides (CI)
            </h1>
            <p class="text-[13px] text-slate-500 mt-1">
              Fast reference for nursing and laboratory equipment by category, ward, and related procedures.
            </p>
          </div>
        </div>

        {{-- New Equipment (desktop, optional) --}}
        <div class="hidden sm:flex">
          @if(($canManage ?? false) && Route::has('faculty.equipment_guides.create'))
            <a href="{{ route('faculty.equipment_guides.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-3.5 py-2.5 text-[13px] font-medium text-white shadow-sm hover:bg-emerald-700">
              <i data-lucide="plus" class="h-4 w-4"></i>
              <span>New Equipment</span>
            </a>
          @endif
        </div>
      </header>

      @php
        // Safety defaults in case route didn't pass them (dev environments)
        $categories = $categories ?? ['Monitoring','Respiratory','Infusion','Laboratory','Surgical','PPE'];
        $wards      = $wards ?? ['MS','ER','ICU','OR','OB','PEDIA','CHN','GERIA','ORTHO','PSYCH','ONCO','CDN'];
      @endphp

      {{-- Search + filters (styled like Procedures / Drug Guide filter card) --}}
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center w-full flex-1">
            {{-- Search --}}
            <div class="relative flex-1 sm:w-64 lg:w-80">
              <i data-lucide="search" class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
              <input id="q" type="text"
                     placeholder="Search item name, uses, notes…"
                     aria-label="Search equipment"
                     class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" />
            </div>

            {{-- Category --}}
            <select id="category" aria-label="Filter by category"
                    class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
              <option value="">All categories</option>
              @foreach($categories as $c)
                <option value="{{ $c }}">{{ $c }}</option>
              @endforeach
            </select>

            {{-- Ward --}}
            <select id="ward" aria-label="Filter by ward"
                    class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
              <option value="">All wards</option>
              @foreach($wards as $w)
                <option value="{{ $w }}">{{ $w }}</option>
              @endforeach
            </select>
          </div>

          {{-- New Equipment (mobile) --}}
          <div class="flex sm:hidden">
            @if(($canManage ?? false) && Route::has('faculty.equipment_guides.create'))
              <a href="{{ route('faculty.equipment_guides.create') }}"
                 class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-3.5 py-2.5 text-[13px] font-medium text-white shadow-sm hover:bg-emerald-700 w-full justify-center">
                <i data-lucide="plus" class="h-4 w-4"></i>
                <span>New Equipment</span>
              </a>
            @endif
          </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center gap-2 pt-1">
          <button id="btnClear"
                  class="rounded-xl border px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Clear filters
          </button>
        </div>
      </div>

      {{-- Results grid (cards + skeleton, same grid pattern as Procedures) --}}
      <div id="grid" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3"></div>

      {{-- Empty state (matching overall style) --}}
      <div id="emptyState" class="hidden rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
        <p class="text-sm text-slate-500">
          No equipment found. Try a different search or remove filters.
        </p>
      </div>

      {{-- Pager (same look as Drug Guide / Procedures pager) --}}
      <div id="pager" class="rounded-2xl border border-slate-200 bg-white p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <div id="summary" class="text-[12px] text-slate-600">Showing 0–0 of 0 items</div>
          <nav class="flex items-center gap-1" aria-label="Pagination">
            <button id="prev"
                    class="rounded-lg border px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:hover:bg-transparent"
                    disabled>‹ Prev</button>
            <div id="pages" class="flex items-center gap-1"></div>
            <button id="next"
                    class="rounded-lg border px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:hover:bg-transparent"
                    disabled>Next ›</button>
          </nav>
        </div>
      </div>

    </div>
  </section>
</main>

{{-- Footer include (faculty > fallback student) --}}
@includeIf('partials.faculty-footer')
@includeWhen(!View::exists('partials.faculty-footer'), 'partials.student-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();

  // --- State ---
  let state = {
    q: '',
    category: '',
    ward: '',
    page: 1,
    per_page: 12,
    total: 0
  };

  const grid     = document.getElementById('grid');
  const emptyEl  = document.getElementById('emptyState');
  const pagerEl  = document.getElementById('pager');
  const summary  = document.getElementById('summary');
  const prevBtn  = document.getElementById('prev');
  const nextBtn  = document.getElementById('next');
  const pagesEl  = document.getElementById('pages');

  const qInput   = document.getElementById('q');
  const catSel   = document.getElementById('category');
  const wardSel  = document.getElementById('ward');
  const btnClear = document.getElementById('btnClear');

  // --- Fetch helper ---
  const DATA_URL = @json(route('faculty.equipment_guides.data'));

  async function fetchData() {
    const params = new URLSearchParams({
      q: state.q,
      category: state.category,
      ward: state.ward,
      page: state.page,
      per_page: state.per_page
    });
    const res = await fetch(`${DATA_URL}?${params.toString()}`, {
      headers: { 'Accept': 'application/json' }
    });
    if (!res.ok) throw new Error('Failed to fetch');
    return await res.json();
  }

  // --- Card template (styled like Procedures / Drug cards) ---
  function cardTemplate(item) {
    const t = (item.item_name || '').toLowerCase();
    const icon =
      t.includes('monitor')    ? 'activity'    :
      t.includes('syringe')    ? 'syringe'     :
      t.includes('catheter')   ? 'tube'        :
      t.includes('pump')       ? 'gauge'       :
      t.includes('mask')       ? 'mask'        :
      t.includes('oxygen')     ? 'wind'        :
      t.includes('microscope') ? 'microscope'  :
      t.includes('centrifuge') ? 'refresh-cw'  :
      'stethoscope';

    const related = (item.related || '')
      .split(',')
      .map(s => s.trim())
      .filter(Boolean)
      .map(slug =>
        `<span class="inline-flex rounded-full border border-slate-200 px-2.5 py-1 text-xs text-slate-600">${escapeHtml(slug)}</span>`
      )
      .join(' ');

    const linkBtn = item.show_url ? `
      <a href="${item.show_url}"
         class="inline-flex items-center gap-1 rounded-xl border border-slate-200 px-3 py-1.5 text-[12px] font-medium text-slate-800 hover:bg-slate-50">
        <i data-lucide="eye" class="h-3 w-3"></i>
        View guide
      </a>` : '';

    return `
      <article class="js-card flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:shadow-md transition-shadow opacity-0">
        <header class="flex items-start justify-between gap-2">
          <div class="flex items-start gap-3">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-50">
              <i data-lucide="${icon}" class="h-4 w-4 text-slate-700"></i>
            </span>
            <div>
              <h3 class="text-[14px] sm:text-[15px] font-semibold text-slate-900 leading-snug">
                ${escapeHtml(item.item_name || '—')}
              </h3>
              <div class="mt-1 text-[12px] text-slate-600">
                ${escapeHtml(item.category || '—')} • ${escapeHtml(item.ward || 'All wards')}
              </div>
            </div>
          </div>
        </header>

        ${ item.variants
          ? `<p class="mt-2 text-[11px] text-slate-600"><span class="font-medium">Variants:</span> ${escapeHtml(item.variants)}</p>`
          : '' }

        ${ item.uses
          ? `<p class="mt-2 text-[12px] text-slate-700">${escapeHtml(item.uses)}</p>`
          : '' }

        ${ related
          ? `<div class="mt-3 flex flex-wrap gap-2">${related}</div>`
          : '' }

        <div class="mt-auto pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2 text-[11px] text-slate-500">
          ${linkBtn}
          ${ item.notes
            ? `<span class="inline-flex items-center gap-1">
                 <i data-lucide="info" class="h-3 w-3"></i>
                 ${escapeHtml(item.notes)}
               </span>`
            : '' }
        </div>
      </article>
    `;
  }

  // --- Skeleton cards (Procedures-style skeleton grid) ---
  function skeletons(n = 9) {
    return Array.from({length:n}).map(() => `
      <div class="rounded-2xl border border-slate-200 bg-white p-4">
        <div class="animate-pulse">
          <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
              <span class="h-8 w-8 rounded-xl bg-slate-200"></span>
              <div class="space-y-2">
                <div class="h-3 w-40 bg-slate-200 rounded"></div>
                <div class="h-3 w-24 bg-slate-100 rounded"></div>
              </div>
            </div>
            <span class="h-6 w-24 rounded-full bg-slate-100"></span>
          </div>
          <div class="mt-3 space-y-2">
            <div class="h-3 w-full bg-slate-100 rounded"></div>
            <div class="h-3 w-5/6 bg-slate-100 rounded"></div>
          </div>
          <div class="mt-3 flex gap-2">
            <span class="h-5 w-16 rounded-full bg-slate-100"></span>
            <span class="h-5 w-20 rounded-full bg-slate-100"></span>
            <span class="h-5 w-12 rounded-full bg-slate-100"></span>
          </div>
          <div class="mt-4 flex gap-2">
            <span class="h-8 w-24 rounded-xl bg-slate-100"></span>
            <span class="h-8 w-24 rounded-xl bg-slate-100"></span>
            <span class="h-8 w-16 rounded-xl bg-slate-100"></span>
          </div>
        </div>
      </div>
    `).join('');
  }

  // --- Animate cards after skeleton (same as Procedures / Drug Guide) ---
  function animateCards() {
    const cards = [...document.querySelectorAll('.js-card')];
    cards.forEach((el, idx) => {
      el.style.animationDelay = `${Math.min(idx, 6) * 40}ms`;
      el.classList.remove('opacity-0');
      el.classList.add('animate-card-in');
    });
  }

  // --- Rendering ---
  function renderList(items) {
    if (!items.length) {
      grid.innerHTML = '';
      emptyEl.classList.remove('hidden');
      pagerEl.style.display = 'none';
      return;
    }
    emptyEl.classList.add('hidden');
    pagerEl.style.display = '';

    grid.innerHTML = items.map(cardTemplate).join('');
    lucide.createIcons();
    requestAnimationFrame(animateCards);
  }

  function renderPager(page, perPage, total) {
    const totalPages = Math.max(1, Math.ceil(total / perPage));
    const start = total ? ((page - 1) * perPage) + 1 : 0;
    const end   = Math.min(page * perPage, total);
    summary.textContent = `Showing ${start}–${end} of ${total} items`;

    prevBtn.disabled = (page <= 1);
    nextBtn.disabled = (page >= totalPages);

    pagesEl.innerHTML = '';
    const windowSize = 5;
    let pStart = Math.max(1, page - Math.floor(windowSize / 2));
    let pEnd   = Math.min(totalPages, pStart + windowSize - 1);
    pStart     = Math.max(1, Math.min(pStart, Math.max(1, totalPages - windowSize + 1)));

    const makeBtn = (label, p, active = false) => {
      const b = document.createElement('button');
      b.type = 'button';
      b.textContent = label;
      b.className = [
        'rounded-lg px-3 py-1.5 text-sm font-medium',
        active ? 'bg-slate-900 text-white' : 'border text-slate-700 hover:bg-slate-50'
      ].join(' ');
      if (!active) {
        b.addEventListener('click', () => { state.page = p; load(true); });
      }
      return b;
    };

    if (pStart > 1) {
      pagesEl.appendChild(makeBtn('1', 1, page === 1));
      if (pStart > 2) pagesEl.appendChild(makeDots());
    }
    for (let p = pStart; p <= pEnd; p++) {
      pagesEl.appendChild(makeBtn(String(p), p, page === p));
    }
    if (pEnd < totalPages) {
      if (pEnd < totalPages - 1) pagesEl.appendChild(makeDots());
      pagesEl.appendChild(makeBtn(String(totalPages), totalPages, page === totalPages));
    }
  }

  function makeDots() {
    const s = document.createElement('span');
    s.className = 'px-1 text-slate-400 select-none';
    s.textContent = '…';
    return s;
  }

  // --- Debounce ---
  const debounce = (fn, ms = 300) => {
    let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); };
  };

  // --- Events ---
  qInput.addEventListener('input', debounce(() => {
    state.q = qInput.value;
    state.page = 1;
    load();
  }, 250));

  catSel.addEventListener('change', () => {
    state.category = catSel.value;
    state.page = 1;
    load();
  });

  wardSel.addEventListener('change', () => {
    state.ward = wardSel.value;
    state.page = 1;
    load();
  });

  btnClear.addEventListener('click', () => {
    qInput.value = '';
    catSel.value = '';
    wardSel.value = '';
    state = { q:'', category:'', ward:'', page:1, per_page:12, total:0 };
    load();
  });

  prevBtn.addEventListener('click', () => {
    if (state.page > 1) {
      state.page--;
      load(true);
    }
  });

  nextBtn.addEventListener('click', () => {
    state.page++;
    load(true);
  });

  // --- Load with skeleton + animation ---
  async function load(preserveScroll = false) {
    const topY = pagerEl.getBoundingClientRect().top + window.scrollY;
    grid.innerHTML = skeletons(state.per_page);

    try {
      const data = await fetchData();
      state.total = data.total ?? 0;
      renderList(data.items ?? []);
      renderPager(data.page ?? state.page, data.per_page ?? state.per_page, data.total ?? 0);

      if (!preserveScroll) {
        window.scrollTo({ top: topY - 220, behavior: 'smooth' });
      }
    } catch (e) {
      grid.innerHTML = `
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-rose-600">
          Failed to load equipment guides.
        </div>`;
    }
  }

  // HTML-escape helper
  function escapeHtml(s) {
    return (s ?? '').replace(/[&<>"']/g, m => ({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
    }[m]));
  }

  // Initial load
  load();
</script>
</body>
</html>
