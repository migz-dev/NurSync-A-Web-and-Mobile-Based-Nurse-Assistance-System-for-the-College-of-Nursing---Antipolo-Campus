@php
  $onHome   = request()->routeIs('home');
  $howHref   = $onHome ? '#how'   : route('home').'#how';
  $aboutHref = $onHome ? '#about' : route('home').'#about';
@endphp

<header class="sticky top-0 z-50 w-full bg-white/90 backdrop-blur border-b border-slate-200 transition-all duration-300">
  <div class="mx-auto max-w-7xl px-4 sm:px-6">
    <div class="h-14 flex items-center justify-between">
      <a href="{{ route('home') }}" class="flex items-center gap-2">
        <img src="/assets/images/CON_LOGO.png" class="h-7 w-7 rounded-md" alt="NurSync">
        <span class="font-semibold text-sm tracking-tight">NurSync</span>
      </a>

      <nav class="flex items-center gap-6 text-sm">
        <a href="{{ $howHref }}" class="nav-link-light nav-link-underline">
          How It Works
        </a>
        <a href="{{ $aboutHref }}" class="nav-link-light nav-link-underline">
          About
        </a>
        <a href="{{ url('/faculty/login') }}" class="nav-link-light nav-link-underline">
          Log in
        </a>
        <a href="{{ url('/faculty/register') }}" class="btn-black !py-2 !px-4 hover-lift">
          Get Started
        </a>
      </nav>
    </div>
  </div>
</header>
