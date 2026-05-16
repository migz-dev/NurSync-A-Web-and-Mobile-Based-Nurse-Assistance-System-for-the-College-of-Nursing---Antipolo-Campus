{{-- resources/views/layouts/app.blade.php --}}
@php
  // optional flag your layout seems to use
  $isAuth = $isAuth ?? false;

  // safe Vite include (works in dev or when built)
  $manifest = public_path('build/manifest.json');
@endphp

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>@yield('title', 'NurSync')</title>

  @if (app()->environment('local') || file_exists($manifest))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  @endif

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif
    }
  </style>

  {{-- Optional per-page head stack --}}
  @stack('head')
</head>

<body class="min-h-screen bg-slate-50">

  {{-- ✅ Header always visible --}}
  @include('partials.header')

  <main id="page-root" class="{{ $isAuth ? 'auth-root' : 'min-h-[60vh]' }}">
    @yield('content')
  </main>

  {{-- ✅ Footer always visible --}}
  @include('partials.footer')

  <script src="https://unpkg.com/lucide@latest"></script>
  <script>try { lucide.createIcons() } catch (e) { }</script>

  {{-- ✅ Page-level scripts (e.g. login animations) --}}
  @stack('scripts')
</body>

</html>
