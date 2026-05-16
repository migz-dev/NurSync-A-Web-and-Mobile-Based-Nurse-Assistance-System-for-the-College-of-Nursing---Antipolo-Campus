{{-- resources/views/faculty/chartings/_modal-new-patient.blade.php --}}
<div id="modalNewPatient" class="hidden fixed inset-0 z-50">
  {{-- Background overlay --}}
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-modal-close></div>

  {{-- Centered Large Rectangle Form --}}
  <div class="relative mx-auto my-10 w-[95%] max-w-5xl rounded-2xl bg-white shadow-2xl border border-slate-200">
    {{-- Header --}}
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-2xl">
      <h3 class="text-xl font-bold text-slate-900">Add New Patient</h3>
      <button type="button" class="h-9 w-9 inline-flex items-center justify-center rounded-lg hover:bg-slate-100" data-modal-close>
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('faculty.chartings.patients.store') }}" class="p-6 space-y-6">
      @csrf

      {{-- 3-column grid --}}
      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        {{-- Column 1 --}}
        <div class="space-y-3">
          <label class="block text-xs font-semibold text-slate-600 mb-1">Hospital / MRN</label>
          <input name="hospital_no" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200" />

          <label class="block text-xs font-semibold text-slate-600 mb-1">Last Name *</label>
          <input name="last_name" required type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200" />

          <label class="block text-xs font-semibold text-slate-600 mb-1">Middle Name</label>
          <input name="middle_name" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200" />

          <label class="block text-xs font-semibold text-slate-600 mb-1">Sex</label>
          <select name="sex" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200">
            <option value="">—</option>
            <option value="M">Male</option>
            <option value="F">Female</option>
            <option value="I">Intersex</option>
            <option value="U">Unknown</option>
          </select>

          <label class="block text-xs font-semibold text-slate-600 mb-1">Date of Birth</label>
          <input name="dob" type="date" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200" />

          <label class="block text-xs font-semibold text-slate-600 mb-1">Age</label>
          <input name="age" type="number" min="0" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200" />
        </div>

        {{-- Column 2 --}}
        <div class="space-y-3">
          <label class="block text-xs font-semibold text-slate-600 mb-1">First Name *</label>
          <input name="first_name" required type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200" />

          <label class="block text-xs font-semibold text-slate-600 mb-1">Suffix</label>
          <input name="suffix" type="text" placeholder="Jr., III, etc." class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200" />

          <label class="block text-xs font-semibold text-slate-600 mb-1">Contact No.</label>
          <input name="contact_no" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200" />

          <label class="block text-xs font-semibold text-slate-600 mb-1">Address</label>
          <textarea name="address" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"></textarea>

          <label class="block text-xs font-semibold text-slate-600 mb-1">Attending Physician</label>
          <input name="attending_physician" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200" />

          <label class="block text-xs font-semibold text-slate-600 mb-1">Admitting Diagnosis</label>
          <input name="admitting_diagnosis" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200" />
        </div>

        {{-- Column 3 --}}
        <div class="space-y-3">
          <label class="block text-xs font-semibold text-slate-600 mb-1">Unit / Ward</label>
          <select name="ward" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200">
            <option value="">—</option>
            <option value="ms">Medical Ward (MS)</option>
            <option value="sr">Surgical Ward (SR)</option>
            <option value="medsurg">Medical–Surgical Ward</option>
            <option value="icu">ICU</option>
            <option value="ccu">CCU</option>
            <option value="nicu">NICU</option>
            <option value="picu">PICU</option>
            <option value="ob">OB</option>
            <option value="dr">DR</option>
            <option value="pedia">PEDIA</option>
            <option value="nursery">Nursery</option>
            <option value="er">ER</option>
            <option value="or">OR</option>
            <option value="recovery">PACU/Recovery</option>
            <option value="onco">Oncology</option>
            <option value="orthopedics">Orthopedics</option>
            <option value="neuro">Neurology</option>
            <option value="psych">Psychiatric</option>
            <option value="geriatrics">Geriatric</option>
            <option value="rehab">Rehabilitation</option>
            <option value="dialysis">Dialysis</option>
            <option value="burn">Burn Unit</option>
            <option value="triage">Triage</option>
            <option value="isolation">Isolation</option>
            <option value="infect">Infection Control</option>
            <option value="chn">Community Health Nursing</option>
          </select>

          <label class="block text-xs font-semibold text-slate-600 mb-1">Bed No.</label>
          <input name="bed_no" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200" />

          <label class="block text-xs font-semibold text-slate-600 mb-1">Admission Date / Time</label>
          <input name="admission_date" type="datetime-local" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200" />

          <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
          <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200">
            <option value="active" selected>Active</option>
            <option value="discharged">Discharged</option>
            <option value="archived">Archived</option>
          </select>

          <label class="block text-xs font-semibold text-slate-600 mb-1">Notes</label>
          <textarea name="notes" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200"></textarea>
        </div>
      </div>

      {{-- Footer buttons --}}
      <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-200 px-6 pb-6">
        <button type="button" class="rounded-lg px-5 py-2 text-sm border border-slate-300 text-slate-700 hover:bg-slate-100" data-modal-close>
          Cancel
        </button>
        <button type="submit" class="rounded-lg px-5 py-2 text-sm bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm active:scale-[.98]">
          Save Patient
        </button>
      </div>
    </form>
  </div>
</div>
