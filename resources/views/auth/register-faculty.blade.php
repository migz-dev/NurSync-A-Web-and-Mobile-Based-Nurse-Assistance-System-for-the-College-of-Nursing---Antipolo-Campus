{{-- resources/views/auth/register-faculty.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Faculty Account · NurSync')

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
        <h1 class="text-3xl font-bold">Create your CI account</h1>
        <p class="mt-2 text-slate-500">Join as a Clinical Instructor to guide skills training, coordinate demonstrations, and lead hands-on nursing practice</p>
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
        <span>Faculty ID</span>
      </div>

      <form id="registerFacultyForm"
            method="POST"
            action="{{ url('/faculty/register') }}"
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
                  placeholder="you@school.edu"
                  aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                  aria-describedby="{{ $errors->has('email') ? 'email-error' : '' }}">
                @error('email')
                  <p id="email-error" class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
              </div>

              {{-- Faculty ID (text) --}}
              <div>
                <label for="faculty_id" class="block text-sm font-medium text-slate-700">Faculty ID</label>
                <input
                  id="faculty_id" name="faculty_id" type="text" autocomplete="off" required
                  value="{{ old('faculty_id') }}"
                  @class([
                    'mt-2 w-full rounded-xl border bg-white px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2',
                    'border-slate-300 focus:border-slate-400 focus:ring-slate-200' => !$errors->has('faculty_id'),
                    'border-red-300 focus:border-red-400 focus:ring-red-100' => $errors->has('faculty_id')
                  ])
                  placeholder="e.g., FAC-2025-001"
                  aria-invalid="{{ $errors->has('faculty_id') ? 'true' : 'false' }}"
                  aria-describedby="{{ $errors->has('faculty_id') ? 'faculty_id-error' : '' }}">
                @error('faculty_id')
                  <p id="faculty_id-error" class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
              </div>

{{-- Nurse Type --}}
<div>
  <label for="nurse_type" class="block text-sm font-medium text-slate-700">Nurse type</label>
  <select
    id="nurse_type" name="nurse_type" required
    @class([
      // ✅ same base styles as other inputs
      'mt-2 w-full rounded-xl border bg-white px-4 py-3 text-slate-900 focus:outline-none focus:ring-2',
      'border-slate-300 focus:border-slate-400 focus:ring-slate-200' => !$errors->has('nurse_type'),
      'border-red-300 focus:border-red-400 focus:ring-red-100' => $errors->has('nurse_type')
    ])
    aria-invalid="{{ $errors->has('nurse_type') ? 'true' : 'false' }}"
    aria-describedby="{{ $errors->has('nurse_type') ? 'nurse_type-error' : '' }}"
  >
    <option value="">{{ __('— Select nurse type —') }}</option>
    <option value="Nurse Practitioner" {{ old('nurse_type') === 'Nurse Practitioner' ? 'selected' : '' }}>Nurse Practitioner</option>
    <option value="Emergency room nurse" {{ old('nurse_type') === 'Emergency room nurse' ? 'selected' : '' }}>Emergency room nurse</option>
    <option value="Oncology nursing" {{ old('nurse_type') === 'Oncology nursing' ? 'selected' : '' }}>Oncology nursing</option>
    <option value="Labor and Delivery Nurse" {{ old('nurse_type') === 'Labor and Delivery Nurse' ? 'selected' : '' }}>Labor and Delivery Nurse</option>
    <option value="Licensed Practical Nurse" {{ old('nurse_type') === 'Licensed Practical Nurse' ? 'selected' : '' }}>Licensed Practical Nurse</option>
    <option value="Nurse Anesthetist" {{ old('nurse_type') === 'Nurse Anesthetist' ? 'selected' : '' }}>Nurse Anesthetist</option>
    <option value="Cardiac nurse" {{ old('nurse_type') === 'Cardiac nurse' ? 'selected' : '' }}>Cardiac nurse</option>
    <option value="Clinical nurse specialist" {{ old('nurse_type') === 'Clinical nurse specialist' ? 'selected' : '' }}>Clinical nurse specialist</option>
    <option value="Home Health nurse" {{ old('nurse_type') === 'Home Health nurse' ? 'selected' : '' }}>Home Health nurse</option>
    <option value="Nurse educator" {{ old('nurse_type') === 'Nurse educator' ? 'selected' : '' }}>Nurse educator</option>
    <option value="Nurse midwife" {{ old('nurse_type') === 'Nurse midwife' ? 'selected' : '' }}>Nurse midwife</option>
    <option value="Critical care nursing" {{ old('nurse_type') === 'Critical care nursing' ? 'selected' : '' }}>Critical care nursing</option>
    <option value="ICU nurse" {{ old('nurse_type') === 'ICU nurse' ? 'selected' : '' }}>ICU nurse</option>
    <option value="Mental health nursing" {{ old('nurse_type') === 'Mental health nursing' ? 'selected' : '' }}>Mental health nursing</option>
    <option value="Pediatric nursing" {{ old('nurse_type') === 'Pediatric nursing' ? 'selected' : '' }}>Pediatric nursing</option>
    <option value="Surgical nurses" {{ old('nurse_type') === 'Surgical nurses' ? 'selected' : '' }}>Surgical nurses</option>
    <option value="Travel Nurse" {{ old('nurse_type') === 'Travel Nurse' ? 'selected' : '' }}>Travel Nurse</option>
    <option value="Informatics nurse" {{ old('nurse_type') === 'Informatics nurse' ? 'selected' : '' }}>Informatics nurse</option>
    <option value="Public Health Nurse" {{ old('nurse_type') === 'Public Health Nurse' ? 'selected' : '' }}>Public Health Nurse</option>
    <option value="Geriatric nurse" {{ old('nurse_type') === 'Geriatric nurse' ? 'selected' : '' }}>Geriatric nurse</option>
    <option value="NICU nurse" {{ old('nurse_type') === 'NICU nurse' ? 'selected' : '' }}>NICU nurse</option>
    <option value="Nurse administrator" {{ old('nurse_type') === 'Nurse administrator' ? 'selected' : '' }}>Nurse administrator</option>
    <option value="Operating Room nurse" {{ old('nurse_type') === 'Operating Room nurse' ? 'selected' : '' }}>Operating Room nurse</option>
  </select>
  @error('nurse_type')
    <p id="nurse_type-error" class="mt-2 text-xs text-red-600">{{ $message }}</p>
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

            {{-- STEP 2: Faculty ID Upload --}}
            <div class="min-w-full pl-1">
              <div>
                <label for="faculty_id_file" class="block text-sm font-medium text-slate-700">
                  Upload Faculty ID (PDF/JPG/PNG, max 8MB)
                </label>
                <input
                  id="faculty_id_file" name="faculty_id_file" type="file" accept=".pdf,.jpg,.jpeg,.png"
                  class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                  {{-- made required via JS only when on Step 2 --}}
                >
                <p class="mt-2 text-xs text-slate-500">
                  Make sure your name and ID number are clearly visible. This will be submitted to the Admin for verification.
                </p>

                {{-- Preview (for images) --}}
                <img id="facultyIdPreview" alt="" class="mt-3 hidden w-full max-h-64 rounded-lg object-contain border border-slate-200" />

                @error('faculty_id_file')
                  <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
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
            Already have a CI account?
            <a href="{{ url('/faculty/login') }}" class="font-medium text-slate-900 hover:underline">Sign in</a>
          </p>

          {{-- Switch to student registration --}}
          <p class="mt-2 text-center text-xs text-slate-500">
            Prefer to register as a student?
            <a href="{{ url('/register') }}" class="font-medium text-slate-700 hover:underline">Student registration</a>
          </p>
        </div>
      </form>
    </div>
  </section>
</main>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Entrance animation
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

    // Wizard logic (no dependencies)
    (function () {
      const track     = document.getElementById('paneTrack');
      const btnNext   = document.getElementById('btnNext');
      const btnBack   = document.getElementById('btnBack');
      const btnSubmit = document.getElementById('btnSubmit');
      const stepDot1  = document.getElementById('stepDot1');
      const stepDot2  = document.getElementById('stepDot2');
      const preview   = document.getElementById('facultyIdPreview');
      const fileInput = document.getElementById('faculty_id_file');

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

        // required toggling (file only required on step 2)
        if (step === 2) {
          fileInput.setAttribute('required', 'required');
        } else {
          fileInput.removeAttribute('required');
        }
      }

      function step1Valid() {
        const need = ['full_name','email','faculty_id','nurse_type','password','password_confirmation'];
        for (const id of need) {
          const el = document.getElementById(id);
          if (!el || !el.value.trim()) return false;
        }
        return true;
      }

      btnNext.addEventListener('click', () => {
        if (!step1Valid()) {
          alert('Please complete all fields on Step 1, including Nurse type.');
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

      // If there were validation errors about the file, open Step 2 automatically.
      @if ($errors->has('faculty_id_file'))
        go(2);
      @else
        go(1);
      @endif
    })();
  });
</script>
@endpush
