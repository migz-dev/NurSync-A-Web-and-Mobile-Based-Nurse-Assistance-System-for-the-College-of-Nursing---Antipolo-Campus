<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>New Board Exam Set · NurSync (CI)</title>

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

    @php
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
    @endphp

    {{-- MAIN --}}
    <section class="flex-1 min-w-0">
        <div class="container mx-auto px-8 py-12 space-y-8">

            {{-- Page Heading --}}
            <header class="flex items-center justify-between animate-card-in">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <i data-lucide="graduation-cap" class="h-4 w-4"></i>
                    </span>
                    <div>
                        <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
                            New Board Exam Set
                        </h1>
                        <p class="text-[13px] text-slate-500 mt-1">
                            Give this exam a title and add as many NLE-style questions as you need.
                        </p>
                    </div>
                </div>

                {{-- Back to list --}}
                <div class="hidden sm:flex">
                    <a href="{{ route('faculty.instructor.board_exam.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3.5 py-2.5
                              text-[13px] font-medium text-slate-700 hover:bg-slate-50">
                        <i data-lucide="arrow-left" class="h-4 w-4"></i>
                        Back
                    </a>
                </div>
            </header>

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 animate-card-in">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Form Card --}}
            <form method="POST" action="{{ route('faculty.instructor.board_exam.store') }}"
                  class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-8 animate-card-in">
                @csrf

                {{-- Exam Meta --}}
                <div class="space-y-4">
                    <div>
                        <label class="text-[14px] font-medium text-slate-700">Exam Title</label>
                        <input type="text" name="exam_title"
                               value="{{ old('exam_title') }}"
                               placeholder="e.g., MS Practice Exam – Cardiovascular Focus"
                               class="mt-1 w-full rounded-xl border-slate-300 text-[14px] p-3
                                      focus:ring-2 focus:ring-slate-300" required>
                    </div>
                    <p class="text-[12px] text-slate-500">
                        This title will group all questions in this set (e.g., “OB – Postpartum Complications Set 1”).
                    </p>
                </div>

                {{-- QUESTIONS WRAPPER --}}
                <div id="questionsWrapper" class="space-y-6">
                    {{-- One question block template instance (index 0) --}}
                    <div class="question-block rounded-2xl border border-slate-200 bg-slate-50/40 p-4 space-y-4"
                         data-index="0">
                        <div class="flex items-center justify-between">
                            <h2 class="text-[15px] font-semibold text-slate-900">
                                Question <span class="js-question-label">1</span>
                            </h2>
                            <button type="button"
                                    class="js-remove-question inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-2.5 py-1.5 text-[12px] text-slate-600 hover:bg-slate-100"
                                    style="display:none">
                                <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                                Remove
                            </button>
                        </div>

                        {{-- Question Stem --}}
                        <div>
                            <label class="text-[13px] font-medium text-slate-700">Question Stem</label>
                            <textarea name="questions[0][question_text]" required rows="3"
                                      class="mt-1 w-full rounded-xl border-slate-300 text-[14px] p-3 focus:ring-2 focus:ring-slate-300"
                                      placeholder="Enter the full NLE-style question here...">{{ old('questions.0.question_text') }}</textarea>
                        </div>

                        {{-- Choices --}}
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-[13px] font-medium text-slate-700">Choice A</label>
                                <textarea name="questions[0][choice_a]" required rows="2"
                                          class="mt-1 w-full rounded-xl border-slate-300 text-[14px] p-3 focus:ring-2 focus:ring-slate-300"
                                          placeholder="Option A...">{{ old('questions.0.choice_a') }}</textarea>
                            </div>

                            <div>
                                <label class="text-[13px] font-medium text-slate-700">Choice B</label>
                                <textarea name="questions[0][choice_b]" required rows="2"
                                          class="mt-1 w-full rounded-xl border-slate-300 text-[14px] p-3 focus:ring-2 focus:ring-slate-300"
                                          placeholder="Option B...">{{ old('questions.0.choice_b') }}</textarea>
                            </div>

                            <div>
                                <label class="text-[13px] font-medium text-slate-700">Choice C</label>
                                <textarea name="questions[0][choice_c]" required rows="2"
                                          class="mt-1 w-full rounded-xl border-slate-300 text-[14px] p-3 focus:ring-2 focus:ring-slate-300"
                                          placeholder="Option C...">{{ old('questions.0.choice_c') }}</textarea>
                            </div>

                            <div>
                                <label class="text-[13px] font-medium text-slate-700">Choice D</label>
                                <textarea name="questions[0][choice_d]" required rows="2"
                                          class="mt-1 w-full rounded-xl border-slate-300 text-[14px] p-3 focus:ring-2 focus:ring-slate-300"
                                          placeholder="Option D...">{{ old('questions.0.choice_d') }}</textarea>
                            </div>
                        </div>

                        {{-- Correct Answer, Category, Difficulty --}}
                        <div class="grid gap-4 sm:grid-cols-3">
                            {{-- Correct Answer --}}
                            <div>
                                <label class="text-[13px] font-medium text-slate-700">Correct Answer</label>
                                <select name="questions[0][correct_answer]" required
                                        class="mt-1 w-full rounded-xl border-slate-300 text-[14px] py-2.5 px-3
                                               focus:ring-2 focus:ring-slate-300">
                                    <option value="">Select</option>
                                    @foreach(['A','B','C','D'] as $ans)
                                        <option value="{{ $ans }}" {{ old('questions.0.correct_answer') == $ans ? 'selected' : '' }}>
                                            {{ $ans }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Ward / Area --}}
                            <div>
                                <label class="text-[13px] font-medium text-slate-700">Ward / Area</label>
                                <select name="questions[0][category]" required
                                        class="mt-1 w-full rounded-xl border-slate-300 text-[14px] py-2.5 px-3
                                               focus:ring-2 focus:ring-slate-300">
                                    <option value="">Select ward / area</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area }}" {{ old('questions.0.category') == $area ? 'selected' : '' }}>
                                            {{ $area }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Difficulty --}}
                            <div>
                                <label class="text-[13px] font-medium text-slate-700">Difficulty</label>
                                <select name="questions[0][difficulty]" required
                                        class="mt-1 w-full rounded-xl border-slate-300 text-[14px] py-2.5 px-3
                                               focus:ring-2 focus:ring-slate-300">
                                    <option value="">Select</option>
                                    @foreach(['easy','moderate','difficult'] as $d)
                                        <option value="{{ $d }}" {{ old('questions.0.difficulty') == $d ? 'selected' : '' }}>
                                            {{ ucfirst($d) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Rationale --}}
                        <div>
                            <label class="text-[13px] font-medium text-slate-700">
                                Rationale <span class="text-slate-400 font-normal">(optional)</span>
                            </label>
                            <textarea name="questions[0][rationale]" rows="3"
                                      class="mt-1 w-full rounded-xl border-slate-300 text-[14px] p-3 focus:ring-2 focus:ring-slate-300"
                                      placeholder="Explain why the answer is correct (or why others are wrong).">{{ old('questions.0.rationale') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Add Question Button --}}
                <div class="flex justify-between items-center">
                    <p class="text-[12px] text-slate-500">
                        You can add multiple questions under this exam title. Each one will be saved as a separate item.
                    </p>
                    <button type="button" id="btnAddQuestion"
                            class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 px-3.5 py-2
                                   text-[13px] font-medium text-emerald-700 hover:bg-emerald-50">
                        <i data-lucide="plus-circle" class="h-4 w-4"></i>
                        Add another question
                    </button>
                </div>

                {{-- Status for whole set (applies to all questions initially) --}}
                <div class="pt-4 border-t border-slate-200">
                    <label class="text-[14px] font-medium text-slate-700">Default Status for Questions</label>
                    <select name="status"
                            class="mt-1 w-full rounded-xl border-slate-300 text-[14px] py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
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
                        Save Exam & Questions
                    </button>
                </div>

            </form>
        </div>
    </section>

