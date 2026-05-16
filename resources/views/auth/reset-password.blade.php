@extends('layouts.app')

@section('title', 'Create New Password · NurSync')

@section('content')
<section
  class="bg-slate-50 py-24 sm:py-28 flex items-center"
  style="min-height: calc(100svh - var(--header-h, 56px));"
>
  <div class="mx-auto max-w-7xl px-4 sm:px-6 flex justify-center">
    <div class="mx-auto w-full max-w-md animate-fade-in">

      <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm">

        {{-- Decorative top icons --}}
        <div class="mb-4 flex items-center justify-center gap-3 text-slate-800 opacity-80">
          <i data-lucide="key-round" class="h-6 w-6"></i>
          <i data-lucide="shield-check" class="h-6 w-6"></i>
        </div>

        <h1 class="text-center text-2xl font-bold text-slate-900">Create a New Password</h1>
        <p class="mt-2 text-center text-slate-600">
          Enter a strong password below to secure your account.
        </p>

        {{-- Errors --}}
        @if ($errors->any())
          <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-5">
          @csrf

          <input type="hidden" name="token" value="{{ $token }}">
          <input type="hidden" name="email" value="{{ old('email', $email) }}">

          <div>
            <label class="block text-sm font-medium text-slate-700">Email</label>
            <div class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900">
              {{ $email }}
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-slate-700">
              New Password
            </label>
            <input
              id="password"
              name="password"
              type="password"
              required
              autocomplete="new-password"
              class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3
                     focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
            >
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700">
              Confirm Password
            </label>
            <input
              id="password_confirmation"
              name="password_confirmation"
              type="password"
              required
              class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3
                     focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
            >
          </div>

          <button type="submit" class="w-full btn-black">
            Update Password
          </button>
        </form>

        <div class="mt-6 text-center">
          <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            Back to login
          </a>
        </div>

      </div>
    </div>
  </div>
</section>

{{-- Simple fade-in animation --}}
<style>
@keyframes fade-in {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fade-in .5s ease-out;
}
</style>
@endsection
