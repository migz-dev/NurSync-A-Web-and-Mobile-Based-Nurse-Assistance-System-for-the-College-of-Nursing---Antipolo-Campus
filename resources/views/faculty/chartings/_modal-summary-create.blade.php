{{-- resources/views/faculty/chartings/_modal-summary-create.blade.php --}}
<div id="modalCreateSummary" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-modal-close></div>

  <div class="relative mx-auto my-10 w-[95%] max-w-5xl rounded-2xl bg-white shadow-2xl border border-slate-200">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-2xl">
      <h3 class="text-lg font-bold text-slate-900">New Daily Progress / Summary</h3>
      <button type="button"
              class="h-9 w-9 inline-flex items-center justify-center rounded-lg hover:bg-slate-100"
              data-modal-close>
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    <form method="POST"
          action="{{ route('faculty.chartings.summary.store', $patient->id) }}"
          class="p-6 space-y-5">
      @csrf

      {{-- 2-column layout: left = summary form, right = helper panel --}}
      <div class="grid gap-6 md:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
        {{-- LEFT: SUMMARY FORM --}}
        <div>
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Logged At *</label>
              <input name="logged_at"
                     type="datetime-local"
                     required
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Author</label>
              <input name="author"
                     type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="e.g., RN Santos">
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Progress Notes</label>
              <textarea id="summaryProgressNotes"
                        name="progress_notes"
                        rows="8"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Daily overview of patient status, response to treatment, and plan. You can use the helper panel on the right for quick templates."></textarea>
            </div>
          </div>
        </div>

        {{-- RIGHT: DAILY SUMMARY HELPER PANEL --}}
        <aside class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-sm font-semibold text-slate-900">Daily Summary Helper</h4>
              <p class="text-[11px] text-slate-500">
                Build a 1–2 paragraph progress note using vitals, I&amp;O, meds, pain, mobility, and plan.
              </p>
            </div>
            <i data-lucide="notepad-text" class="h-4 w-4 text-slate-400"></i>
          </div>

          {{-- Full-day templates --}}
          <div class="space-y-2">
            <p class="text-[11px] font-semibold text-slate-600">Full-day templates</p>
            <div class="flex flex-wrap gap-2">
              <button type="button"
                      id="btnSummaryStableDay"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="shield-check" class="mr-1.5 h-3.5 w-3.5"></i>
                Stable day
              </button>
              <button type="button"
                      id="btnSummaryImprovingDay"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="trending-up" class="mr-1.5 h-3.5 w-3.5"></i>
                Improving day
              </button>
              <button type="button"
                      id="btnSummaryWorseningDay"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="trending-down" class="mr-1.5 h-3.5 w-3.5"></i>
                Worsening / concern
              </button>
            </div>
          </div>

          <hr class="border-dashed border-slate-200">

          {{-- Snippets by domain --}}
          <div class="space-y-2">
            <p class="text-[11px] font-semibold text-slate-600">Add snippets</p>
            <div class="flex flex-wrap gap-2">
              <button type="button"
                      id="btnSummaryVitals"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="activity" class="mr-1.5 h-3.5 w-3.5"></i>
                Vitals &amp; pain
              </button>
              <button type="button"
                      id="btnSummaryIO"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="droplets" class="mr-1.5 h-3.5 w-3.5"></i>
                I&amp;O / fluid
              </button>
              <button type="button"
                      id="btnSummaryMeds"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="pill" class="mr-1.5 h-3.5 w-3.5"></i>
                Meds &amp; response
              </button>
              <button type="button"
                      id="btnSummaryMobility"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="person-standing" class="mr-1.5 h-3.5 w-3.5"></i>
                Mobility / function
              </button>
              <button type="button"
                      id="btnSummaryPlan"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="list-checks" class="mr-1.5 h-3.5 w-3.5"></i>
                Plan for next 24h
              </button>
            </div>
            <p class="text-[10px] text-slate-400">
              Pull actual numbers from Vitals, I&amp;O, MAR, NCP, and Treatment calculators, then drop them into these templates.
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

