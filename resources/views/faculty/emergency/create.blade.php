<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Create Emergency Protocol · NurSync (Faculty)</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Sidebar --}}
  @include('partials.faculty-sidebar', ['active' => 'emergency'])

  {{-- Main content --}}
  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-8 py-12 space-y-6">

      {{-- Header --}}
      <header class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-red-50 text-red-600">
            <i data-lucide="alert-triangle" class="h-4 w-4"></i>
          </span>
          <div>
            <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
              New Emergency Protocol
            </h1>
            <p class="text-[13px] text-slate-500 mt-1">
              Add a new emergency procedure to guide responses in clinical or simulated scenarios.
            </p>
          </div>
        </div>

        <a href="{{ route('faculty.emergency.index') }}"
           class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-[12px] font-medium text-slate-700 hover:bg-slate-50">
          <i data-lucide="chevron-left" class="h-4 w-4"></i>
          Back
        </a>
      </header>

      {{-- Form --}}
      <form method="POST" action="{{ route('faculty.emergency.store') }}" enctype="multipart/form-data"
            class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
        @csrf

        {{-- Title --}}
        <div>
          <label for="title" class="block text-[13px] font-medium text-slate-700 mb-1">
            Protocol Title <span class="text-red-500">*</span>
          </label>
          <input id="title" name="title" type="text" value="{{ old('title') }}"
                 placeholder="e.g., Code Blue Response, Anaphylaxis Management"
                 class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-red-300" required>
        </div>

        {{-- Summary --}}
        <div>
          <label for="summary" class="block text-[13px] font-medium text-slate-700 mb-1">Summary</label>
          <textarea id="summary" name="summary" rows="2"
                    placeholder="Brief overview of the emergency situation or management goals."
                    class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-red-300">{{ old('summary') }}</textarea>
        </div>

        {{-- Category + Ward --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="category" class="block text-[13px] font-medium text-slate-700 mb-1">Category / Type</label>
            <input id="category" name="category" type="text" value="{{ old('category') }}"
                   placeholder="e.g., Cardiac, Respiratory, Trauma"
                   class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-red-300">
          </div>

          <div>
            <label for="ward" class="block text-[13px] font-medium text-slate-700 mb-1">Applicable Ward / Area</label>
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
                'Disaster Response / Community Field',
              ];
            @endphp
            <select id="ward" name="ward"
                    class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-red-300">
              <option value="">Select ward</option>
              @foreach ($wards as $ward)
                <option value="{{ $ward }}" @selected(old('ward') === $ward)>
                  {{ $ward }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        {{-- Severity + Status --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="severity" class="block text-[13px] font-medium text-slate-700 mb-1">Severity</label>
            <select id="severity" name="severity"
                    class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-red-300">
              <option value="Critical" @selected(old('severity') === 'Critical')>Critical</option>
              <option value="Moderate" @selected(old('severity') === 'Moderate')>Moderate</option>
              <option value="Mild" @selected(old('severity') === 'Mild')>Mild</option>
            </select>
          </div>

          <div>
            <label for="status" class="block text-[13px] font-medium text-slate-700 mb-1">Status</label>
            <select id="status" name="status"
                    class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-red-300">
              <option value="draft" @selected(old('status') === 'draft')>Draft</option>
              <option value="published" @selected(old('status') === 'published')>Published</option>
            </select>
          </div>
        </div>

        {{-- Tags (select existing + create new) --}}
        @php
          $existingTags = $tags ?? collect();
          $oldTagIds = old('tag_ids', []);
        @endphp
        <div class="border border-slate-100 rounded-2xl p-4 bg-slate-50/60">
          <h2 class="text-[13px] font-semibold text-slate-900 mb-2 flex items-center gap-2">
            <i data-lucide="tags" class="h-4 w-4 text-slate-500"></i>
            Tags
          </h2>

          {{-- Existing tags as checkboxes --}}
          <div class="space-y-2">
            <p class="text-[12px] text-slate-500 mb-1">
              Select existing tags that apply to this protocol.
            </p>

            @if($existingTags->count())
              <div class="flex flex-wrap gap-2">
                @foreach ($existingTags as $tag)
                  <label class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-2.5 py-1 text-[11px] text-slate-700 cursor-pointer hover:bg-slate-50">
                    <input
                      type="checkbox"
                      name="tag_ids[]"
                      value="{{ $tag->id }}"
                      class="h-3 w-3 rounded border-slate-300 text-red-600 focus:ring-red-300"
                      @checked(in_array($tag->id, $oldTagIds))
                    >
                    <span>{{ $tag->name }}</span>
                  </label>
                @endforeach
              </div>
            @else
              <p class="text-[12px] text-slate-400 italic">
                No tags defined yet. You can create new tags below.
              </p>
            @endif
          </div>

          {{-- Create new tags --}}
          <div class="mt-4">
            <label for="new_tags" class="block text-[13px] font-medium text-slate-700 mb-1">
              Create new tags
            </label>
            <input
              id="new_tags"
              name="new_tags"
              type="text"
              value="{{ old('new_tags') }}"
              placeholder="e.g., Adult, Code Blue, Simulation"
              class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-red-300"
            >
            <p class="mt-1 text-[11px] text-slate-500">
              Separate multiple tags with commas. New tags can be auto-attached by the controller logic.
            </p>
          </div>
        </div>

        {{-- Description --}}
        <div>
          <label for="description" class="block text-[13px] font-medium text-slate-700 mb-1">Detailed Description</label>
          <textarea id="description" name="description" rows="5"
                    placeholder="Describe the background, rationale, and management procedure."
                    class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-red-300">{{ old('description') }}</textarea>
        </div>

        {{-- Video & PDF --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="video_url" class="block text-[13px] font-medium text-slate-700 mb-1">Reference Video URL</label>
            <input id="video_url" name="video_url" type="url" value="{{ old('video_url') }}"
                   placeholder="https://youtube.com/watch?v=..."
                   class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-red-300">
          </div>

          <div>
            <label for="pdf_path" class="block text-[13px] font-medium text-slate-700 mb-1">Attach PDF (optional)</label>
            <input id="pdf_path" name="pdf_path" type="file"
                   accept=".pdf"
                   class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-red-600 file:px-3 file:py-1.5 file:text-white hover:file:bg-red-700">
          </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
          <a href="{{ route('faculty.emergency.index') }}"
             class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-[13px] font-medium text-slate-700 hover:bg-slate-50">
            Cancel
          </a>
          <button type="submit"
                  class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2 text-[13px] font-medium text-white shadow-sm hover:bg-red-700">
            <i data-lucide="save" class="h-4 w-4"></i>
            Save Protocol
          </button>
        </div>
      </form>

    </div>
  </section>
</main>

@include('partials.admin-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>

</body>
</html>
