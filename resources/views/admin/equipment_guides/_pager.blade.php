@php
  /** @var \Illuminate\Pagination\LengthAwarePaginator $items */
@endphp

<div id="eg-pager" class="flex items-center justify-between px-4 py-3 border-t border-slate-200 bg-slate-50">
  <div class="text-[12px] text-slate-600">
    @php
      $from = $items->firstItem() ?? 0;
      $to   = $items->lastItem()  ?? 0;
      $tot  = $items->total();
    @endphp
    Showing {{ $from }}–{{ $to }} of {{ $tot }} items
  </div>

  @if ($items->hasPages())
    <nav class="flex items-center gap-1" aria-label="Pagination">
      {{-- Prev --}}
      @if ($items->onFirstPage())
        <span class="rounded-lg border px-3 py-1.5 text-sm text-slate-400">‹ Prev</span>
      @else
        <a href="{{ $items->previousPageUrl() }}"
           class="js-page rounded-lg border px-3 py-1.5 text-sm text-slate-700 hover:bg-white"
           data-page="{{ $items->currentPage()-1 }}">‹ Prev</a>
      @endif

      {{-- Windowed pages --}}
      @php
        $window = 2;
        $start = max(1, $items->currentPage() - $window);
        $end   = min($items->lastPage(), $items->currentPage() + $window);
        if ($start > 1) {
          echo '<a href="'.$items->url(1).'" class="js-page rounded-lg border px-3 py-1.5 text-sm text-slate-700 hover:bg-white" data-page="1">1</a>';
          if ($start > 2) echo '<span class="px-1 text-slate-400 select-none">…</span>';
        }
        for ($p = $start; $p <= $end; $p++) {
          if ($p == $items->currentPage()) {
            echo '<span class="rounded-lg bg-slate-900 text-white px-3 py-1.5 text-sm">'.$p.'</span>';
          } else {
            echo '<a href="'.$items->url($p).'" class="js-page rounded-lg border px-3 py-1.5 text-sm text-slate-700 hover:bg-white" data-page="'.$p.'">'.$p.'</a>';
          }
        }
        if ($end < $items->lastPage()) {
          if ($end < $items->lastPage() - 1) echo '<span class="px-1 text-slate-400 select-none">…</span>';
          echo '<a href="'.$items->url($items->lastPage()).'" class="js-page rounded-lg border px-3 py-1.5 text-sm text-slate-700 hover:bg-white" data-page="'.$items->lastPage().'">'.$items->lastPage().'</a>';
        }
      @endphp

      {{-- Next --}}
      @if ($items->hasMorePages())
        <a href="{{ $items->nextPageUrl() }}"
           class="js-page rounded-lg border px-3 py-1.5 text-sm text-slate-700 hover:bg-white"
           data-page="{{ $items->currentPage()+1 }}">Next ›</a>
      @else
        <span class="rounded-lg border px-3 py-1.5 text-sm text-slate-400">Next ›</span>
      @endif
    </nav>
  @endif
</div>
