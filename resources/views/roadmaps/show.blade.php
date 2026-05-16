{{-- resources/views/roadmaps/show.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $item->role }} · Roadmap · NurSync</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family:'Poppins', ui-sans-serif, system-ui, sans-serif; }

    /* --- same reveal animation as index --- */
    @keyframes slideInBottom { 0%{opacity:0;transform:translateY(30px)} 100%{opacity:1;transform:translateY(0)}}
    .reveal{opacity:0;transform:translateY(30px)}
    .reveal.in{animation:slideInBottom .6s cubic-bezier(.22,.8,.36,1) forwards}

    /* --- timeline look --- */
    .timeline { position: relative; padding-left: 28px; }
    .timeline:before {
      content: ""; position: absolute; left: 14px; top: 0; bottom: 0; width: 2px;
      background: rgba(203, 213, 225, .9); /* slate-300 */
    }
    .tl-section { position: relative; margin-bottom: 26px; }
    .tl-bullet {
      position: absolute; left: -2px; top: 2px; width: 28px; height: 28px;
      border-radius: 10px; background: #EEF2FF; display: flex; align-items: center; justify-content: center;
      box-shadow: 0 1px 0 rgba(0,0,0,.04);
    }
    .tl-card {
      border-radius: 18px; background: #fff; border: 1px solid rgba(226,232,240,.8);
      box-shadow: 0 1px 2px rgba(0,0,0,.04);
    }
    .list-tight li { margin-bottom: .35rem; }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  @php($active = 'roadmaps')
  @include('partials.sidebar')

  <section class="flex-1">
    <div class="container mx-auto px-6 lg:px-8 py-8 lg:py-10 space-y-6">

      {{-- Header --}}
      <header class="space-y-2 reveal" data-delay="0">
        <a href="{{ route('student.roadmaps.index') }}"
           class="inline-flex items-center gap-1 text-sm text-slate-600 hover:text-emerald-700">
          <i data-lucide="arrow-left" class="h-4 w-4"></i>
          Back to Roadmaps
        </a>

        <h1 class="text-[28px] lg:text-[32px] font-extrabold tracking-tight text-slate-900">
          {{ $item->role }}
        </h1>
        <p class="text-sm text-slate-500">
          {{ $item->category }} • {{ $item->career_level }}
        </p>
      </header>

      {{-- Overview --}}
      @if(!empty($item->description))
        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm p-6 md:p-8 reveal" data-delay="1">
          <div class="flex items-start gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-50">
              <i data-lucide="book-open-check" class="h-5 w-5 text-indigo-600"></i>
            </span>
            <div>
              <h2 class="text-[18px] md:text-[20px] font-semibold text-slate-900">Overview</h2>
              <p class="text-[13px] text-slate-600 leading-relaxed mt-1">{{ $item->description }}</p>
            </div>
          </div>
        </div>
      @endif

      {{-- Requirements (optional) --}}
      @if(!empty($item->requirements))
        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm p-6 md:p-8 reveal" data-delay="2">
          <div class="flex items-start gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-50">
              <i data-lucide="list-checks" class="h-5 w-5 text-amber-600"></i>
            </span>
            <div class="flex-1">
              <h2 class="text-[18px] md:text-[20px] font-semibold text-slate-900">Requirements</h2>
              <div class="mt-2 text-[13px] text-slate-700 whitespace-pre-line">{{ $item->requirements }}</div>
            </div>
          </div>
        </div>
      @endif

      {{-- Steps (JSON preferred; fallback to steps_text) --}}
      <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm p-6 md:p-8 reveal" data-delay="3">
        <div class="flex items-start gap-3 mb-4">
          <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50">
            <i data-lucide="route" class="h-5 w-5 text-emerald-600"></i>
          </span>
          <h2 class="text-[18px] md:text-[20px] font-semibold text-slate-900">Follow this Path</h2>
        </div>

        @php
          // Ensure $steps exists and is an array
          $stepsArr = (isset($steps) && is_array($steps)) ? $steps : [];

          // Determine if structured (sections with title/items)
          $hasStructured = false;
          foreach ($stepsArr as $sec) {
            if (is_array($sec) && (array_key_exists('title', $sec) || array_key_exists('items', $sec))) {
              $hasStructured = true; break;
            }
          }
        @endphp

        @if($hasStructured)
          <div class="timeline">
            @foreach($stepsArr as $i => $sec)
              @php
                $title = is_array($sec) ? ($sec['title'] ?? null) : null;
                $items = is_array($sec) ? ($sec['items'] ?? []) : [];
                $delay = 4 + $i;
              @endphp

              <section class="tl-section reveal" data-delay="{{ $delay }}">
                <div class="tl-bullet">
                  <i data-lucide="book-open" class="h-4 w-4 text-indigo-600"></i>
                </div>

                <div class="tl-card p-5 md:p-6 ml-3">
                  @if($title)
                    <h3 class="text-slate-900 font-semibold text-[16px] md:text-[17px] mb-1">{{ $title }}</h3>
                    <p class="text-slate-400 text-[12px] mb-3">Learn and practice the following:</p>
                  @endif

                  @if(is_array($items) && count($items))
                    <ul class="list-tight text-[13px] text-slate-700">
                      @foreach($items as $it)
                        <li class="flex gap-2 items-start">
                          <i data-lucide="check-circle-2" class="mt-[2px] h-4 w-4"></i>
                          <span>{{ is_array($it) ? ($it['text'] ?? json_encode($it)) : $it }}</span>
                        </li>
                      @endforeach
                    </ul>
                  @endif
                </div>
              </section>
            @endforeach
          </div>

        @elseif(!empty($item->steps_text))
          {{-- Fallback: plain text steps (newline separated) --}}
          @php $rows = preg_split('/\r\n|\r|\n/', trim($item->steps_text)); @endphp
          <div class="timeline">
            @foreach($rows as $i => $row)
              <section class="tl-section reveal" data-delay="{{ 4 + $i }}">
                <div class="tl-bullet">
                  <i data-lucide="book-open" class="h-4 w-4 text-indigo-600"></i>
                </div>
                <div class="tl-card p-5 md:p-6 ml-3">
                  <p class="text-[13px] text-slate-700">{{ $row }}</p>
                </div>
              </section>
            @endforeach
          </div>

        @else
          <p class="text-[13px] text-slate-600">No steps provided for this roadmap yet.</p>
        @endif
      </div>
    </div>
  </section>
</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();

  // keep the same reveal behavior
  const cards = document.querySelectorAll('.reveal');
  const io = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const el = entry.target;
        const delay = Number(el.getAttribute('data-delay') || 0);
        el.style.animationDelay = (0.06 * delay) + 's';
        el.classList.add('in');
        io.unobserve(el);
      }
    });
  }, { threshold: 0.08 });
  cards.forEach(c => io.observe(c));
</script>
</body>
</html>
