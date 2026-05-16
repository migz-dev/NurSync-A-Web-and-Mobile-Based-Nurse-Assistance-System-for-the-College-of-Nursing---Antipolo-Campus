{{-- resources/views/faculty/chartings-archives.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Archived Patients · NurSync (CI)</title>

  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family:'Poppins',ui-sans-serif,system-ui,sans-serif; }

    /* Smooth card entrance */
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
  {{-- Sidebar --}}
  @include('partials.faculty-sidebar', ['active' => 'chartings'])

  <section class="flex-1">
    <div class="container mx-auto px-8 py-12 space-y-6">

      {{-- Page heading --}}
      <header class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
            <i data-lucide="archive" class="h-4 w-4"></i>
          </span>
          <div>
            <h1 class="text-[24px] sm:text-[26px] font-extrabold tracking-tight text-slate-900">
              Archived Patients
            </h1>
            <p class="text-[13px] text-slate-500 mt-1">
              Review, restore, or permanently remove archived patient charting records.
            </p>
          </div>
        </div>

        <a href="{{ route('faculty.chartings.index') }}"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-300 text-slate-700 px-3.5 py-2.5 text-[13px] font-medium hover:bg-slate-50">
          <i data-lucide="arrow-left" class="h-4 w-4"></i>
          <span>Back to Patients & Tasks</span>
        </a>
      </header>

      {{-- Flash messages --}}
      @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 px-4 py-3 text-sm">
          {{ session('success') }}
        </div>
      @endif
      @if (session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 text-red-900 px-4 py-3 text-sm">
          {{ session('error') }}
        </div>
      @endif

      {{-- Filters (Search + Unit) --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid gap-3 md:grid-cols-3">
          <div class="relative">
            <i data-lucide="search" class="absolute left-3 top-3.5 h-4 w-4 text-slate-400"></i>
            <input id="searchBox" type="text" placeholder="Search (name, MRN, unit, attending)…"
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-9 pr-3 py-2.5 text-sm placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-200" />
          </div>
          <div>
            <label for="unitFilter" class="sr-only">Unit/Ward</label>
            <select id="unitFilter"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-200">
              <option value="all">All Units/Wards</option>
              <option value="ms">Medical Ward (MS)</option>
              <option value="sr">Surgical Ward (SR)</option>
              <option value="medsurg">Medical–Surgical Ward</option>
              <option value="icu">ICU</option>
              <option value="ccu">CCU</option>
              <option value="nicu">NICU</option>
              <option value="picu">PICU</option>
              <option value="ob">OB</option>
              <option value="dr">DR</option>
              <option value="pedia">PEDIA</option>
              <option value="nursery">Nursery</option>
              <option value="er">ER</option>
              <option value="or">OR</option>
              <option value="recovery">PACU/Recovery</option>
              <option value="onco">Oncology</option>
              <option value="orthopedics">Orthopedics</option>
              <option value="neuro">Neurology</option>
              <option value="psych">Psychiatric</option>
              <option value="geriatrics">Geriatric</option>
              <option value="rehab">Rehabilitation</option>
              <option value="dialysis">Dialysis</option>
              <option value="burn">Burn Unit</option>
              <option value="triage">Triage</option>
              <option value="isolation">Isolation</option>
              <option value="infect">Infection Control</option>
              <option value="chn">Community Health Nursing</option>
            </select>
          </div>
          <div></div>
        </div>
      </div>

      {{-- Cards --}}
      <div id="cardsGrid" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @php
          // Ward badge color (same mapping as main page)
          $wardChip = function (?string $ward) {
            return match ($ward) {
              'Community Health Nursing', 'Community Health (CHN)' => 'bg-emerald-50 text-emerald-700',
              'OB', 'OB Ward', 'Obstetrics Ward (OB)', 'DR', 'Delivery Room (DR)', 'Nursery', 'Newborn/Nursery Unit'
                => 'bg-pink-50 text-pink-700',
              'PEDIA', 'Pediatric Ward (PEDIA)', 'Pediatrics (PEDIA)' => 'bg-sky-50 text-sky-700',
              'Medical Ward (MS)', 'Surgical Ward (SR)', 'Medical–Surgical Ward', 'Medical-Surgical (MS)'
                => 'bg-slate-50 text-slate-700',
              'ICU', 'Intensive Care Unit (ICU)' => 'bg-rose-50 text-rose-700',
              'Oncology', 'Oncology Unit' => 'bg-fuchsia-50 text-fuchsia-700',
              'Isolation', 'Isolation Ward', 'Isolation Unit' => 'bg-amber-50 text-amber-700',
              'Endocrine Unit' => 'bg-cyan-50 text-cyan-700',
              'Neurology', 'Neurology Unit' => 'bg-indigo-50 text-indigo-700',
              'Psychiatric', 'Psychiatric Ward', 'Psychiatric (PSYCH)' => 'bg-violet-50 text-violet-700',
              'ER', 'Emergency Room (ER)' => 'bg-orange-50 text-orange-700',
              'OR', 'Operating Room (OR)' => 'bg-green-50 text-green-700',
              'Trauma Unit', 'Triage' => 'bg-red-50 text-red-700',
              'Geriatric', 'Geriatric Ward' => 'bg-yellow-50 text-yellow-700',
              default => 'bg-slate-50 text-slate-700',
            };
          };
        @endphp

        @forelse ($patients as $p)
          @php
            $name = $p->display_name;
            $mrn  = $p->hospital_no ?: '—';
            $age  = !is_null($p->age) ? (int) $p->age : ($p->dob ? \Carbon\Carbon::parse($p->dob)->age : null);
            $sex  = $p->sex ?: 'U';
            $unit = strtolower($p->ward ?? '');

            $statusClass = 'bg-orange-50 text-orange-700';

            $unitLabel = $p->ward ?: 'Unassigned unit';
            $unitBed   = ($p->ward ?: '—') . ' — ' . ($p->bed_no ?: '—');
            $attending = $p->attending_physician ?: '—';
            $keywords  = strtolower(trim("{$name} {$mrn} {$p->ward} {$attending} {$sex} {$p->bed_no}"));

            $wardChipClass = $wardChip($p->ward);
            $admittedAt = $p->admission_date ? \Carbon\Carbon::parse($p->admission_date)->format('M d, Y H:i') : null;
            $updatedAt  = $p->updated_at?->diffForHumans();
          @endphp

          <article id="patient-card-{{ $p->id }}"
                   class="js-card flex flex-col h-full rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow animate-card-in"
                   data-patient-id="{{ $p->id }}"
                   data-unit="{{ $unit }}"
                   data-keywords="{{ $keywords }}">

            {{-- Header --}}
            <header class="flex items-start justify-between gap-3">
              <div>
                <h2 class="text-[14px] sm:text-[15px] font-semibold text-slate-900 leading-snug flex items-center gap-2">
                  <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-50">
                    <i data-lucide="user-round" class="h-4 w-4 text-slate-700"></i>
                  </span>
                  <span>{{ $name }}</span>
                </h2>
                <p class="mt-1 text-[12px] text-slate-600">
                  MRN <span class="font-medium text-slate-800">{{ $mrn }}</span>
                  <span class="mx-1 text-slate-300">•</span>
                  @if($age !== null)
                    {{ $age }} yrs /
                  @endif
                  {{ $sex }}
                </p>

                <div class="mt-2 flex flex-wrap items-center gap-2">
                  @if($unitLabel)
                    <span class="inline-flex items-center rounded-full {{ $wardChipClass }} px-2 py-0.5 text-[11px]">
                      <i data-lucide="hospital" class="h-3 w-3 mr-1"></i>
                      {{ $unitLabel }}
                    </span>
                  @endif

                  <span class="inline-flex items-center rounded-full {{ $statusClass }} px-2 py-0.5 text-[11px] font-medium">
                    Archived
                  </span>
                </div>
              </div>

              <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                <i data-lucide="archive" class="h-4 w-4"></i>
              </span>
            </header>

            {{-- Meta --}}
            <div class="mt-3 flex items-start justify-between gap-3 text-[11px] text-slate-500">
              <div class="space-y-1">
                <div>
                  <span class="font-medium text-slate-700">Unit/Bed:</span>
                  <span>{{ $unitBed }}</span>
                </div>
                <div>
                  <span class="font-medium text-slate-700">Attending:</span>
                  <span>{{ $attending }}</span>
                </div>
              </div>
              <div class="text-right space-y-1">
                @if($admittedAt)
                  <div>
                    <span class="font-medium text-slate-700">Admitted:</span>
                    <span>{{ $admittedAt }}</span>
                  </div>
                @endif
                <div>
                  <span class="font-medium text-slate-700">Updated:</span>
                  <span>{{ $updatedAt ?? '—' }}</span>
                </div>
              </div>
            </div>

            {{-- Actions --}}
            <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2">
              {{-- View --}}
              <a href="{{ route('faculty.chartings.patient', $p->id) }}"
                 class="inline-flex items-center gap-1.5 rounded-xl border border-purple-200 text-purple-700 px-3 py-1.5 text-[12px] font-medium hover:bg-purple-50"
                 aria-label="Open patient records">
                <i data-lucide="file-text" class="h-3 w-3" aria-hidden="true"></i>
                Records
              </a>

              {{-- Restore --}}
              <button type="button"
                class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 text-emerald-800 px-3 py-1.5 text-[12px] font-medium hover:bg-emerald-50"
                data-restore-action="{{ route('faculty.chartings.patients.restore', $p->id) }}"
                data-restore-method="PATCH"
                data-patient-name="{{ $name }}">
                <i data-lucide="rotate-ccw" class="h-3 w-3"></i>
                Restore
              </button>

              {{-- Delete --}}
              <button type="button"
                class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 text-red-800 px-3 py-1.5 text-[12px] font-medium hover:bg-red-50"
                data-delete-action="{{ route('faculty.chartings.patients.destroy', $p->id) }}"
                data-delete-method="DELETE"
                data-patient-name="{{ $name }}">
                <i data-lucide="trash-2" class="h-3 w-3"></i>
                Delete
              </button>
            </div>
          </article>
        @empty
          <div class="col-span-full">
            <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500">
              No archived patients.
            </div>
          </div>
        @endforelse
      </div>

      {{-- Empty state when filters hide all --}}
      <div id="emptyFilterState" class="hidden">
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500">
          No matches found. Try adjusting your search or filters.
        </div>
      </div>
    </div>
  </section>
</main>

@include('partials.faculty-footer')

<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  try { lucide.createIcons(); } catch (_) {}

  const q       = document.getElementById('searchBox');
  const unitSel = document.getElementById('unitFilter');
  const emptyBox = document.getElementById('emptyFilterState');

  function visibleCardsCount() {
    return [...document.querySelectorAll('.js-card')].filter(c => c.style.display !== 'none').length;
  }

  function render() {
    const needle   = (q?.value || '').toLowerCase().trim();
    const wantUnit = (unitSel?.value || 'all').toLowerCase();

    document.querySelectorAll('.js-card').forEach(card => {
      const kw   = (card.dataset.keywords || '').toLowerCase();
      const unit = (card.dataset.unit || '').toLowerCase();
      const textOk = !needle || kw.includes(needle);
      const unitOk = wantUnit === 'all' || unit.includes(wantUnit) || kw.includes(wantUnit);
      card.style.display = (textOk && unitOk) ? '' : 'none';
    });

    const hasAnyCard = document.querySelector('.js-card') !== null;
    const anyVisible = visibleCardsCount() > 0;
    emptyBox.classList.toggle('hidden', !hasAnyCard || anyVisible);
  }

  q?.addEventListener('input', render);
  unitSel?.addEventListener('change', render);
  render();

  // Restore + Delete handlers (SweetAlert2 + fetch + fade-out)
  document.addEventListener('click', async (e) => {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Restore
    const restoreBtn = e.target.closest('[data-restore-action]');
    if (restoreBtn) {
      const action = restoreBtn.getAttribute('data-restore-action');
      const method = (restoreBtn.getAttribute('data-restore-method') || 'PATCH').toUpperCase();
      const name   = restoreBtn.getAttribute('data-patient-name') || 'this patient';
      const card   = restoreBtn.closest('.js-card');

      const confirm = await Swal.fire({
        title: 'Restore patient?',
        html: `This will move <b>${name}</b> back to Active.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Restore',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#10b981',
        reverseButtons: true
      });
      if (!confirm.isConfirmed) return;

      restoreBtn.disabled = true;
      try {
        const res = await fetch(action, {
          method,
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({})
        });
        const ct = res.headers.get('content-type') || '';
        if (!ct.includes('application/json')) return location.reload();
        const data = await res.json();
        if (!res.ok || !data?.success) throw new Error(data?.message || 'Restore failed.');

        if (card) {
          card.style.transition = 'opacity .2s ease, transform .2s ease';
          card.style.opacity = '0';
          card.style.transform = 'scale(.98)';
          setTimeout(() => { card.remove(); render(); }, 200);
        }
        Swal.fire({ icon: 'success', title: 'Restored', timer: 1200, showConfirmButton: false });
      } catch (err) {
        Swal.fire({ icon: 'error', title: 'Restore failed', text: err?.message || 'Please try again.' });
        restoreBtn.disabled = false;
      }
      return;
    }

    // Delete
    const deleteBtn = e.target.closest('[data-delete-action]');
    if (deleteBtn) {
      const action = deleteBtn.getAttribute('data-delete-action');
      const method = (deleteBtn.getAttribute('data-delete-method') || 'DELETE').toUpperCase();
      const name   = deleteBtn.getAttribute('data-patient-name') || 'this patient';
      const card   = deleteBtn.closest('.js-card');

      const confirm = await Swal.fire({
        title: 'Delete permanently?',
        html: `This will permanently remove <b>${name}</b> and cannot be undone.`,
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#ef4444',
        reverseButtons: true
      });
      if (!confirm.isConfirmed) return;

      deleteBtn.disabled = true;
      try {
        const res = await fetch(action, {
          method,
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({})
        });
        const ct = res.headers.get('content-type') || '';
        if (!ct.includes('application/json')) return location.reload();
        const data = await res.json();
        if (!res.ok || !data?.success) throw new Error(data?.message || 'Delete failed.');

        if (card) {
          card.style.transition = 'opacity .2s ease, transform .2s ease';
          card.style.opacity = '0';
          card.style.transform = 'scale(.98)';
          setTimeout(() => { card.remove(); render(); }, 200);
        }
        Swal.fire({ icon: 'success', title: 'Deleted', timer: 1200, showConfirmButton: false });
      } catch (err) {
        Swal.fire({ icon: 'error', title: 'Delete failed', text: err?.message || 'Please try again.' });
        deleteBtn.disabled = false;
      }
    }
  });
</script>
</body>
</html>
