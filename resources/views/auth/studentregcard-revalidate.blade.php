{{-- resources/views/auth/studentregcard-revalidate.blade.php --}}
@extends('layouts.app')

@section('title', 'Student Reg Card · Revalidate · NurSync')

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

        <h1 class="text-center text-2xl font-bold text-slate-900">Student Registration Card</h1>
        <p class="mt-2 text-center text-slate-600">
          Upload your updated registration form to revalidate your student account.
        </p>

        {{-- success message --}}
        @if (session('status'))
          <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
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

        <form method="POST" action="{{ url('/student/regcard/revalidate') }}" enctype="multipart/form-data" class="mt-6 space-y-5">
          @csrf

          {{-- Upload Registration Form --}}
          <div>
            <label for="regcard_file" class="block text-sm font-medium text-slate-700">
              Upload Updated Registration Form <span class="text-red-500">*</span>
            </label>
            <input
              id="regcard_file"
              name="regcard_file"
              type="file"
              required
              accept=".pdf,.jpg,.jpeg,.png"
              @class([
                'mt-2 block w-full text-sm text-slate-700 border rounded-xl px-4 py-3 bg-white focus:outline-none focus:ring-2',
                'border-slate-300 focus:border-slate-400 focus:ring-slate-200' => !$errors->has('regcard_file'),
                'border-red-300 focus:border-red-400 focus:ring-red-100' => $errors->has('regcard_file')
              ])
              aria-invalid="{{ $errors->has('regcard_file') ? 'true' : 'false' }}"
              aria-describedby="{{ $errors->has('regcard_file') ? 'regcard_file-error' : '' }}"
            >
            <p class="mt-2 text-xs text-slate-500">Accepted formats: PDF, JPG, JPEG, PNG (max 5MB)</p>
            @error('regcard_file')
              <p id="regcard_file-error" class="mt-2 text-xs text-red-600">{{ $message }}</p>
            @enderror
          </div>

          {{-- Optional: Date of Birth for verification --}}
          <div>
            <label for="birthdate" class="block text-sm font-medium text-slate-700">
              Date of Birth <span class="text-slate-400 font-normal">(optional)</span>
            </label>
            <input
              id="birthdate"
              name="birthdate"
              type="date"
              value="{{ old('birthdate') }}"
              @class([
                'mt-2 w-full rounded-xl border bg-white px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2',
                'border-slate-300 focus:border-slate-400 focus:ring-slate-200' => !$errors->has('birthdate'),
                'border-red-300 focus:border-red-400 focus:ring-red-100' => $errors->has('birthdate')
              ])
            >
            @error('birthdate')
              <p id="birthdate-error" class="mt-2 text-xs text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <button type="submit" class="w-full btn-black">
            Submit Revalidation
          </button>
        </form>

        <div class="mt-6 text-center">
          <a href="{{ url('/login') }}" class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900">
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
@endsection
