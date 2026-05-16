<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $product->brand_name ?: ($product->substance?->name ?? 'Unknown generic') }} · Drug Guide · NurSync (Student)</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style> body{font-family:'Poppins',ui-sans-serif,system-ui,sans-serif;} </style>
</head>
<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  @include('partials.sidebar', ['active' => 'drug_guide'])

  <section class="flex-1">
    <div class="container mx-auto px-8 py-12 space-y-6">

      <header class="flex items-center justify-between">
        <div class="space-y-1">
          <h1 class="text-[28px] font-extrabold tracking-tight text-slate-900">
            {{ $product->brand_name ?: '—' }}
          </h1>
          <p class="text-sm text-slate-600">{{ $product->substance->name ?? 'Unknown generic' }}</p>
        </div>
        <a href="{{ route('student.drugs.index') }}"
           class="rounded-lg border px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
          ‹ Back to Drug Guide
        </a>
      </header>

      <div class="grid gap-6 md:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
          <div class="grid grid-cols-1 gap-3 text-sm">
            <div class="flex items-center justify-between">
              <span class="text-slate-500">Registration #</span>
              <span class="font-medium text-slate-900">{{ $product->registration_number ?: '—' }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-500">Strength</span>
              <span class="font-medium text-slate-900">{{ $product->dosage_strength ?: '—' }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-500">Dosage form</span>
              <span class="font-medium text-slate-900">{{ optional($product->dosageForm)->name ?: '—' }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-500">Category</span>
              <span class="font-medium text-slate-900">{{ optional($product->category)->name ?: '—' }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-500">Classification</span>
              <span class="font-medium text-slate-900">{{ $product->classification ?: '—' }}</span>
            </div>
            <div>
              <div class="text-slate-500 text-sm">Packaging</div>
              <div class="mt-1 text-slate-900 text-sm">{{ $product->packaging ?: '—' }}</div>
            </div>
          </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
          <div class="grid grid-cols-1 gap-3 text-sm">
            <div class="flex items-center justify-between">
              <span class="text-slate-500">Manufacturer</span>
              <span class="font-medium text-slate-900">{{ optional($product->manufacturer)->name ?: '—' }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-500">Importer</span>
              <span class="font-medium text-slate-900">{{ optional($product->importer)->name ?: '—' }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-500">Distributor</span>
              <span class="font-medium text-slate-900">{{ optional($product->distributor)->name ?: '—' }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-500">Trader</span>
              <span class="font-medium text-slate-900">{{ optional($product->trader)->name ?: '—' }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-500">Country of origin</span>
              <span class="font-medium text-slate-900">{{ $product->country_of_origin ?: '—' }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-500">Application type</span>
              <span class="font-medium text-slate-900">{{ $product->application_type ?: '—' }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-500">Issued</span>
              <span class="font-medium text-slate-900">{{ optional($product->issued_at)->toDateString() ?: '—' }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-500">Expires</span>
              <span class="font-medium text-slate-900">{{ optional($product->expires_at)->toDateString() ?: '—' }}</span>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</main>

@includeIf('partials.student-footer')
@includeWhen(!View::exists('partials.student-footer'), 'partials.faculty-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>
</body>
</html>
