{{-- resources/views/admin/emergency/_pager.blade.php --}}

@if ($protocols instanceof \Illuminate\Pagination\LengthAwarePaginator && $protocols->hasPages())
  @php
    $current = $protocols->currentPage();
    $last    = $protocols->lastPage();
  @endphp

  <div id="ep-pager"
       class="flex items-center justify-between px-4 py-3 border-t border-slate-200 bg-slate-50 text-xs text-slate-500">
    <div>
      Showing
      <span class="font-semibold">{{ $protocols->firstItem() }}</span>
      –
      <span class="font-semibold">{{ $protocols->lastItem() }}</span>
      of
      <span class="font-semibold">{{ $protocols->total() }}</span>
      protocols
    </div>

    <div class="flex items-center gap-1">
      {{-- Previous --}}
      <button
        type="button"
        class="ep-page inline-flex items-center rounded-lg border px-2.5 py-1
               {{ $current <= 1 ? 'opacity-40 cursor-default border-slate-200' : 'border-slate-200 bg-white hover:bg-slate-50' }}"
        @if($current > 1) data-page="{{ $current - 1 }}" @endif
        @if($current <= 1) disabled @endif
      >
        <i data-lucide="chevron-left" class="h-3 w-3"></i>
      </button>

      {{-- Page numbers (compact: first, current±1, last) --}}
      @php
        $start = max(1, $current - 1);
        $end   = min($last, $current + 1);
      @endphp

      @for ($page = 1; $page <= $last; $page++)
        @if ($last > 5 && $page != 1 && $page != $last && ($page < $start || $page > $end))
          @if ($page == 2 || $page == $last - 1)
            <span class="px-1">…</span>
          @endif
          @continue
        @endif

        <button
          type="button"
          class="ep-page inline-flex items-center rounded-lg border px-2.5 py-1 text-[11px]
                 {{ $page == $current
                    ? 'bg-slate-900 text-white border-slate-900'
                    : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}"
          data-page="{{ $page }}">
          {{ $page }}
        </button>
      @endfor

      {{-- Next --}}
      <button
        type="button"
        class="ep-page inline-flex items-center rounded-lg border px-2.5 py-1
               {{ $current >= $last ? 'opacity-40 cursor-default border-slate-200' : 'border-slate-200 bg-white hover:bg-slate-50' }}"
        @if($current < $last) data-page="{{ $current + 1 }}" @endif
        @if($current >= $last) disabled @endif
      >
        <i data-lucide="chevron-right" class="h-3 w-3"></i>
      </button>
    </div>
  </div>
@else
  <div id="ep-pager"
       class="flex items-center justify-end px-4 py-3 border-t border-slate-200 bg-slate-50 text-xs text-slate-500">
    {{-- No pagination needed --}}
  </div>
@endif
