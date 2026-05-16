<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Assist Mode · NurSync – Nurse Assistance (CI)</title>

  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style> body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; } </style>
</head>
<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Sidebar --}}
  @include('partials.faculty-sidebar', ['active' => 'procedures'])

  <section class="flex-1">
    <div class="container mx-auto px-8 py-12 space-y-8">

      {{-- Header --}}
      <header class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
            <i data-lucide="play-circle" class="h-4 w-4"></i>
          </span>
          <div>
            <h1 class="text-[28px] font-extrabold tracking-tight text-slate-900">
              {{ $procedure->title ?? 'Procedure' }} – Assist Mode
            </h1>
            @if(!empty($procedure->description))
              <p class="mt-1 text-xs text-slate-500">{{ $procedure->description }}</p>
            @endif
          </div>
        </div>

        {{-- Overall timer --}}
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700">
          <span class="inline-flex items-center gap-2">
            <i data-lucide="clock" class="h-4 w-4"></i>
            <span id="overallTimer">00:00</span>
            <button id="btnTimerToggle" class="ml-3 rounded-lg border px-2 py-1 text-xs hover:bg-slate-50">Start</button>
            <button id="btnTimerReset" class="rounded-lg border px-2 py-1 text-xs hover:bg-slate-50">Reset</button>
          </span>
        </div>
      </header>

      {{-- Progress bar --}}
      @php $stepsCol = $procedure->steps ?? collect(); $stepsCol = $stepsCol->sortBy('step_no')->values(); @endphp
      <div class="rounded-2xl border border-slate-200 bg-white p-4">
        <div class="flex items-center justify-between text-xs text-slate-600">
          <div class="font-medium"><span id="progressText">Step 1</span> / {{ max(1, $stepsCol->count()) }}</div>
          <div class="flex items-center gap-2">
            <span class="hidden sm:inline">Shortcuts:</span>
            <code class="rounded bg-slate-100 px-1.5 py-0.5">←</code>
            <code class="rounded bg-slate-100 px-1.5 py-0.5">→</code>
            <code class="rounded bg-slate-100 px-1.5 py-0.5">Space</code>
          </div>
        </div>
        <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-slate-100">
          <div id="progressBar" class="h-2 w-0 rounded-full bg-slate-900 transition-[width]"></div>
        </div>
      </div>

      <div class="grid gap-6 lg:grid-cols-3">
        {{-- Left: Steps navigator --}}
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Step-by-Step</h2>
            <div class="flex items-center gap-2">
              <button id="btnPrev" class="rounded-lg border px-3 py-1.5 text-xs hover:bg-slate-50">Prev</button>
              <button id="btnNext" class="rounded-lg border px-3 py-1.5 text-xs hover:bg-slate-50">Next</button>
            </div>
          </div>

          <div id="stepContainer" class="mt-4">
            @foreach($stepsCol as $idx => $step)
              <article class="js-step {{ $idx === 0 ? '' : 'hidden' }} space-y-3" data-index="{{ $idx }}">
                <div class="flex items-center justify-between">
                  <div class="text-sm font-semibold text-slate-700">
                    Step {{ $step->step_no }}{{ $step->title ? ": {$step->title}" : '' }}
                  </div>
                  {{-- Per-step timer --}}
                  <div class="inline-flex items-center gap-2 rounded-lg border px-2 py-1 text-xs text-slate-600">
                    <i data-lucide="timer" class="h-3.5 w-3.5"></i>
                    <span class="js-step-timer">00:00</span>
                    <button class="js-step-timer-toggle rounded border px-1.5 py-0.5">Start</button>
                    <button class="js-step-timer-reset rounded border px-1.5 py-0.5">Reset</button>
                  </div>
                </div>

                <div class="text-sm text-slate-700 leading-6">{{ $step->body }}</div>

                @if(!empty($step->rationale) || !empty($step->caution))
                  <div class="grid gap-3 sm:grid-cols-2">
                    @if(!empty($step->rationale))
                      <div class="rounded-lg bg-slate-50 border border-slate-200 p-3 text-xs">
                        <span class="font-semibold text-slate-800">Rationale:</span> {{ $step->rationale }}
                      </div>
                    @endif
                    @if(!empty($step->caution))
                      <div class="rounded-lg bg-rose-50 border border-rose-200 p-3 text-xs text-rose-800">
                        <span class="font-semibold">Caution:</span> {{ $step->caution }}
                      </div>
                    @endif
                  </div>
                @endif
              </article>
            @endforeach

            @if($stepsCol->isEmpty())
              <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                No steps available. Use <span class="font-semibold">Edit</span> to add steps.
              </div>
            @endif
          </div>
        </div>

        {{-- Right: Checklist + PPE + Hazards + Notes --}}
        <aside class="rounded-2xl border border-slate-200 bg-white p-6 space-y-6">
          {{-- Step Checklist --}}
          <div>
            <h3 class="text-sm font-semibold text-slate-900">Step Checklist</h3>
            <div id="checklist" class="mt-2 space-y-2">
              @foreach($stepsCol as $i => $s)
                <label class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 px-3 py-2">
                  <span class="flex items-center gap-2 text-sm">
                    <input type="checkbox" class="js-step-done" data-index="{{ $i }}">
                    <span>Step {{ $s->step_no }}{{ $s->title ? ': ' . $s->title : '' }}</span>
                  </span>
                  <button type="button" class="js-jump text-xs text-slate-600 hover:underline" data-index="{{ $i }}">Jump</button>
                </label>
              @endforeach
              @if($stepsCol->isEmpty())
                <p class="text-xs text-slate-500">No steps to list.</p>
              @endif
            </div>
          </div>

          {{-- PPE checklist --}}
          <div>
            <h3 class="text-sm font-semibold text-slate-900">PPE & Equipment</h3>
            <div class="mt-2 space-y-2">
              @foreach(($procedure->ppe_json ?? []) as $i => $item)
                <label class="flex items-center gap-2 text-sm text-slate-700">
                  <input type="checkbox" class="js-ppe" data-key="ppe-{{ $i }}">
                  <span>{{ $item }}</span>
                </label>
              @endforeach
              @if(empty($procedure->ppe_json))
                <p class="text-xs text-slate-500">No PPE list defined.</p>
              @endif
            </div>
          </div>

          {{-- Hazards / Safety --}}
          @if(!blank($procedure->hazards_text))
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-3">
              <div class="text-xs font-semibold text-amber-800 mb-1 flex items-center gap-2">
                <i data-lucide="alert-triangle" class="h-4 w-4"></i> Hazards / Safety Notes
              </div>
              <div class="text-xs text-amber-900 leading-5">{{ trim((string)$procedure->hazards_text) }}</div>
            </div>
          @endif

          {{-- CI Notes (local only) --}}
          <div>
            <h3 class="text-sm font-semibold text-slate-900">CI Notes (private)</h3>
            <textarea id="ciNotes" rows="8"
              class="mt-2 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm outline-none focus:ring-2 focus:ring-slate-200"
              placeholder="Observations, reminders, points to emphasize..."></textarea>
            <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
              <span id="notesSaved" class="opacity-0 transition">Saved</span>
              <div class="space-x-2">
                <button id="btnResetSession" class="rounded-lg border px-3 py-1.5 hover:bg-slate-50">Reset Session</button>
                <button id="btnEndSession" class="rounded-lg border px-3 py-1.5 hover:bg-slate-50">End Session</button>
              </div>
            </div>
          </div>
        </aside>
      </div>

      {{-- Footer notice --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-4 text-[13px] text-slate-600">
        Assist Mode is an instructional aid for demonstrations—no grading, attendance, or scheduling is recorded.
      </div>

    </div>
  </section>
</main>

@includeIf('partials.faculty-footer')
@includeWhen(!View::exists('partials.faculty-footer'), 'partials.student-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>
<script>
  // ---------- Utils ----------
  const fmt = s => `${String(Math.floor(s/60)).padStart(2,'0')}:${String(s%60).padStart(2,'0')}`;
  const clamp = (n, min, max) => Math.max(min, Math.min(max, n));

  // ---------- State ----------
  const STATE_KEY = 'NURSYNC_ASSIST_' + @json($procedure->slug ?? 'proc');
  const DEFAULT_STATE = { current: 0, notes: '', ppe: {}, steps: {}, overallSec: 0, overallRunning: false };
  let state = { ...DEFAULT_STATE };
  try { const saved = JSON.parse(localStorage.getItem(STATE_KEY) || '{}'); state = { ...state, ...saved }; } catch {}

  function persist() { localStorage.setItem(STATE_KEY, JSON.stringify(state)); }

  // ---------- Overall Timer ----------
  let overallInt = null;
  const elOverall = document.getElementById('overallTimer');
  const btnToggle = document.getElementById('btnTimerToggle');
  const btnResetOverall = document.getElementById('btnTimerReset');

  function setOverallRunning(run) {
    state.overallRunning = !!run; persist();
    btnToggle.textContent = state.overallRunning ? 'Pause' : 'Start';
    if (overallInt) { clearInterval(overallInt); overallInt = null; }
    if (state.overallRunning) {
      overallInt = setInterval(()=>{ state.overallSec++; elOverall.textContent = fmt(state.overallSec); }, 1000);
    }
  }
  elOverall.textContent = fmt(state.overallSec || 0);
  setOverallRunning(state.overallRunning);
  btnToggle.onclick = () => setOverallRunning(!state.overallRunning);
  btnResetOverall.onclick = () => { state.overallSec = 0; persist(); elOverall.textContent = '00:00'; };

  // ---------- Steps & Navigation ----------
  const steps = Array.from(document.querySelectorAll('.js-step'));
  let idx = clamp(state.current || 0, 0, Math.max(0, steps.length-1));
  const progressText = document.getElementById('progressText');
  const progressBar = document.getElementById('progressBar');

  function show(i) {
    if (!steps.length) return;
    idx = clamp(i, 0, steps.length-1);
    state.current = idx; persist();
    steps.forEach((s,k)=> s.classList.toggle('hidden', k!==idx));
    progressText.textContent = `Step ${idx+1}`;
    const doneCount = Array.from(document.querySelectorAll('.js-step-done')).filter(cb=>cb.checked).length;
    const pct = steps.length ? Math.round(((idx) / (steps.length-1 || 1)) * 100) : 0;
    progressBar.style.width = `${clamp(pct, 0, 100)}%`;
    // highlight current checklist item
    document.querySelectorAll('#checklist label').forEach((lab,k)=>{
      lab.classList.toggle('ring-1', k===idx);
      lab.classList.toggle('ring-slate-300', k===idx);
    });
  }

  document.getElementById('btnPrev').onclick = () => show(idx-1);
  document.getElementById('btnNext').onclick = () => show(idx+1);

  // Keyboard shortcuts
  window.addEventListener('keydown', (e)=>{
    if (['INPUT','TEXTAREA'].includes(document.activeElement.tagName)) return; // ignore while typing
    if (e.key === 'ArrowLeft') { e.preventDefault(); show(idx-1); }
    if (e.key === 'ArrowRight') { e.preventDefault(); show(idx+1); }
    if (e.code === 'Space') {
      e.preventDefault();
      const cur = steps[idx];
      cur?.querySelector('.js-step-timer-toggle')?.click();
    }
  });

  // ---------- Checklist ----------
  document.querySelectorAll('.js-jump').forEach(btn=>{
    btn.addEventListener('click', ()=> show(Number(btn.dataset.index || 0)));
  });

  const savedSteps = state.steps || {};
  document.querySelectorAll('.js-step-done').forEach(cb=>{
    const i = String(cb.dataset.index);
    cb.checked = savedSteps[i]?.done || false;
    cb.addEventListener('change', ()=>{
      state.steps[i] = { ...(state.steps[i]||{}), done: cb.checked, sec: (state.steps[i]?.sec||0) };
      persist();
      // auto-advance on check
      if (cb.checked) show(idx+1);
    });
  });

  // ---------- Per-step timers ----------
  steps.forEach(step=>{
    const i = String(step.dataset.index || '0');
    const t = step.querySelector('.js-step-timer');
    const btnToggle = step.querySelector('.js-step-timer-toggle');
    const btnReset  = step.querySelector('.js-step-timer-reset');

    let sec = savedSteps[i]?.sec || 0;
    t.textContent = fmt(sec);
    let int = null;

    function run(on) {
      if (int) { clearInterval(int); int=null; }
      if (on) int = setInterval(()=>{ sec++; t.textContent = fmt(sec); state.steps[i] = { ...(state.steps[i]||{}), sec, done: state.steps[i]?.done||false }; persist(); }, 1000);
      btnToggle.textContent = on ? 'Pause' : 'Start';
    }

    btnToggle?.addEventListener('click', ()=> run(!int));
    btnReset?.addEventListener('click', ()=>{ sec=0; t.textContent='00:00'; state.steps[i] = { ...(state.steps[i]||{}), sec }; persist(); });
  });

  // ---------- PPE & Notes persistence ----------
  const notes = document.getElementById('ciNotes');
  const saved = document.getElementById('notesSaved');
  notes.value = state.notes || '';
  function flashSaved(){ saved.classList.remove('opacity-0'); setTimeout(()=>saved.classList.add('opacity-0'), 900); }
  notes.addEventListener('input', ()=>{ state.notes = notes.value; persist(); flashSaved(); });

  const ppeBoxes = document.querySelectorAll('.js-ppe');
  ppeBoxes.forEach(cb=>{
    const key = cb.dataset.key;
    cb.checked = !!state.ppe?.[key];
    cb.addEventListener('change', ()=>{ state.ppe[key] = cb.checked; persist(); flashSaved(); });
  });

  // ---------- Session controls ----------
  document.getElementById('btnEndSession').onclick = () => {
    persist();
    alert('Session ended. Notes and checklist remain saved on this device.');
  };

  document.getElementById('btnResetSession').onclick = () => {
    if (!confirm('Reset timers, checklist, PPE checks and notes for this session?')) return;
    // clear timers
    if (overallInt) { clearInterval(overallInt); overallInt=null; }
    state = { ...DEFAULT_STATE };
    persist();
    // UI resets
    elOverall.textContent = '00:00';
    setOverallRunning(false);
    document.querySelectorAll('.js-step-timer').forEach(el=> el.textContent='00:00');
    document.querySelectorAll('.js-step-done, .js-ppe').forEach(cb=> cb.checked=false);
    notes.value = '';
    show(0);
    flashSaved();
  };

  // Initialize current view
  show(idx);
</script>
</body>
</html>