{{-- Inline script: Daily summary helper templates --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const notesEl = document.getElementById('summaryProgressNotes');
    if (!notesEl) return;

    function append(text, replace = false) {
      if (replace || !notesEl.value.trim()) {
        notesEl.value = text;
      } else {
        notesEl.value = notesEl.value.trim() + '\\n\\n' + text;
      }
      notesEl.scrollTop = notesEl.scrollHeight;
      notesEl.focus();
    }

    // Full-day templates
    document.getElementById('btnSummaryStableDay')?.addEventListener('click', function () {
      const t =
`Patient remained generally stable throughout the shift/day. VS within acceptable range for age (T __ °C, HR __ bpm, RR __ cpm, BP __ / __ mmHg, SpO₂ __% on __), pain controlled at __/10 with prescribed regimen. 
No acute events reported. Appetite __, oral intake __, urine output adequate at __ mL/kg/hr (see I&O). Ambulates/turns with __ assistance. Will continue current plan of care and monitoring.`;
      append(t, true);
    });

    document.getElementById('btnSummaryImprovingDay')?.addEventListener('click', function () {
      const t =
`Patient shows improvement compared to previous shift/day. VS trending towards baseline; current VS: T __ °C, HR __ bpm, RR __ cpm, BP __ / __ mmHg, SpO₂ __% on __. Pain decreased from __/10 to __/10 after interventions. 
Respiratory effort __, lung sounds __; I&O close to balanced with net __ mL (see I&O). Mobility improved from __ to __. Patient and family verbalize better understanding of condition and treatment plan. Continue to progress current interventions and reassess response.`;
      append(t, true);
    });

    document.getElementById('btnSummaryWorseningDay')?.addEventListener('click', function () {
      const t =
`Noted concerning changes this shift/day. VS: T __ °C, HR __ bpm (baseline __), RR __ cpm, BP __ / __ mmHg, SpO₂ __% on __; pain increased from __/10 to __/10 despite interventions. 
Respiratory status: __; I&O net __ mL suggesting __ (deficit/overload). Appetite __, activity tolerance decreased. MD/CI notified at __; new orders: __. Close monitoring and follow-up required next shift.`;
      append(t, true);
    });

    // Domain snippets
    document.getElementById('btnSummaryVitals')?.addEventListener('click', function () {
      const t =
`VS/Pain: T __ °C, HR __ bpm, RR __ cpm, BP __ / __ mmHg, SpO₂ __% on __. Pain score __/10 located at __, relieved by __ with effect __. No complaints of dizziness/chest pain/SOB unless stated: __.`;
      append(t);
    });

    document.getElementById('btnSummaryIO')?.addEventListener('click', function () {
      const t =
`Fluid & I&O: Intake __ mL (Oral __ / IV __ / NG __), Output __ mL (Urine __ / Stool __ / Emesis __ / Drain __); Net __ mL this shift/day. Urine output __ mL/kg/hr, urine color __. No significant edema or signs of dehydration / __.`;
      append(t);
    });

    document.getElementById('btnSummaryMeds')?.addEventListener('click', function () {
      const t =
`Medications & response: Scheduled meds given as ordered except __ (reason: __). PRN meds: __ at __ for __ with effect __. High-alert/IV meds double-checked and infused at calculated rates (see MAR & IV calculator). No adverse reactions observed / __.`;
      append(t);
    });

    document.getElementById('btnSummaryMobility')?.addEventListener('click', function () {
      const t =
`Mobility & function: Patient is __ (bedbound/with assist/independent). Able to __ (sit, stand, ambulate __ meters with __ assist). ADLs performed with __ assistance. Turning q2h maintained / partially maintained; skin over bony prominences __.`;
      append(t);
    });

    document.getElementById('btnSummaryPlan')?.addEventListener('click', function () {
      const t =
`Plan for next 24 hours: Continue monitoring VS and pain q__ hrs; maintain I&O monitoring with target urine output ≥ 0.5 mL/kg/hr. Continue current meds/IV fluids, adjust per MD orders and response. Reinforce teaching on __, encourage mobilization as tolerated, and prepare for possible __ (procedure/transfer/discharge) as ordered.`;
      append(t);
    });
  });
</script>
