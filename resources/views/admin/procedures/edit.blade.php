<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Edit Procedure · NurSync (Admin)</title>

  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <style> body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; } </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Sidebar --}}
  @include('partials.admin-sidebar', ['active' => 'resources'])

  <section class="flex-1">
    <div class="container mx-auto px-8 py-12 space-y-8">

      {{-- Page header --}}
      <header class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
            <i data-lucide="pencil" class="h-4 w-4"></i>
          </span>
          <h1 class="text-[28px] font-extrabold tracking-tight text-slate-900">
            Edit — {{ $procedure->title ?? 'Procedure' }}
          </h1>
        </div>
        <a href="{{ route('admin.procedures.show', $procedure) }}"
           class="rounded-lg border px-3 py-1.5 text-sm hover:bg-slate-50 inline-flex items-center gap-2">
          <i data-lucide="book-open" class="h-4 w-4"></i> Open Guide
        </a>
      </header>

      {{-- Flash banners --}}
      @if (session('ok'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-2">
          {{ session('ok') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="rounded-lg border border-rose-200 bg-rose-50 text-rose-800 px-4 py-2">
          Please fix the errors below and try again.
        </div>
      @endif

      {{-- Form --}}
      <form id="editForm"
            action="{{ route('admin.procedures.update', $procedure) }}"
            method="POST" enctype="multipart/form-data"
            class="space-y-8">
        @csrf
        @method('PUT')

        {{-- Basics --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
          <h2 class="text-lg font-semibold text-slate-900">Basics</h2>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="text-xs font-medium text-slate-600">Title</label>
              <input name="title" value="{{ old('title', $procedure->title) }}" required
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm focus:ring-2 focus:ring-slate-200"/>
              @error('title')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>

            {{-- Clinical Ward / Area --}}
            <div>
              <label class="text-xs font-medium text-slate-600">Clinical Ward / Area</label>
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
              <select name="clinical_wards"
                      class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm" required>
                <option value="">Select area</option>
                @foreach($wards as $w)
                  <option value="{{ $w }}" @selected(old('clinical_wards', $procedure->clinical_wards) === $w)>{{ $w }}</option>
                @endforeach
              </select>
              @error('clinical_wards')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>
          </div>

          <div>
            <label class="text-xs font-medium text-slate-600">Description</label>
            <textarea name="description" rows="3"
                      class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200">{{ old('description', $procedure->description) }}</textarea>
            @error('description')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="text-xs font-medium text-slate-600">Status</label>
              <select name="status" class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm">
                @foreach(['draft','published'] as $st)
                  <option value="{{ $st }}" @selected(old('status', $procedure->status) === $st)>{{ ucfirst($st) }}</option>
                @endforeach
              </select>
              @error('status')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Video URL (YouTube/Vimeo)</label>
              <input name="video_url" value="{{ old('video_url', $procedure->video_url) }}"
                     placeholder="e.g. https://www.youtube.com/watch?v=XXXXXXXXXXX"
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
              <div class="mt-1 text-[11px] text-slate-500">We’ll auto-convert to an embeddable player URL.</div>
              @error('video_url')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="text-xs font-medium text-slate-600">Upload Video (optional)</label>
              <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg"
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
              <div class="mt-1 text-[11px] text-slate-500">MP4, WebM, or Ogg • up to 200&nbsp;MB.</div>
              @if(!empty($procedure->video_path))
                <div class="mt-2 text-xs">
                  Current: <a href="{{ asset($procedure->video_path) }}" class="text-slate-700 underline" target="_blank" rel="noopener">Play current video</a>
                </div>
                <label class="mt-2 inline-flex items-center gap-2 text-xs text-slate-700">
                  <input type="checkbox" name="remove_video" value="1" class="rounded border-slate-300">
                  Remove existing video
                </label>
              @endif
              @error('video_file')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Hazards / Safety Notes</label>
              <textarea name="hazards_text" rows="2"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200">{{ old('hazards_text', $procedure->hazards_text) }}</textarea>
              @error('hazards_text')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="text-xs font-medium text-slate-600">PPE & Equipment (comma-separated)</label>
              <input name="ppe_csv"
                     value="{{ old('ppe_csv', implode(', ', (array) ($procedure->ppe_json ?? []))) }}"
                     placeholder="Gloves, Mask, Gown"
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
              @error('ppe_csv')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Tags (comma-separated)</label>
              <input name="tags_csv"
                     value="{{ old('tags_csv', implode(', ', (array) ($procedure->tags_json ?? []))) }}"
                     placeholder="Safety, Documentation"
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
              @error('tags_csv')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>

        {{-- Steps --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Steps</h2>
            <div class="flex items-center gap-2">
              <button type="button" id="btnAddStep"
                      class="rounded-lg border px-3 py-1.5 text-xs hover:bg-slate-50 inline-flex items-center gap-2">
                <i data-lucide="plus" class="h-4 w-4"></i> Add Step
              </button>
            </div>
          </div>

          <div id="stepsWrap" class="mt-4 space-y-4">
            @php($steps = old('steps', $procedure->steps?->values()->toArray() ?? []))

            @forelse($steps as $i => $step)
              <div class="js-step-row rounded-xl border border-slate-200 p-4" data-index="{{ $i }}">
                <div class="flex items-center justify-between gap-3">
                  <div class="text-sm font-semibold text-slate-700">
                    Step <span class="js-step-number">{{ $step['step_no'] ?? $i + 1 }}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <button type="button" class="js-move-up rounded border px-2 py-1 text-xs hover:bg-slate-50">Up</button>
                    <button type="button" class="js-move-down rounded border px-2 py-1 text-xs hover:bg-slate-50">Down</button>
                    <button type="button" class="js-remove rounded border px-2 py-1 text-xs text-rose-700 hover:bg-rose-50">Remove</button>
                  </div>
                </div>

                <div class="mt-3 grid gap-3 md:grid-cols-2">
                  <div>
                    <label class="text-xs font-medium text-slate-600">Title (optional)</label>
                    <input name="steps[{{ $i }}][title]" value="{{ $step['title'] ?? '' }}"
                           class="mt-1 w-full rounded-lg border border-slate-200 p-2.5 text-sm"/>
                  </div>
                  <div>
                    <label class="text-xs font-medium text-slate-600">Duration (seconds) — optional</label>
                    <input name="steps[{{ $i }}][duration_seconds]" type="number" min="0" step="1"
                           value="{{ $step['duration_seconds'] ?? '' }}"
                           class="mt-1 w-full rounded-lg border border-slate-200 p-2.5 text-sm"/>
                  </div>
                </div>

                <div class="mt-3">
                  <label class="text-xs font-medium text-slate-600">Body / Instruction</label>
                  <textarea name="steps[{{ $i }}][body]" rows="3" required
                            class="mt-1 w-full rounded-lg border border-slate-200 p-3 text-sm focus:ring-2 focus:ring-slate-200">{{ $step['body'] ?? '' }}</textarea>
                  @error("steps.$i.body")<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="mt-3 grid gap-3 md:grid-cols-2">
                  <div>
                    <label class="text-xs font-medium text-slate-600">Rationale (optional)</label>
                    <textarea name="steps[{{ $i }}][rationale]" rows="2"
                              class="mt-1 w-full rounded-lg border border-slate-200 p-3 text-sm">{{ $step['rationale'] ?? '' }}</textarea>
                  </div>
                  <div>
                    <label class="text-xs font-medium text-slate-600">Caution (optional)</label>
                    <textarea name="steps[{{ $i }}][caution]" rows="2"
                              class="mt-1 w-full rounded-lg border border-slate-200 p-3 text-sm">{{ $step['caution'] ?? '' }}</textarea>
                  </div>
                </div>

                {{-- NEW: Step Media --}}
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                  <div>
                    <label class="text-xs font-medium text-slate-600">Step Video URL (optional)</label>
                    <input name="steps[{{ $i }}][video_url]"
                           value="{{ $step['video_url'] ?? '' }}"
                           placeholder="YouTube/Vimeo/Embed URL"
                           class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
                    <div class="mt-1 text-[11px] text-slate-500">We’ll convert supported links to an embeddable player.</div>
                    @error("steps.$i.video_url")<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
                  </div>
                  <div>
                    <label class="text-xs font-medium text-slate-600">Upload Step Video (optional)</label>
                    <input type="file" name="steps[{{ $i }}][video_file]"
                           accept="video/mp4,video/webm,video/ogg"
                           class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
                    <div class="mt-1 text-[11px] text-slate-500">MP4 / WebM / Ogg • up to 200&nbsp;MB.</div>

                    @if(!empty($step['video_path']))
                      <div class="mt-2 text-xs">
                        Current: <a href="{{ asset($step['video_path']) }}" class="text-slate-700 underline" target="_blank" rel="noopener">Play current step video</a>
                      </div>
                      <label class="mt-2 inline-flex items-center gap-2 text-xs text-slate-700">
                        <input type="checkbox" name="steps[{{ $i }}][remove_video]" value="1" class="rounded border-slate-300">
                        Remove existing step video
                      </label>
                    @endif

                    @error("steps.$i.video_file")<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
                  </div>
                </div>

                <input type="hidden" name="steps[{{ $i }}][step_no]" value="{{ $step['step_no'] ?? $i + 1 }}" class="js-step-no">
              </div>
            @empty
              {{-- start with one empty row --}}
              <div class="js-step-row rounded-xl border border-slate-200 p-4" data-index="0">
                <div class="flex items-center justify-between gap-3">
                  <div class="text-sm font-semibold text-slate-700">
                    Step <span class="js-step-number">1</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <button type="button" class="js-move-up rounded border px-2 py-1 text-xs hover:bg-slate-50">Up</button>
                    <button type="button" class="js-move-down rounded border px-2 py-1 text-xs hover:bg-slate-50">Down</button>
                    <button type="button" class="js-remove rounded border px-2 py-1 text-xs text-rose-700 hover:bg-rose-50">Remove</button>
                  </div>
                </div>

                <div class="mt-3 grid gap-3 md:grid-cols-2">
                  <div>
                    <label class="text-xs font-medium text-slate-600">Title (optional)</label>
                    <input name="steps[0][title]" class="mt-1 w-full rounded-lg border border-slate-200 p-2.5 text-sm"/>
                  </div>
                  <div>
                    <label class="text-xs font-medium text-slate-600">Duration (seconds) — optional</label>
                    <input name="steps[0][duration_seconds]" type="number" min="0" step="1"
                           class="mt-1 w-full rounded-lg border border-slate-200 p-2.5 text-sm"/>
                  </div>
                </div>

                <div class="mt-3">
                  <label class="text-xs font-medium text-slate-600">Body / Instruction</label>
                  <textarea name="steps[0][body]" rows="3" required
                            class="mt-1 w-full rounded-lg border border-slate-200 p-3 text-sm focus:ring-2 focus:ring-slate-200"></textarea>
                </div>

                <div class="mt-3 grid gap-3 md:grid-cols-2">
                  <div>
                    <label class="text-xs font-medium text-slate-600">Rationale (optional)</label>
                    <textarea name="steps[0][rationale]" rows="2"
                              class="mt-1 w-full rounded-lg border border-slate-200 p-3 text-sm"></textarea>
                  </div>
                  <div>
                    <label class="text-xs font-medium text-slate-600">Caution (optional)</label>
                    <textarea name="steps[0][caution]" rows="2"
                              class="mt-1 w-full rounded-lg border border-slate-200 p-3 text-sm"></textarea>
                  </div>
                </div>

                {{-- NEW: Step Media (empty template) --}}
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                  <div>
                    <label class="text-xs font-medium text-slate-600">Step Video URL (optional)</label>
                    <input name="steps[0][video_url]"
                           placeholder="YouTube/Vimeo/Embed URL"
                           class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
                    <div class="mt-1 text-[11px] text-slate-500">We’ll convert supported links to an embeddable player.</div>
                  </div>
                  <div>
                    <label class="text-xs font-medium text-slate-600">Upload Step Video (optional)</label>
                    <input type="file" name="steps[0][video_file]"
                           accept="video/mp4,video/webm,video/ogg"
                           class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
                    <div class="mt-1 text-[11px] text-slate-500">MP4 / WebM / Ogg • up to 200&nbsp;MB.</div>
                  </div>
                </div>

                <input type="hidden" name="steps[0][step_no]" value="1" class="js-step-no">
              </div>
            @endforelse
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
        Editing affects the official guide shared to students when published. Ensure accuracy and safety notes.
      </div>

    </div>
  </section>
</main>

@include('partials.admin-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>

<script>
  // --- Unsaved changes guard ---
  const form = document.getElementById('editForm');
  let dirty = false;
  form.addEventListener('input', () => dirty = true);
  window.addEventListener('beforeunload', (e) => { if (dirty) { e.preventDefault(); e.returnValue = ''; }});
  form.querySelectorAll('button[name="action"]').forEach(b => b.addEventListener('click', () => dirty = false));

  // --- Steps repeater helpers ---
  const wrap = document.getElementById('stepsWrap');
  const addBtn = document.getElementById('btnAddStep');

  function renumber() {
    [...wrap.querySelectorAll('.js-step-row')].forEach((row, i) => {
      row.dataset.index = i;
      row.querySelector('.js-step-number').textContent = i + 1;
      row.querySelector('.js-step-no').value = i + 1;
      row.querySelectorAll('input[name], textarea[name]').forEach(inp => {
        inp.name = inp.name.replace(/steps\[\d+]/, `steps[${i}]`);
      });
    });
  }

  function bindRow(row) {
    const up = row.querySelector('.js-move-up');
    const down = row.querySelector('.js-move-down');
    const remove = row.querySelector('.js-remove');

    up?.addEventListener('click', () => { const prev = row.previousElementSibling; if (prev) wrap.insertBefore(row, prev); renumber(); });
    down?.addEventListener('click', () => { const next = row.nextElementSibling; if (next) wrap.insertBefore(next, row); renumber(); });
    remove?.addEventListener('click', () => { if (wrap.children.length > 1) { row.remove(); renumber(); } else alert('At least one step is required.'); });
  }

  function newStepRow(index) {
    const el = document.createElement('div');
    el.className = 'js-step-row rounded-xl border border-slate-200 p-4';
    el.dataset.index = index;
    el.innerHTML = `
      <div class="flex items-center justify-between gap-3">
        <div class="text-sm font-semibold text-slate-700">
          Step <span class="js-step-number">${index + 1}</span>
        </div>
        <div class="flex items-center gap-2">
          <button type="button" class="js-move-up rounded border px-2 py-1 text-xs hover:bg-slate-50">Up</button>
          <button type="button" class="js-move-down rounded border px-2 py-1 text-xs hover:bg-slate-50">Down</button>
          <button type="button" class="js-remove rounded border px-2 py-1 text-xs text-rose-700 hover:bg-rose-50">Remove</button>
        </div>
      </div>

      <div class="mt-3 grid gap-3 md:grid-cols-2">
        <div>
          <label class="text-xs font-medium text-slate-600">Title (optional)</label>
          <input name="steps[${index}][title]" class="mt-1 w-full rounded-lg border border-slate-200 p-2.5 text-sm"/>
        </div>
        <div>
          <label class="text-xs font-medium text-slate-600">Duration (seconds) — optional</label>
          <input name="steps[${index}][duration_seconds]" type="number" min="0" step="1"
                 class="mt-1 w-full rounded-lg border border-slate-200 p-2.5 text-sm"/>
        </div>
      </div>

      <div class="mt-3">
        <label class="text-xs font-medium text-slate-600">Body / Instruction</label>
        <textarea name="steps[${index}][body]" rows="3" required
                  class="mt-1 w-full rounded-lg border border-slate-200 p-3 text-sm focus:ring-2 focus:ring-slate-200"></textarea>
      </div>

      <div class="mt-3 grid gap-3 md:grid-cols-2">
        <div>
          <label class="text-xs font-medium text-slate-600">Rationale (optional)</label>
          <textarea name="steps[${index}][rationale]" rows="2"
                    class="mt-1 w-full rounded-lg border border-slate-200 p-3 text-sm"></textarea>
        </div>
        <div>
          <label class="text-xs font-medium text-slate-600">Caution (optional)</label>
          <textarea name="steps[${index}][caution]" rows="2"
                    class="mt-1 w-full rounded-lg border border-slate-200 p-3 text-sm"></textarea>
        </div>
      </div>

      <!-- NEW: Step Media (empty template row) -->
      <div class="mt-3 grid gap-3 md:grid-cols-2">
        <div>
          <label class="text-xs font-medium text-slate-600">Step Video URL (optional)</label>
          <input name="steps[${index}][video_url]"
                 placeholder="YouTube/Vimeo/Embed URL"
                 class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
          <div class="mt-1 text-[11px] text-slate-500">We’ll convert supported links to an embeddable player.</div>
        </div>
        <div>
          <label class="text-xs font-medium text-slate-600">Upload Step Video (optional)</label>
          <input type="file" name="steps[${index}][video_file]"
                 accept="video/mp4,video/webm,video/ogg"
                 class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
          <div class="mt-1 text-[11px] text-slate-500">MP4 / WebM / Ogg • up to 200&nbsp;MB.</div>
        </div>
      </div>

      <input type="hidden" name="steps[${index}][step_no]" value="${index + 1}" class="js-step-no">
    `;
    bindRow(el);
    return el;
  }

  [...wrap.querySelectorAll('.js-step-row')].forEach(bindRow);

  addBtn?.addEventListener('click', () => {
    const nextIndex = wrap.querySelectorAll('.js-step-row').length;
    const node = newStepRow(nextIndex);
    wrap.appendChild(node);
    renumber();
  });
</script>
</body>
</html>
