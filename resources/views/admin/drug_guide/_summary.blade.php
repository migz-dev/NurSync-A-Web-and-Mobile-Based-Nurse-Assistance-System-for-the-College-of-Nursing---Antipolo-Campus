{{-- resources/views/admin/drug_guide/_summary.blade.php --}}
@php
    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $products */
    $from  = $products->firstItem() ?? 0;
    $to    = $products->lastItem()  ?? 0;
    $total = $products->total();
@endphp

<div class="flex items-center justify-between rounded-2xl bg-white border border-slate-200 shadow-sm px-4 py-3 text-sm">
  <div class="text-slate-600">
    Showing
    <span class="font-medium text-slate-900">{{ $from }}</span>
    –
    <span class="font-medium text-slate-900">{{ $to }}</span>
    of
    <span class="font-medium text-slate-900">{{ $total }}</span>
    results
  </div>

  {{-- Optional: current sort/per-page badges (feel free to remove if not needed) --}}
  <div class="hidden sm:flex items-center gap-2">
    @if(request('sort'))
      <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs text-slate-700">
        Sort: {{ str_replace('_',' ', request('sort')) }} {{ request('dir','asc') === 'desc' ? '↓' : '↑' }}
      </span>
    @endif
    @if(request('per'))
      <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs text-slate-700">
        Per page: {{ (int) request('per') }}
      </span>
    @endif
  </div>
</div>
