{{-- resources/views/components/readbox.blade.php --}}
@props(['label' => '', 'value' => ''])
<div {{ $attributes->merge(['class'=>'']) }}>
  <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">{{ $label }}</div>
  <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 whitespace-pre-line">
    {{ (filled($value) || $value==='0') ? $value : '—' }}
  </div>
</div>
