{{-- resources/views/student/student-practice-guide.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <title>{{ $procedure->title }} — Practice · NurSync – Nurse Assistance</title>

  <meta name="csrf-token" content="{{ csrf_token() }}">

  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; }
  </style>
</head>
<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Sidebar --}}
  @php($active = 'procedures')
  @include('partials.sidebar')

  {{-- Main content --}}
  <section class="flex-1">
    <div class="container mx-auto px-8 py-12 space-y-8">

      {{-- Breadcrumb --}}
      <nav class="text-xs text-slate-500">
        <a href="{{ route('student.procedures.index') }}" class="hover:text-slate-700">Procedures</a>
        <span class="mx-2">/</span>
        <a href="{{ route('student.procedures.open-guide', $procedure->slug) }}" class="hover:text-slate-700">{{ $procedure->title }}</a>
        <span class="mx-2">/</span>
        <span class="text-slate-700">Practice</span>
      </nav>

      <!-- Header -->
      <header>
        <div class="flex items-center gap-3">
          <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
            <i data-lucide="play-circle" class="h-5 w-5"></i>
          </span>
          <h1 class="text-[28px] font-extrabold leading-tight tracking-tight text-slate-900">
            {{ $procedure->title }} — Practice Simulation
          </h1>
        </div>
        <p class="mt-2 text-sm text-slate-500">
          {{ $procedure->description ?? 'Simulate the steps without physical equipment. Train your sequence, timing, and decisions.' }}
        </p>
      </header>

      <!-- Toolbar -->
      <div class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200/70 bg-white p-5">
          <div class="text-[13px] font-medium text-slate-700 flex items-center gap-2">
            <i data-lucide="sliders-horizontal" class="h-4 w-4 text-slate-500"></i> Practice Mode
          </div>
          <div class="mt-3 flex flex-wrap gap-2">
            <button class="px-3 py-1.5 rounded-lg border text-sm hover:bg-slate-50 mode-btn" data-mode="quick" aria-pressed="true">Quick Run</button>
            <button class="px-3 py-1.5 rounded-lg border text-sm hover:bg-slate-50 mode-btn" data-mode="full"  aria-pressed="false">Full Skill</button>
            <button class="px-3 py-1.5 rounded-lg border text-sm hover:bg-slate-50 mode-btn" data-mode="exam"  aria-pressed="false">Exam Simulation</button>
          </div>
          <p class="mt-2 text-xs text-slate-500">Tip: “Exam Simulation” adds stricter timing and fewer hints.</p>
        </div>

        <div class="rounded-2xl border border-slate-200/70 bg-white p-5" id="scenarioCard" hidden>
          <div class="text-[13px] font-medium text-slate-700 flex items-center gap-2">
            <i data-lucide="sparkles" class="h-4 w-4 text-slate-500"></i> Scenario
          </div>
          <p class="mt-2 text-sm text-slate-600" id="scenarioPrompt"></p>
          <div class="mt-3 flex flex-wrap gap-2" id="scenarioChoices"></div>
          <p class="mt-3 text-xs text-slate-500 hidden" id="scenarioRationale"></p>
        </div>

        <div class="rounded-2xl border border-slate-200/70 bg-white p-5">
          <div class="text-[13px] font-medium text-slate-700 flex items-center gap-2">
            <i data-lucide="clock" class="h-4 w-4 text-slate-500"></i> Timers
          </div>
          <div class="mt-3 grid grid-cols-2 gap-3">
            <div class="rounded-xl bg-slate-50 border p-3 text-center">
              <div class="text-xs text-slate-500">ABHR (20–30s)</div>
              <div class="mt-1 text-2xl font-bold" id="abhrTimer">00:30</div>
              <div class="mt-2 flex justify-center gap-2">
                <button class="px-3 py-1.5 rounded-lg border text-xs hover:bg-slate-100" data-timer="abhr-start">Start</button>
                <button class="px-3 py-1.5 rounded-lg border text-xs hover:bg-slate-100" data-timer="abhr-reset">Reset</button>
              </div>
            </div>
            <div class="rounded-xl bg-slate-50 border p-3 text-center">
              <div class="text-xs text-slate-500">Handwash (40–60s)</div>
              <div class="mt-1 text-2xl font-bold" id="washTimer">01:00</div>
              <div class="mt-2 flex justify-center gap-2">
                <button class="px-3 py-1.5 rounded-lg border text-xs hover:bg-slate-100" data-timer="wash-start">Start</button>
                <button class="px-3 py-1.5 rounded-lg border text-xs hover:bg-slate-100" data-timer="wash-reset">Reset</button>
              </div>
            </div>
          </div>
          <p class="mt-2 text-xs text-slate-500">These are guidance timers only.</p>
        </div>
      </div>

      <!-- Main two-column layout -->
      <div class="grid gap-6 lg:grid-cols-3">
        <!-- Left: Interactive checklist -->
        <div class="lg:col-span-2 rounded-2xl border border-slate-200/70 bg-white p-6">
          <div class="flex items-center justify-between">
            <div class="text-[13px] font-medium text-slate-700 flex items-center gap-2">
              <i data-lucide="check-square" class="h-4 w-4 text-slate-500"></i> Interactive Checklist
            </div>
            <div class="flex items-center gap-2">
              <label class="flex items-center gap-2 text-xs text-slate-600">
                <input type="checkbox" id="toggleHints" class="rounded"> Show hints
              </label>
              <button class="px-3 py-1.5 rounded-lg border text-sm hover:bg-slate-50" id="expandAll">Expand all</button>
              <button class="px-3 py-1.5 rounded-lg border text-sm hover:bg-slate-50" id="collapseAll">Collapse all</button>
            </div>
          </div>

          <div class="mt-4 divide-y divide-slate-200/70" id="checklist">
            {{-- Steps injected by JS --}}
          </div>

          <!-- Bottom controls -->
          <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
            <div class="text-xs text-slate-500">
              Simulation only — no real patient data. View-only training.
            </div>
            <div class="flex items-center gap-2">
              <a href="{{ route('student.procedures.open-guide', $procedure->slug) }}"
                 class="px-3 py-2 rounded-lg border text-sm hover:bg-slate-50">
                <i data-lucide="book-open" class="h-4 w-4 mr-1.5 inline"></i> Open Guide
              </a>
              <button class="px-3 py-2 rounded-lg border text-sm hover:bg-slate-50" id="resetPractice">
                <i data-lucide="rotate-ccw" class="h-4 w-4 mr-1.5 inline"></i> Reset
              </button>
              <button class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:opacity-95" id="finishPractice">
                <i data-lucide="flag-checkered" class="h-4 w-4 mr-1.5 inline"></i> Finish Practice
              </button>
            </div>
          </div>
        </div>

        <!-- Right: Summary & Rubric -->
        <aside class="rounded-2xl border border-slate-200/70 bg-white p-6">
          <div class="text-[13px] font-medium text-slate-700 flex items-center gap-2">
            <i data-lucide="clipboard-list" class="h-4 w-4 text-slate-500"></i> Practice Summary
          </div>

          <div class="mt-4 grid grid-cols-2 gap-3 text-center">
            <div class="rounded-xl bg-slate-50 border p-3">
              <div class="text-xs text-slate-500">Steps Completed</div>
              <div class="mt-1 text-2xl font-bold" id="stepsDone">0</div>
            </div>
            <div class="rounded-xl bg-slate-50 border p-3">
              <div class="text-xs text-slate-500">Hints Used</div>
              <div class="mt-1 text-2xl font-bold" id="hintsUsed">0</div>
            </div>
            <div class="rounded-xl bg-slate-50 border p-3">
              <div class="text-xs text-slate-500">Decision Errors</div>
              <div class="mt-1 text-2xl font-bold" id="errors">0</div>
            </div>
            <div class="rounded-xl bg-slate-50 border p-3">
              <div class="text-xs text-slate-500">Practice Time</div>
              <div class="mt-1 text-2xl font-bold" id="elapsed">00:00</div>
            </div>
          </div>

          <div class="mt-6">
            <h3 class="text-sm font-semibold text-slate-800">Self-Score (Mapped to CI Rubric)</h3>
            <ul class="mt-3 space-y-2 text-sm text-slate-700">
              <li class="flex items-center justify-between">
                <span>Safety in patient care</span>
                <span class="rounded bg-slate-100 px-2 py-0.5 text-xs">—</span>
              </li>
              <li class="flex items-center justify-between">
                <span>Sterile/clean technique</span>
                <span class="rounded bg-slate-100 px-2 py-0.5 text-xs">—</span>
              </li>
              <li class="flex items-center justify-between">
                <span>Organization & timing</span>
                <span class="rounded bg-slate-100 px-2 py-0.5 text-xs">—</span>
              </li>
            </ul>
          </div>

          <div class="mt-6 space-y-2">
            <button class="w-full px-3 py-2 rounded-lg border text-sm hover:bg-slate-50">
              <i data-lucide="file-down" class="h-4 w-4 mr-1.5 inline"></i> Download practice log (PDF)
            </button>
            <button class="w-full px-3 py-2 rounded-lg border text-sm hover:bg-slate-50" id="retryScenario">
              <i data-lucide="refresh-ccw" class="h-4 w-4 mr-1.5 inline"></i> Retry with new scenario
            </button>
          </div>
        </aside>
      </div>

      <!-- Campus note -->
      <div class="rounded-2xl border border-slate-200/70 bg-white p-5">
        <div class="flex items-start gap-3">
          <i data-lucide="shield-alert" class="h-5 w-5 text-slate-500 mt-0.5"></i>
          <p class="text-[13px] leading-6 text-slate-600">
            <span class="font-semibold text-slate-800">Note:</span> Campus training & simulation only. No real patient data is stored.
          </p>
        </div>
      </div>

    </div>
  </section>
