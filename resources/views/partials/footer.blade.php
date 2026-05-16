<footer class="bg-[#00a63e] text-white py-12">
  <div class="mx-auto max-w-7xl px-4 sm:px-6">
    <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
      {{-- About --}}
      <div>
        <h3 class="text-base font-semibold">About NurSync</h3>
        <p class="mt-3 text-sm text-white/80 leading-relaxed">
          We help colleges of nursing organize modules, automate duty schedules,
          manage return demos, and publish results—accessible to everyone.
        </p>
      </div>

      {{-- Quick Links --}}
      <div>
        <h3 class="text-base font-semibold">Quick Links</h3>
        <ul class="mt-4 space-y-2 text-sm">
          <li><a href="{{ url('/') }}" class="text-white/80 hover:text-white transition">Home</a></li>
          <li><a href="{{ url('/').'#how' }}" class="text-white/80 hover:text-white transition">How It Works</a></li>
          <li><a href="{{ url('/').'#about' }}" class="text-white/80 hover:text-white transition">About</a></li>
          <li><a href="{{ url('/contact') }}" class="text-white/80 hover:text-white transition">Contact</a></li>
        </ul>
      </div>

      {{-- Legal --}}
      <div>
        <h3 class="text-base font-semibold">Legal</h3>
        <ul class="mt-4 space-y-2 text-sm">
          <li>
            <a href="{{ route('legal.terms') }}" class="text-white/80 hover:text-white transition">
              Terms of Service
            </a>
          </li>
          <li>
            <a href="{{ route('legal.privacy') }}" class="text-white/80 hover:text-white transition">
              Privacy Policy
            </a>
          </li>
          <li>
            <a href="{{ route('legal.cookies') }}" class="text-white/80 hover:text-white transition">
              Cookie Policy
            </a>
          </li>
        </ul>
      </div>

      {{-- Connect --}}
      <div>
        <h3 class="text-base font-semibold">Connect</h3>
        <div class="mt-4 flex items-center gap-4">
          {{-- same social icons --}}
          <a href="#" aria-label="Facebook" class="text-white/80 hover:text-white transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M22 12.07C22 6.48 17.52 2 11.93 2S1.86 6.48 1.86 12.07c0 5 3.66 9.14 8.44 9.93v-7.02H7.9v-2.91h2.4V9.41c0-2.37 1.41-3.68 3.57-3.68 1.03 0 2.12.18 2.12.18v2.33h-1.19c-1.17 0-1.54.73-1.54 1.48v1.77h2.63l-.42 2.91h-2.21V22c4.78-.79 8.44-4.93 8.44-9.93Z"/>
            </svg>
          </a>
          <a href="#" aria-label="Instagram" class="text-white/80 hover:text-white transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5Zm0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H7Zm5 3.5a5.5 5.5 0 1 1 0 11 5.5 5.5 0 0 1 0-11Zm0 2a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Zm5.75-.25a1.25 1.25 0 1 1-2.5 0 1.25 1.25 0 0 1 2.5 0Z"/>
            </svg>
          </a>
          <a href="#" aria-label="TikTok" class="text-white/80 hover:text-white transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M17.1 6.4a5.7 5.7 0 0 0 3.4 1.2V10a8 8 0 0 1-3.4-.8v5.2a5.6 5.6 0 1 1-5.6-5.6c.3 0 .6 0 .9.1v3.1a2.6 2.6 0 1 0 1.9 2.5V2h2.8v4.4Z"/>
            </svg>
          </a>
        </div>
      </div>
    </div>

    {{-- Bottom bar --}}
    <div class="mt-10 border-t border-white/10 pt-6 text-sm text-white/70 flex flex-col sm:flex-row items-center justify-between gap-4">
      <div>© {{ now()->year }} NurSync. All rights reserved.</div>
      <div class="flex items-center gap-6">
        <a href="{{ route('legal.privacy') }}" class="hover:text-white transition">Privacy</a>
        <a href="{{ route('legal.terms') }}" class="hover:text-white transition">Terms</a>
        <a href="{{ route('legal.cookies') }}" class="hover:text-white transition">Cookies</a>
      </div>
    </div>
  </div>
</footer>
