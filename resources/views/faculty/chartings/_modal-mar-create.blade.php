{{-- resources/views/faculty/chartings/_modal-mar-create.blade.php --}}
<div id="modalCreateMAR" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-modal-close></div>

  <div class="relative mx-auto my-10 w-[95%] max-w-5xl rounded-2xl bg-white shadow-2xl border border-slate-200">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-2xl">
      <h3 class="text-lg font-bold text-slate-900">Add Medication Administration Record</h3>
      <button type="button" class="h-9 w-9 inline-flex items-center justify-center rounded-lg hover:bg-slate-100" data-modal-close>
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    <form method="POST"
          action="{{ route('faculty.chartings.mar.store', $patient->id) }}"
          class="p-6 space-y-5">
      @csrf

      {{-- 2-column layout: left = MAR form, right = calculator --}}
      <div class="grid gap-6 md:grid-cols-[minmax(0,1.25fr)_minmax(0,1fr)]">
        {{-- LEFT: MAR FORM --}}
        <div>
          <div class="grid gap-4 md:grid-cols-2">
            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Drug Name / Medication *</label>
              <input name="drug_name" required type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="e.g., Cefuroxime 750 mg IV">
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Dose</label>
              <input id="marDose" name="dose" type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="e.g., 750 mg, 5 mL, 1 tab">
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Route</label>
              <input name="route" type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="PO / IV / IM / Topical">
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Frequency</label>
              <input name="frequency" type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="q8h / OD / BID">
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Scheduled Time</label>
              <input name="scheduled_time" type="datetime-local"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Administered At</label>
              <input name="administered_at" type="datetime-local"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
              <select id="marStatus" name="status"
                      class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="Given">Given</option>
                <option value="Held">Held</option>
                <option value="Missed">Missed</option>
                <option value="Refused">Refused</option>
                <option value="Scheduled">Scheduled</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Given By</label>
              <input name="given_by" type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="e.g., CI Gremio">
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Indication</label>
              <input name="indication" type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="e.g., Surgical prophylaxis">
            </div>

            {{-- PRN + omission + effects (map to DB: is_prn, prn_reason, omitted_reason, effects) --}}
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">PRN?</label>
              <div class="flex items-center gap-2">
                {{-- hidden 0 so unchecked still submits --}}
                <input type="hidden" name="is_prn" value="0">
                <input id="marIsPrn" type="checkbox" name="is_prn" value="1"
                       class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <span class="text-xs text-slate-600">As needed (PRN)</span>
              </div>
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">PRN Reason</label>
              <input id="marPrnReason" name="prn_reason" type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="e.g., Pain &gt; 5/10"
                     disabled>
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Omitted Reason (if not given)</label>
              <input id="marOmittedReason" name="omitted_reason" type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="e.g., Patient refused, NPO, hypotensive"
                     disabled>
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Effects / Patient Response</label>
              <textarea name="effects" rows="2"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="e.g., Pain reduced from 8/10 to 3/10; no adverse reactions observed"></textarea>
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Remarks</label>
              <textarea id="marRemarks" name="remarks" rows="2"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Other notes (e.g., double-checked with co-nurse, MD notified, etc.)"></textarea>
            </div>
          </div>
        </div>

        {{-- RIGHT: MEDICATION DOSAGE CALCULATOR --}}
        <aside class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 space-y-3">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-sm font-semibold text-slate-900">Medication Dosage Calculator</h4>
              <p class="text-[11px] text-slate-500">Helps compute mL to draw / tablets to give.</p>
            </div>
            <i data-lucide="calculator" class="h-4 w-4 text-slate-400"></i>
          </div>

          <div class="space-y-3">
            <div>
              <label class="block text-[11px] font-semibold text-slate-600 mb-1">Ordered dose</label>
              <div class="flex gap-2">
                <input id="calcOrderedDose" type="number" step="0.01" min="0"
                       class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-xs"
                       placeholder="e.g., 500">
                <span class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-2 text-[11px] text-slate-700">
                  mg
                </span>
              </div>
            </div>

            <div>
              <label class="block text-[11px] font-semibold text-slate-600 mb-1">Stock strength</label>
              <div class="flex gap-2">
                <input id="calcStockStrength" type="number" step="0.01" min="0"
                       class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-xs"
                       placeholder="mg per tab / vial">
                <span class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-2 text-[11px] text-slate-700">
                  mg
                </span>
              </div>
            </div>

            <div>
              <label class="block text-[11px] font-semibold text-slate-600 mb-1">Volume per stock (if liquid)</label>
              <div class="flex gap-2">
                <input id="calcVolumePerStock" type="number" step="0.01" min="0"
                       class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-xs"
                       placeholder="e.g., 5">
                <span class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-2 text-[11px] text-slate-700">
                  mL
                </span>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
              <div>
                <p class="text-[11px] font-semibold text-slate-500">Result – Liquid draw</p>
                <p id="calcResultLiquid" class="mt-1 text-base font-semibold text-slate-900">— mL</p>
              </div>
              <div>
                <p class="text-[11px] font-semibold text-slate-500">Result – Tablets</p>
                <p id="calcResultTabs" class="mt-1 text-base font-semibold text-slate-900">— tabs</p>
              </div>
            </div>

            <div class="flex flex-wrap gap-2 pt-1">
              <button type="button"
                      id="btnApplyDoseToField"
                      class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-1.5 text-[11px] font-medium text-white hover:bg-emerald-700">
                Use result in Dose
              </button>
              <button type="button"
                      id="btnAppendDoseToRemarks"
                      class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-[11px] font-medium text-slate-700 hover:bg-slate-100">
                Append note to Remarks
              </button>
            </div>

            <p class="text-[10px] text-slate-400">
              Formula: mL = (Ordered ÷ Stock) × Volume. Tablets = Ordered ÷ Stock.
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

