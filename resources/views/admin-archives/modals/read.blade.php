<x-modal id="modalRead" class="max-w-xl">
  <div class="flex items-center justify-between">
    <h3 class="text-[15px] font-semibold">User details</h3>
    <button class="p-2 rounded-lg hover:bg-slate-100" data-modal-close>
      <i data-lucide="x" class="h-5 w-5"></i>
    </button>
  </div>

  <dl class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
    <div>
      <dt class="text-slate-500 text-[12px]">Name</dt>
      <dd class="font-medium">Juan Dela Cruz</dd>
    </div>
    <div>
      <dt class="text-slate-500 text-[12px]">Role</dt>
      <dd class="font-medium">Student</dd>
    </div>
    <div class="sm:col-span-2">
      <dt class="text-slate-500 text-[12px]">Email</dt>
      <dd class="font-medium">juan@sys.test.ph</dd>
    </div>
    <div>
      <dt class="text-slate-500 text-[12px]">Status</dt>
      <dd class="font-medium">Archived</dd>
    </div>
    <div>
      <dt class="text-slate-500 text-[12px]">Archived</dt>
      <dd class="font-medium">Sep 10, 2025</dd>
    </div>
  </dl>

  <div class="mt-5 flex items-center justify-end">
    <button class="px-3 py-2 rounded-xl text-[13px] bg-blue-600 text-white hover:bg-blue-700 inline-flex items-center gap-2" data-modal-close>
      <i data-lucide="check" class="h-4 w-4"></i> Close
    </button>
  </div>
</x-modal>
