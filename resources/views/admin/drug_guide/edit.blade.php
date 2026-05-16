{{-- resources/views/admin/drug_guide/edit.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Edit Drug · NurSync (Admin)</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Sidebar --}}
  @include('partials.admin-sidebar', ['active' => 'drug_guide'])

  <section class="flex-1 min-w-0">

    {{-- Sticky header --}}
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <a href="{{ route('admin.drug_guide.index') }}"
             class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-[12px] hover:bg-slate-50">
            <i data-lucide="arrow-left" class="h-4 w-4 mr-1"></i>
            Back
          </a>

          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-900 text-white shadow-sm">
            <i data-lucide="pill" class="h-4 w-4"></i>
          </span>

          <div>
            <h1 class="text-[15px] sm:text-[16px] font-semibold leading-tight">
              Edit – {{ $product->brand_name ?? 'Drug Product' }}
            </h1>
            <p class="text-[12px] text-slate-500 -mt-0.5">
              {{ $product->substance->name ?? '—' }}
            </p>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <a href="{{ route('admin.drug_guide.show', $product->id) }}"
             class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-[13px] hover:bg-slate-50">
            <i data-lucide="book-open" class="h-4 w-4"></i>
            View Details
          </a>
        </div>
      </div>
    </header>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 space-y-6">

      {{-- Flash banners --}}
      @if (session('ok'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
          {{ session('ok') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
          Please fix the errors below and try again.
        </div>
      @endif

      {{-- Form --}}
      <form id="editDrugForm"
            action="{{ route('admin.drug_guide.update', $product->id) }}"
            method="POST"
            class="space-y-6">
        @csrf
        @method('PUT')

        {{-- BASICS --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
          <div class="flex items-center justify-between gap-2">
            <h2 class="text-[15px] font-semibold text-slate-900">Basics</h2>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="text-xs font-medium text-slate-600">Brand Name <span class="text-rose-500">*</span></label>
              <input
                name="brand_name"
                value="{{ old('brand_name', $product->brand_name) }}"
                required
                class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm focus:ring-2 focus:ring-slate-200"
              />
              @error('brand_name')
                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Active Substance <span class="text-rose-500">*</span></label>
              <select
                name="substance_id"
                required
                class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"
              >
                @foreach ($substances as $s)
                  <option value="{{ $s->id }}" @selected(old('substance_id', $product->substance_id) == $s->id)>
                    {{ $s->name }}
                  </option>
                @endforeach
              </select>
              @error('substance_id')
                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="grid gap-4 md:grid-cols-3">
            <div>
              <label class="text-xs font-medium text-slate-600">Pharmacologic Category <span class="text-rose-500">*</span></label>
              <select
                name="pharmacologic_category_id"
                required
                class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"
              >
                @foreach ($cats as $c)
                  <option value="{{ $c->id }}" @selected(old('pharmacologic_category_id', $product->pharmacologic_category_id) == $c->id)>
                    {{ $c->name }}
                  </option>
                @endforeach
              </select>
              @error('pharmacologic_category_id')
                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Dosage Form</label>
              <select
                name="dosage_form_id"
                class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"
              >
                <option value="">—</option>
                @foreach ($forms as $f)
                  <option value="{{ $f->id }}" @selected(old('dosage_form_id', $product->dosage_form_id) == $f->id)>
                    {{ $f->name }}
                  </option>
                @endforeach
              </select>
              @error('dosage_form_id')
                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Dosage Strength</label>
              <input
                name="dosage_strength"
                value="{{ old('dosage_strength', $product->dosage_strength) }}"
                class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"
              />
              @error('dosage_strength')
                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div>
            <label class="text-xs font-medium text-slate-600">Packaging</label>
            <textarea
              name="packaging"
              rows="2"
              class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
            >{{ old('packaging', $product->packaging) }}</textarea>
            @error('packaging')
              <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div class="grid gap-4 md:grid-cols-3">
            <div>
              <label class="text-xs font-medium text-slate-600">Classification</label>
              <input
                name="classification"
                value="{{ old('classification', $product->classification) }}"
                class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"
              />
              @error('classification')
                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Country of Origin</label>
              <input
                name="country_of_origin"
                value="{{ old('country_of_origin', $product->country_of_origin) }}"
                class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"
              />
              @error('country_of_origin')
                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Application Type</label>
              <input
                name="application_type"
                value="{{ old('application_type', $product->application_type) }}"
                class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"
              />
              @error('application_type')
                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>

        {{-- COMPANIES --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
          <h2 class="text-[15px] font-semibold text-slate-900">Companies</h2>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="text-xs font-medium text-slate-600">Manufacturer</label>
              <select
                name="manufacturer_id"
                class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"
              >
                <option value="">—</option>
                @foreach ($mfgs as $m)
                  <option value="{{ $m->id }}" @selected(old('manufacturer_id', $product->manufacturer_id) == $m->id)>
                    {{ $m->name }}
                  </option>
                @endforeach
              </select>
              @error('manufacturer_id')
                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Importer</label>
              <select
                name="importer_id"
                class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"
              >
                <option value="">—</option>
                @foreach ($importers as $c)
                  <option value="{{ $c->id }}" @selected(old('importer_id', $product->importer_id) == $c->id)>
                    {{ $c->name }}
                  </option>
                @endforeach
              </select>
              @error('importer_id')
                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Distributor</label>
              <select
                name="distributor_id"
                class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"
              >
                <option value="">—</option>
                @foreach ($distributors as $c)
                  <option value="{{ $c->id }}" @selected(old('distributor_id', $product->distributor_id) == $c->id)>
                    {{ $c->name }}
                  </option>
                @endforeach
              </select>
              @error('distributor_id')
                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Trader</label>
              <select
                name="trader_id"
                class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"
              >
                <option value="">—</option>
                @foreach ($traders as $c)
                  <option value="{{ $c->id }}" @selected(old('trader_id', $product->trader_id) == $c->id)>
                    {{ $c->name }}
                  </option>
                @endforeach
              </select>
              @error('trader_id')
                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>

        {{-- REGULATORY --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
          <h2 class="text-[15px] font-semibold text-slate-900">Regulatory</h2>

          <div class="grid gap-4 md:grid-cols-3">
            <div>
              <label class="text-xs font-medium text-slate-600">Registration Number</label>
              <input
                name="registration_number"
                value="{{ old('registration_number', $product->registration_number) }}"
                class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"
              />
              @error('registration_number')
                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Issued At</label>
              <input
                type="date"
                name="issued_at"
                value="{{ old('issued_at', optional($product->issued_at)->format('Y-m-d')) }}"
                class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"
              />
              @error('issued_at')
                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Expires At</label>
              <input
                type="date"
                name="expires_at"
                value="{{ old('expires_at', optional($product->expires_at)->format('Y-m-d')) }}"
                class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"
              />
              @error('expires_at')
                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
          <a href="{{ route('admin.drug_guide.show', $product->id) }}"
             class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            <i data-lucide="x" class="h-4 w-4"></i>
            Cancel
          </a>

          <button type="submit"
                  class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:opacity-95">
            <i data-lucide="save" class="h-4 w-4"></i>
            Save Changes
          </button>
        </div>
      </form>

      {{-- Footer note --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-4 text-[13px] text-slate-600">
        Changes take effect immediately after saving. Double-check regulatory dates and company assignments.
      </div>
    </div>
  </section>
</main>

@include('partials.admin-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script>
  if (window.lucide?.createIcons) lucide.createIcons();

  // Unsaved changes guard
  const form = document.getElementById('editDrugForm');
  let dirty = false;

  if (form) {
    form.addEventListener('input', () => { dirty = true; });
    form.addEventListener('change', () => { dirty = true; });
    form.addEventListener('submit', () => { dirty = false; });

    window.addEventListener('beforeunload', (e) => {
      if (!dirty) return;
      e.preventDefault();
      e.returnValue = '';
    });
  }
</script>
</body>
</html>
