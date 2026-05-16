<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>{{ $mode === 'create' ? 'New Drug' : 'Edit Drug' }} · NurSync (CI)</title>

  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body{font-family:'Poppins',ui-sans-serif,system-ui,sans-serif;}
    /* Sticky actions on wide screens */
    @media (min-width:1024px){
      .sticky-actions{position:sticky; top:16px}
    }
  </style>
</head>
<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  @include('partials.faculty-sidebar', ['active'=>'drug_guide'])

  <section class="flex-1">
    <div class="container mx-auto px-8 py-12 space-y-6">

      {{-- Header --}}
      @php
        $canManage = $canManage ?? (auth('faculty')->user()?->is_admin ?? false);
      @endphp
      <div class="rounded-xl border border-slate-200 bg-white p-5">
        <div class="flex items-start justify-between gap-4">
          <div>
            <h1 class="text-[24px] sm:text-[28px] font-extrabold text-slate-900">
              {{ $mode === 'create' ? 'Create Drug Monograph' : 'Edit Drug Monograph' }}
            </h1>
            <p class="text-sm text-slate-500 mt-1">Generic, brands, class, monograph, doses, and interactions.</p>
          </div>

          <div class="sticky-actions flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
            <a href="{{ route('faculty.drug_guide.index') }}"
               class="rounded-lg border px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 inline-flex items-center gap-2">
              <i data-lucide="arrow-left" class="h-4 w-4"></i> Back to list
            </a>

            {{-- Enrich from RxNav (edit mode only) --}}
            @if($mode==='edit' && $canManage && isset($drug))
              <form method="POST" action="{{ route('faculty.drug_guide.enrich', $drug->id) }}"
                    class="rounded-lg border px-2 py-1.5 text-xs text-slate-700 bg-white flex items-center gap-2">
                @csrf
                <label class="inline-flex items-center gap-1">
                  <input type="checkbox" name="overwrite_brands" value="1" class="rounded border-slate-300">
                  Overwrite brands
                </label>
                <label class="inline-flex items-center gap-1">
                  <input type="checkbox" name="overwrite_interactions" value="1" class="rounded border-slate-300">
                  Overwrite interactions
                </label>
                <button class="rounded-md bg-slate-900 px-3 py-1.5 text-white font-medium inline-flex items-center gap-2">
                  <i data-lucide="sparkles" class="h-4 w-4"></i> Enrich from RxNav
                </button>
              </form>
            @endif
          </div>
        </div>
      </div>

      {{-- Flash alerts --}}
      @if(session('ok'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-emerald-800 text-sm">
          {{ session('ok') }}
        </div>
      @endif
      @if(session('err'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-rose-800 text-sm">
          {{ session('err') }}
        </div>
      @endif

      {{-- Validation errors --}}
      @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-rose-800 text-sm">
          <strong>Fix the following:</strong>
          <ul class="list-disc pl-5">
            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ $mode==='create'
        ? route('faculty.drug_guide.store')
        : route('faculty.drug_guide.update', $drug->id ?? 0) }}">
        @csrf
        @if($mode==='edit') @method('PUT') @endif

        {{-- Drug basics --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
          <h3 class="text-sm font-semibold text-slate-900">Drug Basics</h3>

          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="block text-xs text-slate-600 mb-1">Generic name</label>
              <input name="generic_name" value="{{ old('generic_name', $drug->generic_name ?? '') }}"
                     class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" required>
            </div>

            <div>
              <label class="block text-xs text-slate-600 mb-1">ATC / Class</label>
              <select name="atc_class" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                <option value="">—</option>
                @foreach($classes as $c)
                  <option value="{{ $c }}" @selected(old('atc_class', $drug->atc_class ?? '') === $c)>{{ $c }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block text-xs text-slate-600 mb-1">Brand names (comma-separated)</label>
              <input name="brand_names" value="{{ old('brand_names', isset($drug)? implode(', ', $drug->brand_names ?? []) : '') }}"
                     class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="e.g., Amoxil, Himox">
            </div>

            <div>
              <label class="block text-xs text-slate-600 mb-1">Pregnancy category</label>
              <input name="pregnancy_category" value="{{ old('pregnancy_category', $drug->pregnancy_category ?? '') }}"
                     class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="B, C, D">
            </div>
          </div>

          <div class="grid gap-4 sm:grid-cols-3">
            <div>
              <label class="block text-xs text-slate-600 mb-1">Lactation notes</label>
              <textarea name="lactation_notes" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" rows="2">{{ old('lactation_notes', $drug->lactation_notes ?? '') }}</textarea>
            </div>
            <div>
              <label class="block text-xs text-slate-600 mb-1">Renal adjustment notes</label>
              <textarea name="renal_adjust_notes" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" rows="2">{{ old('renal_adjust_notes', $drug->renal_adjust_notes ?? '') }}</textarea>
            </div>
            <div>
              <label class="block text-xs text-slate-600 mb-1">Hepatic adjustment notes</label>
              <textarea name="hepatic_adjust_notes" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" rows="2">{{ old('hepatic_adjust_notes', $drug->hepatic_adjust_notes ?? '') }}</textarea>
            </div>
          </div>

          {{-- RxCUI display (read-only) if available --}}
          @if(isset($drug) && !empty($drug->rxcui ?? null))
            <div class="grid gap-4 sm:grid-cols-3">
              <div>
                <label class="block text-xs text-slate-600 mb-1">RxCUI</label>
                <input value="{{ $drug->rxcui }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm bg-slate-50" readonly>
              </div>
            </div>
          @endif

          <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input type="checkbox" name="is_core" value="1" @checked(old('is_core', $drug->is_core ?? false)) class="rounded border-slate-300">
            Mark as core (offline set)
          </label>
        </div>

        {{-- Monograph --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
          <h3 class="text-sm font-semibold text-slate-900">Monograph</h3>
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="block text-xs text-slate-600 mb-1">Indications</label>
              <textarea name="indications" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">{{ old('indications', $drug->monograph->indications ?? '') }}</textarea>
            </div>
            <div>
              <label class="block text-xs text-slate-600 mb-1">Contraindications</label>
              <textarea name="contraindications" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">{{ old('contraindications', $drug->monograph->contraindications ?? '') }}</textarea>
            </div>
            <div>
              <label class="block text-xs text-slate-600 mb-1">Adverse effects</label>
              <textarea name="adverse_effects" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">{{ old('adverse_effects', $drug->monograph->adverse_effects ?? '') }}</textarea>
            </div>
            <div>
              <label class="block text-xs text-slate-600 mb-1">Mechanism</label>
              <textarea name="mechanism" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">{{ old('mechanism', $drug->monograph->mechanism ?? '') }}</textarea>
            </div>
          </div>

          <div class="grid gap-4 sm:grid-cols-3">
            <div>
              <label class="block text-xs text-slate-600 mb-1">Nursing responsibilities</label>
              <textarea name="nursing_responsibilities" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">{{ old('nursing_responsibilities', $drug->monograph->nursing_responsibilities ?? '') }}</textarea>
            </div>
            <div>
              <label class="block text-xs text-slate-600 mb-1">Patient teaching</label>
              <textarea name="patient_teaching" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">{{ old('patient_teaching', $drug->monograph->patient_teaching ?? '') }}</textarea>
            </div>
            <div>
              <label class="block text-xs text-slate-600 mb-1">Monitoring parameters</label>
              <textarea name="monitoring_params" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">{{ old('monitoring_params', $drug->monograph->monitoring_params ?? '') }}</textarea>
            </div>
          </div>

          <div>
            <label class="block text-xs text-slate-600 mb-1">References (separate with semicolons)</label>
            <input name="references" value="{{ old('references', isset($drug)? implode('; ', $drug->monograph->references ?? []) : '') }}"
                   class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="Davis’s Drug Guide; OpenFDA">
          </div>
        </div>

        {{-- Doses --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-900">Doses</h3>
            <button type="button" id="addDose" class="rounded-lg border px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Add dose</button>
          </div>

          <div id="dosesList" class="space-y-3">
            @php
              $dosesOld = old('doses', isset($drug)? $drug->doses->map(fn($d)=>[
                'population'=>$d->population,'route'=>$d->route,'form'=>$d->form,'dose_text'=>$d->dose_text,'max_dose_text'=>$d->max_dose_text
              ])->values()->all() : [['population'=>'adult','route'=>'PO','form'=>'tablet','dose_text'=>'','max_dose_text'=>'']]);
            @endphp
            @foreach($dosesOld as $i => $row)
              <div class="grid gap-3 sm:grid-cols-5 items-start">
                <select name="doses[{{ $i }}][population]" class="rounded-lg border border-slate-200 px-2 py-2 text-sm">
                  @foreach($ages as $a)<option value="{{ $a }}" @selected(($row['population'] ?? '')===$a)>{{ ucfirst($a) }}</option>@endforeach
                </select>
                <select name="doses[{{ $i }}][route]" class="rounded-lg border border-slate-200 px-2 py-2 text-sm">
                  <option value="">—</option>
                  @foreach($routes as $r)<option value="{{ $r }}" @selected(($row['route'] ?? '')===$r)>{{ $r }}</option>@endforeach
                </select>
                <input name="doses[{{ $i }}][form]" value="{{ $row['form'] ?? '' }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="form">
                <input name="doses[{{ $i }}][dose_text]" value="{{ $row['dose_text'] ?? '' }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="Dose text" required>
                <div class="flex items-center gap-2">
                  <input name="doses[{{ $i }}][max_dose_text]" value="{{ $row['max_dose_text'] ?? '' }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="Max (optional)">
                  <button type="button" class="rm-row rounded-lg border px-3 py-2 text-xs" aria-label="Remove dose">✕</button>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        {{-- Interactions --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-900">Interactions</h3>
            <button type="button" id="addIx" class="rounded-lg border px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Add interaction</button>
          </div>

          <div id="ixList" class="space-y-3">
            @php
              $ixOld = old('interactions', isset($drug)? $drug->interactions->map(fn($ix)=>[
                'with'=>$ix->interacts_with,'severity'=>$ix->severity,'mechanism'=>$ix->mechanism,'management'=>$ix->management
              ])->values()->all() : [['with'=>'','severity'=>'moderate','mechanism'=>'','management'=>'']]);
            @endphp
            @foreach($ixOld as $i => $row)
              <div class="grid gap-3 sm:grid-cols-5 items-start">
                <input name="interactions[{{ $i }}][with]" value="{{ $row['with'] ?? '' }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="Drug name" required>
                <select name="interactions[{{ $i }}][severity]" class="rounded-lg border border-slate-200 px-2 py-2 text-sm">
                  @foreach(['minor','moderate','major'] as $sev)
                    <option value="{{ $sev }}" @selected(($row['severity'] ?? 'moderate')===$sev)>{{ ucfirst($sev) }}</option>
                  @endforeach
                </select>
                <input name="interactions[{{ $i }}][mechanism]" value="{{ $row['mechanism'] ?? '' }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="Mechanism">
                <input name="interactions[{{ $i }}][management]" value="{{ $row['management'] ?? '' }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="Management">
                <div class="flex items-center"><button type="button" class="rm-row rounded-lg border px-3 py-2 text-xs" aria-label="Remove interaction">✕</button></div>
              </div>
            @endforeach
          </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3">
          <a href="{{ route('faculty.drug_guide.index') }}"
             class="rounded-lg border px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
          <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:opacity-95">
            {{ $mode==='create' ? 'Create' : 'Save changes' }}
          </button>
        </div>
      </form>

      {{-- Footer note --}}
      <div class="rounded-xl border border-slate-200 bg-white p-5">
        <div class="flex items-start gap-3">
          <i data-lucide="info" class="h-5 w-5 text-slate-500 mt-0.5"></i>
          <p class="text-[13px] leading-6 text-slate-600">
            Educational reference only. Updates set the “Last reviewed” date automatically.
          </p>
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

  // Add/remove rows for doses & interactions
  function cloneRow(listId){
    const list = document.getElementById(listId);
    const last = list.querySelector('.grid:last-of-type');
    const clone = last.cloneNode(true);

    // reindex inputs
    const idx = list.querySelectorAll('.grid').length;
    clone.querySelectorAll('input,select,textarea').forEach(el=>{
      // reset values except selects keep current selected option (better UX)
      if(el.tagName !== 'SELECT'){ el.value = ''; }
      el.name = el.name.replace(/\[\d+\]/, `[${idx}]`);
    });

    list.appendChild(clone);
  }

  document.getElementById('addDose')?.addEventListener('click', ()=> cloneRow('dosesList'));
  document.getElementById('addIx')?.addEventListener('click', ()=> cloneRow('ixList'));

  document.addEventListener('click', (e)=>{
    if (e.target.classList.contains('rm-row')) {
      const row = e.target.closest('.grid');
      const list = row.parentElement;
      if (list.querySelectorAll('.grid').length > 1) row.remove();
    }
  });
</script>
</body>
</html>
