{{-- resources/views/admin/patients/_pager.blade.php --}}
@php
  /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $patients */
  $cur  = $patients->currentPage();
  $last = max(1, $patients->lastPage());
  $win  = 5;                    // how many page numbers to show in the middle window
  $half = intdiv($win, 2);

  $from = max(1, $cur - $half);
  $to   = min($last, $from + $win - 1);
  $from = max(1, $to - $win + 1);
@endphp

@if ($last > 1)
  <div id="ap-pager" class="flex items-center justify-end px-4 py-3 border-t border-slate-200 bg-slate-50">
    <nav class="flex items-center gap-1 text-[13px]">

      {{-- First --}}
      @if ($cur === 1)
        <button
          class="h-9 px-3 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white opacity-50 cursor-not-allowed"
          type="button"
          disabled
        >
          <i data-lucide="chevrons-left" class="h-4 w-4 mr-1"></i>
          <span>First</span>
        </button>
      @else
        <a
          href="{{ $patients->url(1) }}"
          class="ap-page h-9 px-3 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50"
          data-page="1"
        >
          <i data-lucide="chevrons-left" class="h-4 w-4 mr-1"></i>
          <span>First</span>
        </a>
      @endif

      {{-- Prev --}}
      @php $prev = max(1, $cur - 1); @endphp
      @if ($cur === 1)
        <button
          class="h-9 px-3 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white opacity-50 cursor-not-allowed"
          type="button"
          disabled
        >
          <i data-lucide="chevron-left" class="h-4 w-4"></i>
        </button>
      @else
        <a
          href="{{ $patients->url($prev) }}"
          class="ap-page h-9 px-3 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50"
          data-page="{{ $prev }}"
        >
          <i data-lucide="chevron-left" class="h-4 w-4"></i>
        </a>
      @endif

      {{-- Page numbers --}}
      @for ($page = $from; $page <= $to; $page++)
        @if ($page === $cur)
          <span
            class="h-9 px-3 inline-flex items-center justify-center rounded-lg border border-emerald-500 bg-emerald-50 text-emerald-700 font-semibold"
          >
            {{ $page }}
          </span>
        @else
          <a
            href="{{ $patients->url($page) }}"
            class="ap-page h-9 px-3 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50"
            data-page="{{ $page }}"
          >
            {{ $page }}
          </a>
        @endif
      @endfor

      {{-- Next --}}
      @php $next = min($last, $cur + 1); @endphp
      @if ($cur === $last)
        <button
          class="h-9 px-3 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white opacity-50 cursor-not-allowed"
          type="button"
          disabled
        >
          <i data-lucide="chevron-right" class="h-4 w-4"></i>
        </button>
      @else
        <a
          href="{{ $patients->url($next) }}"
          class="ap-page h-9 px-3 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50"
          data-page="{{ $next }}"
        >
          <i data-lucide="chevron-right" class="h-4 w-4"></i>
        </a>
      @endif

      {{-- Last --}}
      @if ($cur === $last)
        <button
          class="h-9 px-3 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white opacity-50 cursor-not-allowed"
          type="button"
          disabled
        >
          <span>Last</span>
          <i data-lucide="chevrons-right" class="h-4 w-4 ml-1"></i>
        </button>
      @else
        <a
          href="{{ $patients->url($last) }}"
          class="ap-page h-9 px-3 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50"
          data-page="{{ $last }}"
        >
          <span>Last</span>
          <i data-lucide="chevrons-right" class="h-4 w-4 ml-1"></i>
        </a>
      @endif

    </nav>
  </div>
@endif
