{{-- resources/views/faculty/calculators/index.blade.php --}}
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <title>Calculators · NurSync — CI</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
        }

        .result-kbd {
            @apply inline-block rounded px-2 py-0.5 text-[12px] bg-slate-100 text-slate-700;
        }

        .label-sm {
            @apply block text-[12px] font-medium text-slate-700;
        }

        .inp {
            @apply w-full rounded-xl border border-slate-300 px-3 py-2 text-[13px] focus:outline-none focus:ring-2 focus:ring-emerald-200;
        }

        .sel {
            @apply w-full rounded-xl border border-slate-300 px-3 py-2 text-[13px] bg-white focus:outline-none focus:ring-2 focus:ring-emerald-200;
        }

        .btn {
            @apply inline-flex items-center justify-center rounded-xl border border-slate-300 px-3 py-2 text-[13px] font-medium hover:bg-slate-50;
        }

        .card {
            @apply rounded-2xl border border-slate-200 bg-white p-5;
        }

        .card-h2 {
            @apply text-[15px] font-semibold text-slate-800;
        }

        .subtle {
            @apply text-[12px] text-slate-500;
        }
    </style>
</head>

<body class="min-h-screen bg-slate-50">
    <main class="min-h-screen flex">
        {{-- Sidebar --}}
        @include('partials.faculty-sidebar', ['active' => 'calculators'])

        {{-- Main content --}}
        <section class="flex-1 px-8 py-10">
            {{-- Title --}}
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-green-50 text-green-600">
                    <i data-lucide="calculator" class="h-5 w-5"></i>
                </span>
                <h1 class="text-2xl font-bold">Nurse Calculators</h1>
            </div>
            <p class="text-[13px] text-slate-500 mt-1">All clinical aids on one page. Enter values, see results
                instantly, and copy notes for charting.</p>

            {{-- Quick links (in-page anchors) --}}


{{-- REPLACE the entire "<div class='mt-8 grid gap-4 lg:grid-cols-2'> … </div>" block with this --}}

