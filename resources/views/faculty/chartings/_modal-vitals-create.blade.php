{{-- resources/views/faculty/chartings/_modal-vitals-create.blade.php --}}
<div id="modalCreateVitals" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-modal-close></div>

  <div class="relative mx-auto my-10 w-[95%] max-w-5xl rounded-2xl bg-white shadow-2xl border border-slate-200">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-2xl">
      <h3 class="text-lg font-bold text-slate-900">Record Vital Signs</h3>
      <button type="button"
              class="h-9 w-9 inline-flex items-center justify-center rounded-lg hover:bg-slate-100"
              data-modal-close>
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    <form method="POST"
          action="{{ route('faculty.chartings.vitals.store', $patient->id) }}"
          class="p-6 space-y-5">
      @csrf

      {{-- 2-column flexible layout --}}
      <div class="grid gap-6 md:grid-cols-[minmax(0,1.25fr)_minmax(0,1fr)]">

        {{-- LEFT PANEL --}}
        <div>
          <div class="grid gap-4 md:grid-cols-2">

            {{-- Timestamp --}}
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Taken At *</label>
              <input name="taken_at" type="datetime-local" required
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            {{-- Remarks --}}
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Remarks</label>
              <input name="remarks" type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="e.g., pre-medication, at rest, post-ambulation">
            </div>

            {{-- Temperature --}}
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Temperature (°C)</label>
              <input name="temp_c" type="number" step="0.1" min="30" max="45"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            {{-- HR --}}
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Heart Rate (bpm)</label>
              <input name="heart_rate_bpm" type="number" min="0" max="250"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            {{-- RR --}}
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Respiratory Rate (cpm)</label>
              <input name="resp_rate_cpm" type="number" min="0" max="80"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            {{-- BP --}}
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">BP Systolic</label>
                <input name="bp_systolic" type="number" min="40" max="300"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">BP Diastolic</label>
                <input name="bp_diastolic" type="number" min="20" max="200"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
              </div>
            </div>

            {{-- SpO2 --}}
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">SpO₂ (%)</label>
              <input name="spo2_pct" type="number" min="0" max="100"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            {{-- Pain --}}
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Pain Score (0–10)</label>
              <input name="pain_score" type="number" min="0" max="10"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            {{-- Weight --}}
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Weight (kg)</label>
              <input id="vitalsWeightKg" name="weight_kg" type="number" step="0.1" min="0"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            {{-- Height --}}
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Height (cm)</label>
              <input id="vitalsHeightCm" name="height_cm" type="number" step="0.1" min="0"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            {{-- BMI --}}
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">BMI</label>
              <input id="vitalsBmi" name="bmi" readonly
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-slate-50"
                     placeholder="auto-calculated">
            </div>

            {{-- BSA --}}
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">BSA (m²)</label>
              <input id="vitalsBsa" name="bsa_m2" readonly
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-slate-50"
                     placeholder="auto-calculated">
            </div>

            {{-- BMI Category --}}
            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">BMI Category</label>
              <input id="vitalsBmiCategory" name="bmi_category" readonly
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-slate-50"
                     placeholder="auto-filled based on BMI">
            </div>

          </div>
        </div>

        {{-- RIGHT PANEL (Calculator) --}}
        <aside class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 space-y-3">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-sm font-semibold text-slate-900">BMI &amp; BSA Calculator</h4>
              <p class="text-[11px] text-slate-500">
                BMI uses weight (kg) ÷ height² (m²).  
                BSA uses the Mosteller formula.
              </p>
            </div>
            <i data-lucide="activity" class="h-4 w-4 text-slate-400"></i>
          </div>

          {{-- Inputs for calculator --}}
          <div class="space-y-3">
            <div>
              <label class="block text-[11px] font-semibold text-slate-600 mb-1">Weight (kg)</label>
              <input id="calcWeightKg" type="number" step="0.1" min="0"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs">
            </div>

            <div>
              <label class="block text-[11px] font-semibold text-slate-600 mb-1">Height (cm)</label>
              <input id="calcHeightCm" type="number" step="0.1" min="0"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs">
            </div>

            {{-- Results --}}
            <div class="grid grid-cols-2 gap-3 text-xs">
              <div>
                <p class="text-[11px] font-semibold text-slate-500">BMI</p>
                <p id="calcResultBmi"
                   class="mt-1 text-base font-semibold text-slate-900">—</p>
              </div>

              <div>
                <p class="text-[11px] font-semibold text-slate-500">BSA (m²)</p>
                <p id="calcResultBsa"
                   class="mt-1 text-base font-semibold text-slate-900">—</p>
              </div>
            </div>

            {{-- Classification --}}
            <div class="mt-2 rounded-xl border border-slate-200 bg-white px-3 py-2">
              <p class="text-[11px] font-semibold text-slate-600 mb-1">BMI Classification</p>
              <span id="calcResultBmiLabel"
                    class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-semibold text-slate-500">
                —
              </span>
            </div>

            {{-- Apply to left side --}}
            <button type="button"
                    id="btnVitalsApplyAnthro"
                    class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-1.5 text-[11px] font-medium text-white hover:bg-emerald-700">
              Use result in fields
            </button>

            <p class="text-[10px] text-slate-400">
              BMI = kg / (m²)  
              BSA = √((cm × kg) / 3600)
            </p>
          </div>
        </aside>
      </div>

      {{-- Submit --}}
      <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-200">
        <button type="button" data-modal-close
                class="rounded-lg px-5 py-2 text-sm border border-slate-300 text-slate-700 hover:bg-slate-100">
          Cancel
        </button>

        <button type="submit"
                class="rounded-lg px-5 py-2 text-sm bg-emerald-600 text-white hover:bg-emerald-700">
          Save
        </button>
      </div>

    </form>
  </div>
