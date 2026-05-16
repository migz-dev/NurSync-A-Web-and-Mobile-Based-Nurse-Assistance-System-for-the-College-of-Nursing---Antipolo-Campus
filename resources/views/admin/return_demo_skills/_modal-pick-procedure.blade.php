{{-- resources/views/admin/return_demo_skills/_modal-pick-procedure.blade.php --}}
<div id="modalPickProcedure" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-modal-overlay></div>

  <div class="relative mx-auto my-10 w-[95%] max-w-4xl rounded-2xl bg-white shadow-2xl border border-slate-200">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-2xl">
      <h3 class="text-lg font-bold text-slate-900">Add from Procedures</h3>
      <button type="button" class="h-9 w-9 inline-flex items-center justify-center rounded-lg hover:bg-slate-100" data-modal-close>
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    <div class="p-6 space-y-4">
      @php
        $wards = [
          'Community Health (CHN)','Delivery Room (DR)','Disaster Response / Community Field','Emergency Room (ER)',
          'Endocrine Unit','ICU','Isolation Unit','Medical-Surgical (MS)','Neurology Unit','Nursery','OB Ward',
          'Oncology','Operating Room (OR)','Pediatrics (PEDIA)','Psychiatric (PSYCH)','Trauma Unit',
        ];
        sort($wards, SORT_NATURAL | SORT_FLAG_CASE);
      @endphp

      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 flex-col sm:flex-row gap-3 sm:items-center">
          <div class="relative flex-1 sm:w-72">
            <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
            <input id="pp-q" type="text" placeholder="Search procedures…"
                   class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300">
          </div>

          <select id="pp-status" class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
            <option value="">All statuses</option>
            <option value="draft">Draft</option>
            <option value="published">Published</option>
          </select>

          <select id="pp-ward" class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
            <option value="">All wards/areas</option>
            @foreach($wards as $w)
              <option value="{{ $w }}">{{ $w }}</option>
            @endforeach
          </select>
        </div>

        <button id="pp-refresh"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-[13px] font-medium hover:bg-slate-50">
          <i data-lucide="rotate-cw" class="h-4 w-4"></i> Refresh
        </button>
      </div>

      <div class="rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
              <tr class="text-left text-slate-600">
                <th class="px-4 py-3 w-10">
                  <input id="pp-check-all" type="checkbox" class="h-4 w-4 rounded border-slate-300">
                </th>
                <th class="px-4 py-3">Procedure Name</th>
                <th class="px-4 py-3">Wards</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Created</th>
              </tr>
            </thead>
            <tbody id="pp-table" class="divide-y divide-slate-200">
              <tr>
                <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                  Use the search/filters to load procedures…
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div id="pp-pager" class="flex items-center justify-end px-4 py-3 border-t border-slate-200 bg-slate-50"></div>
      </div>
    </div>

    <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-slate-200 rounded-b-2xl">
      <button type="button" class="rounded-lg px-5 py-2 text-sm border border-slate-300 text-slate-700 hover:bg-slate-100" data-modal-close>
        Cancel
      </button>
      <button id="pp-add-selected"
              class="rounded-lg px-5 py-2 text-sm bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50"
              disabled>
        Add Selected
      </button>
    </div>
  </div>
</div>

