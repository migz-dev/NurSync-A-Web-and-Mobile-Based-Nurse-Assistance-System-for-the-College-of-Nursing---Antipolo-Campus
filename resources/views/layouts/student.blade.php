{{-- resources/views/layouts/student.blade.php --}}
@php
  // Page title helper
  $pageTitle = trim($__env->yieldContent('title') ?? 'Dashboard');
  $active = $active ?? ''; // allow pages to pass ['active' => 'simulations'] etc.
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'NurSync') }} — {{ $pageTitle }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-900 antialiased">

  <div class="min-h-screen flex">
    {{-- Sidebar --}}
    @include('partials.sidebar', ['active' => $active])

    {{-- Main content --}}
    <main class="flex-1">
      {{-- Topbar --}}
      <header class="border-b border-slate-200/70 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
          <h1 class="text-[15px] sm:text-base font-semibold text-slate-900">
            @yield('title', 'Student Dashboard')
          </h1>
          <div class="flex items-center gap-3">
            {{-- room for breadcrumbs / actions --}}
          </div>
        </div>
      </header>

      {{-- Flash messages --}}
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        @if(session('ok'))
          <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm">
            {{ session('ok') }}
          </div>
        @endif
        @if(session('error'))
          <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
            {{ session('error') }}
          </div>
        @endif
      </div>

      {{-- Page body --}}
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-10">
        @yield('content')
      </div>

      {{-- Footer (optional) --}}

    </main>
  </div>
  @include('partials.student-footer')


  {{-- Icon hydrate (lucide) --}}
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide?.createIcons) lucide.createIcons();
    });
  </script>
</body>

</html>