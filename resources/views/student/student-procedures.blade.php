{{-- resources/views/student/procedures.blade.php --}}
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <title>Procedures Library · NurSync</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>

<body class="min-h-screen bg-slate-50">
    <main class="min-h-screen flex">
        {{-- Sidebar --}}
        @php($active = 'procedures')
        @include('partials.sidebar')

        {{-- Main content --}}
        <section class="flex-1">
            <div class="container mx-auto px-6 lg:px-8 py-8 lg:py-10 space-y-6">

                {{-- Page header --}}
                <header class="space-y-1">
                    <h1 class="text-[28px] lg:text-[32px] font-extrabold tracking-tight text-slate-900">
                        Procedures Library
                    </h1>
                    <p class="text-sm text-slate-500">
                        Browse campus-approved skills with step-by-step guides, safety reminders, and quick practice checklists.
                    </p>
                </header>
            {{-- 3-step info strip --}}
                <div class="rounded-xl border border-slate-200 bg-white p-4 lg:p-5">
                    <div class="grid gap-6 sm:grid-cols-3">
                        <div class="flex items-start gap-3">
                            <i data-lucide="notebook-pen" class="h-5 w-5 text-slate-600 mt-0.5"></i>
                            <div>
                                <div class="text-sm font-semibold text-slate-900">Choose your Procedure</div>
                                <div class="text-xs text-slate-500">Select from skill guides tailored to your level.</div>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i data-lucide="play-circle" class="h-5 w-5 text-slate-600 mt-0.5"></i>
                            <div>
                                <div class="text-sm font-semibold text-slate-900">Practice the Steps</div>
                                <div class="text-xs text-slate-500">Follow the checklist, use timers, and watch the demo.</div>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i data-lucide="award" class="h-5 w-5 text-slate-600 mt-0.5"></i>
                            <div>
                                <div class="text-sm font-semibold text-slate-900">Get Assessed</div>
                                <div class="text-xs text-slate-500">Formal scoring happens during Return Demo (separate).</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Cards grid --}}
                <div class="grid gap-6 md:grid-cols-2">
                    @forelse($procedures as $p)
                        <article class="rounded-xl border border-slate-200 bg-white p-5 hover:shadow-sm transition-shadow">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 shrink-0">
                                        <i data-lucide="{{ $p->icon ?? 'book-open' }}" class="h-5 w-5 text-slate-700"></i>
                                    </span>
                                    <div class="min-w-0">
                                        <h3 class="text-base sm:text-lg font-semibold text-slate-900 truncate">{{ $p->title }}</h3>
                                        @if(!empty($p->subtitle))
                                            <p class="text-xs text-slate-500 truncate">{{ $p->subtitle }}</p>
                                        @endif
                                    </div>
                                </div>
                                {{-- (level pill removed) --}}
                            </div>

                            {{-- Description --}}
                            @if(!empty($p->description))
                                <p class="mt-2 text-sm leading-6 text-slate-600 line-clamp-3">{{ $p->description }}</p>
                            @endif

                            {{-- Tags --}}
                            @if(!empty($p->tags_json))
                                <div class="mt-3 flex flex-wrap items-center gap-2 text-[11px]">
                                    @foreach((array) $p->tags_json as $tag)
                                        <span class="rounded-full bg-slate-100 px-2 py-1 text-slate-700">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Meta row --}}
                            <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-slate-500">
                                @if(!empty($p->eta_minutes))
                                    <span class="inline-flex items-center gap-1">
                                        <i data-lucide="clock" class="h-3.5 w-3.5"></i> {{ $p->eta_minutes }} minutes
                                    </span>
                                @endif
                                @if($p->video_url || $p->video_path)
                                    <span class="inline-flex items-center gap-1">
                                        <i data-lucide="video" class="h-3.5 w-3.5"></i> Demo
                                    </span>
                                @endif
                                @if(!empty($p->pdf_path))
                                    <span class="inline-flex items-center gap-1">
                                        <i data-lucide="file-text" class="h-3.5 w-3.5"></i> PDF
                                    </span>
                                @endif
                                @if(!empty($p->hazards_text))
                                    <span class="inline-flex items-center gap-1">
                                        <i data-lucide="shield-alert" class="h-3.5 w-3.5"></i> Safety
                                    </span>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="mt-4 flex items-center gap-3">
                                <a href="{{ route('student.procedures.open-guide', $p->slug) }}"
                                   class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:opacity-95">
                                   Open Guide
                                </a>
                                {{-- Wired to the real Practice route --}}
                                <a href="{{ route('student.procedures.practice', $p->slug) }}"
                                   class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                   Practice
                                </a>
                            </div>
                        </article>
                    @empty
                        <p class="text-sm text-slate-500">No procedures found.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </main>

    @include('partials.student-footer')

    <script src="https://unpkg.com/lucide@latest"></script>
    <script> lucide.createIcons(); </script>
</body>
</html>
