<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Feedback · NurSync</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style> body{font-family:'Poppins',ui-sans-serif,system-ui,sans-serif} </style>
</head>
<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">

  {{-- Sidebar --}}
  @include('partials.sidebar', ['active' => 'feedback'])

  {{-- Main --}}
  <section class="flex-1 px-8 py-10">
    <!-- Title -->
    <div class="flex items-center gap-3">
      <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-blue-50 text-blue-600">
        <i data-lucide="message-square" class="h-5 w-5"></i>
      </span>
      <h1 class="text-2xl font-bold">Feedback</h1>
    </div>
    <p class="text-sm text-slate-500 mt-1">
      Your feedback helps us improve NurSync. We appreciate you taking the time to share your thoughts.
    </p>

    <!-- Content grid -->
    <div class="mt-6 grid lg:grid-cols-[1fr,300px] gap-6">
      <!-- Left: form card -->
      <div class="rounded-2xl border border-slate-200 bg-white p-6">
        <!-- Category tiles -->
        <div class="grid sm:grid-cols-2 gap-4">
          <!-- Suggestion (active) -->
          <button type="button"
                  class="feedback-type group rounded-xl border-2 border-blue-500/60 bg-blue-50 ring-1 ring-blue-200 p-4 text-left">
            <div class="flex items-center gap-2">
              <i data-lucide="lightbulb" class="h-5 w-5 text-blue-600"></i>
              <span class="font-semibold text-slate-800">Suggestion</span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Share your ideas on how we can improve</p>
          </button>

          <button type="button"
                  class="feedback-type rounded-xl border border-slate-200 hover:border-slate-300 p-4 text-left">
            <div class="flex items-center gap-2">
              <i data-lucide="bug" class="h-5 w-5 text-slate-500"></i>
              <span class="font-semibold text-slate-800">Bug Report</span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Report issues or unexpected behavior</p>
          </button>

          <button type="button"
                  class="feedback-type rounded-xl border border-slate-200 hover:border-slate-300 p-4 text-left">
            <div class="flex items-center gap-2">
              <i data-lucide="sparkles" class="h-5 w-5 text-slate-500"></i>
              <span class="font-semibold text-slate-800">Feature Request</span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Request new features or functionality</p>
          </button>

          <button type="button"
                  class="feedback-type rounded-xl border border-slate-200 hover:border-slate-300 p-4 text-left">
            <div class="flex items-center gap-2">
              <i data-lucide="info" class="h-5 w-5 text-slate-500"></i>
              <span class="font-semibold text-slate-800">Other</span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Any other feedback you’d like to share</p>
          </button>
        </div>

        <!-- Textarea -->
        <div class="mt-6">
          <label class="block text-sm font-medium text-slate-700 mb-2">Your Feedback</label>
          <textarea rows="7"
                    placeholder="Share your thoughts, suggestions, or report issues…"
                    class="w-full rounded-xl border border-slate-200 focus:border-slate-300 focus:ring-0 p-4 text-[13px] placeholder:text-slate-400"></textarea>
        </div>

        <!-- Submit -->
        <div class="mt-4 flex justify-end">
          <button type="button"
                  class="rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium px-4 py-2">
            Submit Feedback
          </button>
        </div>
      </div>

      <!-- Right: recent feedback (placeholder) -->
      <aside>
        <h3 class="text-sm font-medium text-slate-700">Recent Feedback</h3>
        <div class="mt-3 space-y-3">
          <div class="rounded-xl border border-slate-200 bg-white p-3">
            <div class="text-[13px] text-slate-700 line-clamp-2">
              “Loving the clean UI. A dark mode toggle on the exam page would be awesome!”
            </div>
            <div class="mt-2 text-[11px] text-slate-400">2d ago</div>
          </div>
          <div class="rounded-xl border border-slate-200 bg-white p-3">
            <div class="text-[13px] text-slate-700 line-clamp-2">
              “Found a small bug in the leaderboard when sorting by score.”
            </div>
            <div class="mt-2 text-[11px] text-slate-400">5d ago</div>
          </div>
        </div>
      </aside>
    </div>
  </section>
</main>

{{-- Footer --}}
@include('partials.student-footer')

<!-- Lucide -->
<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();

  // (Optional) tiny toggle to highlight a selected type – purely visual for design mode
  const types = document.querySelectorAll('.feedback-type');
  types.forEach(btn => {
    btn.addEventListener('click', () => {
      types.forEach(b => b.className = b.className
        .replace(' border-blue-500/60', '')
        .replace(' bg-blue-50', '')
        .replace(' ring-1 ring-blue-200', '')
        .replace(' border-2', '')
        .replace(' border ', ' border ')
      );
      btn.classList.add('border-2','border-blue-500/60','bg-blue-50','ring-1','ring-blue-200');
    });
  });
</script>
</body>
</html>
