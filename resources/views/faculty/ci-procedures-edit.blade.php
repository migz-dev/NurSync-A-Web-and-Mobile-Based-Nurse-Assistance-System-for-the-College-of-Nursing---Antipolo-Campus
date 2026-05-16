<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Edit Procedure · NurSync – Nurse Assistance (CI)</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; }

    @keyframes slide-fade-up {
      from { opacity: 0; transform: translateY(8px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    .animate-shell {
      animation: slide-fade-up .35s ease-out both;
      will-change: opacity, transform;
    }

    @keyframes step-pop-in {
      from { opacity: 0; transform: translateY(6px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    .animate-step-in {
      animation: step-pop-in .3s ease-out both;
      will-change: opacity, transform;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Sidebar --}}
  @include('partials.faculty-sidebar', ['active' => 'procedures'])

  <section class="flex-1">
    @php
      $status = old('status', $procedure->status ?? 'draft');
      $statusLabel = ucfirst($status);
      $steps = old('steps', $procedure->steps?->values()->toArray() ?? []);
    @endphp

    <div class="container mx-auto px-8 py-12 space-y-8 animate-shell">

      {{-- Page header (mirrors Skill Mastery edit style) --}}
      <header class="flex items-center justify-between gap-4">
        <div class="flex items-start gap-3">
          <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-sky-50 text-sky-600">
            <i data-lucide="stethoscope" class="h-5 w-5"></i>
          </span>
          <div>
            <h1 class="text-[26px] sm:text-[28px] font-extrabold tracking-tight text-slate-900">
              Edit Clinical Procedure Guide
            </h1>
            <p class="text-[13px] text-slate-500 mt-1">
              Refine this procedure so student nurses see how nurses actually perform it step-by-step in the ward.
            </p>

            <div class="mt-2 inline-flex flex-wrap items-center gap-2 text-[11px]">
              <span class="inline-flex items-center gap-1 rounded-full bg-slate-900 text-white px-2.5 py-0.5">
                <i data-lucide="check-square" class="h-3 w-3"></i>
                {{ $procedure->title ?? 'Untitled procedure' }}
              </span>

              <span class="inline-flex items-center gap-1 rounded-full bg-slate-50 text-slate-700 border border-slate-200 px-2.5 py-0.5">
                <i data-lucide="badge-check" class="h-3 w-3"></i>
                Current status: {{ $statusLabel }}
              </span>

              @if($procedure->clinical_wards)
                <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 text-sky-800 border border-sky-100 px-2.5 py-0.5">
                  <i data-lucide="map-pin" class="h-3 w-3"></i>
                  {{ $procedure->clinical_wards }}
                </span>
              @endif
            </div>
          </div>
        </div>

        <div class="flex flex-col items-end gap-2">
          <a href="{{ route('faculty.procedures.show', $procedure->slug) }}"
             class="rounded-lg border px-3 py-1.5 text-sm hover:bg-slate-50 inline-flex items-center gap-2">
            <i data-lucide="book-open" class="h-4 w-4"></i> Open Guide
          </a>
          <a href="{{ route('faculty.procedures.index') }}"
             class="rounded-lg border px-3 py-1.5 text-sm hover:bg-slate-50 inline-flex items-center gap-2">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Back to Procedures
          </a>
        </div>
      </header>

      {{-- Flash banners --}}
      @if (session('ok'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm">
          {{ session('ok') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
          Please fix the errors below and try again.
        </div>
      @endif

      {{-- Form --}}
      <form id="editForm" action="{{ route('faculty.procedures.update', $procedure->slug) }}" method="POST"
            enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- Basics --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
          <div class="flex items-start justify-between gap-3">
            <div>
              <h2 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                <i data-lucide="file-text" class="h-4 w-4 text-slate-600"></i>
                Basic Information
              </h2>
              <p class="mt-1 text-[12px] text-slate-500">
                Give this procedure a clear title, ward, and overview so students instantly know where and when nurses use it.
              </p>
            </div>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="text-xs font-medium text-slate-600">Procedure Title</label>
              <input name="title" value="{{ old('title', $procedure->title) }}" required
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm focus:ring-2 focus:ring-slate-200"/>
              @error('title')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>

            {{-- Ward --}}
            <div>
              <label class="text-xs font-medium text-slate-600">Clinical Ward / Area</label>
              <select name="clinical_wards"
                      class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm">
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
                <option value="" @selected(old('clinical_wards', $procedure->clinical_wards) === null)>— Select area —</option>
                @foreach ($wards as $ward)
                  <option value="{{ $ward }}" @selected(old('clinical_wards', $procedure->clinical_wards)===$ward)>{{ $ward }}</option>
                @endforeach
              </select>
              <div class="mt-1 text-[11px] text-slate-500">
                Helps students filter procedures per ward (ICU, ER, OR, Community, etc.).
              </div>
              @error('clinical_wards')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>
          </div>

          <div>
            <label class="text-xs font-medium text-slate-600">Short Description</label>
            <textarea name="description" rows="3"
                      class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                      placeholder="High-level overview: when nurses use this procedure, typical patients, and main goals.">{{ old('description', $procedure->description) }}</textarea>
            @error('description')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            {{-- Status cards (same pattern as Skill Mastery) --}}
            <div>
              <label class="text-xs font-medium text-slate-600">Procedure Status</label>
              <div class="mt-1 grid grid-cols-2 gap-2 text-xs">
                @php
                  $currentStatus = $status;
                @endphp

                {{-- Draft --}}
                <label class="flex items-start gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 cursor-pointer">
                  <input type="radio" name="__status_choice" value="draft"
                         class="mt-1 h-3 w-3 text-slate-900"
                         @checked($currentStatus === 'draft')
                         onclick="document.getElementById('jsStatus').value='draft'">
                  <div>
                    <div class="font-medium text-slate-800">Draft</div>
                    <div class="text-[11px] text-slate-500">Visible only to you while you’re still refining it.</div>
                  </div>
                </label>

                {{-- Published --}}
                <label class="flex items-start gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 cursor-pointer">
                  <input type="radio" name="__status_choice" value="published"
                         class="mt-1 h-3 w-3 text-slate-900"
                         @checked($currentStatus === 'published')
                         onclick="document.getElementById('jsStatus').value='published'">
                  <div>
                    <div class="font-medium text-slate-800">Published</div>
                    <div class="text-[11px] text-slate-500">Ready for student nurses (view-only) to learn from.</div>
                  </div>
                </label>
              </div>
              <input type="hidden" name="status" id="jsStatus" value="{{ $currentStatus }}">
              @error('status')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Main Video URL (YouTube/Vimeo)</label>
              <input name="video_url" value="{{ old('video_url', $procedure->video_url) }}"
                     placeholder="e.g. https://www.youtube.com/watch?v=XXXXXXXXXXX"
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
              <div class="mt-1 text-[11px] text-slate-500">
                This becomes the main embedded video students see at the top of the guide.
              </div>
              @error('video_url')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>

        {{-- Teaching Focus & Safety --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
          <div class="flex items-start justify-between gap-3">
            <div>
              <h2 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                <i data-lucide="shield" class="h-4 w-4 text-slate-600"></i>
                Teaching Focus & Safety
              </h2>
              <p class="mt-1 text-[12px] text-slate-500">
                Highlight what you emphasize in real duty: what to watch out for, equipment to prepare, and key reminders.
              </p>
            </div>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="text-xs font-medium text-slate-600">Upload Main Video (optional)</label>
              <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg"
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
              <div class="mt-1 text-[11px] text-slate-500">MP4 / WebM / Ogg • up to 200 MB.</div>
              @if($procedure->video_path)
                <div class="mt-2 text-xs">
                  Current: <a href="{{ asset($procedure->video_path) }}" class="text-slate-700 underline" target="_blank" rel="noopener">Play current video</a>
                </div>
                <label class="mt-2 inline-flex items-center gap-2 text-xs text-slate-700">
                  <input type="checkbox" name="remove_video" value="1" class="rounded border-slate-300">
                  Remove existing main video
                </label>
              @endif
              @error('video_file')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Hazards / Safety Notes</label>
              <textarea name="hazards_text" rows="3"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                        placeholder="Critical red flags, complication risks, and “never ignore” situations for this procedure.">{{ old('hazards_text', $procedure->hazards_text) }}</textarea>
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
              <div class="mt-1 text-[11px] text-slate-500">
                This becomes a quick checklist of what nurses prepare before performing the skill.
              </div>
              @error('ppe_csv')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Tags (comma-separated)</label>
              <input name="tags_csv"
                     value="{{ old('tags_csv', implode(', ', (array) ($procedure->tags_json ?? []))) }}"
                     placeholder="Safety, Documentation"
                     class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
              <div class="mt-1 text-[11px] text-slate-500">
                Helps students discover related procedures (e.g., “IV meds”, “post-op”, “pediatric”).
              </div>
              @error('tags_csv')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>

        {{-- Steps --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
          <div class="flex items-center justify-between gap-3">
            <div>
              <h2 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                <i data-lucide="list-ordered" class="h-4 w-4 text-slate-600"></i>
                Step-by-Step Flow (How nurses perform it)
              </h2>
              <p class="mt-1 text-[12px] text-slate-500 max-w-xl">
                Break the procedure into clear bedside actions. Students will see these steps as a real nurse’s flow, not as a checklist they can edit.
              </p>
            </div>
            <div class="flex items-center gap-2 text-[12px] text-slate-500">
              <span class="hidden sm:inline-block">Total steps:</span>
              <span class="inline-flex h-7 min-w-[2rem] items-center justify-center rounded-full bg-slate-900 text-white text-xs font-semibold">
                {{ count($steps) ?: 1 }}
              </span>
            </div>
          </div>

          <div class="flex items-center justify-between mt-1">
            <div class="text-[11px] text-slate-500">
              Use short, action-focused wording. Rationale explains the “why” behind each step, while caution highlights what can go wrong.
            </div>
            <button type="button" id="btnAddStep"
                    class="rounded-lg border px-3 py-1.5 text-xs hover:bg-slate-50 inline-flex items-center gap-2">
              <i data-lucide="plus" class="h-4 w-4"></i> Add Step
            </button>
          </div>

          <div id="stepsWrap" class="mt-4 space-y-4">
            @forelse($steps as $i => $step)
              <div class="js-step-row rounded-xl border border-slate-200 bg-slate-50 p-4 animate-step-in"
                   data-index="{{ $i }}" style="animation-delay: {{ min($i, 8) * 40 }}ms">
                <div class="flex items-center justify-between gap-3">
                  <div class="flex items-center gap-2">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-900 text-white text-[12px] font-semibold">
                      <span class="js-step-number">{{ $step['step_no'] ?? $i + 1 }}</span>
                    </span>
                    <div class="text-[13px] font-semibold text-slate-800">
                      Step {{ $step['step_no'] ?? $i + 1 }}
                    </div>
                  </div>
                  <div class="flex items-center gap-2">
                    <button type="button" class="js-move-up rounded border px-2 py-1 text-xs hover:bg-slate-50">Up</button>
                    <button type="button" class="js-move-down rounded border px-2 py-1 text-xs hover:bg-slate-50">Down</button>
                    <button type="button" class="js-remove rounded border px-2 py-1 text-xs text-rose-700 hover:bg-rose-50">Remove</button>
                  </div>
                </div>

                <div class="mt-3 grid gap-3 md:grid-cols-2">
                  <div>
                    <label class="text-xs font-medium text-slate-600">Step Title (optional)</label>
                    <input name="steps[{{ $i }}][title]" value="{{ $step['title'] ?? '' }}"
                           class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
                  </div>
                  <div>
                    <label class="text-xs font-medium text-slate-600">Duration (seconds) — optional</label>
                    <input name="steps[{{ $i }}][duration_seconds]" type="number" min="0" step="1"
                           value="{{ $step['duration_seconds'] ?? '' }}"
                           class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
                  </div>
                </div>

                <div class="mt-3">
                  <label class="text-xs font-medium text-slate-600">Instruction (what the nurse does)</label>
                  <textarea name="steps[{{ $i }}][body]" rows="3" required
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                            placeholder="Example: Verify patient identity using two identifiers and cross-check with MAR.">{{ $step['body'] ?? '' }}</textarea>
                  @error("steps.$i.body")<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="mt-3 grid gap-3 md:grid-cols-2">
                  <div>
                    <label class="text-xs font-medium text-slate-600">Rationale (why the nurse does it)</label>
                    <textarea name="steps[{{ $i }}][rationale]" rows="2"
                              class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm"
                              placeholder="Explain the reasoning behind this action to deepen student understanding.">{{ $step['rationale'] ?? '' }}</textarea>
                  </div>
                  <div>
                    <label class="text-xs font-medium text-slate-600">Caution / Safety reminder (optional)</label>
                    <textarea name="steps[{{ $i }}][caution]" rows="2"
                              class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm"
                              placeholder="What can go wrong? What do you always warn students about at this step?">{{ $step['caution'] ?? '' }}</textarea>
                  </div>
                </div>

                {{-- Per-step media --}}
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                  <div>
                    <label class="text-xs font-medium text-slate-600">Step Video URL (optional)</label>
                    <input name="steps[{{ $i }}][video_url]"
                           value="{{ $step['video_url'] ?? '' }}"
                           placeholder="YouTube/Vimeo link"
                           class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
                  </div>
                  <div>
                    <label class="text-xs font-medium text-slate-600">Upload Step Video (optional)</label>
                    <input type="file" name="steps[{{ $i }}][video_file]"
                           accept="video/mp4,video/webm,video/ogg"
                           class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
                    @if(!empty($step['video_path']))
                      <div class="mt-2 text-xs">
                        Current: <a href="{{ asset($step['video_path']) }}" class="text-slate-700 underline" target="_blank" rel="noopener">Play step video</a>
                      </div>
                      <label class="mt-2 inline-flex items-center gap-2 text-xs text-slate-700">
                        <input type="checkbox" name="steps[{{ $i }}][remove_video]" value="1" class="rounded border-slate-300">
                        Remove existing step video
                      </label>
                    @endif
                  </div>
                </div>

                <input type="hidden" name="steps[{{ $i }}][step_no]" value="{{ $step['step_no'] ?? $i + 1 }}" class="js-step-no">
              </div>
            @empty
              {{-- start with one empty row --}}
              <div class="js-step-row rounded-xl border border-slate-200 bg-slate-50 p-4 animate-step-in" data-index="0">
                <div class="flex items-center justify-between gap-3">
                  <div class="flex items-center gap-2">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-900 text-white text-[12px] font-semibold">
                      <span class="js-step-number">1</span>
                    </span>
                    <div class="text-[13px] font-semibold text-slate-800">
                      Step 1
                    </div>
                  </div>
                  <div class="flex items-center gap-2">
                    <button type="button" class="js-move-up rounded border px-2 py-1 text-xs hover:bg-slate-50">Up</button>
                    <button type="button" class="js-move-down rounded border px-2 py-1 text-xs hover:bg-slate-50">Down</button>
                    <button type="button" class="js-remove rounded border px-2 py-1 text-xs text-rose-700 hover:bg-rose-50">Remove</button>
                  </div>
                </div>

                <div class="mt-3 grid gap-3 md:grid-cols-2">
                  <div>
                    <label class="text-xs font-medium text-slate-600">Step Title (optional)</label>
                    <input name="steps[0][title]" class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
                  </div>
                  <div>
                    <label class="text-xs font-medium text-slate-600">Duration (seconds) — optional</label>
                    <input name="steps[0][duration_seconds]" type="number" min="0" step="1"
                           class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
                  </div>
                </div>

                <div class="mt-3">
                  <label class="text-xs font-medium text-slate-600">Instruction (what the nurse does)</label>
                  <textarea name="steps[0][body]" rows="3" required
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                            placeholder="Example: Verify patient identity using two identifiers and cross-check with MAR."></textarea>
                </div>

                <div class="mt-3 grid gap-3 md:grid-cols-2">
                  <div>
                    <label class="text-xs font-medium text-slate-600">Rationale (why the nurse does it)</label>
                    <textarea name="steps[0][rationale]" rows="2"
                              class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm"></textarea>
                  </div>
                  <div>
                    <label class="text-xs font-medium text-slate-600">Caution / Safety reminder (optional)</label>
                    <textarea name="steps[0][caution]" rows="2"
                              class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm"></textarea>
                  </div>
                </div>

                {{-- Per-step media (empty row) --}}
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                  <div>
                    <label class="text-xs font-medium text-slate-600">Step Video URL (optional)</label>
                    <input name="steps[0][video_url]"
                           placeholder="YouTube/Vimeo link"
                           class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
                  </div>
                  <div>
                    <label class="text-xs font-medium text-slate-600">Upload Step Video (optional)</label>
                    <input type="file" name="steps[0][video_file]"
                           accept="video/mp4,video/webm,video/ogg"
                           class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
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
        Editing this guide affects what student nurses see when they review the procedure. Keep steps realistic to how nurses
        actually work in your ward, and highlight the safety points you always emphasize during duty.
      </div>

    </div>
  </section>
</main>

@includeIf('partials.faculty-footer')
@includeWhen(!View::exists('partials.faculty-footer'), 'partials.student-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>

<script>
  // --- Unsaved changes guard ---
  const form = document.getElementById('editForm');
  let dirty = false;
  form.addEventListener('input', () => dirty = true);
  window.addEventListener('beforeunload', (e) => { if (dirty) { e.preventDefault(); e.returnValue = ''; }});
  form.querySelectorAll('button[name="action"]').forEach(b => b.addEventListener('click', () => dirty = false));

  // --- Steps repeater helpers (same logic, design upgraded) ---
  const wrap = document.getElementById('stepsWrap');
  const addBtn = document.getElementById('btnAddStep');

  function renumber() {
    [...wrap.querySelectorAll('.js-step-row')].forEach((row, i) => {
      row.dataset.index = i;
      row.querySelector('.js-step-number').textContent = i + 1;
      row.querySelector('.js-step-no').value = i + 1;

      // Update names for inputs & textareas to the new index (including video fields)
      row.querySelectorAll('input[name], textarea[name]').forEach(inp => {
        inp.name = inp.name.replace(/steps\[\d+]/, `steps[${i}]`);
      });

      // Refresh animation delay like Skill Mastery steps
      row.style.animationDelay = `${Math.min(i, 8) * 40}ms`;
    });
  }

  function bindRow(row) {
    const up = row.querySelector('.js-move-up');
    const down = row.querySelector('.js-move-down');
    const remove = row.querySelector('.js-remove');

    up?.addEventListener('click', () => {
      const prev = row.previousElementSibling;
      if (prev) {
        wrap.insertBefore(row, prev);
        renumber();
      }
    });

    down?.addEventListener('click', () => {
      const next = row.nextElementSibling;
      if (next) {
        wrap.insertBefore(next, row);
        renumber();
      }
    });

    remove?.addEventListener('click', () => {
      if (wrap.children.length > 1) {
        row.remove();
        renumber();
      } else {
        alert('At least one step is required.');
      }
    });
  }

  function newStepRow(index) {
    const el = document.createElement('div');
    el.className = 'js-step-row rounded-xl border border-slate-200 bg-slate-50 p-4 animate-step-in';
    el.dataset.index = index;
    el.style.animationDelay = `${Math.min(index, 8) * 40}ms`;
    el.innerHTML = `
      <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2">
          <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-900 text-white text-[12px] font-semibold">
            <span class="js-step-number">${index + 1}</span>
          </span>
          <div class="text-[13px] font-semibold text-slate-800">
            Step ${index + 1}
          </div>
        </div>
        <div class="flex items-center gap-2">
          <button type="button" class="js-move-up rounded border px-2 py-1 text-xs hover:bg-slate-50">Up</button>
          <button type="button" class="js-move-down rounded border px-2 py-1 text-xs hover:bg-slate-50">Down</button>
          <button type="button" class="js-remove rounded border px-2 py-1 text-xs text-rose-700 hover:bg-rose-50">Remove</button>
        </div>
      </div>

      <div class="mt-3 grid gap-3 md:grid-cols-2">
        <div>
          <label class="text-xs font-medium text-slate-600">Step Title (optional)</label>
          <input name="steps[${index}][title]" class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
        </div>
        <div>
          <label class="text-xs font-medium text-slate-600">Duration (seconds) — optional</label>
          <input name="steps[${index}][duration_seconds]" type="number" min="0" step="1"
                 class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
        </div>
      </div>

      <div class="mt-3">
        <label class="text-xs font-medium text-slate-600">Instruction (what the nurse does)</label>
        <textarea name="steps[${index}][body]" rows="3" required
                  class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                  placeholder="Example: Verify patient identity using two identifiers and cross-check with MAR."></textarea>
      </div>

      <div class="mt-3 grid gap-3 md:grid-cols-2">
        <div>
          <label class="text-xs font-medium text-slate-600">Rationale (why the nurse does it)</label>
          <textarea name="steps[${index}][rationale]" rows="2"
                    class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm"></textarea>
        </div>
        <div>
          <label class="text-xs font-medium text-slate-600">Caution / Safety reminder (optional)</label>
          <textarea name="steps[${index}][caution]" rows="2"
                    class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm"></textarea>
        </div>
      </div>

      <div class="mt-3 grid gap-3 md:grid-cols-2">
        <div>
          <label class="text-xs font-medium text-slate-600">Step Video URL (optional)</label>
          <input name="steps[${index}][video_url]"
                 placeholder="YouTube/Vimeo link"
                 class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
        </div>
        <div>
          <label class="text-xs font-medium text-slate-600">Upload Step Video (optional)</label>
          <input type="file" name="steps[${index}][video_file]"
                 accept="video/mp4,video/webm,video/ogg"
                 class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm"/>
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