</main>

@include('partials.student-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>

{{-- Make simConfig available BEFORE logic runs --}}
<script> window.simConfig = @json($sim); </script>

<script>
(function(){
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const sim  = window.simConfig || {};
  const baseSteps = Array.isArray(sim.steps) ? sim.steps : [];
  const scenario = sim.scenario || null;

  // --- Mode rules ---
  const modeRules = {
    quick: { hintsDefault: true,  hintsLocked: false, timers: {abhr:20, wash:40}, enforceOrder:false, strict:false },
    full:  { hintsDefault: false, hintsLocked: false, timers: {abhr:30, wash:60}, enforceOrder:true,  strict:false },
    exam:  { hintsDefault: false, hintsLocked: true,  timers: {abhr:30, wash:60}, enforceOrder:true,  strict:true  },
  };

  // State
  let mode = 'quick';
  let steps = [...baseSteps]; // rendered list
  let elapsed = 0;
  let selectedChoice = null;

  // --- UI helpers ---
  const $ = s => document.querySelector(s);
  const $$ = s => document.querySelectorAll(s);
  const inc = (id) => { const n = $('#'+id); n.textContent = String((+n.textContent||0)+1); };
  const fmt = (seconds) => `${String(Math.floor(seconds/60)).padStart(2,'0')}:${String(seconds%60).padStart(2,'0')}`;

  // --- Timers ---
  function bindTimer(prefix, seconds){
    const el = $('#'+prefix+'Timer');
    if (!el) return;
    clearInterval(el._t);

    // Remove previous listeners by cloning buttons
    const startSel = `[data-timer="${prefix}-start"]`;
    const resetSel = `[data-timer="${prefix}-reset"]`;
    const startOld = document.querySelector(startSel);
    const resetOld = document.querySelector(resetSel);
    if (startOld) { const c = startOld.cloneNode(true); startOld.replaceWith(c); }
    if (resetOld) { const c = resetOld.cloneNode(true); resetOld.replaceWith(c); }

    const startBtn = document.querySelector(startSel);
    const resetBtn = document.querySelector(resetSel);

    const set = s => { el._remain = s; el.textContent = fmt(s); };
    set(seconds);

    startBtn?.addEventListener('click', () => {
      clearInterval(el._t);
      el._t = setInterval(() => {
        el._remain--;
        if (el._remain <= 0) { clearInterval(el._t); el._remain = 0; }
        el.textContent = fmt(el._remain);
      }, 1000);
    });

    resetBtn?.addEventListener('click', () => { clearInterval(el._t); set(seconds); });
  }

  function applyTimers() {
    const t = modeRules[mode].timers;
    bindTimer('abhr', Number(t.abhr));
    bindTimer('wash', Number(t.wash));
  }

  // --- Checklist rendering ---
  const checklist = $('#checklist');
  const toggleHints = $('#toggleHints');
  const stepCounter = $('#stepsDone');

  function renderChecklist(){
    checklist.innerHTML = '';
    if (!steps.length){
      checklist.innerHTML = '<p class="py-6 text-sm text-slate-500">No steps configured for this procedure.</p>';
      return;
    }

    steps.forEach((s, idx) => {
      const block = document.createElement('details');
      block.className = 'group py-4';
      if (idx === 0) block.setAttribute('open','');

      const duration = s.duration ? `<span class="ml-2 inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-700"><i data-lucide="timer" class="h-3.5 w-3.5"></i> ~${s.duration}s</span>` : '';
      const hint = s.hint ? `<p class="mt-2 text-xs text-slate-500 hint ${toggleHints?.checked ? '' : 'hidden'}">Hint: ${s.hint}</p>` : '';

      block.innerHTML = `
        <summary class="flex cursor-pointer list-none items-center justify-between">
          <span class="text-sm font-semibold text-slate-800">Step ${idx+1}: ${s.title || '—'} ${duration}</span>
          <i data-lucide="chevron-down" class="h-4 w-4 transition group-open:rotate-180"></i>
        </summary>
        <div class="mt-3 space-y-3">
          <ul class="space-y-2 text-sm text-slate-700">
            <li class="flex items-start gap-2">
              <input type="checkbox" class="mt-1 stepbox" data-index="${idx}">
              <span>${(s.text || '').replace(/\n/g,'<br>')}</span>
            </li>
          </ul>
          ${hint}
        </div>
      `;
      checklist.appendChild(block);
    });

    if (window.lucide?.createIcons) window.lucide.createIcons();

    const boxes = checklist.querySelectorAll('.stepbox');
    boxes.forEach(cb => cb.addEventListener('change', () => handleStepCheck(cb, boxes)));
  }

  function handleStepCheck(cb, boxes){
    const idx = Number(cb.dataset.index);
    const rules = modeRules[mode];

    if (rules.enforceOrder) {
      const prevUnchecked = Array.from(boxes).slice(0, idx).filter(x => !x.checked);

      if (prevUnchecked.length) {
        inc('errors');

        if (rules.strict) {
          // Strict: don't allow out-of-order
          cb.checked = false;
          // flash ring
          const det = cb.closest('details');
          det?.classList.add('ring-2','ring-rose-300','rounded-xl');
          setTimeout(() => det?.classList.remove('ring-2','ring-rose-300','rounded-xl'), 900);
        } else {
          // Gentle: auto-complete missing previous steps
          prevUnchecked.forEach(x => x.checked = true);
        }
      }
    }

    const done = Array.from(boxes).filter(x => x.checked).length;
    stepCounter.textContent = String(done);
  }

  // --- Scenario ---
  const scCard = $('#scenarioCard');
  function renderScenario(){
    if (!(scenario && scenario.prompt && Array.isArray(scenario.choices))) return;
    scCard.hidden = false;
    $('#scenarioPrompt').innerHTML = `<span class="font-medium text-slate-800">Prompt:</span> ${scenario.prompt}`;
    const wrap = $('#scenarioChoices');
    wrap.innerHTML = '';
    scenario.choices.forEach(ch => {
      const b = document.createElement('button');
      b.type = 'button';
      b.className = 'px-3 py-1.5 rounded-lg border text-sm hover:bg-slate-50';
      b.textContent = ch.label ?? ch.value;
      b.dataset.value = ch.value;

      b.addEventListener('click', () => {
        // In exam mode: lock after first choice
        if (modeRules[mode].strict && selectedChoice !== null) return;

        selectedChoice = ch.value;
        const correct = String(ch.value) === String(scenario.answer);
        if (!correct) inc('errors');

        const rat = $('#scenarioRationale');
        rat.textContent = scenario.rationale || (correct ? 'Correct.' : 'Incorrect.');
        rat.classList.remove('hidden');

        wrap.querySelectorAll('button').forEach(x => x.classList.remove('bg-slate-900','text-white'));
        b.classList.add('bg-slate-900','text-white');

        if (modeRules[mode].strict) {
          // disable all buttons after first selection
          wrap.querySelectorAll('button').forEach(x => x.disabled = true);
        }
      });

      wrap.appendChild(b);
    });
  }

  // --- Mode switching ---
  const modeBtns = document.querySelectorAll('.mode-btn');
  function paintModes(m){
    modeBtns.forEach(b => {
      const active = (b.dataset.mode === m);
      b.classList.toggle('bg-slate-900', active);
      b.classList.toggle('text-white', active);
      b.setAttribute('aria-pressed', active ? 'true' : 'false');
    });
  }

  function applyHintsRule(){
    const rules = modeRules[mode];
    const label = toggleHints?.closest('label');
    if (toggleHints) {
      toggleHints.checked = !!rules.hintsDefault;
      toggleHints.disabled = !!rules.hintsLocked;
      if (label) label.classList.toggle('opacity-50', !!rules.hintsLocked);
      // apply to rendered hints
      document.querySelectorAll('.hint').forEach(h => h.classList.toggle('hidden', !toggleHints.checked));
    }
  }

  function setMode(m){
    mode = m;
    paintModes(m);
    applyHintsRule();
    applyTimers();
    // (optional) collapse-all for exam
    if (mode === 'exam') {
      document.querySelectorAll('details').forEach((d,i) => d.open = (i===0));
    }
  }

  // --- Expand/Collapse controls ---
  $('#expandAll')?.addEventListener('click', () => document.querySelectorAll('details').forEach(d => d.open = true));
  $('#collapseAll')?.addEventListener('click', () => document.querySelectorAll('details').forEach(d => d.open = false));

  // --- Hint toggle counter ---
  toggleHints?.addEventListener('change', () => {
    document.querySelectorAll('.hint').forEach(h => h.classList.toggle('hidden', !toggleHints.checked));
    if (toggleHints.checked) inc('hintsUsed');
  });

  // --- Stopwatch ---
  setInterval(() => {
    elapsed++;
    $('#elapsed').textContent = fmt(elapsed);
  }, 1000);

  // --- Reset ---
  $('#resetPractice')?.addEventListener('click', () => {
    checklist.querySelectorAll('.stepbox').forEach(cb => cb.checked = false);
    $('#stepsDone').textContent = '0';
    $('#hintsUsed').textContent = '0';
    $('#errors').textContent = '0';
    elapsed = 0;
    selectedChoice = null;
    // re-apply current mode timers
    applyTimers();
    // re-enable scenario choices if exam
    if (scenario && $('#scenarioChoices')) {
      $('#scenarioChoices').querySelectorAll('button').forEach(x => { x.disabled = false; x.classList.remove('bg-slate-900','text-white'); });
      $('#scenarioRationale').classList.add('hidden');
    }
    alert('Practice reset.');
  });

  // --- Finish → POST (route must exist or this will show error message gracefully) ---
  const finishBtn = $('#finishPractice');
  finishBtn?.addEventListener('click', async () => {
    try {
      finishBtn.disabled = true;
      finishBtn.textContent = 'Saving...';

      const payload = {
        mode,
        steps_completed: Number($('#stepsDone').textContent || 0),
        hints_used:      Number($('#hintsUsed').textContent || 0),
        decision_errors: Number($('#errors').textContent || 0),
        elapsed_seconds: elapsed,
        scenario_id:     scenario?.id ?? null,
        meta:            { selected_choice: selectedChoice }
      };

      const res = await fetch('{{ route('student.procedures.practice.log', $procedure->slug) }}', {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload),
        credentials: 'same-origin'
      });

      if (!res.ok) {
        const txt = await res.text();
        throw new Error(`Save failed (${res.status}). ${txt || ''}`);
      }

      await res.json().catch(()=>({ok:true}));
      alert('Practice saved. Great job!');
    } catch (err) {
      console.error(err);
      alert('Could not save your practice. Please ensure you are logged in and try again.');
    } finally {
      finishBtn.disabled = false;
      finishBtn.textContent = 'Finish Practice';
    }
  });

  // --- Boot ---
  renderScenario();
  // default: Quick Run
  renderChecklist();
  setMode('quick');
  // ensure icons in dynamically added nodes
  if (window.lucide?.createIcons) window.lucide.createIcons();
})();
</script>
</body>
</html>
