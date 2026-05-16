<x-modal id="modalArchive" class="max-w-md">
  <div class="flex items-center gap-3">
    <div class="h-9 w-9 rounded-xl bg-orange-100 text-orange-700 inline-flex items-center justify-center">
      <i data-lucide="alert-triangle" class="h-5 w-5"></i>
    </div>
    <div>
      <h3 class="text-[15px] font-semibold">Archive user?</h3>
      <p class="text-[13px] text-slate-600">The user will lose access, but their data will be retained.</p>
    </div>
    <button class="ml-auto p-2 rounded-lg hover:bg-slate-100" data-modal-close>
      <i data-lucide="x" class="h-5 w-5"></i>
    </button>
  </div>
  <div class="mt-5 flex items-center justify-end gap-2">
    <button class="px-3 py-2 rounded-xl text-[13px] border border-slate-200 bg-white hover:bg-slate-50" data-modal-close>Cancel</button>
    <button id="archiveConfirmBtn"
            class="px-3 py-2 rounded-xl text-[13px] bg-orange-500 text-white hover:bg-orange-600 inline-flex items-center gap-2">
      <i data-lucide="archive" class="h-4 w-4"></i> Archive
    </button>
  </div>
</x-modal>
