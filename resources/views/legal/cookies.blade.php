{{-- resources/views/legal/cookies.blade.php --}}
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Cookie Policy · NurSync</title>

  <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }
  </style>
</head>

<body class="min-h-screen bg-white text-slate-900 flex flex-col">
<main class="flex-1 flex items-center justify-center py-10 bg-white">
  <div class="container mx-auto px-4 md:px-6">
    <div class="mx-auto max-w-4xl">

      {{-- Heading --}}
      <header
        class="mb-10 opacity-0 translate-y-5 transition-all duration-500 ease-out"
        data-animate="stagger"
        data-animate-index="0"
      >
        <p class="text-xs font-semibold tracking-[0.18em] uppercase text-emerald-600 mb-2">
          Legal Information
        </p>
        <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-slate-900">
          Cookie & Tracking Policy
        </h1>
        <p class="mt-4 text-base md:text-lg text-slate-600 leading-relaxed">
          This Policy explains how <span class="font-semibold">NurSync</span> may use cookies or similar
          technologies when you access the system via web browsers or compatible devices.
        </p>
      </header>

      <div class="space-y-6 md:space-y-8">

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="1"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            1. What Are Cookies?
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed">
            Cookies are small text files that may be stored on your device when you visit a website or use a
            web application. They help the system remember certain information about your session, such as
            login status or preferences.
          </p>
        </section>

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="2"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            2. Types of Cookies Used in NurSync
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed mb-3">
            Depending on the configuration of your institution’s deployment, NurSync may use:
          </p>
          <ul class="list-disc pl-5 space-y-1 text-sm md:text-base text-slate-600">
            <li><span class="font-semibold">Strictly necessary cookies</span> – required to keep you logged in and to maintain a secure session.</li>
            <li><span class="font-semibold">Preference or functional cookies</span> – used to remember simple settings such as interface options.</li>
            <li><span class="font-semibold">Analytics or performance cookies</span> – optionally used to understand system usage trends in aggregated form.</li>
          </ul>
        </section>

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="3"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            3. How We Use These Technologies
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed">
            Cookies and similar technologies in NurSync are primarily used to:
          </p>
          <ul class="list-disc pl-5 mt-3 space-y-1 text-sm md:text-base text-slate-600">
            <li>Authenticate users and maintain secure sessions.</li>
            <li>Help protect the platform from unauthorized access or misuse.</li>
            <li>Improve stability, performance, and user experience.</li>
            <li>Provide aggregated, non-identifying metrics about usage to administrators, where configured.</li>
          </ul>
        </section>

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="4"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            4. Managing Cookies
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed">
            Most web browsers allow you to manage cookies (e.g., block, delete, or receive notifications).
            Disabling strictly necessary cookies may prevent you from signing in or using certain features of
            NurSync. For detailed instructions, please refer to your browser’s help documentation.
          </p>
        </section>

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="5"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            5. Third-Party Services
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed">
            If your institution integrates third-party tools (for example, analytics platforms, single
            sign-on, or external learning resources), those services may place their own cookies or tracking
            technologies subject to their own policies. Your institution should inform you of such integrations
            where applicable.
          </p>
        </section>

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="6"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            6. Changes to This Cookie Policy
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed">
            This Cookie &amp; Tracking Policy may be updated as NurSync evolves or as institutional practices
            change. Updated versions will be posted in the NurSync application. Continued use after changes
            indicates your acknowledgement of the updated Policy.
          </p>
        </section>

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="7"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            7. Contact
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed">
            If you have questions about how cookies or similar technologies are used in NurSync, please
            contact your institution’s NurSync administrator or Data Protection Officer (DPO), if applicable.
          </p>
        </section>

      </div>

      {{-- Back button --}}
      <div
        class="mt-10 opacity-0 translate-y-5 transition-all duration-500 ease-out"
        data-animate="stagger"
        data-animate-index="8"
      >
        <button
          type="button"
          onclick="window.history.back()"
          class="inline-flex items-center px-5 py-2.5 text-sm font-medium rounded-xl border border-slate-300 bg-white text-slate-800 hover:bg-slate-50 hover:shadow-md transition-all duration-200"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m12 19-7-7 7-7"></path>
            <path d="M19 12H5"></path>
          </svg>
          Back to previous page
        </button>
      </div>

    </div>
  </div>
</main>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const els = Array.from(document.querySelectorAll('[data-animate="stagger"]'))
      .sort((a, b) => {
        const ia = parseInt(a.getAttribute('data-animate-index') || '0', 10);
        const ib = parseInt(b.getAttribute('data-animate-index') || '0', 10);
        return ia - ib;
      });

    els.forEach((el, idx) => {
      setTimeout(() => {
        el.classList.remove('opacity-0', 'translate-y-5');
        el.classList.add('opacity-100', 'translate-y-0');
      }, 80 * idx);
    });
  });
</script>

</body>
</html>
