{{-- resources/views/admin/patients/_rows.blade.php --}}
@php
  /** @var \Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator $patients */
@endphp

@forelse ($patients as $p)
  @php
    $fullName = trim(($p->last_name ?? '') . ', ' . ($p->first_name ?? '') . ' ' . ($p->middle_name ? mb_substr($p->middle_name, 0, 1) . '.' : '') . ' ' . ($p->suffix ?? ''));
    $fullName = $fullName !== ',' ? $fullName : 'Unnamed patient';

    $mrn   = $p->hospital_no ?? $p->mrn ?? $p->id;
    $sex   = $p->sex ?? null;
    $ward  = $p->ward ?? '—';
    $bed   = $p->bed_no ?? null;
    $attending  = $p->attending_physician ?? 'Not specified';

    // Optional: CI owner if relation exists
    $ciOwner = method_exists($p, 'faculty') && $p->faculty
      ? ($p->faculty->display_name ?? $p->faculty->full_name ?? $p->faculty->name ?? null)
      : null;

    $admissionDate = $p->admission_date ?? null;
    if ($admissionDate instanceof \Carbon\Carbon) {
        $admissionFormatted = $admissionDate->format('M d, Y');
    } elseif (!empty($admissionDate)) {
        try {
            $admissionFormatted = \Carbon\Carbon::parse($admissionDate)->format('M d, Y');
        } catch (\Exception $e) {
            $admissionFormatted = $admissionDate;
        }
    } else {
        $admissionFormatted = '—';
    }

    $status = strtolower($p->status ?? 'active');

    $statusLabelMap = [
      'active'     => 'Active',
      'discharged' => 'Discharged',
      'archived'   => 'Archived',
    ];
    $statusLabel = $statusLabelMap[$status] ?? ucfirst($status);

    $statusClassMap = [
      'active'     => 'bg-emerald-50 text-emerald-700 border-emerald-200',
      'discharged' => 'bg-sky-50 text-sky-700 border-sky-200',
      'archived'   => 'bg-slate-50 text-slate-600 border-slate-200',
    ];
    $statusClass = $statusClassMap[$status] ?? 'bg-slate-50 text-slate-600 border-slate-200';

    $bedLabel = $bed ? "Bed {$bed}" : 'No bed assigned';
  @endphp

  <tr class="js-ap-row opacity-0">
    {{-- Patient / Identifier --}}
    <td class="px-4 py-3 align-top">
      <div class="flex items-start gap-3">
        <div class="mt-0.5 inline-flex h-9 w-9 items-center justify-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-xs font-semibold">
          <i data-lucide="user" class="h-4 w-4"></i>
        </div>
        <div class="space-y-0.5">
          <div class="text-[13px] font-semibold text-slate-900 leading-snug">
            {{ $fullName }}
          </div>
          <div class="text-[11px] text-slate-500">
            MRN: <span class="font-medium text-slate-700">{{ $mrn }}</span>
            @if(!empty($sex))
              <span class="mx-1">•</span>
              <span>{{ strtoupper($sex) }}</span>
            @endif
            @if(!empty($p->age))
              <span class="mx-1">•</span>
              <span>{{ $p->age }} yrs</span>
            @endif
          </div>
        </div>
      </div>
    </td>

    {{-- Ward / Bed --}}
    <td class="px-4 py-3 align-top">
      <div class="space-y-0.5">
        <div class="text-[13px] font-medium text-slate-900">
          {{ $ward }}
        </div>
        <div class="text-[11px] text-slate-500">
          {{ $bedLabel }}
        </div>
      </div>
    </td>

    {{-- Attending / CI --}}
    <td class="px-4 py-3 align-top">
      <div class="space-y-0.5">
        <div class="text-[13px] font-medium text-slate-900">
          {{ $attending }}
        </div>
        <div class="text-[11px] text-slate-500">
          @if($ciOwner)
            CI owner: <span class="font-medium text-slate-700">{{ $ciOwner }}</span>
          @else
            CI owner: <span class="text-slate-400">Not linked</span>
          @endif
        </div>
      </div>
    </td>

    {{-- Admission / Status --}}
    <td class="px-4 py-3 align-top">
      <div class="space-y-1">
        <div class="text-[11px] text-slate-500">
          Admission:
          <span class="font-medium text-slate-700">{{ $admissionFormatted }}</span>
        </div>
        <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[11px] font-medium {{ $statusClass }}">
          <span class="h-1.5 w-1.5 rounded-full bg-current mr-1.5 opacity-70"></span>
          {{ $statusLabel }}
        </span>
      </div>
    </td>

    {{-- Actions (View + Archive only) --}}
    <td class="px-4 py-3 align-top">
      <div class="flex items-center justify-end gap-2">
        {{-- View --}}
        <a
          href="{{ route('admin.patient_data.show', $p) }}"
          class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-[11px] font-medium text-slate-700 hover:bg-slate-50"
        >
          <i data-lucide="eye" class="h-3.5 w-3.5"></i>
          <span>View</span>
        </a>

        {{-- Archive (hide if already archived) --}}
        @if($status !== 'archived')
          <form
            method="POST"
            action="{{ route('admin.patient_data.archive', $p) }}"
            class="inline-flex ap-archive-form"
            data-title="{{ $fullName }}"
          >
            @csrf
            <button
              type="submit"
              class="inline-flex items-center gap-1.5 rounded-xl border border-amber-200 bg-amber-50 px-2.5 py-1.5 text-[11px] font-medium text-amber-800 hover:bg-amber-100"
            >
              <i data-lucide="archive" class="h-3.5 w-3.5"></i>
              <span>Archive</span>
            </button>
          </form>
        @endif
      </div>
    </td>
  </tr>
@empty
  <tr>
    <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">
      No patient records found.
    </td>
  </tr>
@endforelse
