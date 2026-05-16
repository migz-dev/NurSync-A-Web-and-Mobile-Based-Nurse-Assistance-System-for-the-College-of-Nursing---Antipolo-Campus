{{-- resources/views/faculty/chartings/_modal-education-create.blade.php --}}
<div id="modalCreateEducation" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-modal-close></div>

  <div class="relative mx-auto my-10 w-[95%] max-w-5xl rounded-2xl bg-white shadow-2xl border border-slate-200">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-2xl">
      <h3 class="text-xl font-bold text-slate-900">
        New Patient Education Session
      </h3>
      <button type="button"
              class="h-9 w-9 inline-flex items-center justify-center rounded-lg hover:bg-slate-100"
              data-modal-close>
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    <form method="POST"
          action="{{ route('faculty.chartings.education.store', $patient->id) }}"
          class="p-6 space-y-6">
      @csrf
      <input type="hidden" name="patient_id" value="{{ $patient->id }}">

      <div class="space-y-5">
        {{-- Topic + method + materials --}}
        <div class="grid gap-4 md:grid-cols-3">
          <div class="space-y-2 md:col-span-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Topic / Focus *
            </label>
            <input name="topic"
                   type="text"
                   required
                   placeholder="e.g., Diabetes foot care, Post-op wound care, Medication adherence"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
          </div>

          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Method Used
            </label>
            <input name="method_used"
                   type="text"
                   placeholder="e.g., One-on-one teaching, Demo-return demo"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
          </div>
        </div>

        <div class="space-y-2">
          <label class="block text-xs font-semibold text-slate-600 mb-1">
            Materials / Tools Used
          </label>
          <input name="materials_used"
                 type="text"
                 placeholder="e.g., Pamphlet, model, tablet video, flipchart"
                 class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
        </div>

        {{-- Session notes --}}
        <div class="space-y-2">
          <label class="block text-xs font-semibold text-slate-600 mb-1">
            Session Notes
          </label>
          <textarea name="session_notes"
                    rows="4"
                    placeholder="Summarize what was discussed, questions asked, and any teaching points reinforced."
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>

        {{-- Patient understanding & follow-up --}}
        <div class="grid gap-4 md:grid-cols-2">
          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Patient Understanding / Response
            </label>
            <textarea name="patient_understanding"
                      rows="3"
                      placeholder="E.g., verbalized understanding, needs reinforcement, able to return-demo, etc."
                      class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
          </div>

          <div class="space-y-3">
            <div class="flex items-center gap-2 mt-1">
              <input id="follow_up_required"
                     name="follow_up_required"
                     type="checkbox"
                     value="1"
                     class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-200">
              <label for="follow_up_required" class="text-xs font-semibold text-slate-700">
                Follow-up session required
              </label>
            </div>

            <div class="space-y-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">
                Follow-up Notes
              </label>
              <textarea name="follow_up_notes"
                        rows="3"
                        placeholder="What needs to be revisited? When? Any specific instructions?"
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
          Save Education
        </button>
      </div>
    </form>
  </div>
</div>
