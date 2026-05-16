{{-- resources/views/components/readrow.blade.php --}}
@props(['label' => '', 'value' => ''])
<div {{ $attributes->merge(['class'=>'']) }}>
  <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">{{ $label }}</div>
  <div class="text-sm text-slate-800">{{ (filled($value) || $value==='0') ? $value : '—' }}</div>
</div>
