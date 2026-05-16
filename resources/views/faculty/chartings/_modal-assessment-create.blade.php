{{-- resources/views/faculty/chartings/_modal-assessment-create.blade.php --}}
<div id="modalCreateAssessment" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-modal-close></div>
  <div class="relative mx-auto my-10 w-[95%] max-w-5xl rounded-2xl bg-white shadow-2xl border border-slate-200">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-2xl">
      <h3 class="text-lg font-bold text-slate-900">New Patient Assessment</h3>
      <button type="button"
              class="h-9 w-9 inline-flex items-center justify-center rounded-lg hover:bg-slate-100"
              data-modal-close>
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    <form method="POST"
          action="{{ route('faculty.chartings.assessment.store', $patient->id) }}"
          class="p-6 space-y-5">
      @csrf

      {{-- 2-column layout: left = assessment form, right = helper panel --}}
      <div class="grid gap-6 md:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
        {{-- LEFT: ASSESSMENT FORM --}}
        <div>
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Assessed At *</label>
              <input name="assessed_at"
                     type="datetime-local"
                     required
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Assessment Type</label>
              <select name="assessment_type"
                      class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="focused">Focused</option>
                <option value="initial">Initial</option>
                <option value="head_to_toe">Head-to-Toe</option>
                <option value="reassessment">Reassessment</option>
              </select>
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Chief Complaint</label>
              <input name="chief_complaint"
                     type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="e.g., 'Masakit po dibdib ko' since 2 hours, radiating to left arm.">
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Subjective</label>
              <textarea id="assessSubjective"
                        name="subjective"
                        rows="2"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Patient's own words, history of present illness, associated symptoms."></textarea>
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Objective</label>
              <textarea id="assessObjective"
                        name="objective"
                        rows="3"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Head-to-toe / system-based findings, vital signs, BMI, I&O, diagnostics."></textarea>
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Assessment</label>
              <textarea id="assessAssessment"
                        name="assessment"
                        rows="2"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Clinical impression or prioritized problems based on S + O."></textarea>
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Plan</label>
              <textarea id="assessPlan"
                        name="plan"
                        rows="2"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Diagnostics, meds, interventions, teaching, referrals, and follow-up."></textarea>
            </div>
          </div>
        </div>

        {{-- RIGHT: ASSESSMENT HELPER PANEL --}}
        <aside class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-sm font-semibold text-slate-900">Assessment Helper Panel</h4>
              <p class="text-[11px] text-slate-500">
                Insert structured patterns for head-to-toe, vitals, fluid status, and plan.
              </p>
            </div>
            <i data-lucide="stethoscope" class="h-4 w-4 text-slate-400"></i>
          </div>

          {{-- Objective templates --}}
          <div class="space-y-2">
            <p class="text-[11px] font-semibold text-slate-600">Objective patterns</p>
            <div class="flex flex-wrap gap-2">
              <button type="button"
                      id="btnAssessObjInitial"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="user-check" class="mr-1.5 h-3.5 w-3.5"></i>
                Initial / head-to-toe
              </button>
              <button type="button"
                      id="btnAssessObjFocusedResp"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="lungs" class="mr-1.5 h-3.5 w-3.5"></i>
                Focused respiratory
              </button>
              <button type="button"
                      id="btnAssessObjFluid"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="droplets" class="mr-1.5 h-3.5 w-3.5"></i>
                Fluid / I&amp;O snapshot
              </button>
            </div>
          </div>

          <hr class="border-dashed border-slate-200">

          {{-- Assessment (clinical impression) templates --}}
          <div class="space-y-2">
            <p class="text-[11px] font-semibold text-slate-600">Assessment impressions</p>
            <div class="flex flex-wrap gap-2">
              <button type="button"
                      id="btnAssessImpressionStable"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="shield-check" class="mr-1.5 h-3.5 w-3.5"></i>
                Hemodynamically stable
              </button>
              <button type="button"
                      id="btnAssessImpressionPain"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="activity" class="mr-1.5 h-3.5 w-3.5"></i>
                Pain-related
              </button>
              <button type="button"
                      id="btnAssessImpressionRisk"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="alert-triangle" class="mr-1.5 h-3.5 w-3.5"></i>
                At risk (falls / aspiration)
              </button>
            </div>
          </div>

          <hr class="border-dashed border-slate-200">

          {{-- Plan templates --}}
          <div class="space-y-2">
            <p class="text-[11px] font-semibold text-slate-600">Plan patterns</p>
            <div class="flex flex-wrap gap-2">
              <button type="button"
                      id="btnAssessPlanDiagnostics"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="beaker" class="mr-1.5 h-3.5 w-3.5"></i>
                Diagnostics / monitoring
              </button>
              <button type="button"
                      id="btnAssessPlanMeds"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="pill" class="mr-1.5 h-3.5 w-3.5"></i>
                Meds &amp; pain control
              </button>
              <button type="button"
                      id="btnAssessPlanEducation"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="book-open-check" class="mr-1.5 h-3.5 w-3.5"></i>
                Teaching &amp; follow-up
              </button>
            </div>
          </div>

          <p class="text-[10px] text-slate-400">
            Fill in actual values using results from Vitals (incl. BMI/BSA), I&amp;O, MAR, and IV calculators,
            then adjust these templates to match the patient’s condition.
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

