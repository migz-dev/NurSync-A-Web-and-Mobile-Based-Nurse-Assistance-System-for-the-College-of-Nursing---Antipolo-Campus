{{-- resources/views/admin/drug_guide/_rows.blade.php --}}
@php($paginator = $products)

@forelse ($paginator as $p)
<tr class="js-dg-row opacity-0 hover:bg-slate-50">
  {{-- Brand / Substance --}}
  <td class="px-4 py-3 align-top">
    <div class="font-medium text-slate-900">{{ $p->brand_name ?? '—' }}</div>
    <div class="text-[12px] text-slate-500">{{ $p->substance->name ?? '—' }}</div>
    @if(!empty($p->dosage_strength))
      <div class="text-[12px] text-slate-400">{{ $p->dosage_strength }}</div>
    @endif
  </td>

  {{-- Category --}}
  <td class="px-4 py-3 text-slate-700 align-top">
    {{ $p->category->name ?? '—' }}
  </td>

  {{-- Manufacturer --}}
  <td class="px-4 py-3 text-slate-700 align-top">
    {{ $p->manufacturer->name ?? '—' }}
    @if(!empty($p->manufacturer?->country))
      <div class="text-[12px] text-slate-400">{{ $p->manufacturer->country }}</div>
    @endif
  </td>

  {{-- Registration Number --}}
  <td class="px-4 py-3 text-slate-700 align-top">
    {{ $p->registration_number ?? '—' }}
  </td>

  {{-- Issued --}}
  <td class="px-4 py-3 text-slate-700 align-top">
    @if(!empty($p->issued_at))
      <span class="text-slate-800">{{ $p->issued_at->format('Y-m-d') }}</span>
    @else
      <span class="text-slate-400">—</span>
    @endif
  </td>

  {{-- Expires --}}
  <td class="px-4 py-3 text-slate-700 align-top">
    @if(!empty($p->expires_at))
      <span class="{{ (now()->gt($p->expires_at)) ? 'text-red-600 font-medium' : 'text-slate-800' }}">
        {{ $p->expires_at->format('Y-m-d') }}
      </span>
    @else
      <span class="text-slate-400">—</span>
    @endif
  </td>

  {{-- Actions --}}
  <td class="px-4 py-3 align-top">
    <div class="flex items-center justify-end gap-1.5">
      <a href="{{ route('admin.drug_guide.show', $p->id) }}"
         class="inline-flex items-center justify-center rounded-lg bg-blue-600 text-white p-2 hover:bg-blue-700"
         title="View">
        <i data-lucide="eye" class="h-4 w-4"></i>
      </a>

      <a href="{{ route('admin.drug_guide.edit', $p->id) }}"
         class="inline-flex items-center justify-center rounded-lg bg-yellow-400 text-slate-900 p-2 hover:brightness-95"
         title="Edit">
        <i data-lucide="pencil" class="h-4 w-4"></i>
      </a>

      <form method="POST"
            action="{{ route('admin.drug_guide.destroy', $p->id) }}"
            class="js-delete-form"
            data-title="{{ $p->brand_name ?? 'this product' }}">
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
@empty
<tr>
  <td colspan="7" class="px-4 py-8 text-center text-slate-500">No products found.</td>
</tr>
@endforelse
