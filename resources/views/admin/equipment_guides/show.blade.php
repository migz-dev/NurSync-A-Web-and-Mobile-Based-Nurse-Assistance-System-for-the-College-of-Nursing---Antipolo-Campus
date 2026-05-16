<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <title>Admin • Equipment · NurSync</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style> body{font-family:'Poppins',ui-sans-serif,system-ui,sans-serif;} </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Sidebar --}}
  @include('partials.admin-sidebar', ['active' => 'equipment-guides'])

  <section class="flex-1 min-w-0">
    {{-- Header --}}
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <a href="{{ route('admin.equipment_guide.index') }}"
             class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-[12px] hover:bg-slate-50">
            <i data-lucide="arrow-left" class="h-4 w-4 mr-1"></i> Back
          </a>
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-900 text-white shadow-sm">
            <i data-lucide="wrench" class="h-4 w-4"></i>
          </span>
          <div>
            <h1 class="text-[15px] sm:text-[16px] font-semibold leading-tight">
              {{ $item->item_name ?? 'Untitled Equipment' }}
            </h1>
            <p class="text-[12px] text-slate-500 -mt-0.5">
              {{ $item->category ?? '—' }} {{ $item->ward_scope ? '• '.$item->ward_scope : '' }}
            </p>
          </div>
        </div>

        {{-- Actions: Edit / Delete --}}
        <div class="flex items-center gap-2">
          <a href="{{ route('admin.equipment_guide.edit', $item->id) }}"
             class="inline-flex items-center gap-2 rounded-xl bg-yellow-400 text-slate-900 px-3 py-2 text-[13px] hover:brightness-95">
            <i data-lucide="pencil" class="h-4 w-4"></i> Edit
          </a>

          <form method="POST"
                action="{{ route('admin.equipment_guide.destroy', $item->id) }}"
                class="js-delete-form"
                data-title="{{ $item->item_name ?? 'this item' }}">
            @csrf @method('DELETE')
            <button class="inline-flex items-center gap-2 rounded-xl bg-red-600 text-white px-3 py-2 text-[13px] hover:bg-red-700">
              <i data-lucide="trash-2" class="h-4 w-4"></i> Delete
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

      {{-- Meta --}}
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 text-[13px]">
          <div>
            <div class="text-slate-500">Category</div>
            <div class="mt-1 text-slate-800">{{ $item->category ?? '—' }}</div>
          </div>
          <div>
            <div class="text-slate-500">Ward Scope</div>
            <div class="mt-1 text-slate-800">{{ $item->ward_scope ?? '—' }}</div>
          </div>
          <div>
            <div class="text-slate-500">Variants / Examples</div>
            <div class="mt-1 text-slate-800">{{ $item->variants_or_examples ?? '—' }}</div>
          </div>

          <div class="sm:col-span-2 lg:col-span-3">
            <div class="text-slate-500">Typical Uses</div>
            <div class="mt-1 text-slate-800 leading-6">
              {{ $item->typical_uses ?? '—' }}
            </div>
          </div>

          <div class="sm:col-span-2 lg:col-span-3">
            <div class="text-slate-500">Related Procedures / Tasks</div>
            <div class="mt-1 text-slate-800 leading-6">
              {{ $item->related_procedures_or_tasks ?? '—' }}
            </div>
          </div>

          <div>
            <div class="text-slate-500">Created</div>
            <div class="mt-1 text-slate-800">
              {{ optional($item->created_at)->format('M d, Y h:ia') ?? '—' }}
            </div>
          </div>
          <div>
            <div class="text-slate-500">Last Updated</div>
            <div class="mt-1 text-slate-800">
              {{ optional($item->updated_at)->format('M d, Y h:ia') ?? '—' }}
            </div>
          </div>
        </div>
      </div>

      {{-- Notes --}}
      @if(!empty($item->notes))
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
          <h2 class="text-[14px] font-semibold mb-2">Notes</h2>
          <p class="text-[13px] text-slate-700 leading-6">{{ $item->notes }}</p>
        </div>
      @endif
    </div>
  </section>
</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  (function () {
    const form = document.querySelector('.js-delete-form');
    if (!form) return;

    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const name = this.dataset.title || 'this item';

      Swal.fire({
        title: 'Delete equipment?',
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
</script>

<script> lucide.createIcons(); </script>
</body>
</html>
