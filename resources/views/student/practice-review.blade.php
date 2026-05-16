<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $run->procedure->title }} · Practice Review · NurSync</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
  <main class="min-h-screen flex">
    @php($active = 'procedures')
    @include('partials.sidebar')

    <section class="flex-1">
      <div class="container mx-auto px-8 py-10 space-y-6">

        <header class="space-y-2">
          <div class="flex items-center gap-3">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
              <i data-lucide="{{ $run->procedure->icon ?? 'book-open' }}" class="h-5 w-5"></i>
            </span>
            <h1 class="text-[24px] font-extrabold text-slate-900">{{ $run->procedure->title }} — Practice Review</h1>
          </div>
          <p class="text-xs text-slate-600">
            Started {{ $run->started_at?->format('M d, Y H:i') }} •
            Finished {{ $run->ended_at?->format('M d, Y H:i') ?? '—' }} •
            Status: {{ ucfirst($run->status) }}
          </p>
        </header>

        <div class="grid gap-6 lg:grid-cols-12">
          <section class="lg:col-span-8 space-y-4">
            <div class="rounded-2xl border border-slate-200/70 bg-white p-5">
              <div class="text-[13px] font-semibold text-slate-800 mb-3">Step Timeline</div>
              <ol class="space-y-3">
                @foreach($steps as $rs)
                  <li
                    class="rounded-xl border p-3 @class(['bg-white border-slate-200' => !$rs->is_done, 'bg-emerald-50 border-emerald-200' => $rs->is_done])">
                    <div class="flex items-center justify-between">
                      <div class="text-sm font-semibold text-slate-800">
                        Step {{ $rs->step->step_no }} — {{ $rs->step->title }}
                      </div>
                      <div class="text-xs text-slate-500">
                        {{ $rs->is_done ? 'Done' : 'Not done' }}
                        @if($rs->done_at) • {{ $rs->done_at->format('H:i') }} @endif
                      </div>
                    </div>
                    @if($rs->notes)
                      <p class="mt-2 text-xs text-slate-600"><span class="font-medium">Notes:</span> {{ $rs->notes }}</p>
                    @endif
                  </li>
                @endforeach
              </ol>
            </div>
          </section>

          <aside class="lg:col-span-4 space-y-4">
            <div class="rounded-2xl border border-slate-200/70 bg-white p-5">
              <div class="text-[13px] font-semibold text-slate-800 mb-2">Reflection</div>
              <div class="text-sm text-slate-700 whitespace-pre-wrap">{{ $run->reflection_text ?: '—' }}</div>
            </div>

            <a href="{{ route('student.procedures.index') }}"
              class="inline-flex items-center gap-2 text-sm text-slate-700 hover:underline">
              <i data-lucide="arrow-left" class="h-4 w-4"></i> Back to Procedures
            </a>
          </aside>
        </div>

        <div class="rounded-2xl border border-slate-200/70 bg-white p-5">
          <div class="flex items-start gap-3">
            <i data-lucide="info" class="h-5 w-5 text-slate-500 mt-0.5"></i>
            <p class="text-[13px] leading-6 text-slate-600">This summary is for learning reflection only. No scores
              recorded.</p>
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