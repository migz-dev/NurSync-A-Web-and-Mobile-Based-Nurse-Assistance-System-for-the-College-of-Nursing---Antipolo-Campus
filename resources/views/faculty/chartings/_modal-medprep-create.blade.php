{{-- resources/views/faculty/chartings/_modal-medprep-create.blade.php --}}
<div id="modalCreateMedPrep" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-modal-close></div>

  <div class="relative mx-auto my-10 w-[95%] max-w-5xl rounded-2xl bg-white shadow-2xl border border-slate-200">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-2xl">
      <h3 class="text-xl font-bold text-slate-900">
        New Medication Preparation
      </h3>
      <button type="button"
              class="h-9 w-9 inline-flex items-center justify-center rounded-lg hover:bg-slate-100"
              data-modal-close>
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    <form method="POST"
          action="{{ route('faculty.chartings.medprep.store', $patient->id) }}"
          class="p-6 space-y-6">
      @csrf
      <input type="hidden" name="patient_id" value="{{ $patient->id }}">

      <div class="space-y-5">
        {{-- Top fields --}}
        <div class="grid gap-4 md:grid-cols-3">
          <div class="space-y-2 md:col-span-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Medication Name *
            </label>
            <input name="medication_name"
                   type="text"
                   required
                   placeholder="e.g., Paracetamol 500 mg tab, Ceftriaxone 1 g IV"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
          </div>

          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Dose
            </label>
            <input name="dose"
                   type="text"
                   placeholder="e.g., 1 tab, 1 g in 100 mL NS"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
          </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Route
            </label>
            <input name="route"
                   type="text"
                   placeholder="e.g., PO, IV, IM"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
          </div>

          <div class="space-y-2 md:col-span-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Time Prepared *
            </label>
            <input name="time_prepared"
                   type="datetime-local"
                   required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
          </div>
        </div>

        {{-- Preparation steps --}}
        <div class="space-y-2">
          <label class="block text-xs font-semibold text-slate-600 mb-1">
            Preparation Steps
          </label>
          <textarea name="preparation_steps"
                    rows="4"
                    placeholder="Describe how the medication was prepared: reconstitution, dilution, label, expiration check, etc."
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>

        {{-- Double-check & safety checks --}}
        <div class="grid gap-4 md:grid-cols-2">
          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Double-Checked By
            </label>
            <input name="double_checked_by"
                   type="text"
                   placeholder="e.g., RN Santos, CI Dela Cruz"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
          </div>

          <div class="space-y-3">
            <div class="flex items-center gap-2 mt-1">
              <input id="safety_checks_completed"
                     name="safety_checks_completed"
                     type="checkbox"
                     value="1"
                     class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-200">
              <label for="safety_checks_completed" class="text-xs font-semibold text-slate-700">
                Safety checks completed (5 Rights, expiry, allergies, etc.)
              </label>
            </div>

            <div class="space-y-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">
                Remarks
              </label>
              <textarea name="remarks"
                        rows="3"
                        placeholder="Additional notes, issues encountered, clarifications from MD, etc."
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-200">
        <button type="button"
                class="rounded-lg px-5 py-2 text-sm border border-slate-300 text-slate-700 hover:bg-slate-100"
                data-modal-close>
          Cancel
        </button>
        <button type="submit"
                class="rounded-lg px-5 py-2 text-sm bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm">
          Save Preparation
        </button>
      </div>
    </form>
  </div>
</div>
