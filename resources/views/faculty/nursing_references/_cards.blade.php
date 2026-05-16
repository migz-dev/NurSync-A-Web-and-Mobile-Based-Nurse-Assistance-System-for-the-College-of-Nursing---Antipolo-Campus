@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $items */
@endphp

@if ($items->isEmpty())
  <div class="col-span-full px-4 py-8 text-center text-slate-500">
    No references found. Try a different keyword or filter.
  </div>
@else
  @foreach ($items as $ref)
<article class="flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
    {{-- Header --}}
    <header class="flex items-start justify-between gap-4">
        <div class="w-full">
            <h2 class="text-[16px] font-semibold text-slate-900 leading-snug">
                {{ $ref->title }}
            </h2>

            <div class="mt-2 flex flex-wrap items-center gap-2 text-[12px]">
                @if($ref->category)
                    <span class="inline-flex items-center rounded-full bg-slate-50 border border-slate-200 px-2.5 py-0.5 text-slate-700">
                        <i data-lucide="layers" class="h-4 w-4 mr-1"></i>
                        {{ $ref->category }}
                    </span>
                @endif

                @if($ref->source)
                    <span class="inline-flex items-center rounded-full bg-slate-50 border border-slate-200 px-2.5 py-0.5 text-slate-700">
                        <i data-lucide="shield-check" class="h-4 w-4 mr-1"></i>
                        {{ $ref->source }}
                    </span>
                @endif

                @if($ref->is_featured)
                    <span class="inline-flex items-center rounded-full bg-amber-50 border border-amber-200 px-2.5 py-0.5 text-amber-800">
                        <i data-lucide="star" class="h-4 w-4 mr-1"></i>
                        Featured
                    </span>
                @endif
            </div>
        </div>
    </header>

    {{-- Description --}}
    @if($ref->description)
        <p class="mt-3 text-[13px] text-slate-600 line-clamp-3">
            {{ $ref->description }}
        </p>
    @endif

    {{-- URL --}}
    @if($ref->url)
        <p class="mt-2 text-[12px] text-slate-400 truncate">
            {{ $ref->url }}
        </p>
    @endif

    {{-- Sticky Footer --}}
    <div class="mt-auto pt-5 flex items-center justify-between">
        <span class="text-[12px] text-slate-400">
            External site • opens in new tab
        </span>

        @if($ref->url)
            <a href="{{ $ref->url }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-3.5 py-2 text-[13px] font-medium text-white hover:bg-slate-800">
                <i data-lucide="external-link" class="h-4 w-4"></i>
                <span>Open</span>
            </a>
        @endif
    </div>
</article>

  @endforeach
@endif
