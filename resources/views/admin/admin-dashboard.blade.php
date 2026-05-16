<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Admin Dashboard · NurSync</title>

  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }

    /* Smooth card entrance (same pattern as CI/Student dashboards) */
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
    @include('partials.admin-sidebar', ['active' => 'dashboard'])

    {{-- Main contents --}}
    @include('partials.admin-dashboard-main')
  </main>

  {{-- Shared footer --}}
  @include('partials.admin-footer')

  {{-- Dashboard reveal + animation logic --}}
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

        // Staggered slide-in animation
        cards.forEach((card, idx) => {
          card.style.animationDelay = `${Math.min(idx, 6) * 40}ms`;
          requestAnimationFrame(() => {
            card.classList.add('animate-card-in');
            card.classList.remove('opacity-0');
          });
        });
      }

      document.addEventListener('DOMContentLoaded', () => {
        const prefersReduced = window.matchMedia &&
          window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const delay = prefersReduced ? 0 : 220;
        setTimeout(revealAndAnimate, delay);
      });
    })();
  </script>

  <script src="https://unpkg.com/lucide@latest"></script>
  <script> lucide.createIcons(); </script>
</body>
</html>
