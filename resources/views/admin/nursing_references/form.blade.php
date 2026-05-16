<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <title>{{ $pageTitle ?? 'Nursing Reference' }} · NurSync (Admin)</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style> body { font-family:'Poppins', ui-sans-serif, system-ui, sans-serif; } </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Sidebar --}}
  @include('partials.admin-sidebar', ['active' => 'nursing_references'])

  {{-- Main content --}}
  <section class="flex-1">
    <div class="container mx-auto px-8 py-12 space-y-8">

      {{-- Header --}}
      <header class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
            <i data-lucide="{{ $mode === 'edit' ? 'pencil' : 'book-open-check' }}" class="h-4 w-4"></i>
          </span>
          <div>
            <h1 class="text-[28px] font-extrabold tracking-tight text-slate-900">
              {{ $pageTitle ?? ($mode === 'edit' ? 'Edit Nursing Reference' : 'Create Nursing Reference') }}
            </h1>
            <p class="text-[13px] text-slate-500">
              {{ $mode === 'edit'
                  ? 'Update details of this external reference site.'
                  : 'Add a trusted external site used by nurses for guidelines, drugs, or education.' }}
            </p>
          </div>
        </div>

        <a href="{{ route('admin.nursing_references.index') }}"
           class="rounded-lg border px-3 py-1.5 text-sm hover:bg-slate-50 inline-flex items-center gap-2">
          <i data-lucide="arrow-left" class="h-4 w-4"></i>
          Back to Nursing References
        </a>
      </header>

      {{-- Flash / errors --}}
      @if (session('ok'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
          {{ session('ok') }}
        </div>
      @endif
      @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
          {{ $errors->first() }}
        </div>
      @endif

      {{-- Form --}}
      @php
        $isEdit = $mode === 'edit';
        $action = $isEdit
          ? route('admin.nursing_references.update', $ref->id)
          : route('admin.nursing_references.store');
      @endphp

      <form action="{{ $action }}" method="POST" class="space-y-8">
        @csrf
        @if($isEdit)
          @method('PUT')
        @endif

        {{-- Basic Information --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
          <h2 class="text-lg font-semibold text-slate-900">Basic Information</h2>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="text-xs font-medium text-slate-600">
                Title <span class="text-rose-600">*</span>
              </label>
              <input name="title"
                     value="{{ old('title', $ref->title) }}"
                     required
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm focus:ring-2 focus:ring-slate-200"
                     placeholder="e.g., World Health Organization (WHO)"/>
              @error('title')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">
                Category <span class="text-rose-600">*</span>
              </label>
              <input name="category"
                     value="{{ old('category', $ref->category) }}"
                     list="nr-category-options"
                     required
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"
                     placeholder="e.g., Clinical Guidelines, Drug Reference, Journals"/>
              @error('category')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror

              @if(!empty($categories))
                <datalist id="nr-category-options">
                  @foreach($categories as $c)
                    @if($c) <option value="{{ $c }}">{{ $c }}</option> @endif
                  @endforeach
                </datalist>
              @endif
            </div>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="text-xs font-medium text-slate-600">Source / Organization</label>
              <input name="source"
                     value="{{ old('source', $ref->source) }}"
                     list="nr-source-options"
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"
                     placeholder="e.g., WHO, CDC, MIMS, Lippincott"/>
              @error('source')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror

              @if(!empty($sources))
                <datalist id="nr-source-options">
                  @foreach($sources as $s)
                    @if($s) <option value="{{ $s }}">{{ $s }}</option> @endif
                  @endforeach
                </datalist>
              @endif
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Tags (optional)</label>
              <input name="tags_text"
                     value="{{ old('tags_text', $tagsText ?? '') }}"
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"
                     placeholder="Comma-separated, e.g., guidelines, infection-control, philippines"/>
              <p class="mt-1 text-[11px] text-slate-400">
                These will be converted into tags for filtering and search.
              </p>
            </div>
          </div>
        </div>

        {{-- Link & Meta --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
          <h2 class="text-lg font-semibold text-slate-900">Link & Meta</h2>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="text-xs font-medium text-slate-600">
                URL <span class="text-rose-600">*</span>
              </label>
              <input name="url"
                     value="{{ old('url', $ref->url) }}"
                     required
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm focus:ring-2 focus:ring-slate-200"
                     placeholder="https://example.org/path"/>
              @error('url')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="flex items-end gap-4">
              <div class="flex items-center gap-2 mt-6">
                <input id="nr-is-featured" type="checkbox" name="is_featured" value="1"
                       class="h-4 w-4 rounded border-slate-300 text-slate-900"
                       @checked(old('is_featured', $ref->is_featured))>
                <label for="nr-is-featured" class="text-xs font-medium text-slate-700">
                  Mark as featured
                </label>
              </div>

              <div class="flex items-center gap-2 mt-6">
                <input id="nr-is-active" type="checkbox" name="is_active" value="1"
                       class="h-4 w-4 rounded border-slate-300 text-slate-900"
                       @checked(old('is_active', $ref->is_active ?? true))>
                <label for="nr-is-active" class="text-xs font-medium text-slate-700">
                  Active (visible to faculty)
                </label>
              </div>
            </div>
          </div>
        </div>

        {{-- Description --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
          <h2 class="text-lg font-semibold text-slate-900">Description</h2>
          <p class="text-[12px] text-slate-500">
            Provide a short summary so faculty understand how and when to use this reference.
          </p>

          <textarea name="description" rows="4"
                    class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                    placeholder="e.g., Global guidelines on communicable diseases, vaccines, and outbreak management.">{{ old('description', $ref->description) }}</textarea>
          @error('description')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
          <button type="submit"
                  name="submit_type" value="save"
                  class="rounded-lg border px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Save
          </button>
          <button type="submit"
                  name="submit_type" value="create"
                  class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:opacity-95">
            {{ $isEdit ? 'Save Changes' : 'Create Reference' }}
          </button>
        </div>
      </form>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 text-[13px] text-slate-600">
        After {{ $isEdit ? 'updating' : 'creation' }}, you can further refine details from this <strong>form</strong> anytime.
      </div>
    </div>
  </section>
</main>

@include('partials.admin-footer')
<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>
</body>
</html>
