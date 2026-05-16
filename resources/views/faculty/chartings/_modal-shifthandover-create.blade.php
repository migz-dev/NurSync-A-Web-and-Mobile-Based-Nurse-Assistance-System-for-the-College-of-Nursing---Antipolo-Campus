{{-- resources/views/faculty/chartings/_modal-shift-create.blade.php --}}
<div id="modalCreateShift" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-modal-close></div>
  <div class="relative mx-auto my-10 w-[95%] max-w-5xl rounded-2xl bg-white shadow-2xl border border-slate-200">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-2xl">
      <h3 class="text-lg font-bold text-slate-900">New Shift Handover</h3>
      <button type="button"
              class="h-9 w-9 inline-flex items-center justify-center rounded-lg hover:bg-slate-100"
              data-modal-close>
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    <form method="POST"
          action="{{ route('faculty.chartings.shift.store', $patient->id) }}"
          class="p-6 space-y-5">
      @csrf

      {{-- 2-column layout: left = handover form, right = helper panel --}}
      <div class="grid gap-6 md:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
        {{-- LEFT: SHIFT HANDOVER FORM --}}
        <div>
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Handover Time *</label>
              <input name="handed_over_at"
                     type="datetime-local"
                     required
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Shift</label>
              <select name="shift"
                      class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="AM">AM</option>
                <option value="PM">PM</option>
                <option value="NOC">Night</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">From Nurse</label>
              <input name="from_nurse" type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="e.g., RN Santos">
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">To Nurse</label>
              <input name="to_nurse" type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="e.g., RN Dela Cruz">
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Summary</label>
              <textarea id="shiftSummary"
                        name="summary"
                        rows="3"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Brief SBAR-style summary of patient condition and events this shift."></textarea>
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Pending Orders / Tasks</label>
              <textarea id="shiftPending"
                        name="pending_orders"
                        rows="3"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Outstanding labs, meds, procedures, referrals, teaching, follow-up."></textarea>
            </div>
          </div>
        </div>

        {{-- RIGHT: SHIFT HANDOVER HELPER PANEL --}}
        <aside class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-sm font-semibold text-slate-900">Handover Helper Panel</h4>
              <p class="text-[11px] text-slate-500">
                Build a quick SBAR-style handover using vitals, I&amp;O, meds, and IV info.
              </p>
            </div>
            <i data-lucide="hand-heart" class="h-4 w-4 text-slate-400"></i>
          </div>

          {{-- SBAR skeleton --}}
          <div class="space-y-2">
            <p class="text-[11px] font-semibold text-slate-600">SBAR skeleton</p>
            <button type="button"
                    id="btnShiftInsertSBAR"
                    class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
              <i data-lucide="clipboard-list" class="mr-1.5 h-3.5 w-3.5"></i>
              Insert SBAR template
            </button>
          </div>

          <hr class="border-dashed border-slate-200">

          {{-- Summary snippets (from calculators) --}}
          <div class="space-y-2">
            <p class="text-[11px] font-semibold text-slate-600">Summary snippets</p>
            <div class="flex flex-wrap gap-2">
              <button type="button"
                      id="btnShiftVitals"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="activity" class="mr-1.5 h-3.5 w-3.5"></i>
                Vitals &amp; pain
              </button>
              <button type="button"
                      id="btnShiftIO"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="droplets" class="mr-1.5 h-3.5 w-3.5"></i>
                I&amp;O / fluid balance
              </button>
              <button type="button"
                      id="btnShiftMeds"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="pill" class="mr-1.5 h-3.5 w-3.5"></i>
                Meds given / due
              </button>
              <button type="button"
                      id="btnShiftIV"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="droplet" class="mr-1.5 h-3.5 w-3.5"></i>
                IV infusions running
              </button>
              <button type="button"
                      id="btnShiftRisks"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="alert-triangle" class="mr-1.5 h-3.5 w-3.5"></i>
                Risks &amp; precautions
              </button>
            </div>
            <p class="text-[10px] text-slate-400">
              Use outputs from Vitals, I&amp;O, MAR, and Treatment calculators to fill in the blanks.
            </p>
          </div>

          <hr class="border-dashed border-slate-200">

          {{-- Pending tasks helper --}}
          <div class="space-y-2">
            <p class="text-[11px] font-semibold text-slate-600">Pending tasks patterns</p>
            <div class="flex flex-wrap gap-2">
              <button type="button"
                      id="btnShiftPendingLabs"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="beaker" class="mr-1.5 h-3.5 w-3.5"></i>
                Labs / diagnostics
              </button>
              <button type="button"
                      id="btnShiftPendingMeds"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="clock-4" class="mr-1.5 h-3.5 w-3.5"></i>
                Meds &amp; IV tasks
              </button>
              <button type="button"
                      id="btnShiftPendingNursing"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="clipboard-pen" class="mr-1.5 h-3.5 w-3.5"></i>
                Nursing / teaching
              </button>
            </div>
          </div>
        </aside>
      </div>

      <div class="flex items-center justify-end gap-2 pt-4 border-top border-slate-200">
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