</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();

    (function () {
        const wrapper = document.getElementById('questionsWrapper');
        const addBtn  = document.getElementById('btnAddQuestion');

        if (!wrapper || !addBtn) return;

        let questionIndex = 1; // we already have index 0

        function updateLabels() {
            const blocks = wrapper.querySelectorAll('.question-block');
            blocks.forEach((block, idx) => {
                const label = block.querySelector('.js-question-label');
                if (label) label.textContent = idx + 1;
                const removeBtn = block.querySelector('.js-remove-question');
                if (removeBtn) {
                    // Only show remove if more than 1 block
                    removeBtn.style.display = (blocks.length > 1) ? 'inline-flex' : 'none';
                }
            });
        }

        function createQuestionBlock(idx) {
            const template = wrapper.querySelector('.question-block');
            const clone = template.cloneNode(true);
            clone.dataset.index = idx;

            // Reset values
            const textareas = clone.querySelectorAll('textarea');
            const inputs    = clone.querySelectorAll('input');
            const selects   = clone.querySelectorAll('select');

            textareas.forEach(t => { t.value = ''; });
            inputs.forEach(i => { i.value = ''; });
            selects.forEach(s => { s.value = ''; });

            // Fix name attributes (questions[IDX][field])
            clone.querySelectorAll('textarea, input, select').forEach(el => {
                if (!el.name) return;
                el.name = el.name.replace(/questions\[\d+]/, `questions[${idx}]`);
            });

            // Hook remove button
            const removeBtn = clone.querySelector('.js-remove-question');
            if (removeBtn) {
                removeBtn.addEventListener('click', () => {
                    clone.remove();
                    updateLabels();
                });
            }

            return clone;
        }

        // Attach remove handler to initial block
        const firstBlockRemove = wrapper.querySelector('.question-block .js-remove-question');
        if (firstBlockRemove) {
            firstBlockRemove.addEventListener('click', function () {
                const block = this.closest('.question-block');
                if (!block) return;
                block.remove();
                updateLabels();
            });
        }

        addBtn.addEventListener('click', () => {
            const block = createQuestionBlock(questionIndex++);
            wrapper.appendChild(block);
            updateLabels();
        });

        updateLabels();
    })();
</script>

</body>
</html>
