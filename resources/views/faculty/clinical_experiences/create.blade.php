{{-- resources/views/faculty/experiences/create.blade.php --}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>New Clinical Experience · NurSync (CI)</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
        }

        /* Smooth card entrance (same as other Instructor Mode pages) */
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
    @include('partials.instructor-sidebar', ['active' => 'my_clinical_experience'])

    {{-- Main content --}}
    <section class="flex-1 min-w-0">
        <div class="container mx-auto px-8 py-12 space-y-6">

            {{-- Page header --}}
            <header class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <button type="button"
                            onclick="window.location.href='{{ route('faculty.instructor.experiences.index') }}'"
                            class="inline-flex items-center justify-center h-9 w-9 rounded-full border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
                        <i data-lucide="arrow-left" class="h-4 w-4"></i>
                    </button>
                    <div>
                        <h1 class="text-[22px] sm:text-[24px] font-extrabold tracking-tight text-slate-900">
                            New Clinical Experience Story
                        </h1>
                        <p class="text-[13px] text-slate-500 mt-1">
                            Capture a real case or shift moment from your perspective as a nurse, then attach photos or
                            videos so student nurses can visualize the scenario.
                        </p>
                    </div>
                </div>

                {{-- Header actions: Back only --}}
                <div class="hidden sm:flex items-center gap-3">
                    <a href="{{ route('faculty.instructor.experiences.index') }}"
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
                $wardOptions = [
                    'CHN'       => 'Community Health Nursing (CHN)',
                    'OB'        => 'Obstetrics (OB)',
                    'DR'        => 'Delivery Room (DR)',
                    'PEDIA'     => 'Pediatrics (PEDIA)',
                    'CDN'       => 'Children’s / DepEd / Community (CDN)',
                    'ONCO'      => 'Oncology',
                    'MS'        => 'Medical-Surgical (MS)',
                    'OR'        => 'Operating Room (OR)',
                    'GERIA'     => 'Geriatric (GERIA)',
                    'ORTHO'     => 'Orthopedics (ORTHO)',
                    'PSYCH'     => 'Psychiatric (PSYCH)',
                    'ICU'       => 'Intensive Care Unit (ICU)',
                    'ER'        => 'Emergency Room (ER)',
                    'DN'        => 'Dialysis / Nephro (DN)',
                    'MEDICINE'  => 'Medicine Ward',
                    'SURGERY'   => 'Surgery Ward',
                ];
                $oldWard   = old('ward');
                $oldStatus = old('status', 'draft');
            @endphp

            {{-- Main form card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 md:p-7 lg:p-8 opacity-0 animate-card-in">
                <form method="POST"
                      action="{{ route('faculty.instructor.experiences.store') }}"
                      enctype="multipart/form-data"
                      class="space-y-6">
                    @csrf

                    {{-- BASIC DETAILS --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-slate-500">
                            <i data-lucide="info" class="h-4 w-4"></i>
                            <span>Basic story details</span>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            {{-- Title --}}
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-slate-800">
                                    Story title <span class="text-rose-500">*</span>
                                </label>
                                <input type="text"
                                       id="title"
                                       name="title"
                                       value="{{ old('title') }}"
                                       placeholder="e.g., Managing a deteriorating patient in the ER"
                                       class="mt-1 block w-full rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400">
                            </div>

                            {{-- Ward --}}
                            <div>
                                <label for="ward" class="block text-sm font-medium text-slate-800">
                                    Ward / area <span class="text-rose-500">*</span>
                                </label>
                                <select id="ward" name="ward"
                                        class="mt-1 block w-full rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400">
                                    <option value="">Select ward</option>
                                    @foreach($wardOptions as $code => $label)
                                        <option value="{{ $code }}" {{ $oldWard === $code ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-[11px] text-slate-500">
                                    Choose where this experience happened. This helps students filter stories by upcoming rotations.
                                </p>
                            </div>

                            {{-- Status --}}
                            <div>
                                <label for="status" class="block text-sm font-medium text-slate-800">
                                    Status <span class="text-rose-500">*</span>
                                </label>
                                <select id="status" name="status"
                                        class="mt-1 block w-full rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400">
                                    <option value="draft"     {{ $oldStatus === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ $oldStatus === 'published' ? 'selected' : '' }}>Published</option>
                                </select>
                                <p class="mt-1 text-[11px] text-slate-500">
                                    Drafts are visible only to you. Published stories can be surfaced to student nurses (read-only).
                                </p>
                            </div>
                        </div>
                    </div>

                    <hr class="border-slate-100">

                    {{-- STORY CONTENT --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-slate-500">
                            <i data-lucide="file-text" class="h-4 w-4"></i>
                            <span>Tell the story from a nurse’s perspective</span>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            {{-- Summary --}}
                            <div class="md:col-span-1">
                                <label for="summary" class="block text-sm font-medium text-slate-800">
                                    Short summary / teaser <span class="text-rose-500">*</span>
                                </label>
                                <textarea id="summary"
                                          name="summary"
                                          rows="4"
                                          placeholder="In 2–3 sentences, describe what this story is about and what the student should notice."
                                          class="mt-1 block w-full rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400 resize-none">{{ old('summary') }}</textarea>
                                <p class="mt-1 text-[11px] text-slate-500">
                                    This is what students will see first on the card. Keep it concise but meaningful.
                                </p>
                            </div>

                            {{-- Key takeaways --}}
                            <div class="md:col-span-1">
                                <label for="key_takeaways" class="block text-sm font-medium text-slate-800">
                                    Key takeaways for student nurses
                                </label>
                                <textarea id="key_takeaways"
                                          name="key_takeaways"
                                          rows="4"
                                          placeholder="Highlight 2–4 things you want students to remember. You can use bullet style lines or short sentences."
                                          class="mt-1 block w-full rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400 resize-none">{{ old('key_takeaways') }}</textarea>
                                <p class="mt-1 text-[11px] text-slate-500">
                                    Example: early warning signs you noticed, communication moves that helped, or mistakes to avoid.
                                </p>
                            </div>
                        </div>

                        {{-- Full story --}}
                        <div>
                            <label for="story" class="block text-sm font-medium text-slate-800">
                                Full story / reflection <span class="text-rose-500">*</span>
                            </label>
                            <textarea id="story"
                                      name="story"
                                      rows="10"
                                      placeholder="Write the experience as if you’re debriefing with your students after duty. Include context, assessment, actions, and what you learned."
                                      class="mt-1 block w-full rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400">{{ old('story') }}</textarea>
                            <p class="mt-1 text-[11px] text-slate-500">
                                You don’t need to use patient identifiers. Focus on the situation, nursing judgment, and how it shaped your practice.
                            </p>
                        </div>
                    </div>

                    <hr class="border-slate-100">

                    {{-- ATTACHMENTS --}}
                    <div class="space-y-4">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-slate-500">
                                <i data-lucide="paperclip" class="h-4 w-4"></i>
                                <span>Optional photos &amp; videos</span>
                            </div>
                            <span class="text-[11px] text-slate-400">
                                Max 10 files · Images &amp; short videos only
                            </span>
                        </div>

                        <p class="text-[11px] text-slate-500">
                            You can add photos of equipment setups, whiteboard notes, or anonymized bedside scenes. Avoid uploading any content
                            that reveals a patient’s identity.
                        </p>

                        <div
                            class="rounded-xl border border-dashed border-slate-300 bg-slate-50/70 px-4 py-5 flex flex-col md:flex-row gap-4 md:items-center">
                            <div class="flex items-center gap-3 flex-1">
                                <span
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white text-slate-700 border border-slate-200">
                                    <i data-lucide="images" class="h-5 w-5"></i>
                                </span>
                                <div class="space-y-1">
                                    <p class="text-sm font-medium text-slate-800">
                                        Attach reference media (optional)
                                    </p>
                                    <p class="text-[11px] text-slate-500">
                                        JPEG, PNG, MP4, MOV. Attachments will appear alongside your story for students to view.
                                    </p>
                                </div>
                            </div>

                            <div class="md:w-56">
                                <label
                                    class="inline-flex items-center justify-center w-full cursor-pointer rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-[13px] font-medium text-slate-700 hover:bg-slate-50">
                                    <i data-lucide="upload-cloud" class="h-4 w-4 mr-1.5"></i>
                                    <span>Choose files</span>
                                    <input type="file"
                                           name="attachments[]"
                                           id="attachmentsInput"
                                           class="sr-only"
                                           accept="image/*,video/*"
                                           multiple>
                                </label>
                            </div>
                        </div>

                        <div id="attachmentsPreview" class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-3 text-[12px]">
                            {{-- JS will populate selected files here --}}
                        </div>
                    </div>

                    <hr class="border-slate-100">

                    {{-- ACTIONS --}}
                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
                        <button type="button"
                                onclick="window.location.href='{{ route('faculty.instructor.experiences.index') }}'"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            <i data-lucide="chevron-left" class="h-4 w-4 mr-1.5"></i>
                            Cancel
                        </button>

                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 sm:items-center">
                            <button type="submit" name="save_as" value="draft"
                                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-slate-900/90 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-900">
                                <i data-lucide="save" class="h-4 w-4 mr-1.5"></i>
                                Save Story
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

    const attachmentsInput   = document.getElementById('attachmentsInput');
    const attachmentsPreview = document.getElementById('attachmentsPreview');

    function formatFileSize(bytes) {
        if (!bytes && bytes !== 0) return '';
        const thresh = 1024;
        if (Math.abs(bytes) < thresh) return bytes + ' B';
        const units = ['KB', 'MB', 'GB'];
        let u = -1;
        do {
            bytes /= thresh;
            ++u;
        } while (Math.abs(bytes) >= thresh && u < units.length - 1);
        return bytes.toFixed(1) + ' ' + units[u];
    }

    function renderAttachmentPreview(files) {
        if (!attachmentsPreview) return;
        attachmentsPreview.innerHTML = '';

        if (!files || !files.length) {
            return;
        }

        [...files].slice(0, 10).forEach((file, idx) => {
            const type = file.type.startsWith('video') ? 'video' : 'image';

            const card = document.createElement('div');
            card.className = 'rounded-xl border border-slate-200 bg-slate-50/80 p-3 flex items-center gap-3 opacity-0';

            card.innerHTML = `
                <div class="flex-shrink-0 inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white border border-slate-200">
                    <i data-lucide="${type === 'video' ? 'film' : 'image'}" class="h-4 w-4 text-slate-700"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-[12px] font-medium text-slate-800 truncate" title="${file.name}">
                        ${file.name}
                    </div>
                    <div class="text-[11px] text-slate-500">
                        ${type.toUpperCase()} · ${formatFileSize(file.size)}
                    </div>
                </div>
                <div class="text-[11px] text-slate-400">
                    #${idx + 1}
                </div>
            `;

            attachmentsPreview.appendChild(card);

            requestAnimationFrame(() => {
                card.classList.add('animate-card-in');
                card.classList.remove('opacity-0');
            });
        });

        lucide.createIcons();
    }

    attachmentsInput?.addEventListener('change', () => {
        renderAttachmentPreview(attachmentsInput.files);
    });

    // Animate main card on load if reduced motion is disabled
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
