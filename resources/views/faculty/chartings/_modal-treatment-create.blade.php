{{-- resources/views/faculty/chartings/_modal-treatment-create.blade.php --}}
<div id="modalCreateTreatment" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-modal-close></div>

  <div class="relative mx-auto my-10 w-[95%] max-w-5xl rounded-2xl bg-white shadow-2xl border border-slate-200">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-2xl">
      <h3 class="text-lg font-bold text-slate-900">New Treatment / Procedure</h3>
      <button type="button" class="h-9 w-9 inline-flex items-center justify-center rounded-lg hover:bg-slate-100" data-modal-close>
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    <form method="POST"
          action="{{ route('faculty.chartings.treatment.store', $patient->id) }}"
          class="p-6 space-y-5">
      @csrf

      {{-- 2-column layout: left = form, right = IV rate calculator --}}
      <div class="grid gap-6 md:grid-cols-[minmax(0,1.35fr)_minmax(0,1fr)]">
        {{-- LEFT: TREATMENT / PROCEDURE FORM --}}
        <div>
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Performed At *</label>
              <input name="performed_at" type="datetime-local" required
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Procedure Name *</label>
              <input name="procedure_name" required type="text"
                     placeholder="e.g., Wound dressing change"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Indication</label>
              <input name="indication" type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="e.g., Post-op wound care, pain management">
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Details / Steps</label>
              <textarea name="details" rows="3"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Briefly describe the procedure, aseptic technique, special precautions, etc."></textarea>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Outcome</label>
              <input name="outcome" type="text"
                     placeholder="e.g., Clean, dry, intact; patient tolerated procedure well"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Performed By</label>
              <input name="performed_by" type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="e.g., RN Santos">
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Observed By</label>
              <input name="observed_by" type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="e.g., CI Gremio">
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Complications</label>
              <input name="complications" type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="e.g., None noted, minor bleeding, hypotension, etc.">
            </div>

            {{-- Optional IV / Infusion details --}}
            <div class="md:col-span-2 mt-2">
              <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-3 space-y-3">
                <p class="text-[11px] font-semibold text-slate-700">
                  IV / Medication Infusion Details (optional)
                </p>

                <div class="grid gap-3 md:grid-cols-2">
                  <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                      IV Fluid / Medication
                    </label>
                    <input id="treatIvName" name="iv_med_name" type="text"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                           placeholder="e.g., D5LR 1L, Ceftriaxone 1g in 100 mL NSS">
                  </div>
                  <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                      Volume to Infuse (mL)
                    </label>
                    <input id="treatVolumeMl" name="iv_volume_ml" type="number" min="0"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                           placeholder="e.g., 1000">
                  </div>
                </div>

                <div class="grid gap-3 md:grid-cols-3 mt-1">
                  <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                      Duration (hours)
                    </label>
                    <input id="treatDurationHr" name="iv_duration_hours" type="number" step="0.1" min="0"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                           placeholder="e.g., 8">
                  </div>
                  <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                      Pump Rate (mL/hr)
                    </label>
                    <input id="treatRateMlHr" name="iv_rate_ml_per_hr" type="number" step="0.1" min="0"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                           placeholder="auto / calculated">
                  </div>
                  <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                      Drip Rate (gtt/min)
                    </label>
                    <input id="treatDripRate" name="iv_drip_rate_gtt_per_min" type="number" step="0.1" min="0"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                           placeholder="auto / calculated">
                  </div>
                </div>

                <div class="grid gap-3 md:grid-cols-2">
                  <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                      Drop Factor (gtt/mL)
                    </label>
                    <input id="treatDropFactor" name="iv_drop_factor_gtt_per_ml" type="number" step="1" min="1"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                           placeholder="e.g., 15, 20, 60">
                  </div>
                  <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                      Site / Line
                    </label>
                    <input name="iv_site" type="text"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                           placeholder="e.g., Right forearm, patent; no signs of infiltration">
                  </div>
                </div>
              </div>
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Remarks</label>
              <textarea id="treatRemarks" name="remarks" rows="2"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Other notes (teaching given, patient response, MD notified, etc.)"></textarea>
            </div>
          </div>
        </div>

        {{-- RIGHT: IV FLOW RATE CALCULATOR --}}
        <aside class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 space-y-3">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-sm font-semibold text-slate-900">IV Flow Rate Calculator</h4>
              <p class="text-[11px] text-slate-500">
                Computes pump rate (mL/hr) and gravity drip rate (gtt/min).
              </p>
            </div>
            <i data-lucide="droplet" class="h-4 w-4 text-slate-400"></i>
          </div>

          <div class="space-y-3 text-xs">
            <div class="grid gap-3 md:grid-cols-2">
              <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">
                  Volume to infuse (mL)
                </label>
                <input id="calcIvVolume" type="number" min="0" step="0.1"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs"
                       placeholder="e.g., 1000">
              </div>
              <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">
                  Time (hours)
                </label>
                <input id="calcIvTimeHr" type="number" min="0" step="0.1"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs"
                       placeholder="e.g., 8">
              </div>
            </div>

            <div>
              <label class="block text-[11px] font-semibold text-slate-600 mb-1">
                Drop Factor (gtt/mL)
              </label>
              <input id="calcIvDropFactor" type="number" min="1" step="1"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs"
                     placeholder="e.g., 15, 20, 60">
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div class="rounded-xl bg-white border border-emerald-100 px-3 py-2">
                <p class="text-[11px] font-semibold text-emerald-700">Pump Rate</p>
                <p id="calcIvRateMlHr" class="mt-1 text-lg font-semibold text-emerald-900">— mL/hr</p>
              </div>
              <div class="rounded-xl bg-white border border-sky-100 px-3 py-2">
                <p class="text-[11px] font-semibold text-sky-700">Drip Rate</p>
                <p id="calcIvRateGttMin" class="mt-1 text-lg font-semibold text-sky-900">— gtt/min</p>
              </div>
            </div>

            <div class="flex flex-wrap gap-2 pt-1">
              <button type="button"
                      id="btnTreatApplyIv"
                      class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-1.5 text-[11px] font-medium text-white hover:bg-emerald-700">
                Use values in IV fields
              </button>
              <button type="button"
                      id="btnTreatAppendIv"
                      class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-[11px] font-medium text-slate-700 hover:bg-slate-100">
                Append note to Remarks
              </button>
            </div>

            <p class="text-[10px] text-slate-400">
              Pump rate = Volume ÷ Time. Drip rate = (Volume × Drop factor) ÷ (Time × 60).
            </p>
          </div>
        </aside>
      </div>

      <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-200">
        <button type="button"
                class="rounded-lg px-5 py-2 text-sm border border-slate-300 text-slate-700 hover:bg-slate-100"
                data-modal-close>
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

