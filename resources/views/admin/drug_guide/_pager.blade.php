{{-- resources/views/admin/drug_guide/_pager.blade.php --}}
@php
  /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $products */
  $cur  = $products->currentPage();
  $last = max(1, $products->lastPage());
  $win  = 5;                  
  $half = intdiv($win, 2);

  $from = max(1, $cur - $half);
  $to   = min($last, $from + $win - 1);
  $from = max(1, $to - $win + 1);
@endphp

@if ($last > 1)
<div id="dg-pager"
     class="flex items-center justify-end px-4 py-3 border-t border-slate-200 bg-slate-50">

  <nav class="flex items-center gap-1">

    {{-- First --}}
    @if ($cur === 1)
      <button class="h-9 px-3 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white opacity-50 cursor-not-allowed">
        <i data-lucide="chevrons-left" class="h-4 w-4"></i>
      </button>
    @else
      <button data-page="1"
              class="js-page h-9 px-3 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50"
              title="First">
        <i data-lucide="chevrons-left" class="h-4 w-4"></i>
      </button>
    @endif

    {{-- Prev --}}
    @if ($products->onFirstPage())
      <button class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white opacity-50 cursor-not-allowed">
        <i data-lucide="chevron-left" class="h-4 w-4"></i>
      </button>
    @else
      <button data-page="{{ $cur - 1 }}"
              class="js-page h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50"
              title="Previous">
        <i data-lucide="chevron-left" class="h-4 w-4"></i>
      </button>
    @endif

    {{-- Left edge + ellipsis --}}
    @if ($from > 1)
      <button data-page="1"
              class="js-page h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
        1
      </button>

      @if ($from > 2)
        <span class="px-1 text-slate-400 select-none">…</span>
      @endif
    @endif

    {{-- Window --}}
    @for ($i = $from; $i <= $to; $i++)
      @if ($i === $cur)
        <span aria-current="page"
              class="h-9 w-9 inline-flex items-center justify-center rounded-lg bg-slate-900 text-white font-medium">
          {{ $i }}
        </span>
      @else
        <button data-page="{{ $i }}"
                class="js-page h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
          {{ $i }}
        </button>
      @endif
    @endfor

    {{-- Right ellipsis + last --}}
    @if ($to < $last)
      @if ($to < $last - 1)
        <span class="px-1 text-slate-400 select-none">…</span>
      @endif

      <button data-page="{{ $last }}"
              class="js-page h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
        {{ $last }}
      </button>
    @endif

    {{-- Next --}}
    @if ($cur >= $last)
      <button class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white opacity-50 cursor-not-allowed">
        <i data-lucide="chevron-right" class="h-4 w-4"></i>
      </button>
    @else
      <button data-page="{{ $cur + 1 }}"
              class="js-page h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50"
              title="Next">
        <i data-lucide="chevron-right" class="h-4 w-4"></i>
      </button>
    @endif

    {{-- Last --}}
    @if ($cur === $last)
      <button class="h-9 px-3 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white opacity-50 cursor-not-allowed">
        <i data-lucide="chevrons-right" class="h-4 w-4"></i>
      </button>
    @else
      <button data-page="{{ $last }}"
              class="js-page h-9 px-3 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50"
              title="Last">
        <i data-lucide="chevrons-right" class="h-4 w-4"></i>
      </button>
    @endif

  </nav>
</div>
@endif
