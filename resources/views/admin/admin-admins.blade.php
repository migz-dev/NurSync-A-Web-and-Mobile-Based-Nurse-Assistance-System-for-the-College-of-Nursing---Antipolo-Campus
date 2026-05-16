{{-- resources/views/admin/admin-admins.blade.php --}}
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Admin • Admins · NurSync</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style> body { font-family:'Poppins', ui-sans-serif, system-ui, sans-serif; } </style>
</head>

<body class="min-h-screen bg-slate-50">
<main class="min-h-screen flex">
  {{-- Sidebar --}}
  @include('partials.admin-sidebar', ['active' => 'admins'])

  {{-- Main --}}
  <section class="flex-1 min-w-0">
    {{-- Header --}}
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-900 text-white shadow-sm">
            <i data-lucide="shield" class="h-4 w-4"></i>
          </div>
          <div>
            <h1 class="text-[15px] sm:text-[16px] font-semibold leading-tight">Admins</h1>
            <p class="text-[12px] text-slate-500 -mt-0.5">Manage administrator accounts & access.</p>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <a href="{{ route('admin.admins.create') }}"
             class="inline-flex items-center gap-2 rounded-xl bg-green-600 text-white px-3 py-2 text-[13px] font-medium shadow hover:bg-green-700 active:scale-[.99]">
            <i data-lucide="plus" class="h-4 w-4"></i>
            <span>Create</span>
          </a>
        </div>
      </div>
    </header>

    {{-- Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">

      {{-- Filters / tools (UI only; wire as needed) --}}
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
        <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
          <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-72">
              <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
              <input id="filter-q" type="text" placeholder="Search name, email…"
                     class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300">
            </div>

            <select id="filter-status"
                    class="rounded-xl border-slate-200 text-sm py-2.5 px-3 focus:ring-2 focus:ring-slate-300">
              <option value="">All status</option>
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>

          <div class="flex items-center gap-2">
            <button class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 px-3 py-2 text-[13px] hover:bg-slate-50">
              <i data-lucide="download" class="h-4 w-4"></i>
              Export
            </button>
          </div>
        </div>
      </div>

      {{-- Table --}}
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
            <tr class="text-left text-slate-600">
              <th class="px-4 py-3 w-10"><input type="checkbox" class="rounded border-slate-300"></th>
              <th class="px-4 py-3">Name</th>
              <th class="px-4 py-3">Email</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Created</th>
              <th class="px-4 py-3 text-right">Actions</th>
            </tr>
            </thead>

            <tbody class="divide-y divide-slate-200">
            @forelse($adminsPage as $a)
              @php
                $isActive = (int)$a->is_active === 1;
                $initials = collect(explode(' ', trim($a->full_name)))->map(fn($p)=>mb_substr($p,0,1))->join('');
              @endphp
              <tr class="hover:bg-slate-50">
                <td class="px-4 py-3"><input type="checkbox" class="rounded border-slate-300"></td>

                {{-- Name + avatar --}}
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    @if(!empty($a->profile_image))
                      <img src="{{ asset($a->profile_image) }}" alt="{{ $a->full_name }}"
                           class="h-9 w-9 rounded-xl object-cover">
                    @else
                      <div class="h-9 w-9 rounded-xl bg-slate-100 flex items-center justify-center text-[12px] font-semibold text-slate-600">
                        {{ $initials }}
                      </div>
                    @endif
                    <div>
                      <div class="font-medium text-slate-900">{{ $a->full_name }}</div>
                      <div class="text-[12px] text-slate-500">ID: {{ $a->id }}</div>
                    </div>
                  </div>
                </td>

                {{-- Email --}}
                <td class="px-4 py-3 text-slate-700">{{ $a->email }}</td>

                {{-- Status pill (is_active) --}}
                <td class="px-4 py-3">
                  @if($isActive)
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 text-emerald-700 px-2 py-1 text-[12px] font-medium">
                      <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Active
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 text-slate-600 px-2 py-1 text-[12px] font-medium">
                      <span class="h-2 w-2 rounded-full bg-slate-400"></span> Inactive
                    </span>
                  @endif
                </td>

                {{-- Created --}}
                <td class="px-4 py-3 text-slate-700">
                  {{ \Illuminate\Support\Carbon::parse($a->created_at)->format('M d, Y') }}
                </td>

                {{-- Actions --}}
                <td class="px-4 py-3">
                  <div class="flex items-center justify-end gap-1.5">
                    <a href="{{ route('admin.admins.show', $a->id) }}"
                       class="inline-flex items-center justify-center rounded-lg bg-blue-600 text-white p-2 hover:bg-blue-700">
                      <i data-lucide="eye" class="h-4 w-4"></i>
                    </a>
                    <a href="{{ route('admin.admins.edit', $a->id) }}"
                       class="inline-flex items-center justify-center rounded-lg bg-yellow-400 text-slate-900 p-2 hover:brightness-95">
                      <i data-lucide="pencil" class="h-4 w-4"></i>
                    </a>
                    <button type="button"
                            class="inline-flex items-center justify-center rounded-lg bg-rose-600 text-white p-2 hover:bg-rose-700 js-delete-admin"
                            data-admin-id="{{ $a->id }}" data-admin-name="{{ $a->full_name }}">
                      <i data-lucide="trash-2" class="h-4 w-4"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-4 py-8 text-center text-slate-500">No admins found.</td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>

        {{-- Footer actions / pagination --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 border-t border-slate-200 bg-slate-50">
          <div class="flex items-center gap-2">
            <select class="rounded-xl border-slate-200 text-sm py-2 px-2.5">
              <option>Bulk action</option>
              <option>Set active</option>
              <option>Set inactive</option>
              <option>Delete selected</option>
            </select>
            <button class="inline-flex items-center gap-2 rounded-xl bg-slate-900 text-white px-3 py-2 text-[13px] font-medium hover:bg-black/90">
              <i data-lucide="play" class="h-4 w-4"></i> Apply
            </button>
          </div>

          @php
            $cur = $adminsPage->currentPage();
            $last = max(1, $adminsPage->lastPage());
            $window = 3; $half = intdiv($window - 1, 2);
            $from = max(1, $cur - $half);
            $to = min($last, $from + $window - 1);
            $from = max(1, $to - $window + 1);
          @endphp

          <nav class="flex items-center gap-1">
            {{-- Prev --}}
            @if ($adminsPage->onFirstPage())
              <button class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white opacity-50 cursor-not-allowed">
                <i data-lucide="chevron-left" class="h-4 w-4"></i>
              </button>
            @else
              <a href="{{ $adminsPage->previousPageUrl() }}"
                 class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
                <i data-lucide="chevron-left" class="h-4 w-4"></i>
              </a>
            @endif

            {{-- Numbers --}}
            @for ($i = $from; $i <= $to; $i++)
              @if ($i === $cur)
                <span class="h-9 w-9 inline-flex items-center justify-center rounded-lg bg-slate-900 text-white">{{ $i }}</span>
              @else
                <a href="{{ $adminsPage->url($i) }}"
                   class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
                  {{ $i }}
                </a>
              @endif
            @endfor

            {{-- Next --}}
            @if ($cur >= $last)
              <button class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white opacity-50 cursor-not-allowed">
                <i data-lucide="chevron-right" class="h-4 w-4"></i>
              </button>
            @else
              <a href="{{ $adminsPage->nextPageUrl() }}"
                 class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50">
                <i data-lucide="chevron-right" class="h-4 w-4"></i>
              </a>
            @endif
          </nav>
        </div>
      </div>
    </div>
  </section>
</main>

{{-- Footer (shared) --}}
@include('partials.admin-footer')

{{-- Icons + SweetAlert Delete --}}
<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  document.querySelectorAll('.js-delete-admin').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.getAttribute('data-admin-id');
      const name = btn.getAttribute('data-admin-name');

      const { isConfirmed } = await Swal.fire({
        icon: 'warning',
        title: 'Delete admin?',
        html: `<div class="text-sm text-slate-600">This action cannot be undone.<br><b>${name}</b> will be permanently removed.</div>`,
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc2626',
      });

      if (!isConfirmed) return;

      try {
        const res = await fetch("{{ route('admin.admins.destroy', ['admin' => 'ADMIN_ID']) }}".replace('ADMIN_ID', id), {
          method: 'POST', // allow method spoofing if you prefer form approach; here we use real DELETE
          headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-HTTP-Method-Override': 'DELETE' },
          body: JSON.stringify({}) // Laravel reads DELETE even without a body
        });

        if (!res.ok) {
          const err = await res.json().catch(() => ({}));
          throw new Error(err.message || 'Failed to delete admin.');
        }

        // Remove row
        btn.closest('tr')?.remove();

        await Swal.fire({ icon: 'success', title: 'Deleted', timer: 1300, showConfirmButton: false });
      } catch (e) {
        Swal.fire({ icon: 'error', title: 'Delete failed', text: e.message || 'Something went wrong.' });
      }
    });
  });
</script>

</body>
</html>
