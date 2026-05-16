@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $items */
@endphp

@if ($items->isEmpty())
  <tr>
    <td colspan="4" class="px-4 py-8 text-center text-slate-500">
      No nursing references found.
    </td>
  </tr>
@else
  @foreach ($items as $ref)
    <tr class="js-nr-row opacity-0 hover:bg-slate-50/70">
      {{-- Title / URL --}}
      <td class="px-4 py-3 align-top">
        <div class="flex flex-col gap-0.5">
          <div class="flex items-center gap-2">
            <span class="text-[13px] font-semibold text-slate-900">
              {{ $ref->title }}
            </span>

            @if (!empty($ref->is_featured))
              <span class="inline-flex items-center rounded-full bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 text-[11px] font-medium">
                <i data-lucide="star" class="h-3 w-3 mr-1"></i> Featured
              </span>
            @endif
          </div>

          @if (!empty($ref->url))
            <a href="{{ $ref->url }}" target="_blank" rel="noopener"
               class="text-[12px] text-slate-500 hover:text-slate-700 flex items-center gap-1">
              <i data-lucide="external-link" class="h-3 w-3"></i>
              <span class="truncate max-w-xs sm:max-w-sm">{{ $ref->url }}</span>
            </a>
          @endif

          @if (!empty($ref->description))
            <p class="text-[12px] text-slate-500 line-clamp-2">
              {{ $ref->description }}
            </p>
          @endif
        </div>
      </td>

      {{-- Category --}}
      <td class="px-4 py-3 align-top">
        <span class="inline-flex items-center rounded-full bg-slate-50 border border-slate-200 px-2 py-0.5 text-[11px] text-slate-700">
          {{ $ref->category ?? '—' }}
        </span>
      </td>

      {{-- Source --}}
      <td class="px-4 py-3 align-top">
        <span class="text-[13px] text-slate-700">
          {{ $ref->source ?? '—' }}
        </span>
      </td>

      {{-- Actions --}}
      <td class="px-4 py-3 align-top">
        <div class="flex items-center justify-end gap-1.5">
          {{-- View / Open --}}
          @if (!empty($ref->url))
            <a href="{{ $ref->url }}" target="_blank" rel="noopener"
               class="inline-flex items-center justify-center rounded-lg bg-blue-600 text-white p-2 hover:bg-blue-700"
               title="Open Reference">
              <i data-lucide="eye" class="h-4 w-4"></i>
            </a>
          @else
            <a href="{{ route('admin.nursing_references.show', $ref->id) }}"
               class="inline-flex items-center justify-center rounded-lg bg-blue-600 text-white p-2 hover:bg-blue-700"
               title="View">
              <i data-lucide="eye" class="h-4 w-4"></i>
            </a>
          @endif

          {{-- Edit --}}
          <a href="{{ route('admin.nursing_references.edit', $ref->id) }}
             "class="inline-flex items-center justify-center rounded-lg bg-yellow-400 text-slate-900 p-2 hover:brightness-95"
             title="Edit">
            <i data-lucide="pencil" class="h-4 w-4"></i>
          </a>

          {{-- Delete --}}
          <form method="POST"
                action="{{ route('admin.nursing_references.destroy', $ref->id) }}"
                class="js-delete-form"
                data-title="{{ $ref->title }}">
            @csrf @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-red-600 text-white p-2 hover:bg-red-700"
                    title="Delete">
              <i data-lucide="trash-2" class="h-4 w-4"></i>
            </button>
          </form>
        </div>
      </td>
    </tr>
  @endforeach
@endif
