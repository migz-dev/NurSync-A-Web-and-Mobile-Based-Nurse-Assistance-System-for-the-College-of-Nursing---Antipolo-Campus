<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>{{ $experience->title }} · Clinical Experience · NurSync (CI)</title>

  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body { font-family:'Poppins', ui-sans-serif, system-ui }
    @keyframes slide-in-up {
      from { transform:translateY(8px); opacity:0 }
      to   { transform:translateY(0);   opacity:1 }
    }
    .animate-card-in {
      animation:slide-in-up .35s ease-out both;
      will-change:transform, opacity;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Sidebar --}}
  @include('partials.instructor-sidebar', ['active' => 'my_clinical_experience'])

  <section class="flex-1 min-w-0">
    <div class="container mx-auto px-8 py-10 space-y-10">

      {{-- Back + breadcrumb + (Edit) --}}
      <div class="flex items-center justify-between gap-3 mb-2">

        <div class="flex items-center gap-3">
          <button onclick="history.back()"
                  class="inline-flex items-center justify-center h-9 w-9 rounded-full border border-slate-300 bg-white hover:bg-slate-100">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
          </button>

          <span class="text-[13px] text-slate-500">
            My Clinical Experience / {{ $experience->title }}
          </span>
        </div>

        {{-- CI-only action --}}
        <a href="{{ route('faculty.instructor.experiences.edit', $experience) }}"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-[13px] text-slate-700 hover:bg-slate-50">
          <i data-lucide="edit-3" class="h-4 w-4"></i>
          Edit
        </a>
      </div>

      @php
        // Status chip for potential use (optional)
        $status = $experience->status;
        $statusLabel = ucfirst($status ?? 'draft');
        $statusClass = match($status) {
          'published' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
          'draft'     => 'bg-slate-50 text-slate-700 border-slate-200',
          'archived'  => 'bg-amber-50 text-amber-700 border-amber-200',
          default     => 'bg-slate-50 text-slate-700 border-slate-200',
        };

        // Hero media selection (CI logic but used in student-style layout)
        $attachments  = $experience->attachments ?? collect();
        $primaryMedia = $attachments->sortByDesc('is_primary')->first();
        $otherMedia   = $attachments->when($primaryMedia, fn($c) => $c->where('id', '!=', $primaryMedia->id))
                                    ?? collect();
      @endphp

      {{-- Title + meta (mirroring student layout) --}}
      <header class="space-y-3 animate-card-in">
        <h1 class="text-[28px] sm:text-[32px] font-extrabold text-slate-900 leading-tight">
          {{ $experience->title }}
        </h1>

        <div class="flex flex-wrap items-center gap-3 text-[13px] text-slate-600">

          {{-- Ward --}}
          @if($experience->ward)
            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1">
              <i data-lucide="hospital" class="h-3.5 w-3.5"></i>
              {{ $experience->ward }}
            </span>
          @endif

          {{-- Author (CI) --}}
          <span class="inline-flex items-center gap-1.5">
            <i data-lucide="user" class="h-3.5 w-3.5"></i>
            {{ $experience->faculty?->display_name ?? 'Clinical Instructor' }}
          </span>

          {{-- Updated --}}
          <span class="inline-flex items-center gap-1.5">
            <i data-lucide="clock" class="h-3.5 w-3.5"></i>
            Updated {{ $experience->updated_at->diffForHumans() }}
          </span>

          {{-- Optional status chip for CI (small, subtle) --}}
          @if($status)
            <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-[12px] {{ $statusClass }}">
              <i data-lucide="{{ $status === 'published' ? 'check-circle-2' : ($status === 'archived' ? 'archive' : 'file-pen-line') }}"
                 class="h-3 w-3"></i>
              {{ $statusLabel }}
            </span>
          @endif
        </div>
      </header>

      {{-- Primary media preview (mirroring student size/feel) --}}
      @if($primaryMedia)
        <div class="rounded-2xl overflow-hidden border border-slate-200 shadow-sm animate-card-in bg-slate-50">
          @if($primaryMedia->file_type === 'image')
            <img src="{{ Storage::url($primaryMedia->storage_path) }}"
                 alt="{{ $primaryMedia->caption ?? $primaryMedia->original_name ?? 'Photo' }}"
                 class="w-full h-auto object-cover">
          @else
            <video controls class="w-full rounded-none bg-black">
              <source src="{{ Storage::url($primaryMedia->storage_path) }}">
            </video>
          @endif

          @if($primaryMedia->caption)
            <div class="px-4 py-2 text-[12px] text-slate-500 bg-slate-50 border-t border-slate-200">
              {{ $primaryMedia->caption }}
            </div>
          @endif
        </div>
      @endif

      {{-- Accordions block (Summary / Full Story / Key Takeaways / Additional Media) --}}
      <section class="space-y-4">

        {{-- Summary --}}
        <details class="group rounded-2xl bg-white border border-slate-200 shadow-sm animate-card-in"
                 @if($experience->summary) open @endif>
          <summary class="flex items-center justify-between gap-3 px-6 py-4 cursor-pointer select-none">
            <div class="flex items-center gap-2">
              <i data-lucide="align-left" class="h-5 w-5 text-slate-700"></i>
              <span class="text-[18px] font-semibold text-slate-900">
                Summary
              </span>
            </div>
            <i data-lucide="chevron-down"
               class="h-5 w-5 text-slate-500 transition-transform duration-200 group-open:rotate-180"></i>
          </summary>

          <div class="px-6 pb-5 text-[15px] text-slate-700 leading-relaxed">
            @if($experience->summary)
              {{ $experience->summary }}
            @else
              <span class="italic text-slate-500 text-[14px]">
                No summary written yet. Add a short overview of the case and what made it significant.
              </span>
            @endif
          </div>
        </details>

        {{-- Full Story --}}
        <details class="group rounded-2xl bg-white border border-slate-200 shadow-sm animate-card-in">
          <summary class="flex items-center justify-between gap-3 px-6 py-4 cursor-pointer select-none">
            <div class="flex items-center gap-2">
              <i data-lucide="newspaper" class="h-5 w-5 text-slate-700"></i>
              <span class="text-[18px] font-semibold text-slate-900">
                Full Story
              </span>
            </div>
            <i data-lucide="chevron-down"
               class="h-5 w-5 text-slate-500 transition-transform duration-200 group-open:rotate-180"></i>
          </summary>

          <div class="px-6 pb-6 prose prose-slate max-w-none text-[15px] leading-relaxed whitespace-pre-line">
            @if($experience->story)
              {{ $experience->story }}
            @else
              <span class="italic text-slate-500 text-[14px]">
                The full narrative for this clinical experience has not been written yet.
                Use this space to tell the story in your own words.
              </span>
            @endif
          </div>
        </details>

        {{-- Key Takeaways --}}
        @php
          $takeaways = $experience->key_takeaways
            ? array_filter(array_map('trim', explode("\n", $experience->key_takeaways)))
            : [];
        @endphp

        @if($experience->key_takeaways || !empty($takeaways))
          <details class="group rounded-2xl bg-white border border-slate-200 shadow-sm animate-card-in">
            <summary class="flex items-center justify-between gap-3 px-6 py-4 cursor-pointer select-none">
              <div class="flex items-center gap-2">
                <i data-lucide="sparkles" class="h-5 w-5 text-slate-700"></i>
                <span class="text-[18px] font-semibold text-slate-900">
                  Key Takeaways
                </span>
              </div>
              <i data-lucide="chevron-down"
                 class="h-5 w-5 text-slate-500 transition-transform duration-200 group-open:rotate-180"></i>
            </summary>

            <div class="px-6 pb-6 flex flex-wrap gap-2 text-[13px]">
              @if(!empty($takeaways))
                @foreach($takeaways as $t)
                  <span class="rounded-full border border-emerald-200 bg-emerald-50 text-emerald-700 px-3 py-1">
                    {{ $t }}
                  </span>
                @endforeach
              @else
                <span class="italic text-slate-500 text-[14px]">
                  Add a few short phrases or points you'd like student nurses to remember from this case.
                </span>
              @endif
            </div>
          </details>
        @endif

        {{-- Additional Media (accordion, same style as student but using $otherMedia) --}}
        @if($attachments->count() > 0)
          <details class="group rounded-2xl bg-white border border-slate-200 shadow-sm animate-card-in">
            <summary class="flex items-center justify-between gap-3 px-6 py-4 cursor-pointer select-none">
              <div class="flex items-center gap-2">
                <i data-lucide="paperclip" class="h-5 w-5 text-slate-700"></i>
                <span class="text-[18px] font-semibold text-slate-900">
                  Additional Media
                </span>
              </div>
              <i data-lucide="chevron-down"
                 class="h-5 w-5 text-slate-500 transition-transform duration-200 group-open:rotate-180"></i>
            </summary>

            <div class="px-6 pb-6">
              @if($otherMedia->isEmpty())
                <p class="text-[13px] text-slate-500">
                  No extra photos or clips beyond the primary media. You can attach more visuals when editing this experience.
                </p>
              @else
                <p class="text-[12px] text-slate-500 mb-3">
                  Short clips or extra visuals that you can show when retelling this story to student nurses.
                </p>

                <div class="grid gap-4 sm:grid-cols-2">
                  @foreach($otherMedia as $file)
                    <div class="border border-slate-200 rounded-xl bg-slate-50 p-3">
                      <div class="aspect-video rounded-lg overflow-hidden bg-black">
                        @if($file->file_type === 'image')
                          <img src="{{ Storage::url($file->storage_path) }}"
                               class="w-full h-full object-cover">
                        @else
                          <video controls class="w-full h-full">
                            <source src="{{ Storage::url($file->storage_path) }}">
                          </video>
                        @endif
                      </div>
                      @if($file->caption)
                        <p class="mt-2 text-[12px] text-slate-600 italic">
                          {{ $file->caption }}
                        </p>
                      @endif
                    </div>
                  @endforeach
                </div>
              @endif
            </div>
          </details>
        @endif

      </section>

    </div>
  </section>
</main>

@includeIf('partials.faculty-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>

</body>
</html>
