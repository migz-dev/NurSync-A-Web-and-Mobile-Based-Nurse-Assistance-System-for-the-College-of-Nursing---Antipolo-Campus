{{-- resources/views/faculty/drug_guide/index.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <title>Drug Guide · NurSync (CI)</title>
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
  @include('partials.faculty-sidebar', ['active' => 'drug_guide'])

  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-8 py-12 space-y-6">

      {{-- Page heading (mirrors CI Procedures header) --}}
      <header class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
            <i data-lucide="pill" class="h-4 w-4"></i>
          </span>
          <div>
            <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
              Drug Guide (CI)
            </h1>
            <p class="text-[13px] text-slate-500 mt-1">
              Fast lookup for brand and generic names with dosage, form, class, packaging, and registration details.
            </p>
          </div>
        </div>

        {{-- New Drug (desktop) --}}
        <div class="hidden sm:flex">
          @if($canManage ?? false)
            <a href="{{ route('faculty.drug_guide.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-3.5 py-2.5 text-[13px] font-medium text-white shadow-sm hover:bg-emerald-700">
              <i data-lucide="plus" class="h-4 w-4"></i>
              <span>New Drug</span>
            </a>
          @endif
        </div>
      </header>

{{-- Search + filters (styled like Procedures filter card) --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3">
  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    
    {{-- All filters in ONE container --}}
    <div class="flex flex-col gap-3 md:flex-row md:flex-wrap md:items-center w-full flex-1">
      {{-- Search --}}
      <div class="relative flex-1 min-w-[200px] md:min-w-[240px] lg:min-w-[260px]">
        <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
        <input id="q" type="text"
               placeholder="Search brand, generic, reg no., strength…"
               class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" />
      </div>

      {{-- Dosage form --}}
      <select id="form_id"
              class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300 min-w-[180px]">
        <option value="">All dosage forms</option>
        @foreach($forms as $f)
          <option value="{{ $f->id }}">{{ $f->name }}</option>
        @endforeach
      </select>

      {{-- Category --}}
      <select id="cat_id"
              class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300 min-w-[180px]">
        <option value="">All categories</option>
        @foreach($cats as $c)
          <option value="{{ $c->id }}">{{ $c->name }}</option>
        @endforeach
      </select>

      {{-- Drug Class (normalized) --}}
      <select id="drug_class"
              class="rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 min-w-[180px]">
        <option value="">All drug classes</option>
        @foreach($classGroups as $g)
          <option value="{{ $g }}">{{ $g }}</option>
        @endforeach
      </select>

      {{-- Packaging Type (normalized) --}}
      <select id="packaging_type"
              class="rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 min-w-[180px]">
        <option value="">All packaging types</option>
        @foreach($packagingTypes as $p)
          <option value="{{ $p }}">{{ $p }}</option>
        @endforeach
      </select>

      {{-- Clear filters --}}
      <button id="btnClear"
              class="rounded-xl border px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
        Clear
      </button>
    </div>

    {{-- New Drug (desktop) --}}
    <div class="hidden sm:flex">
      @if($canManage ?? false)
        <a href="{{ route('faculty.drug_guide.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-3.5 py-2.5 text-[13px] font-medium text-white shadow-sm hover:bg-emerald-700">
          <i data-lucide="plus" class="h-4 w-4"></i>
          <span>New Drug</span>
        </a>
      @endif
    </div>
  </div>

  {{-- New Drug (mobile) --}}
  <div class="flex sm:hidden">
    @if($canManage ?? false)
      <a href="{{ route('faculty.drug_guide.create') }}
         class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-3.5 py-2.5 text-[13px] font-medium text-white shadow-sm hover:bg-emerald-700 w-full justify-center">
        <i data-lucide="plus" class="h-4 w-4"></i>
        <span>New Drug</span>
      </a>
    @endif
  </div>
</div>


      {{-- Results grid (cards + skeleton same layout as Procedures cards) --}}
      <div id="grid" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3"></div>

      {{-- Empty state (same feel as Procedures empty) --}}
      <div id="emptyState" class="hidden rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
        <p class="text-sm text-slate-500">
          No results. Try a different search or remove filters.
        </p>
      </div>

      {{-- Pager (styled similar to Procedures pager) --}}
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

@includeIf('partials.faculty-footer')
@includeWhen(!View::exists('partials.faculty-footer'), 'partials.student-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();

  // --- State ---
  let state = {
    q: '',
    form_id: '',
    cat_id: '',
    drug_class: '',
    packaging_type: '',
    page: 1,
    per_page: 12,
    total: 0
  };

  const grid        = document.getElementById('grid');
  const emptyEl     = document.getElementById('emptyState');
  const pagerEl     = document.getElementById('pager');
  const summary     = document.getElementById('summary');
  const prevBtn     = document.getElementById('prev');
  const nextBtn     = document.getElementById('next');
  const pagesEl     = document.getElementById('pages');

  const qInput      = document.getElementById('q');
  const formSel     = document.getElementById('form_id');
  const catSel      = document.getElementById('cat_id');
  const classSel    = document.getElementById('drug_class');
  const packagingSel= document.getElementById('packaging_type');
  const btnClear    = document.getElementById('btnClear');

  const DATA_URL = @json(route('faculty.drug_guide.data'));

  // --- Fetch helper ---
  async function fetchData() {
    const params = new URLSearchParams({
      q: state.q,
      form_id: state.form_id,
      cat_id: state.cat_id,
      drug_class: state.drug_class,
      packaging_type: state.packaging_type,
      page: state.page,
      per_page: state.per_page
    });

    const res = await fetch(`${DATA_URL}?${params.toString()}`, {
      headers: { 'Accept': 'application/json' }
    });
    if (!res.ok) throw new Error('Failed to fetch');
    return await res.json();
  }

  // --- Card template (now shows class + packaging chips) ---
  function cardTemplate(item) {
    const t = ((item.generic || '') + ' ' + (item.brand || '')).toLowerCase();
    const icon = t.includes('syrup') ? 'flask-round'
               : (t.includes('tab') || t.includes('tablet')) ? 'pill'
               : (t.includes('cap') || t.includes('capsule')) ? 'capsule'
               : 'pill';

    const drugClass = item.drug_class || item.class || '';
    const packagingType = item.packaging_type || '';

    return `
<article class="js-card flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow opacity-0">
  <header class="flex items-start justify-between gap-4">
    <div>
      <h2 class="text-[16px] font-semibold text-slate-900 leading-snug flex items-center gap-3">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50">
          <i data-lucide="${icon}" class="h-5 w-5 text-slate-700"></i>
        </span>
        ${escapeHtml(item.brand || '—')}
      </h2>

      <p class="mt-1.5 text-[13px] text-slate-600 line-clamp-3">
        <span class="font-semibold">Generic:</span>
        ${escapeHtml(item.generic || 'Unknown')}
      </p>

      <div class="mt-2.5 flex flex-wrap items-center gap-2.5 text-[12px]">

        <span class="inline-flex items-center rounded-full bg-slate-50 text-slate-700 px-2.5 py-0.5">
          <i data-lucide="flask-conical" class="h-4 w-4 mr-1"></i>
          ${escapeHtml(item.form || '—')}
        </span>

        <span class="inline-flex items-center rounded-full bg-sky-50 text-sky-700 px-2.5 py-0.5">
          <i data-lucide="layers" class="h-4 w-4 mr-1"></i>
          ${escapeHtml(item.category || '—')}
        </span>

        ${drugClass ? `
        <span class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 px-2.5 py-0.5">
          <i data-lucide="badge-check" class="h-4 w-4 mr-1"></i>
          ${escapeHtml(drugClass)}
        </span>` : ''}

        ${packagingType ? `
        <span class="inline-flex items-center rounded-full bg-violet-50 text-violet-700 px-2.5 py-0.5">
          <i data-lucide="package" class="h-4 w-4 mr-1"></i>
          ${escapeHtml(packagingType)}
        </span>` : ''}
      </div>
    </div>

    <div class="text-right text-[12px] text-slate-500 space-y-1.5">
      ${ item.issued_at ? `
        <div>Issued:
          <span class="font-medium">${escapeHtml(item.issued_at)}</span>
        </div>` : '' }

      ${ item.expires_at ? `
        <div>Expires:
          <span class="font-medium">${escapeHtml(item.expires_at)}</span>
        </div>` : '' }
    </div>
  </header>

  ${ item.strength
    ? `<p class="mt-4 text-[13px] text-slate-700">${escapeHtml(item.strength)}</p>`
    : '' }

  <div class="mt-4 flex flex-wrap items-center gap-4 text-[12px] text-slate-500">
    <span class="inline-flex items-center gap-1.5">
      <i data-lucide="hash" class="h-4 w-4"></i>
      ${escapeHtml(item.reg_no || 'No registration number')}
    </span>

    ${ item.mfg ? `
      <span class="inline-flex items-center gap-1.5">
        <i data-lucide="factory" class="h-4 w-4"></i>
        ${escapeHtml(item.mfg)}
      </span>` : '' }
  </div>

  <div class="mt-auto pt-5 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
    <a href="${item.show_url}"
       class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 py-2 text-[13px] font-medium text-slate-800 hover:bg-slate-50">
      <i data-lucide="eye" class="h-4 w-4"></i>
      View Details
    </a>
  </div>
</article>

    `;
  }

  // --- Skeleton cards (matching Procedures-style skeleton look) ---
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

  // --- Animate cards after skeleton (like Procedures) ---
  function animateCards() {
    const cards = [...document.querySelectorAll('.js-card')];
    cards.forEach((el, idx) => {
      el.style.animationDelay = `${Math.min(idx, 6) * 40}ms`;
      el.classList.remove('opacity-0');
      el.classList.add('animate-card-in');
    });
  }

  // --- Renderers ---
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
    // Re-init icons for new markup
    lucide.createIcons();
    // Play entrance animation
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
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
  };

  // --- Events ---
  qInput.addEventListener('input', debounce(() => {
    state.q = qInput.value;
    state.page = 1;
    load();
  }, 250));

  formSel.addEventListener('change', () => {
    state.form_id = formSel.value;
    state.page = 1;
    load();
  });

  catSel.addEventListener('change', () => {
    state.cat_id = catSel.value;
    state.page = 1;
    load();
  });

  classSel.addEventListener('change', () => {
    state.drug_class = classSel.value;
    state.page = 1;
    load();
  });

  packagingSel.addEventListener('change', () => {
    state.packaging_type = packagingSel.value;
    state.page = 1;
    load();
  });

  btnClear.addEventListener('click', () => {
    qInput.value = '';
    formSel.value = '';
    catSel.value = '';
    classSel.value = '';
    packagingSel.value = '';

    state = {
      q: '',
      form_id: '',
      cat_id: '',
      drug_class: '',
      packaging_type: '',
      page: 1,
      per_page: 12,
      total: 0
    };
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
    grid.innerHTML = skeletons(state.per_page); // show skeletons (like Procedures)

    try {
      const data = await fetchData();
      state.total = data.total;
      renderList(data.items);
      renderPager(data.page, data.per_page, data.total);

      if (!preserveScroll) {
        window.scrollTo({ top: topY - 220, behavior: 'smooth' });
      }
    } catch (e) {
      grid.innerHTML = `
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-rose-600">
          Failed to load drug guide data.
        </div>`;
    }
  }

  // HTML-escape helper
  function escapeHtml(s) {
    return (s ?? '').replace(/[&<>"']/g, m => ({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
    }[m]));
  }

  // First load
  load();
</script>
</body>
</html>
