{{-- resources/views/admin/emergency/_rows.blade.php --}}

@if ($protocols->isEmpty())
  <tr>
    <td colspan="5" class="px-4 py-6 text-center text-[13px] text-slate-500">
      No emergency protocols found.
    </td>
  </tr>
@else
  @foreach ($protocols as $p)
    @php
      // Determine owner: Faculty first, else Admin, else fallback
      if ($p->faculty) {
          $owner = $p->faculty->full_name
            ?? $p->faculty->name
            ?? $p->faculty->email
            ?? ('Faculty #'.$p->faculty->id);
      } elseif ($p->createdByAdmin) {
          $adminName = $p->createdByAdmin->full_name
            ?? $p->createdByAdmin->name
            ?? $p->createdByAdmin->email
            ?? ('Admin #'.$p->createdByAdmin->id);

          $owner = 'Admin • '.$adminName;
      } else {
          $owner = 'Admin (system)';
      }

      $sev = $p->severity ?? 'Critical';
      if ($sev === 'Critical') {
          $sevBg = 'bg-red-50 text-red-700 border-red-100';
      } elseif ($sev === 'Moderate') {
          $sevBg = 'bg-amber-50 text-amber-700 border-amber-100';
      } else {
          $sevBg = 'bg-sky-50 text-sky-700 border-sky-100';
      }

      $status = $p->status ?? 'draft';
      $statusClasses = match ($status) {
          'published' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
          'archived'  => 'bg-slate-100 text-slate-600 border-slate-200',
          default     => 'bg-slate-50 text-slate-700 border-slate-200',
      };
    @endphp

    <tr class="js-ep-row opacity-0 hover:bg-slate-50/60">
      {{-- Protocol / Owner --}}
      <td class="px-4 py-3 align-top">
        <div class="flex flex-col gap-1">
          <a href="{{ route('admin.emergency_protocols.show', $p->id) }}"
             class="text-[13px] font-semibold text-slate-900 hover:text-slate-700">
            {{ $p->title }}
          </a>

          @if ($p->summary)
            <p class="text-[11px] text-slate-500 line-clamp-2">
              {{ $p->summary }}
            </p>
          @endif

          <p class="text-[11px] text-slate-400">
            Owner:
            <span class="font-medium text-slate-600">
              {{ $owner }}
            </span>
          </p>
        </div>
      </td>

      {{-- Category / Ward --}}
      <td class="px-4 py-3 align-top text-[12px] text-slate-600">
        <div>{{ $p->category ?? '—' }}</div>
        <div class="text-[11px] text-slate-500">
          {{ $p->ward ?? 'All wards / units' }}
        </div>
      </td>

      {{-- Severity / Status --}}
      <td class="px-4 py-3 align-top text-[12px]">
        <div class="inline-flex items-center rounded-full border px-2 py-0.5 text-[11px] font-medium {{ $sevBg }}">
          <i data-lucide="activity" class="h-3 w-3 mr-1"></i>
          {{ $p->severity ?? 'Critical' }}
        </div>
        <div class="mt-1 inline-flex items-center rounded-full border px-2 py-0.5 text-[11px] font-medium {{ $statusClasses }}">
          {{ ucfirst($status) }}
        </div>
      </td>

      {{-- Updated --}}
      <td class="px-4 py-3 align-top text-[11px] text-slate-500">
        {{ $p->updated_at?->diffForHumans() ?? '—' }}
      </td>

      {{-- Actions --}}
      <td class="px-4 py-3">
        <div class="flex items-center justify-end gap-1.5">
          {{-- View --}}
          <a href="{{ route('admin.emergency_protocols.show', $p) }}"
             class="inline-flex items-center justify-center rounded-lg bg-blue-600 text-white p-2 hover:bg-blue-700"
             title="View">
            <i data-lucide="eye" class="h-4 w-4"></i>
          </a>

          {{-- Edit --}}
          <a href="{{ route('admin.emergency_protocols.edit', $p) }}"
             class="inline-flex items-center justify-center rounded-lg bg-yellow-400 text-slate-900 p-2 hover:brightness-95"
             title="Edit">
            <i data-lucide="pencil" class="h-4 w-4"></i>
          </a>

          {{-- Archive (SweetAlert-confirmed via .ep-archive-form) --}}
          @if ($status !== 'archived')
            <form method="POST"
                  action="{{ route('admin.emergency_protocols.archive', $p) }}"
                  class="inline ep-archive-form"
                  data-title="{{ $p->title }}">
              @csrf
              @method('PATCH')
              <button type="submit"
                      class="inline-flex items-center justify-center rounded-lg bg-orange-500 text-white p-2 hover:bg-orange-600"
                      title="Archive">
                <i data-lucide="archive" class="h-4 w-4"></i>
              </button>
            </form>
          @endif
        </div>
      </td>
    </tr>
  @endforeach
@endif
