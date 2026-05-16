{{-- resources/views/faculty/chartings/_modal-neuro-create.blade.php --}}
<div id="modalCreateNeuro" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-modal-close></div>

  <div class="relative mx-auto my-10 w-[95%] max-w-5xl rounded-2xl bg-white shadow-2xl border border-slate-200">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-2xl">
      <h3 class="text-xl font-bold text-slate-900">
        New Neuro Observation
      </h3>
      <button type="button"
              class="h-9 w-9 inline-flex items-center justify-center rounded-lg hover:bg-slate-100"
              data-modal-close>
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    <form method="POST"
          action="{{ route('faculty.chartings.neuro.store', $patient->id) }}"
          class="p-6 space-y-6">
      @csrf
      <input type="hidden" name="patient_id" value="{{ $patient->id }}">

      <div class="space-y-5">
        <div class="grid gap-4 md:grid-cols-3">
          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Date &amp; Time Observed *
            </label>
            <input name="observed_at"
                   type="datetime-local"
                   required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
          </div>

          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Level of Consciousness
            </label>
            <input name="loc"
                   type="text"
                   placeholder="e.g., alert, drowsy, obtunded, comatose"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
          </div>

          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Orientation
            </label>
            <input name="orientation"
                   type="text"
                   placeholder="e.g., oriented x3, disoriented to time"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"/>
          </div>
        </div>

        {{-- GCS --}}
        <div class="space-y-2">
          <p class="text-xs font-semibold text-slate-600 mb-1">
            Glasgow Coma Scale (GCS)
          </p>
          <div class="grid gap-4 md:grid-cols-4">
            <div class="space-y-1">
              <label class="block text-[11px] font-medium text-slate-600">
                Eye (E)
              </label>
              <input name="gcs_eye"
                     type="number"
                     min="1"
                     max="4"
                     placeholder="E"
                     class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-emerald-200"/>
            </div>
            <div class="space-y-1">
              <label class="block text-[11px] font-medium text-slate-600">
                Verbal (V)
              </label>
              <input name="gcs_verbal"
                     type="number"
                     min="1"
                     max="5"
                     placeholder="V"
                     class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-emerald-200"/>
            </div>
            <div class="space-y-1">
              <label class="block text-[11px] font-medium text-slate-600">
                Motor (M)
              </label>
              <input name="gcs_motor"
                     type="number"
                     min="1"
                     max="6"
                     placeholder="M"
                     class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-emerald-200"/>
            </div>
            <div class="space-y-1">
              <label class="block text-[11px] font-medium text-slate-600">
                Total (auto or manual)
              </label>
              <input name="gcs_total"
                     type="number"
                     min="3"
                     max="15"
                     placeholder="Total"
                     class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-emerald-200"/>
            </div>
          </div>
        </div>

        {{-- Pupils --}}
        <div class="space-y-2">
          <p class="text-xs font-semibold text-slate-600 mb-1">
            Pupils
          </p>
          <div class="grid gap-4 md:grid-cols-2">
            <div class="space-y-1">
              <label class="block text-[11px] font-medium text-slate-600">
                Left – Size / Reaction
              </label>
              <input name="pupil_left"
                     type="text"
                     placeholder="e.g., 3 mm, brisk; or 5 mm, non-reactive"
                     class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-emerald-200"/>
            </div>
            <div class="space-y-1">
              <label class="block text-[11px] font-medium text-slate-600">
                Right – Size / Reaction
              </label>
              <input name="pupil_right"
                     type="text"
                     placeholder="e.g., 3 mm, brisk; or 5 mm, sluggish"
                     class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-emerald-200"/>
            </div>
          </div>
        </div>

        {{-- Motor / sensation --}}
        <div class="grid gap-4 md:grid-cols-2">
          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Motor Function
            </label>
            <textarea name="motor_function"
                      rows="3"
                      placeholder="E.g., moves all extremities, weakness LUE/LLE, decorticate/decerebrate posturing."
                      class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
          </div>
          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">
              Sensation
            </label>
            <textarea name="sensation"
                      rows="3"
                      placeholder="E.g., intact light touch, numbness, tingling, decreased sensation on right side."
                      class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
          </div>
        </div>

        <div class="space-y-2">
          <label class="block text-xs font-semibold text-slate-600 mb-1">
            Notes / Changes from Baseline
          </label>
          <textarea name="notes"
                    rows="3"
                    placeholder="Document trends, acute changes, MD notification, and other neuro-related observations."
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
          Save Neuro Obs
        </button>
      </div>
    </form>
  </div>
</div>
