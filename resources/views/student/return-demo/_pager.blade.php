{{-- resources/views/student/return-demo/_pager.blade.php --}}
@php
  $total = method_exists($skills, 'total') ? $skills->total() : count($skills);
@endphp

@if($total > 0)
  <div class="rounded-2xl border border-slate-200 bg-white p-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      {{-- Summary (JS reads #pagerSummary if needed) --}}
      <div id="pagerSummary" class="text-[12px] text-slate-600">
        Showing {{ $skills->firstItem() ?? 1 }}–{{ $skills->lastItem() ?? $total }} of {{ $total }} procedures
      </div>

      {{-- Laravel pagination links (AJAX will intercept <a> clicks) --}}
      <div class="text-[12px] text-slate-600">
        @if(method_exists($skills, 'hasPages') && $skills->hasPages())
          <div class="inline-flex items-center gap-1">
            {{ $skills->onEachSide(1)->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
@else
  <div class="rounded-2xl border border-slate-200 bg-white p-4">
    <div id="pagerSummary" class="text-[12px] text-slate-600">
      No procedures found
    </div>
  </div>
@endif
