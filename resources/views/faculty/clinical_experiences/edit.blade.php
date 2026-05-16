{{-- resources/views/faculty/clinical_experiences/edit.blade.php --}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Edit Clinical Experience · NurSync (CI)</title>

    @vite(['resources/css/app.css','resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body { font-family:'Poppins', ui-sans-serif, system-ui }

        @keyframes slide-in-up {
            from { transform:translateY(10px); opacity:0 }
            to   { transform:translateY(0);   opacity:1 }
        }
        .animate-card-in {
            animation:slide-in-up .35s ease-out both;
            will-change:transform, opacity;
        }
    </style>
</head>

<body class="min-h-screen bg-slate-50">

<main class="min-h-screen flex">
    {{-- Sidebar --}}
    @include('partials.instructor-sidebar', ['active' => 'my_clinical_experience'])

    <section class="flex-1 min-w-0">
        <div class="container mx-auto px-8 py-12 space-y-6">

            {{-- Header --}}
            <header class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <button onclick="window.location.href='{{ route('faculty.instructor.experiences.index') }}'"
                            class="inline-flex items-center justify-center h-9 w-9 rounded-full border border-slate-200 bg-white hover:bg-slate-100 text-slate-700">
                        <i data-lucide="arrow-left" class="h-4 w-4"></i>
                    </button>
                    <div>
                        <h1 class="text-[22px] sm:text-[24px] font-extrabold text-slate-900 leading-tight">
                            Edit Clinical Experience
                        </h1>
                        <p class="text-[13px] text-slate-500 mt-1">
                            Update your story or add new media for student nurses.
                        </p>
                    </div>
                </div>

                <div class="hidden sm:flex items-center gap-3">
                    <a href="{{ route('faculty.instructor.experiences.show', $experience) }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-[13px] text-slate-700 hover:bg-slate-50">
                        <i data-lucide="eye" class="h-4 w-4"></i>
                        View Story
                    </a>
                </div>
            </header>

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 animate-card-in">
                    <div class="flex items-start gap-2">
                        <i data-lucide="check-circle-2" class="h-4 w-4 mt-0.5"></i>
                        <p>{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 animate-card-in">
                    <div class="flex items-start gap-2">
                        <i data-lucide="alert-circle" class="h-4 w-4 mt-0.5"></i>
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            {{-- ERROR BANNER --}}
            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 animate-card-in">
                    <div class="flex items-start gap-2">
                        <i data-lucide="alert-circle" class="h-4 w-4 mt-0.5"></i>
                        <div>
                            <p class="font-semibold">Please check your inputs.</p>
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

                $oldWard   = old('ward', $experience->ward);
                $oldStatus = old('status', $experience->status);
            @endphp

            {{-- FORM CARD --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 md:p-7 lg:p-8 animate-card-in">

                <form method="POST"
                      action="{{ route('faculty.instructor.experiences.update', $experience) }}"
                      enctype="multipart/form-data"
                      class="space-y-8">

                    @csrf
                    @method('PUT')

                    {{-- BASIC INFO --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 text-xs font-medium uppercase text-slate-500 tracking-wide">
                            <i data-lucide="info" class="h-4 w-4"></i>
                            Basic details
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">

                            {{-- Title --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-800">
                                    Story title <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="title"
                                       value="{{ old('title', $experience->title) }}"
                                       class="mt-1 w-full rounded-xl border-slate-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-emerald-300 @error('title') border-rose-300 @enderror">
                            </div>

                            {{-- Ward --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-800">Ward / area</label>
                                <select name="ward"
                                        class="mt-1 w-full rounded-xl border-slate-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-emerald-300">
                                    <option value="">Select ward</option>
                                    @foreach($wardOptions as $code => $label)
                                        <option value="{{ $code }}" {{ $oldWard === $code ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Status --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-800">Status</label>
                                <select name="status"
                                        class="mt-1 w-full rounded-xl border-slate-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-emerald-300">
                                    <option value="draft"     {{ $oldStatus === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ $oldStatus === 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="archived"  {{ $oldStatus === 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <hr class="border-slate-100">

                    {{-- STORY DETAILS --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 text-xs font-medium uppercase text-slate-500 tracking-wide">
                            <i data-lucide="file-text" class="h-4 w-4"></i>
                            Story content
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">

                            {{-- Summary --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-800">
                                    Short summary
                                    <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="summary" rows="4"
                                          class="mt-1 w-full rounded-xl border-slate-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-emerald-300 @error('summary') border-rose-300 @enderror">{{ old('summary', $experience->summary) }}</textarea>
                            </div>

                            {{-- Key Takeaways --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-800">
                                    Key takeaways
                                </label>
                                <textarea name="key_takeaways" rows="4"
                                          placeholder="You may write bullet-style lines, one per line."
                                          class="mt-1 w-full rounded-xl border-slate-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-emerald-300">{{ old('key_takeaways', $experience->key_takeaways) }}</textarea>
                            </div>

                        </div>

                        {{-- Full story --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-800">
                                Full story <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="story" rows="10"
                                      class="mt-1 w-full rounded-xl border-slate-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-emerald-300 @error('story') border-rose-300 @enderror">{{ old('story', $experience->story) }}</textarea>
                        </div>
                    </div>

                    <hr class="border-slate-100">

                    {{-- EXISTING ATTACHMENTS --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 text-xs font-medium uppercase text-slate-500 tracking-wide">
                            <i data-lucide="paperclip" class="h-4 w-4"></i>
                            Current media attachments
                        </div>

                        @if($experience->attachments->isEmpty())
                            <p class="text-[13px] text-slate-500">No existing files.</p>
                        @else
                            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($experience->attachments as $file)
                                    <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-3 flex flex-col animate-card-in">

                                        {{-- Icon + File Name --}}
                                        <div class="flex items-start gap-3">
                                            <div class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-white border border-slate-200">
                                                <i data-lucide="{{ $file->file_type === 'video' ? 'film' : 'image' }}"
                                                   class="h-5 w-5 text-slate-700"></i>
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <p class="text-[13px] font-medium text-slate-800 truncate">
                                                    {{ $file->original_name ?? basename($file->storage_path) }}
                                                </p>
                                                @if($file->caption)
                                                    <p class="text-[12px] text-slate-500 mt-1 italic">
                                                        {{ $file->caption }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Preview --}}
                                        <div class="mt-3">
                                            @if($file->file_type === 'image')
                                                <img src="{{ Storage::url($file->storage_path) }}"
                                                     class="w-full rounded-xl border border-slate-200" alt="">
                                            @else
                                                <video controls class="w-full rounded-xl border border-slate-200">
                                                    <source src="{{ Storage::url($file->storage_path) }}">
                                                </video>
                                            @endif
                                        </div>

                                        {{-- Delete button (JS submits its own form; avoids nested forms) --}}
                                        <button type="button"
                                                class="js-delete-attachment mt-3 inline-flex items-center gap-1 rounded-xl border border-rose-200 bg-rose-50 text-rose-700 text-[12px] px-3 py-1.5 hover:bg-rose-100 w-full justify-center"
                                                data-delete-url="{{ route('faculty.instructor.experiences.attachments.destroy', $file) }}">
                                            <i data-lucide="trash" class="h-3 w-3"></i>
                                            Remove
                                        </button>

                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <hr class="border-slate-100">

                    {{-- ADD NEW ATTACHMENTS --}}
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-xs font-medium uppercase text-slate-500 tracking-wide">
                                <i data-lucide="upload-cloud" class="h-4 w-4"></i>
                                Add more media
                            </div>
                            <span class="text-[11px] text-slate-400">
                                You may attach up to 10 files (images or short videos, max ~50MB each).
                            </span>
                        </div>

                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50/70 p-4 flex flex-col md:flex-row gap-4 md:items-center">
                            <div class="flex items-center gap-3 flex-1">
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white border border-slate-200">
                                    <i data-lucide="images" class="h-5 w-5 text-slate-700"></i>
                                </span>
                                <div class="space-y-1">
                                    <p class="text-sm font-medium text-slate-800">Upload new attachments</p>
                                    <p class="text-[11px] text-slate-500">
                                        Photos & short videos are allowed. Larger files may be rejected by the server.
                                    </p>
                                </div>
                            </div>

                            <div class="md:w-56">
                                <label class="inline-flex items-center justify-center w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-[13px] font-medium text-slate-700 hover:bg-slate-50 cursor-pointer">
                                    <i data-lucide="upload" class="h-4 w-4 mr-1.5"></i>
                                    Choose files
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
                            {{-- JS preview for new files --}}
                        </div>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-between gap-3 pt-2">
                        <button type="button"
                                onclick="window.location.href='{{ route('faculty.instructor.experiences.index') }}'"
                                class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                            <i data-lucide="chevron-left" class="h-4 w-4 mr-1.5"></i>
                            Cancel
                        </button>

                        <button type="submit"
                                class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-900 text-white px-4 py-2.5 text-sm hover:bg-slate-800">
                            <i data-lucide="save" class="h-4 w-4 mr-1.5"></i>
                            Update Story
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </section>
</main>

@includeIf('partials.faculty-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Handle delete attachment via JS-created form (avoids nested forms)
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.js-delete-attachment');
        if (!btn) return;

        if (!confirm('Remove this attachment only? The story will stay.')) {
            return;
        }

        const url = btn.dataset.deleteUrl;
        if (!url) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        form.style.display = 'none';

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = csrfToken;

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';

        form.appendChild(tokenInput);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    });

    // New attachment previews (front-end only, does not affect submit)
    const attachmentsInput  = document.getElementById('attachmentsInput');
    const attachmentsPreview = document.getElementById('attachmentsPreview');

    function fileSize(bytes) {
        const sizes = ['B','KB','MB','GB'];
        if (!bytes || bytes === 0) return '0 B';
        const i = Math.floor(Math.log(bytes) / Math.log(1024));
        return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
    }

    function renderPreview(files) {
        attachmentsPreview.innerHTML = '';

        if (!files || !files.length) return;

        [...files].slice(0, 10).forEach((file) => {
            const isVideo = file.type.startsWith('video');

            const card = document.createElement('div');
            card.className = 'rounded-xl border border-slate-200 bg-slate-50 p-3 flex flex-col gap-2 opacity-0';

            card.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white border border-slate-200">
                        <i data-lucide="${isVideo ? 'film' : 'image'}" class="h-4 w-4 text-slate-700"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[12px] font-medium text-slate-800 truncate">${file.name}</p>
                        <p class="text-[11px] text-slate-500">
                            ${isVideo ? 'VIDEO' : 'IMAGE'} · ${fileSize(file.size)}
                        </p>
                    </div>
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
        renderPreview(attachmentsInput.files);
    });
</script>

</body>
</html>
