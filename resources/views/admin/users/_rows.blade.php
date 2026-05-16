@forelse ($usersPage as $u)
  @php
    $isActive = strtolower($u->status ?? '') === 'active';
    $pill = $roleStyles[$u->role] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'icon' => 'user'];
  @endphp

  <tr class="hover:bg-slate-50">
    <td class="px-4 py-3">
      <input type="checkbox" class="rounded border-slate-300">
    </td>

    {{-- Name + avatar --}}
    <td class="px-4 py-3">
      <div class="flex items-center gap-3">
        @if (!empty($u->avatar_url))
          <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="h-9 w-9 rounded-xl object-cover">
        @else
          <div class="h-9 w-9 rounded-xl bg-slate-100 flex items-center justify-center">
            <i data-lucide="user" class="h-4 w-4 text-slate-500"></i>
          </div>
        @endif
        <div>
          <div class="font-medium text-slate-900">{{ $u->name }}</div>
          <div class="text-[12px] text-slate-500">ID: {{ $u->id }}</div>
        </div>
      </div>
    </td>

    {{-- Email --}}
    <td class="px-4 py-3 text-slate-700">
      {{ $u->email ?? '—' }}
    </td>

    {{-- Role pill --}}
    <td class="px-4 py-3">
      <span class="inline-flex items-center gap-1.5 rounded-lg {{ $pill['bg'] }} {{ $pill['text'] }} px-2 py-1 text-[12px] font-medium">
        <i data-lucide="{{ $pill['icon'] }}" class="h-3.5 w-3.5"></i> {{ $u->role }}
      </span>
    </td>

    {{-- Status pill --}}
    <td class="px-4 py-3">
      @switch(strtolower($u->status ?? ''))
        @case('active')
          <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 text-emerald-700 px-2 py-1 text-[12px] font-medium">
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Active
          </span>
          @break

        @case('pending')
          <span class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 text-amber-700 px-2 py-1 text-[12px] font-medium">
            <span class="h-2 w-2 rounded-full bg-amber-500"></span> Pending
          </span>
          @break

        @case('rejected')
          <span class="inline-flex items-center gap-1.5 rounded-lg bg-rose-50 text-rose-700 px-2 py-1 text-[12px] font-medium">
            <span class="h-2 w-2 rounded-full bg-rose-500"></span> Rejected
          </span>
          @break

        @case('inactive')
          <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 text-slate-600 px-2 py-1 text-[12px] font-medium">
            <span class="h-2 w-2 rounded-full bg-slate-400"></span> Inactive
          </span>
          @break

        @case('archived')
          <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 text-slate-600 px-2 py-1 text-[12px] font-medium">
            <span class="h-2 w-2 rounded-full bg-slate-400"></span> Archived
          </span>
          @break

        @default
          <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 text-slate-600 px-2 py-1 text-[12px] font-medium">
            <span class="h-2 w-2 rounded-full bg-slate-400"></span> {{ $u->status ?? '—' }}
          </span>
      @endswitch
    </td>

    {{-- Created date --}}
    <td class="px-4 py-3 text-slate-700">
      {{ \Illuminate\Support\Carbon::parse($u->created_at)->format('M d, Y') }}
    </td>

    {{-- Actions --}}
    <td class="px-4 py-3">
      <div class="flex items-center justify-end gap-1.5">
        <button type="button"
          class="inline-flex items-center justify-center rounded-lg bg-blue-600 text-white p-2 hover:bg-blue-700"
          data-modal-target="modalRead">
          <i data-lucide="eye" class="h-4 w-4"></i>
        </button>

        <button type="button"
          class="inline-flex items-center justify-center rounded-lg bg-yellow-400 text-slate-900 p-2 hover:brightness-95"
          data-modal-target="modalUpdate">
          <i data-lucide="pencil" class="h-4 w-4"></i>
        </button>

        <button type="button"
          class="inline-flex items-center justify-center rounded-lg bg-orange-500 text-white p-2 hover:bg-orange-600"
          data-modal-target="modalArchive" data-user-id="{{ $u->id }}" data-user-name="{{ $u->name }}">
          <i data-lucide="archive" class="h-4 w-4"></i>
        </button>
      </div>
    </td>
  </tr>
@empty
  <tr>
    <td colspan="7" class="px-4 py-8 text-center text-slate-500">No users found.</td>
  </tr>
@endforelse
