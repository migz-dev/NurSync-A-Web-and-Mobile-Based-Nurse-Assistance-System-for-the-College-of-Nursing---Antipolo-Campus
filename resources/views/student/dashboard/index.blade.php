<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard · NurSync (Student)</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
        }

        /* Smooth card entrance (same as CI Dashboard) */
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
    @include('partials.sidebar', ['active' => 'dashboard'])

    {{-- Main --}}
    <section class="flex-1 min-w-0 px-6 lg:px-10 py-10">
        @php
            // Same helper pattern as sidebar: route() with safe fallback URL.
            $r = function (string $name, string $fallback) {
                try {
                    return route($name);
                } catch (\Throwable $e) {
                    return $fallback;
                }
            };

            $modulesExplored    = $stats['modulesExplored']    ?? 0;
            $skillsAvailable    = $stats['skillsAvailable']    ?? 0;
            $emergencyAvailable = $stats['emergencyAvailable'] ?? 0;
        @endphp

        {{-- Header --}}
        <header class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <span
                        class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M12 3l2 3h3a1 1 0 0 1 1 1v2.5" stroke-width="1.5" stroke-linecap="round" />
                            <path d="M6 21h12a1 1 0 0 0 1-1v-7.5a1 1 0 0 0-1-1H6a1 1 0 0 0-1 1V20a1 1 0 0 0 1 1Z"
                                  stroke-width="1.5" />
                            <path d="M9 14l2 2 4-4" stroke-width="1.5" stroke-linecap="round"
                                  stroke-linejoin="round" />
                        </svg>
                    </span>
                    <h1 class="text-2xl font-bold text-slate-900">
                        Student Dashboard
                    </h1>
                </div>
                <p class="text-[13px] text-slate-500 mt-1">
                    Quick access to your skills, procedure guides, and emergency learning materials.
                </p>
            </div>
        </header>

        {{-- LEARNING SNAPSHOT – SKELETON --}}
        <div id="statsSkeleton" aria-hidden="true" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mt-6">
            @for ($i = 0; $i < 3; $i++)
                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <div class="animate-pulse space-y-3">
                        <div class="h-3 w-32 bg-slate-200 rounded"></div>
                        <div class="h-7 w-16 bg-slate-100 rounded mt-2"></div>
                        <div class="h-3 w-40 bg-slate-100 rounded mt-2"></div>
                    </div>
                </div>
            @endfor
        </div>

        {{-- LEARNING SNAPSHOT – REAL (DATA) --}}
        <div id="statsReal" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mt-6 hidden">
            {{-- Card 1: Modules available --}}
            <div class="js-dash-card rounded-2xl border border-slate-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <div class="text-[13px] font-medium text-slate-700">Learning Modules Available</div>
                    <span
                        class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 px-2 py-0.5 text-[10px] font-semibold tracking-wide uppercase">
                        Overview
                    </span>
                </div>
                <div class="mt-2 text-3xl font-bold text-emerald-700">
                    {{ $modulesExplored }}
                </div>
                <p class="mt-1 text-[12px] text-slate-500">
                    Core learning areas currently available to you.
                </p>
            </div>

            {{-- Card 2: Skill checklists --}}
            <div class="js-dash-card rounded-2xl border border-slate-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <div class="text-[13px] font-medium text-slate-700">Skill Checklists</div>
                    <span
                        class="inline-flex items-center rounded-full bg-sky-50 text-sky-700 border border-sky-100 px-2 py-0.5 text-[10px] font-semibold tracking-wide uppercase">
                        Skills
                    </span>
                </div>
                <div class="mt-2 text-3xl font-bold text-sky-700">
                    {{ $skillsAvailable }}
                </div>
                <p class="mt-1 text-[12px] text-slate-500">
                    Skill mastery checklists available to explore.
                </p>
            </div>

            {{-- Card 3: Emergency guides --}}
            <div class="js-dash-card rounded-2xl border border-slate-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <div class="text-[13px] font-medium text-slate-700">Emergency Protocols</div>
                    <span
                        class="inline-flex items-center rounded-full bg-rose-50 text-rose-700 border border-rose-100 px-2 py-0.5 text-[10px] font-semibold tracking-wide uppercase">
                        Critical
                    </span>
                </div>
                <div class="mt-2 text-3xl font-bold text-rose-700">
                    {{ $emergencyAvailable }}
                </div>
                <p class="mt-1 text-[12px] text-slate-500">
                    Life-saving protocols you can review anytime.
                </p>
            </div>
        </div>

        {{-- QUICK ACTIONS – SKELETON --}}
        <div id="quickSkeleton" aria-hidden="true" class="mt-8 rounded-2xl border border-slate-200 bg-white p-5">
            <div class="animate-pulse space-y-4">
                <div class="h-4 w-32 bg-slate-200 rounded"></div>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @for ($i = 0; $i < 4; $i++)
                        <div class="rounded-xl border border-slate-100 px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span class="h-8 w-8 rounded-xl bg-slate-200"></span>
                                <div class="space-y-2 flex-1">
                                    <div class="h-3 w-24 bg-slate-200 rounded"></div>
                                    <div class="h-3 w-20 bg-slate-100 rounded"></div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        {{-- QUICK ACTIONS – REAL (LINKED) --}}
        <div id="quickReal" class="mt-8 rounded-2xl border border-slate-200 bg-white p-5 js-dash-card hidden">
            <div class="flex items-center justify-between">
                <h2 class="text-[15px] font-semibold text-slate-800">Quick Actions</h2>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">

                {{-- Skill Checklists --}}
                <a href="{{ $r('student.skills.index', '/student/skill-checklists') }}"
                   class="js-dash-card rounded-xl border border-slate-200 px-4 py-3 
                          text-[13px] font-medium bg-white flex items-center gap-2
                          hover:border-emerald-300 hover:bg-emerald-50/40 transition cursor-pointer">
                    <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600"></i>
                    <span>Open Skill Checklists</span>
                </a>

                {{-- Procedure Guides --}}
                <a href="{{ $r('student.procedures.index', '/student/procedures') }}"
                   class="js-dash-card rounded-xl border border-slate-200 px-4 py-3 
                          text-[13px] font-medium bg-white flex items-center gap-2
                          hover:border-sky-300 hover:bg-sky-50/40 transition cursor-pointer">
                    <i data-lucide="stethoscope" class="h-4 w-4 text-sky-600"></i>
                    <span>Browse Procedure Guides</span>
                </a>

                {{-- Emergency Protocols --}}
                <a href="{{ $r('student.emergency.index', '/student/emergency-protocols') }}"
                   class="js-dash-card rounded-xl border border-slate-200 px-4 py-3 
                          text-[13px] font-medium bg-white flex items-center gap-2
                          hover:border-rose-300 hover:bg-rose-50/40 transition cursor-pointer">
                    <i data-lucide="shield-alert" class="h-4 w-4 text-rose-600"></i>
                    <span>Emergency Protocols</span>
                </a>

                {{-- Ward Orientation --}}
                <a href="{{ $r('student.wards.index', '/student/ward-orientation') }}"
                   class="js-dash-card rounded-xl border border-slate-200 px-4 py-3 
                          text-[13px] font-medium bg-white flex items-center gap-2
                          hover:border-amber-300 hover:bg-amber-50/40 transition cursor-pointer">
                    <i data-lucide="map" class="h-4 w-4 text-amber-600"></i>
                    <span>Ward Orientation</span>
                </a>

            </div>
        </div>

        {{-- CORE LEARNING MODULES --}}
        <div class="mt-8">
            <div class="flex items-center justify-between">
                <h2 class="text-[15px] font-semibold text-slate-800">Core Learning Modules</h2>
            </div>

            {{-- CORE – SKELETON --}}
            <div id="coreSkeleton" aria-hidden="true" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @for ($i = 0; $i < 3; $i++)
                    <div class="rounded-2xl border border-slate-200 bg-white p-5">
                        <div class="animate-pulse space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <span class="h-8 w-8 rounded-xl bg-slate-200"></span>
                                    <div class="space-y-2">
                                        <div class="h-3 w-32 bg-slate-200 rounded"></div>
                                        <div class="h-3 w-24 bg-slate-100 rounded"></div>
                                    </div>
                                </div>
                                <span class="h-5 w-20 rounded-full bg-slate-100"></span>
                            </div>
                            <div class="h-3 w-full bg-slate-100 rounded"></div>
                            <div class="h-3 w-2/3 bg-slate-100 rounded"></div>
                            <div class="mt-3 flex gap-2">
                                <span class="h-3 w-24 rounded bg-slate-100"></span>
                                <span class="h-3 w-28 rounded bg-slate-100"></span>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>

            {{-- CORE – REAL (LINKED) --}}
            <div id="coreReal" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 hidden">

                {{-- Card 1: Skill Mastery --}}
                <a href="{{ $r('student.skills.index', '/student/skill-checklists') }}"
                   class="js-dash-card block rounded-2xl border border-slate-200 bg-white p-5
                          hover:border-emerald-300 hover:bg-emerald-50/40 transition cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-2">
                            <span
                                class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                                <i data-lucide="check-circle-2" class="h-4 w-4"></i>
                            </span>
                            <div>
                                <div class="text-[13px] font-semibold text-slate-800">Skill Mastery Checklists</div>
                                <div class="text-[12px] text-slate-500">Practice-ready skills</div>
                            </div>
                        </div>
                        <span
                            class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 px-2.5 py-1 text-[11px] font-medium">
                            View-only
                        </span>
                    </div>

                    <div class="mt-3 text-[12px] text-slate-500 line-clamp-2">
                        Explore nursing skills with step-by-step checklists guided by your clinical instructors.
                    </div>

                    <div class="mt-4 flex items-center gap-4 text-[11px] text-slate-500">
                        <div class="flex items-center gap-1">
                            <i data-lucide="target" class="h-3.5 w-3.5 text-emerald-600"></i>
                            <span>Focus on core skills</span>
                        </div>
                    </div>
                </a>

                {{-- Card 2: Procedure Guides --}}
                <a href="{{ $r('student.procedures.index', '/student/procedures') }}"
                   class="js-dash-card block rounded-2xl border border-slate-200 bg-white p-5
                          hover:border-sky-300 hover:bg-sky-50/40 transition cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-2">
                            <span
                                class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                                <i data-lucide="stethoscope" class="h-4 w-4"></i>
                            </span>
                            <div>
                                <div class="text-[13px] font-semibold text-slate-800">Procedure Guides</div>
                                <div class="text-[12px] text-slate-500">Step-by-step</div>
                            </div>
                        </div>
                        <span
                            class="inline-flex items-center rounded-full bg-sky-50 text-sky-700 border border-sky-100 px-2.5 py-1 text-[11px] font-medium">
                            With hazards & PPE
                        </span>
                    </div>

                    <div class="mt-3 text-[12px] text-slate-500 line-clamp-2">
                        Learn how real nurses perform procedures, including preparation, execution, and safety
                        checks.
                    </div>

                    <div class="mt-4 flex items-center gap-4 text-[11px] text-slate-500">
                        <div class="flex items-center gap-1">
                            <i data-lucide="play-circle" class="h-3.5 w-3.5 text-sky-600"></i>
                            <span>Watch videos & demos</span>
                        </div>
                    </div>
                </a>

                {{-- Card 3: Emergency & Ward Orientation --}}
                <a href="{{ $r('student.emergency.index', '/student/emergency-protocols') }}"
                   class="js-dash-card block rounded-2xl border border-slate-200 bg-white p-5
                          hover:border-rose-300 hover:bg-rose-50/40 transition cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-2">
                            <span
                                class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                                <i data-lucide="shield-alert" class="h-4 w-4"></i>
                            </span>
                            <div>
                                <div class="text-[13px] font-semibold text-slate-800">Emergency & Ward Orientation
                                </div>
                                <div class="text-[12px] text-slate-500">Be prepared</div>
                            </div>
                        </div>
                        <span
                            class="inline-flex items-center rounded-full bg-rose-50 text-rose-700 border border-rose-100 px-2.5 py-1 text-[11px] font-medium">
                            High-yield
                        </span>
                    </div>

                    <div class="mt-3 text-[12px] text-slate-500 line-clamp-2">
                        Review emergency algorithms and get oriented to different wards through CI-led guides.
                    </div>

                    <div class="mt-4 flex items-center gap-4 text-[11px] text-slate-500">
                        <div class="flex items-center gap-1">
                            <i data-lucide="map" class="h-3.5 w-3.5 text-amber-600"></i>
                            <span>Understand ward culture</span>
                        </div>
                    </div>
                </a>

            </div>
        </div> {{-- /CORE LEARNING MODULES WRAPPER --}}
    </section>