{{-- Inline script: IV rate calculator + auto-fill into form --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Calculator elements
    const calcVolume    = document.getElementById('calcIvVolume');
    const calcTimeHr    = document.getElementById('calcIvTimeHr');
    const calcDropFact  = document.getElementById('calcIvDropFactor');
    const calcRateMlHr  = document.getElementById('calcIvRateMlHr');
    const calcRateGtt   = document.getElementById('calcIvRateGttMin');

    // Form fields to sync to
    const fldVolumeMl   = document.getElementById('treatVolumeMl');
    const fldDurationHr = document.getElementById('treatDurationHr');
    const fldRateMlHr   = document.getElementById('treatRateMlHr');
    const fldDripRate   = document.getElementById('treatDripRate');
    const fldDropFactor = document.getElementById('treatDropFactor');
    const fldRemarks    = document.getElementById('treatRemarks');
    const fldIvName     = document.getElementById('treatIvName');

    const btnApply      = document.getElementById('btnTreatApplyIv');
    const btnAppend     = document.getElementById('btnTreatAppendIv');

    if (!calcVolume || !calcTimeHr || !calcRateMlHr) return;

    function num(v) {
      const n = parseFloat(v);
      return isNaN(n) ? 0 : n;
    }

    function computeIv() {
      const V = num(calcVolume.value);
      const T = num(calcTimeHr.value);
      const DF = num(calcDropFact.value);

      let pumpText = '— mL/hr';
      let dripText = '— gtt/min';

      if (V > 0 && T > 0) {
        const rateMlHr = V / T;
        pumpText = rateMlHr.toFixed(1) + ' mL/hr';

        if (DF > 0) {
          const gttMin = (V * DF) / (T * 60);
          dripText = gttMin.toFixed(1) + ' gtt/min';
        }
      }

      calcRateMlHr.textContent = pumpText;
      calcRateGtt.textContent  = dripText;
    }

    ['input', 'change'].forEach(evt => {
      calcVolume.addEventListener(evt, computeIv);
      calcTimeHr.addEventListener(evt, computeIv);
      calcDropFact.addEventListener(evt, computeIv);
    });
    computeIv();

    // Apply calculator values into the IV fields on the left
    btnApply?.addEventListener('click', function () {
      const V = num(calcVolume.value);
      const T = num(calcTimeHr.value);
      const DF = num(calcDropFact.value);

      if (fldVolumeMl && V > 0)   fldVolumeMl.value   = V.toFixed(1);
      if (fldDurationHr && T > 0) fldDurationHr.value = T.toFixed(1);
      if (fldDropFactor && DF > 0) fldDropFactor.value = DF.toFixed(0);

      // Parse displayed results for pump & drip
      const pumpText = calcRateMlHr.textContent || '';
      const dripText = calcRateGtt.textContent || '';

      const pumpVal = parseFloat(pumpText) || 0;
      const dripVal = parseFloat(dripText) || 0;

      if (fldRateMlHr && pumpVal > 0)  fldRateMlHr.value  = pumpVal.toFixed(1);
      if (fldDripRate && dripVal > 0)  fldDripRate.value  = dripVal.toFixed(1);
    });

    // Append a clean infusion note to Remarks
    btnAppend?.addEventListener('click', function () {
      if (!fldRemarks) return;

      const med  = fldIvName && fldIvName.value ? fldIvName.value.trim() : 'IV fluid';
      const V    = calcVolume.value || '';
      const T    = calcTimeHr.value || '';
      const pump = calcRateMlHr.textContent || '';
      const drip = calcRateGtt.textContent || '';
      const DF   = calcDropFact.value || '';

      let parts = [];
      if (V && T)     parts.push(`${V} mL over ${T} hr`);
      if (pump && !pump.startsWith('—')) parts.push(pump);
      if (drip && !drip.startsWith('—')) {
        if (DF) {
          parts.push(`${drip} (DF ${DF} gtt/mL)`);
        } else {
          parts.push(drip);
        }
      }

      if (!parts.length) return;

      const note = `IV infusion: ${med} at ${parts.join(', ')}.`;

      if (!fldRemarks.value) {
        fldRemarks.value = note;
      } else {
        fldRemarks.value = fldRemarks.value.trim() + '\n' + note;
      }
    });
  });
</script>