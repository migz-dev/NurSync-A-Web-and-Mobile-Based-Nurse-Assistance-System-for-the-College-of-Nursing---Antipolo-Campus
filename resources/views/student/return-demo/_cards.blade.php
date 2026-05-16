<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
  @forelse($skills as $p)
    @php
      // ---- Tags (robust decode like we discussed) ----
      $tagsRaw = $p->tags_json ?? [];

      if (is_array($tagsRaw)) {
          $tags = $tagsRaw;
      } elseif (is_string($tagsRaw)) {
          $decoded = json_decode($tagsRaw, true);
          $tags = is_array($decoded) ? $decoded : [$tagsRaw];
      } else {
          $tags = [];
      }

      // Handle case where it's ["[\"a\",\"b\"]"]
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

      $hasPdf  = filled($p->pdf_path);
      $hasVid  = filled($p->video_url) || filled($p->video_path);
      $ward    = $p->clinical_wards ?? '';
      $wardLbl = $ward ?: 'Unassigned';

      // Ward chip colors (same as CI)
      $wardChip = function (?string $w) {
        return match ($w) {
          'Community Health (CHN)'              => 'bg-emerald-50 text-emerald-700',
          'OB Ward', 'Delivery Room (DR)', 'Nursery'
                                              => 'bg-pink-50 text-pink-700',
          'Pediatrics (PEDIA)'                  => 'bg-sky-50 text-sky-700',
          'Medical-Surgical (MS)'               => 'bg-slate-50 text-slate-700',
          'ICU'                                 => 'bg-rose-50 text-rose-700',
          'Oncology'                            => 'bg-fuchsia-50 text-fuchsia-700',
          'Isolation Unit'                      => 'bg-amber-50 text-amber-700',
          'Endocrine Unit'                      => 'bg-cyan-50 text-cyan-700',
          'Neurology Unit'                      => 'bg-indigo-50 text-indigo-700',
          'Psychiatric (PSYCH)'                 => 'bg-violet-50 text-violet-700',
          'Emergency Room (ER)'                 => 'bg-orange-50 text-orange-700',
          'Operating Room (OR)'                 => 'bg-green-50 text-green-700',
          'Trauma Unit'                         => 'bg-red-50 text-red-700',
          'Disaster Response / Community Field' => 'bg-yellow-50 text-yellow-700',
          default                               => 'bg-slate-50 text-slate-700',
        };
      };

      // Colorful tag pills (same palette as CI)
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

    <article
      class="js-card flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow"
    >
      {{-- Header (matches CI layout, large, with fixed text block height) --}}
      <header class="flex items-start justify-between gap-4">
        <div class="flex-1">
          {{-- This block gets a min-height so meta row + tags align across cards --}}
          <div class="min-h-[120px] flex flex-col gap-1.5">
            <h2 class="text-[16px] font-semibold text-slate-900 leading-snug flex items-center gap-3">
              <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50">
                <i data-lucide="stethoscope" class="h-5 w-5 text-slate-700"></i>
              </span>
              {{ $p->title }}
            </h2>

            @if(filled($p->description))
              <p class="text-[13px] text-slate-600 line-clamp-3">
                {{ \Illuminate\Support\Str::limit($p->description, 180) }}
              </p>
            @endif

            <div class="mt-1.5 flex flex-wrap items-center gap-2.5 text-[12px]">
              @if ($ward)
                <span class="inline-flex items-center rounded-full {{ $wardChip($ward) }} px-2.5 py-0.5">
                  <i data-lucide="hospital" class="h-4 w-4 mr-1"></i>
                  {{ $wardLbl }}
                </span>
              @endif
            </div>
          </div>
        </div>

        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
          <i data-lucide="stethoscope" class="h-5 w-5"></i>
        </span>
      </header>

      {{-- Meta row (large) --}}
      <div class="mt-4 flex items-center justify-between text-[12px] text-slate-500">
        <span class="flex flex-wrap items-center gap-2.5">
          @if($hasVid)
            <span class="inline-flex items-center gap-1.5">
              <i data-lucide="video" class="h-4 w-4"></i> Demo
            </span>
          @endif
          @if($hasPdf)
            <span class="inline-flex items-center gap-1.5">
              <i data-lucide="file-text" class="h-4 w-4"></i> PDF
            </span>
          @endif
          @if(isset($p->steps_count))
            <span class="inline-flex items-center gap-1.5">
              <i data-lucide="list-checks" class="h-4 w-4"></i> {{ $p->steps_count }} steps
            </span>
          @endif
        </span>

        @if(isset($p->updated_at))
          <span class="whitespace-nowrap">
            Updated {{ $p->updated_at?->diffForHumans() ?? '—' }}
          </span>
        @endif
      </div>

      {{-- Tags (large pills) --}}
      @if(!empty($tags))
        <div class="mt-3 flex flex-wrap items-center gap-2 text-[12px]">
          @foreach($tags as $t)
            @php $label = mb_strtolower($t); @endphp
            <span class="rounded-full border px-2.5 py-0.5 {{ $tagClass($t) }}">
              {{ $label }}
            </span>
          @endforeach
        </div>
      @endif

      {{-- Actions (large button) --}}
      <div class="mt-auto pt-5 border-t border-slate-100 flex flex-wrap items-center gap-2.5">
        <a href="{{ route('student.return_demo.show', $p->slug) }}"
           class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 py-2 text-[13px] font-medium text-slate-800 hover:bg-slate-50">
          <i data-lucide="eye" class="h-4 w-4"></i>
          View Procedure
        </a>
      </div>
    </article>
  @empty
    <div class="md:col-span-2 xl:col-span-3">
      <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
        <p class="text-sm text-slate-500">
          No procedures found. Try adjusting your search or filters.
        </p>
      </div>
    </div>
  @endforelse
</div>
