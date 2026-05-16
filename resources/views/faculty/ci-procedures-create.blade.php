<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>New Procedure · NurSync – Nurse Assistance (CI)</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }

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
    @include('partials.faculty-sidebar', ['active' => 'procedures'])

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
                Create New Procedure
              </h1>
              <p class="text-[13px] text-slate-500 mt-1">
                Capture how nurses actually perform this procedure in your ward, with real safety notes,
                PPE, and video guidance for your students.
              </p>
            </div>
          </div>
          <a href="{{ route('faculty.procedures.index') }}"
             class="rounded-lg border px-3 py-1.5 text-sm hover:bg-slate-50 inline-flex items-center gap-2">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            Back to Library
          </a>
        </header>

        {{-- Form --}}
        <form action="{{ route('faculty.procedures.store') }}" method="POST" enctype="multipart/form-data"
              class="space-y-8">
          @csrf

          {{-- Basics --}}
          <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">Basic Information</h2>

            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <label class="text-xs font-medium text-slate-600">Title</label>
                <input name="title"
                       value="{{ old('title') }}"
                       required
                       class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm focus:ring-2 focus:ring-slate-200" />
                @error('title')
                  <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
                @enderror
              </div>

              {{-- Clinical Ward/Area --}}
              <div>
                <label class="text-xs font-medium text-slate-600">Clinical Ward / Area</label>
                <select name="clinical_wards"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm">
                  <option value="" @selected(old('clinical_wards') === '')>— Select area —</option>
                  <option value="Community Health (CHN)" @selected(old('clinical_wards') === 'Community Health (CHN)')>
                    Community Health (CHN)</option>
                  <option value="OB Ward" @selected(old('clinical_wards') === 'OB Ward')>OB Ward</option>
                  <option value="Delivery Room (DR)" @selected(old('clinical_wards') === 'Delivery Room (DR)')>
                    Delivery Room (DR)</option>
                  <option value="Nursery" @selected(old('clinical_wards') === 'Nursery')>Nursery</option>
                  <option value="Pediatrics (PEDIA)" @selected(old('clinical_wards') === 'Pediatrics (PEDIA)')>
                    Pediatrics (PEDIA)</option>
                  <option value="Medical-Surgical (MS)" @selected(old('clinical_wards') === 'Medical-Surgical (MS)')>
                    Medical-Surgical (MS)</option>
                  <option value="ICU" @selected(old('clinical_wards') === 'ICU')>ICU</option>
                  <option value="Oncology" @selected(old('clinical_wards') === 'Oncology')>Oncology</option>
                  <option value="Isolation Unit" @selected(old('clinical_wards') === 'Isolation Unit')>Isolation Unit</option>
                  <option value="Endocrine Unit" @selected(old('clinical_wards') === 'Endocrine Unit')>Endocrine Unit</option>
                  <option value="Neurology Unit" @selected(old('clinical_wards') === 'Neurology Unit')>Neurology Unit</option>
                  <option value="Psychiatric (PSYCH)" @selected(old('clinical_wards') === 'Psychiatric (PSYCH)')>
                    Psychiatric (PSYCH)</option>
                  <option value="Emergency Room (ER)" @selected(old('clinical_wards') === 'Emergency Room (ER)')>
                    Emergency Room (ER)</option>
                  <option value="Operating Room (OR)" @selected(old('clinical_wards') === 'Operating Room (OR)')>
                    Operating Room (OR)</option>
                  <option value="Trauma Unit" @selected(old('clinical_wards') === 'Trauma Unit')>Trauma Unit</option>
                  <option value="Disaster Response / Community Field"
                          @selected(old('clinical_wards') === 'Disaster Response / Community Field')>
                    Disaster Response / Community Field
                  </option>
                </select>
                @error('clinical_wards')
                  <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Description</label>
              <textarea name="description" rows="3"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200"
                        placeholder="Brief summary of this procedure...">{{ old('description') }}</textarea>
              @error('description')
                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <label class="text-xs font-medium text-slate-600">PPE & Equipment (comma-separated)</label>
                <input name="ppe_csv"
                       value="{{ old('ppe_csv') }}"
                       class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm" />
                @error('ppe_csv')
                  <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
                @enderror
              </div>
              <div>
                <label class="text-xs font-medium text-slate-600">Tags (comma-separated)</label>
                <input name="tags_csv"
                       value="{{ old('tags_csv') }}"
                       class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm" />
                @error('tags_csv')
                  <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <label class="text-xs font-medium text-slate-600">Video URL (optional)</label>
                <input name="video_url"
                       value="{{ old('video_url') }}"
                       placeholder="https://www.youtube.com/watch?v=… or https://vimeo.com/…"
                       class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm" />
                <div class="mt-1 text-[11px] text-slate-500">
                  You can paste a YouTube/Vimeo link; we’ll convert it to an embeddable player.
                </div>
                @error('video_url')
                  <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
                @enderror
              </div>

              <div>
                <label class="text-xs font-medium text-slate-600">Upload Video (optional)</label>
                <input type="file"
                       name="video_file"
                       accept="video/mp4,video/webm,video/ogg"
                       class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-2.5 text-sm" />
                <div class="mt-1 text-[11px] text-slate-500">
                  MP4 / WebM / Ogg • up to 200&nbsp;MB. If you upload a file, it will be used instead of the URL.
                </div>
                @error('video_file')
                  <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div>
              <label class="text-xs font-medium text-slate-600">Hazards / Safety Notes</label>
              <textarea name="hazards_text" rows="2"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm focus:ring-2 focus:ring-slate-200">{{ old('hazards_text') }}</textarea>
              @error('hazards_text')
                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
              @enderror
            </div>
          </div>

          {{-- Actions --}}
          <div class="flex items-center justify-end gap-3">
            {{-- default to draft; JS flips this to "publish" when Publish Now is clicked --}}
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
          After creation, you can add step-by-step actions, rationales, and further refinements from the
          <strong>Procedures Library</strong> edit page.
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
