{{-- resources/views/student/roadmaps/index.blade.php --}}
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Student Nurse • Career Roadmaps · NurSync</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        /* Same animation as Nursing References */
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
    {{-- Student sidebar --}}
    @include('partials.sidebar', ['active' => 'roadmaps'])

    {{-- Main content --}}
    <section class="flex-1 min-w-0">
        <div class="container mx-auto px-8 py-12 space-y-6">

            {{-- HEADER (mirrors Nursing References style) --}}
            <header class="flex items-center justify-between">
                <div class="flex items-start gap-3">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                        <i data-lucide="map" class="h-4 w-4"></i>
                    </span>

                    <div>
                        <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
                            Career Roadmaps
                        </h1>
                        <p class="mt-1 text-[13px] text-slate-500 max-w-2xl">
                            Explore different nursing paths from entry-level bedside roles to advanced practice,
                            leadership, and global opportunities. See the requirements and steps for each journey.
                        </p>
                    </div>
                </div>

                {{-- Legend (small chips on the right for desktop) --}}
                @php
                    $legend = $careerLevelLabels ?? [];
                @endphp
                @if(!empty($legend))
                    <div class="hidden lg:flex flex-wrap gap-2 text-xs">
                        @foreach($legend as $lvl => $label)
                            <span
                                class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 shadow-sm">
                                <span class="mr-1.5 inline-block h-2 w-2 rounded-full
                                    @if($lvl == 1) bg-emerald-500
                                    @elseif($lvl == 2) bg-sky-500
                                    @elseif($lvl == 3) bg-indigo-500
                                    @elseif($lvl == 4) bg-violet-500
                                    @elseif($lvl == 5) bg-amber-500
                                    @else bg-slate-500
                                    @endif"></span>
                                <span class="font-medium text-[11px] text-slate-700">
                                    {{ $label }}
                                </span>
                            </span>
                        @endforeach
                    </div>
                @endif
            </header>

            {{-- Legend (stacked for mobile) --}}
            @if(!empty($legend))
                <div class="lg:hidden flex flex-wrap gap-2 text-xs">
                    @foreach($legend as $lvl => $label)
                        <span
                            class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 shadow-sm">
                            <span class="mr-1.5 inline-block h-2 w-2 rounded-full
                                @if($lvl == 1) bg-emerald-500
                                @elseif($lvl == 2) bg-sky-500
                                @elseif($lvl == 3) bg-indigo-500
                                @elseif($lvl == 4) bg-violet-500
                                @elseif($lvl == 5) bg-amber-500
                                @else bg-slate-500
                                @endif"></span>
                            <span class="font-medium text-[11px] text-slate-700">
                                {{ $label }}
                            </span>
                        </span>
                    @endforeach
                </div>
            @endif

            {{-- FILTERS BAR (styled like Nursing References, still using GET) --}}
            <section
                class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 md:p-5">
                <form method="GET" action="{{ route('student.roadmaps.index') }}"
                      class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">

                    <div class="flex flex-col sm:flex-row gap-3 sm:items-end w-full">

                        {{-- Search --}}
                        <div class="flex-1">
                            <label for="q" class="block text-xs font-semibold text-slate-600 mb-1.5">
                                Search roles
                            </label>
                            <div class="relative">
                                <i data-lucide="search"
                                   class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
                                <input
                                    id="q"
                                    name="q"
                                    type="text"
                                    value="{{ $activeSearch ?? '' }}"
                                    placeholder="Search by role, category, or description…"
                                    class="w-full rounded-xl border-slate-200 bg-white py-2.5 pl-9 pr-3 text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-slate-300 focus:outline-none transition"
                                >
                            </div>
                        </div>

                        {{-- Level filter --}}
                        <div class="sm:w-44">
                            <label for="level" class="block text-xs font-semibold text-slate-600 mb-1.5">
                                Career level
                            </label>
                            <select
                                id="level"
                                name="level"
                                class="w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm text-slate-800 focus:ring-2 focus:ring-slate-300 focus:outline-none transition">
                                <option value="">All levels</option>
                                @php
                                    $selectedLevel = $activeLevel ?? '';
                                @endphp
                                @foreach($levels as $lvl)
                                    @php
                                        $lvlStr = (string) $lvl;
                                        $label = $careerLevelLabels[$lvl] ?? ('Level ' . $lvl);
                                    @endphp
                                    <option value="{{ $lvlStr }}"
                                        {{ (string) $selectedLevel === $lvlStr ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Category filter --}}
                        <div class="sm:w-56">
                            <label for="category" class="block text-xs font-semibold text-slate-600 mb-1.5">
                                Category
                            </label>
                            <select
                                id="category"
                                name="category"
                                class="w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm text-slate-800 focus:ring-2 focus:ring-slate-300 focus:outline-none transition">
                                <option value="">All categories</option>
                                @php
                                    $selectedCategory = $activeCategory ?? '';
                                @endphp
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}"
                                        {{ $selectedCategory === $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Submit button --}}
                    <div class="flex justify-end sm:w-auto">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-sky-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 focus-visible:ring-offset-1 focus-visible:ring-offset-white transition">
                            <i data-lucide="sliders-horizontal" class="mr-2 h-4 w-4"></i>
                            Apply filters
                        </button>
                    </div>
                </form>
            </section>

            {{-- RESULTS SUMMARY (matches the subtle bar under filters) --}}
            <div class="flex items-center justify-between text-xs text-slate-500 px-1">
                <div>
                    @php
                        $count = $roadmaps->count();
                    @endphp
                    @if($count === 0)
                        Showing 0 roadmaps
                    @elseif($count === 1)
                        Showing 1 roadmap
                    @else
                        Showing {{ $count }} roadmaps
                    @endif

                    @if(!empty($activeSearch) || !empty($activeLevel) || !empty($activeCategory))
                        <span class="hidden md:inline">
                            matching your filters.
                        </span>
                    @endif
                </div>

                @if(!empty($activeSearch) || !empty($activeLevel) || !empty($activeCategory))
                    <a href="{{ route('student.roadmaps.index') }}"
                       class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-500 hover:text-slate-700">
                        <i data-lucide="x-circle" class="h-3 w-3"></i>
                        Clear filters
                    </a>
                @endif
            </div>

            {{-- CONTENT AREA: Skeleton + Cards (like Nursing References) --}}
            <div class="bg-transparent">
                {{-- Skeleton grid (first-load visual, like Nursing References) --}}
                <div id="sr-skeleton" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" aria-hidden="true">
                    @for ($i = 0; $i < 6; $i++)
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <div class="animate-pulse">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <span class="h-8 w-8 rounded-xl bg-slate-200"></span>
                                        <div class="space-y-2">
                                            <div class="h-3 w-40 bg-slate-200 rounded"></div>
                                            <div class="h-3 w-24 bg-slate-100 rounded"></div>
                                        </div>
                                    </div>
                                    <span class="h-6 w-20 rounded-full bg-slate-100"></span>
                                </div>
                                <div class="mt-3 space-y-2">
                                    <div class="h-3 w-full bg-slate-100 rounded"></div>
                                    <div class="h-3 w-5/6 bg-slate-100 rounded"></div>
                                </div>
                                <div class="mt-3 flex gap-2">
                                    <span class="h-5 w-16 rounded-full bg-slate-100"></span>
                                    <span class="h-5 w-20 rounded-full bg-slate-100"></span>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>

                {{-- Real content --}}
                @if($roadmaps->isEmpty())
                    <div id="sr-cards-wrapper"
                         class="mt-4 flex flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-slate-300 bg-white/70 px-6 py-10 text-center hidden">
                        <i data-lucide="search-x" class="h-10 w-10 text-slate-300"></i>
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-slate-800">
                                No roadmaps match your current filters.
                            </p>
                            <p class="text-xs text-slate-500 max-w-md">
                                Try clearing your filters or searching with a different keyword.
                            </p>
                        </div>
                    </div>
                @else
                    <section id="sr-cards-wrapper"
                             class="mt-2 grid gap-5 sm:gap-6 md:grid-cols-2 lg:grid-cols-3 hidden">
                        @foreach($roadmaps as $roadmap)
                            @php
                                $lvl = (int) $roadmap->career_level;
                                $levelLabel = $careerLevelLabels[$lvl] ?? 'Unknown level';

                                // Color set per level (CodeCred-style subtle chips)
                                $levelBg = match($lvl) {
                                    1 => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
                                    2 => 'bg-sky-50 text-sky-700 ring-sky-100',
                                    3 => 'bg-indigo-50 text-indigo-700 ring-indigo-100',
                                    4 => 'bg-violet-50 text-violet-700 ring-violet-100',
                                    5 => 'bg-amber-50 text-amber-700 ring-amber-100',
                                    default => 'bg-slate-50 text-slate-700 ring-slate-100',
                                };

                                $category = $roadmap->category ?? '';
                                $slug = $roadmap->slug ?? \Illuminate\Support\Str::slug($roadmap->role);
                            @endphp

                            <article
                                class="js-roadmap-card flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow animate-card-in"
                                data-level="{{ $lvl }}"
                                data-category="{{ $category }}"
                                data-keywords="{{ strtolower($roadmap->role . ' ' . $category . ' ' . ($roadmap->description ?? '')) }}"
                            >
                                {{-- Top row: level pill + icon --}}
                                <div class="flex items-start justify-between gap-3">
                                    <span
                                        class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-medium ring-1 {{ $levelBg }}">
                                        <span
                                            class="mr-1.5 inline-block h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>
                                        {{ $levelLabel }}
                                    </span>

                                    <button type="button"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-400 hover:text-slate-600 hover:border-slate-300 transition"
                                            aria-label="Roadmap details">
                                        <i data-lucide="arrow-up-right" class="h-4 w-4"></i>
                                    </button>
                                </div>

                                {{-- Role title + description --}}
                                <div class="mt-3 space-y-2">
                                    <h2 class="text-[15px] md:text-[16px] font-semibold text-slate-900 leading-snug">
                                        {{ $roadmap->role }}
                                    </h2>
                                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                        {{ $category }}
                                    </p>
                                    @if($roadmap->description)
                                        <p class="text-sm text-slate-600 line-clamp-3">
                                            {{ $roadmap->description }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Chips / quick meta --}}
                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    <span
                                        class="inline-flex items-center rounded-full bg-slate-50 px-2.5 py-1 text-[11px] font-medium text-slate-600">
                                        <i data-lucide="stethoscope" class="mr-1.5 h-3 w-3"></i>
                                        {{ $levelLabel }}
                                    </span>
                                    <span
                                        class="inline-flex items-center rounded-full bg-slate-50 px-2.5 py-1 text-[11px] font-medium text-slate-600">
                                        <i data-lucide="graduation-cap" class="mr-1.5 h-3 w-3"></i>
                                        Path overview
                                    </span>
                                </div>

                                {{-- Footer actions --}}
                                <div
                                    class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
                                    <div class="flex flex-col text-[11px] text-slate-400">
                                        <span>
                                            Updated
                                            {{ optional($roadmap->updated_at)->format('M d, Y') ?? 'Recently' }}
                                        </span>
                                    </div>

                                    <a href="{{ route('student.roadmaps.show', $slug) }}"
                                       class="inline-flex items-center rounded-xl border border-sky-500/80 bg-sky-50/70 px-3 py-2 text-xs font-semibold text-sky-700 hover:bg-sky-600 hover:text-white hover:border-sky-600 transition">
                                        <i data-lucide="route" class="mr-1.5 h-3.5 w-3.5"></i>
                                        View roadmap
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </section>
                @endif
            </div>
        </div>
    </section>
</main>

@include('partials.admin-footer') {{-- or student-footer if you have one --}}

<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>

<script>
    // Simple skeleton -> cards reveal + staggered entrance (mirrors Nursing References behavior)
    document.addEventListener('DOMContentLoaded', () => {
        const skeleton = document.getElementById('sr-skeleton');
        const cardsWrapper = document.getElementById('sr-cards-wrapper');
        const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const delay = prefersReduced ? 0 : 220;

        if (skeleton && cardsWrapper) {
            setTimeout(() => {
                skeleton.classList.add('hidden');
                cardsWrapper.classList.remove('hidden');

                const cards = cardsWrapper.querySelectorAll('.js-roadmap-card');
                cards.forEach((card, index) => {
                    card.classList.remove('animate-card-in');
                    card.style.opacity = '0';
                    card.style.animationDelay = (Math.min(index, 6) * 40) + 'ms';
                    void card.offsetWidth; // force reflow
                    card.classList.add('animate-card-in');
                });
            }, delay);
        }
    });
</script>
</body>
</html>
