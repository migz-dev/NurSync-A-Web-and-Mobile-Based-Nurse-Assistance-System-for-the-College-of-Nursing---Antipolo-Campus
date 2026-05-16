{{-- resources/views/faculty/chartings/_modal-allergies-create.blade.php --}}
<div id="modalCreateAllergies" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-modal-close></div>

  <div class="relative mx-auto my-10 w-[95%] max-w-5xl rounded-2xl bg-white shadow-2xl border border-slate-200">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-2xl">
      <h3 class="text-xl font-bold text-slate-900">
        New Allergy / Adverse Reaction
      </h3>
      <button type="button"
              class="h-9 w-9 inline-flex items-center justify-center rounded-lg hover:bg-slate-100"
              data-modal-close>
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    <form method="POST"
          action="{{ route('faculty.chartings.allergies.store', $patient->id) }}"
          class="p-6 space-y-6">
      @csrf
      <input type="hidden" name="patient_id" value="{{ $patient->id }}">

      <div class="space-y-5">
        <div class="grid gap-4 md:grid-cols-3">
          {{-- Allergen --}}
          <div class="space-y-2 md:col-span-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Allergen / Trigger *
            </label>
            <input name="allergen"
                   type="text"
                   required
                   placeholder="e.g., Penicillin, Shrimp, Latex"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
          </div>

          {{-- Date observed --}}
          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Date &amp; Time Observed
            </label>
            <input name="date_observed"
                   type="datetime-local"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
          </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
          {{-- Reaction --}}
          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Reaction Description *
            </label>
            <textarea name="reaction"
                      rows="3"
                      required
                      placeholder="e.g., urticaria, bronchospasm, facial edema, hypotension"
                      class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
          </div>

          {{-- Severity --}}
          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Severity
            </label>
            <select name="severity"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200">
              <option value="">Select severity</option>
              <option value="Mild">Mild</option>
              <option value="Moderate">Moderate</option>
              <option value="Severe">Severe</option>
              <option value="Anaphylaxis">Anaphylaxis</option>
            </select>
          </div>
        </div>

        {{-- Actions taken --}}
        <div class="space-y-2">
          <label class="block text-xs font-semibold text-slate-600 mb-1">
            Actions Taken
          </label>
          <textarea name="action_taken"
                    rows="3"
                    placeholder="E.g., stopped drug, notified MD, given epinephrine, documented in chart, applied ID band, etc."
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>

        {{-- Notes --}}
        <div class="space-y-2">
          <label class="block text-xs font-semibold text-slate-600 mb-1">
            Notes
          </label>
          <textarea name="notes"
                    rows="3"
                    placeholder="Additional information, cross-allergies, patient’s report, etc."
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
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
          Save Allergy
        </button>
      </div>
    </form>
  </div>
</div>
