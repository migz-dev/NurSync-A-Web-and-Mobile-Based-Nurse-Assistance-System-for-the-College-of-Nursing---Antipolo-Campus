{{-- resources/views/faculty/chartings/_modal-pain-create.blade.php --}}
<div id="modalCreatePain" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-modal-close></div>

  <div class="relative mx-auto my-10 w-[95%] max-w-5xl rounded-2xl bg-white shadow-2xl border border-slate-200">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-2xl">
      <h3 class="text-xl font-bold text-slate-900">
        New Pain Assessment
      </h3>
      <button type="button"
              class="h-9 w-9 inline-flex items-center justify-center rounded-lg hover:bg-slate-100"
              data-modal-close>
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    <form method="POST"
          action="{{ route('faculty.chartings.pain.store', $patient->id) }}"
          class="p-6 space-y-6">
      @csrf
      <input type="hidden" name="patient_id" value="{{ $patient->id }}">

      <div class="space-y-5">
        <div class="grid gap-4 md:grid-cols-3">
          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Date &amp; Time Assessed *
            </label>
            <input name="assessed_at"
                   type="datetime-local"
                   required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
          </div>

          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Pain Score (0–10) *
            </label>
            <input name="pain_score"
                   type="number"
                   min="0"
                   max="10"
                   required
                   placeholder="e.g., 7"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
          </div>

          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Scale Used
            </label>
            <input name="scale_used"
                   type="text"
                   placeholder="e.g., Numeric, Wong-Baker, FLACC"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
          </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Location
            </label>
            <input name="location"
                   type="text"
                   placeholder="e.g., epigastric area, lower back, incisional site"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>

            <label class="block text-xs font-semibold text-slate-600 mb-1 mt-3">
              Characteristics / Quality
            </label>
            <input name="characteristics"
                   type="text"
                   placeholder="e.g., sharp, dull, throbbing, burning, cramping"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
          </div>

          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Aggravating Factors
            </label>
            <textarea name="aggravating_factors"
                      rows="2"
                      placeholder="e.g., movement, coughing, deep breathing"
                      class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>

            <label class="block text-xs font-semibold text-slate-600 mb-1 mt-3">
              Relieving Factors
            </label>
            <textarea name="relieving_factors"
                      rows="2"
                      placeholder="e.g., rest, position change, analgesics"
                      class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
          </div>
        </div>

        <div class="space-y-2">
          <label class="block text-xs font-semibold text-slate-600 mb-1">
            Interventions / Management
          </label>
          <textarea name="interventions"
                    rows="3"
                    placeholder="E.g., given paracetamol 500 mg, repositioned, applied warm compress, taught deep breathing."
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>

        <div class="space-y-2">
          <label class="block text-xs font-semibold text-slate-600 mb-1">
            Response to Intervention
          </label>
          <textarea name="response"
                    rows="3"
                    placeholder="E.g., pain reduced from 7/10 to 3/10 after 30 minutes; still grimacing at movement, etc."
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
          Save Pain Assessment
        </button>
      </div>
    </form>
  </div>
</div>
