<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Instructor Dashboard · NurSync</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body { font-family:'Poppins', ui-sans-serif, system-ui, sans-serif; }

    @keyframes slide-in-up {
      from { transform: translateY(10px); opacity: 0; }
      to   { transform: translateY(0); opacity: 1; }
    }
    .animate-card-in {
      animation: slide-in-up .35s ease-out both;
      will-change: transform, opacity;
    }

    /* Loader */
    .pl { width:6em; height:6em; }
    .pl__ring { animation: ringA 2s linear infinite; }
    .pl__ring--a { stroke:#000 }
    .pl__ring--b { animation-name:ringB; stroke:#7e7e7e }
    .pl__ring--c { animation-name:ringC; stroke:#686868 }
    .pl__ring--d { animation-name:ringD; stroke:#000 }

    /* Animations A/B/C/D remain unchanged */
    @keyframes ringA {
      from,4% {stroke-dasharray:0 660; stroke-width:20; stroke-dashoffset:-330;}
      12% {stroke-dasharray:60 600; stroke-width:30; stroke-dashoffset:-335;}
      32% {stroke-dasharray:60 600; stroke-width:30; stroke-dashoffset:-595;}
      40%,54% {stroke-dasharray:0 660; stroke-width:20; stroke-dashoffset:-660;}
      62% {stroke-dasharray:60 600; stroke-width:30; stroke-dashoffset:-665;}
      82% {stroke-dasharray:60 600; stroke-width:30; stroke-dashoffset:-925;}
      90%,to {stroke-dasharray:0 660; stroke-width:20; stroke-dashoffset:-990;}
    }
    @keyframes ringB {
      from,12% {stroke-dasharray:0 220; stroke-width:20; stroke-dashoffset:-110;}
      20% {stroke-dasharray:20 200; stroke-width:30; stroke-dashoffset:-115;}
      40% {stroke-dasharray:20 200; stroke-width:30; stroke-dashoffset:-195;}
      48%,62% {stroke-dasharray:0 220; stroke-width:20; stroke-dashoffset:-220;}
      70% {stroke-dasharray:20 200; stroke-width:30; stroke-dashoffset:-225;}
      90% {stroke-dasharray:20 200; stroke-width:30; stroke-dashoffset:-305;}
      98%,to {stroke-dasharray:0 220; stroke-width:20; stroke-dashoffset:-330;}
    }
    @keyframes ringC {
      from {stroke-dasharray:0 440; stroke-width:20; stroke-dashoffset:0;}
      8% {stroke-dasharray:40 400; stroke-width:30; stroke-dashoffset:-5;}
      28% {stroke-dasharray:40 400; stroke-width:30; stroke-dashoffset:-175;}
      36%,58% {stroke-dasharray:0 440; stroke-width:20; stroke-dashoffset:-220;}
      66% {stroke-dasharray:40 400; stroke-width:30; stroke-dashoffset:-225;}
      86% {stroke-dasharray:40 400; stroke-width:30; stroke-dashoffset:-395;}
      94%,to {stroke-dasharray:0 440; stroke-width:20; stroke-dashoffset:-440;}
    }
    @keyframes ringD {
      from,8% {stroke-dasharray:0 440; stroke-width:20; stroke-dashoffset:0;}
      16% {stroke-dasharray:40 400; stroke-width:30; stroke-dashoffset:-5;}
      36% {stroke-dasharray:40 400; stroke-width:30; stroke-dashoffset:-175;}
      44%,50% {stroke-dasharray:0 440; stroke-width:20; stroke-dashoffset:-220;}
      58% {stroke-dasharray:40 400; stroke-width:30; stroke-dashoffset:-225;}
      78% {stroke-dasharray:40 400; stroke-width:30; stroke-dashoffset:-395;}
      86%,to {stroke-dasharray:0 440; stroke-width:20; stroke-dashoffset:-440;}
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">

<main class="min-h-screen flex">

  {{-- ⭐ Instructor Sidebar (Dashboard as active) --}}
  @include('partials.instructor-sidebar', ['active' => 'dashboard'])

  <section class="flex-1 px-8 py-10">

    <!-- Title -->
    <div class="flex items-center gap-3">
      <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-emerald-50 text-emerald-600">
        <i data-lucide="graduation-cap" class="h-5 w-5"></i>
      </span>
      <h1 class="text-2xl font-bold">Instructor Dashboard</h1>
    </div>

    <p class="text-[13px] text-slate-500 mt-1">
      Training resources, assessment tools, and student nurse development.
    </p>

    {{-- Stat cards – skeleton --}}
    <div id="statsSkeleton" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mt-6">
      @for($i=0;$i<3;$i++)
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
          <div class="animate-pulse space-y-3">
            <div class="h-3 w-32 bg-slate-200 rounded"></div>
            <div class="h-7 w-16 bg-slate-100 rounded mt-2"></div>
            <div class="h-3 w-40 bg-slate-100 rounded mt-2"></div>
          </div>
        </div>
      @endfor
    </div>

    {{-- Stat cards – real --}}
    <div id="statsReal" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mt-6 hidden">
      <div class="js-dash-card rounded-2xl border border-slate-200 bg-white p-5">
        <div class="text-[13px] font-medium text-slate-700">Assessment Guides</div>
        <div class="mt-2 text-3xl font-bold text-emerald-700">12</div>
        <p class="text-[12px] text-slate-500 mt-1">Available for student training.</p>
      </div>

      <div class="js-dash-card rounded-2xl border border-slate-200 bg-white p-5">
        <div class="text-[13px] font-medium text-slate-700">Skills Checklists</div>
        <div class="mt-2 text-3xl font-bold text-sky-700">18</div>
        <p class="text-[12px] text-slate-500 mt-1">Core nursing competencies.</p>
      </div>

      <div class="js-dash-card rounded-2xl border border-slate-200 bg-white p-5">
        <div class="text-[13px] font-medium text-slate-700">Career Resources</div>
        <div class="mt-2 text-3xl font-bold text-amber-700">49</div>
        <p class="text-[12px] text-slate-500 mt-1">Roles & growth pathways.</p>
      </div>
    </div>

<div id="quickReal" class="mt-8 rounded-2xl border border-slate-200 bg-white p-5 js-dash-card hidden">
  <h2 class="text-[15px] font-semibold text-slate-800">Quick Actions</h2>

  <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">

    <a href="#"
       class="js-dash-card rounded-xl border border-slate-200 px-4 py-3 text-[13px] font-medium hover:bg-slate-50 flex items-center gap-2">
      <i data-lucide="clipboard-list" class="h-4 w-4 text-emerald-600"></i>
      <span>Assessment Guides</span>
    </a>

    <a href="#"
       class="js-dash-card rounded-xl border border-slate-200 px-4 py-3 text-[13px] font-medium hover:bg-slate-50 flex items-center gap-2">
      <i data-lucide="check-square" class="h-4 w-4 text-sky-600"></i>
      <span>Skill Mastery</span>
    </a>

    <a href="#"
       class="js-dash-card rounded-xl border border-slate-200 px-4 py-3 text-[13px] font-medium hover:bg-slate-50 flex items-center gap-2">
      <i data-lucide="map" class="h-4 w-4 text-indigo-600"></i>
      <span>Ward Orientation</span>
    </a>

    <button id="btnNurseMode"
      class="js-dash-card rounded-xl border border-slate-200 px-4 py-3 text-[13px] font-medium hover:bg-slate-50 flex items-center gap-2">
      <i data-lucide="user-round" class="h-4 w-4 text-emerald-600"></i>
      <span>Nurse Mode</span>
    </button>

  </div>
</div>


  </section>
</main>

{{-- Full-screen loader when switching back to Nurse Mode --}}
<div id="instructorModeOverlay"
     class="fixed inset-0 z-[70] flex items-center justify-center 
            opacity-0 pointer-events-none transition-opacity duration-300"
     style="background:#e8e8e8;">
  <div class="flex flex-col items-center gap-4">
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

    <p class="text-sm text-slate-700">Switching to Nurse Mode…</p>
  </div>
</div>

<script>
  (function () {
    const statsSkeleton = document.getElementById('statsSkeleton');
    const statsReal     = document.getElementById('statsReal');
    const quickReal     = document.getElementById('quickReal');
    const cards         = [...document.querySelectorAll('.js-dash-card')];

    function revealAndAnimate() {
      if (statsSkeleton && statsReal) {
        statsSkeleton.classList.add('hidden');
        statsReal.classList.remove('hidden');
      }
      if (quickReal) {
        quickReal.classList.remove('hidden');
      }

      cards.forEach(card => card.classList.add('opacity-0'));

      cards.forEach((card, idx) => {
        card.style.animationDelay = `${Math.min(idx, 6) * 40}ms`;
        requestAnimationFrame(() => {
          card.classList.add('animate-card-in');
          card.classList.remove('opacity-0');
        });
      });
    }

    document.addEventListener('DOMContentLoaded', () => {
      const prefersReduced =
        window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      const delay = prefersReduced ? 0 : 220;

      setTimeout(revealAndAnimate, delay);

      // Nurse Mode switch button
      const nurseBtn = document.getElementById('btnNurseMode');
      const overlay  = document.getElementById('instructorModeOverlay');
      const main     = document.querySelector('main');

      if (nurseBtn && overlay && main) {
        nurseBtn.addEventListener('click', () => {
          main.classList.add('opacity-0', 'pointer-events-none', 'transition-opacity', 'duration-300');

          overlay.classList.remove('pointer-events-none');
          overlay.classList.add('opacity-100');

          setTimeout(() => {
            window.location.href = "{{ route('faculty.dashboard') }}";
          }, 1000);
        });
      }
    });
  })();
</script>

<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>

</body>
</html>