{{-- Inline script: Shift handover helper templates --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const summaryEl = document.getElementById('shiftSummary');
    const pendingEl = document.getElementById('shiftPending');

    function appendTo(el, text) {
      if (!el) return;
      const current = el.value.trim();
      if (!current) {
        el.value = text;
      } else {
        el.value = current + '\\n\\n' + text;
      }
      el.scrollTop = el.scrollHeight;
      el.focus();
    }

    // SBAR skeleton
    document.getElementById('btnShiftInsertSBAR')?.addEventListener('click', function () {
      const t =
`S (Situation): __ (Age/sex, main diagnosis, current condition).
B (Background): Admitted on __ for __. Significant history: __. Allergies: __.
A (Assessment): VS: T __ °C, HR __ bpm, RR __ cpm, BP __ / __ mmHg, SpO₂ __% on __. Pain __/10. I&O: Intake __ mL, Output __ mL, Net __ mL this shift. On __ IV fluids at __ mL/hr, meds given as ordered / exceptions: __.
R (Recommendation): Continue monitoring __, follow up on labs __, prepare for __ (procedure/transfer/discharge).`;
      appendTo(summaryEl, t);
    });

    // Summary snippets
    document.getElementById('btnShiftVitals')?.addEventListener('click', function () {
      const t =
`Vitals this shift: T __ °C, HR __ bpm, RR __ cpm, BP __ / __ mmHg, SpO₂ __% on __, Pain __/10. Patient is in __ distress / comfortable; mental status __.`;
      appendTo(summaryEl, t);
    });

    document.getElementById('btnShiftIO')?.addEventListener('click', function () {
      const t =
`I&O this shift: Intake __ mL (Oral __ / IV __ / NG __), Output __ mL (Urine __ / Stool __ / Emesis __ / Drain __); Net __ mL. No signs of dehydration/fluid overload / __.`;
      appendTo(summaryEl, t);
    });

    document.getElementById('btnShiftMeds')?.addEventListener('click', function () {
      const t =
`Meds this shift: __ (given as ordered), PRN meds given: __ with effect __. Next due meds at __. Held/missed doses: __ (reason: __).`;
      appendTo(summaryEl, t);
    });

    document.getElementById('btnShiftIV')?.addEventListener('click', function () {
      const t =
`IV infusions: __ mL of __ via __ line at __ mL/hr (≈ __ gtt/min, DF __ gtt/mL). Site __ (patent/intact/redness/infiltration), last checked at __.`;
      appendTo(summaryEl, t);
    });

    document.getElementById('btnShiftRisks')?.addEventListener('click', function () {
      const t =
`Risks / precautions: Fall risk (yes/no: __), Aspiration risk (yes/no: __), Skin breakdown risk (yes/no: __). Bed in low position, rails __, call light within reach, family instructed re: __.`;
      appendTo(summaryEl, t);
    });

    // Pending tasks patterns
    document.getElementById('btnShiftPendingLabs')?.addEventListener('click', function () {
      const t =
`Labs/diagnostics pending:
• __ (specimen to be collected at __)
• __ (result to be followed up at __)
• Imaging: __ (scheduled at __).`;
      appendTo(pendingEl, t);
    });

    document.getElementById('btnShiftPendingMeds')?.addEventListener('click', function () {
      const t =
`Meds/IV tasks:
• Next scheduled meds at __ (__, __).
• Titrate/monitor IV __ at __ mL/hr; reassess site at __.
• Check blood glucose / parameters before giving __.`;
      appendTo(pendingEl, t);
    });

    document.getElementById('btnShiftPendingNursing')?.addEventListener('click', function () {
      const t =
`Nursing care / teaching:
• Turn/reposition every __ hours; skin check on __ areas.
• Continue pain management plan; reassess pain at __.
• Complete health teaching on __ before end of shift.
• Prepare patient for __ (procedure/transfer/discharge) by __.`;
      appendTo(pendingEl, t);
    });
  });
</script>
