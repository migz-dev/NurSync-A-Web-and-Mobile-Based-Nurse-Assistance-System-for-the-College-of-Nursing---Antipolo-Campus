<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>CI Dashboard · NurSync</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }

    /* Smooth card entrance after skeleton hides (same as Chartings) */
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

    /* Instructor Mode loader */
    .pl {
      width: 6em;
      height: 6em;
    }

    .pl__ring {
      animation: ringA 2s linear infinite;
    }

    .pl__ring--a {
      stroke: #000000;
    }

    .pl__ring--b {
      animation-name: ringB;
      stroke: #7e7e7e;
    }

    .pl__ring--c {
      animation-name: ringC;
      stroke: #686868;
    }

    .pl__ring--d {
      animation-name: ringD;
      stroke: #000000;
    }

    @keyframes ringA {

      from,
      4% {
        stroke-dasharray: 0 660;
        stroke-width: 20;
        stroke-dashoffset: -330;
      }

      12% {
        stroke-dasharray: 60 600;
        stroke-width: 30;
        stroke-dashoffset: -335;
      }

      32% {
        stroke-dasharray: 60 600;
        stroke-width: 30;
        stroke-dashoffset: -595;
      }

      40%,
      54% {
        stroke-dasharray: 0 660;
        stroke-width: 20;
        stroke-dashoffset: -660;
      }

      62% {
        stroke-dasharray: 60 600;
        stroke-width: 30;
        stroke-dashoffset: -665;
      }

      82% {
        stroke-dasharray: 60 600;
        stroke-width: 30;
        stroke-dashoffset: -925;
      }

      90%,
      to {
        stroke-dasharray: 0 660;
        stroke-width: 20;
        stroke-dashoffset: -990;
      }
    }

    @keyframes ringB {

      from,
      12% {
        stroke-dasharray: 0 220;
        stroke-width: 20;
        stroke-dashoffset: -110;
      }

      20% {
        stroke-dasharray: 20 200;
        stroke-width: 30;
        stroke-dashoffset: -115;
      }

      40% {
        stroke-dasharray: 20 200;
        stroke-width: 30;
        stroke-dashoffset: -195;
      }

      48%,
      62% {
        stroke-dasharray: 0 220;
        stroke-width: 20;
        stroke-dashoffset: -220;
      }

      70% {
        stroke-dasharray: 20 200;
        stroke-width: 30;
        stroke-dashoffset: -225;
      }

      90% {
        stroke-dasharray: 20 200;
        stroke-width: 30;
        stroke-dashoffset: -305;
      }

      98%,
      to {
        stroke-dasharray: 0 220;
        stroke-width: 20;
        stroke-dashoffset: -330;
      }
    }

    @keyframes ringC {
      from {
        stroke-dasharray: 0 440;
        stroke-width: 20;
        stroke-dashoffset: 0;
      }

      8% {
        stroke-dasharray: 40 400;
        stroke-width: 30;
        stroke-dashoffset: -5;
      }

      28% {
        stroke-dasharray: 40 400;
        stroke-width: 30;
        stroke-dashoffset: -175;
      }

      36%,
      58% {
        stroke-dasharray: 0 440;
        stroke-width: 20;
        stroke-dashoffset: -220;
      }

      66% {
        stroke-dasharray: 40 400;
        stroke-width: 30;
        stroke-dashoffset: -225;
      }

      86% {
        stroke-dasharray: 40 400;
        stroke-width: 30;
        stroke-dashoffset: -395;
      }

      94%,
      to {
        stroke-dasharray: 0 440;
        stroke-width: 20;
        stroke-dashoffset: -440;
      }
    }

    @keyframes ringD {

      from,
      8% {
        stroke-dasharray: 0 440;
        stroke-width: 20;
        stroke-dashoffset: 0;
      }

      16% {
        stroke-dasharray: 40 400;
        stroke-width: 30;
        stroke-dashoffset: -5;
      }

      36% {
        stroke-dasharray: 40 400;
        stroke-width: 30;
        stroke-dashoffset: -175;
      }

      44%,
      50% {
        stroke-dasharray: 0 440;
        stroke-width: 20;
        stroke-dashoffset: -220;
      }

      58% {
        stroke-dasharray: 40 400;
        stroke-width: 30;
        stroke-dashoffset: -225;
      }

      78% {
        stroke-dasharray: 40 400;
        stroke-width: 30;
        stroke-dashoffset: -395;
      }

      86%,
      to {
        stroke-dasharray: 0 440;
        stroke-width: 20;
        stroke-dashoffset: -440;
      }
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">

  <main class="min-h-screen flex">
    <!-- Sidebar -->
    @include('partials.faculty-sidebar', ['active' => 'dashboard'])

    <!-- Main content -->
    <section class="flex-1 px-8 py-10">
      <!-- Title -->
      <div class="flex items-center gap-3">
        <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-green-50 text-green-600">
          <!-- Clipboard-check icon -->
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M9 5h6a2 2 0 0 1 2 2v0a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2v0a2 2 0 0 1 2-2Z" stroke-width="1.5" />
            <path d="M19 7v9a3 3 0 0 1-3 3H8a3 3 0 0 1-3-3V7" stroke-width="1.5" stroke-linecap="round" />
            <path d="M9 14l2 2 4-4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </span>
        <h1 class="text-2xl font-bold">CI Dashboard</h1>
      </div>
      <p class="text-[13px] text-slate-500 mt-1">
        Quick access to your patients, chartings, clinical guides, and teaching schedule.
      </p>

      {{-- Stat cards – SKELETON --}}
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

      <!-- Stat cards (real content) -->
      <div id="statsReal" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mt-6 hidden">
        <!-- Total patients in chartings -->
        <div class="js-dash-card rounded-2xl border border-slate-200 bg-white p-5">
          <div class="flex items-center justify-between">
            <div class="text-[13px] font-medium text-slate-700">Active Patients in Chartings</div>
            <span
              class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 px-2 py-0.5 text-[10px] font-semibold tracking-wide uppercase">
              Chartings
            </span>
          </div>
          <div class="mt-2 text-3xl font-bold text-emerald-700">
            {{ number_format($totalPatientsInChartings ?? 0) }}
          </div>
          <p class="mt-1 text-[12px] text-slate-500">Across your current units and wards.</p>
        </div>

        <!-- Active Patients -->
        <div class="js-dash-card rounded-2xl border border-slate-200 bg-white p-5">
          <div class="flex items-center justify-between">
            <div class="text-[13px] font-medium text-slate-700">Active Patients</div>
            <span
              class="inline-flex items-center rounded-full bg-sky-50 text-sky-700 border border-sky-100 px-2 py-0.5 text-[10px] font-semibold tracking-wide uppercase">
              In care
            </span>
          </div>
          <div class="mt-2 text-3xl font-bold text-sky-700">
            {{ number_format($activePatientsCount ?? 0) }}
          </div>
          <p class="mt-1 text-[12px] text-slate-500">Currently admitted and under your care.</p>
        </div>

        <!-- Discharged Patients -->
        <div class="js-dash-card rounded-2xl border border-slate-200 bg-white p-5">
          <div class="flex items-center justify-between">
            <div class="text-[13px] font-medium text-slate-700">Discharged Patients</div>
            <span
              class="inline-flex items-center rounded-full bg-amber-50 text-amber-700 border border-amber-100 px-2 py-0.5 text-[10px] font-semibold tracking-wide uppercase">
              Recent
            </span>
          </div>
          <div class="mt-2 text-3xl font-bold text-amber-700">
            {{ number_format($dischargedPatientsCount ?? 0) }}
          </div>
          <p class="mt-1 text-[12px] text-slate-500">Recently discharged with completed chartings.</p>
        </div>
      </div>


      {{-- Quick actions – SKELETON --}}
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

      <!-- Quick actions (direct to core modules) -->
      <div id="quickReal" class="mt-8 rounded-2xl border border-slate-200 bg-white p-5 js-dash-card hidden">
        <div class="flex items-center justify-between">
          <h2 class="text-[15px] font-semibold text-slate-800">Quick Actions</h2>
        </div>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
          <a href="{{ route('faculty.chartings.index') }}"
            class="js-dash-card rounded-xl border border-slate-200 px-4 py-3 text-[13px] font-medium hover:bg-slate-50 flex items-center gap-2">
            <i data-lucide="clipboard-list" class="h-4 w-4 text-emerald-600"></i>
            <span>Open Patient &amp; Task (Chartings)</span>
          </a>

          <a href="{{ route('faculty.procedures.index') }}"
            class="js-dash-card rounded-xl border border-slate-200 px-4 py-3 text-[13px] font-medium hover:bg-slate-50 flex items-center gap-2">
            <i data-lucide="stethoscope" class="h-4 w-4 text-sky-600"></i>
            <span>Open Procedure Guides</span>
          </a>

          <a href="{{ route('faculty.drug_guide.index') }}"
            class="js-dash-card rounded-xl border border-slate-200 px-4 py-3 text-[13px] font-medium hover:bg-slate-50 flex items-center gap-2">
            <i data-lucide="pill" class="h-4 w-4 text-rose-600"></i>
            <span>Open Drug Guide</span>
          </a>

          <a href="{{ route('faculty.emergency.index') }}"
            class="js-dash-card rounded-xl border border-slate-200 px-4 py-3 text-[13px] font-medium hover:bg-slate-50 flex items-center gap-2">
            <i data-lucide="alert-triangle" class="h-4 w-4 text-amber-600"></i>
            <span>Emergency Protocols</span>
          </a>

          {{-- Clinical Instructor Mode button --}}
          <button type="button" id="btnInstructorMode"
            class="js-dash-card rounded-xl border border-slate-200 px-4 py-3 text-[13px] font-medium hover:bg-slate-50 flex items-center gap-2">
            <i data-lucide="user-round" class="h-4 w-4 text-emerald-600"></i>
            <span>Clinical Instructor Mode</span>
          </button>
        </div>
      </div>


      <!-- Core modules overview -->
      <div class="mt-8">
        <div class="flex items-center justify-between">
          <h2 class="text-[15px] font-semibold text-slate-800">Core Clinical Modules</h2>
          <a href="{{ route('faculty.chartings.index') }}" class="text-[12px] text-slate-600 hover:text-slate-800">
            Go to Patient &amp; Task
          </a>
        </div>

        {{-- Core modules – SKELETON --}}
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

        <!-- Core modules – REAL content -->
        <div id="coreReal" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 hidden">
          <!-- Card 1: Patient & Task / Chartings -->
          <a href="{{ route('faculty.chartings.index') }}"
            class="js-dash-card block rounded-2xl border border-slate-200 bg-white p-5 hover:bg-slate-50">
            <div class="flex items-start justify-between">
              <div class="flex items-center gap-2">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                  <i data-lucide="clipboard-list" class="h-4 w-4"></i>
                </span>
                <div>
                  <div class="text-[13px] font-semibold text-slate-800">Patient &amp; Task</div>
                  <div class="text-[12px] text-slate-500">Chartings</div>
                </div>
              </div>
              <span
                class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 px-2.5 py-1 text-[11px] font-medium">
                {{ number_format($activePatientsCount ?? 0) }} active patients
              </span>

            </div>
            <div class="mt-3 text-[12px] text-slate-500 line-clamp-2">
              Access Nurse’s Notes, Vital Signs, I&amp;O, MAR, NCP, and other core chartings in one place.
            </div>
            <div class="mt-4 flex items-center gap-4 text-[11px] text-slate-500">
              <div class="flex items-center gap-1">
                <i data-lucide="activity" class="h-3.5 w-3.5 text-emerald-600"></i>
                <span>Latest: 1 new note today</span>
              </div>
              <div class="flex items-center gap-1">
                <i data-lucide="hospital" class="h-3.5 w-3.5 text-slate-600"></i>
                <span>Mixed wards</span>
              </div>
            </div>
          </a>

          <!-- Card 2: Procedure Guides -->
          <a href="{{ route('faculty.procedures.index') }}"
            class="js-dash-card block rounded-2xl border border-slate-200 bg-white p-5 hover:bg-slate-50">
            <div class="flex items-start justify-between">
              <div class="flex items-center gap-2">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                  <i data-lucide="stethoscope" class="h-4 w-4"></i>
                </span>
                <div>
                  <div class="text-[13px] font-semibold text-slate-800">Procedure Guides</div>
                  <div class="text-[12px] text-slate-500">Skills &amp; Checklists</div>
                </div>
              </div>
              <span
                class="inline-flex items-center rounded-full bg-sky-50 text-sky-700 border border-sky-100 px-2.5 py-1 text-[11px] font-medium">
                26 procedures
              </span>
            </div>
            <div class="mt-3 text-[12px] text-slate-500 line-clamp-2">
              Step-by-step guides with hazards, PPE, and videos to support clinical teaching and simulations.
            </div>
            <div class="mt-4 flex items-center gap-4 text-[11px] text-slate-500">
              <div class="flex items-center gap-1">
                <i data-lucide="calendar" class="h-3.5 w-3.5 text-sky-600"></i>
                <span>Recently updated this week</span>
              </div>
              <div class="flex items-center gap-1">
                <i data-lucide="check-circle-2" class="h-3.5 w-3.5 text-emerald-600"></i>
                <span>Ready for return demos</span>
              </div>
            </div>
          </a>

          <!-- Card 3: Drug & Emergency References -->
          <a href="{{ route('faculty.drug_guide.index') }}"
            class="js-dash-card block rounded-2xl border border-slate-200 bg-white p-5 hover:bg-slate-50">
            <div class="flex items-start justify-between">
              <div class="flex items-center gap-2">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                  <i data-lucide="pill" class="h-4 w-4"></i>
                </span>
                <div>
                  <div class="text-[13px] font-semibold text-slate-800">Drug &amp; Emergency</div>
                  <div class="text-[12px] text-slate-500">References</div>
                </div>
              </div>
              <span
                class="inline-flex items-center rounded-full bg-rose-50 text-rose-700 border border-rose-100 px-2.5 py-1 text-[11px] font-medium">
                MIMS-style + protocols
              </span>
            </div>
            <div class="mt-3 text-[12px] text-slate-500 line-clamp-2">
              Quickly look up drugs, check allergies, and open emergency protocols for critical scenarios.
            </div>
            <div class="mt-4 flex items-center gap-4 text-[11px] text-slate-500">
              <div class="flex items-center gap-1">
                <i data-lucide="alert-triangle" class="h-3.5 w-3.5 text-amber-600"></i>
                <span>Emergency algorithms</span>
              </div>
              <div class="flex items-center gap-1">
                <i data-lucide="book-open-check" class="h-3.5 w-3.5 text-indigo-600"></i>
                <span>Integrated with teaching</span>
              </div>
            </div>
          </a>
        </div>
      </div>
    </section>
  </main>
  <div id="instructorModeOverlay" class="fixed inset-0 z-[70] flex items-center justify-center 
            opacity-0 pointer-events-none transition-opacity duration-300" style="background:#e8e8e8;">
    <div class="flex flex-col items-center gap-4">
      <!-- SVG stays unchanged -->
      <svg viewBox="0 0 240 240" height="240" width="240" class="pl">
        <circle stroke-linecap="round" stroke-dashoffset="-330" stroke-dasharray="0 660" stroke-width="20" stroke="#000"
          fill="none" r="105" cy="120" cx="120" class="pl__ring pl__ring--a"></circle>
        <circle stroke-linecap="round" stroke-dashoffset="-110" stroke-dasharray="0 220" stroke-width="20" stroke="#000"
          fill="none" r="35" cy="120" cx="120" class="pl__ring pl__ring--b"></circle>
        <circle stroke-linecap="round" stroke-dasharray="0 440" stroke-width="20" stroke="#000" fill="none" r="70"
          cy="120" cx="85" class="pl__ring pl__ring--c"></circle>
        <circle stroke-linecap="round" stroke-dasharray="0 440" stroke-width="20" stroke="#000" fill="none" r="70"
          cy="120" cx="155" class="pl__ring pl__ring--d"></circle>
      </svg>

      <p class="text-sm text-slate-700">Switching to Instructor Mode…</p>
    </div>
  </div>


  @include('partials.faculty-footer')

  <script>
    (function () {
      const statsSkeleton = document.getElementById('statsSkeleton');
      const statsReal = document.getElementById('statsReal');
      const quickSkeleton = document.getElementById('quickSkeleton');
      const quickReal = document.getElementById('quickReal');
      const coreSkeleton = document.getElementById('coreSkeleton');
      const coreReal = document.getElementById('coreReal');

      const cards = [...document.querySelectorAll('.js-dash-card')];

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

        // Add base opacity-0 so animation can fade them in
        cards.forEach(card => {
          card.classList.add('opacity-0');
        });

        // Staggered slide-in animation (same pattern as Chartings)
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

        // Clinical Instructor Mode logic
        const instructorBtn = document.getElementById('btnInstructorMode');
        const overlay = document.getElementById('instructorModeOverlay');
        const mainLayout = document.querySelector('main');

        if (instructorBtn && overlay && mainLayout) {
          instructorBtn.addEventListener('click', () => {
            // fade out current dashboard + sidebar
            mainLayout.classList.add('transition-opacity', 'duration-300');
            mainLayout.classList.add('opacity-0', 'pointer-events-none');

            // fade in loading overlay
            overlay.classList.remove('pointer-events-none');
            overlay.classList.add('opacity-100');

            // after 1 second go to Instructor Mode
            setTimeout(() => {
              window.location.href = "{{ route('faculty.instructor-mode.index') }}";
            }, 1000);
          });
        }
      });
    })();
  </script>


  <!-- Lucide icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <script> lucide.createIcons(); </script>
</body>

</html>