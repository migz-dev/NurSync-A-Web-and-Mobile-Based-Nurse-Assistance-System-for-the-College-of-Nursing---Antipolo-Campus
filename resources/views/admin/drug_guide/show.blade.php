{{-- resources/views/admin/drug_guide/show.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Admin • Drug Product · NurSync</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family:'Poppins',ui-sans-serif,system-ui,sans-serif; }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Sidebar --}}
  @include('partials.admin-sidebar', ['active' => 'drug_guide'])

  <section class="flex-1 min-w-0">
    {{-- Header --}}
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
              {{ $product->brand_name ?? 'Untitled Product' }}
            </h1>
            <p class="text-[12px] text-slate-500 -mt-0.5">
              {{ $product->substance->name ?? '—' }}
            </p>
          </div>
        </div>

        {{-- Actions: Edit / Delete --}}
        <div class="flex items-center gap-2">
          <a href="{{ route('admin.drug_guide.edit', $product->id) }}"
             class="inline-flex items-center gap-2 rounded-xl bg-yellow-400 text-slate-900 px-3 py-2 text-[13px] hover:brightness-95">
            <i data-lucide="pencil" class="h-4 w-4"></i>
            Edit
          </a>

          <form method="POST"
                action="{{ route('admin.drug_guide.destroy', $product->id) }}"
                class="js-delete-form"
                data-title="{{ $product->brand_name ?? 'this product' }}">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-red-600 text-white px-3 py-2 text-[13px] hover:bg-red-700">
              <i data-lucide="trash-2" class="h-4 w-4"></i>
              Delete
            </button>
          </form>
        </div>
      </div>
    </header>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 space-y-6">
      {{-- Flash --}}
      @if(session('ok'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
          {{ session('ok') }}
        </div>
      @endif

      @php
        $expired       = optional($product->expires_at)?->isPast();
        $classification = $product->classification;
        $packaging      = $product->packaging;
        $packagingType  = $packaging
          ? \Illuminate\Support\Str::of($packaging)->before(' ')->trim()
          : null;
      @endphp

      {{-- Overview + status chips --}}
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-3">
        <div class="flex flex-wrap items-center gap-2 text-[11px]">
          {{-- Classification chip --}}
          @if($classification)
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 px-2 py-0.5 font-medium">
              <i data-lucide="badge-check" class="h-3 w-3"></i>
              {{ $classification }}
            </span>
          @endif

          {{-- Packaging type chip --}}
          @if($packagingType)
            <span class="inline-flex items-center gap-1.5 rounded-full bg-violet-50 text-violet-700 border border-violet-100 px-2 py-0.5 font-medium">
              <i data-lucide="package" class="h-3 w-3"></i>
              {{ $packagingType }}
            </span>
          @endif

          {{-- Expiry status --}}
          <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 font-medium
                       {{ $expired ? 'bg-rose-50 text-rose-700 border border-rose-100' : 'bg-green-50 text-green-700 border border-green-100' }}">
            @if($expired)
              <i data-lucide="alert-triangle" class="h-3.5 w-3.5"></i>
              Expired
            @else
              <i data-lucide="check-circle" class="h-3.5 w-3.5"></i>
              Active
            @endif
          </span>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 text-[13px] mt-2">
          <div>
            <div class="text-slate-500">Registration No.</div>
            <div class="mt-1 text-slate-800">{{ $product->registration_number ?? '—' }}</div>
          </div>
          <div>
            <div class="text-slate-500">Category</div>
            <div class="mt-1 text-slate-800">{{ $product->category->name ?? '—' }}</div>
          </div>
          <div>
            <div class="text-slate-500">Dosage Strength</div>
            <div class="mt-1 text-slate-800">{{ $product->dosage_strength ?? '—' }}</div>
          </div>

          <div>
            <div class="text-slate-500">Dosage Form</div>
            <div class="mt-1 text-slate-800">{{ $product->dosageForm->name ?? '—' }}</div>
          </div>
          <div>
            <div class="text-slate-500">Packaging</div>
            <div class="mt-1 text-slate-800">{{ $packaging ?? '—' }}</div>
          </div>
          <div>
            <div class="text-slate-500">Application Type</div>
            <div class="mt-1 text-slate-800">{{ $product->application_type ?? '—' }}</div>
          </div>

          <div>
            <div class="text-slate-500">Issued</div>
            <div class="mt-1 text-slate-800">
              {{ $product->issued_at ? $product->issued_at->format('M d, Y') : '—' }}
            </div>
          </div>
          <div>
            <div class="text-slate-500">Expires</div>
            <div class="mt-1 {{ $expired ? 'text-rose-600 font-medium' : 'text-slate-800' }}">
              {{ $product->expires_at ? $product->expires_at->format('M d, Y') : '—' }}
            </div>
          </div>
          <div>
            <div class="text-slate-500">Country of Origin</div>
            <div class="mt-1 text-slate-800">{{ $product->country_of_origin ?? '—' }}</div>
          </div>
        </div>
      </div>

      {{-- Companies --}}
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <h2 class="text-[14px] font-semibold mb-3">Companies</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 text-[13px]">
          <div>
            <div class="text-slate-500">Manufacturer</div>
            <div class="mt-1 text-slate-800">
              {{ $product->manufacturer->name ?? '—' }}
              @if(!empty($product->manufacturer?->country))
                <span class="text-slate-400 text-[12px] block">
                  {{ $product->manufacturer->country }}
                </span>
              @endif
            </div>
          </div>
          <div>
            <div class="text-slate-500">Importer</div>
            <div class="mt-1 text-slate-800">
              {{ $product->importer->name ?? '—' }}
              @if(!empty($product->importer?->country))
                <span class="text-slate-400 text-[12px] block">
                  {{ $product->importer->country }}
                </span>
              @endif
            </div>
          </div>
          <div>
            <div class="text-slate-500">Distributor</div>
            <div class="mt-1 text-slate-800">
              {{ $product->distributor->name ?? '—' }}
              @if(!empty($product->distributor?->country))
                <span class="text-slate-400 text-[12px] block">
                  {{ $product->distributor->country }}
                </span>
              @endif
            </div>
          </div>
          <div>
            <div class="text-slate-500">Trader</div>
            <div class="mt-1 text-slate-800">
              {{ $product->trader->name ?? '—' }}
              @if(!empty($product->trader?->country))
                <span class="text-slate-400 text-[12px] block">
                  {{ $product->trader->country }}
                </span>
              @endif
            </div>
          </div>
        </div>
      </div>

      {{-- Notes (optional long text field if you have one) --}}
      @if(!empty($product->remarks) || !empty($product->notes))
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
          <h2 class="text-[14px] font-semibold mb-2">Notes</h2>
          <p class="text-[13px] text-slate-700 leading-6">
            {{ $product->remarks ?? $product->notes }}
          </p>
        </div>
      @endif

      {{-- Attachments (optional) --}}
      @if(!empty($product->file_path))
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
          <h2 class="text-[14px] font-semibold mb-3">Attachments</h2>
          <a target="_blank" href="{{ asset($product->file_path) }}"
             class="inline-flex items-center gap-2 text-slate-800 hover:underline text-[13px]">
            <i data-lucide="paperclip" class="h-4 w-4"></i>
            Open file
          </a>
        </div>
      @endif
    </div>
  </section>
</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // Delete confirm
  (function () {
    const form = document.querySelector('.js-delete-form');
    if (!form) return;

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const name = this.dataset.title || 'this product';

      Swal.fire({
        title: 'Delete drug product?',
        html: `Are you sure you want to delete <b>${name}</b>? This cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        focusCancel: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#334155'
      }).then(result => {
        if (result.isConfirmed) this.submit();
      });
    }, { once: true });
  })();

  if (window.lucide?.createIcons) lucide.createIcons();
</script>
</body>
</html>
