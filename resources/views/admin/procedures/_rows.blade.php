{{-- resources/views/admin/procedures/_rows.blade.php --}}
@php $rendered = 0; @endphp

@foreach ($procedures as $p)
  @if (!empty($p->is_archived) && (int) $p->is_archived === 1)
    @continue {{-- hide archived from active list --}}
  @endif
  @php $rendered++; @endphp

  {{-- animated row --}}
  <tr class="hover:bg-slate-50 js-proc-row opacity-0">
    <td class="px-4 py-3 font-medium text-slate-900">
      {{ $p->title ?: '—' }}
    </td>

    <td class="px-4 py-3 text-slate-700">
      {{ $p->clinical_wards ?: '—' }}
    </td>

    <td class="px-4 py-3">
      @if ($p->status === 'published')
        <span
          class="inline-flex items-center gap-1.5 rounded-lg bg-green-50 text-green-700 px-2 py-1 text-[12px] font-medium">
          <i data-lucide="check-circle" class="h-3.5 w-3.5"></i> Published
        </span>
      @else
        <span
          class="inline-flex items-center gap-1.5 rounded-lg bg-yellow-50 text-yellow-700 px-2 py-1 text-[12px] font-medium">
          <i data-lucide="clock" class="h-3.5 w-3.5"></i> Draft
        </span>
      @endif
    </td>

    <td class="px-4 py-3 text-slate-700">
      {{ optional($p->created_at)->format('M d, Y') ?: '—' }}
    </td>

    <td class="px-4 py-3 text-slate-700">
      @if ($p->created_by_admin)
        {{ optional($p->adminCreator)->full_name ?? '—' }}
      @elseif ($p->created_by)
        {{ optional($p->author)->full_name ?? '—' }}
      @else
        —
      @endif
    </td>

    <td class="px-4 py-3">
      <div class="flex items-center justify-end gap-1.5">
        <a href="{{ route('admin.procedures.show', $p) }}"
           class="inline-flex items-center justify-center rounded-lg bg-blue-600 text-white p-2 hover:bg-blue-700"
           title="View">
          <i data-lucide="eye" class="h-4 w-4"></i>
        </a>

        <a href="{{ route('admin.procedures.edit', $p) }}"
           class="inline-flex items-center justify-center rounded-lg bg-yellow-400 text-slate-900 p-2 hover:brightness-95"
           title="Edit">
          <i data-lucide="pencil" class="h-4 w-4"></i>
        </a>

        {{-- ARCHIVE (NOT DELETE) --}}
        <button type="button"
                class="inline-flex items-center justify-center rounded-lg bg-orange-500 text-white p-2 hover:bg-orange-600"
                data-action="archive"
                data-procedure-id="{{ $p->id }}"
                data-procedure-title="{{ $p->title }}"
                data-url="{{ route('admin.procedures.archive', $p) }}">
          <i data-lucide="archive" class="h-4 w-4"></i>
        </button>
      </div>
    </td>
  </tr>
@endforeach

@if ($rendered === 0)
  <tr>
    <td colspan="6" class="px-4 py-8 text-center text-slate-500">
      No procedures found.
    </td>
  </tr>
@endif
