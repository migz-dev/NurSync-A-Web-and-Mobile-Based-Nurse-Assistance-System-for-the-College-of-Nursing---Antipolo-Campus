<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>New Ward Orientation · NurSync (CI)</title>
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
    {{-- Sidebar --}}
  @include('partials.instructor-sidebar', ['active' => 'ward_orientation'])

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
                Create New Ward Orientation
              </h1>
              <p class="text-[13px] text-slate-500 mt-1">
                Narrate what it’s like working in this ward as a nurse so student nurses feel prepared.
              </p>
            </div>
          </div>
          <a href="{{ route('faculty.instructor.orientation.index') }}"
             class="rounded-lg border px-3 py-1.5 text-sm hover:bg-slate-50 inline-flex items-center gap-2">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Back to Ward Orientation
          </a>
        </header>

        {{-- Form --}}
        <form action="{{ route('faculty.instructor.orientation.store') }}" method="POST" class="space-y-8">
          @csrf

          {{-- Basics --}}
          <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">Basic Information</h2>

            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <label class="text-xs font-medium text-slate-600">Title</label>
                <input name="title" value="{{ old('title') }}" required
                       class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm focus:ring-2 focus:ring-slate-200" />
                @error('title')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
              </div>

              <div>
                <label class="text-xs font-medium text-slate-600">Ward / Area</label>
                <select name="ward_code"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm">
                  <option value="">— Select ward —</option>
                  @php
                    $opts = $wardOptions ?? [
                      'CHN'      => 'Community Health Nursing',
                      'OB'       => 'Obstetrics',
                      'DR'       => 'Delivery Room',
                      'PEDIA'    => 'Pediatrics',
                      'MS'       => 'Medical-Surgical',
                      'ICU'      => 'ICU',
                      'ONCO'     => 'Oncology',
                      'GERIA'    => 'Geriatric',
                      'ORTHO'    => 'Orthopedics',
                      'PSYCH'    => 'Psychiatric',
                      'ER'       => 'Emergency Room',
                      'OR'       => 'Operating Room',
                      'MEDICINE' => 'Medicine Ward',
                      'SURGERY'  => 'Surgery Ward',
                      'DN'       => 'Dialysis / DN',
                      'CDN'      => 'CDN',
                    ];
                  @endphp
                  @foreach($opts as $code => $label)
                    <option value="{{ $code }}" @selected(old('ward_code') === $code)>
                      {{ $label }} ({{ $code }})
                    </option>
                  @endforeach
                </select>
                @error('ward_code')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
              </div>
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Short Summary</label>
              <textarea name="summary" rows="3"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                        placeholder="Brief overview of what this ward orientation will cover...">{{ old('summary') }}</textarea>
              @error('summary')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <label class="text-xs font-medium text-slate-600">Estimated Duration (minutes)</label>
                <input type="number" name="estimated_watch_minutes" min="1" max="240"
                       value="{{ old('estimated_watch_minutes') }}"
                       class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm" />
                <div class="mt-1 text-[11px] text-slate-500">
                  Rough estimate of how long it takes to go through this orientation.
                </div>
                @error('estimated_watch_minutes')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          {{-- Narrated Sections --}}
          <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">Narrated Orientation Content</h2>

            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <label class="text-xs font-medium text-slate-600">Ward Culture & Expectations</label>
                <textarea name="culture_text" rows="4"
                          class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                          placeholder="How do nurses work together here? What attitude and professionalism is expected?">{{ old('culture_text') }}</textarea>
                @error('culture_text')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
              </div>

              <div>
                <label class="text-xs font-medium text-slate-600">Daily Routines</label>
                <textarea name="routines_text" rows="4"
                          class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                          placeholder="Endorsement flow, med rounds, charting times, shift handover...">{{ old('routines_text') }}</textarea>
                @error('routines_text')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
              </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <label class="text-xs font-medium text-slate-600">Typical Patient Cases</label>
                <textarea name="patient_cases_text" rows="4"
                          class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                          placeholder="What kinds of cases/admissions are common in this ward?">{{ old('patient_cases_text') }}</textarea>
                @error('patient_cases_text')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
              </div>

              <div>
                <label class="text-xs font-medium text-slate-600">How Nurses Manage Workload</label>
                <textarea name="workload_text" rows="4"
                          class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                          placeholder="How many patients per nurse? How do they prioritize and stay organized?">{{ old('workload_text') }}</textarea>
                @error('workload_text')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
              </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <label class="text-xs font-medium text-slate-600">Common Emergencies & Responses</label>
                <textarea name="emergencies_text" rows="4"
                          class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                          placeholder="What emergencies commonly occur here? How do nurses typically respond?">{{ old('emergencies_text') }}</textarea>
                @error('emergencies_text')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
              </div>

              <div>
                <label class="text-xs font-medium text-slate-600">Layout, Locations & Who to Approach</label>
                <textarea name="layout_locations_text" rows="4"
                          class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                          placeholder="Where are supplies, crash cart, nurses’ station, charts, etc? Who do students approach for help?">{{ old('layout_locations_text') }}</textarea>
                @error('layout_locations_text')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
              </div>
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Practical Tips for Student Nurses</label>
              <textarea name="tips_text" rows="3"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                        placeholder="Simple dos and don’ts, how to blend into the ward, how to be helpful and safe.">{{ old('tips_text') }}</textarea>
              @error('tips_text')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Actions --}}
          <div class="flex items-center justify-end gap-3">
            <input type="hidden" name="action" id="jsAction" value="draft">

            <button type="submit"
                    class="rounded-lg border px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
              Save Draft
            </button>

            <button type="submit"
                    onclick="document.getElementById('jsAction').value='publish'"
                    class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:opacity-95">
              Publish Now
            </button>
          </div>
        </form>

        {{-- Info note --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 text-[13px] text-slate-600">
          After creation, you can refine this ward orientation anytime from the <strong>Ward Orientation</strong> library.
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
