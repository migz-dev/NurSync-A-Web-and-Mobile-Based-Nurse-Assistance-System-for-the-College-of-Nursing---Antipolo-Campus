{{-- resources/views/faculty/procedures/show.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Faculty • Procedure Guide · NurSync</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style> body { font-family:'Poppins', ui-sans-serif, system-ui, sans-serif; } </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Sidebar (CI) --}}
  @include('partials.faculty-sidebar', ['active' => 'procedures'])

  {{-- Main content --}}
  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-8 py-12 space-y-6">

      {{-- Back / header --}}
      <header class="space-y-4">
        <div class="flex items-center justify-between gap-3">
          <button type="button"
                  onclick="window.location.href='{{ route('faculty.procedures.index') }}'"
                  class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-[12px] font-medium text-slate-700 hover:bg-slate-50">
            <i data-lucide="chevron-left" class="h-4 w-4"></i>
            Back to list
          </button>

          <div class="flex items-center gap-2">
            <a href="{{ route('faculty.procedures.edit', $procedure->slug) }}"
               class="inline-flex items-center gap-1 rounded-xl border border-amber-200 bg-white px-3 py-1.5 text-[12px] font-medium text-amber-800 hover:bg-amber-50">
              <i data-lucide="edit-3" class="h-4 w-4"></i>
              Edit
            </a>
          </div>
        </div>

        {{-- Main heading block --}}
        @php
          $status = $procedure->status ?? 'draft';
          $statusClasses = match ($status) {
              'published' => 'bg-emerald-50 text-emerald-700',
              default     => 'bg-slate-50 text-slate-700',
          };
          $ward = $procedure->clinical_wards ?? null;

          $wardChip = function (?string $w) {
            return match ($w) {
              'Community Health (CHN)' => 'bg-emerald-50 text-emerald-700',
              'OB Ward','Delivery Room (DR)','Nursery' => 'bg-pink-50 text-pink-700',
              'Pediatrics (PEDIA)' => 'bg-sky-50 text-sky-700',
              'Medical-Surgical (MS)' => 'bg-slate-50 text-slate-700',
              'ICU' => 'bg-rose-50 text-rose-700',
              'Oncology' => 'bg-fuchsia-50 text-fuchsia-700',
              'Isolation Unit' => 'bg-amber-50 text-amber-700',
              'Endocrine Unit' => 'bg-cyan-50 text-cyan-700',
              'Neurology Unit' => 'bg-indigo-50 text-indigo-700',
              'Psychiatric (PSYCH)' => 'bg-violet-50 text-violet-700',
              'Emergency Room (ER)' => 'bg-orange-50 text-orange-700',
              'Operating Room (OR)' => 'bg-green-50 text-green-700',
              'Trauma Unit' => 'bg-red-50 text-red-700',
              'Disaster Response / Community Field' => 'bg-yellow-50 text-yellow-700',
              default => 'bg-slate-50 text-slate-700',
            };
          };

          $tags = (array)($procedure->tags_json ?? []);
          $tagPalette = [
            'bg-emerald-50 text-emerald-700 border-emerald-200',
            'bg-sky-50 text-sky-700 border-sky-200',
            'bg-amber-50 text-amber-800 border-amber-200',
            'bg-violet-50 text-violet-700 border-violet-200',
            'bg-rose-50 text-rose-700 border-rose-200',
            'bg-indigo-50 text-indigo-700 border-indigo-200',
            'bg-teal-50 text-teal-700 border-teal-200',
            'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200',
            'bg-lime-50 text-lime-700 border-lime-200',
          ];
          $tagClass = function(string $t) use ($tagPalette) {
              $i = abs(crc32(mb_strtolower($t))) % count($tagPalette);
              return $tagPalette[$i];
          };
        @endphp

        <div class="flex items-start justify-between gap-4">
          <div class="flex items-start gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
              <i data-lucide="book-open" class="h-5 w-5"></i>
            </span>
            <div>
              <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
                {{ $procedure->title ?? 'Procedure' }}
              </h1>
              <p class="mt-1 text-[13px] text-slate-600 max-w-3xl">
                {{ $procedure->description ?? 'No summary provided for this guide yet.' }}
              </p>

              <div class="mt-3 flex flex-wrap items-center gap-2">
                @if ($ward)
                  <span class="inline-flex items-center rounded-full {{ $wardChip($ward) }} px-2 py-0.5 text-[11px]">
                    <i data-lucide="hospital" class="h-3 w-3 mr-1"></i>
                    {{ $ward }}
                  </span>
                @endif

                <span class="inline-flex items-center rounded-full {{ $statusClasses }} px-2 py-0.5 text-[11px] font-medium">
                  {{ ucfirst($status) }}
                </span>

                <span class="inline-flex items-center rounded-full bg-slate-50 px-2 py-0.5 text-[11px] text-slate-700">
                  <i data-lucide="clock" class="h-3 w-3 mr-1"></i>
                  Updated {{ $procedure->updated_at?->diffForHumans() ?? '—' }}
                </span>
              </div>
            </div>
          </div>

          <div class="text-right text-[11px] text-slate-500">
            <div>Created: {{ $procedure->created_at?->format('M d, Y H:i') ?? '—' }}</div>
            <div>Published: {{ $procedure->published_at?->format('M d, Y H:i') ?? '—' }}</div>
          </div>
        </div>
      </header>

      {{-- Tags + Meta links --}}
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-wrap items-center gap-2">
          <span class="text-[12px] font-medium text-slate-600">Tags:</span>
          @if (!empty($tags))
            @foreach ($tags as $t)
              <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[11px] {{ $tagClass($t) }}">
                <i data-lucide="tag" class="h-3 w-3 mr-1"></i>
                {{ $t }}
              </span>
            @endforeach
          @else
            <span class="text-[12px] text-slate-400">No tags assigned.</span>
          @endif
        </div>

        <div class="flex flex-wrap items-center gap-3 text-[11px] text-slate-500">
          @if ($procedure->video_url || $procedure->video_path)
            <a href="{{ $procedure->video_path ? asset($procedure->video_path) : $procedure->video_url }}"
               target="_blank"
               class="inline-flex items-center gap-1 rounded-xl border border-slate-200 px-2.5 py-1 hover:bg-slate-50">
              <i data-lucide="play-circle" class="h-3 w-3"></i>
              Demo video
            </a>
          @endif

          @if ($procedure->pdf_path)
            <a href="{{ asset($procedure->pdf_path) }}" target="_blank"
               class="inline-flex items-center gap-1 rounded-xl border border-slate-200 px-2.5 py-1 hover:bg-slate-50">
              <i data-lucide="file-text" class="h-3 w-3"></i>
              Attached PDF
            </a>
          @endif
        </div>
      </div>

      {{-- Layout: Overview / Meta + Steps --}}
      <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,3fr)]">
        {{-- Left: Overview / PPE / Meta --}}
        <div class="space-y-4">
          {{-- Overview & Hazards --}}
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
            <h2 class="text-[14px] font-semibold text-slate-900 mb-2 flex items-center gap-2">
              <i data-lucide="file-text" class="h-4 w-4 text-slate-500"></i>
              Overview & Safety Notes
            </h2>
            @php $haz = trim((string)($procedure->hazards_text ?? '')); @endphp
            @if (!empty($procedure->description) || $haz)
              @if (!empty($procedure->description))
                <div class="prose prose-sm max-w-none text-slate-700">
                  {!! nl2br(e($procedure->description)) !!}
                </div>
              @endif
              @if ($haz)
                <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50/60 p-3 text-[12px] text-amber-900">
                  <span class="inline-flex items-center gap-1 font-semibold">
                    <i data-lucide="alert-triangle" class="h-3.5 w-3.5"></i> Hazards / Cautions
                  </span>
                  <div class="mt-1">{!! nl2br(e($haz)) !!}</div>
                </div>
              @endif
            @else
              <p class="text-[13px] text-slate-500">No overview or safety notes yet.</p>
            @endif
          </div>

          {{-- PPE --}}
          @php $ppe = (array)($procedure->ppe_json ?? []); @endphp
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
            <h2 class="text-[14px] font-semibold text-slate-900 mb-2 flex items-center gap-2">
              <i data-lucide="shield" class="h-4 w-4 text-slate-500"></i>
              PPE & Equipment
            </h2>
            @if (!empty($ppe))
              <div class="mt-1 flex flex-wrap gap-2">
                @foreach ($ppe as $item)
                  <span class="inline-flex items-center rounded-full bg-slate-50 border border-slate-200 px-2 py-0.5 text-[11px] text-slate-700">
                    <i data-lucide="check" class="h-3 w-3 mr-1"></i>{{ $item }}
                  </span>
                @endforeach
              </div>
            @else
              <p class="text-[12px] text-slate-500">No PPE items listed.</p>
            @endif
          </div>

          {{-- Quick Meta --}}
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
            <h2 class="text-[14px] font-semibold text-slate-900 mb-2 flex items-center gap-2">
              <i data-lucide="info" class="h-4 w-4 text-slate-500"></i>
              Quick Meta
            </h2>
            <dl class="grid grid-cols-1 gap-2 text-[12px] text-slate-600">
              <div class="flex justify-between">
                <dt class="text-slate-500">Ward / Area</dt>
                <dd class="font-medium">{{ $ward ?? '—' }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-slate-500">Status</dt>
                <dd class="font-medium capitalize">{{ $status }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-slate-500">Total steps</dt>
                <dd class="font-medium">{{ $procedure->steps?->count() ?? 0 }}</dd>
              </div>
            </dl>
          </div>
        </div>

        {{-- Right: Step-by-step algorithm (accordion + per-step video) --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-[14px] font-semibold text-slate-900 flex items-center gap-2">
              <i data-lucide="list-ordered" class="h-4 w-4 text-slate-500"></i>
              Step-by-step algorithm
            </h2>
            <span class="text-[11px] text-slate-500">Tap a step to expand details & video.</span>
          </div>

          @if (!$procedure->steps || $procedure->steps->isEmpty())
            <p class="text-[13px] text-slate-500">No steps added yet. Use the Edit page to add steps.</p>
          @else
            <ol class="space-y-2" id="stepAccordion">
              @foreach ($procedure->steps->sortBy('step_no') as $step)
                @php
                  $panelId = 'step-panel-'.$step->id;
                  $hasVideo = !empty($step->video_url) || !empty($step->video_path);
                  $bodyPreview = \Illuminate\Support\Str::limit((string) $step->body, 80);
                @endphp
                <li class="rounded-xl border border-slate-100 bg-slate-50/70 overflow-hidden">
                  {{-- Accordion header --}}
                  <button type="button"
                          class="w-full flex items-center justify-between gap-3 px-3 py-2 text-left hover:bg-slate-100/70"
                          data-step-toggle="{{ $panelId }}">
                    <div class="flex items-center gap-3">
                      {{-- Number badge --}}
                      <div class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-50 text-[12px] font-semibold text-emerald-700 border border-emerald-100">
                        {{ $step->step_no ?? $loop->iteration }}
                      </div>
                      <div>
                        <div class="text-[13px] font-semibold text-slate-900">
                          {{ $step->title ?: 'Step '.$step->step_no }}
                        </div>
                        @if($bodyPreview)
                          <div class="text-[11px] text-slate-500">
                            {{ $bodyPreview }}
                          </div>
                        @endif
                      </div>
                    </div>

                    <div class="flex items-center gap-2 text-[11px] text-slate-500">
                      @if(!empty($step->duration_seconds))
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5">
                          <i data-lucide="timer" class="h-3 w-3"></i>
                          {{ $step->duration_seconds }}s
                        </span>
                      @endif
                      @if($hasVideo)
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5">
                          <i data-lucide="play-circle" class="h-3 w-3"></i>
                          Video
                        </span>
                      @endif
                      <i data-lucide="chevron-down"
                         class="h-4 w-4 text-slate-500 js-step-chevron transition-transform duration-200"></i>
                    </div>
                  </button>

                  {{-- Accordion panel --}}
                  <div id="{{ $panelId }}" class="js-step-panel px-3 pb-3 pt-0 hidden">
                    {{-- Instruction --}}
                    @if ($step->body)
                      <div class="mt-2 text-[12.5px] text-slate-700">
                        {{ $step->body }}
                      </div>
                    @endif

                    {{-- Rationale / Caution --}}
                    @if (!empty($step->rationale) || !empty($step->caution))
                      <div class="mt-3 grid gap-2 md:grid-cols-2">
                        @if (!empty($step->rationale))
                          <p class="text-[12px] text-slate-700">
                            <span class="font-semibold text-slate-900">Rationale:</span>
                            {{ $step->rationale }}
                          </p>
                        @endif
                        @if (!empty($step->caution))
                          <p class="text-[12px] text-rose-700">
                            <span class="font-semibold">Caution:</span>
                            {{ $step->caution }}
                          </p>
                        @endif
                      </div>
                    @endif

                    {{-- Per-step video --}}
                    @if ($hasVideo)
                      <div class="mt-3">
                        <div class="mb-1 text-[11px] text-slate-500 flex items-center gap-1">
                          <i data-lucide="play-circle" class="h-3 w-3"></i>
                          Step video
                        </div>

                        @if(!empty($step->video_url))
                          {{-- Embedded YouTube/Vimeo --}}
                          <div class="aspect-video w-full rounded-xl overflow-hidden border border-slate-200 bg-black/80">
                            <iframe
                              src="{{ $step->video_url }}"
                              class="w-full h-full"
                              frameborder="0"
                              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                              allowfullscreen>
                            </iframe>
                          </div>
                        @elseif(!empty($step->video_path))
                          {{-- Uploaded video file --}}
                          <video controls class="w-full rounded-xl border border-slate-200 bg-black/80">
                            <source src="{{ asset($step->video_path) }}" type="video/mp4">
                            Your browser does not support the video tag.
                          </video>
                        @endif
                      </div>
                    @endif
                  </div>
                </li>
              @endforeach
            </ol>
          @endif
        </div>
      </div>

    </div>
  </section>
</main>

@includeIf('partials.faculty-footer')
@includeWhen(!View::exists('partials.faculty-footer'), 'partials.student-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();

  // Simple accordion behavior – only one step open at a time
  document.addEventListener('DOMContentLoaded', () => {
    const toggles = document.querySelectorAll('[data-step-toggle]');

    toggles.forEach(btn => {
      btn.addEventListener('click', () => {
        const targetId = btn.getAttribute('data-step-toggle');
        const panel = document.getElementById(targetId);
        const isOpen = panel && !panel.classList.contains('hidden');

        // Close all panels
        document.querySelectorAll('.js-step-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.js-step-chevron').forEach(icon => {
          icon.classList.remove('rotate-180');
        });

        // Open clicked one if it was previously closed
        if (!isOpen && panel) {
          panel.classList.remove('hidden');
          const icon = btn.querySelector('.js-step-chevron');
          if (icon) icon.classList.add('rotate-180');
        }
      });
    });
  });
</script>
</body>
</html>
