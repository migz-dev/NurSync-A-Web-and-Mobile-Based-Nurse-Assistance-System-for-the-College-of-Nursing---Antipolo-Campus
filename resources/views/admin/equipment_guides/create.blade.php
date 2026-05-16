<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <title>New Equipment · NurSync (Admin)</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style> body { font-family:'Poppins', ui-sans-serif, system-ui, sans-serif; } </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Sidebar --}}
  @include('partials.admin-sidebar', ['active' => 'equipment-guides'])

  {{-- Main content --}}
  <section class="flex-1">
    <div class="container mx-auto px-8 py-12 space-y-8">

      {{-- Header --}}
      <header class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
            <i data-lucide="plus" class="h-4 w-4"></i>
          </span>
          <h1 class="text-[28px] font-extrabold tracking-tight text-slate-900">
            Create New Equipment
          </h1>
        </div>
        <a href="{{ route('admin.equipment_guide.index') }}"
           class="rounded-lg border px-3 py-1.5 text-sm hover:bg-slate-50 inline-flex items-center gap-2">
          <i data-lucide="arrow-left" class="h-4 w-4"></i>
          Back to Equipment Guides
        </a>
      </header>

      {{-- Flash / errors --}}
      @if (session('ok'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('ok') }}</div>
      @endif
      @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
          {{ $errors->first() }}
        </div>
      @endif

      {{-- Form --}}
      <form action="{{ route('admin.equipment_guide.store') }}" method="POST" class="space-y-8">
        @csrf

        {{-- Basic Information --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
          <h2 class="text-lg font-semibold text-slate-900">Basic Information</h2>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="text-xs font-medium text-slate-600">Item Name <span class="text-rose-600">*</span></label>
              <input name="item_name" value="{{ old('item_name') }}" required
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm focus:ring-2 focus:ring-slate-200"
                     placeholder="e.g., Stethoscope"/>
              @error('item_name')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Category</label>
              <input name="category" value="{{ old('category') }}" list="eg-category-options"
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"
                     placeholder="e.g., General, Instruments, PPE"/>
              @error('category')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
              @if(!empty($categories))
                <datalist id="eg-category-options">
                  @foreach($categories as $c)
                    @if($c) <option value="{{ $c }}">{{ $c }}</option> @endif
                  @endforeach
                </datalist>
              @endif
            </div>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="text-xs font-medium text-slate-600">Ward Scope</label>
              <input name="ward_scope" value="{{ old('ward_scope') }}" list="eg-ward-options"
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"
                     placeholder="e.g., All, ER, OR, OB, Pedia, ICU, MS"/>
              @error('ward_scope')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
              @if(!empty($wards))
                <datalist id="eg-ward-options">
                  @foreach($wards as $w)
                    @if($w) <option value="{{ $w }}">{{ $w }}</option> @endif
                  @endforeach
                </datalist>
              @endif
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Variants / Examples</label>
              <input name="variants_or_examples" value="{{ old('variants_or_examples') }}"
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"
                     placeholder="e.g., Adult, Pediatric, Cardiology"/>
              @error('variants_or_examples')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>

        {{-- Usage & Mapping --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
          <h2 class="text-lg font-semibold text-slate-900">Usage & Mapping</h2>

          <div>
            <label class="text-xs font-medium text-slate-600">Typical Uses</label>
            <textarea name="typical_uses" rows="3"
                      class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                      placeholder="What is this equipment typically used for? (e.g., auscultation of heart, lungs, and bowel sounds)">{{ old('typical_uses') }}</textarea>
            @error('typical_uses')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
          </div>

          <div>
            <label class="text-xs font-medium text-slate-600">Related Procedures / Tasks</label>
            <textarea name="related_procedures_or_tasks" rows="2"
                      class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                      placeholder="e.g., Vitals monitoring, Physical assessment, IV insertion">{{ old('related_procedures_or_tasks') }}</textarea>
            @error('related_procedures_or_tasks')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
          </div>
        </div>

        {{-- Notes --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
          <h2 class="text-lg font-semibold text-slate-900">Notes</h2>
          <textarea name="notes" rows="3"
                    class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                    placeholder="Cleaning, storage, hazards, or maintenance notes…">{{ old('notes') }}</textarea>
          @error('notes')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
          <button type="submit"
                  class="rounded-lg border px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Save
          </button>
          <button type="submit"
                  class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:opacity-95">
            Create Equipment
          </button>
        </div>
      </form>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 text-[13px] text-slate-600">
        After creation you can refine details from the <strong>Edit</strong> page.
      </div>
    </div>
  </section>
</main>

@include('partials.admin-footer')
<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>
</body>
</html>
