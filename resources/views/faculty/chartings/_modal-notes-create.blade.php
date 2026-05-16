{{-- resources/views/faculty/chartings/_modal-notes-create.blade.php --}}
<div id="modalCreateNotes" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-modal-close></div>

  <div class="relative mx-auto my-10 w-[95%] max-w-5xl rounded-2xl bg-white shadow-2xl border border-slate-200">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-2xl">
      <h3 class="text-xl font-bold text-slate-900">New Nurse’s Note</h3>
      <button type="button"
              class="h-9 w-9 inline-flex items-center justify-center rounded-lg hover:bg-slate-100"
              data-modal-close>
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    <form method="POST"
          action="{{ route('faculty.chartings.notes.store', $patient->id) }}"
          class="p-6 space-y-6">
      @csrf
      <input type="hidden" name="patient_id" value="{{ $patient->id }}">

      {{-- 2-column layout: left = SOAP + narrative, right = smart insert helpers --}}
      <div class="grid gap-6 md:grid-cols-[minmax(0,1.35fr)_minmax(0,1fr)]">
        {{-- LEFT: FORM FIELDS --}}
        <div class="space-y-5">
          <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div class="space-y-3">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Logged At *</label>
              <input name="logged_at"
                     type="datetime-local"
                     required
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>

              <label class="block text-xs font-semibold text-slate-600 mb-1">Type</label>
              <select name="note_type"
                      class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200">
                @foreach(\App\Models\NursesNote::types() as $t)
                  <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
              </select>

              <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
              <select name="status"
                      class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200">
                @foreach(\App\Models\NursesNote::statuses() as $s)
                  <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                @endforeach
              </select>
            </div>

            <div class="space-y-3">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Subjective (S)</label>
              <textarea name="subjective" rows="3"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>

              <label class="block text-xs font-semibold text-slate-600 mb-1">Objective (O)</label>
              <textarea name="objective" rows="3"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
            </div>

            <div class="space-y-3">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Assessment (A)</label>
              <textarea name="assessment" rows="3"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>

              <label class="block text-xs font-semibold text-slate-600 mb-1">Plan (P)</label>
              <textarea name="plan" rows="3"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
            </div>
          </div>

          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Narrative / Free-text</label>
            <textarea id="nurseNoteNarrative"
                      name="note"
                      rows="6"
                      class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                      placeholder="Use SOAP/DAR format or free narrative. You can also use the quick-insert helpers on the right."></textarea>
          </div>
        </div>

        {{-- RIGHT: SMART INSERT / CALCULATOR-INTEGRATED HELPERS --}}
        <aside class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-sm font-semibold text-slate-900">Quick Insert Helpers</h4>
              <p class="text-[11px] text-slate-500">
                Insert common note structures and summaries from your other chartings.
              </p>
            </div>
            <i data-lucide="sparkles" class="h-4 w-4 text-slate-400"></i>
          </div>

          {{-- Templates: SOAP, DAR --}}
          <div class="space-y-2">
            <p class="text-[11px] font-semibold text-slate-600">Note Templates</p>
            <div class="flex flex-wrap gap-2">
              <button type="button"
                      id="btnInsertSoapTemplate"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="file-pen" class="mr-1.5 h-3.5 w-3.5"></i>
                Insert SOAP skeleton
              </button>
              <button type="button"
                      id="btnInsertDarTemplate"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="list-todo" class="mr-1.5 h-3.5 w-3.5"></i>
                Insert DAR skeleton
              </button>
            </div>
          </div>

          <hr class="border-dashed border-slate-200">

          {{-- Calculator-related snippets --}}
          <div class="space-y-2">
            <p class="text-[11px] font-semibold text-slate-600">Summaries from other chartings</p>
            <div class="flex flex-wrap gap-2">
              <button type="button"
                      id="btnInsertVitalsSummary"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="activity" class="mr-1.5 h-3.5 w-3.5"></i>
                Vitals summary line
              </button>
              <button type="button"
                      id="btnInsertIoSummary"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="droplets" class="mr-1.5 h-3.5 w-3.5"></i>
                Intake &amp; Output summary
              </button>
              <button type="button"
                      id="btnInsertMarSummary"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="pill" class="mr-1.5 h-3.5 w-3.5"></i>
                Meds / MAR summary
              </button>
              <button type="button"
                      id="btnInsertIvSummary"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                <i data-lucide="droplet" class="mr-1.5 h-3.5 w-3.5"></i>
                IV infusion summary
              </button>
            </div>
            <p class="text-[10px] text-slate-400">
              These insert structured lines that you can quickly fill with values from the MAR, Vitals, I&amp;O, and Treatment calculators.
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
                class="rounded-lg px-5 py-2 text-sm bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm">
          Save Note
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Inline script: quick insert helpers for Nurse's Notes --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const narrative = document.getElementById('nurseNoteNarrative');
    if (!narrative) return;

    function appendBlock(text) {
      const current = narrative.value.trim();
      if (!current) {
        narrative.value = text;
      } else {
        narrative.value = current + '\\n\\n' + text;
      }
      narrative.scrollTop = narrative.scrollHeight;
      narrative.focus();
    }

    const btnSoap = document.getElementById('btnInsertSoapTemplate');
    const btnDar  = document.getElementById('btnInsertDarTemplate');

    const btnVitals = document.getElementById('btnInsertVitalsSummary');
    const btnIo     = document.getElementById('btnInsertIoSummary');
    const btnMar    = document.getElementById('btnInsertMarSummary');
    const btnIv     = document.getElementById('btnInsertIvSummary');

    // SOAP skeleton
    btnSoap?.addEventListener('click', function () {
      const block =
`S: ________________________________________________
O: T: __ °C, HR: __ bpm, RR: __ cpm, BP: __ / __ mmHg, SpO₂: __%; Pain: __/10. Skin, LOC, and activity as observed.
A: ________________________________________________
P: ________________________________________________`;
      appendBlock(block);
    });

    // DAR skeleton
    btnDar?.addEventListener('click', function () {
      const block =
`D: ________________________________________________
A: ________________________________________________
R: ________________________________________________`;
      appendBlock(block);
    });

    // Vitals summary line (fill using Vitals + BMI/BSA calculator)
    btnVitals?.addEventListener('click', function () {
      const block =
`Vitals: T __ °C, HR __ bpm, RR __ cpm, BP __ / __ mmHg, SpO₂ __%, Pain __/10, Wt __ kg, Ht __ cm, BMI __, BSA __ m².`;
      appendBlock(block);
    });

    // I&O summary line (fill using I&O calculator)
    btnIo?.addEventListener('click', function () {
      const block =
`I&O (this shift): Intake __ mL (Oral __ / IV __ / NG __), Output __ mL (Urine __ / Stool __ / Emesis __ / Drain __); Net __ mL.`;
      appendBlock(block);
    });

    // Meds / MAR summary line (fill using MAR calculator)
    btnMar?.addEventListener('click', function () {
      const block =
`Medications: Given __ (dose/form/route/frequency). No adverse reactions observed / __. Held/Missed doses: __ (reason: __).`;
      appendBlock(block);
    });

    // IV infusion summary (fill using IV rate calculator in Treatment/MAR)
    btnIv?.addEventListener('click', function () {
      const block =
`IV infusion: __ mL of __ via __ line at __ mL/hr (≈ __ gtt/min, DF __ gtt/mL). Site patent, no signs of infiltration / __.`;
      appendBlock(block);
    });
  });
</script>
