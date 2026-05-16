{{-- resources/views/auth/faculty-login.blade.php --}}
@extends('layouts.app')

@section('title', 'Faculty Sign in · NurSync')

@section('content')
<main class="min-h-screen grid lg:grid-cols-2">
  {{-- Left: Quote panel --}}
  <section class="relative hidden lg:flex items-center justify-center bg-gradient-to-b from-slate-50 to-white">
    <div class="absolute inset-0 pointer-events-none bg-grid-slate opacity-60"></div>

    <div
      class="relative max-w-xl px-10"
      data-anim="quote"
      style="opacity:0; transform:translateY(20px);"
    >
      <div class="flex items-center gap-6 mb-8 text-slate-400">
        <button type="button" class="size-9 grid place-items-center rounded-full border border-slate-300 hover:bg-slate-50 transition" aria-hidden="true" tabindex="-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
        <button type="button" class="size-9 grid place-items-center rounded-full border border-slate-300 hover:bg-slate-50 transition" aria-hidden="true" tabindex="-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 5l7 7-7 7"/>
          </svg>
        </button>
      </div>

      <blockquote class="text-2xl font-semibold leading-snug text-slate-800">
        “Nursing is more than a career, it's a noble calling that touches the lives of people at their most vulnerable.”
      </blockquote>
      <p class="mt-4 text-slate-500">— Elizabeth Kenny</p>
    </div>
  </section>

  {{-- Right: Form --}}
  <section class="flex items-center">
    <div
      class="w-full max-w-md mx-auto px-6 py-12"
      data-anim="panel"
      style="opacity:0; transform:translateX(20px);"
    >
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold">CI sign in</h1>
        <p class="mt-2 text-slate-500">Access your classes and schedules</p>
      </div>

      {{-- Friendly banner for pending/rejected coming from controller --}}
      @if ($errors->has('email'))
        @php $msg = $errors->first('email'); @endphp
        <div class="mb-4 rounded-lg border px-4 py-3 text-sm
                    {{ str_contains(strtolower($msg), 'rejected') ? 'border-rose-200 bg-rose-50 text-rose-800' : 'border-amber-200 bg-amber-50 text-amber-800' }}">
          {{ $msg }}
        </div>
      @endif

      {{-- Top-level validation list (other errors) --}}
      @if ($errors->any() && !$errors->has('email'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
          <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- NOTE: action matches faculty login route --}}
      <form method="POST" action="{{ url('/faculty/login') }}" class="space-y-5" novalidate>
        @csrf

        {{-- Email --}}
        <div>
          <label for="email" class="block text-sm font-medium text-slate-700">Email address</label>
          <input
            id="email"
            name="email"
            type="email"
            inputmode="email"
            autocomplete="email"
            required
            value="{{ old('email') }}"
            @class([
              'mt-2 w-full rounded-xl border bg-white px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2',
              'border-slate-300 focus:border-slate-400 focus:ring-slate-200' => !$errors->has('email'),
              'border-red-300 focus:border-red-400 focus:ring-red-100' => $errors->has('email')
            ])
            aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
            aria-describedby="{{ $errors->has('email') ? 'email-error' : '' }}"
            autofocus
          >
          @error('email')
            <p id="email-error" class="mt-2 text-xs {{ str_contains(strtolower($message),'rejected') ? 'text-rose-600' : 'text-amber-700' }}">
              {{ $message }}
            </p>
          @enderror
        </div>

        {{-- Password --}}
        <div>
          <div class="flex items-center justify-between">
            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
            <a href="{{ url('/forgot-password') }}" class="text-sm text-slate-600 hover:text-slate-900">Forgot password?</a>
          </div>
          <input
            id="password"
            name="password"
            type="password"
            autocomplete="current-password"
            required
            @class([
              'mt-2 w-full rounded-xl border bg-white px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2',
              'border-slate-300 focus:border-slate-400 focus:ring-slate-200' => !$errors->has('password'),
              'border-red-300 focus:border-red-400 focus:ring-red-100' => $errors->has('password')
            ])
            aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
            aria-describedby="{{ $errors->has('password') ? 'password-error' : '' }}"
          >
          @error('password')
            <p id="password-error" class="mt-2 text-xs text-red-600">{{ $message }}</p>
          @enderror
        </div>

        {{-- Remember me --}}
        <div class="flex items-center justify-between">
          <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input
              type="checkbox"
              name="remember"
              class="size-4 rounded border-slate-300 text-slate-900 focus:ring-slate-300"
              {{ old('remember') ? 'checked' : '' }}
            >
            Remember me
          </label>
        </div>

        {{-- Primary submit --}}
        <button type="submit" class="w-full btn-black">Sign in</button>

        {{-- Small note to set expectations --}}
        <p class="mt-2 text-center text-xs text-slate-500">
          Note: Faculty accounts require admin approval before you can sign in.
        </p>

        {{-- Divider --}}
        <div class="relative my-3 text-center">
          <span class="inline-block bg-white px-3 text-slate-400 text-sm">Don’t have an account?</span>
          <div class="absolute inset-x-0 top-1/2 -z-10 h-px bg-slate-200"></div>
        </div>

        {{-- Secondary CTA --}}
        <a href="{{ url('/faculty/register') }}" class="w-full btn-outline justify-center">Create an account →</a>
      </form>

      {{-- Quick access icons (Admin + Student) --}}
      <div class="mt-6">
        <div class="text-center text-xs uppercase tracking-wide text-slate-400">Quick access</div>
        <div class="mt-3 flex items-center justify-center gap-4">

          {{-- Admin --}}
          <a href="{{ url('/admin/login') }}"
            class="group flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 shadow-sm hover:shadow transition"
            aria-label="Admin">
            <span class="grid place-items-center rounded-lg bg-[#00a63e]/10 p-2 group-hover:bg-[#00a63e]/15 transition">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#00a63e]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 3l7 4v5c0 4.7-3.2 8.8-7 9.8-3.8-1-7-5.1-7-9.8V7l7-4z"/>
                <path d="M9 12l2 2 4-4"/>
              </svg>
            </span>
            <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900">Administrator</span>
          </a>

          {{-- Student --}}
          <a href="{{ url('/login') }}"
            class="group flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 shadow-sm hover:shadow transition"
            aria-label="Student">
            <span class="grid place-items-center rounded-lg bg-[#00a63e]/10 p-2 group-hover:bg-[#00a63e]/15 transition">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#00a63e]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 10L12 5 2 10l10 5 10-5z"/>
                <path d="M6 12v4a4 4 0 0 0 8 0v-4"/>
              </svg>
            </span>
            <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900">Student Nurse</span>
          </a>

        </div>
      </div>

    </div>
  </section>
</main>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const quote = document.querySelector('[data-anim="quote"]');
    const panel = document.querySelector('[data-anim="panel"]');

    // Let layout render first, then animate
    window.requestAnimationFrame(function () {
      if (quote) {
        quote.style.transition = 'opacity 600ms ease, transform 600ms ease';
        quote.style.opacity = '1';
        quote.style.transform = 'translateY(0)';
      }

      if (panel) {
        panel.style.transition = 'opacity 600ms ease, transform 600ms ease';
        panel.style.opacity = '1';
        panel.style.transform = 'translateX(0)';
      }
    });
  });
</script>
@endpush
