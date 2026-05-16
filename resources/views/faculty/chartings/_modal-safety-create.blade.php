{{-- resources/views/faculty/chartings/_modal-safety-create.blade.php --}}
<div id="modalCreateSafety" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-modal-close></div>

  <div class="relative mx-auto my-10 w-[95%] max-w-5xl rounded-2xl bg-white shadow-2xl border border-slate-200">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-2xl">
      <h3 class="text-xl font-bold text-slate-900">
        New Safety / Fall Risk Check
      </h3>
      <button type="button"
              class="h-9 w-9 inline-flex items-center justify-center rounded-lg hover:bg-slate-100"
              data-modal-close>
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    <form method="POST"
          action="{{ route('faculty.chartings.safety.store', $patient->id) }}"
          class="p-6 space-y-6">
      @csrf
      <input type="hidden" name="patient_id" value="{{ $patient->id }}">

      <div class="space-y-5">
        <div class="grid gap-4 md:grid-cols-3">
          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Date &amp; Time Checked *
            </label>
            <input name="checked_at"
                   type="datetime-local"
                   required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
          </div>

          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Tool / Scale Used
            </label>
            <input name="tool_used"
                   type="text"
                   placeholder="e.g., Morse Fall Scale, local fall risk tool"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
          </div>

          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Risk Level
            </label>
            <select name="risk_level"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200">
              <option value="">Select level</option>
              <option value="Low">Low</option>
              <option value="Moderate">Moderate</option>
              <option value="High">High</option>
            </select>
          </div>
        </div>

        {{-- Environmental and basic safety checks --}}
        <div class="grid gap-4 md:grid-cols-2">
          <div class="space-y-3">
            <p class="text-xs font-semibold text-slate-600 mb-1">
              Safety Items Checked
            </p>

            <div class="flex items-center gap-2">
              <input id="bed_in_low_position"
                     name="bed_in_low_position"
                     type="checkbox"
                     value="1"
                     class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-200">
              <label for="bed_in_low_position" class="text-xs text-slate-700">
                Bed in lowest position
              </label>
            </div>

            <div class="flex items-center gap-2">
              <input id="side_rails_up"
                     name="side_rails_up"
                     type="checkbox"
                     value="1"
                     class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-200">
              <label for="side_rails_up" class="text-xs text-slate-700">
                Side rails up as ordered
              </label>
            </div>

            <div class="flex items-center gap-2">
              <input id="call_bell_within_reach"
                     name="call_bell_within_reach"
                     type="checkbox"
                     value="1"
                     class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-200">
              <label for="call_bell_within_reach" class="text-xs text-slate-700">
                Call bell within reach
              </label>
            </div>
          </div>

          <div class="space-y-3">
            <div class="flex items-center gap-2">
              <input id="non_slip_footwear"
                     name="non_slip_footwear"
                     type="checkbox"
                     value="1"
                     class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-200">
              <label for="non_slip_footwear" class="text-xs text-slate-700">
                Non-slip footwear used (if ambulatory)
              </label>
            </div>

            <div class="flex items-center gap-2">
              <input id="environment_safe"
                     name="environment_safe"
                     type="checkbox"
                     value="1"
                     class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-200">
              <label for="environment_safe" class="text-xs text-slate-700">
                Environment free of clutter / spills
              </label>
            </div>

            <div class="flex items-center gap-2">
              <input id="restraints_in_place"
                     name="restraints_in_place"
                     type="checkbox"
                     value="1"
                     class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-200">
              <label for="restraints_in_place" class="text-xs text-slate-700">
                Restraints in place as ordered (if applicable)
              </label>
            </div>
          </div>
        </div>

        <div class="space-y-2">
          <label class="block text-xs font-semibold text-slate-600 mb-1">
            Interventions / Education
          </label>
          <textarea name="interventions"
                    rows="3"
                    placeholder="E.g., fall risk signage, instructed patient to call before ambulating, family informed, etc."
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>

        <div class="space-y-2">
          <label class="block text-xs font-semibold text-slate-600 mb-1">
            Notes
          </label>
          <textarea name="notes"
                    rows="3"
                    placeholder="Other safety concerns, incidents, or monitoring notes."
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
          Save Safety Check
        </button>
      </div>
    </form>
  </div>
</div>
