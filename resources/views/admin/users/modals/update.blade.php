<x-modal id="modalUpdate" class="max-w-xl">
  <form id="edit_form" method="POST" class="contents">
    @csrf
    {{-- JS sets form.action => route('admin.users.update', id) --}}
    <input type="hidden" id="edit_id" name="id" />

    <div class="flex items-center justify-between">
      <h3 class="text-[15px] font-semibold">Update user</h3>
      <button type="button" class="p-2 rounded-lg hover:bg-slate-100" data-modal-close>
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
      {{-- Full name --}}
      <div>
        <label for="edit_name" class="text-[12px] text-slate-600">Full name</label>
        <input id="edit_name" name="name" type="text"
               class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm"
               placeholder="Full name" />
      </div>

      {{-- Role (display-only for this controller; CI/Student) --}}
      <div>
        <label for="edit_role" class="text-[12px] text-slate-600">Role</label>
        <select id="edit_role" name="role"
                class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm"
                disabled>
          <option>Clinical Instructor</option>
          <option>Student Nurse</option>
          <option>Admin</option>
        </select>
        {{-- If you want to actually edit role later, remove `disabled` and handle in controller. --}}
      </div>

      {{-- Email --}}
      <div class="sm:col-span-2">
        <label for="edit_email" class="text-[12px] text-slate-600">Email</label>
        <input id="edit_email" name="email" type="email"
               class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm"
               placeholder="email@example.com" />
      </div>

      {{-- Status (options for both CI and Student; the controller maps appropriately) --}}
      <div class="sm:col-span-2">
        <label for="edit_status" class="text-[12px] text-slate-600">Status</label>
        <select id="edit_status" name="status"
                class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm">
          <optgroup label="Clinical Instructor">
            <option value="approved">approved</option>
            <option value="pending">pending</option>
            <option value="rejected">rejected</option>
            <option value="archived">archived</option>
          </optgroup>
          <optgroup label="Student">
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
            <option value="Archived">Archived</option>
          </optgroup>
        </select>
      </div>

      {{-- Optional: Reset password (not handled server-side yet) --}}
      <div class="sm:col-span-2">
        <label for="edit_password" class="text-[12px] text-slate-600">Reset password (optional)</label>
        <input id="edit_password" name="password" type="password"
               class="mt-1 w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm"
               placeholder="••••••••" />
      </div>
    </div>

    <div class="mt-5 flex items-center justify-end gap-2">
      <button type="button" class="px-3 py-2 rounded-xl text-[13px] border border-slate-200 bg-white hover:bg-slate-50" data-modal-close>
        Cancel
      </button>
      <button type="submit" class="px-3 py-2 rounded-xl text-[13px] bg-yellow-400 text-slate-900 hover:brightness-95 inline-flex items-center gap-2">
        <i data-lucide="save" class="h-4 w-4"></i>
        Save changes
      </button>
    </div>
  </form>
</x-modal>
