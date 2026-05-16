<x-modal id="modalDelete" class="max-w-md">
  <div class="flex items-center gap-3">
    <div class="h-9 w-9 rounded-xl bg-rose-100 text-rose-700 inline-flex items-center justify-center">
      <i data-lucide="trash-2" class="h-5 w-5"></i>
    </div>
    <div>
      <h3 class="text-[15px] font-semibold">Delete user permanently?</h3>
      <p class="text-[13px] text-slate-600">This action cannot be undone.</p>
    </div>
    <button class="ml-auto p-2 rounded-lg hover:bg-slate-100" data-modal-close>
      <i data-lucide="x" class="h-5 w-5"></i>
    </button>
  </div>
  <div class="mt-5 flex items-center justify-end gap-2">
    <button class="px-3 py-2 rounded-xl text-[13px] border border-slate-200 bg-white hover:bg-slate-50" data-modal-close>Cancel</button>
    <button id="deleteConfirmBtn"
            class="px-3 py-2 rounded-xl text-[13px] bg-rose-600 text-white hover:bg-rose-700 inline-flex items-center gap-2">
      <i data-lucide="trash-2" class="h-4 w-4"></i> Delete
    </button>
  </div>
</x-modal>