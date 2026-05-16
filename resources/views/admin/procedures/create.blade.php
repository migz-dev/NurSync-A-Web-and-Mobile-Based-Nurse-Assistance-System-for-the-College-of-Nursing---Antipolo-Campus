<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>New Procedure · NurSync (Admin)</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style> body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; } </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Sidebar --}}
  @include('partials.admin-sidebar', ['active' => 'resources'])

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
            Create New Procedure
          </h1>
        </div>
        <a href="{{ route('admin.procedures.index') }}"
           class="rounded-lg border px-3 py-1.5 text-sm hover:bg-slate-50 inline-flex items-center gap-2">
          <i data-lucide="arrow-left" class="h-4 w-4"></i> Back to Library
        </a>
      </header>

      {{-- Form --}}
      <form action="{{ route('admin.procedures.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        {{-- Basics --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
          <h2 class="text-lg font-semibold text-slate-900">Basic Information</h2>

          <div class="grid gap-4 md:grid-cols-2">
            {{-- Title --}}
            <div>
              <label class="text-xs font-medium text-slate-600">Title</label>
              <input name="title" value="{{ old('title') }}" required
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm focus:ring-2 focus:ring-slate-200"/>
              @error('title')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>

            {{-- Clinical Ward --}}
            <div>
              <label class="text-xs font-medium text-slate-600">Clinical Ward / Area</label>
              <select name="clinical_wards" required
                      class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm">
                <option value="">Select area</option>
                @php
                  $wards = [
                    'Community Health (CHN)',
                    'OB Ward',
                    'Delivery Room (DR)',
                    'Nursery',
                    'Pediatrics (PEDIA)',
                    'Medical-Surgical (MS)',
                    'ICU',
                    'Oncology',
                    'Isolation Unit',
                    'Endocrine Unit',
                    'Neurology Unit',
                    'Psychiatric (PSYCH)',
                    'Emergency Room (ER)',
                    'Operating Room (OR)',
                    'Trauma Unit',
                    'Disaster Response / Community Field'
                  ];
                @endphp
                @foreach($wards as $w)
                  <option value="{{ $w }}" @selected(old('clinical_wards')===$w)>{{ $w }}</option>
                @endforeach
              </select>
              @error('clinical_wards')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Description --}}
          <div>
            <label class="text-xs font-medium text-slate-600">Description</label>
            <textarea name="description" rows="3"
                      class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                      placeholder="Brief summary of this procedure...">{{ old('description') }}</textarea>
          </div>

          {{-- PPE & Tags --}}
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="text-xs font-medium text-slate-600">PPE & Equipment (comma-separated)</label>
              <input name="ppe_csv" value="{{ old('ppe_csv') }}"
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
            </div>
            <div>
              <label class="text-xs font-medium text-slate-600">Tags (comma-separated)</label>
              <input name="tags_csv" value="{{ old('tags_csv') }}"
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
            </div>
          </div>

          {{-- Videos --}}
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="text-xs font-medium text-slate-600">Video URL (optional)</label>
              <input name="video_url" value="{{ old('video_url') }}"
                     placeholder="https://www.youtube.com/watch?v=… or https://vimeo.com/…"
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
              <div class="mt-1 text-[11px] text-slate-500">
                You can paste a YouTube/Vimeo link; we’ll convert it to an embeddable player.
              </div>
              @error('video_url')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Upload Video (optional)</label>
              <input type="file" name="video_file"
                     accept="video/mp4,video/webm,video/ogg"
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
              <div class="mt-1 text-[11px] text-slate-500">
                MP4 / WebM / Ogg • up to 200&nbsp;MB. If you upload a file, it will be used instead of the URL.
              </div>
              @error('video_file')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Hazards --}}
          <div>
            <label class="text-xs font-medium text-slate-600">Hazards / Safety Notes</label>
            <textarea name="hazards_text" rows="2"
                      class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200">{{ old('hazards_text') }}</textarea>
          </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
          <input type="hidden" name="action" id="jsAction" value="draft">
          <button type="submit"
                  class="rounded-lg border px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Save Draft
          </button>
          <button type="submit" onclick="document.getElementById('jsAction').value='publish'"
                  class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:opacity-95">
            Publish Now
          </button>
        </div>
      </form>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 text-[13px] text-slate-600">
        After creation, you can add steps and detailed content in the <strong>Edit</strong> page.
      </div>

    </div>
  </section>
</main>

@include('partials.admin-footer')
<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>
</body>
</html>