<x-modal id="modalCreate">
  <div class="flex items-center justify-between">
    <h3 class="text-[15px] font-semibold">Create user</h3>
    <button class="p-2 rounded-lg hover:bg-slate-100" data-modal-close>
      <i data-lucide="x" class="h-5 w-5"></i>
    </button>
  </div>
  <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
    <div>
      <label class="text-[12px] text-slate-600">Full name</label>
      <input class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm" placeholder="e.g., Juan Dela Cruz">
    </div>
    <div>
      <label class="text-[12px] text-slate-600">Role</label>
      <select class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm">
        <option>Student</option>
        <option>Faculty</option>
        <option>Admin</option>
      </select>
    </div>
    <div class="sm:col-span-2">
      <label class="text-[12px] text-slate-600">Email</label>
      <input type="email" class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm" placeholder="name@sys.test.ph">
    </div>
    <div class="sm:col-span-2">
      <label class="text-[12px] text-slate-600">Temporary password</label>
      <input type="password" class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm" placeholder="••••••••">
    </div>
  </div>
  <div class="mt-5 flex items-center justify-end gap-2">
    <button class="px-3 py-2 rounded-xl text-[13px] border border-slate-200 bg-white hover:bg-slate-50" data-modal-close>Cancel</button>
    <button class="px-3 py-2 rounded-xl text-[13px] bg-green-600 text-white hover:bg-green-700 inline-flex items-center gap-2">
      <i data-lucide="check" class="h-4 w-4"></i> Create
    </button>
  </div>
</x-modal>
