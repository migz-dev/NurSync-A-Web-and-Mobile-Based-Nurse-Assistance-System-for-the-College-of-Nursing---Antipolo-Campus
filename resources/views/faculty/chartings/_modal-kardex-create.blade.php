{{-- resources/views/faculty/chartings/_modal-kardex-create.blade.php --}}
<div id="modalCreateKardex" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-modal-close></div>

  <div class="relative mx-auto my-10 w-[95%] max-w-5xl rounded-2xl bg-white shadow-2xl border border-slate-200">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-2xl">
      <h3 class="text-lg font-bold text-slate-900">New Nursing Kardex Entry</h3>
      <button type="button"
              class="h-9 w-9 inline-flex items-center justify-center rounded-lg hover:bg-slate-100"
              data-modal-close>
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    <form method="POST"
          action="{{ route('faculty.chartings.kardex.store', $patient->id) }}"
          class="p-6 space-y-5">
      @csrf

      {{-- 2-column layout: left = Kardex form, right = helper panel --}}
      <div class="grid gap-6 md:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
        {{-- LEFT: KARDEX FORM --}}
        <div class="space-y-4">
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Updated For *</label>
              <input name="updated_for"
                     type="datetime-local"
                     required
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Diagnosis</label>
              <input id="kardexDiagnosis"
                     name="diagnosis"
                     type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="e.g., PNA, Type 2 DM, HTN; post-op day #__">
            </div>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Diet</label>
              <input id="kardexDiet"
                     name="diet"
                     type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="e.g., Regular, Soft diet, NPO after midnight">
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Activity</label>
              <input id="kardexActivity"
                     name="activity"
                     type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="e.g., Bed rest, Up ad lib with assist">
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Medications / IV Fluids</label>
              <textarea id="kardexMeds"
                        name="medications"
                        rows="3"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Key maintenance meds, PRNs, and IV fluids with rates (pull from MAR + IV calculator)."></textarea>
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Nursing Orders</label>
              <textarea id="kardexOrders"
                        name="nursing_orders"
                        rows="3"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Monitoring, safety, turning schedule, I&O, labs to follow, teaching, etc."></textarea>
            </div>
          </div>
        </div>

        {{-- RIGHT: KARDEX HELPER PANEL --}}
        <aside class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-sm font-semibold text-slate-900">Kardex Helper Panel</h4>
              <p class="text-[11px] text-slate-500">
                Quickly fill common Kardex sections using standard patterns and calculator outputs.
              </p>
            </div>
            <i data-lucide="layout-dashboard" class="h-4 w-4 text-slate-400"></i>
          </div>

          {{-- Diagnosis / profile helper --}}
          <div class="space-y-2">
            <p class="text-[11px] font-semibold text-slate-600">Diagnosis / profile</p>
            <button type="button"
                    id="btnKardexDxTemplate"
                    class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
              <i data-lucide="file-badge-2" class="mr-1.5 h-3.5 w-3.5"></i>
              Insert Dx pattern
            </button>
          </div>

          <hr class="border-dashed border-slate-200">

          {{-- Diet & Activity presets --}}
          <div class="space-y-2">
            <p class="text-[11px] font-semibold text-slate-600">Diet &amp; activity presets</p>

            <div class="space-y-1">
              <p class="text-[11px] text-slate-500 mb-0.5">Diet</p>
              <div class="flex flex-wrap gap-2">
                <button type="button"
                        id="btnKardexDietRegular"
                        class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                  Regular / house diet
                </button>
                <button type="button"
                        id="btnKardexDietSoft"
                        class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                  Soft diet
                </button>
                <button type="button"
                        id="btnKardexDietNPO"
                        class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                  NPO w/ instructions
                </button>
              </div>
            </div>

            <div class="space-y-1">
              <p class="text-[11px] text-slate-500 mb-0.5">Activity</p>
              <div class="flex flex-wrap gap-2">
                <button type="button"
                        id="btnKardexActivityBR"
                        class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                  Strict bed rest
                </button>
                <button type="button"
                        id="btnKardexActivityBRP"
                        class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                  BR with assist
                </button>
                <button type="button"
                        id="btnKardexActivityUpAdLib"
                        class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                  Up ad lib
                </button>
              </div>
            </div>
          </div>

          <hr class="border-dashed border-slate-200">

          {{-- Meds / IV section helper --}}
          <div class="space-y-2">
            <p class="text-[11px] font-semibold text-slate-600">Meds / IV snapshot</p>
            <button type="button"
                    id="btnKardexMedsTemplate"
                    class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
              <i data-lucide="pill" class="mr-1.5 h-3.5 w-3.5"></i>
              Insert meds &amp; IV pattern
            </button>
            <p class="text-[10px] text-slate-400">
              Fill in doses and IV rates using the MAR dosage &amp; IV flow calculators.
            </p>
          </div>

          <hr class="border-dashed border-slate-200">

          {{-- Nursing orders helper --}}
          <div class="space-y-2">
            <p class="text-[11px] font-semibold text-slate-600">Nursing orders</p>
            <div class="flex flex-wrap gap-2">
              <button type="button"
                      id="btnKardexOrdersRoutine"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                Routine monitoring
              </button>
              <button type="button"
                      id="btnKardexOrdersRisk"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                Safety / risk orders
              </button>
              <button type="button"
                      id="btnKardexOrdersIOTurn"
                      class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[11px] font-medium text-slate-800 hover:bg-slate-100">
                I&amp;O &amp; turning schedule
              </button>
            </div>
          </div>

          <p class="text-[10px] text-slate-400">
            Kardex should match your latest chartings: VS trends, pain score, I&amp;O net,
            current meds, and IVs — all supported by the calculators you've already used.
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

