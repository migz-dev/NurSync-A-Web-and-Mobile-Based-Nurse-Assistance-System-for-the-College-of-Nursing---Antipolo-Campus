{{-- resources/views/faculty/chartings/_modal-ncp-create.blade.php --}}
<div id="modalCreateNCP" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-modal-close></div>

  <div class="relative mx-auto my-10 w-[95%] max-w-5xl rounded-2xl bg-white shadow-2xl border border-slate-200">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-2xl">
      <h3 class="text-lg font-bold text-slate-900">New Nursing Care Plan</h3>
      <button type="button" class="h-9 w-9 inline-flex items-center justify-center rounded-lg hover:bg-slate-100" data-modal-close>
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    <form method="POST"
          action="{{ route('faculty.chartings.ncp.store', $patient->id) }}"
          class="p-6 space-y-5">
      @csrf

      {{-- 2-column layout: left = NCP form, right = helpers --}}
      <div class="grid gap-6 md:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
        {{-- LEFT: NCP FORM --}}
        <div>
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Started At *</label>
              <input name="started_at"
                     type="date"
                     required
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
              <select name="status"
                      class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="ongoing">Ongoing</option>
                <option value="met">Met</option>
                <option value="revised">Revised</option>
                <option value="discontinued">Discontinued</option>
              </select>
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">
                Nursing Diagnosis (primary) *
              </label>
              <input name="dx_primary" required type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="e.g., Acute pain r/t tissue trauma AEB grimacing, pain scale 6/10">
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Related to</label>
              <input name="dx_related_to" type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="r/t ...">
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">As evidenced by</label>
              <input name="dx_as_evidenced_by" type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="AEB ...">
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">
                Goals / Expected Outcomes
              </label>
              <textarea id="ncpGoals"
                        name="goals"
                        rows="2"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="State SMART goals aligned with the diagnosis."></textarea>
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">
                Nursing Interventions
              </label>
              <textarea id="ncpInterventions"
                        name="interventions"
                        rows="3"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="List independent, dependent, and collaborative interventions."></textarea>
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">
                Outcomes / Evaluation
              </label>
              <textarea id="ncpOutcomes"
                        name="outcomes_evaluation"
                        rows="2"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Document if goals were met/partially met/not met and supporting data."></textarea>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Reviewed At</label>
              <input name="reviewed_at"
                     type="date"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
          </div>
        </div>

        {{-- RIGHT: HELPER PANEL (uses outputs from calculators) --}}
        <aside class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-sm font-semibold text-slate-900">NCP Helper Panel</h4>
              <p class="text-[11px] text-slate-500">
                Insert ready-made goals, interventions, and evaluation lines based on vitals, I&amp;O, meds, and pain.
              </p>
            </div>
            <i data-lucide="clipboard-list" class="h-4 w-4 text-slate-400"></i>
          </div>

          {{-- Goals templates --}}
          <div class="space-y-2">
            <p class="text-[11px] font-semibold text-slate-600">Goals / Outcomes templates</p>
            <div class="flex flex-wrap gap-2">
              <button type="button"
                      id="btnNcpGoalPain"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="activity" class="mr-1.5 h-3.5 w-3.5"></i>
                Pain management goal
              </button>
              <button type="button"
                      id="btnNcpGoalFluid"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="droplets" class="mr-1.5 h-3.5 w-3.5"></i>
                Fluid balance goal
              </button>
              <button type="button"
                      id="btnNcpGoalVitals"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="heart-pulse" class="mr-1.5 h-3.5 w-3.5"></i>
                Vitals stability goal
              </button>
            </div>
          </div>

          <hr class="border-dashed border-slate-200">

          {{-- Interventions templates --}}
          <div class="space-y-2">
            <p class="text-[11px] font-semibold text-slate-600">Interventions templates</p>
            <div class="flex flex-wrap gap-2">
              <button type="button"
                      id="btnNcpInterventionsPain"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="smile-plus" class="mr-1.5 h-3.5 w-3.5"></i>
                Pain interventions
              </button>
              <button type="button"
                      id="btnNcpInterventionsFluid"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="beaker" class="mr-1.5 h-3.5 w-3.5"></i>
                Fluid &amp; I&amp;O interventions
              </button>
              <button type="button"
                      id="btnNcpInterventionsMeds"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="pill" class="mr-1.5 h-3.5 w-3.5"></i>
                Medication safety
              </button>
            </div>
          </div>

          <hr class="border-dashed border-slate-200">

          {{-- Outcomes/Evaluation templates --}}
          <div class="space-y-2">
            <p class="text-[11px] font-semibold text-slate-600">Evaluation templates</p>
            <div class="flex flex-wrap gap-2">
              <button type="button"
                      id="btnNcpOutcomePain"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="check-circle-2" class="mr-1.5 h-3.5 w-3.5"></i>
                Pain outcome
              </button>
              <button type="button"
                      id="btnNcpOutcomeFluid"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="waves" class="mr-1.5 h-3.5 w-3.5"></i>
                Fluid balance outcome
              </button>
              <button type="button"
                      id="btnNcpOutcomeVitals"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="stethoscope" class="mr-1.5 h-3.5 w-3.5"></i>
                Vitals outcome
              </button>
            </div>
          </div>

          <p class="text-[10px] text-slate-400">
            Use the calculators in Vitals, I&amp;O, MAR, and Treatment to fill in actual values
            (pain score, BMI, net I&amp;O, IV rates), then update these templates accordingly.
          </p>
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

