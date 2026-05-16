<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Account Settings · NurSync (CI)</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family:'Poppins', ui-sans-serif, system-ui, sans-serif }

    /* Same animation used in CI Procedures / Nursing References */
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
  {{-- Sidebar (highlight Settings) --}}
  @include('partials.instructor-sidebar', ['active' => 'settings'])

  {{-- Main content --}}
  <section class="flex-1">
    <div class="container mx-auto px-8 py-12">

      {{-- Skeleton loading (initial shimmer) --}}
      <div id="settingsSkeleton" class="space-y-5">
        <div class="flex items-center gap-3">
          <div class="h-8 w-8 rounded-xl bg-slate-200 animate-pulse"></div>
          <div class="space-y-2">
            <div class="h-4 w-40 bg-slate-200 rounded animate-pulse"></div>
            <div class="h-3 w-64 bg-slate-100 rounded animate-pulse"></div>
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
          <div class="animate-pulse space-y-4">
            <div class="flex items-center gap-2">
              <div class="h-6 w-6 rounded-lg bg-slate-100"></div>
              <div class="h-3 w-24 bg-slate-100 rounded"></div>
            </div>
            <div class="flex items-center gap-4 mt-4">
              <div class="h-[84px] w-[84px] rounded-full bg-slate-100 border border-slate-200"></div>
              <div class="flex-1 space-y-2">
                <div class="h-3 w-40 bg-slate-100 rounded"></div>
                <div class="h-3 w-56 bg-slate-100 rounded"></div>
                <div class="h-3 w-32 bg-slate-100 rounded"></div>
              </div>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 mt-4">
              <div class="space-y-2">
                <div class="h-3 w-24 bg-slate-100 rounded"></div>
                <div class="h-9 w-full bg-slate-50 rounded-xl"></div>
              </div>
              <div class="space-y-2">
                <div class="h-3 w-24 bg-slate-100 rounded"></div>
                <div class="h-9 w-full bg-slate-50 rounded-xl"></div>
              </div>
              <div class="sm:col-span-2 space-y-2">
                <div class="h-3 w-24 bg-slate-100 rounded"></div>
                <div class="h-9 w-full bg-slate-50 rounded-xl"></div>
              </div>
            </div>
            <div class="mt-4 h-10 w-full bg-slate-100 rounded-xl"></div>
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
          <div class="animate-pulse space-y-4">
            <div class="flex items-center gap-2">
              <div class="h-6 w-6 rounded-lg bg-slate-100"></div>
              <div class="h-3 w-24 bg-slate-100 rounded"></div>
            </div>
            <div class="space-y-3 mt-4">
              <div class="h-3 w-32 bg-slate-100 rounded"></div>
              <div class="h-9 w-full bg-slate-50 rounded-xl"></div>
              <div class="h-3 w-32 bg-slate-100 rounded"></div>
              <div class="h-9 w-full bg-slate-50 rounded-xl"></div>
              <div class="h-3 w-40 bg-slate-100 rounded"></div>
              <div class="h-9 w-full bg-slate-50 rounded-xl"></div>
            </div>
            <div class="mt-4 h-10 w-full bg-slate-100 rounded-xl"></div>
          </div>
        </div>
      </div>

      {{-- Real content (hidden until skeleton finishes) --}}
      <div id="settingsReal" class="space-y-6 hidden">

        {{-- Alerts --}}
        @if(session('ok'))
          <div class="mb-1 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 js-settings-card">
            {{ session('ok') }}
          </div>
        @endif
        @if($errors->any())
          <div class="mb-1 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 js-settings-card">
            Please correct the errors below.
          </div>
        @endif

        {{-- Title --}}
        <div class="flex items-center gap-3 js-settings-card">
          <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-slate-100 text-slate-700">
            <i data-lucide="sliders-horizontal" class="h-5 w-5"></i>
          </span>
          <div>
            <h1 class="text-2xl font-bold">Account Settings</h1>
            <p class="text-[13px] text-slate-500 mt-1">Manage your CI account info, avatar, and password.</p>
          </div>
        </div>

        {{-- Profile card --}}
        <div class="mt-2 rounded-2xl border border-slate-200/70 bg-white p-6 js-settings-card">
          <div class="flex items-center gap-2">
            <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-slate-100 text-slate-700">
              <i data-lucide="user-round" class="h-4 w-4"></i>
            </span>
            <h2 class="text-[15px] font-semibold">Profile</h2>
          </div>

          @php
            /** @var \App\Models\Faculty|null $u */
            $u = auth('faculty')->user();
            $avatarUrl = $u?->avatar_url ?? null;   // add accessor on Faculty model
            $initials  = $u?->name ? strtoupper(mb_substr($u->name, 0, 1)) : 'F';
            $selectedNurseType = old('nurse_type', $u?->nurse_type);
          @endphp

          <form class="mt-5 space-y-6" method="POST" action="{{ route('faculty.settings.profile') }}" enctype="multipart/form-data">
            @csrf

            {{-- Avatar --}}
            <div class="flex items-center gap-6">
              <div class="relative">
                @if($avatarUrl)
                  <img src="{{ $avatarUrl }}" alt="Profile photo"
                       class="h-[84px] w-[84px] rounded-full object-cover border border-slate-200">
                @else
                  <div class="h-[84px] w-[84px] rounded-full bg-slate-100 border border-slate-200
                              flex items-center justify-center text-slate-700 font-semibold">
                    {{ $initials }}
                  </div>
                @endif
              </div>

              <div class="space-y-2">
                <div class="text-[13px] text-slate-600">Profile Picture</div>
                <div class="flex items-center gap-3">
                  <label class="inline-flex cursor-pointer rounded-lg border border-slate-200 px-3 py-1.5 text-[13px] font-medium hover:bg-slate-50">
                    <input type="file" name="avatar" class="hidden" accept="image/*">
                    Upload new photo
                  </label>

                  @if($avatarUrl)
                    <button form="remove-avatar" type="submit" class="text-[13px] text-slate-600 hover:text-slate-900">
                      Remove
                    </button>
                  @endif
                </div>
                <p class="text-[12px] text-slate-400">Recommended: JPG/PNG/WebP, max 2MB.</p>
              </div>
            </div>

            {{-- Name, Nurse Type & Email --}}
            <div class="grid gap-4 sm:grid-cols-2">
              <div class="sm:col-span-2">
                <label class="block text-[12px] text-slate-500 mb-1">Full Name</label>
                <input name="name" type="text" value="{{ old('name', $u?->name) }}"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-[14px] outline-none focus:border-slate-300">
                @error('name') <p class="mt-1 text-[12px] text-rose-600">{{ $message }}</p> @enderror
              </div>

              {{-- Nurse Type --}}
              <div class="sm:col-span-2">
                <label class="block text-[12px] text-slate-500 mb-1">Nurse Type</label>
                <select name="nurse_type" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[14px] outline-none focus:border-slate-300">
                  <option value="">{{ __('— Select nurse type —') }}</option>
                  <option value="Nurse Practitioner" {{ $selectedNurseType === 'Nurse Practitioner' ? 'selected' : '' }}>Nurse Practitioner</option>
                  <option value="Emergency room nurse" {{ $selectedNurseType === 'Emergency room nurse' ? 'selected' : '' }}>Emergency room nurse</option>
                  <option value="Oncology nursing" {{ $selectedNurseType === 'Oncology nursing' ? 'selected' : '' }}>Oncology nursing</option>
                  <option value="Labor and Delivery Nurse" {{ $selectedNurseType === 'Labor and Delivery Nurse' ? 'selected' : '' }}>Labor and Delivery Nurse</option>
                  <option value="Licensed Practical Nurse" {{ $selectedNurseType === 'Licensed Practical Nurse' ? 'selected' : '' }}>Licensed Practical Nurse</option>
                  <option value="Nurse Anesthetist" {{ $selectedNurseType === 'Nurse Anesthetist' ? 'selected' : '' }}>Nurse Anesthetist</option>
                  <option value="Cardiac nurse" {{ $selectedNurseType === 'Cardiac nurse' ? 'selected' : '' }}>Cardiac nurse</option>
                  <option value="Clinical nurse specialist" {{ $selectedNurseType === 'Clinical nurse specialist' ? 'selected' : '' }}>Clinical nurse specialist</option>
                  <option value="Home Health nurse" {{ $selectedNurseType === 'Home Health nurse' ? 'selected' : '' }}>Home Health nurse</option>
                  <option value="Nurse educator" {{ $selectedNurseType === 'Nurse educator' ? 'selected' : '' }}>Nurse educator</option>
                  <option value="Nurse midwife" {{ $selectedNurseType === 'Nurse midwife' ? 'selected' : '' }}>Nurse midwife</option>
                  <option value="Critical care nursing" {{ $selectedNurseType === 'Critical care nursing' ? 'selected' : '' }}>Critical care nursing</option>
                  <option value="ICU nurse" {{ $selectedNurseType === 'ICU nurse' ? 'selected' : '' }}>ICU nurse</option>
                  <option value="Mental health nursing" {{ $selectedNurseType === 'Mental health nursing' ? 'selected' : '' }}>Mental health nursing</option>
                  <option value="Pediatric nursing" {{ $selectedNurseType === 'Pediatric nursing' ? 'selected' : '' }}>Pediatric nursing</option>
                  <option value="Surgical nurses" {{ $selectedNurseType === 'Surgical nurses' ? 'selected' : '' }}>Surgical nurses</option>
                  <option value="Travel Nurse" {{ $selectedNurseType === 'Travel Nurse' ? 'selected' : '' }}>Travel Nurse</option>
                  <option value="Informatics nurse" {{ $selectedNurseType === 'Informatics nurse' ? 'selected' : '' }}>Informatics nurse</option>
                  <option value="Public Health Nurse" {{ $selectedNurseType === 'Public Health Nurse' ? 'selected' : '' }}>Public Health Nurse</option>
                  <option value="Geriatric nurse" {{ $selectedNurseType === 'Geriatric nurse' ? 'selected' : '' }}>Geriatric nurse</option>
                  <option value="NICU nurse" {{ $selectedNurseType === 'NICU nurse' ? 'selected' : '' }}>NICU nurse</option>
                  <option value="Nurse administrator" {{ $selectedNurseType === 'Nurse administrator' ? 'selected' : '' }}>Nurse administrator</option>
                  <option value="Operating Room nurse" {{ $selectedNurseType === 'Operating Room nurse' ? 'selected' : '' }}>Operating Room nurse</option>
                </select>
                @error('nurse_type') <p class="mt-1 text-[12px] text-rose-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-[12px] text-slate-400">Choose the nurse type that best describes your role (optional).</p>
              </div>

              <div class="sm:col-span-2">
                <label class="block text-[12px] text-slate-500 mb-1">Email</label>
                <input type="email" value="{{ $u?->email }}" disabled
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[14px] text-slate-500">
                <p class="mt-1 text-[12px] text-slate-400">Email cannot be changed</p>
              </div>
            </div>

            <div>
              <button type="submit" class="w-full rounded-xl bg-black text-white py-2.5 text-[14px] font-semibold hover:bg-neutral-900">
                Update Profile
              </button>
            </div>
          </form>

          {{-- Separate DELETE form for removing avatar --}}
          <form id="remove-avatar" class="hidden" method="POST" action="{{ route('faculty.settings.avatar.remove') }}">
            @csrf
            @method('DELETE')
          </form>
        </div>

        {{-- Security card --}}
        <div class="mt-2 rounded-2xl border border-slate-200/70 bg-white p-6 js-settings-card">
          <div class="flex items-center gap-2">
            <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-slate-100 text-slate-700">
              <i data-lucide="shield" class="h-4 w-4"></i>
            </span>
            <h2 class="text-[15px] font-semibold">Security</h2>
          </div>

          <form class="mt-5 grid gap-4" method="POST" action="{{ route('faculty.settings.password') }}">
            @csrf
            <div>
              <label class="block text-[12px] text-slate-500 mb-1">Current Password</label>
              <input name="current_password" type="password" placeholder="Enter your current password"
                     class="w-full rounded-xl border border-slate-200 px-3 py-2 text-[14px] outline-none focus:border-slate-300">
              @error('current_password') <p class="mt-1 text-[12px] text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
              <label class="block text-[12px] text-slate-500 mb-1">New Password</label>
              <input name="password" type="password" placeholder="Enter new password"
                     class="w-full rounded-xl border border-slate-200 px-3 py-2 text-[14px] outline-none focus:border-slate-300">
              @error('password') <p class="mt-1 text-[12px] text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
              <label class="block text-[12px] text-slate-500 mb-1">Confirm New Password</label>
              <input name="password_confirmation" type="password" placeholder="Confirm new password"
                     class="w-full rounded-xl border border-slate-200 px-3 py-2 text-[14px] outline-none focus:border-slate-300">
            </div>

            <div class="mt-1">
              <button type="submit" class="w-full rounded-xl bg-black text-white py-2.5 text-[14px] font-semibold hover:bg-neutral-900">
                Update Password
              </button>
            </div>
          </form>
        </div>

      </div>
    </div>
  </section>
</main>

{{-- Lucide icons --}}
<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();

  document.addEventListener('DOMContentLoaded', () => {
    const skeleton = document.getElementById('settingsSkeleton');
    const real     = document.getElementById('settingsReal');
    if (!skeleton || !real) return;

    const delay = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 220;

    setTimeout(() => {
      skeleton.classList.add('hidden');
      real.classList.remove('hidden');

      // Animate cards (alerts, header, profile, security)
      const cards = real.querySelectorAll('.js-settings-card');
      cards.forEach((el, idx) => {
        el.style.animationDelay = `${Math.min(idx, 6) * 40}ms`;
        el.classList.add('animate-card-in');
      });
    }, delay);
  });
</script>
</body>
</html>