{{-- Inline script: Assessment helper templates --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const subjEl = document.getElementById('assessSubjective');
    const objEl  = document.getElementById('assessObjective');
    const assEl  = document.getElementById('assessAssessment');
    const planEl = document.getElementById('assessPlan');

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

    // Objective: initial / head-to-toe
    document.getElementById('btnAssessObjInitial')?.addEventListener('click', function () {
      const t =
`General: Awake, oriented x__, appears __, in __ distress. 
HEENT: __. 
Cardio: S1 S2 present, no murmurs, pulses __, cap refill __ sec. 
Resp: Chest expansion equal, breath sounds __, RR __ cpm, SpO₂ __% on __. 
GI: Abdomen __, BS __/min, no guarding or rigidity, last BM __. 
GU: Voids __, urine color __, output __ mL (see I&O). 
Extremities: ROM __, strength __/5, edema __. 
Skin: Color __, turgor __, lesions/wounds: __. 
VS: T __ °C, HR __ bpm, BP __ / __ mmHg, Pain __/10, Wt __ kg, Ht __ cm, BMI __.`;
      appendTo(objEl, t);
    });

    // Objective: focused respiratory
    document.getElementById('btnAssessObjFocusedResp')?.addEventListener('click', function () {
      const t =
`Focused respiratory: RR __ cpm, pattern __, use of accessory muscles (yes/no: __). 
Chest expansion __, breath sounds __ (clear/wheezes/crackles) on __ lobes. 
SpO₂ __% on room air / O₂ at __ LPM via __. 
Cough __ (productive/non-productive) with sputum __. Patient reports SOB at rest/exertion: __.`;
      appendTo(objEl, t);
    });

    // Objective: fluid / I&O snapshot
    document.getElementById('btnAssessObjFluid')?.addEventListener('click', function () {
      const t =
`Fluid status: Intake __ mL, Output __ mL, Net __ mL over last __ hrs (see I&O chart). 
Mucous membranes __, skin turgor __, edema __, daily weight __ kg vs baseline __ kg. 
Urine output __ mL/kg/hr, urine color __. No signs of severe dehydration or fluid overload noted / __.`;
      appendTo(objEl, t);
    });

    // Assessment impressions
    document.getElementById('btnAssessImpressionStable')?.addEventListener('click', function () {
      const t =
`Patient currently hemodynamically stable with VS within acceptable range for age, adequate oxygenation, and no acute distress noted. Will continue close monitoring for any changes in status.`;
      appendTo(assEl, t);
    });

    document.getElementById('btnAssessImpressionPain')?.addEventListener('click', function () {
      const t =
`Assessment indicates acute/chronic pain related to __ as evidenced by reported pain __/10, guarding, facial grimacing, and limited movement of __. Pain impacts sleep/ADLs: __.`;
      appendTo(assEl, t);
    });

    document.getElementById('btnAssessImpressionRisk')?.addEventListener('click', function () {
      const t =
`Patient at risk for __ (falls/aspiration/skin breakdown) due to __ (weakness, impaired mobility, altered LOC, incontinence, etc.). Requires safety and prevention strategies.`;
      appendTo(assEl, t);
    });

    // Plan: diagnostics / monitoring
    document.getElementById('btnAssessPlanDiagnostics')?.addEventListener('click', function () {
      const t =
`Plan (monitoring/diagnostics): 
• Monitor VS (including pain score) every __ hours and PRN, watch for trends. 
• Track I&O and net balance using I&O tool; weigh patient daily at same time. 
• Review labs (CBC, electrolytes, RFTs, ABG, others) when available and report abnormal findings. 
• Reassess respiratory and cardiovascular status after interventions or if condition changes.`;
      appendTo(planEl, t);
    });

    // Plan: meds & pain control
    document.getElementById('btnAssessPlanMeds')?.addEventListener('click', function () {
      const t =
`Plan (medications/pain control): 
• Administer medications as ordered, using MAR and dosage calculator for high-risk drugs. 
• Provide analgesics as prescribed, evaluate effect within 30–60 minutes, and document response. 
• Coordinate with MD for dose adjustment if pain remains > __/10 or if adverse effects occur. 
• Ensure IV fluids infuse at calculated rate; check IV site and patency regularly.`;
      appendTo(planEl, t);
    });

    // Plan: education & follow-up
    document.getElementById('btnAssessPlanEducation')?.addEventListener('click', function () {
      const t =
`Plan (education/follow-up): 
• Educate patient/family on current condition, treatment plan, and warning signs that need immediate reporting. 
• Teach proper use of call light, safe ambulation, and adherence to fluid/diet restrictions if ordered. 
• Reinforce medication schedule and possible side effects in simple terms. 
• Plan for reassessment in __ hours or sooner if patient status changes.`;
      appendTo(planEl, t);
    });
  });
</script>