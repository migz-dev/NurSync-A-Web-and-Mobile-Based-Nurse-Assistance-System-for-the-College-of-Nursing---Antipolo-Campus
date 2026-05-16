{{-- resources/views/admin/emergency/create.blade.php --}}
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Create Emergency Protocol · NurSync (Admin)</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; }</style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  @include('partials.admin-sidebar', ['active' => 'emergency_protocols'])

  <section class="flex-1 min-w-0">
    <div class="max-w-7xl mx-auto px-6 py-8 space-y-6">

      {{-- Header --}}
      <header class="space-y-4">
        <div class="flex items-center justify-between gap-3">
          <a href="{{ route('admin.emergency_protocols.index') }}"
             class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-[12px] font-medium text-slate-700 hover:bg-slate-50">
            <i data-lucide="chevron-left" class="h-4 w-4"></i>
            Back to list
          </a>

          <div class="flex items-center gap-2">
            <button form="ep-create-form"
                    type="submit"
                    class="inline-flex items-center gap-1 rounded-xl bg-emerald-600 px-4 py-2 text-[13px] font-medium text-white shadow-sm hover:bg-emerald-700">
              <i data-lucide="save" class="h-4 w-4"></i>
              Save protocol
            </button>
          </div>
        </div>

        <div class="flex items-start gap-3">
          <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-red-50 text-red-600">
            <i data-lucide="alert-triangle" class="h-5 w-5"></i>
          </span>
          <div>
            <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
              New Emergency Protocol
            </h1>
            <p class="mt-1 text-[13px] text-slate-600 max-w-3xl">
              Add a new emergency procedure. Owner will automatically be set to the current admin.
            </p>
          </div>
        </div>
      </header>

      {{-- Validation errors --}}
      @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
          <p class="font-semibold mb-1">Please fix the following:</p>
          <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @php
        $severities = $severities ?? ['Critical', 'Moderate', 'Mild'];
        $wards      = $wards ?? [];
        $tags       = $tags ?? collect();
        $stepRows   = old('steps');
        if (is_null($stepRows) || !is_array($stepRows) || !count($stepRows)) {
            $stepRows = [
                ['title' => '', 'description' => '', 'expected_action' => ''],
            ];
        }
        $oldTagIds = old('tag_ids', []);
      @endphp

      {{-- Main form --}}
      <form id="ep-create-form"
            method="POST"
            action="{{ route('admin.emergency_protocols.store') }}"
            class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,3fr)]">
        @csrf

        {{-- LEFT: meta + content --}}
        <div class="space-y-4">

          {{-- Protocol details --}}
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3">
            <h2 class="text-[14px] font-semibold text-slate-900 flex items-center gap-2">
              <i data-lucide="file-text" class="h-4 w-4 text-slate-500"></i>
              Protocol details
            </h2>

            <div class="space-y-3">
              {{-- Title --}}
              <div>
                <label for="ep-title" class="block text-[12px] font-medium text-slate-700 mb-1">
                  Title <span class="text-red-500">*</span>
                </label>
                <input id="ep-title" type="text" name="title"
                       value="{{ old('title') }}"
                       placeholder="e.g., Code Blue – Adult Cardiac Arrest"
                       class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300"
                       required>
              </div>

              {{-- Category + Ward --}}
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <label for="ep-category" class="block text-[12px] font-medium text-slate-700 mb-1">
                    Category / type
                  </label>
                  <input id="ep-category" type="text" name="category"
                         value="{{ old('category') }}"
                         placeholder="e.g., Cardiac, Respiratory, Trauma"
                         class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300">
                </div>

                <div>
                  <label for="ep-ward" class="block text-[12px] font-medium text-slate-700 mb-1">
                    Applicable ward / area
                  </label>
                  <select id="ep-ward" name="ward"
                          class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300">
                    <option value="">Select ward</option>
                    @foreach ($wards as $w)
                      <option value="{{ $w }}" @selected(old('ward') === $w)>{{ $w }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              {{-- Severity + Status --}}
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <label for="ep-severity" class="block text-[12px] font-medium text-slate-700 mb-1">
                    Severity <span class="text-red-500">*</span>
                  </label>
                  <select id="ep-severity" name="severity"
                          class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300"
                          required>
                    @foreach ($severities as $sev)
                      <option value="{{ $sev }}" @selected(old('severity', 'Critical') === $sev)>{{ $sev }}</option>
                    @endforeach
                  </select>
                </div>

                <div>
                  <label for="ep-status" class="block text-[12px] font-medium text-slate-700 mb-1">
                    Status <span class="text-red-500">*</span>
                  </label>
                  <select id="ep-status" name="status"
                          class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300"
                          required>
                    <option value="draft" @selected(old('status', 'draft') === 'draft')>Draft</option>
                    <option value="published" @selected(old('status') === 'published')>Published</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          {{-- Summary & overview --}}
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3">
            <h2 class="text-[14px] font-semibold text-slate-900 flex items-center gap-2">
              <i data-lucide="align-left" class="h-4 w-4 text-slate-500"></i>
              Summary & overview
            </h2>

            <div class="space-y-3">
              <div>
                <label for="ep-summary" class="block text-[12px] font-medium text-slate-700 mb-1">
                  Short summary
                </label>
                <textarea id="ep-summary" name="summary" rows="2"
                          class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300"
                          placeholder="Brief overview of the emergency situation or management goals.">{{ old('summary') }}</textarea>
              </div>

              <div>
                <label for="ep-description" class="block text-[12px] font-medium text-slate-700 mb-1">
                  Detailed description / rationale
                </label>
                <textarea id="ep-description" name="description" rows="6"
                          class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300"
                          placeholder="Describe when to use this protocol, key cautions, and institutional notes.">{{ old('description') }}</textarea>
              </div>
            </div>
          </div>

          {{-- Tags & resources --}}
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-4">
            <h2 class="text-[14px] font-semibold text-slate-900 flex items-center gap-2">
              <i data-lucide="tags" class="h-4 w-4 text-slate-500"></i>
              Tags & resources
            </h2>

            {{-- Existing tags --}}
            <div>
              <p class="text-[12px] text-slate-500 mb-1">
                Select existing tags that apply to this protocol.
              </p>

              @if ($tags->count())
                <div class="flex flex-wrap gap-2">
                  @foreach ($tags as $tag)
                    <label
                      class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] text-slate-700 cursor-pointer">
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
            <div>
              <label for="ep-new-tags" class="block text-[12px] font-medium text-slate-700 mb-1">
                Create new tags
              </label>
              <input
                id="ep-new-tags"
                type="text"
                name="new_tags"
                value="{{ old('new_tags') }}"
                placeholder="e.g., Adult, ICU, Code Blue"
                class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300">
              <p class="mt-1 text-[11px] text-slate-500">
                Separate multiple tags with commas. New tags will be created and attached on save.
              </p>
            </div>

            {{-- Links --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-100">
              <div>
                <label for="ep-video" class="block text-[12px] font-medium text-slate-700 mb-1">
                  Video URL (optional)
                </label>
                <input id="ep-video" type="url" name="video_url"
                       value="{{ old('video_url') }}"
                       placeholder="https://youtube.com/watch?v=..."
                       class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300">
              </div>
              <div>
                <label for="ep-pdf" class="block text-[12px] font-medium text-slate-700 mb-1">
                  PDF path (optional)
                </label>
                <input id="ep-pdf" type="text" name="pdf_path"
                       value="{{ old('pdf_path') }}"
                       placeholder="storage/emergency/filename.pdf"
                       class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300">
              </div>
            </div>
          </div>
        </div>

        {{-- RIGHT: steps --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-[14px] font-semibold text-slate-900 flex items-center gap-2">
              <i data-lucide="list-ordered" class="h-4 w-4 text-slate-500"></i>
              Step-by-step algorithm
            </h2>
            <button type="button"
                    id="ep-add-step"
                    class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-[11px] font-medium text-slate-700 hover:bg-slate-50">
              <i data-lucide="plus" class="h-3 w-3"></i>
              Add step
            </button>
          </div>

          <p class="text-[11px] text-slate-500 mb-3">
            Keep each step clear and action-oriented. Order follows this list.
          </p>

          <div id="ep-steps-shell" class="space-y-3">
            @foreach ($stepRows as $idx => $step)
              <div class="ep-step-card rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-3" data-index="{{ $idx }}">
                <div class="flex items-center justify-between mb-2">
                  <div class="flex items-center gap-2">
                    <span
                      class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-red-50 text-[12px] font-semibold text-red-700 border border-red-100 ep-step-number">
                      {{ $idx + 1 }}
                    </span>
                    <span class="text-[12px] font-medium text-slate-800">Step {{ $idx + 1 }}</span>
                  </div>
                  <button type="button"
                          class="ep-remove-step inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-1 text-[10px] text-slate-500 hover:bg-slate-100">
                    <i data-lucide="x" class="h-3 w-3"></i>
                    Remove
                  </button>
                </div>

                <div class="space-y-2">
                  <div>
                    <label class="block text-[11px] font-medium text-slate-700 mb-0.5">Title</label>
                    <input type="text"
                           name="steps[{{ $idx }}][title]"
                           value="{{ $step['title'] ?? '' }}"
                           class="w-full rounded-lg border-slate-200 px-2.5 py-1.5 text-[12px] focus:ring-2 focus:ring-slate-300">
                  </div>
                  <div>
                    <label class="block text-[11px] font-medium text-slate-700 mb-0.5">Description</label>
                    <textarea
                      name="steps[{{ $idx }}][description]"
                      rows="2"
                      class="w-full rounded-lg border-slate-200 px-2.5 py-1.5 text-[12px] focus:ring-2 focus:ring-slate-300">{{ $step['description'] ?? '' }}</textarea>
                  </div>
                  <div>
                    <label class="block text-[11px] font-medium text-slate-700 mb-0.5">Expected action</label>
                    <textarea
                      name="steps[{{ $idx }}][expected_action]"
                      rows="2"
                      class="w-full rounded-lg border-slate-200 px-2.5 py-1.5 text-[12px] focus:ring-2 focus:ring-slate-300">{{ $step['expected_action'] ?? '' }}</textarea>
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          {{-- Template --}}
          <template id="ep-step-template">
            <div class="ep-step-card rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-3" data-index="__INDEX__">
              <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                  <span
                    class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-red-50 text-[12px] font-semibold text-red-700 border border-red-100 ep-step-number">
                    __NUM__
                  </span>
                  <span class="text-[12px] font-medium text-slate-800">Step __NUM__</span>
                </div>
                <button type="button"
                        class="ep-remove-step inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-1 text-[10px] text-slate-500 hover:bg-slate-100">
                  <i data-lucide="x" class="h-3 w-3"></i>
                  Remove
                </button>
              </div>

              <div class="space-y-2">
                <div>
                  <label class="block text-[11px] font-medium text-slate-700 mb-0.5">Title</label>
                  <input type="text"
                         name="steps[__INDEX__][title]"
                         class="w-full rounded-lg border-slate-200 px-2.5 py-1.5 text-[12px] focus:ring-2 focus:ring-slate-300">
                </div>
                <div>
                  <label class="block text-[11px] font-medium text-slate-700 mb-0.5">Description</label>
                  <textarea
                    name="steps[__INDEX__][description]"
                    rows="2"
                    class="w-full rounded-lg border-slate-200 px-2.5 py-1.5 text-[12px] focus:ring-2 focus:ring-slate-300"></textarea>
                </div>
                <div>
                  <label class="block text-[11px] font-medium text-slate-700 mb-0.5">Expected action</label>
                  <textarea
                    name="steps[__INDEX__][expected_action]"
                    rows="2"
                    class="w-full rounded-lg border-slate-200 px-2.5 py-1.5 text-[12px] focus:ring-2 focus:ring-slate-300"></textarea>
                </div>
              </div>
            </div>
          </template>
        </div>
      </form>
    </div>
  </section>
</main>

@include('partials.admin-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const shell  = document.querySelector('#ep-steps-shell');
    const btnAdd = document.querySelector('#ep-add-step');
    const tpl    = document.querySelector('#ep-step-template');

    function renumberSteps() {
      const cards = shell.querySelectorAll('.ep-step-card');
      cards.forEach((card, idx) => {
        card.dataset.index = idx;
        card.querySelectorAll('input, textarea').forEach(el => {
          const name = el.getAttribute('name');
          if (!name) return;
          const newName = name.replace(/steps\[\d+]/, `steps[${idx}]`);
          el.setAttribute('name', newName);
        });
        card.querySelectorAll('.ep-step-number').forEach(badge => {
          badge.textContent = idx + 1;
        });
        const label = card.querySelector('span.text-[12px].font-medium.text-slate-800');
        if (label) label.textContent = `Step ${idx + 1}`;
      });
      if (window.lucide?.createIcons) lucide.createIcons();
    }

    function bindRemoveButtons() {
      shell.querySelectorAll('.ep-remove-step').forEach(btn => {
        if (btn.dataset.bound === '1') return;
        btn.dataset.bound = '1';
        btn.addEventListener('click', () => {
          const card = btn.closest('.ep-step-card');
          if (!card) return;
          card.remove();
          renumberSteps();
        });
      });
    }

    btnAdd?.addEventListener('click', () => {
      if (!tpl || !shell) return;
      const idx  = shell.querySelectorAll('.ep-step-card').length;
      const html = tpl.innerHTML
        .replace(/__INDEX__/g, idx)
        .replace(/__NUM__/g, idx + 1);
      const wrapper = document.createElement('div');
      wrapper.innerHTML = html.trim();
      const card = wrapper.firstElementChild;
      shell.appendChild(card);
      bindRemoveButtons();
      if (window.lucide?.createIcons) lucide.createIcons();
    });

    bindRemoveButtons();
  });
</script>

</body>
</html>
