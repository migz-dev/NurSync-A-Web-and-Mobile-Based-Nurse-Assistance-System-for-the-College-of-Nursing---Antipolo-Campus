@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $items */
@endphp

<div id="nr-pager" class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-1 text-[12px]">
  {{-- Summary --}}
  <div class="text-slate-500">
    @if ($items->total() > 0)
      @php
        $from = ($items->currentPage() - 1) * $items->perPage() + 1;
        $to   = $from + $items->count() - 1;
      @endphp
      Showing <span class="font-medium text-slate-700">{{ $from }}</span>
      to <span class="font-medium text-slate-700">{{ $to }}</span>
      of <span class="font-medium text-slate-700">{{ $items->total() }}</span> references
    @else
      No references to display.
    @endif
  </div>

  {{-- Pager controls --}}
  @if ($items->hasPages())
    @php
      $current = $items->currentPage();
      $last    = $items->lastPage();
      $window  = 2;
      $start   = max(1, $current - $window);
      $end     = min($last, $current + $window);
    @endphp

    <div class="flex items-center gap-1 justify-end">
      {{-- Prev --}}
      @if ($current > 1)
        <button type="button"
                class="nr-page inline-flex items-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-[12px] text-slate-700 hover:bg-slate-100"
                data-page="{{ $current - 1 }}">
          <i data-lucide="chevron-left" class="h-3.5 w-3.5 mr-0.5"></i>
          Prev
        </button>
      @endif

      {{-- First + ellipsis --}}
      @if ($start > 1)
        <button type="button"
                class="nr-page inline-flex items-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-[12px] text-slate-700 hover:bg-slate-100"
                data-page="1">
          1
        </button>
        @if ($start > 2)
          <span class="px-1 text-[11px] text-slate-400">…</span>
        @endif
      @endif

      {{-- Window --}}
      @for ($page = $start; $page <= $end; $page++)
        @if ($page == $current)
          <span class="inline-flex items-center rounded-lg bg-slate-900 text-white px-2.5 py-1.5 text-[12px] font-medium">
            {{ $page }}
          </span>
        @else
          <button type="button"
                  class="nr-page inline-flex items-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-[12px] text-slate-700 hover:bg-slate-100"
                  data-page="{{ $page }}">
            {{ $page }}
          </button>
        @endif
      @endfor

      {{-- Ellipsis + last --}}
      @if ($end < $last)
        @if ($end < $last - 1)
          <span class="px-1 text-[11px] text-slate-400">…</span>
        @endif
        <button type="button"
                class="nr-page inline-flex items-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-[12px] text-slate-700 hover:bg-slate-100"
                data-page="{{ $last }}">
          {{ $last }}
        </button>
      @endif

      {{-- Next --}}
      @if ($current < $last)
        <button type="button"
                class="nr-page inline-flex items-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-[12px] text-slate-700 hover:bg-slate-100"
                data-page="{{ $current + 1 }}">
          Next
          <i data-lucide="chevron-right" class="h-3.5 w-3.5 ml-0.5"></i>
        </button>
      @endif
    </div>
  @endif
</div>
