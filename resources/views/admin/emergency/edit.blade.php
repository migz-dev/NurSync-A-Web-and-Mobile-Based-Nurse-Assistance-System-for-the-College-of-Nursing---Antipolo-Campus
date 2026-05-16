{{-- resources/views/admin/emergency/edit.blade.php --}}
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Admin • Edit Emergency Protocol · NurSync</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style> body { font-family:'Poppins', ui-sans-serif, system-ui, sans-serif; } </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Sidebar (Admin) --}}
  @include('partials.admin-sidebar', ['active' => 'emergency_protocols'])

  {{-- Main content --}}
  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-8 py-12 space-y-6">

      {{-- Header --}}
      <header class="space-y-4">
        <div class="flex items-center justify-between gap-3">
          <button type="button"
                  onclick="window.location.href='{{ route('admin.emergency_protocols.show', $protocol->id) }}'"
                  class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-[12px] font-medium text-slate-700 hover:bg-slate-50">
            <i data-lucide="chevron-left" class="h-4 w-4"></i>
            Back to protocol
          </button>

          <div class="flex items-center gap-2">
            <button form="ep-edit-form"
                    type="submit"
                    class="inline-flex items-center gap-1 rounded-xl bg-emerald-600 px-4 py-2 text-[13px] font-medium text-white shadow-sm hover:bg-emerald-700">
              <i data-lucide="save" class="h-4 w-4"></i>
              Save changes
            </button>
          </div>
        </div>

        @php
          $sev = $protocol->severity ?? 'Critical';
          $sevBg = $sevText = '';
          if ($sev === 'Critical') {
              $sevBg = 'bg-red-50';    $sevText = 'text-red-700';
          } elseif ($sev === 'Moderate') {
              $sevBg = 'bg-amber-50';  $sevText = 'text-amber-700';
          } else {
              $sevBg = 'bg-sky-50';    $sevText = 'text-sky-700';
          }

          $status = $protocol->status ?? 'draft';
          $statusClasses = match ($status) {
              'published' => 'bg-emerald-50 text-emerald-700',
              'archived'  => 'bg-slate-100 text-slate-600',
              default     => 'bg-slate-50 text-slate-700',
          };
        @endphp

        <div class="flex items-start gap-3">
          <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-red-50 text-red-600">
            <i data-lucide="alert-triangle" class="h-5 w-5"></i>
          </span>
          <div class="flex-1">
            <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
              Edit Emergency Protocol
            </h1>
            <p class="mt-1 text-[13px] text-slate-600 max-w-3xl">
              Update the title, ward, severity, and step-by-step actions for this protocol.
            </p>

            <div class="mt-3 flex flex-wrap items-center gap-2">
              <span class="inline-flex items-center rounded-full {{ $sevBg }} px-2 py-0.5 text-[11px] font-medium {{ $sevText }}">
                <i data-lucide="activity" class="h-3 w-3 mr-1"></i>
                {{ $protocol->severity ?? 'Critical' }}
              </span>

              <span class="inline-flex items-center rounded-full {{ $statusClasses }} px-2 py-0.5 text-[11px] font-medium">
                {{ ucfirst($status) }}
              </span>

              @if ($protocol->ward)
                <span class="inline-flex items-center rounded-full bg-sky-50 border border-sky-100 px-2 py-0.5 text-[11px] text-sky-700">
                  <i data-lucide="stethoscope" class="h-3 w-3 mr-1"></i>
                  {{ $protocol->ward }}
                </span>
              @endif

              <span class="inline-flex items-center rounded-full bg-slate-50 px-2 py-0.5 text-[11px] text-slate-700">
                <i data-lucide="eye" class="h-3 w-3 mr-1"></i>
                {{ $protocol->view_count }} views
              </span>
            </div>
          </div>

          <div class="text-right text-[11px] text-slate-500 min-w-[160px]">
            @if($protocol->faculty)
              <div class="mb-2">
                <span class="font-medium text-slate-700">Owner:</span>
                <span>{{ $protocol->faculty->full_name ?? $protocol->faculty->name ?? '—' }}</span>
              </div>
            @endif
            <div>Created: {{ $protocol->created_at?->format('M d, Y H:i') ?? '—' }}</div>
            <div>Updated: {{ $protocol->updated_at?->format('M d, Y H:i') ?? '—' }}</div>
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
        // Prepare steps for editing (old input takes priority)
        $stepRows = old('steps');
        if (is_null($stepRows)) {
            $stepRows = $protocol->steps->sortBy('step_no')->map(function($s) {
                return [
                    'title'           => $s->title,
                    'description'     => $s->description,
                    'expected_action' => $s->expected_action,
                ];
            })->values()->toArray();
        }
        if (empty($stepRows)) {
            $stepRows = [
                ['title' => '', 'description' => '', 'expected_action' => ''],
            ];
        }

        $selectedTagIds = collect(old('tag_ids', $protocol->tags->pluck('id')->all()))
            ->map(fn($v) => (int) $v)
            ->all();
      @endphp

      {{-- Main form --}}
      <form id="ep-edit-form"
            method="POST"
            action="{{ route('admin.emergency_protocols.update', $protocol->id) }}"
            class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,3fr)]">
        @csrf
        @method('PUT')

        {{-- LEFT: Meta & content --}}
        <div class="space-y-4">

          {{-- Meta card --}}
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
                       value="{{ old('title', $protocol->title) }}"
                       class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300"
                       required>
              </div>

              {{-- Category + Ward --}}
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <label for="ep-category" class="block text-[12px] font-medium text-slate-700 mb-1">
                    Category
                  </label>
                  <input id="ep-category" type="text" name="category"
                         value="{{ old('category', $protocol->category) }}"
                         placeholder="e.g., Cardiac / Respiratory"
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
                      <option value="{{ $w }}" @selected(old('ward', $protocol->ward) === $w)>
                        {{ $w }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                {{-- Severity --}}
                <div>
                  <label for="ep-severity" class="block text-[12px] font-medium text-slate-700 mb-1">
                    Severity <span class="text-red-500">*</span>
                  </label>
                  <select id="ep-severity" name="severity"
                          class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300"
                          required>
                    @foreach ($severities as $sev)
                      <option value="{{ $sev }}" @selected(old('severity', $protocol->severity) === $sev)>
                        {{ $sev }}
                      </option>
                    @endforeach
                  </select>
                </div>

                {{-- Status --}}
                <div>
                  <label for="ep-status" class="block text-[12px] font-medium text-slate-700 mb-1">
                    Status <span class="text-red-500">*</span>
                  </label>
                  <select id="ep-status" name="status"
                          class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300"
                          required>
                    <option value="draft" @selected(old('status', $protocol->status) === 'draft')>Draft</option>
                    <option value="published" @selected(old('status', $protocol->status) === 'published')>Published</option>
                    <option value="archived" @selected(old('status', $protocol->status) === 'archived')>Archived</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          {{-- Summary / Description --}}
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
                          class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300">{{ old('summary', $protocol->summary) }}</textarea>
              </div>

              <div>
                <label for="ep-description" class="block text-[12px] font-medium text-slate-700 mb-1">
                  Detailed description / rationale
                </label>
                <textarea id="ep-description" name="description" rows="6"
                          class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300"
                          placeholder="Describe when to use this protocol, key cautions, and institutional notes.">{{ old('description', $protocol->description) }}</textarea>
              </div>
            </div>
          </div>

          {{-- Tags & resources --}}
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3">
            <h2 class="text-[14px] font-semibold text-slate-900 flex items-center gap-2">
              <i data-lucide="tag" class="h-4 w-4 text-slate-500"></i>
              Tags & resources
            </h2>

            <div class="space-y-3">
              {{-- Tags --}}
              <div>
                <p class="block text-[12px] font-medium text-slate-700 mb-1">
                  Tags
                </p>
                @if ($tags->isEmpty())
                  <p class="text-[12px] text-slate-500">No tags defined yet.</p>
                @else
                  <div class="flex flex-wrap gap-2">
                    @foreach ($tags as $tag)
                      <label class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] text-slate-700 cursor-pointer">
                        <input
                          type="checkbox"
                          name="tag_ids[]"
                          value="{{ $tag->id }}"
                          class="h-3 w-3 rounded border-slate-300 text-emerald-600 focus:ring-0"
                          @checked(in_array($tag->id, $selectedTagIds))
                        >
                        <span>{{ $tag->name }}</span>
                      </label>
                    @endforeach
                  </div>
                @endif
              </div>

              {{-- Links --}}
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <label for="ep-video" class="block text-[12px] font-medium text-slate-700 mb-1">
                    Video URL (optional)
                  </label>
                  <input id="ep-video" type="url" name="video_url"
                         value="{{ old('video_url', $protocol->video_url) }}"
                         placeholder="https://..."
                         class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300">
                </div>
                <div>
                  <label for="ep-pdf" class="block text-[12px] font-medium text-slate-700 mb-1">
                    PDF path (optional)
                  </label>
                  <input id="ep-pdf" type="text" name="pdf_path"
                         value="{{ old('pdf_path', $protocol->pdf_path) }}"
                         placeholder="storage/emergency/filename.pdf"
                         class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300">
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- RIGHT: Steps --}}
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
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-red-50 text-[12px] font-semibold text-red-700 border border-red-100 ep-step-number">
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
                    <label class="block text-[11px] font-medium text-slate-700 mb-0.5">
                      Title
                    </label>
                    <input type="text"
                           name="steps[{{ $idx }}][title]"
                           value="{{ $step['title'] ?? '' }}"
                           class="w-full rounded-lg border-slate-200 px-2.5 py-1.5 text-[12px] focus:ring-2 focus:ring-slate-300">
                  </div>
                  <div>
                    <label class="block text-[11px] font-medium text-slate-700 mb-0.5">
                      Description
                    </label>
                    <textarea
                      name="steps[{{ $idx }}][description]"
                      rows="2"
                      class="w-full rounded-lg border-slate-200 px-2.5 py-1.5 text-[12px] focus:ring-2 focus:ring-slate-300">{{ $step['description'] ?? '' }}</textarea>
                  </div>
                  <div>
                    <label class="block text-[11px] font-medium text-slate-700 mb-0.5">
                      Expected action
                    </label>
                    <textarea
                      name="steps[{{ $idx }}][expected_action]"
                      rows="2"
                      class="w-full rounded-lg border-slate-200 px-2.5 py-1.5 text-[12px] focus:ring-2 focus:ring-slate-300">{{ $step['expected_action'] ?? '' }}</textarea>
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          {{-- Hidden template for new steps --}}
          <template id="ep-step-template">
            <div class="ep-step-card rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-3" data-index="__INDEX__">
              <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                  <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-red-50 text-[12px] font-semibold text-red-700 border border-red-100 ep-step-number">
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
                  <label class="block text-[11px] font-medium text-slate-700 mb-0.5">
                    Title
                  </label>
                  <input type="text"
                         name="steps[__INDEX__][title]"
                         class="w-full rounded-lg border-slate-200 px-2.5 py-1.5 text-[12px] focus:ring-2 focus:ring-slate-300">
                </div>
                <div>
                  <label class="block text-[11px] font-medium text-slate-700 mb-0.5">
                    Description
                  </label>
                  <textarea
                    name="steps[__INDEX__][description]"
                    rows="2"
                    class="w-full rounded-lg border-slate-200 px-2.5 py-1.5 text-[12px] focus:ring-2 focus:ring-slate-300"></textarea>
                </div>
                <div>
                  <label class="block text-[11px] font-medium text-slate-700 mb-0.5">
                    Expected action
                  </label>
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
<script>
  lucide.createIcons();

  document.addEventListener('DOMContentLoaded', () => {
    const shell   = document.querySelector('#ep-steps-shell');
    const btnAdd  = document.querySelector('#ep-add-step');
    const tpl     = document.querySelector('#ep-step-template');

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
        const numBadges = card.querySelectorAll('.ep-step-number');
        numBadges.forEach(b => b.textContent = idx + 1);
        const label = card.querySelector('span.text-[12px].font-medium');
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
      const idx = shell.querySelectorAll('.ep-step-card').length;
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
