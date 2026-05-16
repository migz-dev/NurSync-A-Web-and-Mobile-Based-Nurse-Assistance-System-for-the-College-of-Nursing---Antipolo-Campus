{{-- resources/views/legal/terms.blade.php --}}
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Terms of Service · NurSync</title>

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

      {{-- Page heading --}}
      <header
        class="mb-10 opacity-0 translate-y-5 transition-all duration-500 ease-out"
        data-animate="stagger"
        data-animate-index="0"
      >
        <p class="text-xs font-semibold tracking-[0.18em] uppercase text-emerald-600 mb-2">
          Legal Information
        </p>
        <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-slate-900">
          Terms of Service
        </h1>
        <p class="mt-4 text-base md:text-lg text-slate-600 leading-relaxed">
          Welcome to <span class="font-semibold">NurSync</span>. By accessing or using our web and mobile
          applications, you agree to the terms and conditions outlined below. Please read them carefully
          before using the system.
        </p>
      </header>

      {{-- Legal sections (cards) --}}
      <div class="space-y-6 md:space-y-8">

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="1"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            1. Purpose of NurSync
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed">
            NurSync is a nurse assistance and training support system designed primarily for use in academic
            and clinical training environments. It provides tools such as chartings, procedure guides,
            reference materials, and simulation aids to support learning and documentation workflows.
            NurSync is <span class="font-semibold">not</span> a substitute for professional clinical judgement
            or hospital policies.
          </p>
        </section>

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="2"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            2. User Accounts & Roles
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed mb-3">
            Access to NurSync may be granted under different roles (e.g., Admin, Clinical Instructor/Registered
            Nurse, Student Nurse). You are responsible for maintaining the confidentiality of your account
            credentials and for all activities that occur under your account.
          </p>
          <ul class="list-disc pl-5 space-y-1 text-sm md:text-base text-slate-600">
            <li>Do not share your username or password with others.</li>
            <li>Notify the system administrator immediately if you suspect unauthorized use of your account.</li>
            <li>Use only the modules and data that your role has been authorized to access.</li>
          </ul>
        </section>

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="3"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            3. Acceptable Use
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed mb-3">
            You agree to use NurSync in a responsible and ethical manner. You must not:
          </p>
          <ul class="list-disc pl-5 space-y-1 text-sm md:text-base text-slate-600">
            <li>Upload false, misleading, or fabricated records or chartings.</li>
            <li>Use the system to harass, discriminate, or violate the rights of others.</li>
            <li>Attempt to gain unauthorized access to other accounts or system components.</li>
            <li>Reverse engineer, copy, or redistribute the system without proper authorization.</li>
          </ul>
        </section>

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="4"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            4. Educational & Clinical Disclaimer
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed">
            Content provided by NurSync (including procedure guides, checklists, and reference materials) is
            intended for educational and training support only. It may not reflect all requirements of your
            institution, hospital, or regulatory body. Always follow:
          </p>
          <ul class="list-disc pl-5 mt-3 space-y-1 text-sm md:text-base text-slate-600">
            <li>Existing hospital and clinical policies and protocols.</li>
            <li>Direct orders and supervision of licensed healthcare professionals.</li>
            <li>Your institution’s guidelines, curriculum, and regulations.</li>
          </ul>
        </section>

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="5"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            5. Data, Privacy & Security
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed">
            NurSync may process information such as user profiles, academic records, and simulated or
            de-identified patient records depending on how it is configured by your institution. We implement
            reasonable technical and organizational measures to protect this data; however, no system is fully
            risk-free. For more information, please refer to the
            <span class="font-semibold">NurSync Privacy Policy</span>.
          </p>
        </section>

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="6"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            6. Intellectual Property
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed">
            The NurSync platform, including its design, code, logos, and content created by the development
            team, is protected by copyright and other intellectual property laws. You may not copy, modify,
            distribute, or create derivative works without explicit permission except where allowed by
            applicable law or institutional agreements.
          </p>
        </section>

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="7"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            7. Suspension & Termination
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed">
            Your access to NurSync may be suspended or terminated by the system administrators or institution
            if you violate these Terms, institutional policies, or if your affiliation (e.g., student
            enrollment, employment) ends. Certain records may still be retained as required for academic,
            audit, or legal purposes.
          </p>
        </section>

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="8"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            8. Changes to These Terms
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed">
            These Terms of Service may be updated from time to time to reflect changes in the system, legal
            requirements, or institutional policies. When changes are made, an updated version will be posted
            in the NurSync application. Continued use of NurSync after such changes constitutes your
            acceptance of the updated Terms.
          </p>
        </section>

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="9"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            9. Contact Information
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed">
            For questions or concerns regarding these Terms of Service, please contact your institution’s
            NurSync administrator or the designated project team.
          </p>
        </section>

      </div>

      {{-- Back button --}}
      <div
        class="mt-10 opacity-0 translate-y-5 transition-all duration-500 ease-out"
        data-animate="stagger"
        data-animate-index="10"
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

{{-- Simple staggered slide-up animation --}}
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
