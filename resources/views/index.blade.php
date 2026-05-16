@extends('layouts.public')

@section('title', 'NurSync — Plan. Coordinate. Guide.')

@section('content')
  {{-- HERO (centered, grid background) --}}
  <section class="relative isolate overflow-hidden" aria-label="Hero">
    <div class="absolute inset-0 -z-10 bg-grid-slate"></div>

    <div class="mx-auto max-w-5xl px-4 sm:px-6 pt-20 pb-12 sm:pt-24 sm:pb-16 text-center">

      {{-- Pill badge --}}
      <div
        class="inline-flex items-center justify-center rounded-full bg-slate-900 text-white text-xs font-semibold px-4 py-2 shadow-sm fade-up-init"
        data-animate="fade-up">
        Nurse Assistance System
      </div>

      {{-- Headline --}}
      <h1
        class="mt-6 text-[38px] leading-[1.05] font-extrabold tracking-tight text-slate-900 sm:text-[56px] fade-up-init"
        data-animate="fade-up">
        Organize procedures.<br class="hidden sm:block" />
        Coordinate return demonstrations.
      </h1>

      {{-- Subhead --}}
      <p
        class="mt-4 text-base sm:text-lg text-slate-600 max-w-2xl mx-auto fade-up-init"
        data-animate="fade-up">
        Built for Clinical Instructors to plan skills labs, orchestrate demonstrations, and guide trainees with
        consistent, standardized materials—no clinical data handling required.
      </p>

      {{-- CTAs --}}
      <div
        class="mt-8 flex flex-wrap items-center justify-center gap-3 fade-up-init"
        data-animate="fade-up">
        <a href="{{ url('/faculty/register') }}" class="btn-green-light hover-lift">
          Create Your CI Account
          <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M13.5 4.5l6 6-6 6M3 12h16.5" />
          </svg>
        </a>
        <a href="#how" class="btn-outline hover-lift">
          See How It Works
        </a>
      </div>

      {{-- Logo strip (floating icons, subtle like CodeCred logos) --}}
      <div
        class="mt-10 flex items-center justify-center gap-8 opacity-30 grayscale fade-up-init"
        data-animate="fade-up"
        aria-hidden="true">
        <img src="/logos/nurse1.svg" alt="" class="h-8 float-soft">
        <img src="/logos/nurse2.svg" alt="" class="h-8 float-soft" style="animation-delay: .2s">
        <img src="/logos/nurse3.svg" alt="" class="h-8 float-soft" style="animation-delay: .4s">
        <img src="/logos/nurse4.svg" alt="" class="h-8 float-soft" style="animation-delay: .6s">
      </div>
    </div>
  </section>

  {{-- HOW IT WORKS --}}
  <section id="how" class="section-pad bg-slate-50/60" aria-labelledby="how-title">
    <div class="mx-auto max-w-7xl px-4 sm:px-6">
      <div class="grid lg:grid-cols-2 gap-8">

        {{-- LEFT: Who it’s for --}}
        <div
          class="rounded-2xl bg-white p-6 sm:p-8 shadow-sm border border-slate-200 fade-up-init"
          role="region"
          aria-labelledby="who-title"
          data-animate="fade-up">
          <span class="inline-flex items-center rounded-full bg-sky-100 text-sky-900 text-xs font-semibold px-3 py-1">
            Who’s it for?
          </span>
          <h2 id="who-title" class="mt-4 text-2xl sm:text-3xl font-bold tracking-tight">
            Designed for clinical instruction &amp; simulation
          </h2>
          <p class="mt-3 text-slate-600 max-w-prose">
            NurSync helps Clinical Instructors and program leads keep hands-on practice organized, consistent, and easy to run.
          </p>

          @php
            $audience = [
              ['icon' => '🧑‍🏫', 't' => 'Clinical Instructors', 'd' => 'Plan and coordinate procedure demonstrations and lab activities'],
              ['icon' => '🏥', 't' => 'Skills Lab / Program Leads', 'd' => 'Standardize materials and oversee session logistics'],
            ];
          @endphp

          <div class="mt-6 space-y-4">
            @foreach ($audience as $a)
              <div class="flex items-start gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4 hover-lift fade-up-init"
                   data-animate="fade-up">
                <div class="text-xl">{{ $a['icon'] }}</div>
                <div>
                  <div class="font-semibold">{{ $a['t'] }}</div>
                  <div class="text-sm text-slate-600">{{ $a['d'] }}</div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        {{-- RIGHT: Four steps --}}
        <div
          class="rounded-2xl bg-white p-6 sm:p-8 shadow-sm border border-slate-200 fade-up-init"
          role="region"
          aria-labelledby="steps-title"
          data-animate="fade-up">
          <span class="inline-flex items-center rounded-full bg-green-100 text-green-900 text-xs font-semibold px-3 py-1">
            How to get started
          </span>
          <h3 id="steps-title" class="mt-4 text-2xl sm:text-3xl font-bold tracking-tight">
            Get organized in four steps
          </h3>
          <p class="mt-2 text-slate-700">
            Set up your materials and sessions in minutes:
          </p>

          @php
            $steps = [
              ['n' => '1', 'i' => '🗂️', 't' => 'Create your CI account', 'd' => 'Quick sign-up to access the CI dashboard'],
              ['n' => '2', 'i' => '📘', 't' => 'Choose procedures', 'd' => 'Adopt standardized, faculty-approved guides and checklists'],
              ['n' => '3', 'i' => '🗓️', 't' => 'Plan demonstrations', 'd' => 'Arrange sessions by date, time, room, and cohort'],
              ['n' => '4', 'i' => '🩺', 't' => 'Guide practice', 'd' => 'Supervise activities and mark session completion securely'],
            ];
          @endphp

          <div class="mt-6 space-y-6">
            @foreach ($steps as $s)
              <div class="flex items-start gap-4 fade-up-init" data-animate="fade-up">
                <div class="shrink-0 grid place-items-center size-10 rounded-xl bg-brand text-white font-bold">
                  {{ $s['n'] }}
                </div>
                <div class="min-w-0">
                  <div class="font-semibold">
                    {{ $s['i'] }} {{ $s['t'] }}
                  </div>
                  <div class="text-sm text-slate-700">
                    {{ $s['d'] }}
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          <div class="mt-8 fade-up-init" data-animate="fade-up">
            <a href="{{ url('/faculty/register') }}" class="btn-black hover-lift">
              Create Your CI Account
            </a>
          </div>
        </div>

      </div>
    </div>
  </section>

  {{-- ABOUT --}}
  <section id="about" class="section-pad" aria-labelledby="about-title">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 text-center">
      <h2
        id="about-title"
        class="text-3xl sm:text-4xl font-extrabold tracking-tight fade-up-init"
        data-animate="fade-up">
        What does NurSync help you do?
      </h2>
      <p
        class="mt-3 text-slate-600 max-w-3xl mx-auto fade-up-init"
        data-animate="fade-up">
        Coordinate return demonstrations, standardize procedure resources, and deliver guided practice—without collecting
        or storing hospital EHR data.
      </p>

      <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div
          class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover-lift fade-up-init"
          data-animate="fade-up">
          <div class="mx-auto mb-3 size-10 grid place-items-center rounded-xl bg-blue-100 text-xl">🧰</div>
          <h3 class="font-semibold">Procedure Library</h3>
          <p class="mt-2 text-sm text-slate-600">
            Consistent step-by-step guides, files, and checklists for simulations.
          </p>
        </div>

        <div
          class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover-lift fade-up-init"
          data-animate="fade-up">
          <div class="mx-auto mb-3 size-10 grid place-items-center rounded-xl bg-green-100 text-xl">🤝</div>
          <h3 class="font-semibold">Demonstration Assistance</h3>
          <p class="mt-2 text-sm text-slate-600">
            Assist Clinical Instructors in organizing and guiding nursing demonstrations with clarity and consistency.
          </p>
        </div>

        <div
          class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover-lift fade-up-init"
          data-animate="fade-up">
          <div class="mx-auto mb-3 size-10 grid place-items-center rounded-xl bg-purple-100 text-xl">🗄️</div>
          <h3 class="font-semibold">Session Records</h3>
          <p class="mt-2 text-sm text-slate-600">
            Track completion and maintain organized archives for reference.
          </p>
        </div>
      </div>
    </div>
  </section>
@endsection