{{-- Inline script: NCP helper templates --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const goalsEl        = document.getElementById('ncpGoals');
    const interventionsEl = document.getElementById('ncpInterventions');
    const outcomesEl     = document.getElementById('ncpOutcomes');

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

    // Goals buttons
    document.getElementById('btnNcpGoalPain')?.addEventListener('click', function () {
      const t =
`Within __ hours of nursing care, patient will report pain ≤ __/10, with relaxed facial expression and ability to perform ADLs as tolerated.`;
      appendTo(goalsEl, t);
    });

    document.getElementById('btnNcpGoalFluid')?.addEventListener('click', function () {
      const t =
`Within 24 hours, patient will maintain balanced fluid volume as evidenced by net I&O between -__ mL and +__ mL, stable vital signs, moist mucous membranes, and adequate urine output (≥ 0.5 mL/kg/hr).`;
      appendTo(goalsEl, t);
    });

    document.getElementById('btnNcpGoalVitals')?.addEventListener('click', function () {
      const t =
`Within __ hours, patient will maintain vital signs within acceptable range (T __–__ °C, HR __–__ bpm, RR __–__ cpm, BP __–__ mmHg, SpO₂ ≥ __%) and report no difficulty in breathing or chest pain.`;
      appendTo(goalsEl, t);
    });

    // Interventions buttons
    document.getElementById('btnNcpInterventionsPain')?.addEventListener('click', function () {
      const t =
`• Assess pain characteristics (location, intensity, quality, duration) using a standardized pain scale every __ hours. 
• Administer analgesics as ordered (refer to MAR and dosage calculator), monitoring for side effects. 
• Provide non-pharmacologic measures (positioning, relaxation, deep breathing, cold/heat as appropriate). 
• Educate patient on reporting pain early and using pain scale consistently. 
• Evaluate pain relief 30–60 minutes after intervention and document response.`;
      appendTo(interventionsEl, t);
    });

    document.getElementById('btnNcpInterventionsFluid')?.addEventListener('click', function () {
      const t =
`• Monitor intake & output accurately every __ hours; compute running net balance using I&O tool. 
• Assess for signs of fluid deficit/excess (mucous membranes, skin turgor, edema, lung sounds, weight changes). 
• Administer IV fluids as ordered at calculated rate (refer to IV flow calculator), verifying line patency and site. 
• Encourage oral intake as tolerated and explain fluid restrictions if ordered. 
• Collaborate with physician for lab monitoring (electrolytes, Hct/Hgb, BUN/Creatinine) and adjust plan as indicated.`;
      appendTo(interventionsEl, t);
    });

    document.getElementById('btnNcpInterventionsMeds')?.addEventListener('click', function () {
      const t =
`• Verify all medication orders against patient ID, allergies, and MAR (5 rights + 3 checks). 
• Use medication dosage calculator when preparing high-risk or weight-based drugs. 
• Observe patient for therapeutic and adverse effects; document responses in MAR and nurse’s notes. 
• Educate patient about purpose, schedule, and possible side effects of medications. 
• Coordinate with medical team for dose adjustments based on VS trends, labs, and patient response.`;
      appendTo(interventionsEl, t);
    });

    // Outcomes buttons
    document.getElementById('btnNcpOutcomePain')?.addEventListener('click', function () {
      const t =
`Goal evaluation (pain): Patient verbalized pain reduced from __/10 to __/10 after interventions; facial expression relaxed, able to perform ADLs with minimal discomfort. Goal __ (met/partially met/not met).`;
      appendTo(outcomesEl, t);
    });

    document.getElementById('btnNcpOutcomeFluid')?.addEventListener('click', function () {
      const t =
`Goal evaluation (fluid balance): Net I&O over last 24 hours is __ mL, vital signs stable, urine output adequate at __ mL/kg/hr, no signs of edema or dehydration. Goal __ (met/partially met/not met).`;
      appendTo(outcomesEl, t);
    });

    document.getElementById('btnNcpOutcomeVitals')?.addEventListener('click', function () {
      const t =
`Goal evaluation (vital signs): Current VS: T __ °C, HR __ bpm, RR __ cpm, BP __ / __ mmHg, SpO₂ __%. Patient denies dizziness, chest pain, or difficulty breathing. Goal __ (met/partially met/not met).`;
      appendTo(outcomesEl, t);
    });
  });
</script>
