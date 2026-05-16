@extends('layouts.app')

@section('title', 'Create Account · NurSync')

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
      <blockquote class="text-2xl font-semibold leading-snug text-slate-800">
        “Education is not the filling of a pail, but the lighting of a fire.”
      </blockquote>
      <p class="mt-4 text-slate-500">— William Butler Yeats</p>
    </div>
  </section>

  {{-- Right: Registration Wizard --}}
  <section class="flex items-center">
    <div
      class="w-full max-w-md mx-auto px-6 py-12"
      data-anim="panel"
      style="opacity:0; transform:translateX(20px);"
    >
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold">Create your account</h1>
        <p class="mt-2 text-slate-500">Sign up to start using NurSync</p>
      </div>

      {{-- Top-level errors --}}
      @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
          <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- Stepper --}}
      <div class="mb-6 flex items-center justify-center gap-3 text-sm">
        <div id="stepDot1" class="h-2.5 w-2.5 rounded-full bg-slate-900 transition-all"></div>
        <span>Account</span>
        <div class="h-px w-10 bg-slate-300"></div>
        <div id="stepDot2" class="h-2.5 w-2.5 rounded-full bg-slate-300 transition-all"></div>
        <span>Reg Card</span>
      </div>

      <form id="registerForm"
            method="POST"
            action="{{ url('/register') }}"
            class="space-y-5 overflow-hidden"
            enctype="multipart/form-data"
            novalidate>
        @csrf

        {{-- Panes wrapper (for slide transition) --}}
        <div class="relative">
          <div id="paneTrack" class="flex transition-transform duration-300 ease-out" style="will-change: transform;">
            {{-- STEP 1: Account --}}
            <div class="min-w-full pr-1">
              {{-- Full name --}}
              <div>
                <label for="full_name" class="block text-sm font-medium text-slate-700">Full name</label>
                <input
                  id="full_name" name="full_name" type="text" autocomplete="name" required
                  value="{{ old('full_name') }}"
                  @class([
                    'mt-2 w-full rounded-xl border bg-white px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2',
                    'border-slate-300 focus:border-slate-400 focus:ring-slate-200' => !$errors->has('full_name'),
                    'border-red-300 focus:border-red-400 focus:ring-red-100' => $errors->has('full_name')
                  ])
                  placeholder="First M. Last"
                  aria-invalid="{{ $errors->has('full_name') ? 'true' : 'false' }}"
                  aria-describedby="{{ $errors->has('full_name') ? 'full_name-error' : '' }}">
                @error('full_name')
                  <p id="full_name-error" class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
              </div>

              {{-- Email --}}
              <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Email address</label>
                <input
                  id="email" name="email" type="email" inputmode="email" autocomplete="email" required
                  value="{{ old('email') }}"
                  @class([
                    'mt-2 w-full rounded-xl border bg-white px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2',
                    'border-slate-300 focus:border-slate-400 focus:ring-slate-200' => !$errors->has('email'),
                    'border-red-300 focus:border-red-400 focus:ring-red-100' => $errors->has('email')
                  ])
                  placeholder="you@example.com"
                  aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                  aria-describedby="{{ $errors->has('email') ? 'email-error' : '' }}">
                @error('email')
                  <p id="email-error" class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
              </div>

              {{-- Student Number --}}
              <div>
                <label for="student_no" class="block text-sm font-medium text-slate-700">Student Number</label>
                <input
                  id="student_no" name="student_no" type="text" autocomplete="off" required
                  value="{{ old('student_no') }}"
                  @class([
                    'mt-2 w-full rounded-xl border bg-white px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2',
                    'border-slate-300 focus:border-slate-400 focus:ring-slate-200' => !$errors->has('student_no'),
                    'border-red-300 focus:border-red-400 focus:ring-red-100' => $errors->has('student_no')
                  ])
                  placeholder="e.g., 03240008440"
                  aria-invalid="{{ $errors->has('student_no') ? 'true' : 'false' }}"
                  aria-describedby="{{ $errors->has('student_no') ? 'student_no-error' : '' }}">
                @error('student_no')
                  <p id="student_no-error" class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
              </div>

              {{-- Password --}}
              <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                <input
                  id="password" name="password" type="password" autocomplete="new-password" required
                  @class([
                    'mt-2 w-full rounded-xl border bg-white px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2',
                    'border-slate-300 focus:border-slate-400 focus:ring-slate-200' => !$errors->has('password'),
                    'border-red-300 focus:border-red-400 focus:ring-red-100' => $errors->has('password')
                  ])
                  placeholder="Enter a strong password"
                  aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                  aria-describedby="{{ $errors->has('password') ? 'password-error' : '' }}">
                @error('password')
                  <p id="password-error" class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
              </div>

              {{-- Confirm Password --}}
              <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm Password</label>
                <input
                  id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                  class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                  placeholder="Re-enter your password">
              </div>
            </div>

            {{-- STEP 2: Reg Card --}}
            <div class="min-w-full pl-1">
              <div>
                <label for="regcard_file" class="block text-sm font-medium text-slate-700">
                  Upload Registration Card (PDF/JPG/PNG, max 8MB)
                </label>
                <input
                  id="regcard_file" name="regcard_file" type="file" accept=".pdf,.jpg,.jpeg,.png"
                  class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                  {{-- make required on step 2 only via JS --}}
                >
                <p class="mt-2 text-xs text-slate-500">
                  Make sure your Student ID, Name, Year Level, and AY/Semester are clearly visible.
                </p>

                {{-- Preview (for images) --}}
                <img id="regcardPreview" alt="" class="mt-3 hidden w-full max-h-64 rounded-lg object-contain border border-slate-200" />
              </div>
            </div>
          </div>
        </div>

        {{-- Wizard footer --}}
        <div class="pt-2">
          <div class="flex items-center justify-between">
            <button type="button" id="btnBack"
                    class="hidden px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50">
              Back
            </button>

            <button type="button" id="btnNext"
                    class="ml-auto px-4 py-2 rounded-xl bg-slate-900 text-white hover:bg-black">
              Next
            </button>

            <button type="submit" id="btnSubmit"
                    class="hidden ml-auto px-4 py-2 rounded-xl bg-slate-900 text-white hover:bg-black">
              Create account
            </button>
          </div>

          {{-- Already registered --}}
          <p class="mt-6 text-center text-sm text-slate-600">
            Already have an account?
            <a href="{{ url('/login') }}" class="font-medium text-slate-900 hover:underline">Sign in</a>
          </p>
        </div>
      </form>
    </div>
  </section>
