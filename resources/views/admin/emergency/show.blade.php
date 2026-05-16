{{-- resources/views/admin/emergency/show.blade.php --}}
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Admin • Emergency Protocol · NurSync</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style> body { font-family:'Poppins', ui-sans-serif, system-ui, sans-serif; } </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Admin Sidebar --}}
  @include('partials.admin-sidebar', ['active' => 'emergency_protocols'])

  {{-- Main content --}}
  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-8 py-12 space-y-6">

      {{-- Back / header --}}
      <header class="space-y-4">
        <div class="flex items-center justify-between gap-3">
          <button type="button"
                  onclick="window.location.href='{{ route('admin.emergency_protocols.index') }}'"
                  class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-[12px] font-medium text-slate-700 hover:bg-slate-50">
            <i data-lucide="chevron-left" class="h-4 w-4"></i>
            Back to list
          </button>

          <div class="flex items-center gap-2">
            <a href="{{ route('admin.emergency_protocols.edit', $protocol->id) }}"
               class="inline-flex items-center gap-1 rounded-xl border border-amber-200 bg-white px-3 py-1.5 text-[12px] font-medium text-amber-800 hover:bg-amber-50">
              <i data-lucide="edit-3" class="h-4 w-4"></i>
              Edit
            </a>

            {{-- Admin: allow hard delete --}}
            <form method="POST"
                  action="{{ route('admin.emergency_protocols.destroy', $protocol->id) }}"
                  onsubmit="return confirm('Delete this protocol? This cannot be undone.');">
              @csrf
              @method('DELETE')
              <button type="submit"
                      class="inline-flex items-center gap-1 rounded-xl border border-red-200 bg-white px-3 py-1.5 text-[12px] font-medium text-red-600 hover:bg-red-50">
                <i data-lucide="trash-2" class="h-4 w-4"></i>
                Delete
              </button>
            </form>
          </div>
        </div>

        {{-- Main heading block --}}
        @php
          $sev = $protocol->severity ?? 'Critical';
          $sevBg = $sevText = '';
          if ($sev === 'Critical') {
              $sevBg = 'bg-red-50';    $sevText = 'text-red-700';
          } elseif ($sev === 'Moderate') {
              $sevBg = 'bg-amber-50';  $sevText = 'text-amber-700';
          } else {
              $sevBg = 'bg-sky-50';    $sevText = 'text-sky-700';
          }

          $status = $protocol->status ?? 'draft';
          $statusClasses = match ($status) {
              'published' => 'bg-emerald-50 text-emerald-700',
              'archived'  => 'bg-slate-100 text-slate-600',
              default     => 'bg-slate-50 text-slate-700',
          };
        @endphp

        <div class="flex items-start justify-between gap-4">
          <div class="flex items-start gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-red-50 text-red-600">
              <i data-lucide="alert-triangle" class="h-5 w-5"></i>
            </span>
            <div>
              <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
                {{ $protocol->title }}
              </h1>
              <p class="mt-1 text-[13px] text-slate-600 max-w-3xl">
                {{ $protocol->summary ?? 'No summary provided for this protocol yet.' }}
              </p>

              <div class="mt-3 flex flex-wrap items-center gap-2">
                @if ($protocol->category)
                  <span class="inline-flex items-center rounded-full bg-slate-50 border border-slate-200 px-2 py-0.5 text-[11px] text-slate-700">
                    <i data-lucide="layers" class="h-3 w-3 mr-1"></i>
                    {{ $protocol->category }}
                  </span>
                @endif

                @if ($protocol->ward)
                  <span class="inline-flex items-center rounded-full bg-slate-50 border border-slate-200 px-2 py-0.5 text-[11px] text-slate-700">
                    <i data-lucide="hospital" class="h-3 w-3 mr-1"></i>
                    {{ $protocol->ward }}
                  </span>
                @endif

                <span class="inline-flex items-center rounded-full {{ $sevBg }} px-2 py-0.5 text-[11px] font-medium {{ $sevText }}">
                  <i data-lucide="activity" class="h-3 w-3 mr-1"></i>
                  {{ $protocol->severity ?? 'Critical' }}
                </span>

                <span class="inline-flex items-center rounded-full {{ $statusClasses }} px-2 py-0.5 text-[11px] font-medium">
                  {{ ucfirst($status) }}
                </span>

                <span class="inline-flex items-center rounded-full bg-slate-50 px-2 py-0.5 text-[11px] text-slate-700">
                  <i data-lucide="eye" class="h-3 w-3 mr-1"></i>
                  {{ $protocol->view_count }} views
                </span>
              </div>
            </div>
          </div>

          <div class="text-right text-[11px] text-slate-500">
            @if($protocol->faculty)
              <div class="mb-1">
                Owner: <span class="font-medium text-slate-700">{{ $protocol->faculty->full_name ?? $protocol->faculty->name }}</span>
              </div>
            @endif
            <div>Created: {{ $protocol->created_at?->format('M d, Y H:i') ?? '—' }}</div>
            <div>Updated: {{ $protocol->updated_at?->diffForHumans() ?? '—' }}</div>
          </div>
        </div>
      </header>

      {{-- Tags + Meta --}}
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-wrap items-center gap-2">
          <span class="text-[12px] font-medium text-slate-600">Tags:</span>
          @if ($protocol->tags && $protocol->tags->count())
            @foreach ($protocol->tags as $tag)
              <span class="inline-flex items-center rounded-full bg-slate-50 border border-slate-200 px-2 py-0.5 text-[11px] text-slate-700">
                <i data-lucide="tag" class="h-3 w-3 mr-1"></i>
                {{ $tag->name }}
              </span>
            @endforeach
          @else
            <span class="text-[12px] text-slate-400">No tags assigned.</span>
          @endif
        </div>

        <div class="flex flex-wrap items-center gap-3 text-[11px] text-slate-500">
          @if ($protocol->video_url)
            <a href="{{ $protocol->video_url }}" target="_blank"
               class="inline-flex items-center gap-1 rounded-xl border border-slate-200 px-2.5 py-1 hover:bg-slate-50">
              <i data-lucide="play-circle" class="h-3 w-3"></i>
              Video reference
            </a>
          @endif

          @if ($protocol->pdf_path)
            <a href="{{ asset($protocol->pdf_path) }}" target="_blank"
               class="inline-flex items-center gap-1 rounded-xl border border-slate-200 px-2.5 py-1 hover:bg-slate-50">
              <i data-lucide="file-text" class="h-3 w-3"></i>
              Attached PDF
            </a>
          @endif
        </div>
      </div>

      {{-- Layout: Description + Steps --}}
      <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,3fr)]">

        {{-- Left: Overview / Description --}}
        <div class="space-y-4">
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
            <h2 class="text-[14px] font-semibold text-slate-900 mb-2 flex items-center gap-2">
              <i data-lucide="file-text" class="h-4 w-4 text-slate-500"></i>
              Overview & Rationale
            </h2>
            @if ($protocol->description)
              <div class="prose prose-sm max-w-none text-slate-700">
                {!! nl2br(e($protocol->description)) !!}
              </div>
            @else
              <p class="text-[13px] text-slate-500">
                No detailed description has been added yet.
              </p>
            @endif
          </div>

          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
            <h2 class="text-[14px] font-semibold text-slate-900 mb-2 flex items-center gap-2">
              <i data-lucide="info" class="h-4 w-4 text-slate-500"></i>
              Quick Meta
            </h2>
            <dl class="grid grid-cols-1 gap-2 text-[12px] text-slate-600">
              <div class="flex justify-between">
                <dt class="text-slate-500">Category</dt>
                <dd class="font-medium">{{ $protocol->category ?? '—' }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-slate-500">Ward</dt>
                <dd class="font-medium">{{ $protocol->ward ?? '—' }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-slate-500">Severity</dt>
                <dd class="font-medium">{{ $protocol->severity ?? '—' }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-slate-500">Status</dt>
                <dd class="font-medium capitalize">{{ $protocol->status ?? '—' }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-slate-500">Total steps</dt>
                <dd class="font-medium">{{ $protocol->steps?->count() ?? 0 }}</dd>
              </div>
            </dl>
          </div>
        </div>

        {{-- Right: Step-by-step algorithm --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-[14px] font-semibold text-slate-900 flex items-center gap-2">
              <i data-lucide="list-ordered" class="h-4 w-4 text-slate-500"></i>
              Step-by-step algorithm
            </h2>
            <span class="text-[11px] text-slate-500">
              Follow in order during simulation or real events.
            </span>
          </div>

          @if (!$protocol->steps || $protocol->steps->isEmpty())
            <p class="text-[13px] text-slate-500">
              No steps defined yet.
            </p>
          @else
            <ol class="space-y-3">
              @foreach ($protocol->steps->sortBy('step_no') as $step)
                <li class="flex items-start gap-3">
                  {{-- Number badge: own column, no overlap --}}
                  <div class="mt-1 flex h-6 w-6 items-center justify-center rounded-full bg-red-50 text-[12px] font-semibold text-red-700 border border-red-100">
                    {{ $step->step_no ?? $loop->iteration }}
                  </div>

                  {{-- Step card --}}
                  <div class="flex-1 rounded-xl border border-slate-100 bg-slate-50/70 px-3 py-2.5">
                    @if ($step->title)
                      <h3 class="text-[13px] font-semibold text-slate-900">
                        {{ $step->title }}
                      </h3>
                    @endif
                    @if ($step->description)
                      <p class="mt-1 text-[12px] text-slate-700">
                        {{ $step->description }}
                      </p>
                    @endif
                    @if ($step->expected_action)
                      <p class="mt-2 text-[12px] text-slate-700">
                        <span class="font-semibold text-slate-900">Expected action:</span>
                        {{ $step->expected_action }}
                      </p>
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

@include('partials.admin-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>

</body>
</html>
