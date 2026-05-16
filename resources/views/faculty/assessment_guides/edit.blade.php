{{-- resources/views/faculty/assessment_guides/edit.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Edit Assessment Guide · NurSync (CI)</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body { font-family:'Poppins', ui-sans-serif, system-ui, sans-serif; }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">

  {{-- Sidebar --}}
  @include('partials.instructor-sidebar', ['active' => 'assessment_guides'])

  {{-- Main --}}
  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-8 py-12 space-y-8">

      @php
        /** @var \App\Models\AssessmentGuide $guide */
        $status = $guide->status ?? 'draft';
        $updatedAt = $guide->updated_at;
        $tagsInput = old('tags', !empty($tags) ? implode(' ', $tags) : '');
      @endphp

      {{-- Page Header --}}
      <header class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
            <i data-lucide="file-pen-line" class="h-4 w-4"></i>
          </span>

          <div>
            <h1 class="text-[24px] sm:text-[26px] font-extrabold text-slate-900 line-clamp-2">
              Edit Assessment Guide
            </h1>
            <p class="text-[13px] text-slate-500 mt-1">
              Update how you explain real evaluation, documentation, and safe vs unsafe nursing practice.
            </p>

            <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px] text-slate-500">
              <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5">
                <i data-lucide="hash" class="h-3 w-3"></i>
                ID: {{ $guide->id }}
              </span>
              <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5 capitalize">
                <i data-lucide="circle-dot" class="h-3 w-3"></i>
                Status: {{ $status }}
              </span>
              @if($updatedAt)
                <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5">
                  <i data-lucide="clock" class="h-3 w-3"></i>
                  Updated {{ $updatedAt->diffForHumans() }}
                </span>
              @endif
            </div>
          </div>
        </div>

        <div class="flex flex-col gap-2 items-end">
<a href="{{ route('faculty.instructor.assessment.index') }}"
             class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-[13px] font-medium text-slate-700 hover:bg-slate-50">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            Back
          </a>

          {{-- Archive shortcut (optional) --}}
          @if($status !== 'archived')
<form method="POST"
      action="{{ route('faculty.instructor.assessment.destroy', $guide) }}"
      onsubmit="return confirm('Archive this assessment guide? It will be hidden from your main list.');">

              @csrf
              @method('DELETE')
              <button type="submit"
                      class="inline-flex items-center gap-1 rounded-xl border border-rose-200 bg-rose-50 px-3 py-1.5 text-[12px] font-medium text-rose-700 hover:bg-rose-100">
                <i data-lucide="archive" class="h-3 w-3"></i>
                Archive
              </button>
            </form>
          @endif
        </div>
      </header>

      {{-- Flash/Error --}}
      @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">
          {{ session('success') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 text-sm">
          {{ $errors->first() }}
        </div>
      @endif

      {{-- FORM CARD --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

<form method="POST"
      action="{{ route('faculty.instructor.assessment.update', $guide) }}"
      class="space-y-8">

          @csrf
          @method('PUT')

          {{-- Title --}}
          <div>
            <label class="block text-sm font-medium text-slate-800 mb-1">
              Title <span class="text-rose-600">*</span>
            </label>
            <input type="text" name="title" required
                   value="{{ old('title', $guide->title) }}"
                   placeholder="e.g., How Nurses Are Really Evaluated in the Medical-Surgical Ward"
                   class="w-full rounded-xl border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-300" />
          </div>

          {{-- Summary --}}
          <div>
            <label class="block text-sm font-medium text-slate-800 mb-1">Short Summary</label>
            <textarea name="summary" rows="3"
                      placeholder="A brief description of what this guide teaches..."
                      class="w-full rounded-xl border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-300">{{ old('summary', $guide->summary) }}</textarea>
          </div>

          {{-- Rubric Section --}}
          <div>
            <label class="block text-sm font-medium text-slate-800 mb-1">
              Real-World Evaluation Rubrics
            </label>
            <textarea name="content_rubric" rows="6"
                      placeholder="Explain how real nurses are evaluated during actual clinical practice..."
                      class="w-full rounded-xl border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-300">{{ old('content_rubric', $guide->content_rubric) }}</textarea>
          </div>

          {{-- Documentation Section --}}
          <div>
            <label class="block text-sm font-medium text-slate-800 mb-1">
              Documentation (DAR / SOAP / PIE)
            </label>
            <textarea name="content_documentation" rows="6"
                      placeholder="Show correct examples and common mistakes in DAR, SOAP, and PIE charting..."
                      class="w-full rounded-xl border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-300">{{ old('content_documentation', $guide->content_documentation) }}</textarea>
          </div>

          {{-- Tips Section --}}
          <div>
            <label class="block text-sm font-medium text-slate-800 mb-1">
              Tips from Practicing Nurses
            </label>
            <textarea name="content_tips" rows="5"
                      placeholder="Real advice from nurses working in the field..."
                      class="w-full rounded-xl border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-300">{{ old('content_tips', $guide->content_tips) }}</textarea>
          </div>

          {{-- Mistakes Section --}}
          <div>
            <label class="block text-sm font-medium text-slate-800 mb-1">
              Common Mistakes & Unsafe Practices
            </label>
            <textarea name="content_mistakes" rows="5"
                      placeholder="Things student nurses must avoid..."
                      class="w-full rounded-xl border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-300">{{ old('content_mistakes', $guide->content_mistakes) }}</textarea>
          </div>

          {{-- Tags --}}
          <div>
            <label class="block text-sm font-medium text-slate-800 mb-1">Tags</label>
            <input type="text" name="tags"
                   value="{{ $tagsInput }}"
                   placeholder="e.g., evaluation documentation safety tips"
                   class="w-full rounded-xl border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-300" />
            <p class="text-[12px] text-slate-500 mt-1">Separate tags with commas or spaces.</p>
          </div>

          {{-- Status --}}
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="block text-sm font-medium text-slate-800 mb-1">
                Status <span class="text-rose-600">*</span>
              </label>
              <select name="status"
                      class="w-full rounded-xl border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-300">
                <option value="draft" {{ old('status', $status) === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ old('status', $status) === 'published' ? 'selected' : '' }}>Published</option>
                <option value="archived" {{ old('status', $status) === 'archived' ? 'selected' : '' }}>Archived</option>
              </select>
            </div>

            <div class="text-[12px] text-slate-500 flex items-center">
              <p>
                <span class="font-semibold text-slate-700">Draft</span> – still working on it, hidden from students.<br>
                <span class="font-semibold text-slate-700">Published</span> – visible on the student side.<br>
                <span class="font-semibold text-slate-700">Archived</span> – kept for reference, hidden from main lists.
              </p>
            </div>
          </div>

          {{-- Buttons --}}
          <div class="pt-4 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
<a href="{{ route('faculty.instructor.assessment.index') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
              <i data-lucide="arrow-left" class="h-4 w-4"></i>
              Cancel
            </a>

            <div class="flex flex-wrap items-center gap-3">
              <button type="submit"
                      class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-emerald-700">
                <i data-lucide="save" class="h-4 w-4"></i>
                Save Changes
              </button>
            </div>
          </div>

        </form>
      </div>
    </div>
  </section>
</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();
</script>

</body>
</html>
