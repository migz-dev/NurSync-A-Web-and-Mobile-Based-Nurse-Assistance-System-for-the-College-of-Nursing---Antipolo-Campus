<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>New Skill Mastery Checklist · NurSync (CI)</title>
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
  </style>
</head>

<body class="min-h-screen bg-slate-50">
  <main class="min-h-screen flex">
    {{-- Sidebar (Instructor mode) --}}
    @include('partials.instructor-sidebar', ['active' => 'skill_mastery'])

    {{-- Main content --}}
    <section class="flex-1">
      <div class="container mx-auto px-8 py-12 space-y-8 animate-shell">

        {{-- Header --}}
        <header class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
              <i data-lucide="plus" class="h-4 w-4"></i>
            </span>
            <div>
              <h1 class="text-[28px] font-extrabold tracking-tight text-slate-900">
                Create New Skill Mastery Checklist
              </h1>
              <p class="text-[13px] text-slate-500 mt-1">
                Break down a real-world nursing skill step-by-step, with safety reminders and teaching points for your students.
              </p>
            </div>
          </div>
          <a href="{{ route('faculty.instructor.skills.index') }}"
             class="rounded-lg border px-3 py-1.5 text-sm hover:bg-slate-50 inline-flex items-center gap-2">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Back to Skill Mastery
          </a>
        </header>

        {{-- Form --}}
        <form action="{{ route('faculty.instructor.skills.store') }}" method="POST" class="space-y-8">
          @csrf

          {{-- Basic Information --}}
          <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">Basic Information</h2>

            <div class="grid gap-4 md:grid-cols-2">
              {{-- Title --}}
              <div>
                <label class="text-xs font-medium text-slate-600">Checklist Title</label>
                <input name="title"
                       value="{{ old('title') }}"
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
                  @endphp
                  @foreach($cats as $cat)
                    <option value="{{ $cat }}" @selected(old('category') === $cat)>{{ $cat }}</option>
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
                  @endphp
                  @foreach($areaOptions as $area)
                    <option value="{{ $area }}" @selected(old('skill_area') === $area)>{{ $area }}</option>
                  @endforeach
                </select>
                <div class="mt-1 text-[11px] text-slate-500">
                  Optional, but helps group skills per ward (e.g., ICU routines, OR skills, ER emergencies).
                </div>
                @error('skill_area')
                  <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
                @enderror
              </div>

              {{-- Status --}}
              <div>
                <label class="text-xs font-medium text-slate-600">Initial Status</label>
                <div class="mt-1 grid grid-cols-2 gap-2">
                  <label class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs cursor-pointer">
                    <input type="radio" name="__status_choice" value="draft"
                           class="h-3 w-3 text-slate-900"
                           checked
                           onclick="document.getElementById('jsStatus').value='draft'">
                    <div>
                      <div class="font-medium text-slate-800">Save as Draft</div>
                      <div class="text-[11px] text-slate-500">Keep it private while you refine the steps.</div>
                    </div>
                  </label>

                  <label class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs cursor-pointer">
                    <input type="radio" name="__status_choice" value="published"
                           class="h-3 w-3 text-slate-900"
                           onclick="document.getElementById('jsStatus').value='published'">
                    <div>
                      <div class="font-medium text-slate-800">Publish</div>
                      <div class="text-[11px] text-slate-500">Make this visible to students once steps are added.</div>
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
                        placeholder="Example: Step-by-step IV insertion as done in our hospital, including safety checks and troubleshooting.">{{ old('summary') }}</textarea>
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
                          placeholder="What do nurses check and prepare before starting this skill? (e.g., patient identity, allergies, consent, equipment, environment)">{{ old('pre_procedure') }}</textarea>
                @error('pre_procedure')
                  <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
                @enderror
              </div>

              {{-- Post-procedure --}}
              <div>
                <label class="text-xs font-medium text-slate-600">After the Skill (Follow-up & Documentation)</label>
                <textarea name="post_procedure" rows="4"
                          class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                          placeholder="What do nurses do after? (monitoring, documentation, patient teaching, equipment disposal, endorsements, etc.)">{{ old('post_procedure') }}</textarea>
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
                        placeholder="List common errors, red flags, and safety reminders nurses actually follow in this skill.">{{ old('safety_notes') }}</textarea>
              <div class="mt-1 text-[11px] text-slate-500">
                Think like a preceptor: what do you always warn students about for this skill?
              </div>
              @error('safety_notes')
                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
              @enderror
            </div>
          </div>

          {{-- Actions --}}
          <div class="flex items-center justify-end gap-3">
            {{-- This hidden field is what the controller actually validates --}}
            <input type="hidden" name="status" id="jsStatus" value="{{ old('status', 'draft') }}">

            <button type="submit"
                    onclick="document.getElementById('jsStatus').value='draft'"
                    class="rounded-lg border px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
              Save as Draft
            </button>

            <button type="submit"
                    onclick="document.getElementById('jsStatus').value='published'"
                    class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:opacity-95">
              Save & Publish
            </button>
          </div>
        </form>

        {{-- Info note --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 text-[13px] text-slate-600">
          After creating this checklist, you can add detailed steps, equipment, and further refinements from the
          <strong>Skill Mastery Checklists</strong> library.
        </div>

      </div>
    </section>
  </main>

  @includeIf('partials.faculty-footer')
  @includeWhen(!View::exists('partials.faculty-footer'), 'partials.student-footer')

  <script src="https://unpkg.com/lucide@latest"></script>
  <script> lucide.createIcons(); </script>
</body>
</html>
