{{-- resources/views/faculty/instructor/board_exam/show.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Board Exam Analytics · NurSync (CI)</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; }

    @keyframes slide-in-up {
      from { transform: translateY(10px); opacity: 0; }
      to   { transform: translateY(0);     opacity: 1; }
    }
    .animate-card-in {
      animation: slide-in-up .35s ease-out both;
      will-change: transform, opacity;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Sidebar – CI Instructor Mode --}}
  @include('partials.instructor-sidebar', ['active' => 'board_exam_bank'])

  @php
    /** @var \App\Models\BoardExamQuestion $question */
    /** @var \Illuminate\Support\Collection|\App\Models\BoardExamQuestion[] $examQuestions */

    $examTitle   = $question->exam_title ?: 'Untitled exam set';
    $examWard    = $question->category ?: 'Uncategorized ward / area';
    $questionCnt = $examQuestions?->count() ?? 0;

    // Summary stats (controller can override these; here are safe defaults)
    $summary = $summary ?? [
        'total_attempts'       => $totalAttempts       ?? 0,
        'avg_score'            => $avgScore            ?? 0,
        'median_score'         => $medianScore         ?? 0,
        'highest_score'        => $highestScore        ?? 0,
        'lowest_score'         => $lowestScore         ?? 0,
        'avg_time_minutes'     => $avgTimeMinutes      ?? 0,
        'avg_items_answered'   => $avgItemsAnswered    ?? $questionCnt,
    ];

    $hasAttempts = ($summary['total_attempts'] ?? 0) > 0;

    // Buckets for score distribution – controller may pass in $scoreBuckets
    // Example structure: [['label' => '0–10', 'value' => 2], ...]
    $scoreBuckets = $scoreBuckets ?? [];

    // Performance by ward/area (if you log where students are rotating)
    // Example structure: [['label' => 'MS', 'value' => 75], ...] as average score %
    $wardPerformance = $wardPerformance ?? [];

    // Difficulty breakdown: count of attempts or questions answered by difficulty
    // Example: ['easy' => 10, 'moderate' => 25, 'difficult' => 5]
    $difficultyBreakdown = $difficultyBreakdown ?? [
        'easy'      => $difficultyBreakdown['easy']      ?? 0,
        'moderate'  => $difficultyBreakdown['moderate']  ?? 0,
        'difficult' => $difficultyBreakdown['difficult'] ?? 0,
    ];

    // Item-level stats for table
    // expected structure per item:
    // [
    //   'question_id'   => int,
    //   'no'            => int,
    //   'short_stem'    => string,
    //   'correct_pct'   => float,  // 0–100
    //   'avg_time_sec'  => float|null,
    //   'difficulty'    => 'easy'|'moderate'|'difficult'|null,
    // ]
    $itemStats = $itemStats ?? [];
  @endphp

  {{-- Main content --}}
  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-8 py-12 space-y-8">

      {{-- Heading --}}
      <header class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between animate-card-in">
        <div class="flex items-start gap-3">
          <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
            <i data-lucide="bar-chart-3" class="h-4 w-4"></i>
          </span>
          <div>
            <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
              Exam Analytics
            </h1>
            <p class="text-[13px] text-slate-500 mt-1">
              Performance report for <span class="font-semibold text-slate-800">{{ $examTitle }}</span>.
              See how student nurses scored, which wards are struggling, and which items need revision.
            </p>
            <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px]">
              <span class="inline-flex items-center rounded-full border border-sky-200 bg-sky-50 px-2.5 py-0.5 text-sky-700">
                <i data-lucide="notebook-text" class="h-3 w-3 mr-1"></i>
                {{ $examTitle }}
              </span>
              <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2.5 py-0.5 text-slate-700">
                <i data-lucide="map" class="h-3 w-3 mr-1"></i>
                {{ $examWard }}
              </span>
              <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-emerald-700">
                <i data-lucide="list-ordered" class="h-3 w-3 mr-1"></i>
                {{ $questionCnt }} questions in this set
              </span>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <a href="{{ route('faculty.instructor.board_exam.index') }}"
             class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3.5 py-2.5 text-[13px] font-medium text-slate-700 hover:bg-slate-50">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            Back to Question Bank
          </a>
        </div>
      </header>

      {{-- Summary cards --}}
      <section class="grid gap-4 md:grid-cols-3 xl:grid-cols-4 animate-card-in">
        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-[11px] text-slate-500">Total attempts</p>
              <p class="mt-1 text-[20px] font-semibold text-slate-900">
                {{ number_format($summary['total_attempts'] ?? 0) }}
              </p>
            </div>
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-50">
              <i data-lucide="users" class="h-4 w-4 text-slate-700"></i>
            </span>
          </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-[11px] text-slate-500">Average score</p>
              <p class="mt-1 text-[20px] font-semibold text-slate-900">
                {{ number_format($summary['avg_score'] ?? 0, 1) }}%
              </p>
            </div>
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50">
              <i data-lucide="trending-up" class="h-4 w-4 text-emerald-600"></i>
            </span>
          </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-[11px] text-slate-500">Median score</p>
              <p class="mt-1 text-[20px] font-semibold text-slate-900">
                {{ number_format($summary['median_score'] ?? 0, 1) }}%
              </p>
            </div>
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-50">
              <i data-lucide="activity" class="h-4 w-4 text-slate-700"></i>
            </span>
          </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-[11px] text-slate-500">Avg time per exam</p>
              <p class="mt-1 text-[20px] font-semibold text-slate-900">
                {{ number_format($summary['avg_time_minutes'] ?? 0, 1) }} min
              </p>
            </div>
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-50">
              <i data-lucide="clock" class="h-4 w-4 text-slate-700"></i>
            </span>
          </div>
        </article>
      </section>

      @if(!$hasAttempts)
        {{-- No attempts yet --}}
        <section class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center animate-card-in">
          <p class="text-sm text-slate-500">
            No students have taken this exam yet.
            Once student nurses start answering this set, you’ll see score distributions, ward performance,
            and item-by-item analytics here.
          </p>
        </section>
      @else

      {{-- Graphs row --}}
      <section class="grid gap-6 lg:grid-cols-3 animate-card-in">
        {{-- Score distribution --}}
        <article class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
          <header class="flex items-center justify-between mb-3">
            <div>
              <h2 class="text-[15px] font-semibold text-slate-900">Score distribution</h2>
              <p class="text-[11px] text-slate-500">
                See how scores are spread out across all attempts.
              </p>
            </div>
          </header>
          <div class="h-64">
            <canvas id="scoreDistributionChart"></canvas>
          </div>
        </article>

        {{-- Difficulty breakdown --}}
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
          <header class="flex items-center justify-between mb-3">
            <div>
              <h2 class="text-[15px] font-semibold text-slate-900">Difficulty mix</h2>
              <p class="text-[11px] text-slate-500">
                Composition of this exam based on difficulty tags.
              </p>
            </div>
          </header>
          <div class="h-64">
            <canvas id="difficultyChart"></canvas>
          </div>
        </article>
      </section>

      {{-- Ward / area performance --}}
      <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-3 animate-card-in">
        <header class="flex items-center justify-between">
          <div>
            <h2 class="text-[15px] font-semibold text-slate-900">Performance by ward / area</h2>
            <p class="text-[11px] text-slate-500">
              Compare average scores across different clinical rotations (where available).
            </p>
          </div>
        </header>
        <div class="h-72">
          <canvas id="wardPerformanceChart"></canvas>
        </div>
      </section>

      {{-- Item-level analytics --}}
      <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-4 animate-card-in">
        <header class="flex items-center justify-between">
          <div>
            <h2 class="text-[15px] font-semibold text-slate-900">Item-level analysis</h2>
            <p class="text-[11px] text-slate-500">
              Identify weak items, overly difficult questions, and content that needs remediation.
            </p>
          </div>
        </header>

        @if(empty($itemStats))
          <p class="text-[13px] text-slate-500">
            Item-level analytics will appear here once you start logging per-question results for this exam.
          </p>
        @else
          <div class="overflow-x-auto">
            <table class="min-w-full text-left text-[13px]">
              <thead>
              <tr class="border-b border-slate-200 text-[11px] uppercase tracking-wide text-slate-500">
                <th class="py-2 pr-4">Item</th>
                <th class="py-2 pr-4">Question stem</th>
                <th class="py-2 pr-4">Correct %</th>
                <th class="py-2 pr-4">Avg time</th>
                <th class="py-2 pr-4">Difficulty</th>
              </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
              @foreach($itemStats as $stat)
                @php
                  $difficulty = $stat['difficulty'] ?? null;
                  $difficultyLabel = $difficulty ? ucfirst($difficulty) : '—';

                  $diffClass = match($difficulty) {
                    'easy'      => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'moderate'  => 'bg-amber-50 text-amber-800 border-amber-200',
                    'difficult' => 'bg-rose-50 text-rose-700 border-rose-200',
                    default     => 'bg-slate-50 text-slate-700 border-slate-200',
                  };
                @endphp
                <tr class="text-[13px] text-slate-700">
                  <td class="py-2 pr-4 align-top font-medium text-slate-800">
                    Q{{ $stat['no'] ?? '-' }}
                  </td>
                  <td class="py-2 pr-4 align-top">
                    <span class="line-clamp-3">
                      {{ $stat['short_stem'] ?? '—' }}
                    </span>
                  </td>
                  <td class="py-2 pr-4 align-top">
                    {{ isset($stat['correct_pct']) ? number_format($stat['correct_pct'], 1) . '%' : '—' }}
                  </td>
                  <td class="py-2 pr-4 align-top">
                    @if(isset($stat['avg_time_sec']) && $stat['avg_time_sec'] !== null)
                      {{ number_format($stat['avg_time_sec'] / 60, 1) }} min
                    @else
                      —
                    @endif
                  </td>
                  <td class="py-2 pr-4 align-top">
                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[11px] {{ $diffClass }}">
                      {{ $difficultyLabel }}
                    </span>
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </section>

      @endif {{-- /hasAttempts --}}
    </div>
  </section>
