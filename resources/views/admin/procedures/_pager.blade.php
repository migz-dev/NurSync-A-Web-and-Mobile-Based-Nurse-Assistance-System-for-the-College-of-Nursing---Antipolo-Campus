{{-- resources/views/admin/procedures/_pager.blade.php --}}
@php
  $cur = $procedures->currentPage();
  $last = max(1, $procedures->lastPage());
  $window = 3;
  $half = intdiv($window - 1, 2);
  $from = max(1, $cur - $half);
  $to = min($last, $from + $window - 1);
  $from = max(1, $to - $window + 1);
@endphp

<nav class="flex items-center gap-1">
  <button data-page="{{ max(1, $cur-1) }}"
          class="js-page h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white {{ $procedures->onFirstPage() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-slate-50' }}"
          {{ $procedures->onFirstPage() ? 'disabled' : '' }}>
    <i data-lucide="chevron-left" class="h-4 w-4"></i>
  </button>

  @for($i=$from; $i<=$to; $i++)
    @if($i === $cur)
      <span class="h-9 w-9 inline-flex items-center justify-center rounded-lg bg-slate-900 text-white">{{ $i }}</span>
    @else
      <button data-page="{{ $i }}"
              class="js-page h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
        {{ $i }}
      </button>
    @endif
  @endfor

  <button data-page="{{ min($last, $cur+1) }}"
          class="js-page h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white {{ $cur >= $last ? 'opacity-50 cursor-not-allowed' : 'hover:bg-slate-50' }}"
          {{ $cur >= $last ? 'disabled' : '' }}>
    <i data-lucide="chevron-right" class="h-4 w-4"></i>
  </button>
</nav>
