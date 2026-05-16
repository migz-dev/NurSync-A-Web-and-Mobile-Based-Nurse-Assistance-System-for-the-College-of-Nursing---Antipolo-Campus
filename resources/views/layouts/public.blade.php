{{-- resources/views/layouts/public.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>@yield('title', 'NurSync')</title>

  @php $manifest = public_path('build/manifest.json'); @endphp
  @if (app()->environment('local') || file_exists($manifest))
    @vite(['resources/css/app.css','resources/js/app.js'])
  @endif

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }
  </style>
</head>
<body class="min-h-screen bg-slate-50">

  {{-- Header bar (should use <header class="sticky ..."> inside this partial) --}}
  @include('partials.header')

  {{-- Page root: ONLY this part is swapped by app.js --}}
  <div id="page-root">
    <main>
      @yield('content')
    </main>
  </div>

  {{-- Green marketing footer --}}
  @include('partials.footer')

  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    try { lucide.createIcons(); } catch (e) {}
  </script>
</body>
</html>
