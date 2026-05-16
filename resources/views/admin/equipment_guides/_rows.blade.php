@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $items */
@endphp

@if ($items->count() === 0)
    <tr>
        <td colspan="4" class="px-4 py-8 text-center text-slate-500">No results.</td>
    </tr>
@else
    @foreach ($items as $it)
        {{-- Animated row: picked up by animateRows() in index.blade --}}
        <tr class="js-eg-row opacity-0 text-slate-800">
            <td class="px-4 py-3 align-top">
                <div class="font-medium">{{ $it->item_name }}</div>

                @if (!empty($it->typical_uses))
                    <div class="text-[12px] text-slate-500 mt-0.5 line-clamp-2">
                        Uses: {{ $it->typical_uses }}
                    </div>
                @endif

                @if (!empty($it->related_procedures_or_tasks))
                    <div class="text-[12px] text-slate-500 mt-0.5 line-clamp-1">
                        Related: {{ $it->related_procedures_or_tasks }}
                    </div>
                @endif
            </td>

            <td class="px-4 py-3 align-top text-slate-600">
                {{ $it->category ?? '—' }}
            </td>

            <td class="px-4 py-3 align-top text-slate-600">
                {{ $it->ward_scope ?? '—' }}
            </td>

            {{-- Actions --}}
            <td class="px-4 py-3">
                <div class="flex items-center justify-end gap-1.5">
                    {{-- View --}}
                    <a href="{{ route('admin.equipment_guide.show', $it->id) }}"
                       class="inline-flex items-center justify-center rounded-lg bg-blue-600 text-white p-2 hover:bg-blue-700"
                       title="View">
                        <i data-lucide="eye" class="h-4 w-4"></i>
                    </a>

                    {{-- Edit --}}
                    <a href="{{ route('admin.equipment_guide.edit', $it->id) }}"
                       class="inline-flex items-center justify-center rounded-lg bg-yellow-400 text-slate-900 p-2 hover:brightness-95"
                       title="Edit">
                        <i data-lucide="pencil" class="h-4 w-4"></i>
                    </a>

                    {{-- Delete --}}
                    <form method="POST"
                          action="{{ route('admin.equipment_guide.destroy', $it->id) }}"
                          class="js-delete-form"
                          data-title="{{ $it->item_name ?? 'this item' }}">
                        @csrf
                        @method('DELETE')
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
