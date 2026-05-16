{{-- resources/views/faculty/instructor/board_exam/edit.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Edit Board Exam Question · NurSync (CI)</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }

    /* Card entrance UI animation (same as Competency, Ward Orientation, Assessment Guides) */
    @keyframes slide-in-up {
      from {
        transform: translateY(10px);
        opacity: 0;
      }

      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .animate-card-in {
      animation: slide-in-up .35s ease-out both;
      will-change: transform, opacity;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">

<main class="min-h-screen flex">

  {{-- Sidebar --}}
  @include('partials.instructor-sidebar', ['active' => 'board_exam_bank'])

  {{-- MAIN --}}
  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-8 py-12 space-y-8">

      @php
        /** @var \App\Models\BoardExamQuestion $question */
        $examTitle = old('exam_title', $question->exam_title);
        $currentCategory = old('category', $question->category);

        $areas = [
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

        $difficulties = ['easy', 'moderate', 'difficult'];
        $statuses     = ['draft', 'published', 'archived'];

        $categoryIsCustom = $currentCategory && !in_array($currentCategory, $areas, true);
      @endphp

      {{-- Page Heading --}}
      <header class="flex items-center justify-between animate-card-in">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
            <i data-lucide="graduation-cap" class="h-4 w-4"></i>
          </span>
          <div>
            <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
              Edit Board Exam Question
            </h1>
            <p class="text-[13px] text-slate-500 mt-1">
              Update the NLE-style item, its ward/area tagging, difficulty, and rationale.
            </p>
            @if($examTitle)
              <div class="mt-2 inline-flex items-center rounded-full border border-sky-200 bg-sky-50 px-2.5 py-0.5 text-[11px] text-sky-700">
                <i data-lucide="notebook-text" class="h-3 w-3 mr-1"></i>
                {{ $examTitle }}
              </div>
            @endif
          </div>
        </div>

        {{-- Back to bank --}}
        <div class="hidden sm:flex">
          <a href="{{ route('faculty.instructor.board_exam.index') }}"
             class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3.5 py-2.5
                    text-[13px] font-medium text-slate-700 hover:bg-slate-50">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            Back to Question Bank
          </a>
        </div>
      </header>

      {{-- Validation errors --}}
      @if ($errors->any())
        <div class="animate-card-in rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
          <div class="flex gap-2">
            <i data-lucide="alert-circle" class="h-4 w-4 mt-0.5"></i>
            <div>
              <p class="font-semibold mb-1">Please fix the following:</p>
              <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        </div>
      @endif

      {{-- Form Card --}}
      <form method="POST"
            action="{{ route('faculty.instructor.board_exam.update', $question->id) }}"
            class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-8 animate-card-in">
        @csrf
        @method('PUT')

        {{-- Exam Title (optional edit) --}}
        <div class="space-y-1.5">
          <label class="text-[14px] font-medium text-slate-700">Exam Title</label>
          <input type="text" name="exam_title"
                 value="{{ $examTitle }}"
                 placeholder="e.g., MS Practice Exam – Cardiovascular Focus"
                 class="mt-1 w-full rounded-xl border-slate-300 text-[14px] p-3
                        focus:ring-2 focus:ring-slate-300" />
          <p class="text-[11px] text-slate-500">
            Editing this only changes the title stored on this item. Other questions in the same set will keep their current title.
          </p>
        </div>

        <div class="border-t border-slate-200"></div>

        {{-- Question Stem --}}
        <div>
          <label class="text-[14px] font-medium text-slate-700">Question Stem</label>
          <textarea name="question_text" required rows="4"
                    class="mt-1 w-full rounded-xl border-slate-300 text-[14px] p-3 focus:ring-2 focus:ring-slate-300"
                    placeholder="Enter the full NLE-style question here...">{{ old('question_text', $question->question_text) }}</textarea>
        </div>

        {{-- Choices --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-2">
          <div>
            <label class="text-[14px] font-medium text-slate-700">Choice A</label>
            <textarea name="choice_a" required rows="2"
                      class="mt-1 w-full rounded-xl border-slate-300 text-[14px] p-3 focus:ring-2 focus:ring-slate-300"
                      placeholder="Option A...">{{ old('choice_a', $question->choice_a) }}</textarea>
          </div>

          <div>
            <label class="text-[14px] font-medium text-slate-700">Choice B</label>
            <textarea name="choice_b" required rows="2"
                      class="mt-1 w-full rounded-xl border-slate-300 text-[14px] p-3 focus:ring-2 focus:ring-slate-300"
                      placeholder="Option B...">{{ old('choice_b', $question->choice_b) }}</textarea>
          </div>

          <div>
            <label class="text-[14px] font-medium text-slate-700">Choice C</label>
            <textarea name="choice_c" required rows="2"
                      class="mt-1 w-full rounded-xl border-slate-300 text-[14px] p-3 focus:ring-2 focus:ring-slate-300"
                      placeholder="Option C...">{{ old('choice_c', $question->choice_c) }}</textarea>
          </div>

          <div>
            <label class="text-[14px] font-medium text-slate-700">Choice D</label>
            <textarea name="choice_d" required rows="2"
                      class="mt-1 w-full rounded-xl border-slate-300 text-[14px] p-3 focus:ring-2 focus:ring-slate-300"
                      placeholder="Option D...">{{ old('choice_d', $question->choice_d) }}</textarea>
          </div>
        </div>

        {{-- Correct Answer, Category, Difficulty --}}
        <div class="grid gap-4 sm:grid-cols-3">
          {{-- Correct Answer --}}
          <div>
            <label class="text-[14px] font-medium text-slate-700">Correct Answer</label>
            <select name="correct_answer" required
                    class="mt-1 w-full rounded-xl border-slate-300 text-[14px] py-2.5 px-3
                           focus:ring-2 focus:ring-slate-300">
              <option value="">Select</option>
              @foreach(['A','B','C','D'] as $ans)
                <option value="{{ $ans }}"
                  {{ old('correct_answer', $question->correct_answer) === $ans ? 'selected' : '' }}>
                  {{ $ans }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Category (Ward / Area) --}}
          <div>
            <label class="text-[14px] font-medium text-slate-700">Ward / Area</label>
            <select name="category"
                    class="mt-1 w-full rounded-xl border-slate-300 text-[14px] py-2.5 px-3
                           focus:ring-2 focus:ring-slate-300">
              <option value="">Unspecified</option>
              @if($categoryIsCustom)
                {{-- ensure current custom value still appears --}}
                <option value="{{ $currentCategory }}" selected>
                  {{ $currentCategory }} (custom)
                </option>
              @endif
              @foreach($areas as $area)
                <option value="{{ $area }}"
                  {{ $currentCategory === $area ? 'selected' : '' }}>
                  {{ $area }}
                </option>
              @endforeach
            </select>
            <p class="mt-1 text-[11px] text-slate-500">
              Tag this item to the most relevant ward or area (e.g., MS, OB, ER).
            </p>
          </div>

          {{-- Difficulty --}}
          <div>
            <label class="text-[14px] font-medium text-slate-700">Difficulty</label>
            <select name="difficulty" required
                    class="mt-1 w-full rounded-xl border-slate-300 text-[14px] py-2.5 px-3
                           focus:ring-2 focus:ring-slate-300">
              <option value="">Select</option>
              @foreach($difficulties as $d)
                <option value="{{ $d }}"
                  {{ old('difficulty', $question->difficulty) === $d ? 'selected' : '' }}>
                  {{ ucfirst($d) }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        {{-- Rationale --}}
        <div>
          <label class="text-[14px] font-medium text-slate-700">
            Rationale <span class="text-slate-400 font-normal">(optional)</span>
          </label>
          <textarea name="rationale" rows="4"
                    class="mt-1 w-full rounded-xl border-slate-300 text-[14px] p-3 focus:ring-2 focus:ring-slate-300"
                    placeholder="Explain why the answer is correct (and why the distractors are not).">{{ old('rationale', $question->rationale) }}</textarea>
        </div>

        {{-- Status --}}
        <div>
          <label class="text-[14px] font-medium text-slate-700">Status</label>
          <select name="status"
                  class="mt-1 w-full rounded-xl border-slate-300 text-[14px] py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
            <option value="">Keep current ({{ ucfirst($question->status ?? 'draft') }})</option>
            @foreach($statuses as $st)
              <option value="{{ $st }}"
                {{ old('status') === $st ? 'selected' : '' }}>
                {{ ucfirst($st) }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Actions --}}
        <div class="pt-6 flex flex-wrap items-center gap-3 border-t border-slate-200">
          <a href="{{ route('faculty.instructor.board_exam.index') }}"
             class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5
                    text-[14px] font-medium text-slate-700 hover:bg-slate-50">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            Cancel
          </a>

          <button type="submit"
                  class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5
                         text-[14px] font-semibold text-white shadow-sm hover:bg-emerald-700">
            <i data-lucide="check" class="h-4 w-4"></i>
            Save Changes
          </button>
        </div>

      </form>
    </div>
  </section>

</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>

</body>
</html>
