<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>
    {{ $product->substance->name ?? ($product->brand_name ?: 'Drug') }} · Drug Guide · NurSync (CI)
  </title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; }
    @media print {
      body { background: #ffffff !important; }
      main { margin: 0; padding: 0; }
      a[href]:after { content: ""; }
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Sidebar --}}
  @include('partials.faculty-sidebar', ['active' => 'drug_guide'])

  {{-- Main --}}
  <section class="flex-1">
    <div class="container mx-auto px-8 py-12 space-y-6">

      @php
        $generic = $product->substance->name ?? 'Unknown generic';
        $brand   = $product->brand_name ?: '—';
        $classGroup = $product->class_group ?: $product->classification ?: 'Unclassified';
        $packType   = $product->packaging_type ?: 'Not categorized';
        $dosageForm = optional($product->dosageForm)->name ?: '—';
        $category   = optional($product->category)->name ?: '—';
        $mfgName    = optional($product->manufacturer)->name ?: '—';
        $mfgCountry = optional($product->manufacturer)->country ?: null;
      @endphp

      {{-- Title + back/print actions --}}
      <div class="rounded-xl border border-slate-200 bg-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
          <div class="space-y-2">
            <div class="flex flex-wrap items-center gap-2">
              <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100">
                <i data-lucide="pill" class="h-5 w-5 text-slate-700"></i>
              </span>
              <div>
                <h1 class="text-[24px] sm:text-[28px] font-extrabold tracking-tight text-slate-900">
                  {{ $generic }}
                </h1>
                <p class="text-[13px] text-slate-600">
                  Brand: <span class="font-medium text-slate-800">{{ $brand }}</span>
                </p>
              </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 text-[12px]">
              {{-- Class chip --}}
              <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2.5 py-1 text-sky-700 border border-sky-100">
                <i data-lucide="layers" class="h-3.5 w-3.5"></i>
                <span class="font-semibold uppercase tracking-wide">Class:</span>
                <span class="font-medium normal-case">{{ $classGroup }}</span>
              </span>

              {{-- Dosage form chip --}}
              <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-emerald-700 border border-emerald-100">
                <i data-lucide="droplet" class="h-3.5 w-3.5"></i>
                <span class="font-semibold uppercase tracking-wide">Form:</span>
                <span class="font-medium normal-case">{{ $dosageForm }}</span>
              </span>

              {{-- Packaging-type chip --}}
              <span class="inline-flex items-center gap-1 rounded-full bg-violet-50 px-2.5 py-1 text-violet-700 border border-violet-100">
                <i data-lucide="package" class="h-3.5 w-3.5"></i>
                <span class="font-semibold uppercase tracking-wide">Packaging:</span>
                <span class="font-medium normal-case">{{ $packType }}</span>
              </span>
            </div>

            <div class="mt-1 text-[12px] text-slate-600 space-x-1">
              @if($product->dosage_strength)
                <span><span class="font-medium">Strength:</span> {{ $product->dosage_strength }}</span> ·
              @endif
              @if($category && $category !== '—')
                <span><span class="font-medium">Pharmacologic category:</span> {{ $category }}</span> ·
              @endif
              @if($mfgName !== '—')
                <span><span class="font-medium">Manufacturer:</span> {{ $mfgName }}@if($mfgCountry) ({{ $mfgCountry }})@endif</span> ·
              @endif
              @if($product->updated_at)
                <span>Updated {{ $product->updated_at->toFormattedDateString() }}</span>
              @endif
            </div>
          </div>

          <div class="flex items-center gap-2 print:hidden">
            <a href="{{ route('faculty.drug_guide.index') }}"
               class="rounded-lg border px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 inline-flex items-center gap-2">
              <i data-lucide="arrow-left" class="h-4 w-4"></i> Back
            </a>
            <button type="button" onclick="window.print()"
               class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:opacity-95 inline-flex items-center gap-2">
              <i data-lucide="printer" class="h-4 w-4"></i> Print
            </button>
          </div>
        </div>
      </div>

      {{-- Quick product facts (registration, dates, etc.) --}}
      <div class="grid gap-6 md:grid-cols-2">
        <section class="rounded-xl border border-slate-200 bg-white p-5">
          <h3 class="text-sm font-semibold text-slate-900 mb-3">Product details</h3>
          <dl class="grid grid-cols-1 gap-2 text-sm">
            <div class="flex justify-between gap-2">
              <dt class="text-slate-500">Registration #</dt>
              <dd class="font-medium text-slate-900">{{ $product->registration_number ?: '—' }}</dd>
            </div>
            <div class="flex justify-between gap-2">
              <dt class="text-slate-500">Strength</dt>
              <dd class="font-medium text-slate-900">{{ $product->dosage_strength ?: '—' }}</dd>
            </div>
            <div class="flex justify-between gap-2">
              <dt class="text-slate-500">Classification (regulatory)</dt>
              <dd class="font-medium text-slate-900">{{ $product->classification ?: '—' }}</dd>
            </div>
            <div class="flex justify-between gap-2">
              <dt class="text-slate-500">Issued</dt>
              <dd class="font-medium text-slate-900">
                {{ optional($product->issued_at)?->toDateString() ?: '—' }}
              </dd>
            </div>
            <div class="flex justify-between gap-2">
              <dt class="text-slate-500">Expires</dt>
              <dd class="font-medium text-slate-900">
                {{ optional($product->expires_at)?->toDateString() ?: '—' }}
              </dd>
            </div>
          </dl>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-5">
          <h3 class="text-sm font-semibold text-slate-900 mb-3">Packaging & companies</h3>
          <dl class="grid grid-cols-1 gap-2 text-sm">
            <div>
              <dt class="text-slate-500">Packaging description</dt>
              <dd class="mt-1 text-slate-900">
                {{ $product->packaging ?: 'No packaging text has been encoded for this product.' }}
              </dd>
            </div>
            <div class="flex justify-between gap-2">
              <dt class="text-slate-500">Manufacturer</dt>
              <dd class="font-medium text-slate-900">{{ $mfgName }}</dd>
            </div>
            <div class="flex justify-between gap-2">
              <dt class="text-slate-500">Importer</dt>
              <dd class="font-medium text-slate-900">{{ optional($product->importer)->name ?: '—' }}</dd>
            </div>
            <div class="flex justify-between gap-2">
              <dt class="text-slate-500">Distributor</dt>
              <dd class="font-medium text-slate-900">{{ optional($product->distributor)->name ?: '—' }}</dd>
            </div>
            <div class="flex justify-between gap-2">
              <dt class="text-slate-500">Trader</dt>
              <dd class="font-medium text-slate-900">{{ optional($product->trader)->name ?: '—' }}</dd>
            </div>
          </dl>
        </section>
      </div>

      {{-- Monograph content layout (currently placeholder text since DB doesn’t hold these yet) --}}
      <div class="grid gap-6 md:grid-cols-2">
        {{-- Left column --}}
        <div class="space-y-6">
          <section class="rounded-xl border border-slate-200 bg-white p-5">
            <h3 class="text-sm font-semibold text-slate-900">Indications</h3>
            <p class="mt-2 text-sm text-slate-700">
              Detailed indications for this drug have not yet been encoded in NurSync.
              Use your hospital formulary and institutional policies as primary reference.
            </p>
          </section>

          <section class="rounded-xl border border-slate-200 bg-white p-5">
            <h3 class="text-sm font-semibold text-slate-900">Contraindications</h3>
            <p class="mt-2 text-sm text-slate-700">
              Contraindications have not yet been specifically listed for this entry.
              Always check for known hypersensitivity, organ impairment, and institutional
              restrictions before administration.
            </p>
          </section>

          <section class="rounded-xl border border-slate-200 bg-white p-5">
            <h3 class="text-sm font-semibold text-slate-900">Adverse Effects</h3>
            <p class="mt-2 text-sm text-slate-700">
              Common and serious adverse effects are not yet customized here. Refer to your
              drug handbook or local protocol when assessing and documenting reactions.
            </p>
          </section>
        </div>

        {{-- Right column --}}
        <div class="space-y-6">
          <section class="rounded-xl border border-slate-200 bg-white p-5">
            <h3 class="text-sm font-semibold text-slate-900">Dosing</h3>
            <p class="mt-2 text-sm text-slate-700">
              Specific dosing regimens (adult, pediatric, renal/hepatic adjustments) have not
              yet been entered in NurSync. Follow your physician’s order and verify against
              the institutional dosing references.
            </p>
          </section>

          <section class="rounded-xl border border-slate-200 bg-white p-5">
            <h3 class="text-sm font-semibold text-slate-900">Nursing Responsibilities</h3>
            <p class="mt-2 text-sm text-slate-700">
              Use this space in future versions to encode key nursing responsibilities:
              pre-administration checks, monitoring parameters, and safety reminders
              specific to this drug class.
            </p>
          </section>

          <section class="rounded-xl border border-slate-200 bg-white p-5">
            <h3 class="text-sm font-semibold text-slate-900">Patient Teaching</h3>
            <p class="mt-2 text-sm text-slate-700">
              Planned content: essential patient instructions (timing, adherence, side
              effects to report, lifestyle considerations). For now, rely on your standard
              discharge teaching or bedside education tools.
            </p>
          </section>
        </div>
      </div>

      {{-- Full-width sections --}}
      <section class="rounded-xl border border-slate-200 bg-white p-5">
        <h3 class="text-sm font-semibold text-slate-900">Monitoring</h3>
        <p class="mt-2 text-sm text-slate-700">
          Monitoring guidelines specific to this medication can be documented here in later
          iterations. Typical items include vital signs, lab values, organ function, and
          symptom clusters related to the drug’s pharmacologic class.
        </p>
      </section>

      <section class="rounded-xl border border-slate-200 bg-white p-5">
        <h3 class="text-sm font-semibold text-slate-900">Interactions</h3>
        <p class="mt-2 text-sm text-slate-700">
          Detailed drug–drug and drug–food interactions are not yet encoded for this entry.
          Check for high-risk combinations using your hospital’s preferred drug interaction
          checker or reference manual.
        </p>
      </section>

      {{-- References + disclaimer footer --}}
      <div class="rounded-xl border border-slate-200 bg-white p-5">
        <div class="space-y-3">
          <div class="flex items-start gap-3">
            <i data-lucide="book-open" class="h-5 w-5 text-slate-500 mt-0.5"></i>
            <p class="text-[13px] leading-6 text-slate-600">
              Future versions of this page can list the specific references used for this
              drug monograph (e.g., institutional formulary, DOH/FDA resources, and
              standard pharmacology texts).
            </p>
          </div>
          <div class="flex items-start gap-3">
            <i data-lucide="info" class="h-5 w-5 text-slate-500 mt-0.5"></i>
            <p class="text-[13px] leading-6 text-slate-600">
              This page is an educational support tool for nursing practice and training.
              Always verify final decisions with physician’s orders, hospital policy, and
              current drug references.
              @if($product->updated_at)
                <br>Record last updated:
                <span class="font-medium text-slate-700">
                  {{ $product->updated_at->toDayDateTimeString() }}
                </span>
              @endif
            </p>
          </div>
        </div>
      </div>

    </div>
  </section>
</main>

@includeIf('partials.faculty-footer')
@includeWhen(!View::exists('partials.faculty-footer'), 'partials.student-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>
</body>
</html>
