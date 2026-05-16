@props(['id'])

<div id="{{ $id }}" class="hidden fixed inset-0 z-50" role="dialog" aria-modal="true">
  <div class="absolute inset-0 bg-black/40"></div>
  <div {{ $attributes->merge(['class' => 'relative mx-auto mt-24 w-full max-w-lg']) }}>
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-5">
      {{ $slot }}
    </div>
  </div>
</div>
