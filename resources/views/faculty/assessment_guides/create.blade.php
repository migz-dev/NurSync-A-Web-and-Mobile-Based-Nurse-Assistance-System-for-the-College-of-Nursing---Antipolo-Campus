{{-- resources/views/faculty/assessment_guides/create.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>New Assessment Guide · NurSync (CI)</title>

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

      {{-- Page Header --}}
      <header class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
            <i data-lucide="file-plus" class="h-4 w-4"></i>
          </span>

          <div>
            <h1 class="text-[24px] sm:text-[26px] font-extrabold text-slate-900">
              New Assessment Guide
            </h1>
            <p class="text-[13px] text-slate-500 mt-1">
              Create a guide explaining how nurses are evaluated, how documentation is done, and how students can improve.
            </p>
          </div>
        </div>

<a href="{{ route('faculty.instructor.assessment.index') }}"
           class="hidden sm:inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-[13px] font-medium text-slate-700 hover:bg-slate-50">
          <i data-lucide="arrow-left" class="h-4 w-4"></i>
          Back
        </a>
      </header>

      {{-- Flash/Error --}}
      @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 text-sm">
          {{ $errors->first() }}
        </div>
      @endif

      {{-- FORM CARD --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

<form method="POST" action="{{ route('faculty.instructor.assessment.store') }}" class="space-y-8">
          @csrf

          {{-- Title --}}
          <div>
            <label class="block text-sm font-medium text-slate-800 mb-1">Title <span class="text-rose-600">*</span></label>
            <input type="text" name="title" required
                   placeholder="e.g., How Nurses Are Really Evaluated in the Medical-Surgical Ward"
                   class="w-full rounded-xl border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-300" />
          </div>

          {{-- Summary --}}
          <div>
            <label class="block text-sm font-medium text-slate-800 mb-1">Short Summary</label>
            <textarea name="summary" rows="3"
                      placeholder="A brief description of what this guide teaches..."
                      class="w-full rounded-xl border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-300"></textarea>
          </div>

          {{-- Rubric Section --}}
          <div>
            <label class="block text-sm font-medium text-slate-800 mb-1">
              Real-World Evaluation Rubrics
            </label>
            <textarea name="content_rubric" rows="6"
                      placeholder="Explain how real nurses are evaluated during actual clinical practice..."
                      class="w-full rounded-xl border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-300"></textarea>
          </div>

          {{-- Documentation Section --}}
          <div>
            <label class="block text-sm font-medium text-slate-800 mb-1">
              Documentation (DAR / SOAP / PIE)
            </label>
            <textarea name="content_documentation" rows="6"
                      placeholder="Show correct examples and common mistakes in DAR, SOAP, and PIE charting..."
                      class="w-full rounded-xl border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-300"></textarea>
          </div>

          {{-- Tips Section --}}
          <div>
            <label class="block text-sm font-medium text-slate-800 mb-1">
              Tips from Practicing Nurses
            </label>
            <textarea name="content_tips" rows="5"
                      placeholder="Real advice from nurses working in the field..."
                      class="w-full rounded-xl border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-300"></textarea>
          </div>

          {{-- Mistakes Section --}}
          <div>
            <label class="block text-sm font-medium text-slate-800 mb-1">
              Common Mistakes & Unsafe Practices
            </label>
            <textarea name="content_mistakes" rows="5"
                      placeholder="Things student nurses must avoid..."
                      class="w-full rounded-xl border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-300"></textarea>
          </div>

          {{-- Tags --}}
          <div>
            <label class="block text-sm font-medium text-slate-800 mb-1">Tags</label>
            <input type="text" name="tags"
                   placeholder="e.g., evaluation documentation safety tips"
                   class="w-full rounded-xl border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-300" />
            <p class="text-[12px] text-slate-500 mt-1">Separate tags with commas or spaces.</p>
          </div>

          {{-- Status --}}
          <div>
            <label class="block text-sm font-medium text-slate-800 mb-1">Status <span class="text-rose-600">*</span></label>
            <select name="status"
                    class="w-full rounded-xl border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-300">
              <option value="draft">Draft</option>
              <option value="published">Published</option>
            </select>
          </div>

          {{-- Buttons --}}
          <div class="pt-4 flex items-center justify-between">
<a href="{{ route('faculty.instructor.assessment.index') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
              <i data-lucide="arrow-left" class="h-4 w-4"></i>
              Cancel
            </a>

            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-emerald-700">
              <i data-lucide="save" class="h-4 w-4"></i>
              Save Guide
            </button>
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