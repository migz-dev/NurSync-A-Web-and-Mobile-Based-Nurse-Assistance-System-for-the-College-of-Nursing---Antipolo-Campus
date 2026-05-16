{{-- resources/views/developer/portfolio.blade.php --}}
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Developer Portfolio · NurSync</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI",
                sans-serif;
        }

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
<main class="min-h-screen flex flex-col">

    {{-- MAIN CONTENT --}}
    <section class="flex-1">
        <div class="max-w-6xl mx-auto px-6 sm:px-8 py-10 sm:py-14 space-y-10">

            {{-- HEADER --}}
            <header class="space-y-3 animate-card-in">
                <span
                    class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 shadow-sm">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    Developer Portfolio · Static
                </span>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">
                            Miguel Caluya · Full-Stack / Mobile Developer
                        </h1>
                        <p class="mt-2 max-w-2xl text-sm sm:text-[15px] text-slate-600">
                            I build focused, real-world systems for nursing education and clinical workflows —
                            combining Laravel, Kotlin Android, and modern UI patterns to create tools that nurses
                            actually want to use.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a href="mailto:youremail@example.com"
                           class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-4 py-2 text-xs sm:text-sm font-medium text-white shadow-sm hover:bg-slate-800 transition">
                            <span>Contact Developer</span>
                        </a>
                        <span
                            class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-[11px] font-medium text-slate-700">
                            Capstone Developer · NurSync
                        </span>
                    </div>
                </div>
            </header>

            {{-- LAYOUT GRID --}}
            <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,1.2fr)] items-start">

                {{-- LEFT: PROJECTS --}}
                <section class="space-y-4 animate-card-in" style="animation-delay: .05s">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-700">
                                Flagship Projects
                            </h2>
                            <p class="mt-1 text-xs text-slate-500">
                                Real systems shipped for education, clinical practice, and learning tools.
                            </p>
                        </div>
                        <span class="text-[11px] text-slate-400">
                            Static showcase · No auth required
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                        {{-- Project 1: NurSync --}}
                        <article
                            class="bento-card rounded-2xl bg-white border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow animate-card-in">
                            <div class="flex items-start justify-between gap-2">
                                <div class="space-y-1">
                                    <h3 class="text-[15px] font-semibold text-slate-900">
                                        NurSync · Nurse Assistance System
                                    </h3>
                                    <p class="text-[12px] text-slate-600 leading-snug">
                                        A web + mobile platform for nursing education — chartings, procedure guides,
                                        drug reference, and clinical learning tools in one ecosystem.
                                    </p>
                                </div>
                            </div>

                            <div class="mt-3 flex flex-wrap gap-1.5 items-center">
                                <span
                                    class="font-mono text-[11px] bg-slate-900 text-slate-50 px-2.5 py-1 rounded-md inline-block">
                                    nursync.app (Capstone)
                                </span>
                                <span
                                    class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-medium text-emerald-700">
                                    Laravel · Kotlin Android
                                </span>
                                <span
                                    class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-700">
                                    MySQL · REST API
                                </span>
                            </div>
                        </article>

                        {{-- Project 2: CodeCred --}}
                        <article
                            class="bento-card rounded-2xl bg-white border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow animate-card-in"
                            style="animation-delay:.03s">
                            <div class="space-y-1">
                                <h3 class="text-[15px] font-semibold text-slate-900">
                                    CodeCred · Developer Learning Hub
                                </h3>
                                <p class="text-[12px] text-slate-600 leading-snug">
                                    A clean, card-based learning space that showcases roadmaps, guides, and tools for
                                    developers with smooth animations and polished UI.
                                </p>
                            </div>

                            <div class="mt-3 flex flex-wrap gap-1.5 items-center">
                                <span
                                    class="font-mono text-[11px] bg-gray-100 text-slate-800 px-2.5 py-1 rounded-md inline-block">
                                    codecred.dev
                                </span>
                                <span
                                    class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-medium text-indigo-700">
                                    Tailwind · Laravel
                                </span>
                                <span
                                    class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-700">
                                    Animations · UX
                                </span>
                            </div>
                        </article>

                        {{-- Project 3: BASE404 --}}
                        <article
                            class="bento-card rounded-2xl bg-white border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow animate-card-in"
                            style="animation-delay:.06s">
                            <div class="space-y-1">
                                <h3 class="text-[15px] font-semibold text-slate-900">
                                    BASE404 · Error & Status Pages
                                </h3>
                                <p class="text-[12px] text-slate-600 leading-snug">
                                    A mini design system of friendly, animated error and onboarding screens that can be
                                    dropped into any Laravel or static project.
                                </p>
                            </div>

                            <div class="mt-3 flex flex-wrap gap-1.5 items-center">
                                <span
                                    class="font-mono text-[11px] bg-gray-100 text-slate-800 px-2.5 py-1 rounded-md inline-block">
                                    base404.dev
                                </span>
                                <span
                                    class="inline-flex items-center rounded-full bg-fuchsia-50 px-2 py-0.5 text-[11px] font-medium text-fuchsia-700">
                                    Micro UI Pack
                                </span>
                                <span
                                    class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-700">
                                    HTML · Tailwind
                                </span>
                            </div>
                        </article>

                        {{-- Project 4: Demo Slot (optional static card) --}}
                        <article
                            class="bento-card rounded-2xl bg-slate-900/95 border border-slate-900 p-4 shadow-sm hover:shadow-lg transition-shadow animate-card-in"
                            style="animation-delay:.09s">
                            <div class="space-y-1">
                                <h3 class="text-[15px] font-semibold text-slate-50">
                                    Next: Open for Collaboration
                                </h3>
                                <p class="text-[12px] text-slate-200 leading-snug">
                                    Available for nursing-focused tools, learning platforms, and clinical reference
                                    apps that need both solid backend and careful UI.
                                </p>
                            </div>

                            <div class="mt-3 flex flex-wrap gap-1.5 items-center">
                                <span
                                    class="inline-flex items-center rounded-full bg-emerald-500/20 px-2 py-0.5 text-[11px] font-medium text-emerald-200">
                                    Nursing · Education · Healthtech
                                </span>
                                <span
                                    class="inline-flex items-center rounded-full bg-slate-800 px-2 py-0.5 text-[11px] text-slate-200">
                                    Let’s build something
                                </span>
                            </div>
                        </article>
                    </div>
                </section>

                {{-- RIGHT: STACK, FOCUS, LINKS --}}
                <aside class="space-y-4 animate-card-in" style="animation-delay:.1s">
                    {{-- Tech stack --}}
                    <article
                        class="rounded-2xl bg-white border border-slate-200 p-4 shadow-sm space-y-3">
                        <h2 class="text-sm font-semibold text-slate-800">
                            Tech Stack & Focus
                        </h2>
                        <p class="text-xs text-slate-600">
                            I tend to blend solid backend structure with clean, animated frontends — always anchored on
                            realistic nursing and education workflows.
                        </p>

                        <div class="flex flex-wrap gap-1.5 pt-1">
                            <span
                                class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] text-slate-800">
                                Laravel · PHP
                            </span>
                            <span
                                class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] text-slate-800">
                                MySQL · REST API
                            </span>
                            <span
                                class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] text-slate-800">
                                Kotlin · Android
                            </span>
                            <span
                                class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] text-slate-800">
                                TailwindCSS · Vite
                            </span>
                            <span
                                class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] text-slate-800">
                                UI/UX · Animations
                            </span>
                        </div>
                    </article>

                    {{-- What I like building --}}
                    <article
                        class="rounded-2xl bg-slate-900 text-slate-50 p-4 shadow-sm space-y-3">
                        <h2 class="text-sm font-semibold">
                            What I like to build
                        </h2>
                        <ul class="space-y-1.5 text-xs text-slate-100/90">
                            <li>• Clinical charting tools that feel natural for nurses.</li>
                            <li>• Student-facing learning apps that are simple but powerful.</li>
                            <li>• Admin dashboards that make governance less painful.</li>
                            <li>• Small, beautiful utilities like roadmaps, checklists, and guides.</li>
                        </ul>
                    </article>

                    {{-- Quick links --}}
                    <article
                        class="rounded-2xl bg-white border border-slate-200 p-4 shadow-sm space-y-3">
                        <h2 class="text-sm font-semibold text-slate-800">
                            Links & Contact
                        </h2>
                        <div class="space-y-1.5 text-xs text-slate-600">
                            <p>
                                Email:
                                <a href="mailto:youremail@example.com"
                                   class="font-medium text-slate-900 hover:underline">
                                    youremail@example.com
                                </a>
                            </p>
                            <p>
                                GitHub:
                                <span class="font-mono text-[11px] bg-gray-100 px-2 py-1 rounded-md inline-block">
                                    github.com/yourusername
                                </span>
                            </p>
                            <p>
                                LinkedIn:
                                <span class="font-mono text-[11px] bg-gray-100 px-2 py-1 rounded-md inline-block">
                                    linkedin.com/in/yourusername
                                </span>
                            </p>
                        </div>

                        <p class="mt-2 text-[11px] text-slate-400">
                            This page is static and for portfolio / documentation purposes only.
                        </p>
                    </article>
                </aside>
            </div>

        </div>
    </section>

    {{-- SIMPLE FOOTER STRIP (optional, separate from main site footer) --}}
    <footer class="border-t border-slate-200 bg-white">
        <div class="max-w-6xl mx-auto px-6 sm:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="text-[11px] text-slate-500">
                © {{ date('Y') }} NurSync · Developer Portfolio
            </p>
            <p class="text-[11px] text-slate-400">
                Built with Laravel, Tailwind, and way too many cups of coffee.
            </p>
        </div>
    </footer>
</main>
</body>
</html>
