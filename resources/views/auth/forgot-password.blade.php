@extends('layouts.app')

@section('title', 'Reset Password · NurSync')

@section('content')
<section
  class="bg-slate-50 py-24 sm:py-28 flex items-center"
  style="min-height: calc(100svh - var(--header-h, 56px));"
>
  <div class="mx-auto max-w-7xl px-4 sm:px-6 flex justify-center">
    <div class="mx-auto w-full max-w-md">
      <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm">

        {{-- top chevrons (decor) --}}
        <div class="mb-4 flex items-center justify-center gap-3 text-slate-800">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path d="M15 19l-7-7 7-7" />
          </svg>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path d="M9 5l7 7-7 7" />
          </svg>
        </div>

        <h1 class="text-center text-2xl font-bold text-slate-900">Reset your password</h1>
        <p class="mt-2 text-center text-slate-600">
          Enter your email address and we’ll send you instructions to reset your password.
        </p>

        {{-- success message --}}
        @if (session('status'))
          <div
            id="resetSuccessBanner"
            class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 shadow transition-all duration-500"
          >
            {{ session('status') }}
          </div>
        @endif

        {{-- errors --}}
        @if ($errors->any())
          <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">
          @csrf

          <div>
            <label for="email" class="block text-sm font-medium text-slate-700">Email address</label>
            <input
              id="email"
              name="email"
              type="email"
              required
              autocomplete="email"
              placeholder="Enter your email"
              value="{{ old('email') }}"
              class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder:text-slate-400
                     focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
            >
          </div>

          <button type="submit" class="w-full btn-black">
            Send Reset Link
          </button>
        </form>

        <div class="mt-6 text-center">
          <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M15 18l-6-6 6-6" />
            </svg>
            Back to login
          </a>
        </div>

      </div>
    </div>
  </div>
</section>

{{-- Slide-out animation --}}
@if (session('status'))
  <script>
    (function () {
      const banner = document.getElementById('resetSuccessBanner');
      if (!banner) return;

      // Give the user time to read it
      setTimeout(() => {
        banner.classList.add('animate-slide-out-top');
      }, 2000);

      // After animation, hide it completely
      banner.addEventListener('animationend', () => {
        banner.classList.add('hidden');
      });
    })();
  </script>
@endif
@endsection
