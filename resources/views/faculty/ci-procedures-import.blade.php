<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Import (PDF / DOCX) · NurSync – Nurse Assistance (CI)</title>

  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style> body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; } </style>
</head>
<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Sidebar --}}
  @include('partials.faculty-sidebar', ['active' => 'procedures'])

  {{-- Main content --}}
  <section class="flex-1">
    <div class="container mx-auto px-8 py-12 space-y-8">

      {{-- Header --}}
      <header class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
            <i data-lucide="upload" class="h-4 w-4"></i>
          </span>
          <h1 class="text-[28px] font-extrabold tracking-tight text-slate-900">
            Import (PDF / DOCX)
          </h1>
        </div>
        <a href="{{ route('faculty.procedures.index') }}"
           class="rounded-lg border px-3 py-1.5 text-sm hover:bg-slate-50 inline-flex items-center gap-2">
          <i data-lucide="arrow-left" class="h-4 w-4"></i> Back to Library
        </a>
      </header>

      {{-- Flash + errors --}}
      @if (session('ok'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-2">
          {{ session('ok') }}
        </div>
      @endif
      @if ($errors->any())
        <div class="rounded-lg border border-rose-200 bg-rose-50 text-rose-800 px-4 py-2">
          There were validation errors. Please review and try again.
        </div>
      @endif

      {{-- Form --}}
      <form action="{{ route('faculty.procedures.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        <input type="hidden" name="source" value="import">

        {{-- Uploader --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
          <h2 class="text-lg font-semibold text-slate-900">Upload Source File</h2>
          <p class="mt-1 text-sm text-slate-600">Attach a PDF for immediate viewing or a DOCX to optionally parse steps and sections.</p>

          <div class="mt-4">
            {{-- Single input for now; controller can accept PDF (current) & DOCX (once enabled) --}}
            <label class="block">
              <span class="sr-only">Choose PDF or DOCX</span>
              <div id="dropBox"
                   class="flex flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 px-6 py-8 text-center hover:bg-white">
                <i data-lucide="file-up" class="h-6 w-6 text-slate-600"></i>
                <div class="text-sm text-slate-700">
                  <span class="font-medium">Click to choose</span> or drag &amp; drop
                </div>
                <div class="text-[12px] text-slate-500">PDF or DOCX · max 8 MB</div>
                <input id="fileInput" name="pdf" type="file"
                       class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
                       accept="application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
              </div>
            </label>
            <div id="fileName" class="mt-2 text-xs text-slate-600"></div>

            {{-- Parser toggle (shown only if DOCX selected) --}}
            <div id="parseWrap" class="mt-3 hidden">
              <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="parse_steps" value="1" class="rounded border-slate-300">
                Try to parse title/description/steps from DOCX
              </label>
              <div class="mt-1 text-[12px] text-slate-500">
                We’ll create a draft with the extracted content. You can refine it on the Edit page.
              </div>
            </div>

            <div class="mt-3 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-[12px] text-amber-800">
              Tip: If you only need a downloadable handout, upload a PDF. If you want editable steps, use a DOCX.
            </div>
          </div>
        </div>

        {{-- Basics --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
          <h2 class="text-lg font-semibold text-slate-900">Basic Information</h2>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="text-xs font-medium text-slate-600">Title</label>
              <input id="titleInput" name="title" value="{{ old('title') }}" required
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm focus:ring-2 focus:ring-slate-200"/>
              @error('title')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="text-xs font-medium text-slate-600">Level</label>
              <select name="level" class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm">
                <option value="beginner" @selected(old('level')==='beginner')>Beginner</option>
                <option value="intermediate" @selected(old('level')==='intermediate')>Intermediate</option>
                <option value="advanced" @selected(old('level')==='advanced')>Advanced</option>
              </select>
              @error('level')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>
          </div>

          <div>
            <label class="text-xs font-medium text-slate-600">Description</label>
            <textarea name="description" rows="3"
              class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
              placeholder="Brief summary of this procedure...">{{ old('description') }}</textarea>
            @error('description')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="text-xs font-medium text-slate-600">PPE & Equipment (comma-separated)</label>
              <input name="ppe_csv" value="{{ old('ppe_csv') }}"
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
              @error('ppe_csv')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="text-xs font-medium text-slate-600">Tags (comma-separated)</label>
              <input name="tags_csv" value="{{ old('tags_csv') }}"
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
              @error('tags_csv')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="text-xs font-medium text-slate-600">Hazards / Safety Notes</label>
              <textarea name="hazards_text" rows="2"
                class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200">{{ old('hazards_text') }}</textarea>
              @error('hazards_text')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="text-xs font-medium text-slate-600">Video URL (optional)</label>
              <input name="video_url" value="{{ old('video_url') }}"
                     placeholder="https://www.youtube.com/watch?v=…"
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
              <div class="mt-1 text-[11px] text-slate-500">You can paste a YouTube/Vimeo link. We’ll convert to an embeddable player.</div>
              @error('video_url')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
          <button name="action" value="draft"
                  class="rounded-lg border px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Save Draft
          </button>
          <button name="action" value="publish"
                  class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:opacity-95">
            Publish
          </button>
        </div>
      </form>

      {{-- Footer note --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-4 text-[13px] text-slate-600">
        Imported files are stored securely. Publishing makes the guide visible to students; drafts remain private.
      </div>

    </div>
  </section>
</main>

@includeIf('partials.faculty-footer')
@includeWhen(!View::exists('partials.faculty-footer'), 'partials.student-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>
<script>
  // Small UX helpers: show filename, toggle DOCX parsing option, and auto-fill title from filename.
  const fileInput = document.getElementById('fileInput');
  const fileName  = document.getElementById('fileName');
  const parseWrap = document.getElementById('parseWrap');
  const titleEl   = document.getElementById('titleInput');

  const isDocx = (f) => /\.docx$/i.test(f?.name || '');
  const baseName = (f) => (f?.name || '').replace(/\.[^.]+$/, '').replace(/[-_]+/g,' ').trim();

  fileInput?.addEventListener('change', () => {
    const f = fileInput.files?.[0];
    if (!f) { fileName.textContent = ''; parseWrap.classList.add('hidden'); return; }

    fileName.textContent = `Selected: ${f.name} (${Math.round(f.size/1024)} KB)`;
    parseWrap.classList.toggle('hidden', !isDocx(f));

    // If title is blank, suggest from filename
    if (titleEl && !titleEl.value.trim()) {
      const guess = baseName(f);
      if (guess) titleEl.value = guess.charAt(0).toUpperCase() + guess.slice(1);
    }
  });

  // Simple drag over styling for drop zone
  const dropBox = document.getElementById('dropBox');
  ;['dragenter','dragover'].forEach(evt => dropBox.addEventListener(evt, e => {
    e.preventDefault(); e.stopPropagation(); dropBox.classList.add('ring-2','ring-slate-200','bg-white');
  }));
  ;['dragleave','drop'].forEach(evt => dropBox.addEventListener(evt, e => {
    e.preventDefault(); e.stopPropagation(); dropBox.classList.remove('ring-2','ring-slate-200','bg-white');
  }));
  dropBox.addEventListener('drop', (e) => {
    const files = e.dataTransfer.files;
    if (files?.length) { fileInput.files = files; fileInput.dispatchEvent(new Event('change')); }
  });
</script>
</body>
</html>
