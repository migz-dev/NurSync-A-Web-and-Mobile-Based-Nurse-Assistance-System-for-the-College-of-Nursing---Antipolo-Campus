{{-- resources/views/student/roadmaps/show.blade.php --}}
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ $roadmap->role }} · Career Roadmap · NurSync (Student)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', ui-sans-serif, system-ui;
        }

        @keyframes slide-in-up {
            from {
                transform: translateY(8px);
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

    {{-- Student Sidebar --}}
    @include('partials.sidebar', ['active' => 'roadmaps'])

    <section class="flex-1">
        <div class="container mx-auto px-6 lg:px-10 py-8 space-y-8">

            {{-- BACK BUTTON --}}
            <div class="animate-card-in">
                <a href="{{ route('student.roadmaps.index') }}"
                   class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-800 transition">
                    <i data-lucide="arrow-left" class="h-4 w-4"></i>
                    Back to Roadmaps
                </a>
            </div>

            {{-- HEADER --}}
            <header class="space-y-4 animate-card-in">
                <div class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-sky-600">
                    <i data-lucide="map" class="h-4 w-4"></i>
                    <span>Career Roadmap</span>
                </div>

                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    {{ $roadmap->role }}
                </h1>

                {{-- Chips --}}
                @php
                    $lvl = (int) $roadmap->career_level;
                    $levelLabel = $careerLevelLabels[$lvl] ?? 'Level ' . $lvl;

                    $levelBg = match($lvl) {
                        1 => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                        2 => 'bg-sky-50 text-sky-700 ring-sky-200',
                        3 => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
                        4 => 'bg-violet-50 text-violet-700 ring-violet-200',
                        5 => 'bg-amber-50 text-amber-700 ring-amber-200',
                        default => 'bg-slate-50 text-slate-700 ring-slate-200',
                    };
                @endphp

                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium ring-1 {{ $levelBg }}">
                        {{ $levelLabel }}
                    </span>

                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                        <i data-lucide="folder" class="h-3.5 w-3.5 mr-1"></i>
                        {{ $roadmap->category }}
                    </span>
                </div>

                @if($roadmap->description)
                    <p class="text-slate-700 text-[15px] max-w-3xl leading-relaxed">
                        {{ $roadmap->description }}
                    </p>
                @endif
            </header>

            {{-- MAIN CONTENT GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- LEFT COLUMN — OVERVIEW --}}
                <aside class="lg:col-span-1 space-y-6">

                    {{-- Overview card --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm animate-card-in">
                        <h2 class="text-lg font-semibold text-slate-900 mb-3">
                            Overview
                        </h2>

                        <ul class="space-y-4 text-sm text-slate-700">
                            <li class="flex gap-3">
                                <i data-lucide="briefcase" class="h-4 w-4 text-slate-400"></i>
                                <div>
                                    <p class="font-medium">Role</p>
                                    <p class="text-slate-600">{{ $roadmap->role }}</p>
                                </div>
                            </li>

                            <li class="flex gap-3">
                                <i data-lucide="layers" class="h-4 w-4 text-slate-400"></i>
                                <div>
                                    <p class="font-medium">Career Level</p>
                                    <p class="text-slate-600">{{ $levelLabel }}</p>
                                </div>
                            </li>

                            <li class="flex gap-3">
                                <i data-lucide="tag" class="h-4 w-4 text-slate-400"></i>
                                <div>
                                    <p class="font-medium">Category</p>
                                    <p class="text-slate-600">{{ $roadmap->category }}</p>
                                </div>
                            </li>

                            @if($roadmap->requirements)
                                <li class="flex gap-3">
                                    <i data-lucide="clipboard-list" class="h-4 w-4 text-slate-400"></i>
                                    <div>
                                        <p class="font-medium">Requirements</p>
                                        <p class="text-slate-600 whitespace-pre-line">
                                            {{ $roadmap->requirements }}
                                        </p>
                                    </div>
                                </li>
                            @endif
                        </ul>

                        <div class="mt-5 pt-5 border-t text-xs text-slate-400">
                            Last updated {{ optional($roadmap->updated_at)->format('M d, Y') }}
                        </div>
                    </div>

                </aside>

                {{-- RIGHT COLUMN — STEPS --}}
                <section class="lg:col-span-2 space-y-6">

                    {{-- Steps Card --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm animate-card-in">
                        <h2 class="text-lg font-semibold text-slate-900">
                            Steps to Reach This Role
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            A guided path you can follow as you progress in your nursing journey.
                        </p>

                        <ol class="mt-6 space-y-5">
                            @foreach($steps as $i => $step)
                                @php
                                    $num = $i + 1;
                                @endphp

                                <li class="flex items-start gap-4 group animate-card-in" style="animation-delay: {{ 0.04 * $i }}s;">
                                    {{-- Step badge --}}
                                    <div
                                        class="flex h-9 w-9 items-center justify-center rounded-full border-2 border-sky-500 text-sky-600 font-semibold group-hover:bg-sky-500 group-hover:text-white transition">
                                        {{ $num }}
                                    </div>

                                    {{-- Step text --}}
                                    <p class="text-slate-800 text-[15px] leading-relaxed">
                                        {{ $step }}
                                    </p>
                                </li>
                            @endforeach
                        </ol>
                    </div>

                </section>
            </div>

        </div>
    </section>

</main>

<script>
    // Staggered animation on steps
    document.addEventListener('DOMContentLoaded', () => {
        const items = document.querySelectorAll('li.animate-card-in');
        items.forEach((el, idx) => {
            el.style.animationDelay = `${idx * 0.05}s`;
        });
    });
</script>

</body>
</html>
