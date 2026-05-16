<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $procedure->title }} · Practice · NurSync</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style> body{font-family:'Poppins',ui-sans-serif,system-ui,sans-serif;} </style>
</head>
<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  @php($active='procedures')
  @include('partials.sidebar')

  <section class="flex-1">
    <div class="container mx-auto px-8 py-10 space-y-6">

      <nav class="text-xs text-slate-500">
        <a href="{{ route('student.procedures.index') }}" class="hover:text-slate-700">Procedures</a>
        <span class="mx-2">/</span>
        <span class="text-slate-700">{{ $procedure->title }}</span>
      </nav>

      <header class="space-y-2">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
            <i data-lucide="{{ $procedure->icon ?? 'book-open' }}" class="h-5 w-5"></i>
          </span>
          <h1 class="text-[28px] font-extrabold tracking-tight text-slate-900">{{ $procedure->title }} — Practice</h1>
        </div>
        <p class="text-sm text-slate-600">{{ $procedure->description }}</p>
      </header>

      <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
          @php($ppes = (array)($procedure->ppe_json ?? []))
          @if($ppes || !empty($procedure->hazards_text))
            <div class="rounded-2xl border border-slate-200/70 bg-white p-5">
              <div class="grid gap-5 md:grid-cols-2">
                @if($ppes)
                  <div>
                    <div class="text-[13px] font-semibold text-slate-800 flex items-center gap-2">
                      <i data-lucide="shield" class="h-4 w-4 text-slate-500"></i> Required PPE
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                      @foreach($ppes as $ppe)
                        <span class="rounded-lg border px-2 py-1 text-xs text-slate-700 bg-slate-50">{{ $ppe }}</span>
                      @endforeach
                    </div>
                  </div>
                @endif

                @if(!empty($procedure->hazards_text))
                  <div>
                    <div class="text-[13px] font-semibold text-slate-800 flex items-center gap-2">
                      <i data-lucide="alert-triangle" class="h-4 w-4 text-rose-500"></i> Hazards & Precautions
                    </div>
                    <p class="mt-3 text-sm leading-6 text-slate-700">{{ $procedure->hazards_text }}</p>
                  </div>
                @endif
              </div>
            </div>
          @endif

          <div class="rounded-2xl border border-slate-200/70 bg-white p-5">
            <div class="text-[13px] font-semibold text-slate-800 flex items-center gap-2">
              <i data-lucide="list-checks" class="h-4 w-4 text-slate-500"></i> You will practice the following steps
            </div>
            <ol class="mt-3 space-y-1 text-sm text-slate-700">
              @foreach($procedure->steps as $s)
                <li>Step {{ $s->step_no }} — {{ $s->title }}</li>
              @endforeach
            </ol>
          </div>
        </div>

        <aside class="space-y-4">
          @if($procedure->video_url || $procedure->video_path)
            <div class="rounded-2xl border border-slate-200/70 bg-white p-4">
              <div class="text-[13px] font-semibold text-slate-800 flex items-center gap-2">
                <i data-lucide="video" class="h-4 w-4 text-slate-500"></i> Demo Video
              </div>
              <div class="mt-3 aspect-video rounded-xl overflow-hidden border">
                <iframe src="{{ $procedure->video_url ?: Storage::url($procedure->video_path) }}" class="w-full h-full" allowfullscreen loading="lazy"></iframe>
              </div>
            </div>
          @endif

<form method="POST" action="{{ route('student.procedures.practice.start', $procedure->slug) }}" class="rounded-2xl border border-slate-200/70 bg-white p-5 space-y-3">
            @csrf
            <div class="text-[13px] font-semibold text-slate-800">Start Practice</div>
            <p class="text-xs text-slate-600">No grading. You can mark steps done/undo and add notes.</p>

            <input type="hidden" name="mode" value="guided">
            <button class="w-full rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:opacity-95">
              Start Practice
            </button>

            @if($existing)
              <a href="{{ route('student.practice.run', $existing) }}"
                 class="w-full inline-flex items-center justify-center rounded-lg border px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                Continue In-Progress Run
              </a>
            @endif
          </form>

          <a href="{{ route('student.procedures.index') }}"
             class="inline-flex items-center gap-2 text-sm text-slate-700 hover:underline">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Back to Procedures
          </a>
        </aside>
      </div>

      <div class="rounded-2xl border border-slate-200/70 bg-white p-5">
        <div class="flex items-start gap-3">
          <i data-lucide="shield-alert" class="h-5 w-5 text-slate-500 mt-0.5"></i>
          <p class="text-[13px] leading-6 text-slate-600"><span class="font-semibold">Note:</span> Campus practice only; no real patient data is stored.</p>
        </div>
      </div>

    </div>
  </section>
</main>

@include('partials.student-footer')
<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>
</body>
</html>
