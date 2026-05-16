{{-- resources/views/faculty/chartings-patient.blade.php --}}
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Patient Records · NurSync (CI)</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }

    @keyframes slide-in-up {
      from {
        transform: translateY(10px);
        opacity: 0;
      }

      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .animate-card-in {
      animation: slide-in-up .35s ease-out both;
      will-change: transform, opacity;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
  <main class="min-h-screen flex">
    {{-- Sidebar --}}
    @include('partials.faculty-sidebar', ['active' => 'chartings'])

    {{-- Main --}}
    <section class="flex-1">
      <div class="container mx-auto px-8 py-10 space-y-6">

        {{-- Back + Header (matches new pattern) --}}
        <header class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
              <i data-lucide="clipboard-heart" class="h-4 w-4"></i>
            </span>
            <div>
              <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
                Patient Records
              </h1>
              <p class="text-[13px] text-slate-500 mt-1">
                View and document all core chartings for this patient in one organized workspace.
              </p>
            </div>
          </div>

          <a href="{{ route('faculty.chartings.index') }}"
            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 text-slate-700 px-3.5 py-2.5 text-[13px] font-medium hover:bg-slate-50">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            <span>Back to Patients & Tasks</span>
          </a>
        </header>

        {{-- Patient Banner --}}
        @php
          $rawStatus = strtolower((string) ($patient->status ?? ''));
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

            <span
              class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $statusClass }}">
              {{ $statusLabel }}
            </span>
          </div>

          <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
            {{-- Left --}}
            <div class="flex items-start gap-4">
              <div
                class="h-14 w-14 flex items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-bold text-lg uppercase">
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

            {{-- Right meta --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-3 text-sm">
              <p>
                <span class="block text-slate-500 text-xs uppercase font-semibold tracking-wide">Unit / Ward</span>
                <span class="font-medium text-slate-800">{{ strtoupper($patient->ward ?? '—') }}</span>
              </p>
              <p>
                <span class="block text-slate-500 text-xs uppercase font-semibold tracking-wide">Bed No.</span>
                <span class="font-medium text-slate-800">{{ $patient->bed_no ?? '—' }}</span>
              </p>
              <p>
                <span class="block text-slate-500 text-xs uppercase font-semibold tracking-wide">Attending
                  Physician</span>
                <span class="font-medium text-slate-800">{{ $patient->attending_physician ?? '—' }}</span>
              </p>
              <p>
                <span class="block text-slate-500 text-xs uppercase font-semibold tracking-wide">Admission Date</span>
                <span class="font-medium text-slate-800">
                  {{ $patient->admission_date ? \Carbon\Carbon::parse($patient->admission_date)->format('M d, Y') : '—' }}
                </span>
              </p>
              <p>
                <span class="block text-slate-500 text-xs uppercase font-semibold tracking-wide">Contact No.</span>
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


        {{-- Chartings as unified cards --}}
<section aria-label="Chartings list">
  <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">

    {{-- 1) Nurse’s Notes --}}
    <article
      class="flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow animate-card-in">
      <header class="flex items-center gap-4">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50">
          <i data-lucide="file-text" class="h-5 w-5 text-indigo-600"></i>
        </span>
        <div>
          <h3 class="text-[16px] font-semibold text-slate-900">Nurse’s Notes</h3>
          <p class="mt-1 text-[13px] text-slate-500">
            Narrative notes (SOAP, DAR, PIE, Focus) for ongoing patient care.
          </p>
        </div>
      </header>

      <div class="mt-auto pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
        <a href="{{ route('faculty.chartings.notes.index', $patient->id) }}"
          class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 text-blue-700 px-3.5 py-2 text-[13px] font-medium hover:bg-blue-50">
          <i data-lucide="eye" class="h-4 w-4"></i>
          View notes
        </a>
        <a href="javascript:void(0)" data-modal-open="modalCreateNotes"
          class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 text-emerald-800 px-3.5 py-2 text-[13px] font-medium hover:bg-emerald-50">
          <i data-lucide="plus-circle" class="h-4 w-4"></i>
          New entry
        </a>
      </div>
    </article>

    {{-- 2) Vital Signs --}}
    <article
      class="flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow animate-card-in">
      <header class="flex items-center gap-4">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-rose-50">
          <i data-lucide="activity" class="h-5 w-5 text-rose-600"></i>
        </span>
        <div>
          <h3 class="text-[16px] font-semibold text-slate-900">Vital Signs</h3>
          <p class="mt-1 text-[13px] text-slate-500">
            Track temperature, pulse, respiration, BP, O<sub>2</sub> sat, and pain scores.
          </p>
        </div>
      </header>

      <div class="mt-auto pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
        <a href="{{ route('faculty.chartings.vitals.index', $patient->id) }}"
          class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 text-blue-700 px-3.5 py-2 text-[13px] font-medium hover:bg-blue-50">
          <i data-lucide="eye" class="h-4 w-4"></i>
          View sheet
        </a>
        <a href="javascript:void(0)" data-modal-open="modalCreateVitals"
          class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 text-emerald-800 px-3.5 py-2 text-[13px] font-medium hover:bg-emerald-50">
          <i data-lucide="plus-circle" class="h-4 w-4"></i>
          New set
        </a>
      </div>
    </article>

    {{-- 3) Intake & Output --}}
    <article
      class="flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow animate-card-in">
      <header class="flex items-center gap-4">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-50">
          <i data-lucide="droplet" class="h-5 w-5 text-cyan-600"></i>
        </span>
        <div>
          <h3 class="text-[16px] font-semibold text-slate-900">Intake &amp; Output</h3>
          <p class="mt-1 text-[13px] text-slate-500">
            Monitor fluid balance from all oral, IV, and output sources.
          </p>
        </div>
      </header>

      <div class="mt-auto pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
        <a href="{{ route('faculty.chartings.io.index', $patient->id) }}"
          class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 text-blue-700 px-3.5 py-2 text-[13px] font-medium hover:bg-blue-50">
          <i data-lucide="eye" class="h-4 w-4"></i>
          View records
        </a>
        <a href="javascript:void(0)" data-modal-open="modalCreateIO"
          class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 text-emerald-800 px-3.5 py-2 text-[13px] font-medium hover:bg-emerald-50">
          <i data-lucide="plus-circle" class="h-4 w-4"></i>
          New entry
        </a>
      </div>
    </article>

    {{-- 4) Medication Administration Record (MAR) --}}
    <article
      class="flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow animate-card-in">
      <header class="flex items-center gap-4">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50">
          <i data-lucide="pill" class="h-5 w-5 text-emerald-600"></i>
        </span>
        <div>
          <h3 class="text-[16px] font-semibold text-slate-900">Medication Admin Record</h3>
          <p class="mt-1 text-[13px] text-slate-500">
            Log scheduled and PRN medications, doses, and administration times.
          </p>
        </div>
      </header>

      <div class="mt-auto pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
        <a href="{{ route('faculty.chartings.mar.index', $patient->id) }}"
          class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 text-blue-700 px-3.5 py-2 text-[13px] font-medium hover:bg-blue-50">
          <i data-lucide="eye" class="h-4 w-4"></i>
          View MAR
        </a>
        <a href="javascript:void(0)" data-modal-open="modalCreateMAR"
          class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 text-emerald-800 px-3.5 py-2 text-[13px] font-medium hover:bg-emerald-50">
          <i data-lucide="plus-circle" class="h-4 w-4"></i>
          New dose
        </a>
      </div>
    </article>

    {{-- 5) Nursing Care Plan (NCP) --}}
    <article
      class="flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow animate-card-in">
      <header class="flex items-center gap-4">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50">
          <i data-lucide="clipboard-list" class="h-5 w-5 text-purple-600"></i>
        </span>
        <div>
          <h3 class="text-[16px] font-semibold text-slate-900">Nursing Care Plan</h3>
          <p class="mt-1 text-[13px] text-slate-500">
            Document diagnoses, goals, interventions, and evaluation.
          </p>
        </div>
      </header>

      <div class="mt-auto pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
        <a href="{{ route('faculty.chartings.ncp.index', $patient->id) }}"
          class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 text-blue-700 px-3.5 py-2 text-[13px] font-medium hover:bg-blue-50">
          <i data-lucide="eye" class="h-4 w-4"></i>
          View plans
        </a>
        <a href="javascript:void(0)" data-modal-open="modalCreateNCP"
          class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 text-emerald-800 px-3.5 py-2 text-[13px] font-medium hover:bg-emerald-50">
          <i data-lucide="plus-circle" class="h-4 w-4"></i>
          New care plan
        </a>
      </div>
    </article>

    {{-- 6) Treatment / Procedure --}}
    <article
      class="flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow animate-card-in">
      <header class="flex items-center gap-4">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-orange-50">
          <i data-lucide="stethoscope" class="h-5 w-5 text-orange-600"></i>
        </span>
        <div>
          <h3 class="text-[16px] font-semibold text-slate-900">Treatment / Procedure</h3>
          <p class="mt-1 text-[13px] text-slate-500">
            Record bedside procedures, treatments, and special therapies.
          </p>
        </div>
      </header>

      <div class="mt-auto pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
        <a href="{{ route('faculty.chartings.treatment.index', $patient->id) }}"
          class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 text-blue-700 px-3.5 py-2 text-[13px] font-medium hover:bg-blue-50">
          <i data-lucide="eye" class="h-4 w-4"></i>
          View records
        </a>
        <a href="javascript:void(0)" data-modal-open="modalCreateTreatment"
          class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 text-emerald-800 px-3.5 py-2 text-[13px] font-medium hover:bg-emerald-50">
          <i data-lucide="plus-circle" class="h-4 w-4"></i>
          New procedure
        </a>
      </div>
    </article>

    {{-- 7) Patient Assessment --}}
    <article
      class="flex flex-col h-full rounded-2l border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow animate-card-in">
      <header class="flex items-center gap-4">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50">
          <i data-lucide="clipboard-check" class="h-5 w-5 text-sky-600"></i>
        </span>
        <div>
          <h3 class="text-[16px] font-semibold text-slate-900">Patient Assessment</h3>
          <p class="mt-1 text-[13px] text-slate-500">
            Capture initial and ongoing head-to-toe assessment findings.
          </p>
        </div>
      </header>

      <div class="mt-auto pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
        <a href="{{ route('faculty.chartings.assessment.index', $patient->id) }}"
          class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 text-blue-700 px-3.5 py-2 text-[13px] font-medium hover:bg-blue-50">
          <i data-lucide="eye" class="h-4 w-4"></i>
          View assessments
        </a>
        <a href="javascript:void(0)" data-modal-open="modalCreateAssessment"
          class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 text-emerald-800 px-3.5 py-2 text-[13px] font-medium hover:bg-emerald-50">
          <i data-lucide="plus-circle" class="h-4 w-4"></i>
          New assessment
        </a>
      </div>
    </article>

    {{-- 8) Shift Handover --}}
    <article
      class="flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow animate-card-in">
      <header class="flex items-center gap-4">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50">
          {{-- FIXED ICON NAME --}}
          <i data-lucide="arrow-left-right" class="h-5 w-5 text-teal-600"></i>
        </span>
        <div>
          <h3 class="text-[16px] font-semibold text-slate-900">Shift Handover</h3>
          <p class="mt-1 text-[13px] text-slate-500">
            Standardize endorsements between outgoing and incoming nurses.
          </p>
        </div>
      </header>

      <div class="mt-auto pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
        <a href="{{ route('faculty.chartings.shift.index', $patient->id) }}"
          class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 text-blue-700 px-3.5 py-2 text-[13px] font-medium hover:bg-blue-50">
          <i data-lucide="eye" class="h-4 w-4"></i>
          View handovers
        </a>
        <a href="javascript:void(0)" data-modal-open="modalCreateShift"
          class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 text-emerald-800 px-3.5 py-2 text-[13px] font-medium hover:bg-emerald-50">
          <i data-lucide="plus-circle" class="h-4 w-4"></i>
          New handover
        </a>
      </div>
    </article>

    {{-- 9) Nursing Kardex --}}
    <article
      class="flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow animate-card-in">
      <header class="flex items-center gap-4">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-lime-50">
          <i data-lucide="notepad-text" class="h-5 w-5 text-lime-600"></i>
        </span>
        <div>
          <h3 class="text-[16px] font-semibold text-slate-900">Nursing Kardex</h3>
          <p class="mt-1 text-[13px] text-slate-500">
            Snapshot of key orders, routines, and care priorities.
          </p>
        </div>
      </header>

      <div class="mt-auto pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
        <a href="{{ route('faculty.chartings.kardex.index', $patient->id) }}"
          class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 text-blue-700 px-3.5 py-2 text-[13px] font-medium hover:bg-blue-50">
          <i data-lucide="eye" class="h-4 w-4"></i>
          View kardex
        </a>
        <a href="javascript:void(0)" data-modal-open="modalCreateKardex"
          class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 text-emerald-800 px-3.5 py-2 text-[13px] font-medium hover:bg-emerald-50">
          <i data-lucide="plus-circle" class="h-4 w-4"></i>
          New kardex
        </a>
      </div>
    </article>

    {{-- 10) Patient Summary / Daily Progress --}}
    <article
      class="flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow animate-card-in">
      <header class="flex items-center gap-4">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100">
          <i data-lucide="calendar-range" class="h-5 w-5 text-slate-700"></i>
        </span>
        <div>
          <h3 class="text-[16px] font-semibold text-slate-900">Daily Progress</h3>
          <p class="mt-1 text-[13px] text-slate-500">
            Summarize overall patient status and plan each shift or day.
          </p>
        </div>
      </header>

      <div class="mt-auto pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
        <a href="{{ route('faculty.chartings.summary.index', $patient->id) }}"
          class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 text-blue-700 px-3.5 py-2 text-[13px] font-medium hover:bg-blue-50">
          <i data-lucide="eye" class="h-4 w-4"></i>
          View summaries
        </a>
        <a href="javascript:void(0)" data-modal-open="modalCreateSummary"
          class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 text-emerald-800 px-3.5 py-2 text-[13px] font-medium hover:bg-emerald-50">
          <i data-lucide="plus-circle" class="h-4 w-4"></i>
          New summary
        </a>
      </div>
    </article>

    {{-- 11) Diagnostic Results Log --}}
    <article
      class="flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow animate-card-in">
      <header class="flex items-center gap-4">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-fuchsia-50">
          <i data-lucide="flask-conical" class="h-5 w-5 text-fuchsia-600"></i>
        </span>
        <div>
          <h3 class="text-[16px] font-semibold text-slate-900">Diagnostic Results</h3>
          <p class="mt-1 text-[13px] text-slate-500">Track labs and imaging with interpretation notes.</p>
        </div>
      </header>

      <div class="mt-auto pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
        <a href="{{ route('faculty.chartings.diagnostic.index', $patient->id) }}"
          class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 text-blue-700 px-3.5 py-2 text-[13px] font-medium hover:bg-blue-50">
          <i data-lucide="eye" class="h-4 w-4"></i> View log
        </a>
        <a href="javascript:void(0)" data-modal-open="modalCreateDiagnostic"
          class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 text-emerald-800 px-3.5 py-2 text-[13px] font-medium hover:bg-emerald-50">
          <i data-lucide="plus-circle" class="h-4 w-4"></i> New entry
        </a>
      </div>
    </article>

    {{-- 12) Patient Education --}}
    <article
      class="flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow animate-card-in">
      <header class="flex items-center gap-4">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50">
          <i data-lucide="graduation-cap" class="h-5 w-5 text-amber-600"></i>
        </span>
        <div>
          <h3 class="text-[16px] font-semibold text-slate-900">Patient Education</h3>
          <p class="mt-1 text-[13px] text-slate-500">Document teaching sessions and understanding.</p>
        </div>
      </header>

      <div class="mt-auto pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
        <a href="{{ route('faculty.chartings.education.index', $patient->id) }}"
          class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 text-blue-700 px-3.5 py-2 text-[13px] font-medium hover:bg-blue-50">
          <i data-lucide="eye" class="h-4 w-4"></i> View sessions
        </a>
        <a href="javascript:void(0)" data-modal-open="modalCreateEducation"
          class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 text-emerald-800 px-3.5 py-2 text-[13px] font-medium hover:bg-emerald-50">
          <i data-lucide="plus-circle" class="h-4 w-4"></i> New teaching
        </a>
      </div>
    </article>

    {{-- 13) Medication Preparation --}}
    <article
      class="flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow animate-card-in">
      <header class="flex items-center gap-4">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50">
          <i data-lucide="syringe" class="h-5 w-5 text-emerald-600"></i>
        </span>
        <div>
          <h3 class="text-[16px] font-semibold text-slate-900">Medication Preparation</h3>
          <p class="mt-1 text-[13px] text-slate-500">Record checks, dilution steps, & safety verification.</p>
        </div>
      </header>

      <div class="mt-auto pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
        <a href="{{ route('faculty.chartings.medprep.index', $patient->id) }}"
          class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 text-blue-700 px-3.5 py-2 text-[13px] font-medium hover:bg-blue-50">
          <i data-lucide="eye" class="h-4 w-4"></i> View preparations
        </a>
        <a href="javascript:void(0)" data-modal-open="modalCreateMedPrep"
          class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 text-emerald-800 px-3.5 py-2 text-[13px] font-medium hover:bg-emerald-50">
          <i data-lucide="plus-circle" class="h-4 w-4"></i> New checklist
        </a>
      </div>
    </article>

    {{-- 14) Allergy & Reaction Record --}}
    <article
      class="flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow animate-card-in">
      <header class="flex items-center gap-4">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-red-50">
          <i data-lucide="alert-triangle" class="h-5 w-5 text-red-600"></i>
        </span>
        <div>
          <h3 class="text-[16px] font-semibold text-slate-900">Allergy & Reaction</h3>
          <p class="mt-1 text-[13px] text-slate-500">Log allergies, reactions, triggers, & management.</p>
        </div>
      </header>

      <div class="mt-auto pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
        {{-- FIXED: uses allergies.index --}}
        <a href="{{ route('faculty.chartings.allergies.index', $patient->id) }}"
          class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 text-blue-700 px-3.5 py-2 text-[13px] font-medium hover:bg-blue-50">
          <i data-lucide="eye" class="h-4 w-4"></i> View records
        </a>

        {{-- FIXED: modal ID should match your modal file --}}
        <a href="javascript:void(0)" data-modal-open="modalCreateAllergies"
          class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 text-emerald-800 px-3.5 py-2 text-[13px] font-medium hover:bg-emerald-50">
          <i data-lucide="plus-circle" class="h-4 w-4"></i> New record
        </a>
      </div>
    </article>

    {{-- 15) Pain Assessment --}}
    <article
      class="flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow animate-card-in">
      <header class="flex items-center gap-4">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-pink-50">
          <i data-lucide="thermometer" class="h-5 w-5 text-pink-600"></i>
        </span>
        <div>
          <h3 class="text-[16px] font-semibold text-slate-900">Pain Assessment</h3>
          <p class="mt-1 text-[13px] text-slate-500">Rate pain characteristics and response to treatment.</p>
        </div>
      </header>

      <div class="mt-auto pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
        <a href="{{ route('faculty.chartings.pain.index', $patient->id) }}"
          class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 text-blue-700 px-3.5 py-2 text-[13px] font-medium hover:bg-blue-50">
          <i data-lucide="eye" class="h-4 w-4"></i> View assessments
        </a>
        <a href="javascript:void(0)" data-modal-open="modalCreatePain"
          class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 text-emerald-800 px-3.5 py-2 text-[13px] font-medium hover:bg-emerald-50">
          <i data-lucide="plus-circle" class="h-4 w-4"></i> New assessment
        </a>
      </div>
    </article>

    {{-- 16) Safety & Fall Risk --}}
    <article
      class="flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow animate-card-in">
      <header class="flex items-center gap-4">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-yellow-50">
          <i data-lucide="shield-check" class="h-5 w-5 text-yellow-600"></i>
        </span>
        <div>
          <h3 class="text-[16px] font-semibold text-slate-900">Safety & Fall Risk</h3>
          <p class="mt-1 text-[13px] text-slate-500">Check bedside safety & fall risk scores.</p>
        </div>
      </header>

      <div class="mt-auto pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
        <a href="{{ route('faculty.chartings.safety.index', $patient->id) }}"
          class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 text-blue-700 px-3.5 py-2 text-[13px] font-medium hover:bg-blue-50">
          <i data-lucide="eye" class="h-4 w-4"></i> View checks
        </a>
        <a href="javascript:void(0)" data-modal-open="modalCreateSafety"
          class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 text-emerald-800 px-3.5 py-2 text-[13px] font-medium hover:bg-emerald-50">
          <i data-lucide="plus-circle" class="h-4 w-4"></i> New checklist
        </a>
      </div>
    </article>

    {{-- 17) Neurological Observation --}}
    <article
      class="flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow animate-card-in">
      <header class="flex items-center gap-4">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50">
          <i data-lucide="brain" class="h-5 w-5 text-violet-600"></i>
        </span>
        <div>
          <h3 class="text-[16px] font-semibold text-slate-900">Neurological Observation</h3>
          <p class="mt-1 text-[13px] text-slate-500">Record GCS, pupils, motor & sensory status.</p>
        </div>
      </header>

      <div class="mt-auto pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
        <a href="{{ route('faculty.chartings.neuro.index', $patient->id) }}"
          class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 text-blue-700 px-3.5 py-2 text-[13px] font-medium hover:bg-blue-50">
          <i data-lucide="eye" class="h-4 w-4"></i> View observations
        </a>
        <a href="javascript:void(0)" data-modal-open="modalCreateNeuro"
          class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 text-emerald-800 px-3.5 py-2 text-[13px] font-medium hover:bg-emerald-50">
          <i data-lucide="plus-circle" class="h-4 w-4"></i> New observation
        </a>
      </div>
    </article>

  </div>
