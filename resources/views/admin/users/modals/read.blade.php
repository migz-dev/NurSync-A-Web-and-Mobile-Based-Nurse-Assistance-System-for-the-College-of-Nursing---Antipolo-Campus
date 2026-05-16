<x-modal id="modalRead" class="max-w-xl">
  <div class="flex items-center justify-between">
    <h3 class="text-[15px] font-semibold">User Details</h3>
    <button class="p-2 rounded-lg hover:bg-slate-100" data-modal-close>
      <i data-lucide="x" class="h-5 w-5"></i>
    </button>
  </div>

  <!-- Header: Avatar + Name/Email -->
  <div class="mt-4 flex items-center gap-3">
    <img id="view_avatar" src="{{ asset('placeholder-avatar.png') }}"
         alt="Avatar" class="h-12 w-12 rounded-xl object-cover bg-slate-100">
    <div class="min-w-0">
      <div id="view_name" class="font-medium truncate">—</div>
      <div id="view_email" class="text-[13px] text-slate-600 truncate">—</div>
    </div>
  </div>

  <!-- Details -->
  <dl class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
    <div>
      <dt class="text-slate-500 text-[12px]">Role</dt>
      <dd id="view_role" class="font-medium">—</dd>
    </div>

    <div>
      <dt class="text-slate-500 text-[12px]">Status</dt>
      <dd id="view_status" class="font-medium">—</dd>
    </div>

    <!-- New: Nurse Type -->
    <div class="sm:col-span-2">
      <dt class="text-slate-500 text-[12px]">Nurse Type</dt>
      <dd id="view_nurse_type" class="font-medium">—</dd>
    </div>

    <div>
      <dt class="text-slate-500 text-[12px]">Created</dt>
      <dd id="view_created" class="font-medium">—</dd>
    </div>

    <!-- Optional: Archived (shown only if present) -->
    <div id="view_archived_wrap" class="hidden">
      <dt class="text-slate-500 text-[12px]">Archived</dt>
      <dd id="view_archived" class="font-medium">—</dd>
    </div>
  </dl>

  <div class="mt-6 flex items-center justify-end">
    <button class="px-3 py-2 rounded-xl text-[13px] bg-blue-600 text-white hover:bg-blue-700 inline-flex items-center gap-2" data-modal-close>
      <i data-lucide="check" class="h-4 w-4"></i> Close
    </button>
  </div>
</x-modal>