{{-- Simple inline script for calculator + auto-fill + PRN/omitted logic --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const orderedEl = document.getElementById('calcOrderedDose');
    const stockEl   = document.getElementById('calcStockStrength');
    const volEl     = document.getElementById('calcVolumePerStock');
    const resLiquid = document.getElementById('calcResultLiquid');
    const resTabs   = document.getElementById('calcResultTabs');

    const marDose       = document.getElementById('marDose');
    const marRemarks    = document.getElementById('marRemarks');
    const marStatus     = document.getElementById('marStatus');
    const marIsPrn      = document.getElementById('marIsPrn');
    const marPrnReason  = document.getElementById('marPrnReason');
    const marOmitted    = document.getElementById('marOmittedReason');

    const btnApply   = document.getElementById('btnApplyDoseToField');
    const btnAppend  = document.getElementById('btnAppendDoseToRemarks');

    if (!orderedEl || !stockEl || !resLiquid) return;

    function compute() {
      const ordered = parseFloat(orderedEl.value) || 0;
      const stock   = parseFloat(stockEl.value)   || 0;
      const vol     = parseFloat(volEl.value)     || 0;

      let liquidText = '— mL';
      let tabsText   = '— tabs';

      if (ordered > 0 && stock > 0 && vol > 0) {
        const mL = (ordered / stock) * vol;
        liquidText = mL.toFixed(2) + ' mL';
      }
      if (ordered > 0 && stock > 0) {
        const tabs = ordered / stock;
        const tabsFormatted = Number.isInteger(tabs) ? tabs.toFixed(0) : tabs.toFixed(2);
        tabsText = tabsFormatted + ' tabs';
      }

      resLiquid.textContent = liquidText;
      resTabs.textContent   = tabsText;
    }

    ['input', 'change'].forEach(evt => {
      orderedEl.addEventListener(evt, compute);
      stockEl.addEventListener(evt, compute);
      volEl.addEventListener(evt, compute);
    });
    compute();

    btnApply?.addEventListener('click', function () {
      if (!marDose) return;
      const ordered = orderedEl.value ? orderedEl.value + ' mg' : '';
      const liquid  = resLiquid.textContent || '';
      const tabs    = resTabs.textContent || '';

      let pieces = [];
      if (ordered) pieces.push(ordered);
      if (liquid && !liquid.startsWith('—')) pieces.push(liquid);
      if (tabs && !tabs.startsWith('—')) pieces.push(tabs);

      if (pieces.length) {
        marDose.value = pieces.join(' • ');
      }
    });

    btnAppend?.addEventListener('click', function () {
      if (!marRemarks) return;

      const noteParts = [];
      if (!resLiquid.textContent.startsWith('—')) noteParts.push('Draw ' + resLiquid.textContent);
      if (!resTabs.textContent.startsWith('—')) noteParts.push('Give ' + resTabs.textContent);

      if (!noteParts.length) return;

      const note = 'Calculated dose: ' + noteParts.join(', ') + '.';
      if (!marRemarks.value) {
        marRemarks.value = note;
      } else {
        marRemarks.value = marRemarks.value.trim() + '\n' + note;
      }
    });

    // --- PRN toggle: enable/disable PRN reason ---
    function syncPrn() {
      if (!marIsPrn || !marPrnReason) return;
      const isChecked = marIsPrn.checked;
      marPrnReason.disabled = !isChecked;
      if (!isChecked) marPrnReason.value = '';
    }
    marIsPrn?.addEventListener('change', syncPrn);
    syncPrn();

    // --- Status → omitted reason toggle (for Held/Missed/Refused) ---
    function syncOmitted() {
      if (!marStatus || !marOmitted) return;
      const v = marStatus.value;
      const needsReason = (v === 'Held' || v === 'Missed' || v === 'Refused');
      marOmitted.disabled = !needsReason;
      if (!needsReason) marOmitted.value = '';
    }
    marStatus?.addEventListener('change', syncOmitted);
    syncOmitted();
  });
</script>
