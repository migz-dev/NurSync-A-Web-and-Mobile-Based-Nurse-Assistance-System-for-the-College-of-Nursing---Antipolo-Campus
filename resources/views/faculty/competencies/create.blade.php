{{-- resources/views/faculty/competencies/create.blade.php --}}
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>New Competency · NurSync (CI)</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
        }

        /* Smooth card entrance (same as index / assessment guides) */
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
        {{-- Sidebar – CI Instructor Mode --}}
        @include('partials.instructor-sidebar', ['active' => 'competency'])

        {{-- Main content --}}
        <section class="flex-1 min-w-0">
            <div class="container mx-auto px-8 py-12 space-y-6">

                {{-- Page header --}}
                <header class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <button type="button"
                            onclick="window.location.href='{{ route('faculty.instructor.competencies.index') }}'"
                            class="inline-flex items-center justify-center h-9 w-9 rounded-full border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
                            <i data-lucide="arrow-left" class="h-4 w-4"></i>
                        </button>
                        <div>
                            <h1 class="text-[22px] sm:text-[24px] font-extrabold tracking-tight text-slate-900">
                                New Competency Requirement
                            </h1>
                            <p class="text-[13px] text-slate-500 mt-1">
                                Define a real-life competency nurses must master before graduation, including why it
                                matters in practice.
                            </p>
                        </div>
                    </div>

                    {{-- Header actions: Back only --}}
                    <div class="hidden sm:flex items-center gap-3">
                        <a href="{{ route('faculty.instructor.competencies.index') }}"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-[13px] font-medium text-slate-700 hover:bg-slate-50">
                            <i data-lucide="list-checks" class="h-4 w-4"></i>
                            <span>Back to list</span>
                        </a>
                    </div>
                </header>

                {{-- Error banner --}}
                @if ($errors->any())
                    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <div class="flex items-start gap-2">
                            <i data-lucide="alert-circle" class="h-4 w-4 mt-0.5"></i>
                            <div>
                                <p class="font-semibold">Please review the fields below.</p>
                                <p class="mt-1">{{ $errors->first() }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @php
                    /** @var \Illuminate\Support\Collection|\App\Models\CompetencyCategory[] $categories */
                    $categories = $categories ?? collect();

                    // For subtle hint chips if you want to use rotation names somewhere (optional)
                    $rotations = $rotations ?? [];
                @endphp

                {{-- Main form card --}}
                <div
                    class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 md:p-7 lg:p-8 opacity-0 animate-card-in">
                    <form method="POST" action="{{ route('faculty.instructor.competencies.store') }}" class="space-y-6">
                        @csrf

                        {{-- Basic info --}}
                        <div class="space-y-4">
                            <div
                                class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-slate-500">
                                <i data-lucide="info" class="h-4 w-4"></i>
                                <span>Basic competency details</span>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                {{-- Title --}}
                                <div class="md:col-span-2">
                                    <label for="title" class="block text-sm font-medium text-slate-800">
                                        Competency title <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" id="title" name="title" value="{{ old('title') }}"
                                        placeholder="e.g., Safe IV therapy administration"
                                        class="mt-1 block w-full rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400">
                                </div>

                                {{-- Category --}}
                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-slate-800">
                                        Category <span class="text-rose-500">*</span>
                                    </label>
                                    <select id="category_id" name="category_id"
                                        class="mt-1 block w-full rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400">
                                        <option value="">Select category</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-[11px] text-slate-500">
                                        Examples: Core Nursing Skills, RLE Case Requirements, Specialty Skills.
                                    </p>

                                    {{-- Inline create-category mini form --}}
                                    <div class="mt-3 rounded-xl border border-dashed border-slate-200 bg-slate-50 px-3 py-3">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-[11px] text-slate-600">
                                                Need a new category? Create it here and it will be added to the list.
                                            </p>
                                        </div>
                                        <div class="mt-2 flex flex-col sm:flex-row gap-2">
                                            <input type="text"
                                                   id="newCategoryTitle"
                                                   placeholder="e.g., Safety & High-Risk Skills"
                                                   class="flex-1 rounded-xl border-slate-200 bg-white text-sm py-2 px-3 focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400">
                                            <button type="button"
                                                    id="btnCreateCategory"
                                                    data-url="{{ route('faculty.instructor.competencies.categories.store') }}"
                                                    class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-3.5 py-2 text-[12px] font-medium text-white hover:bg-emerald-700 disabled:opacity-60 disabled:cursor-not-allowed">
                                                <i data-lucide="plus" class="h-3 w-3 mr-1.5"></i>
                                                Add category
                                            </button>
                                        </div>
                                        <p id="createCategoryFeedback" class="mt-1 text-[11px] text-slate-500"></p>
                                    </div>
                                </div>

                                {{-- Status --}}
                                <div>
                                    <label for="status" class="block text-sm font-medium text-slate-800">
                                        Status <span class="text-rose-500">*</span>
                                    </label>
                                    <select id="status" name="status"
                                        class="mt-1 block w-full rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400">
                                        @php
                                            $oldStatus = old('status', 'draft');
                                        @endphp
                                        <option value="draft" {{ $oldStatus === 'draft' ? 'selected' : '' }}>Draft
                                        </option>
                                        <option value="published" {{ $oldStatus === 'published' ? 'selected' : '' }}>
                                            Published</option>
                                    </select>
                                    <p class="mt-1 text-[11px] text-slate-500">
                                        Drafts are visible only to you. Published items can be surfaced to student view
                                        (read-only).
                                    </p>
                                </div>
                            </div>
                        </div>

                        <hr class="border-slate-100">

                        {{-- Description + Reason --}}
                        <div class="space-y-4">
                            <div
                                class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-slate-500">
                                <i data-lucide="file-text" class="h-4 w-4"></i>
                                <span>What this competency is and why it matters</span>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                {{-- Description --}}
                                <div class="md:col-span-1">
                                    <label for="description" class="block text-sm font-medium text-slate-800">
                                        Description of the skill / competency
                                    </label>
                                    <textarea id="description" name="description" rows="5"
                                        placeholder="Describe what the student nurse must be able to do. e.g., perform IV insertion using aseptic technique, verify orders, monitor for complications."
                                        class="mt-1 block w-full rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400 resize-none">{{ old('description') }}</textarea>
                                </div>

                                {{-- Reason --}}
                                <div class="md:col-span-1">
                                    <label for="reason" class="block text-sm font-medium text-slate-800">
                                        Why this competency is required in real practice
                                    </label>
                                    <textarea id="reason" name="reason" rows="5"
                                        placeholder="Explain why nurses must master this competency. e.g., IV therapy is one of the most common interventions in acute care and errors can be life-threatening."
                                        class="mt-1 block w-full rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400 resize-none">{{ old('reason') }}</textarea>
                                </div>
                            </div>

                            @if(!empty($rotations))
                                <p class="text-[11px] text-slate-500">
                                    Tip: You can connect this competency to rotation-specific skills and RLE case
                                    requirements later
                                    (e.g., DR, OR, MS, ICU, Pedia).
                                </p>
                            @endif
                        </div>

                        <hr class="border-slate-100">

                        {{-- Explanations – nurse perspective --}}
                        <div class="space-y-4">
                            <div class="flex items-center justify-between gap-2">
                                <div
                                    class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-slate-500">
                                    <i data-lucide="sparkles" class="h-4 w-4"></i>
                                    <span>Nurse explanations (optional)</span>
                                </div>
                                <button type="button" id="btnAddExplanation"
                                    class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-[12px] font-medium text-slate-700 hover:bg-slate-100">
                                    <i data-lucide="plus" class="h-3 w-3"></i>
                                    Add explanation
                                </button>
                            </div>

                            <p class="text-[11px] text-slate-500">
                                Use these to speak to student nurses in a nurse’s voice. For example:
                                <span class="italic">“In the ward, you’ll do this almost every shift…”</span>, or
                                <span class="italic">“Common mistakes I see with this competency are…”</span>
                            </p>

                            <div id="explanationsContainer" class="space-y-3">
                                @php
                                    $oldExps = old('explanations', []);
                                @endphp

                                @if(is_array($oldExps) && count($oldExps))
                                    @foreach($oldExps as $idx => $exp)
                                        <div
                                            class="explanation-card rounded-xl border border-slate-200 bg-slate-50/60 p-4 md:p-5 animate-card-in">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="flex-1 space-y-3">
                                                    <div>
                                                        <label class="block text-xs font-medium text-slate-700">
                                                            Explanation title
                                                        </label>
                                                        <input type="text" name="explanations[{{ $idx }}][title]"
                                                            value="{{ $exp['title'] ?? '' }}"
                                                            placeholder="e.g., Why this competency saves lives"
                                                            class="mt-1 block w-full rounded-xl border-slate-200 text-sm py-2 px-3 focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-slate-700">
                                                            Explanation content
                                                        </label>
                                                        <textarea name="explanations[{{ $idx }}][content]" rows="4"
                                                            placeholder="Write this as if you’re explaining to a student nurse at the bedside."
                                                            class="mt-1 block w-full rounded-xl border-slate-200 text-sm py-2 px-3 focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400 resize-none">{{ $exp['content'] ?? '' }}</textarea>
                                                    </div>
                                                </div>

                                                <button type="button"
                                                    class="btnRemoveExplanation inline-flex items-center justify-center h-8 w-8 rounded-full border border-slate-200 bg-white text-slate-500 hover:bg-rose-50 hover:border-rose-200 hover:text-rose-600 mt-1">
                                                    <i data-lucide="x" class="h-4 w-4"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <hr class="border-slate-100">

                        {{-- Actions --}}
                        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
                            <button type="button"
                                onclick="window.location.href='{{ route('faculty.instructor.competencies.index') }}'"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                <i data-lucide="chevron-left" class="h-4 w-4 mr-1.5"></i>
                                Cancel
                            </button>

                            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 sm:items-center">
                                <button type="submit" name="save_as" value="draft"
                                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-slate-900/90 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-900">
                                    <i data-lucide="save" class="h-4 w-4 mr-1.5"></i>
                                    Save Competency
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </section>
    </main>

    @includeIf('partials.faculty-footer')
    @includeWhen(!View::exists('partials.faculty-footer'), 'partials.student-footer')

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        // Explanations repeater
        const explanationsContainer = document.getElementById('explanationsContainer');
        const btnAddExplanation = document.getElementById('btnAddExplanation');

        let explanationIndex = (function () {
            const existing = explanationsContainer
                ? explanationsContainer.querySelectorAll('.explanation-card')
                : [];
            return existing.length ? existing.length : 0;
        })();

        function createExplanationCard(idx) {
            if (!explanationsContainer) return;

            const wrapper = document.createElement('div');
            wrapper.className = 'explanation-card rounded-xl border border-slate-200 bg-slate-50/60 p-4 md:p-5 opacity-0';
            wrapper.innerHTML = `
      <div class="flex items-start justify-between gap-3">
        <div class="flex-1 space-y-3">
          <div>
            <label class="block text-xs font-medium text-slate-700">
              Explanation title
            </label>
            <input type="text"
                   name="explanations[${idx}][title]"
                   placeholder="e.g., What this competency looks like in the ward"
                   class="mt-1 block w-full rounded-xl border-slate-200 text-sm py-2 px-3 focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400">
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-700">
              Explanation content
            </label>
            <textarea name="explanations[${idx}][content]" rows="4"
                      placeholder="Write in a conversational tone, as if you’re teaching a student nurse."
                      class="mt-1 block w-full rounded-xl border-slate-200 text-sm py-2 px-3 focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400 resize-none"></textarea>
          </div>
        </div>

        <button type="button"
                class="btnRemoveExplanation inline-flex items-center justify-center h-8 w-8 rounded-full border border-slate-200 bg-white text-slate-500 hover:bg-rose-50 hover:border-rose-200 hover:text-rose-600 mt-1">
          <i data-lucide="x" class="h-4 w-4"></i>
        </button>
      </div>
    `;

            explanationsContainer.appendChild(wrapper);

            requestAnimationFrame(() => {
                wrapper.classList.add('animate-card-in');
                wrapper.classList.remove('opacity-0');
            });

            lucide.createIcons();
        }

        btnAddExplanation?.addEventListener('click', () => {
            createExplanationCard(explanationIndex++);
        });

        explanationsContainer?.addEventListener('click', (e) => {
            const btn = e.target.closest('.btnRemoveExplanation');
            if (!btn) return;
            const card = btn.closest('.explanation-card');
            if (card) {
                card.classList.add('opacity-0');
                card.style.transition = 'opacity 150ms ease-out, transform 150ms ease-out';
                card.style.transform = 'translateY(4px)';
                setTimeout(() => card.remove(), 160);
            }
        });

        // Inline create-category
        const categorySelect = document.getElementById('category_id');
        const newCategoryTitleInput = document.getElementById('newCategoryTitle');
        const btnCreateCategory = document.getElementById('btnCreateCategory');
        const createCategoryFeedback = document.getElementById('createCategoryFeedback');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        btnCreateCategory?.addEventListener('click', async () => {
            if (!btnCreateCategory || !newCategoryTitleInput || !categorySelect) return;

            const title = newCategoryTitleInput.value.trim();
            if (!title) {
                createCategoryFeedback.textContent = 'Please enter a category name first.';
                createCategoryFeedback.className = 'mt-1 text-[11px] text-rose-600';
                return;
            }

            const url = btnCreateCategory.dataset.url;
            if (!url) {
                createCategoryFeedback.textContent = 'Category create URL is not configured.';
                createCategoryFeedback.className = 'mt-1 text-[11px] text-rose-600';
                return;
            }

            btnCreateCategory.disabled = true;
            createCategoryFeedback.textContent = 'Creating category…';
            createCategoryFeedback.className = 'mt-1 text-[11px] text-slate-500';

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ title })
                });

                if (!res.ok) {
                    throw new Error('Request failed with status ' + res.status);
                }

                const data = await res.json();
                if (!data || typeof data.id === 'undefined') {
                    throw new Error('Unexpected response from server.');
                }

                // Add new option to select and select it
                const opt = new Option(data.title ?? title, data.id, true, true);
                categorySelect.add(opt);
                categorySelect.value = data.id;

                // Clear input
                newCategoryTitleInput.value = '';

                createCategoryFeedback.textContent = 'Category created and selected.';
                createCategoryFeedback.className = 'mt-1 text-[11px] text-emerald-600';
            } catch (err) {
                console.error(err);
                createCategoryFeedback.textContent = 'Unable to create category. Please try again.';
                createCategoryFeedback.className = 'mt-1 text-[11px] text-rose-600';
            } finally {
                btnCreateCategory.disabled = false;
            }
        });

        // Animate main card on load if not reduced motion
        document.addEventListener('DOMContentLoaded', () => {
            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (prefersReducedMotion) {
                document.querySelectorAll('.animate-card-in').forEach(el => {
                    el.style.animation = 'none';
                    el.classList.remove('opacity-0');
                });
            }
        });
    </script>

</body>

</html>