</main>
@endsection

@push('scripts')
<script>
  (function () {
    // ----------------------------------------
    // Entrance animation (CodeCred-style)
    // ----------------------------------------
    function playAuthAnimations() {
      const quote = document.querySelector('[data-anim="quote"]');
      const panel = document.querySelector('[data-anim="panel"]');

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
    }

    // ----------------------------------------
    // Tiny wizard script (no dependencies)
    // ----------------------------------------
    function initRegisterWizard() {
      const track     = document.getElementById('paneTrack');
      const btnNext   = document.getElementById('btnNext');
      const btnBack   = document.getElementById('btnBack');
      const btnSubmit = document.getElementById('btnSubmit');
      const stepDot1  = document.getElementById('stepDot1');
      const stepDot2  = document.getElementById('stepDot2');
      const preview   = document.getElementById('regcardPreview');
      const fileInput = document.getElementById('regcard_file');

      if (!track || !btnNext || !btnBack || !btnSubmit || !stepDot1 || !stepDot2 || !fileInput) {
        return;
      }

      let step = 1;

      function go(stepNum) {
        step = stepNum;
        const offset = (step - 1) * -100;
        track.style.transform = `translateX(${offset}%)`;

        // dots
        stepDot1.classList.toggle('bg-slate-900', step === 1);
        stepDot1.classList.toggle('bg-slate-300', step !== 1);
        stepDot2.classList.toggle('bg-slate-900', step === 2);
        stepDot2.classList.toggle('bg-slate-300', step !== 2);

        // buttons
        btnBack.classList.toggle('hidden', step === 1);
        btnNext.classList.toggle('hidden', step === 2);
        btnSubmit.classList.toggle('hidden', step === 1);

        // required toggling
        if (step === 2) {
          fileInput.setAttribute('required', 'required');
        } else {
          fileInput.removeAttribute('required');
        }
      }

      function step1Valid() {
        const need = ['full_name','email','student_no','password','password_confirmation'];
        for (const id of need) {
          const el = document.getElementById(id);
          if (!el || !el.value.trim()) return false;
        }
        return true;
      }

      btnNext.addEventListener('click', () => {
        if (!step1Valid()) {
          // simple nudge; server-side validation still runs
          alert('Please complete all fields on Step 1.');
          return;
        }
        go(2);
      });

      btnBack.addEventListener('click', () => go(1));

      // quick image preview (if jpg/png)
      fileInput.addEventListener('change', (e) => {
        const f = e.target.files && e.target.files[0];
        if (!f) {
          preview.src = '';
          preview.classList.add('hidden');
          return;
        }
        if (/image\/(png|jpe?g)/i.test(f.type)) {
          const url = URL.createObjectURL(f);
          preview.src = url;
          preview.classList.remove('hidden');
        } else {
          preview.src = '';
          preview.classList.add('hidden');
        }
      });

      // If there were validation errors from server and file was missing,
      // open Step 2 automatically so user sees the file field.
      @if ($errors->has('regcard_file'))
        go(2);
      @else
        go(1);
      @endif
    }

    // ----------------------------------------
    // Run when DOM is ready (handles first load + fast nav)
    // ----------------------------------------
    function onReady() {
      playAuthAnimations();
      initRegisterWizard();
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', onReady);
    } else {
      onReady();
    }

    // Also handle back/forward cache (Safari/Chrome bfcache)
    window.addEventListener('pageshow', function (event) {
      if (event.persisted) {
        playAuthAnimations();
      }
    });
  })();
</script>
@endpush

