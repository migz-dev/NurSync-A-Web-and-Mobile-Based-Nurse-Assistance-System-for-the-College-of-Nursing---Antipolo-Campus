<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Edit Skill Mastery Checklist · NurSync (CI)</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
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

    /* Gentle slide-in for individual steps */
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
    {{-- Sidebar (Instructor mode) --}}
    @include('partials.instructor-sidebar', ['active' => 'skill_mastery'])

    {{-- Main content --}}
    <section class="flex-1">
      <div class="container mx-auto px-8 py-12 space-y-8 animate-shell">

        @php
          /** @var \App\Models\SkillMasteryChecklist $checklist */
          $status = $checklist->status ?? 'draft';
          $statusLabel = ucfirst($status);
          $steps = $checklist->steps ?? collect();
          $nextStepNo = ($steps->max('step_no') ?? 0) + 1;
        @endphp

        {{-- Header --}}
        <header class="flex items-center justify-between gap-4">
          <div class="flex items-start gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
              <i data-lucide="edit-3" class="h-5 w-5"></i>
            </span>
            <div>
              <h1 class="text-[26px] sm:text-[28px] font-extrabold tracking-tight text-slate-900">
                Edit Skill Mastery Checklist
              </h1>
              <p class="text-[13px] text-slate-500 mt-1">
                Update how you teach this clinical skill so student nurses see how nurses really do it in practice.
              </p>
              <div class="mt-2 inline-flex items-center gap-2 text-[11px]">
                <span class="inline-flex items-center gap-1 rounded-full bg-slate-900 text-white px-2.5 py-0.5">
                  <i data-lucide="check-square" class="h-3 w-3"></i>
                  {{ $checklist->title }}
                </span>
                <span class="inline-flex items-center gap-1 rounded-full bg-slate-50 text-slate-700 border border-slate-200 px-2.5 py-0.5">
                  <i data-lucide="badge-check" class="h-3 w-3"></i>
                  Current status: {{ $statusLabel }}
                </span>
              </div>
            </div>
          </div>

          <div class="flex flex-col items-end gap-2">
            <a href="{{ route('faculty.instructor.skills.show', $checklist->slug) }}"
               class="rounded-lg border px-3 py-1.5 text-sm hover:bg-slate-50 inline-flex items-center gap-2">
              <i data-lucide="eye" class="h-4 w-4"></i> View Checklist
            </a>
            <a href="{{ route('faculty.instructor.skills.index') }}"
               class="rounded-lg border px-3 py-1.5 text-sm hover:bg-slate-50 inline-flex items-center gap-2">
              <i data-lucide="arrow-left" class="h-4 w-4"></i> Back to Skill Mastery
            </a>
          </div>
        </header>

        {{-- Flash --}}
        @if (session('success'))
          <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
          </div>
        @endif
        @if ($errors->any())
          <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            {{ $errors->first() }}
          </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('faculty.instructor.skills.update', $checklist->slug) }}" method="POST" class="space-y-8">
          @csrf
          @method('PUT')

          {{-- Basic Information --}}
          <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">Basic Information</h2>

            <div class="grid gap-4 md:grid-cols-2">
              {{-- Title --}}
              <div>
                <label class="text-xs font-medium text-slate-600">Checklist Title</label>
                <input name="title"
                       value="{{ old('title', $checklist->title) }}"
                       required
                       class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm focus:ring-2 focus:ring-slate-200" />
                @error('title')
                  <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
                @enderror
              </div>

              {{-- Category --}}
              <div>
                <label class="text-xs font-medium text-slate-600">Skill Category</label>
                <select name="category"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm">
                  <option value="">— Select category —</option>
                  @php
                    /** @var array $categories */
                    $cats = $categories ?? [
                      'Vital Signs',
                      'Medication Administration',
                      'IV Therapy',
                      'Wound Care',
                      'Catheterization',
                      'Tube Feeding',
                      'OR / DR Skills',
                      'ICU Routines',
                      'Emergency / ER Skills',
                      'Assessment & Monitoring',
                      'Other',
                    ];
                    $currentCategory = old('category', $checklist->category);
                  @endphp
                  @foreach($cats as $cat)
                    <option value="{{ $cat }}" @selected($currentCategory === $cat)>{{ $cat }}</option>
                  @endforeach
                </select>
                @error('category')
                  <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
              {{-- Clinical Area / Ward --}}
              <div>
                <label class="text-xs font-medium text-slate-600">Clinical Area / Ward</label>
                <select name="skill_area"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm">
                  <option value="">— Select area (optional) —</option>
                  @php
                    /** @var array $areas */
                    $areaOptions = $areas ?? [
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
                    $currentArea = old('skill_area', $checklist->skill_area);
                  @endphp
                  @foreach($areaOptions as $area)
                    <option value="{{ $area }}" @selected($currentArea === $area)>{{ $area }}</option>
                  @endforeach
                </select>
                <div class="mt-1 text-[11px] text-slate-500">
                  Optional, but helpful when filtering skills per ward (ICU, OR, ER, etc.).
                </div>
                @error('skill_area')
                  <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
                @enderror
              </div>

              {{-- Status --}}
              <div>
                <label class="text-xs font-medium text-slate-600">Checklist Status</label>
                <div class="mt-1 grid grid-cols-3 gap-2 text-xs">
                  @php
                    $currentStatus = old('status', $checklist->status ?? 'draft');
                  @endphp

                  {{-- Draft --}}
                  <label class="flex items-start gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 cursor-pointer">
                    <input type="radio" name="__status_choice" value="draft"
                           class="mt-1 h-3 w-3 text-slate-900"
                           @checked($currentStatus === 'draft')
                           onclick="document.getElementById('jsStatus').value='draft'">
                    <div>
                      <div class="font-medium text-slate-800">Draft</div>
                      <div class="text-[11px] text-slate-500">Visible only to you while still developing.</div>
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
                      <div class="text-[11px] text-slate-500">Ready for students (view-only) to learn from.</div>
                    </div>
                  </label>

                  {{-- Archived --}}
                  <label class="flex items-start gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 cursor-pointer">
                    <input type="radio" name="__status_choice" value="archived"
                           class="mt-1 h-3 w-3 text-slate-900"
                           @checked($currentStatus === 'archived')
                           onclick="document.getElementById('jsStatus').value='archived'">
                    <div>
                      <div class="font-medium text-slate-800">Archived</div>
                      <div class="text-[11px] text-slate-500">Hidden from students, kept for reference.</div>
                    </div>
                  </label>
                </div>
                @error('status')
                  <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
                @enderror
              </div>
            </div>

            {{-- Short Summary --}}
            <div>
              <label class="text-xs font-medium text-slate-600">Short Summary</label>
              <textarea name="summary" rows="3"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                        placeholder="High-level description of the skill and what this checklist focuses on.">{{ old('summary', $checklist->summary) }}</textarea>
              @error('summary')
                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
              @enderror
            </div>
          </div>

          {{-- Teaching Content --}}
          <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">Teaching Content (Before, During, After)</h2>

            <div class="grid gap-4 md:grid-cols-2">
              {{-- Pre-procedure --}}
              <div>
                <label class="text-xs font-medium text-slate-600">Before the Skill (Preparation)</label>
                <textarea name="pre_procedure" rows="4"
                          class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                          placeholder="What nurses check and prepare before starting this skill: patient identity, allergies, consent, equipment, environment, etc.">{{ old('pre_procedure', $checklist->pre_procedure) }}</textarea>
                @error('pre_procedure')
                  <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
                @enderror
              </div>

              {{-- Post-procedure --}}
              <div>
                <label class="text-xs font-medium text-slate-600">After the Skill (Follow-up & Documentation)</label>
                <textarea name="post_procedure" rows="4"
                          class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                          placeholder="What nurses do after: monitoring, documentation, patient teaching, equipment disposal, endorsements, etc.">{{ old('post_procedure', $checklist->post_procedure) }}</textarea>
                @error('post_procedure')
                  <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
                @enderror
              </div>
            </div>

            {{-- Safety Notes --}}
            <div>
              <label class="text-xs font-medium text-slate-600">Safety Notes & “What Can Go Wrong”</label>
              <textarea name="safety_notes" rows="4"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                        placeholder="Common pitfalls, red flags, and real-world safety reminders you always emphasize to students.">{{ old('safety_notes', $checklist->safety_notes) }}</textarea>
              <div class="mt-1 text-[11px] text-slate-500">
                Think like a preceptor: what do you always warn students about for this specific skill?
              </div>
              @error('safety_notes')
                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
              @enderror
            </div>
          </div>

        </form> {{-- main checklist form ends here --}}

        {{-- Step-by-Step Flow (Steps creation + list) --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-5">
          <div class="flex items-center justify-between gap-3">
            <div>
              <h2 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                <i data-lucide="list-ordered" class="h-4 w-4 text-slate-600"></i>
                Step-by-Step Flow (Checklist Steps)
              </h2>
              <p class="mt-1 text-[12px] text-slate-500 max-w-xl">
                Add each action exactly how you would perform and explain it at the bedside. Students will see these as a nurse’s
                checklist they can only observe, not edit.
              </p>
            </div>
            <span class="text-[11px] text-slate-500">
              {{ $steps->count() }} step{{ $steps->count() === 1 ? '' : 's' }} defined
            </span>
          </div>

          {{-- New Step Form --}}
          <form method="POST"
                action="{{ route('faculty.instructor.skills.steps.store', $checklist->slug) }}"
                class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-3">
            @csrf

            <div class="flex flex-wrap items-center justify-between gap-3">
              <div class="flex items-center gap-2 text-[13px]">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-900 text-white text-xs font-semibold">
                  {{ old('step_no', $nextStepNo) }}
                </span>
                <div>
                  <div class="font-medium text-slate-900">Add New Step</div>
                  <div class="text-[11px] text-slate-500">
                    Keep each step short and action-focused. Rationale and safety notes come next.
                  </div>
                </div>
              </div>

              <div class="flex items-center gap-2 text-[11px] text-slate-500">
                <span class="hidden sm:inline">Step number:</span>
                <input type="number" name="step_no" min="1"
                       value="{{ old('step_no', $nextStepNo) }}"
                       class="w-16 rounded-lg border border-slate-300 bg-white px-2 py-1 text-xs" />
              </div>
            </div>

            <div class="grid gap-3 md:grid-cols-2">
              <div>
                <label class="text-[11px] font-medium text-slate-600">What the nurse does (Action)</label>
                <textarea name="action" rows="3"
                          class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-[13px] focus:ring-2 focus:ring-slate-200"
                          placeholder="Example: Verify patient identity using two identifiers and cross-check with MAR.">{{ old('action') }}</textarea>
                @error('action')
                  <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
                @enderror
              </div>

              <div>
                <label class="text-[11px] font-medium text-slate-600">Why the nurse does it (Rationale)</label>
                <textarea name="rationale" rows="3"
                          class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-[13px] focus:ring-2 focus:ring-slate-200"
                          placeholder="Example: Ensures correct patient receives the intended medication, reducing risk of identification errors.">{{ old('rationale') }}</textarea>
                @error('rationale')
                  <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div>
              <label class="text-[11px] font-medium text-slate-600">Safety reminder / What can go wrong (optional)</label>
              <textarea name="safety_point" rows="2"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-[13px] focus:ring-2 focus:ring-slate-200"
                        placeholder="Example: If identifiers do not match, stop and clarify before proceeding.">{{ old('safety_point') }}</textarea>
              @error('safety_point')
                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div class="flex items-center justify-end gap-2">
              <button type="submit"
                      class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-[12px] font-medium text-white hover:opacity-95">
                <i data-lucide="plus" class="h-4 w-4"></i>
                Add Step
              </button>
            </div>
          </form>

          {{-- Existing Steps List --}}
          @if($steps->isEmpty())
            <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-[13px] text-slate-500">
              No steps added yet. Start by adding the first step in the form above.
            </div>
          @else
            <ol class="space-y-3">
              @foreach($steps as $idx => $step)
                <li class="flex gap-3 items-start animate-step-in" style="animation-delay: {{ min($idx, 8) * 40 }}ms">
                  {{-- Step badge --}}
                  <div class="mt-1">
                    <div class="h-7 w-7 rounded-full bg-slate-900 text-white text-[12px] flex items-center justify-center font-semibold">
                      {{ $step->step_no }}
                    </div>
                  </div>

                  {{-- Step content --}}
                  <div class="flex-1 rounded-xl bg-slate-50 border border-slate-200 p-3.5">
                    <div class="flex items-start justify-between gap-2">
                      <p class="text-[13px] font-semibold text-slate-900">
                        {{ $step->action }}
                      </p>
                      {{-- (Optional) future: edit/delete icons --}}
                    </div>

                    @if($step->rationale)
                      <p class="mt-2 text-[12px] text-slate-700">
                        <span class="font-medium text-slate-900">Why:</span>
                        <span class="ml-1">{{ $step->rationale }}</span>
                      </p>
                    @endif

                    @if($step->safety_point)
                      <p class="mt-2 inline-flex items-start gap-2 rounded-lg bg-amber-50 border border-amber-200 px-2.5 py-1.5 text-[12px] text-amber-900">
                        <i data-lucide="shield-alert" class="h-3.5 w-3.5 mt-[1px]"></i>
                        <span>
                          <span class="font-semibold">Safety reminder:</span>
                          {{ $step->safety_point }}
                        </span>
                      </p>
                    @endif
                  </div>
                </li>
              @endforeach
            </ol>
          @endif
        </div>

        {{-- Info note --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 text-[13px] text-slate-600">
          You can return anytime to refine this checklist, update its status, and add more steps.
          Student nurses will only be able to view this flow — they cannot tick or modify anything.
        </div>

      </div>
    </section>
  </main>

  @includeIf('partials.faculty-footer')
  @includeWhen(!View::exists('partials.faculty-footer'), 'partials.student-footer')

  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>
