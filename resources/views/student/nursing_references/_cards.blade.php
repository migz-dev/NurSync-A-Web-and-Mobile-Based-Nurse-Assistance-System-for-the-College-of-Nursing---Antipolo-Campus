{{-- resources/views/student/nursing_references/_cards.blade.php --}}

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $items */
@endphp

@if ($items->isEmpty())
  <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
    <h3 class="text-sm font-semibold text-slate-900">
      No nursing references found
    </h3>
    <p class="mt-1 text-[13px] text-slate-500">
      Try adjusting your search or filters to see more resources.
    </p>
  </div>
@else
  @foreach ($items as $ref)
    @php
      $keywords = \Illuminate\Support\Str::of(
        ($ref->title ?? '') . ' ' .
        ($ref->category ?? '') . ' ' .
        ($ref->source ?? '') . ' ' .
        ($ref->description ?? '') . ' ' .
        ($ref->url ?? '')
      )->lower();
    @endphp

    <article
      class="js-card flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow opacity-0"
      data-keywords="{{ $keywords }}"
    >
      {{-- Header (icon + fixed-height title/description/url) --}}
      <header class="flex items-start gap-4">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50">
          <i data-lucide="book-open-check" class="h-5 w-5 text-slate-700"></i>
        </span>

        <div class="flex-1">
          {{-- Title + description + url kept in a fixed-height block so tags align --}}
          <div class="min-h-[80px] flex flex-col gap-1.5">
            <h2 class="text-[16px] font-semibold text-slate-900 leading-snug">
              {{ $ref->title }}
            </h2>

            @if($ref->description)
              <p class="text-[13px] text-slate-600 line-clamp-3">
                {{ \Illuminate\Support\Str::limit($ref->description, 180) }}
              </p>
            @endif

            @if($ref->url)
              <p class="text-[11px] text-slate-400 truncate">
                {{ $ref->url }}
              </p>
            @endif
          </div>

          {{-- Chips row (sticks in same position across cards) --}}
          <div class="mt-2 flex flex-wrap items-center gap-2.5 text-[12px]">
            @if($ref->category)
              <span class="inline-flex items-center rounded-full bg-slate-50 border border-slate-200 px-2.5 py-0.5 text-slate-700">
                <i data-lucide="layers" class="h-3 w-3 mr-1"></i>
                {{ $ref->category }}
              </span>
            @endif

            @if($ref->source)
              <span class="inline-flex items-center rounded-full bg-slate-50 border border-slate-200 px-2.5 py-0.5 text-slate-700">
                <i data-lucide="shield-check" class="h-3 w-3 mr-1"></i>
                {{ $ref->source }}
              </span>
            @endif

            @if($ref->is_featured)
              <span class="inline-flex items-center rounded-full bg-amber-50 border border-amber-200 px-2.5 py-0.5 text-amber-800">
                <i data-lucide="star" class="h-3 w-3 mr-1"></i>
                Featured
              </span>
            @endif
          </div>
        </div>
      </header>

      {{-- Meta row: left label, right “Updated …” (aligned across cards) --}}
      <div class="mt-4 flex items-center justify-between text-[11px] text-slate-500">
        <span class="inline-flex items-center gap-1.5">
          <i data-lucide="external-link" class="h-3 w-3"></i>
          External nursing reference
        </span>

        @if(isset($ref->updated_at))
          <span class="whitespace-nowrap">
            Updated {{ $ref->updated_at?->diffForHumans() ?? '—' }}
          </span>
        @endif
      </div>

      {{-- Footer pinned to bottom (Open button) --}}
      <div class="mt-auto pt-5 border-t border-slate-100 flex items-center justify-between gap-2.5">
        <span class="text-[11px] text-slate-400">
          Opens in a new tab
        </span>

        @if($ref->url)
          <a href="{{ $ref->url }}" target="_blank" rel="noopener"
             class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-3.5 py-2 text-[13px] font-medium text-white hover:bg-slate-800">
            <i data-lucide="external-link" class="h-3.5 w-3.5"></i>
            <span>Open</span>
          </a>
        @endif
      </div>
    </article>
  @endforeach
@endif