</main>

{{-- Animations + skeleton reveal (same pattern as CI Dashboard, without loader overlay) --}}
<script>
    (function () {
        const statsSkeleton = document.getElementById('statsSkeleton');
        const statsReal = document.getElementById('statsReal');
        const quickSkeleton = document.getElementById('quickSkeleton');
        const quickReal = document.getElementById('quickReal');
        const coreSkeleton = document.getElementById('coreSkeleton');
        const coreReal = document.getElementById('coreReal');

        function revealAndAnimate() {
            if (statsSkeleton && statsReal) {
                statsSkeleton.classList.add('hidden');
                statsReal.classList.remove('hidden');
            }
            if (quickSkeleton && quickReal) {
                quickSkeleton.classList.add('hidden');
                quickReal.classList.remove('hidden');
            }
            if (coreSkeleton && coreReal) {
                coreSkeleton.classList.add('hidden');
                coreReal.classList.remove('hidden');
            }

            const cards = Array.from(document.querySelectorAll('.js-dash-card'));

            // Add base opacity-0 so animation can fade them in
            cards.forEach(card => {
                card.classList.add('opacity-0');
            });

            // Staggered slide-in animation (like CI Dashboard)
            cards.forEach((card, idx) => {
                card.style.animationDelay = `${Math.min(idx, 6) * 40}ms`;
                requestAnimationFrame(() => {
                    card.classList.add('animate-card-in');
                    card.classList.remove('opacity-0');
                });
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const delay = prefersReduced ? 0 : 220;
            setTimeout(revealAndAnimate, delay);
        });
    })();
</script>

{{-- Lucide icons --}}
<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>

</body>
</html>
