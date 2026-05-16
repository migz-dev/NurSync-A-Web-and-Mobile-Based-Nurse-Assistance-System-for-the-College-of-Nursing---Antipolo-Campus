{{-- resources/views/admin/drug_guide/create.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Admin • New Drug Product · NurSync</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    body { font-family:'Poppins',ui-sans-serif,system-ui,sans-serif; }

    @keyframes slide-in-up {
      from { transform: translateY(6px); opacity: 0; }
      to   { transform: translateY(0);  opacity: 1; }
    }
    .animate-card-in {
      animation: slide-in-up .25s ease-out both;
      will-change: transform, opacity;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  @include('partials.admin-sidebar', ['active' => 'drug_guide'])

  <section class="flex-1 min-w-0">
    {{-- Sticky header --}}
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <a href="{{ route('admin.drug_guide.index') }}"
             class="inline-flex items-center justify-center h-8 w-8 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
          </a>
          <div class="flex items-center gap-2">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-900 text-white shadow-sm">
              <i data-lucide="pill" class="h-4 w-4"></i>
            </span>
            <div>
              <h1 class="text-[15px] sm:text-[16px] font-semibold leading-tight">
                New Drug Product
              </h1>
              <p class="text-[12px] text-slate-500 -mt-0.5">
                Add a registered drug product to the NurSync Drug Guide.
              </p>
            </div>
          </div>
        </div>

        <div class="hidden sm:flex items-center gap-2 text-[12px] text-slate-400">
          <span>Admin · Drug Guide</span>
        </div>
      </div>
    </header>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 space-y-6">

      {{-- Flash & errors --}}
      @if (session('ok'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
          {{ session('ok') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 space-y-1">
          <div class="font-semibold">Please fix the following:</div>
          <ul class="list-disc list-inside text-[13px]">
            @foreach ($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- Form card --}}
      <form method="POST"
            action="{{ route('admin.drug_guide.store') }}"
            class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-6 animate-card-in">
        @csrf

        {{-- Section: Basic identification --}}
        <section class="space-y-4">
          <div class="flex items-center justify-between gap-2">
            <h2 class="text-sm font-semibold text-slate-900">Identification</h2>
            <p class="text-[12px] text-slate-500">
              Core fields required for listing in the Drug Guide.
            </p>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            {{-- Brand name --}}
            <div class="md:col-span-2">
              <label for="brand_name" class="block text-xs font-medium text-slate-700">
                Brand name <span class="text-red-500">*</span>
              </label>
              <input id="brand_name" name="brand_name" type="text"
                     value="{{ old('brand_name') }}"
                     class="mt-1 block w-full rounded-xl border-slate-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-slate-300"
                     placeholder="e.g., Biogesic 500 mg Tablet">
            </div>

            {{-- Substance --}}
            <div>
              <label for="substance_id" class="block text-xs font-medium text-slate-700">
                Active substance <span class="text-red-500">*</span>
              </label>
              <select id="substance_id" name="substance_id"
                      class="mt-1 block w-full rounded-xl border-slate-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-slate-300">
                <option value="">Select substance…</option>
                @foreach ($substances as $s)
                  <option value="{{ $s->id }}" @selected(old('substance_id') == $s->id)>
                    {{ $s->name }}
                  </option>
                @endforeach
              </select>
            </div>

            {{-- Category --}}
            <div>
              <label for="pharmacologic_category_id" class="block text-xs font-medium text-slate-700">
                Pharmacologic category <span class="text-red-500">*</span>
              </label>
              <select id="pharmacologic_category_id" name="pharmacologic_category_id"
                      class="mt-1 block w-full rounded-xl border-slate-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-slate-300">
                <option value="">Select category…</option>
                @foreach ($cats as $c)
                  <option value="{{ $c->id }}" @selected(old('pharmacologic_category_id') == $c->id)>
                    {{ $c->name }}
                  </option>
                @endforeach
              </select>
            </div>

            {{-- Dosage form --}}
            <div>
              <label for="dosage_form_id" class="block text-xs font-medium text-slate-700">
                Dosage form
              </label>
              <select id="dosage_form_id" name="dosage_form_id"
                      class="mt-1 block w-full rounded-xl border-slate-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-slate-300">
                <option value="">Select form…</option>
                @foreach ($forms as $f)
                  <option value="{{ $f->id }}" @selected(old('dosage_form_id') == $f->id)>
                    {{ $f->name }}
                  </option>
                @endforeach
              </select>
            </div>

            {{-- Strength --}}
            <div>
              <label for="dosage_strength" class="block text-xs font-medium text-slate-700">
                Strength
              </label>
              <input id="dosage_strength" name="dosage_strength" type="text"
                     value="{{ old('dosage_strength') }}"
                     class="mt-1 block w-full rounded-xl border-slate-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-slate-300"
                     placeholder="e.g., 500 mg tablet, 5 mg/mL ampule">
            </div>
          </div>
        </section>

        <hr class="border-slate-100">

        {{-- Section: Classification & packaging --}}
        <section class="space-y-4">
          <div class="flex items-center justify-between gap-2">
            <h2 class="text-sm font-semibold text-slate-900">Classification & Packaging</h2>
            <p class="text-[12px] text-slate-500">
              Helps normalize drug classes (Rx / OTC / DD) and how the product is supplied.
            </p>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            {{-- Classification --}}
            <div>
              <label for="classification" class="block text-xs font-medium text-slate-700">
                Regulatory classification
              </label>
              <input id="classification" name="classification" type="text"
                     value="{{ old('classification') }}"
                     class="mt-1 block w-full rounded-xl border-slate-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-slate-300"
                     placeholder="e.g., Rx, OTC, Dangerous Drug">
              <p class="mt-1 text-[11px] text-slate-400">
                This will be normalized and used for the “Drug class” filter.
              </p>
            </div>

            {{-- Country of origin --}}
            <div>
              <label for="country_of_origin" class="block text-xs font-medium text-slate-700">
                Country of origin
              </label>
              <input id="country_of_origin" name="country_of_origin" type="text"
                     value="{{ old('country_of_origin') }}"
                     class="mt-1 block w-full rounded-xl border-slate-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-slate-300"
                     placeholder="e.g., Philippines, USA, Germany">
            </div>

            {{-- Packaging --}}
            <div class="md:col-span-2">
              <label for="packaging" class="block text-xs font-medium text-slate-700">
                Packaging description
              </label>
              <textarea id="packaging" name="packaging" rows="2"
                        class="mt-1 block w-full rounded-xl border-slate-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-slate-300"
                        placeholder="e.g., Box of 10 tablets; Bottle of 60 mL syrup">{{ old('packaging') }}</textarea>
              <p class="mt-1 text-[11px] text-slate-400">
                The first word (e.g., “Box”, “Bottle”, “Vial”) will be used for the “Packaging type” filter.
              </p>
            </div>

            {{-- Application type --}}
            <div>
              <label for="application_type" class="block text-xs font-medium text-slate-700">
                Application type
              </label>
              <input id="application_type" name="application_type" type="text"
                     value="{{ old('application_type') }}"
                     class="mt-1 block w-full rounded-xl border-slate-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-slate-300"
                     placeholder="e.g., Initial registration, Renewal">
            </div>
          </div>
        </section>

        <hr class="border-slate-100">

        {{-- Section: Companies --}}
        <section class="space-y-4">
          <div class="flex items-center justify-between gap-2">
            <h2 class="text-sm font-semibold text-slate-900">Companies</h2>
            <p class="text-[12px] text-slate-500">
              Link to manufacturer and other companies involved in distribution.
            </p>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            {{-- Manufacturer --}}
            <div>
              <label for="manufacturer_id" class="block text-xs font-medium text-slate-700">
                Manufacturer
              </label>
              <select id="manufacturer_id" name="manufacturer_id"
                      class="mt-1 block w-full rounded-xl border-slate-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-slate-300">
                <option value="">Select manufacturer…</option>
                @foreach ($mfgs as $m)
                  <option value="{{ $m->id }}" @selected(old('manufacturer_id') == $m->id)>
                    {{ $m->name }}{{ $m->country ? ' · '.$m->country : '' }}
                  </option>
                @endforeach
              </select>
            </div>

            {{-- Importer --}}
            <div>
              <label for="importer_id" class="block text-xs font-medium text-slate-700">
                Importer (optional)
              </label>
              <select id="importer_id" name="importer_id"
                      class="mt-1 block w-full rounded-xl border-slate-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-slate-300">
                <option value="">None / Not applicable</option>
                @foreach ($importers as $c)
                  <option value="{{ $c->id }}" @selected(old('importer_id') == $c->id)>
                    {{ $c->name }}{{ $c->country ? ' · '.$c->country : '' }}
                  </option>
                @endforeach
              </select>
            </div>

            {{-- Distributor --}}
            <div>
              <label for="distributor_id" class="block text-xs font-medium text-slate-700">
                Distributor (optional)
              </label>
              <select id="distributor_id" name="distributor_id"
                      class="mt-1 block w-full rounded-xl border-slate-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-slate-300">
                <option value="">None / Not applicable</option>
                @foreach ($distributors as $c)
                  <option value="{{ $c->id }}" @selected(old('distributor_id') == $c->id)>
                    {{ $c->name }}{{ $c->country ? ' · '.$c->country : '' }}
                  </option>
                @endforeach
              </select>
            </div>

            {{-- Trader --}}
            <div>
              <label for="trader_id" class="block text-xs font-medium text-slate-700">
                Trader (optional)
              </label>
              <select id="trader_id" name="trader_id"
                      class="mt-1 block w-full rounded-xl border-slate-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-slate-300">
                <option value="">None / Not applicable</option>
                @foreach ($traders as $c)
                  <option value="{{ $c->id }}" @selected(old('trader_id') == $c->id)>
                    {{ $c->name }}{{ $c->country ? ' · '.$c->country : '' }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>
        </section>

        <hr class="border-slate-100">

        {{-- Section: Regulatory details --}}
        <section class="space-y-4">
          <div class="flex items-center justify-between gap-2">
            <h2 class="text-sm font-semibold text-slate-900">Regulatory Details</h2>
            <p class="text-[12px] text-slate-500">
              Registration number and validity period.
            </p>
          </div>

          <div class="grid gap-4 md:grid-cols-3">
            {{-- Registration number --}}
            <div class="md:col-span-1">
              <label for="registration_number" class="block text-xs font-medium text-slate-700">
                Registration number
              </label>
              <input id="registration_number" name="registration_number" type="text"
                     value="{{ old('registration_number') }}"
                     class="mt-1 block w-full rounded-xl border-slate-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-slate-300"
                     placeholder="e.g., DR-XY-12345">
            </div>

            {{-- Issued --}}
            <div>
              <label for="issued_at" class="block text-xs font-medium text-slate-700">
                Date issued
              </label>
              <input id="issued_at" name="issued_at" type="date"
                     value="{{ old('issued_at') }}"
                     class="mt-1 block w-full rounded-xl border-slate-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-slate-300">
            </div>

            {{-- Expires --}}
            <div>
              <label for="expires_at" class="block text-xs font-medium text-slate-700">
                Date of expiry
              </label>
              <input id="expires_at" name="expires_at" type="date"
                     value="{{ old('expires_at') }}"
                     class="mt-1 block w-full rounded-xl border-slate-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-slate-300">
            </div>
          </div>
        </section>

        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
          <a href="{{ route('admin.drug_guide.index') }}"
             class="inline-flex items-center gap-1 text-[13px] text-slate-600 hover:text-slate-800">
            <i data-lucide="chevron-left" class="h-4 w-4"></i>
            <span>Back to list</span>
          </a>

          <div class="flex items-center gap-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 text-white px-4 py-2.5 text-[13px] font-medium shadow hover:bg-slate-800 active:scale-[.99]">
              <i data-lucide="save" class="h-4 w-4"></i>
              <span>Save Drug Product</span>
            </button>
          </div>
        </div>
      </form>

    </div>
  </section>
</main>

@include('partials.admin-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script>
  if (window.lucide?.createIcons) lucide.createIcons();
</script>
</body>
</html>
