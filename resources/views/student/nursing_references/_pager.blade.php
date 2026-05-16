{{-- resources/views/student/nursing_references/_pager.blade.php --}}

@if ($items->hasPages())
  <div id="nr-pager"
       class="opacity-0 transition-opacity duration-300 flex items-center justify-end px-4 py-3 border-t border-slate-200 bg-white rounded-b-2xl">
    <div class="inline-flex items-center gap-1 text-[13px]">

      {{-- Prev --}}
      @if ($items->onFirstPage())
        <span class="px-2 py-1 text-slate-400 cursor-not-allowed">Prev</span>
      @else
        <a href="{{ $items->previousPageUrl() }}"
           data-page="{{ $items->currentPage() - 1 }}"
           class="nr-page px-2 py-1 rounded-md border border-slate-200 text-slate-700 hover:bg-slate-50">
          Prev
        </a>
      @endif

      {{-- Page Numbers --}}
      @foreach ($items->getUrlRange(1, $items->lastPage()) as $page => $url)
        @if ($page == $items->currentPage())
          <span class="px-3 py-1.5 rounded-md bg-slate-900 text-white font-medium">
            {{ $page }}
          </span>
        @else
          <a href="{{ $url }}"
             data-page="{{ $page }}"
             class="nr-page px-3 py-1.5 rounded-md border border-slate-200 text-slate-700 hover:bg-slate-50">
            {{ $page }}
          </a>
        @endif
      @endforeach

      {{-- Next --}}
      @if ($items->hasMorePages())
        <a href="{{ $items->nextPageUrl() }}"
           data-page="{{ $items->currentPage() + 1 }}"
           class="nr-page px-2 py-1 rounded-md border border-slate-200 text-slate-700 hover:bg-slate-50">
          Next
        </a>
      @else
        <span class="px-2 py-1 text-slate-400 cursor-not-allowed">Next</span>
      @endif

    </div>
  </div>
@endif