{{-- Inline script: Kardex helper templates --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const dxEl    = document.getElementById('kardexDiagnosis');
    const dietEl  = document.getElementById('kardexDiet');
    const actEl   = document.getElementById('kardexActivity');
    const medsEl  = document.getElementById('kardexMeds');
    const ordersEl= document.getElementById('kardexOrders');

    function setIfEmptyOrAppend(el, text, replace = false) {
      if (!el) return;
      if (replace || !el.value.trim()) {
        el.value = text;
      } else {
        el.value = el.value.trim() + '\\n' + text;
      }
      el.scrollTop = el.scrollHeight;
      el.focus();
    }

    // Dx / profile pattern
    document.getElementById('btnKardexDxTemplate')?.addEventListener('click', function () {
      const t =
`Primary Dx: __. Admitted on __ for __. Co-morbidities: __ (e.g., DM, HTN). Allergies: __. Code status: __.`;
      setIfEmptyOrAppend(dxEl, t, true);
    });

    // Diet presets
    document.getElementById('btnKardexDietRegular')?.addEventListener('click', function () {
      const t = 'Regular / house diet as tolerated.';
      setIfEmptyOrAppend(dietEl, t, true);
    });

    document.getElementById('btnKardexDietSoft')?.addEventListener('click', function () {
      const t = 'Soft diet; encourage small frequent meals, observe for nausea/vomiting.';
      setIfEmptyOrAppend(dietEl, t, true);
    });

    document.getElementById('btnKardexDietNPO')?.addEventListener('click', function () {
      const t = 'NPO after __ (time) for __ (procedure); meds per MD order with sips of water only.';
      setIfEmptyOrAppend(dietEl, t, true);
    });

    // Activity presets
    document.getElementById('btnKardexActivityBR')?.addEventListener('click', function () {
      const t = 'Strict bed rest; reposition q2h, HOB __°, assist with all ADLs, fall precautions in place.';
      setIfEmptyOrAppend(actEl, t, true);
    });

    document.getElementById('btnKardexActivityBRP')?.addEventListener('click', function () {
      const t = 'Bed rest with assist to chair; assist to ambulate with __, fall precautions in place.';
      setIfEmptyOrAppend(actEl, t, true);
    });

    document.getElementById('btnKardexActivityUpAdLib')?.addEventListener('click', function () {
      const t = 'Up ad lib with supervision as needed; encourage ambulation and deep breathing exercises.';
      setIfEmptyOrAppend(actEl, t, true);
    });

    // Meds / IV pattern
    document.getElementById('btnKardexMedsTemplate')?.addEventListener('click', function () {
      const t =
`Maintenance: __ (dose, route, frequency), __, __. 
PRN: __ for pain __/10, last given at __ with effect __. 
IV fluids: __ mL of __ at __ mL/hr (≈ __ gtt/min, DF __ gtt/mL); site __, last checked __. 
High-alert meds: __ (double check dose using calculator & 2nd nurse verification).`;
      setIfEmptyOrAppend(medsEl, t, true);
    });

    // Nursing orders: routine
    document.getElementById('btnKardexOrdersRoutine')?.addEventListener('click', function () {
      const t =
`Routine monitoring: VS (incl. pain score) q__ hrs & PRN; strict I&O with running net using I&O tool; monitor labs (__, __) and report critical values; reassess respiratory and hemodynamic status after any change in condition.`;
      setIfEmptyOrAppend(ordersEl, t, false);
    });

    // Nursing orders: risk / safety
    document.getElementById('btnKardexOrdersRisk')?.addEventListener('click', function () {
      const t =
`Safety / risk: Fall risk __; keep bed low, rails up __, call light within reach, non-slip footwear, educate patient/family on calling for assistance. Skin risk: inspect bony prominences q__ hrs, use pressure-relieving devices as needed.`;
      setIfEmptyOrAppend(ordersEl, t, false);
    });

    // Nursing orders: I&O & turning
    document.getElementById('btnKardexOrdersIOTurn')?.addEventListener('click', function () {
      const t =
`I&O & turning: Record intake/output every __ hrs and compute net per shift using I&O calculator; target urine output ≥ 0.5 mL/kg/hr. Turn/reposition q2h, document tolerance, and protect pressure areas with pillows/foam.`;
      setIfEmptyOrAppend(ordersEl, t, false);
    });
  });
</script>