<div class="mt-8 grid gap-5 lg:grid-cols-2">

  {{-- CARD: DOSAGE --}}
  <article id="dosage" class="rounded-2xl border border-slate-200 bg-white p-5">
    <header class="flex items-center justify-between">
      <h2 class="text-[15px] font-semibold text-slate-800">Medication Dosage</h2>
      <span class="text-[12px] text-slate-500">Formula: (Ordered ÷ Stock) × Volume</span>
    </header>

    <div class="mt-4 grid gap-3 sm:grid-cols-2">
      <label class="block">
        <span class="block text-[12px] font-medium text-slate-700">Ordered dose</span>
        <div class="mt-1 flex gap-2">
          <input type="number" step="0.001" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-[13px]" data-calc="dosage-ordered" placeholder="e.g., 500">
          <select class="rounded-xl border border-slate-300 px-3 py-2 text-[13px]" data-calc="dosage-ordered-unit">
            <option value="mg">mg</option><option value="mcg">mcg</option>
          </select>
        </div>
      </label>

      <label class="block">
        <span class="block text-[12px] font-medium text-slate-700">Stock strength</span>
        <div class="mt-1 flex gap-2">
          <input type="number" step="0.001" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-[13px]" data-calc="dosage-stock">
          <select class="rounded-xl border border-slate-300 px-3 py-2 text-[13px]" data-calc="dosage-stock-unit">
            <option value="mg">mg</option><option value="mcg">mcg</option>
          </select>
        </div>
        <p class="mt-1 text-[12px] text-slate-500">Strength per tablet or per mL in vial.</p>
      </label>

      <label class="block">
        <span class="block text-[12px] font-medium text-slate-700">Volume per stock (if liquid)</span>
        <div class="mt-1 flex items-center gap-2">
          <input type="number" step="0.001" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-[13px]" data-calc="dosage-volume" placeholder="mL per stock">
          <span class="inline-block rounded px-2 py-0.5 text-[12px] bg-slate-100 text-slate-700">mL</span>
        </div>
      </label>

      <div>
        <span class="block text-[12px] font-medium text-slate-700">Patient weight (optional)</span>
        <div class="mt-1 flex items-center gap-2">
          <input type="number" step="0.01" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-[13px]" data-calc="dosage-weight" placeholder="kg">
          <span class="inline-block rounded px-2 py-0.5 text-[12px] bg-slate-100 text-slate-700">kg</span>
        </div>
        <div class="mt-2 flex items-center gap-2">
          <input type="number" step="0.01" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-[13px]" data-calc="dosage-mgkg" placeholder="Target mg/kg (optional)">
          <span class="inline-block rounded px-2 py-0.5 text-[12px] bg-slate-100 text-slate-700">mg/kg</span>
        </div>
      </div>
    </div>

    <div class="mt-4 grid gap-3 sm:grid-cols-2">
      <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
        <div class="text-[12px] text-slate-500">Result — Liquid draw</div>
        <div class="mt-1 text-xl font-bold"><span data-out="dosage-ml">—</span> mL</div>
      </div>
      <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
        <div class="text-[12px] text-slate-500">Result — Tablets</div>
        <div class="mt-1 text-xl font-bold"><span data-out="dosage-tabs">—</span> tabs</div>
      </div>
    </div>

    <p class="mt-3 text-[12px] text-amber-700 bg-amber-50 border border-amber-200 rounded-xl p-3 hidden" data-vis="dosage-warning">
      <b>Warning:</b> Result exceeds the soft threshold for a single dose. Review order and stock strength.
    </p>

    <footer class="mt-4 flex items-center gap-2">
      <button class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-3 py-2 text-[13px] font-medium hover:bg-slate-50" data-copy="dosage">Copy note</button>
      <span class="text-[12px] text-slate-500">e.g., “Calculated: <span data-out="dosage-note">—</span>”.</span>
    </footer>
  </article>

  {{-- CARD: IV RATE --}}
  <article id="ivrate" class="rounded-2xl border border-slate-200 bg-white p-5">
    <header class="flex items-center justify-between">
      <h2 class="text-[15px] font-semibold text-slate-800">IV Flow & Infusion Rate</h2>
      <span class="text-[12px] text-slate-500">Pump: mL/hr = Volume ÷ Hours • Gravity: (mL × gtt/mL) ÷ min</span>
    </header>

    <div class="mt-4 grid gap-3 sm:grid-cols-2">
      <label class="block">
        <span class="block text-[12px] font-medium text-slate-700">Total volume (mL)</span>
        <input type="number" step="0.01" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-[13px]" data-calc="iv-vol" placeholder="e.g., 500">
      </label>
      <label class="block">
        <span class="block text-[12px] font-medium text-slate-700">Infuse over</span>
        <div class="mt-1 flex items-center gap-2">
          <input type="number" step="0.01" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-[13px]" data-calc="iv-hours" placeholder="hours">
          <span class="inline-block rounded px-2 py-0.5 text-[12px] bg-slate-100 text-slate-700">hr</span>
        </div>
      </label>
      <label class="block">
        <span class="block text-[12px] font-medium text-slate-700">Drop factor (gravity)</span>
        <div class="mt-1 flex items-center gap-2">
          <input type="number" step="1" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-[13px]" data-calc="iv-gtt" placeholder="e.g., 20">
          <span class="inline-block rounded px-2 py-0.5 text-[12px] bg-slate-100 text-slate-700">gtt/mL</span>
        </div>
      </label>
      <label class="block">
        <span class="block text-[12px] font-medium text-slate-700">Alternate: minutes (gravity)</span>
        <div class="mt-1 flex items-center gap-2">
          <input type="number" step="1" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-[13px]" data-calc="iv-min" placeholder="minutes">
          <span class="inline-block rounded px-2 py-0.5 text-[12px] bg-slate-100 text-slate-700">min</span>
        </div>
      </label>
    </div>

    <div class="mt-4 grid gap-3 sm:grid-cols-2">
      <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
        <div class="text-[12px] text-slate-500">Pump rate</div>
        <div class="mt-1 text-xl font-bold"><span data-out="iv-mlhr">—</span> mL/hr</div>
      </div>
      <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
        <div class="text-[12px] text-slate-500">Gravity</div>
        <div class="mt-1 text-xl font-bold"><span data-out="iv-gttmin">—</span> gtt/min</div>
      </div>
    </div>

    <p class="mt-3 text-[12px] text-amber-700 bg-amber-50 border border-amber-200 rounded-xl p-3 hidden" data-vis="iv-warning">
      <b>Warning:</b> Pump rate unusually high for maintenance. Verify order and patient status.
    </p>

    <footer class="mt-4 flex items-center gap-2">
      <button class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-3 py-2 text-[13px] font-medium hover:bg-slate-50" data-copy="iv">Copy note</button>
      <span class="text-[12px] text-slate-500">“IV rate set to <span data-out="iv-note">—</span>”.</span>
    </footer>
  </article>

  {{-- CARD: DILUTION --}}
  <article id="dilution" class="rounded-2xl border border-slate-200 bg-white p-5">
    <header class="flex items-center justify-between">
      <h2 class="text-[15px] font-semibold text-slate-800">Drug Dilution / Concentration</h2>
      <span class="text-[12px] text-slate-500">Solve any unknown: C = Dose ÷ Volume</span>
    </header>

    <div class="mt-4 grid gap-3 sm:grid-cols-3">
      <label class="block">
        <span class="block text-[12px] font-medium text-slate-700">Dose (mg)</span>
        <input type="number" step="0.001" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-[13px]" data-calc="dil-dose" placeholder="mg">
      </label>
      <label class="block">
        <span class="block text-[12px] font-medium text-slate-700">Volume (mL)</span>
        <input type="number" step="0.001" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-[13px]" data-calc="dil-vol" placeholder="mL">
      </label>
      <label class="block">
        <span class="block text-[12px] font-medium text-slate-700">Concentration</span>
        <div class="mt-1 flex items-center gap-2">
          <input type="number" step="0.0001" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-[13px]" data-calc="dil-conc" placeholder="mg/mL">
          <span class="text-[12px] text-slate-500">mg/mL</span>
        </div>
      </label>
    </div>

    <p class="mt-2 text-[12px] text-slate-500">Leave one field blank — it will be computed from the others.</p>

    <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-3">
      <div class="text-[12px] text-slate-500">Result</div>
      <div class="mt-1 text-xl font-bold" data-out="dil-result">—</div>
    </div>

    <footer class="mt-4 flex items-center gap-2">
      <button class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-3 py-2 text-[13px] font-medium hover:bg-slate-50" data-copy="dilution">Copy note</button>
      <span class="text-[12px] text-slate-500">“Prepared <span data-out="dil-note">—</span>”.</span>
    </footer>
  </article>

  {{-- CARD: I&O --}}
  <article id="io" class="rounded-2xl border border-slate-200 bg-white p-5">
    <header class="flex items-center justify-between">
      <h2 class="text-[15px] font-semibold text-slate-800">Intake & Output (I&O)</h2>
      <span class="text-[12px] text-slate-500">Net = Intake − Output</span>
    </header>

    <div class="mt-4 grid gap-3 sm:grid-cols-2">
      @php $ioPairs = [
        ['Intake — Oral (mL)','io-in-oral'],['Output — Urine (mL)','io-out-urine'],
        ['Intake — IV (mL)','io-in-iv'],   ['Output — Stool (mL)','io-out-stool'],
        ['Intake — Tube (mL)','io-in-tube'],['Output — Emesis (mL)','io-out-emesis'],
      ]; @endphp
      @foreach($ioPairs as [$label,$key])
        <label class="block">
          <span class="block text-[12px] font-medium text-slate-700">{{ $label }}</span>
          <input type="number" step="1" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-[13px]" data-calc="{{ $key }}">
        </label>
      @endforeach
      <label class="block sm:col-span-2">
        <span class="block text-[12px] font-medium text-slate-700">Output — Drains (mL)</span>
        <input type="number" step="1" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-[13px]" data-calc="io-out-drain">
      </label>
    </div>

    <div class="mt-4 grid gap-3 sm:grid-cols-3">
      <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
        <div class="text-[12px] text-slate-500">Total Intake</div>
        <div class="mt-1 text-xl font-bold"><span data-out="io-intake">—</span> mL</div>
      </div>
      <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
        <div class="text-[12px] text-slate-500">Total Output</div>
        <div class="mt-1 text-xl font-bold"><span data-out="io-output">—</span> mL</div>
      </div>
      <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
        <div class="text-[12px] text-slate-500">Net Balance</div>
        <div class="mt-1 text-xl font-bold"><span data-out="io-net">—</span> mL</div>
      </div>
    </div>

    <footer class="mt-4 flex items-center gap-2">
      <button class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-3 py-2 text-[13px] font-medium hover:bg-slate-50" data-copy="io">Copy note</button>
      <span class="text-[12px] text-slate-500">“I&O 24h: Intake <span data-out="io-intake">—</span> mL, Output <span data-out="io-output">—</span> mL, Net <span data-out="io-net">—</span> mL.”</span>
    </footer>
  </article>

  {{-- CARD: BMI/BSA --}}
  <article id="bmi" class="rounded-2xl border border-slate-200 bg-white p-5">
    <header class="flex items-center justify-between">
      <h2 class="text-[15px] font-semibold text-slate-800">BMI & BSA</h2>
      <span class="text-[12px] text-slate-500">BMI = kg/m² • Mosteller BSA = √((cm × kg)/3600)</span>
    </header>

    <div class="mt-4 grid gap-3 sm:grid-cols-2">
      <label class="block">
        <span class="block text-[12px] font-medium text-slate-700">Weight (kg)</span>
        <input type="number" step="0.01" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-[13px]" data-calc="bmi-kg" placeholder="kg">
      </label>
      <label class="block">
        <span class="block text-[12px] font-medium text-slate-700">Height (cm)</span>
        <input type="number" step="0.1" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-[13px]" data-calc="bmi-cm" placeholder="cm">
      </label>
    </div>

    <div class="mt-4 grid gap-3 sm:grid-cols-2">
      <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
        <div class="text-[12px] text-slate-500">BMI</div>
        <div class="mt-1 text-xl font-bold" data-out="bmi-bmi">—</div>
      </div>
      <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
        <div class="text-[12px] text-slate-500">BSA (m²)</div>
        <div class="mt-1 text-xl font-bold" data-out="bmi-bsa">—</div>
      </div>
    </div>
  </article>

  {{-- CARD: PEDIATRIC mg/kg --}}
  <article id="peds" class="rounded-2xl border border-slate-200 bg-white p-5">
    <header class="flex items-center justify-between">
      <h2 class="text-[15px] font-semibold text-slate-800">Pediatric Dose (mg/kg)</h2>
      <span class="text-[12px] text-slate-500">Capped by Max Single Dose</span>
    </header>

    <div class="mt-4 grid gap-3 sm:grid-cols-3">
      <label class="block">
        <span class="block text-[12px] font-medium text-slate-700">Target (mg/kg)</span>
        <input type="number" step="0.01" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-[13px]" data-calc="peds-mgkg" placeholder="e.g., 10">
      </label>
      <label class="block">
        <span class="block text-[12px] font-medium text-slate-700">Weight (kg)</span>
        <input type="number" step="0.01" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-[13px]" data-calc="peds-kg" placeholder="kg">
      </label>
      <label class="block">
        <span class="block text-[12px] font-medium text-slate-700">Max single dose (mg)</span>
        <input type="number" step="0.01" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-[13px]" data-calc="peds-max" placeholder="e.g., 500">
      </label>
    </div>

    <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-3">
      <div class="text-[12px] text-slate-500">Result</div>
      <div class="mt-1 text-xl font-bold"><span data-out="peds-dose">—</span> mg</div>
      <div class="mt-1 text-[12px] text-slate-500" data-vis="peds-cap" hidden>Note: Calculated dose exceeded max and was capped.</div>
    </div>


  </article>