<script>
(() => {
  const qs  = s => document.querySelector(s);
  const qsa = s => Array.from(document.querySelectorAll(s));
  const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const debounce = (fn, ms=300) => { let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), ms); } };

  const MOD = '#modalPickProcedure';
  const $modal = qs(MOD);

  // ---- local paging state (fallback pager)
  const STATE = { items: [], page: 1, pageSize: 9 };

  // Endpoints
  const JSON_ENDPOINT     = "{{ route('admin.return_demo.skills.procedures') }}";
  const FALLBACK_ENDPOINT = "{{ route('admin.procedures.index') }}";
  const IMPORT_URL        = "{{ route('admin.return_demo.skills.import_from_procedures') }}";

  // ---- Fetch helpers
  async function loadJsonList(params={}) {
    const url = new URL(JSON_ENDPOINT, window.location.origin);
    const q      = params.q      ?? (qs('#pp-q')?.value || '');
    const status = params.status ?? (qs('#pp-status')?.value || '');
    const ward   = params.ward   ?? (qs('#pp-ward')?.value || '');
    const page   = params.page   ?? '';

    if (q)      url.searchParams.set('q', q);
    if (status) url.searchParams.set('status', status);
    if (ward)   url.searchParams.set('ward', ward);
    url.searchParams.set('archived', '0');
    if (page)   url.searchParams.set('page', page);

    const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    if (!res.ok) throw new Error('json fetch failed');
    return res.json(); // {data:[...]} or {items:[...]} (no pager)
  }

  async function loadHtmlList(params={}) {
    const url = new URL(FALLBACK_ENDPOINT, window.location.origin);
    const q      = params.q      ?? (qs('#pp-q')?.value || '');
    const status = params.status ?? (qs('#pp-status')?.value || '');
    const ward   = params.ward   ?? (qs('#pp-ward')?.value || '');
    const page   = params.page   ?? '';

    if (q)      url.searchParams.set('q', q);
    if (status) url.searchParams.set('status', status);
    if (ward)   url.searchParams.set('ward', ward);
    url.searchParams.set('archived', '0');
    if (page)   url.searchParams.set('page', page);

    const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    if (!res.ok) throw new Error('html fetch failed');
    const data = await res.json(); // {rows, pager}

    const tmp = document.createElement('tbody');
    tmp.innerHTML = data.rows || '';

    const parsed = [];
    tmp.querySelectorAll('tr').forEach(tr => {
      const tds = tr.querySelectorAll('td');
      if (!tds.length) return;
      const id = tr.querySelector('[data-procedure-id]')?.getAttribute('data-procedure-id')
              || tr.querySelector('a[href*="/procedures/"]')?.getAttribute('href')?.match(/\/procedures\/([^\/?#]+)/)?.[1]
              || '';
      if (!id) return;
      parsed.push({
        id,
        title: (tds[0]?.textContent || '').trim(),
        clinical_wards: (tds[1]?.textContent || '').trim(),
        status: (tds[2]?.textContent || '').trim(),
        created_at: (tds[3]?.textContent || '').trim(),
      });
    });

    return { data: parsed, pager: data.pager || '' };
  }

  // ---- Renderers
  function renderRows(slice) {
    const table = qs('#pp-table');
    if (!table) return;

    if (!slice.length) {
      table.innerHTML = `<tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">No procedures found.</td></tr>`;
      return;
    }

    table.innerHTML = slice.map(p => {
      const created = p.created ?? p.created_at ?? '—';
      const wards   = p.clinical_wards ?? '—';
      const status  = p.status ?? '—';
      const title   = p.title ?? '—';
      return `
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3"><input type="checkbox" class="pp-check h-4 w-4 rounded border-slate-300" value="${p.id}"></td>
          <td class="px-4 py-3 font-medium text-slate-900">${title}</td>
          <td class="px-4 py-3 text-slate-700">${wards}</td>
          <td class="px-4 py-3">${status}</td>
          <td class="px-4 py-3 text-slate-700">${created}</td>
        </tr>`;
    }).join('');

    bindChecks();
    if (window.lucide?.createIcons) lucide.createIcons();
  }

  function renderClientPager() {
    const pager = qs('#pp-pager');
    if (!pager) return;

    const total = STATE.items.length;
    const pages = Math.max(1, Math.ceil(total / STATE.pageSize));
    const cur   = Math.min(Math.max(1, STATE.page), pages);

    // slice current page then render rows
    const start = (cur - 1) * STATE.pageSize;
    const end   = start + STATE.pageSize;
    renderRows(STATE.items.slice(start, end));

    // build pager UI (simple Prev / numbers / Next)
    const btn = (label, page, disabled=false, isActive=false) => {
      const clsBase = 'h-9 min-w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50 px-3';
      const dis = disabled ? ' opacity-50 cursor-not-allowed' : '';
      const act = isActive ? ' bg-slate-900 text-white border-slate-900 hover:bg-slate-900' : '';
      return `<button class="pp-page ${clsBase}${dis}${act}" data-page="${page}" ${disabled?'disabled':''}>${label}</button>`;
    };

    let html = '';
    html += btn('Prev',  cur - 1, cur <= 1);
    // window of 3 pages around current
    const win = 3, half = Math.floor(win/2);
    let from = Math.max(1, cur - half);
    let to   = Math.min(pages, from + win - 1);
    from = Math.max(1, to - win + 1);
    for (let i = from; i <= to; i++) html += btn(i, i, false, i === cur);
    html += btn('Next',  cur + 1, cur >= pages);

    pager.innerHTML = `<nav class="flex items-center gap-1">${html}</nav>`;

    pager.querySelectorAll('.pp-page').forEach(b => {
      b.addEventListener('click', (e) => {
        e.preventDefault();
        const p = parseInt(b.getAttribute('data-page'), 10);
        if (isNaN(p)) return;
        STATE.page = Math.min(Math.max(1, p), pages);
        renderClientPager(); // re-render self and rows
      }, { once: true });
    });
  }

  async function loadList(params={}) {
    const table = qs('#pp-table'), pager = qs('#pp-pager'), checkAll = qs('#pp-check-all');
    if (!table) return;
    if (checkAll) checkAll.checked = false;

    table.innerHTML = `<tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">Loading…</td></tr>`;
    pager.innerHTML = '';

    try {
      let payload;
      try { payload = await loadJsonList(params); }
      catch { payload = await loadHtmlList(params); }

      const items = Array.isArray(payload?.data) ? payload.data
                  : Array.isArray(payload?.items) ? payload.items
                  : [];

      // If server sent a pager (HTML), use it; else client-side paginate @ 9 per page
      if (payload?.pager) {
        // server-driven mode
        renderRows(items);
        pager.innerHTML = payload.pager || '';
        pager.querySelectorAll('.js-page')?.forEach(btn => {
          btn.addEventListener('click', (ev) => {
            ev.preventDefault();
            const p = btn.getAttribute('data-page');
            if (p) loadList({ page: p });
          }, { once: true });
        });
      } else {
        // client-driven mode
        STATE.items = items;
        STATE.page  = 1;               // reset page on new load
        STATE.pageSize = 8;            // 9 per requirement
        renderClientPager();
      }
    } catch (e) {
      table.innerHTML = `<tr><td colspan="5" class="px-4 py-8 text-center text-red-600">Failed to load.</td></tr>`;
      pager.innerHTML = '';
    }
  }

  // ---- Filters
  const reload = debounce(() => loadList({ page: 1 }), 250);
  document.addEventListener('input',  e => { if (e.target.matches('#pp-q')) reload(); });
  document.addEventListener('change', e => { if (e.target.matches('#pp-status, #pp-ward')) loadList({ page: 1 }); });
  qs('#pp-refresh')?.addEventListener('click', () => loadList());

  // ---- Select all / enable button
  function bindChecks() {
    const $all = qs('#pp-check-all');
    const $btn = qs('#pp-add-selected');
    const items = () => qsa('.pp-check');
    const sync = () => {
      const any = items().some(x => x.checked);
      if ($btn) $btn.disabled = !any;
      if ($all) $all.checked = items().length && items().every(x => x.checked);
    };
    if ($all) $all.onchange = () => { items().forEach(x => x.checked = $all.checked); sync(); };
    items().forEach(x => x.addEventListener('change', sync));
    sync();
  }

  // ---- Import
  qs('#pp-add-selected')?.addEventListener('click', async () => {
    const ids = qsa('.pp-check').filter(x => x.checked).map(x => x.value);
    if (!ids.length) return;
    try {
      const res = await fetch(IMPORT_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf() },
        body: JSON.stringify({ procedure_ids: ids })
      });
      if (!res.ok) throw new Error('import failed');
      $modal?.classList.add('hidden');
      if (typeof window.fetchList === 'function') await window.fetchList({ page: 1 });
      else window.location.reload();
    } catch {
      alert('Import failed. Please try again.');
    }
  });

  // ---- Open/close hooks
  document.addEventListener('click', (e) => {
    const opener = e.target.closest('[data-modal-open]');
    if (opener && opener.getAttribute('data-modal-open') === MOD) {
      e.preventDefault();
      $modal?.classList.remove('hidden');
      if (window.lucide?.createIcons) lucide.createIcons();
      loadList({ page: 1 }); // Always load on open
      return;
    }
    if (e.target.closest('[data-modal-close]') || e.target.matches('[data-modal-overlay]')) {
      e.preventDefault();
      $modal?.classList.add('hidden');
    }
  });

  // Public hook (optional)
  window._pp_reload = () => loadList({ page: 1 });
})();
</script>
