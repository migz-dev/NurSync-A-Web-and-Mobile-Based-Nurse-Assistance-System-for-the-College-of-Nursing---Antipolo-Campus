{{-- resources/views/student-open-guide.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <title>{{ $procedure->title }} · Open Guide · NurSync — Nurse Assistance</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }

    /* Same animation style used on Procedures / Return Demo cards */
    @keyframes slide-in-up {
      from {
        transform: translateY(10px);
        opacity: 0;
      }

      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .animate-card-in {
      animation: slide-in-up .35s ease-out both;
      will-change: transform, opacity;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Sidebar --}}
  @php($active = 'procedures')
  @include('partials.sidebar')

  {{-- Main content --}}
  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-8 py-12 space-y-6" data-anim-page>

      {{-- Back / breadcrumb --}}
      <header class="space-y-4">
        <div class="flex items-center justify-between gap-3">
          <button type="button"
                  onclick="window.location.href='{{ route('student.procedures.index') }}'"
                  class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-[12px] font-medium text-slate-700 hover:bg-slate-50">
            <i data-lucide="chevron-left" class="h-4 w-4"></i>
            Back to list
          </button>

          {{-- No Edit for students --}}
        </div>

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

          // Robust tag decoding (same logic as list cards)
          $tagsRaw = $procedure->tags_json ?? [];
          if (is_array($tagsRaw)) {
              $tags = $tagsRaw;
          } elseif (is_string($tagsRaw)) {
              $decoded = json_decode($tagsRaw, true);
              $tags = is_array($decoded) ? $decoded : [$tagsRaw];
          } else {
              $tags = [];
          }
          if (count($tags) === 1 && is_string($tags[0])) {
              $maybeJson = trim($tags[0]);
              if (str_starts_with($maybeJson, '[') && str_ends_with($maybeJson, ']')) {
                  $decodedAgain = json_decode($maybeJson, true);
                  if (is_array($decodedAgain)) {
                      $tags = $decodedAgain;
                  }
              }
          }
          $tags = array_values(array_filter(array_map('trim', $tags), fn($t) => $t !== ''));

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
          $tagClass = function (string $t) use ($tagPalette) {
            $i = abs(crc32(mb_strtolower($t))) % count($tagPalette);
            return $tagPalette[$i];
          };
        @endphp

        {{-- Main heading block (mirrors CI show) --}}
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

      {{-- Tags + Meta links (CI style, student-safe wording) --}}
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-wrap items-center gap-2">
          <span class="text-[12px] font-medium text-slate-600">Tags:</span>
          @if (!empty($tags))
            @foreach ($tags as $t)
              @php $label = mb_strtolower($t); @endphp
              <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[11px] {{ $tagClass($t) }}">
                <i data-lucide="tag" class="h-3 w-3 mr-1"></i>
                {{ $label }}
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

      {{-- Layout: Overview / PPE / Meta + Steps --}}
      <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,3fr)]">
        {{-- Left: Overview / Hazards / PPE / Meta --}}
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

          {{-- Quick Meta + student note --}}
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3">
            <h2 class="text-[14px] font-semibold text-slate-900 flex items-center gap-2">
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

            <p class="mt-2 text-[11px] text-slate-500">
              Training material for campus simulation only. Follow your Clinical Instructor’s instructions during Return Demo.
            </p>
          </div>
        </div>

        {{-- Right: Step-by-step algorithm (CI-style list) --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-[14px] font-semibold text-slate-900 flex items-center gap-2">
              <i data-lucide="list-ordered" class="h-4 w-4 text-slate-500"></i>
              Step-by-step algorithm
            </h2>
            <span class="text-[11px] text-slate-500">Follow in order during demo/practice.</span>
          </div>

          @if (!$procedure->steps || $procedure->steps->isEmpty())
            <p class="text-[13px] text-slate-500">No steps added yet.</p>
          @else
            <ol class="space-y-3">
              @foreach ($procedure->steps->sortBy('step_no') as $step)
                <li class="flex items-start gap-3">
                  {{-- Number badge --}}
                  <div class="mt-1 flex h-6 w-6 items-center justify-center rounded-full bg-emerald-50 text-[12px] font-semibold text-emerald-700 border border-emerald-100">
                    {{ $step->step_no ?? $loop->iteration }}
                  </div>

                  {{-- Step card --}}
                  <div class="flex-1 rounded-xl border border-slate-100 bg-slate-50/70 px-3 py-2.5">
                    @if ($step->title)
                      <h3 class="text-[13px] font-semibold text-slate-900">{{ $step->title }}</h3>
                    @endif

                    @if ($step->body)
                      <p class="mt-1 text-[12.5px] text-slate-700">{!! nl2br(e($step->body)) !!}</p>
                    @endif

                    @if (!empty($step->rationale) || !empty($step->caution))
                      <div class="mt-2 grid gap-2 md:grid-cols-2">
                        @if (!empty($step->rationale))
                          <p class="text-[12px] text-slate-700">
                            <span class="font-semibold text-slate-900">Rationale:</span> {!! nl2br(e($step->rationale)) !!}
                          </p>
                        @endif
                        @if (!empty($step->caution))
                          <p class="text-[12px] text-rose-700">
                            <span class="font-semibold">Caution:</span> {!! nl2br(e($step->caution)) !!}
                          </p>
                        @endif
                      </div>
                    @endif
                  </div>
                </li>
              @endforeach
            </ol>
          @endif

          {{-- Footer actions (Download PDF, if any) --}}
          <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
            <div class="text-xs text-slate-500">
              Review this guide before your scheduled Return Demonstration.
            </div>
            <div class="flex items-center gap-2">
              @if($procedure->pdf_path)
                <a href="{{ Storage::url($procedure->pdf_path) }}"
                   class="inline-flex items-center gap-1 rounded-xl bg-slate-900 px-3 py-2 text-[12px] font-medium text-white hover:opacity-95">
                  <i data-lucide="download" class="h-4 w-4"></i>
                  Download PDF
                </a>
              @endif
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</main>

@include('partials.student-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();

  // Simple page entrance animation (same keyframe as list cards)
  document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-anim-page]');
    if (!root) return;

    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReduced) {
      root.style.opacity = 1;
      root.style.transform = 'none';
      return;
    }

    root.classList.add('animate-card-in');
  });
</script>
</body>
</html>
