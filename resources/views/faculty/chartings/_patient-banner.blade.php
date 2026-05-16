{{-- resources/views/faculty/chartings/_patient-banner.blade.php --}}
@php
  // Normalize and style status if available
  $rawStatus   = strtolower((string)($patient->status ?? ''));
  $statusLabel = 'Status not set';
  $statusClass = 'bg-slate-100 text-slate-700';

  if ($rawStatus === 'active') {
      $statusLabel = 'Active';
      $statusClass = 'bg-emerald-100 text-emerald-800';
  } elseif ($rawStatus === 'discharged') {
      $statusLabel = 'Discharged';
      $statusClass = 'bg-sky-100 text-sky-800';
  } elseif ($rawStatus === 'archived') {
      $statusLabel = 'Archived';
      $statusClass = 'bg-orange-100 text-orange-800';
  }

  $initial = strtoupper(substr($patient->first_name ?? ($patient->full_name ?? 'P'), 0, 1));
@endphp

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 animate-card-in">
  {{-- Top strip: label + status --}}
  <div class="flex items-center justify-between gap-3 mb-4">
    <div class="inline-flex items-center gap-2 rounded-full bg-slate-50 px-3 py-1">
      <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-sky-100 text-sky-600">
        <i data-lucide="user-round" class="h-3 w-3"></i>
      </span>
      <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-600">
        Patient profile
      </span>
    </div>

    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $statusClass }}">
      {{ $statusLabel }}
    </span>
  </div>

  <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
    {{-- Left: Avatar + main identity --}}
    <div class="flex items-start gap-4">
      <div class="h-14 w-14 flex items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-bold text-lg uppercase">
        {{ $initial }}
      </div>
      <div>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">
          {{ $patient->full_name ?? ($patient->display_name ?? 'Unnamed Patient') }}
        </h2>

        <p class="mt-1 text-sm text-slate-600">
          <span class="font-medium text-slate-800">MRN:</span> {{ $patient->hospital_no ?? 'N/A' }}
          <span class="mx-2 text-slate-300">•</span>
          <span class="font-medium text-slate-800">Sex:</span> {{ $patient->sex ?? '—' }}
          <span class="mx-2 text-slate-300">•</span>
          <span class="font-medium text-slate-800">Age:</span> {{ $patient->age ?? '—' }}
        </p>
      </div>
    </div>

    {{-- Right: quick meta grid --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-3 text-sm">
      <p>
        <span class="block text-slate-500 text-xs uppercase font-semibold tracking-wide">Unit / Ward</span>
        <span class="font-medium text-slate-800">
          {{ strtoupper($patient->ward ?? '—') }}
        </span>
      </p>
      <p>
        <span class="block text-slate-500 text-xs uppercase font-semibold tracking-wide">Bed No.</span>
        <span class="font-medium text-slate-800">{{ $patient->bed_no ?? '—' }}</span>
      </p>
      <p>
        <span class="block text-slate-500 text-xs uppercase font-semibold tracking-wide">Attending</span>
        <span class="font-medium text-slate-800">{{ $patient->attending_physician ?? '—' }}</span>
      </p>
      <p>
        <span class="block text-slate-500 text-xs uppercase font-semibold tracking-wide">Admission</span>
        <span class="font-medium text-slate-800">
          {{ $patient->admission_date ? \Carbon\Carbon::parse($patient->admission_date)->format('M d, Y') : '—' }}
        </span>
      </p>
      <p>
        <span class="block text-slate-500 text-xs uppercase font-semibold tracking-wide">Contact</span>
        <span class="font-medium text-slate-800">{{ $patient->contact_no ?? '—' }}</span>
      </p>
      <p>
        <span class="block text-slate-500 text-xs uppercase font-semibold tracking-wide">Address</span>
        <span class="font-medium text-slate-800">{{ $patient->address ?? '—' }}</span>
      </p>
    </div>
  </div>

  {{-- Admitting Diagnosis --}}
  <div class="mt-5 pt-4 border-t border-slate-200">
    <p class="text-sm text-slate-700">
      <span class="font-semibold text-slate-900">Admitting Diagnosis:</span>
      {{ $patient->admitting_diagnosis ?? 'Not specified' }}
    </p>
  </div>
</div>
