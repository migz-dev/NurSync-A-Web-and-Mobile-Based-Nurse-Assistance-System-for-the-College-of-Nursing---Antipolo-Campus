<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $experience->title }} · Clinical Experience · NurSync (Student)</title>

    @vite(['resources/css/app.css','resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family:'Poppins', ui-sans-serif, system-ui }
        @keyframes slide-in-up {
            from { transform:translateY(8px); opacity:0 }
            to   { transform:translateY(0);   opacity:1 }
        }
        .animate-card-in {
            animation:slide-in-up .35s ease-out both;
        }
    </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">

    {{-- Sidebar --}}
    @include('partials.sidebar', ['active' => 'clinical_experiences'])

    <section class="flex-1 min-w-0">
        <div class="container mx-auto px-8 py-10 space-y-10">

            {{-- Back button --}}
            <div class="flex items-center gap-3 mb-2">
                <button onclick="history.back()"
                        class="inline-flex items-center justify-center h-9 w-9 rounded-full border border-slate-300 bg-white hover:bg-slate-100">
                    <i data-lucide="arrow-left" class="h-4 w-4"></i>
                </button>

                <span class="text-[13px] text-slate-500">
                    Clinical Experience / {{ $experience->title }}
                </span>
            </div>

            {{-- Title --}}
            <header class="space-y-3 animate-card-in">
                <h1 class="text-[28px] sm:text-[32px] font-extrabold text-slate-900 leading-tight">
                    {{ $experience->title }}
                </h1>

                <div class="flex flex-wrap items-center gap-3 text-[13px] text-slate-600">

                    {{-- Ward --}}
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1">
                        <i data-lucide="hospital" class="h-3.5 w-3.5"></i>
                        {{ $experience->ward }}
                    </span>

                    {{-- Author --}}
                    <span class="inline-flex items-center gap-1.5">
                        <i data-lucide="user" class="h-3.5 w-3.5"></i>
                        {{ $experience->faculty?->display_name ?? 'Clinical Instructor' }}
                    </span>

                    {{-- Updated --}}
                    <span class="inline-flex items-center gap-1.5">
                        <i data-lucide="clock" class="h-3.5 w-3.5"></i>
                        Updated {{ $experience->updated_at->diffForHumans() }}
                    </span>
                </div>
            </header>

            {{-- Primary media preview (size retained) --}}
            @php
                $primary = $experience->attachments->firstWhere('is_primary', 1)
                         ?? $experience->attachments->first();
            @endphp

            @if($primary)
                <div class="rounded-2xl overflow-hidden border border-slate-200 shadow-sm animate-card-in">
                    @if($primary->file_type === 'image')
                        <img src="{{ Storage::url($primary->storage_path) }}"
                             class="w-full h-auto object-cover">
                    @else
                        <video controls class="w-full rounded-none">
                            <source src="{{ Storage::url($primary->storage_path) }}">
                        </video>
                    @endif
                </div>
            @endif

            {{-- Accordions block --}}
            <section class="space-y-4">

                {{-- Summary --}}
                <details class="group rounded-2xl bg-white border border-slate-200 shadow-sm animate-card-in" open>
                    <summary class="flex items-center justify-between gap-3 px-6 py-4 cursor-pointer select-none">
                        <div class="flex items-center gap-2">
                            <i data-lucide="align-left" class="h-5 w-5 text-slate-700"></i>
                            <span class="text-[18px] font-semibold text-slate-900">
                                Summary
                            </span>
                        </div>
                        <i data-lucide="chevron-down"
                           class="h-5 w-5 text-slate-500 transition-transform duration-200 group-open:rotate-180"></i>
                    </summary>

                    <div class="px-6 pb-5 text-[15px] text-slate-700 leading-relaxed">
                        {{ $experience->summary }}
                    </div>
                </details>

                {{-- Full Story --}}
                <details class="group rounded-2xl bg-white border border-slate-200 shadow-sm animate-card-in">
                    <summary class="flex items-center justify-between gap-3 px-6 py-4 cursor-pointer select-none">
                        <div class="flex items-center gap-2">
                            <i data-lucide="newspaper" class="h-5 w-5 text-slate-700"></i>
                            <span class="text-[18px] font-semibold text-slate-900">
                                Full Story
                            </span>
                        </div>
                        <i data-lucide="chevron-down"
                           class="h-5 w-5 text-slate-500 transition-transform duration-200 group-open:rotate-180"></i>
                    </summary>

                    <div class="px-6 pb-6 prose prose-slate max-w-none text-[15px] leading-relaxed whitespace-pre-line">
                        {{ $experience->story }}
                    </div>
                </details>

                {{-- Key Takeaways --}}
                @if($experience->key_takeaways)
                    @php
                        $takeaways = array_filter(array_map('trim', explode("\n", $experience->key_takeaways)));
                    @endphp

                    <details class="group rounded-2xl bg-white border border-slate-200 shadow-sm animate-card-in">
                        <summary class="flex items-center justify-between gap-3 px-6 py-4 cursor-pointer select-none">
                            <div class="flex items-center gap-2">
                                <i data-lucide="sparkles" class="h-5 w-5 text-slate-700"></i>
                                <span class="text-[18px] font-semibold text-slate-900">
                                    Key Takeaways
                                </span>
                            </div>
                            <i data-lucide="chevron-down"
                               class="h-5 w-5 text-slate-500 transition-transform duration-200 group-open:rotate-180"></i>
                        </summary>

                        <div class="px-6 pb-6 flex flex-wrap gap-2 text-[13px]">
                            @foreach($takeaways as $t)
                                <span class="rounded-full border border-emerald-200 bg-emerald-50 text-emerald-700 px-3 py-1">
                                    {{ $t }}
                                </span>
                            @endforeach
                        </div>
                    </details>
                @endif

                {{-- Additional Media (accordion, attachments same size) --}}
                @if($experience->attachments->count() > 1)
                    <details class="group rounded-2xl bg-white border border-slate-200 shadow-sm animate-card-in">
                        <summary class="flex items-center justify-between gap-3 px-6 py-4 cursor-pointer select-none">
                            <div class="flex items-center gap-2">
                                <i data-lucide="paperclip" class="h-5 w-5 text-slate-700"></i>
                                <span class="text-[18px] font-semibold text-slate-900">
                                    Additional Media
                                </span>
                            </div>
                            <i data-lucide="chevron-down"
                               class="h-5 w-5 text-slate-500 transition-transform duration-200 group-open:rotate-180"></i>
                        </summary>

                        <div class="px-6 pb-6">
                            <p class="text-[12px] text-slate-500 mb-3">
                                Short clips or extra visuals that support this clinical scenario.
                            </p>

                            <div class="grid gap-4 sm:grid-cols-2">
                                @foreach($experience->attachments->skip(1) as $file)
                                    <div class="border border-slate-200 rounded-xl bg-slate-50 p-3">
                                        <div class="aspect-video rounded-lg overflow-hidden bg-black">
                                            @if($file->file_type === 'image')
                                                <img src="{{ Storage::url($file->storage_path) }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <video controls class="w-full h-full">
                                                    <source src="{{ Storage::url($file->storage_path) }}">
                                                </video>
                                            @endif
                                        </div>
                                        @if($file->caption)
                                            <p class="mt-2 text-[12px] text-slate-600 italic">
                                                {{ $file->caption }}
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </details>
                @endif

            </section>

        </div>
    </section>
</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>

</body>
</html>