</div>



        </section>
    </main>

    {{-- Footer --}}
    @include('partials.faculty-footer')

    {{-- Lucide --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    <script> lucide.createIcons(); </script>

    {{-- Inline calc logic (lightweight, no deps) --}}
    <script>
        (function () {
            const $ = (sel) => document.querySelector(sel);
            const $$ = (sel) => [...document.querySelectorAll(sel)];
            const val = (el) => {
                const n = parseFloat(el?.value ?? '');
                return isFinite(n) ? n : 0;
            };
            const setText = (attr, text) => {
                $$(`[data-out="${attr}"]`).forEach(e => e.textContent = text);
            };
            const show = (attr, on) => {
                const el = document.querySelector(`[data-vis="${attr}"]`);
                if (!el) return;
                el.classList.toggle('hidden', !on);
                if (el.hasAttribute('hidden')) el.hidden = !on;
            };
            const copyNote = (key, builder) => {
                const note = builder();
                navigator.clipboard?.writeText(note);
            };

            // --- DOSAGE ---
            function computeDosage() {
                const ordered = val($('[data-calc="dosage-ordered"]'));
                const ordUnit = $('[data-calc="dosage-ordered-unit"]').value;
                const stock = val($('[data-calc="dosage-stock"]'));
                const stkUnit = $('[data-calc="dosage-stock-unit"]').value;
                let volume = val($('[data-calc="dosage-volume"]'));
                const kg = val($('[data-calc="dosage-weight"]'));
                const mgkg = val($('[data-calc="dosage-mgkg"]'));

                // Normalize mcg->mg if needed
                const toMg = (x, unit) => (unit === 'mcg' ? x / 1000 : x);
                const ordMg = toMg(ordered, ordUnit);
                const stkMg = toMg(stock, stkUnit);

                // Weight-based calc (optional)
                let wbDoseMg = 0;
                if (kg > 0 && mgkg > 0) wbDoseMg = kg * mgkg;

                // Liquid draw mL (if volume and stock strength provided)
                let ml = '—';
                if (stkMg > 0) {
                    const base = (ordMg / stkMg) * (volume > 0 ? volume : 1);
                    ml = (volume > 0 ? base : 0).toFixed(volume > 0 ? 2 : 0);
                }

                // Tablets (strength per tab scenario) -> when volume not provided
                let tabs = '—';
                if (stkMg > 0 && (!volume || volume === 0)) {
                    tabs = (ordMg / stkMg).toFixed(2);
                }

                // Safety hint (soft thresholds)
                const warn = (parseFloat(ml) > 10) || (parseFloat(tabs) > 3);
                show('dosage-warning', warn);

                setText('dosage-ml', ml);
                setText('dosage-tabs', tabs);

                let note = '';
                if (wbDoseMg > 0) note = `Weight-based dose ≈ ${wbDoseMg.toFixed(2)} mg. `;
                note += `Calculated: ${ml} mL / ${tabs} tabs`;
                setText('dosage-note', note);
            }

            // --- IV RATE ---
            function computeIV() {
                const vol = val($('[data-calc="iv-vol"]'));
                const hrs = val($('[data-calc="iv-hours"]'));
                const gtt = val($('[data-calc="iv-gtt"]'));
                const mins = val($('[data-calc="iv-min"]'));

                const mlhr = hrs > 0 ? (vol / hrs) : 0;
                setText('iv-mlhr', mlhr ? mlhr.toFixed(0) : '—');

                const gravityMin = (mins > 0 && gtt > 0) ? ((vol * gtt) / mins) : 0;
                setText('iv-gttmin', gravityMin ? gravityMin.toFixed(0) : '—');

                show('iv-warning', mlhr > 150); // soft flag
                const note = `Pump ${mlhr ? mlhr.toFixed(0) : '—'} mL/hr • Gravity ${gravityMin ? gravityMin.toFixed(0) : '—'} gtt/min`;
                setText('iv-note', note);
            }

            // --- DILUTION ---
            function computeDilution() {
                let dose = $('[data-calc="dil-dose"]').value.trim();
                let vol = $('[data-calc="dil-vol"]').value.trim();
                let conc = $('[data-calc="dil-conc"]').value.trim();

                const d = parseFloat(dose), v = parseFloat(vol), c = parseFloat(conc);
                let out = '—', note = '—';

                const hasD = isFinite(d), hasV = isFinite(v), hasC = isFinite(c);

                if (hasD && hasV && !hasC) {
                    const calcC = d / v;
                    out = `Concentration = ${calcC.toFixed(3)} mg/mL`;
                    note = `${d} mg in ${v} mL → ${calcC.toFixed(3)} mg/mL`;
                    $('[data-calc="dil-conc"]').value = calcC.toFixed(3);
                } else if (hasD && hasC && !hasV) {
                    const calcV = d / c;
                    out = `Volume = ${calcV.toFixed(2)} mL`;
                    note = `${d} mg @ ${c} mg/mL → ${calcV.toFixed(2)} mL`;
                    $('[data-calc="dil-vol"]').value = calcV.toFixed(2);
                } else if (hasV && hasC && !hasD) {
                    const calcD = v * c;
                    out = `Dose = ${calcD.toFixed(2)} mg`;
                    note = `${v} mL @ ${c} mg/mL → ${calcD.toFixed(2)} mg`;
                    $('[data-calc="dil-dose"]').value = calcD.toFixed(2);
                } else if (hasD && hasV && hasC) {
                    out = `All set: ${d} mg, ${v} mL, ${c} mg/mL`;
                    note = `${d} mg in ${v} mL (${c} mg/mL)`;
                }

                $('[data-out="dil-result"]').textContent = out;
                setText('dil-note', note);
            }

            // --- I&O ---
            function computeIO() {
                const inOral = val($('[data-calc="io-in-oral"]'));
                const inIV = val($('[data-calc="io-in-iv"]'));
                const inTube = val($('[data-calc="io-in-tube"]'));

                const outUr = val($('[data-calc="io-out-urine"]'));
                const outSt = val($('[data-calc="io-out-stool"]'));
                const outEm = val($('[data-calc="io-out-emesis"]'));
                const outDr = val($('[data-calc="io-out-drain"]'));

                const intake = inOral + inIV + inTube;
                const output = outUr + outSt + outEm + outDr;
                const net = intake - output;

                setText('io-intake', intake.toFixed(0));
                setText('io-output', output.toFixed(0));
                setText('io-net', net.toFixed(0));
            }

            // --- BMI/BSA ---
            function computeBMI() {
                const kg = val($('[data-calc="bmi-kg"]'));
                const cm = val($('[data-calc="bmi-cm"]'));
                const m = cm / 100;

                const bmi = (kg > 0 && m > 0) ? (kg / (m * m)) : 0;
                const bsa = (kg > 0 && cm > 0) ? Math.sqrt((cm * kg) / 3600) : 0;

                setText('bmi-bmi', bmi ? bmi.toFixed(1) : '—');
                setText('bmi-bsa', bsa ? bsa.toFixed(2) : '—');
            }

            // --- PEDS mg/kg ---
            function computePeds() {
                const mgkg = val($('[data-calc="peds-mgkg"]'));
                const kg = val($('[data-calc="peds-kg"]'));
                const max = val($('[data-calc="peds-max"]'));

                let dose = mgkg * kg;
                let capped = false;
                if (max > 0 && dose > max) { dose = max; capped = true; }
                setText('peds-dose', isFinite(dose) && dose > 0 ? dose.toFixed(2) : '—');
                show('peds-cap', capped);
            }

            // Wire inputs
            [
                ['[data-calc^="dosage"]', computeDosage],
                ['[data-calc^="iv-"]', computeIV],
                ['[data-calc^="dil-"]', computeDilution],
                ['[data-calc^="io-"]', computeIO],
                ['[data-calc^="bmi-"]', computeBMI],
                ['[data-calc^="peds-"]', computePeds],
            ].forEach(([sel, fn]) => {
                $$(sel).forEach(el => el.addEventListener('input', fn));
            });

            // Copy buttons
            $$('[data-copy="dosage"]').forEach(b => b.addEventListener('click', () => {
                const t = document.querySelector('[data-out="dosage-note"]').textContent.trim();
                copyNote('dosage', () => `DOSAGE: ${t}`);
            }));
            $$('[data-copy="iv"]').forEach(b => b.addEventListener('click', () => {
                const t = document.querySelector('[data-out="iv-note"]').textContent.trim();
                copyNote('iv', () => `IV RATE: ${t}`);
            }));
            $$('[data-copy="dilution"]').forEach(b => b.addEventListener('click', () => {
                const t = document.querySelector('[data-out="dil-note"]').textContent.trim();
                copyNote('dil', () => `DILUTION: ${t}`);
            }));
            $$('[data-copy="io"]').forEach(b => b.addEventListener('click', () => {
                const inT = document.querySelector('[data-out="io-intake"]').textContent;
                const ouT = document.querySelector('[data-out="io-output"]').textContent;
                const neT = document.querySelector('[data-out="io-net"]').textContent;
                copyNote('io', () => `I&O: Intake ${inT} mL, Output ${ouT} mL, Net ${neT} mL`);
            }));
            $$('[data-copy="peds"]').forEach(b => b.addEventListener('click', () => {
                const d = document.querySelector('[data-out="peds-dose"]').textContent;
                copyNote('peds', () => `PEDIATRIC DOSE: ${d} mg`);
            }));

            // Initial compute
            computeDosage(); computeIV(); computeDilution(); computeIO(); computeBMI(); computePeds();
        })();
    </script>
</body>

</html>