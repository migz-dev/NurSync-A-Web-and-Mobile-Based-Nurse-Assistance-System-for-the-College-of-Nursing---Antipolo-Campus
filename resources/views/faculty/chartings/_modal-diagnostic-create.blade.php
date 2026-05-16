{{-- resources/views/faculty/chartings/_modal-diagnostic-create.blade.php --}}
<div id="modalCreateDiagnostic" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-modal-close></div>

  <div class="relative mx-auto my-10 w-[95%] max-w-5xl rounded-2xl bg-white shadow-2xl border border-slate-200">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-2xl">
      <h3 class="text-xl font-bold text-slate-900">
        New Diagnostic Result
      </h3>
      <button type="button"
              class="h-9 w-9 inline-flex items-center justify-center rounded-lg hover:bg-slate-100"
              data-modal-close>
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    <form method="POST"
          action="{{ route('faculty.chartings.diagnostic.store', $patient->id) }}"
          class="p-6 space-y-6">
      @csrf
      <input type="hidden" name="patient_id" value="{{ $patient->id }}">

      <div class="space-y-5">
        {{-- Top row: datetime + type + title --}}
        <div class="grid gap-4 md:grid-cols-3">
          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Result Date &amp; Time *
            </label>
            <input name="result_date"
                   type="datetime-local"
                   required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
          </div>

          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Type of Test *
            </label>
            <input name="result_type"
                   type="text"
                   required
                   placeholder="e.g., CBC, Chest X-ray, ABG"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
          </div>

          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Result Title / Identifier *
            </label>
            <input name="result_title"
                   type="text"
                   required
                   placeholder="e.g., CBC – 11/14/2025 06:00"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
          </div>
        </div>

        {{-- Significant findings --}}
        <div class="space-y-2">
          <label class="block text-xs font-semibold text-slate-600 mb-1">
            Significant Findings
          </label>
          <textarea name="significant_findings"
                    rows="3"
                    placeholder="Summarize key abnormal or critical findings relevant to nursing care."
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>

        {{-- Critical values --}}
        <div class="space-y-2">
          <label class="block text-xs font-semibold text-slate-600 mb-1">
            Critical Values (if any)
          </label>
          <textarea name="critical_values"
                    rows="2"
                    placeholder="Document any critical lab values and notification details."
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>

        {{-- Interpretation --}}
        <div class="space-y-2">
          <label class="block text-xs font-semibold text-slate-600 mb-1">
            Interpretation / Nursing Impression
          </label>
          <textarea name="interpretation_notes"
                    rows="3"
                    placeholder="Write a short interpretation or clinical relevance from a nursing perspective."
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>

        {{-- Actions taken --}}
        <div class="space-y-2">
          <label class="block text-xs font-semibold text-slate-600 mb-1">
            Actions Taken / Follow-up
          </label>
          <textarea name="actions_taken"
                    rows="3"
                    placeholder="E.g., informed MD, repeated test, started oxygen, adjusted IV fluids, etc."
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>

        {{-- Attachment path (optional) --}}
        <div class="space-y-2">
          <label class="block text-xs font-semibold text-slate-600 mb-1">
            Attachment Path (optional)
          </label>
          <input name="attachment_path"
                 type="text"
                 placeholder="e.g., /storage/diagnostics/cxr_2025-11-14.png"
                 class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
          <p class="text-[11px] text-slate-400">
            Use this if you have a stored image/PDF of the result. File upload handling can be added later.
          </p>
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
          Save Diagnostic
        </button>
      </div>
    </form>
  </div>
</div>
