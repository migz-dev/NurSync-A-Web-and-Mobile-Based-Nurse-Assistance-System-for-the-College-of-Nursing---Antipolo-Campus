{{-- resources/views/partials/_chart_card_static.blade.php --}}
@php
  $icon         = $icon ?? 'clipboard-list';
  $title        = $title ?? 'Chart';
  $status       = $status ?? 'Draft';
  $statusClass  = $statusClass ?? 'bg-slate-100 text-slate-700';
  $unit         = $unit ?? '—';
  $type         = $type ?? '—';
  $updated      = $updated ?? '—';
  $desc         = $desc ?? 'Structured documentation form.';
  $tags         = $tags ?? [];
  $fieldsCount  = $fieldsCount ?? 0;
  $hasTemplate  = $hasTemplate ?? false;
  $id           = $id ?? 0;
@endphp

<article class="group relative rounded-2xl border border-slate-200 bg-white p-5 hover:bg-slate-50">
  <div class="absolute left-4 top-4">
    <input type="checkbox" class="h-4 w-4 rounded border-slate-300 align-middle" disabled>
  </div>

  <div class="pl-6">
    <div class="flex items-start justify-between gap-3">
      <div class="flex items-center gap-3">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
          <i data-lucide="{{ $icon }}" class="h-5 w-5"></i>
        </span>
        <div>
          <div class="flex items-center gap-2">
            <h3 class="text-base sm:text-lg font-semibold text-slate-900">{{ $title }}</h3>
            <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $statusClass }}">{{ $status }}</span>
          </div>
          <div class="mt-0.5 flex flex-wrap items-center gap-1.5">
            <span class="rounded-full bg-slate-100 text-slate-700 text-[11px] font-semibold px-2 py-0.5">{{ $unit }}</span>
            <span class="rounded-full bg-sky-100 text-sky-700 text-[11px] font-semibold px-2 py-0.5">{{ $type }}</span>
          </div>
        </div>
      </div>
      <div class="text-right">
        <div class="text-[11px] text-slate-500">Updated</div>
        <div class="text-[12px] text-slate-700">{{ $updated }}</div>
      </div>
    </div>

    <p class="mt-2 text-sm text-slate-600 line-clamp-2">
      {{ $desc }}
    </p>

    <div class="mt-3 flex flex-wrap items-center gap-2">
      @foreach($tags as $t)
        <span class="rounded-full border border-slate-200 px-2 py-0.5 text-[11px] text-slate-600">#{{ $t }}</span>
      @endforeach
    </div>

    <div class="mt-4 flex items-center justify-between">
      <div class="text-[12px] text-slate-500">
        <i data-lucide="list" class="mr-1 inline h-4 w-4"></i>
        {{ $fieldsCount }} fields
        @if($hasTemplate)
          <span class="ml-2 inline-flex items-center gap-1 text-[12px] text-slate-500">
            <i data-lucide="file" class="h-4 w-4"></i> Template
          </span>
        @endif
      </div>

      <div class="flex items-center gap-2">
        <button class="js-preview rounded-full border border-slate-300 px-3 py-1.5 text-[12px] hover:bg-slate-50"
                data-title="{{ $title }}"
                data-unit="{{ $unit }}"
                data-type="{{ $type }}"
                data-status="{{ $status }}"
                data-desc="{{ $desc }}"
                data-edit="{{ url('/faculty/chartings/'.$id.'/edit') }}"
                data-open="{{ url('/faculty/chartings/'.$id) }}">
          Preview
        </button>
        <a href="{{ url('/faculty/chartings/'.$id) }}"
           class="rounded-full border border-slate-300 px-3 py-1.5 text-[12px] hover:bg-slate-50">Open</a>
        <a href="{{ url('/faculty/chartings/'.$id.'/edit') }}"
           class="rounded-full border border-slate-300 px-3 py-1.5 text-[12px] hover:bg-slate-50">Edit</a>
      </div>
    </div>
  </div>
</article>