</section>


      </div>
    </section>
  </main>

  {{-- Modals --}}
  {{-- Core 10 Existing Chartings --}}
  @include('faculty.chartings._modal-notes-create')
  @include('faculty.chartings._modal-vitals-create')
  @include('faculty.chartings._modal-io-create')
  @include('faculty.chartings._modal-mar-create')
  @include('faculty.chartings._modal-ncp-create')
  @include('faculty.chartings._modal-treatment-create')
  @include('faculty.chartings._modal-assessment-create')
  @include('faculty.chartings._modal-shifthandover-create')
  @include('faculty.chartings._modal-kardex-create')
  @include('faculty.chartings._modal-summary-create')

  {{-- New 7 Extended Chartings --}}
  @include('faculty.chartings._modal-diagnostic-create')
  @include('faculty.chartings._modal-education-create')
  @include('faculty.chartings._modal-medprep-create')
  @include('faculty.chartings._modal-allergy-create')
  @include('faculty.chartings._modal-pain-create')
  @include('faculty.chartings._modal-safety-create')
  @include('faculty.chartings._modal-neuro-create')


  @include('partials.faculty-footer')

  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    try { lucide.createIcons() } catch (_) { }

    // Modal controller
    (function () {
      const openers = document.querySelectorAll('[data-modal-open]');

      function openModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.remove('hidden');
      }
      function closeModal(el) {
        const wrap = el.closest('.fixed.inset-0');
        if (wrap) wrap.classList.add('hidden');
      }

      openers.forEach(btn =>
        btn.addEventListener('click', () => openModal(btn.getAttribute('data-modal-open')))
      );

      document.querySelectorAll('.fixed.inset-0').forEach(modal => {
        modal.addEventListener('click', (e) => {
          const t = e.target;
          if (t && t.hasAttribute('data-modal-close')) closeModal(t);
        });
        modal.querySelectorAll('[data-modal-close]').forEach(x =>
          x.addEventListener('click', () => closeModal(x))
        );
      });

      window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          const open = Array.from(document.querySelectorAll('.fixed.inset-0'))
            .filter(m => !m.classList.contains('hidden'));
          if (open.length) open.at(-1).classList.add('hidden');
        }
      });
    })();
  </script>
</body>

</html>