</main>

@includeIf('partials.faculty-footer')
@includeWhen(!View::exists('partials.faculty-footer'), 'partials.student-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  lucide.createIcons();

  const hasAttempts = @json($hasAttempts);

  if (hasAttempts) {
    const scoreBuckets      = @json($scoreBuckets);
    const wardPerformance   = @json($wardPerformance);
    const difficultyData    = @json($difficultyBreakdown);

    // ---- Score distribution (bar) ----
    const scoreCtx = document.getElementById('scoreDistributionChart');
    if (scoreCtx && scoreBuckets && scoreBuckets.length) {
      new Chart(scoreCtx, {
        type: 'bar',
        data: {
          labels: scoreBuckets.map(b => b.label),
          datasets: [{
            label: 'Number of attempts',
            data: scoreBuckets.map(b => b.value),
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              ticks: { precision: 0 }
            }
          }
        }
      });
    }

    // ---- Difficulty breakdown (doughnut) ----
    const diffCtx = document.getElementById('difficultyChart');
    if (diffCtx) {
      const diffLabels = ['Easy', 'Moderate', 'Difficult'];
      const diffValues = [
        difficultyData.easy      ?? 0,
        difficultyData.moderate  ?? 0,
        difficultyData.difficult ?? 0,
      ];

      new Chart(diffCtx, {
        type: 'doughnut',
        data: {
          labels: diffLabels,
          datasets: [{
            data: diffValues,
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'bottom' }
          }
        }
      });
    }

    // ---- Ward / Area performance (bar) ----
    const wardCtx = document.getElementById('wardPerformanceChart');
    if (wardCtx && wardPerformance && wardPerformance.length) {
      new Chart(wardCtx, {
        type: 'bar',
        data: {
          labels: wardPerformance.map(w => w.label),
          datasets: [{
            label: 'Average score (%)',
            data: wardPerformance.map(w => w.value),
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              max: 100,
              ticks: { callback: v => v + '%' }
            }
          }
        }
      });
    }
  }
</script>
</body>
</html>