</div>


{{-- Inline script for BMI/BSA calculator + auto-fill + BMI classification --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const calcWeight    = document.getElementById('calcWeightKg');
    const calcHeight    = document.getElementById('calcHeightCm');
    const calcResultBmi = document.getElementById('calcResultBmi');
    const calcResultBsa = document.getElementById('calcResultBsa');
    const calcResultBmiLabel = document.getElementById('calcResultBmiLabel');

    const fieldWeight      = document.getElementById('vitalsWeightKg');
    const fieldHeight      = document.getElementById('vitalsHeightCm');
    const fieldBmi         = document.getElementById('vitalsBmi');
    const fieldBsa         = document.getElementById('vitalsBsa');
    const fieldBmiCategory = document.getElementById('vitalsBmiCategory');

    const btnApply      = document.getElementById('btnVitalsApplyAnthro');

    if (!calcWeight || !calcHeight || !calcResultBmi) return;

    const bmiLabelBaseClass =
      'inline-flex items-center rounded-full border px-2.5 py-1 text-[11px] font-semibold';

    function updateBmiClassification(bmi) {
      if (!calcResultBmiLabel) return;

      if (!bmi || bmi <= 0) {
        calcResultBmiLabel.textContent = '—';
        calcResultBmiLabel.className =
          bmiLabelBaseClass + ' border-slate-200 bg-slate-50 text-slate-500';
        if (fieldBmiCategory) fieldBmiCategory.value = '';
        return;
      }

      let category = '';
      let extraClass = '';

      // Standard adult BMI categories
      if (bmi < 18.5) {
        category = 'Underweight';
        extraClass = ' border-sky-200 bg-sky-50 text-sky-800';
      } else if (bmi < 25) {
        category = 'Healthy weight';
        extraClass = ' border-emerald-200 bg-emerald-50 text-emerald-800';
      } else if (bmi < 30) {
        category = 'Overweight';
        extraClass = ' border-amber-200 bg-amber-50 text-amber-800';
      } else {
        category = 'Obesity';
        extraClass = ' border-rose-200 bg-rose-50 text-rose-800';
      }

      calcResultBmiLabel.textContent = category;
      calcResultBmiLabel.className = bmiLabelBaseClass + extraClass;

      if (fieldBmiCategory) {
        fieldBmiCategory.value = category;
      }
    }

    function computeAnthro() {
      const w = parseFloat(calcWeight.value) || 0;
      const h = parseFloat(calcHeight.value) || 0;

      let bmiText = '—';
      let bsaText = '—';
      let bmiNumeric = 0;

      if (w > 0 && h > 0) {
        const hMeters = h / 100;                  // cm → m
        const bmi = w / (hMeters * hMeters);      // accurate metric BMI
        const bsa = Math.sqrt((w * h) / 3600);    // Mosteller BSA

        bmiNumeric = bmi;
        bmiText = bmi.toFixed(2);
        bsaText = bsa.toFixed(2);
      }

      calcResultBmi.textContent = bmiText;
      calcResultBsa.textContent = bsaText;

      updateBmiClassification(bmiNumeric);
    }

    ['input', 'change'].forEach(evt => {
      calcWeight.addEventListener(evt, computeAnthro);
      calcHeight.addEventListener(evt, computeAnthro);
    });
    computeAnthro();

    btnApply?.addEventListener('click', function () {
      const w = parseFloat(calcWeight.value) || 0;
      const h = parseFloat(calcHeight.value) || 0;

      if (fieldWeight && w > 0) fieldWeight.value = w.toFixed(1);
      if (fieldHeight && h > 0) fieldHeight.value = h.toFixed(1);

      if (fieldBmi && calcResultBmi.textContent !== '—') {
        fieldBmi.value = parseFloat(calcResultBmi.textContent).toFixed(2);
      }
      if (fieldBsa && calcResultBsa.textContent !== '—') {
        fieldBsa.value = parseFloat(calcResultBsa.textContent).toFixed(2);
      }

      // BMI category already synchronized through computeAnthro -> updateBmiClassification
    });
  });
</script>
