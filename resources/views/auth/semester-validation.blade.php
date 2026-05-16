<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Account Verification · NurSync</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-white text-slate-800 antialiased">

  <main class="min-h-screen grid lg:grid-cols-2">
    {{-- Left: Quote / info panel (matches login style) --}}
    <section class="relative hidden lg:flex items-center justify-center bg-gradient-to-b from-slate-50 to-white">
      <div class="absolute inset-0 pointer-events-none bg-grid-slate opacity-60"></div>

      <div class="relative max-w-xl px-10">
        <div class="flex items-center gap-6 mb-8 text-slate-400">
          <button type="button" class="size-9 grid place-items-center rounded-full border border-slate-300 hover:bg-slate-50 transition" aria-label="Previous">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 19l-7-7 7-7"/></svg>
          </button>
          <button type="button" class="size-9 grid place-items-center rounded-full border border-slate-300 hover:bg-slate-50 transition" aria-label="Next">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5l7 7-7 7"/></svg>
          </button>
        </div>

        <blockquote class="text-2xl font-semibold leading-snug text-slate-800">
          “Nursing is more than a career, it's a noble calling that touches the lives of people at their most vulnerable.”
        </blockquote>
        <p class="mt-4 text-slate-500">— Elizabeth Kenny</p>
      </div>
    </section>

    {{-- Right: Verification form --}}
    <section class="flex items-center">
      <div class="w-full max-w-md mx-auto px-6 py-12">
        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold">Account verification</h1>
          <p class="mt-2 text-slate-500">Upload your official registration form to activate/reactivate your account this semester.</p>
        </div>

        {{-- Flash / errors --}}
        @if (session('status'))
          <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
          </div>
        @endif
        @if ($errors->any())
          <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ url('/semester-validation') }}" enctype="multipart/form-data" class="space-y-5">
          @csrf

          <div>
            <label for="student_no" class="block text-sm font-medium text-slate-700">Student Number</label>
            <input id="student_no" name="student_no" type="text" required
                   value="{{ old('student_no') }}"
                   class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                   placeholder="e.g., 21-1234-567">
          </div>

          <div>
            <label for="full_name" class="block text-sm font-medium text-slate-700">Full name</label>
            <input id="full_name" name="full_name" type="text" required
                   value="{{ old('full_name') }}"
                   class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                   placeholder="First M. Last">
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label for="program" class="block text-sm font-medium text-slate-700">Program</label>
              <select id="program" name="program" required
                      class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200">
                <option value="">Select</option>
                <option value="BSN" @selected(old('program')==='BSN')>BS Nursing</option>
                <option value="BSN-Bridge" @selected(old('program')==='BSN-Bridge')>BSN Bridge</option>
              </select>
            </div>
            <div>
              <label for="semester" class="block text-sm font-medium text-slate-700">Semester</label>
              <select id="semester" name="semester" required
                      class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200">
                <option value="">Select</option>
                <option value="1st" @selected(old('semester')==='1st')>1st Semester</option>
                <option value="2nd" @selected(old('semester')==='2nd')>2nd Semester</option>
                <option value="midyear" @selected(old('semester')==='midyear')>Midyear / Summer</option>
              </select>
            </div>
          </div>

          <div>
            <label for="sy" class="block text-sm font-medium text-slate-700">School Year</label>
            <input id="sy" name="sy" type="text" required
                   value="{{ old('sy') }}"
                   class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                   placeholder="e.g., 2025–2026">
          </div>

          <div>
            <label for="reg_form" class="block text-sm font-medium text-slate-700">Registration Form (PDF/JPG/PNG)</label>
            <input id="reg_form" name="reg_form" type="file" accept=".pdf,.jpg,.jpeg,.png" required
                   class="mt-2 block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-white hover:file:bg-slate-800">
            <p class="mt-2 text-xs text-slate-500">Max 5MB. Make sure the student number and semester are visible.</p>
          </div>

          <label class="flex items-start gap-3 text-sm text-slate-700">
            <input type="checkbox" name="declaration" required class="mt-1 size-4 rounded border-slate-300 text-slate-900 focus:ring-slate-300">
            <span>I certify the information provided is accurate and the file is the official registration form issued by the University.</span>
          </label>

          {{-- Primary submit --}}
          <button type="submit" class="w-full btn-black">Submit for verification</button>

          {{-- Secondary actions --}}
          <div class="flex items-center gap-3">
            <a href="{{ url('/') }}" class="btn-outline flex-1 justify-center">Cancel</a>
            <button type="submit" name="save_draft" value="1" class="btn-outline flex-1 justify-center" data-no-transition>Save draft</button>
          </div>
        </form>
      </div>
    </section>
  </main>
</body>
</html>
