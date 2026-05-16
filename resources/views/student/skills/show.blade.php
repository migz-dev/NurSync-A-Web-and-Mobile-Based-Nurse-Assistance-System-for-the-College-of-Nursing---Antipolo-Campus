<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>{{ $checklist->title }} · Skill Mastery · NurSync</title>

  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">

  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; }

    /* Entrance animation (same as CI) */
    @keyframes slide-fade-up {
      from { opacity: 0; transform: translateY(8px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    .animate-shell {
      animation: slide-fade-up .35s ease-out both;
      will-change: opacity, transform;
    }

    .step-badge {
      @apply h-7 w-7 rounded-full bg-slate-900 text-white text-[12px] flex items-center justify-center font-semibold;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">

  {{-- Sidebar --}}
  @include('partials.sidebar', ['active' => 'skill_checklists'])

  {{-- Main --}}
  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-8 py-12 space-y-8 animate-shell">

      @php
        $status = 'Published';
        $category = $checklist->category ?? 'General Skill';
        $skillArea = $checklist->skill_area ?? null;

        $steps     = $checklist->steps ?? collect();
        $equipment = $checklist->equipment ?? collect();
        $tags      = $checklist->relationLoaded('tags') ? $checklist->tags : collect();

        // Ward chip colors
        $wardChip = function (?string $area) {
          return match ($area) {
            'Community Health (CHN)'         => 'bg-emerald-50 text-emerald-700',
            'OB Ward','Delivery Room (DR)','Nursery' => 'bg-pink-50 text-pink-700',
            'Pediatrics (PEDIA)'             => 'bg-sky-50 text-sky-700',
            'Medical-Surgical (MS)'          => 'bg-slate-50 text-slate-700',
            'ICU'                             => 'bg-rose-50 text-rose-700',
            'Oncology'                        => 'bg-fuchsia-50 text-fuchsia-700',
            'Isolation Unit'                  => 'bg-amber-50 text-amber-700',
            'Endocrine Unit'                  => 'bg-cyan-50 text-cyan-700',
            'Neurology Unit'                  => 'bg-indigo-50 text-indigo-700',
            'Psychiatric (PSYCH)'             => 'bg-violet-50 text-violet-700',
            'Emergency Room (ER)'             => 'bg-orange-50 text-orange-700',
            'Operating Room (OR)'             => 'bg-green-50 text-green-700',
            'Trauma Unit'                     => 'bg-red-50 text-red-700',
            'Disaster Response / Community Field' => 'bg-yellow-50 text-yellow-700',
            default                           => 'bg-slate-50 text-slate-700',
          };
        };
      @endphp

      {{-- HEADER --}}
      <header class="flex justify-between items-start gap-6">
        <div class="flex gap-3 items-start">
          <span class="h-10 w-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <i data-lucide="check-circle-2" class="h-5 w-5"></i>
          </span>

          <div>
            <h1 class="text-[26px] sm:text-[28px] font-extrabold tracking-tight text-slate-900">
              {{ $checklist->title }}
            </h1>

            {{-- Meta chips --}}
            <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px]">
              {{-- Always published for students --}}
              <span class="inline-flex items-center gap-1 rounded-full bg-emerald-600 text-white px-2.5 py-1">
                <i data-lucide="lock" class="h-3 w-3"></i> Published
              </span>

              {{-- Category --}}
              <span class="inline-flex items-center rounded-full bg-slate-50 text-slate-700 border border-slate-200 px-2.5 py-1">
                <i data-lucide="layers" class="h-3 w-3 mr-1"></i>
                {{ $category }}
              </span>

              {{-- Area --}}
              @if($skillArea)
                <span class="inline-flex items-center rounded-full {{ $wardChip($skillArea) }} border border-transparent px-2.5 py-1">
                  <i data-lucide="map-pin" class="h-3 w-3 mr-1"></i>
                  {{ $skillArea }}
                </span>
              @endif

              {{-- Updated --}}
              <span class="inline-flex items-center gap-1 text-slate-500 ml-1">
                <i data-lucide="clock" class="h-3 w-3"></i>
                Updated {{ $checklist->updated_at?->diffForHumans() }}
              </span>
            </div>

            {{-- Summary --}}
            @if($checklist->summary)
              <p class="mt-3 text-[13px] text-slate-600 max-w-3xl">
                {{ $checklist->summary }}
              </p>
            @endif
          </div>
        </div>

        {{-- Back --}}
        <a href="{{ route('student.skills.index') }}"
           class="rounded-lg border px-3 py-1.5 text-sm hover:bg-slate-50 inline-flex items-center gap-2">
          <i data-lucide="arrow-left" class="h-4 w-4"></i> Back
        </a>
      </header>


      {{-- TAGS ROW --}}
      <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 flex flex-wrap items-center gap-3 text-[12px]">
        @if($tags->isNotEmpty())
          <div class="flex flex-wrap items-center gap-2">
            <span class="text-[11px] uppercase tracking-wide text-slate-400 font-medium">Tags:</span>
            @foreach($tags as $t)
              <span class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5">
                <i data-lucide="hash" class="h-3 w-3 mr-1"></i> {{ $t->name }}
              </span>
            @endforeach
          </div>
        @else
          <span class="text-[12px] text-slate-500">
            No tags for this checklist.
          </span>
        @endif
      </div>


      {{-- TWO-COLUMN LAYOUT --}}
      <div class="grid gap-6 lg:grid-cols-[minmax(0,1.15fr)_minmax(0,1.4fr)]">

        {{-- LEFT — Overview & Teaching Notes --}}
        <section class="space-y-4">

          {{-- OVERVIEW --}}
          <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
              <i data-lucide="info" class="h-4 w-4 text-slate-600"></i>
              Overview
            </h2>

            <dl class="mt-3 space-y-2 text-[13px] text-slate-700">
              <div class="flex gap-2">
                <dt class="w-24 text-slate-500">Category</dt>
                <dd class="font-medium text-slate-800">{{ $category }}</dd>
              </div>

              <div class="flex gap-2">
                <dt class="w-24 text-slate-500">Area</dt>
                <dd>{{ $skillArea ?: 'Not specified' }}</dd>
              </div>

              <div class="flex gap-2">
                <dt class="w-24 text-slate-500">Created</dt>
                <dd>{{ $checklist->created_at?->format('M d, Y · h:i A') }}</dd>
              </div>
            </dl>
          </div>


          {{-- TEACHING NOTES --}}
          <div class="rounded-2xl border border-slate-200 bg-white p-5 space-y-5">

            {{-- PRE --}}
            <div>
              <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                <i data-lucide="sparkles" class="h-4 w-4 text-amber-500"></i>
                Before the Skill (Preparation)
              </h3>
              <p class="mt-2 text-[13px] text-slate-700 whitespace-pre-line">
                {{ $checklist->pre_procedure ?: 'No content added.' }}
              </p>
            </div>

            {{-- POST --}}
            <div class="border-t border-slate-100 pt-4">
              <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                <i data-lucide="clipboard-list" class="h-4 w-4 text-slate-600"></i>
                After the Skill (Follow-up & Documentation)
              </h3>
              <p class="mt-2 text-[13px] text-slate-700 whitespace-pre-line">
                {{ $checklist->post_procedure ?: 'No content added.' }}
              </p>
            </div>

            {{-- SAFETY --}}
            <div class="border-t border-slate-100 pt-4">
              <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                <i data-lucide="alert-triangle" class="h-4 w-4 text-rose-500"></i>
                Safety Notes & “What Can Go Wrong”
              </h3>
              <p class="mt-2 text-[13px] text-slate-700 whitespace-pre-line">
                {{ $checklist->safety_notes ?: 'No documented safety notes.' }}
              </p>
            </div>

          </div>


          {{-- EQUIPMENT --}}
          <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
              <i data-lucide="toolbox" class="h-4 w-4 text-slate-600"></i>
              Equipment Needed
            </h3>

            @if($equipment->isEmpty())
              <p class="mt-3 text-[13px] text-slate-600">No equipment listed.</p>
            @else
              <ul class="mt-3 space-y-2 text-[13px] text-slate-700">
                @foreach($equipment as $item)
                  <li class="flex items-start gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 mt-2"></span>
                    <div>
                      <div class="font-medium text-slate-900">{{ $item->item_name }}</div>
                      @if($item->item_details)
                        <div class="text-[12px] text-slate-500">{{ $item->item_details }}</div>
                      @endif
                    </div>
                  </li>
                @endforeach
              </ul>
            @endif
          </div>

        </section>


        {{-- RIGHT — Steps --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-5 space-y-4">
          <div class="flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
              <i data-lucide="list-ordered" class="h-4 w-4 text-slate-600"></i>
              Step-by-Step Skill Flow
            </h2>
            <span class="text-[11px] text-slate-500">(Read-only)</span>
          </div>

          @if($steps->isEmpty())
            <div class="mt-2 rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-[13px] text-slate-500">
              No steps added yet.
            </div>
          @else
            <ol class="space-y-4 mt-2">
              @foreach($steps as $step)
                <li class="flex gap-3 items-start">
                  <div class="mt-1">
                    <div class="step-badge">{{ $step->step_no }}</div>
                  </div>

                  <div class="flex-1 rounded-xl bg-slate-50 border border-slate-200 p-3.5">
                    <p class="text-[13px] font-semibold text-slate-900">
                      {{ $step->action }}
                    </p>

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

        </section>
      </div>


      {{-- FOOTNOTE --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-4 text-[13px] text-slate-600">
        This is a view-only version of the skill mastery checklist.  
        Study how nurses perform this skill in real hospital practice —  
        step-by-step, with rationale and safety reminders.
      </div>

    </div>
  </section>
</main>

@includeIf('partials.student-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>

</body>
</html>