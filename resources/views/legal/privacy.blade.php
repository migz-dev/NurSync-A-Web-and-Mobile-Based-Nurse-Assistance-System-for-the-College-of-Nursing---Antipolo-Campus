{{-- resources/views/legal/privacy.blade.php --}}
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Privacy Policy · NurSync</title>

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
          Privacy Policy
        </h1>
        <p class="mt-4 text-base md:text-lg text-slate-600 leading-relaxed">
          This Privacy Policy explains how <span class="font-semibold">NurSync</span> collects, uses, and
          protects information when the system is used within your institution for academic and clinical
          training support.
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
            1. Scope of This Policy
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed">
            This Policy applies to the use of NurSync by students, clinical instructors, and administrators
            in authorized institutions. It covers personal information, academic data, and simulated or
            de-identified patient information that may be processed through the system.
          </p>
        </section>

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="2"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            2. Information We May Process
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed mb-3">
            Depending on how NurSync is configured by your institution, the system may handle:
          </p>
          <ul class="list-disc pl-5 space-y-1 text-sm md:text-base text-slate-600">
            <li>Basic user information such as name, email, and role (Admin, CI/RN, Student Nurse).</li>
            <li>Academic-related data such as course or rotation assignments and activity logs.</li>
            <li>Charting entries, procedure notes, or simulation data created within the platform.</li>
            <li>Device or technical logs (e.g., IP address, browser type) for security and audit purposes.</li>
          </ul>
        </section>

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="3"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            3. How Information Is Used
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed mb-3">
            NurSync uses information primarily to deliver its core features and support academic and clinical
            training. This may include:
          </p>
          <ul class="list-disc pl-5 space-y-1 text-sm md:text-base text-slate-600">
            <li>Authenticating users and enforcing appropriate role-based access control.</li>
            <li>Displaying relevant chartings, procedure guides, and reference materials.</li>
            <li>Supporting documentation of simulated or supervised clinical tasks.</li>
            <li>Generating reports or summaries for academic tracking and evaluation.</li>
            <li>Detecting misuse and ensuring the security and stability of the system.</li>
          </ul>
        </section>

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="4"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            4. Data Storage & Security
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed">
            Data stored in NurSync is typically hosted on secure servers managed by, or on behalf of, your
            institution. Reasonable technical and administrative safeguards are used to help protect the
            confidentiality, integrity, and availability of information. However, no system can guarantee
            absolute security. Users are expected to follow institutional IT and confidentiality policies at
            all times.
          </p>
        </section>

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="5"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            5. Sharing of Information
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed mb-3">
            Access to information in NurSync is controlled by the institution and is typically limited to:
          </p>
          <ul class="list-disc pl-5 space-y-1 text-sm md:text-base text-slate-600">
            <li>Authorized Admins and Clinical Instructors for academic, clinical, or auditing purposes.</li>
            <li>Individual users (e.g., students) with respect to their own records and authorized modules.</li>
            <li>Technical personnel who maintain or troubleshoot the system under confidentiality obligations.</li>
          </ul>
          <p class="mt-3 text-sm md:text-base text-slate-600">
            Information is not sold for marketing purposes. Any external sharing (e.g., with regulators or
            accreditation bodies) will follow institutional rules and applicable laws.
          </p>
        </section>

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="6"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            6. Data Retention
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed">
            Information may be retained for as long as necessary to support academic requirements, audits,
            accreditation, and legal obligations, as determined by your institution. Records may be archived
            instead of deleted to preserve academic and clinical documentation history.
          </p>
        </section>

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="7"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            7. Your Choices & Responsibilities
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed mb-3">
            Depending on institutional policies and applicable laws, you may have certain rights such as:
          </p>
          <ul class="list-disc pl-5 space-y-1 text-sm md:text-base text-slate-600">
            <li>Requesting correction of inaccurate personal information.</li>
            <li>Requesting clarification about how your data is used within NurSync.</li>
          </ul>
          <p class="mt-3 text-sm md:text-base text-slate-600">
            Users must avoid uploading sensitive or identifiable patient information in ways that conflict
            with hospital or data protection policies unless explicit authorization and safeguards exist.
          </p>
        </section>

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="8"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            8. Updates to This Privacy Policy
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed">
            This Privacy Policy may be updated to reflect changes in the system, institutional practices, or
            applicable laws. Updated versions will be made available in the NurSync application. Continued
            use of NurSync after changes are posted indicates your acknowledgement of the updated Policy.
          </p>
        </section>

        <section
          class="p-6 md:p-7 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md
                 opacity-0 translate-y-5 transition-all duration-500 ease-out"
          data-animate="stagger"
          data-animate-index="9"
        >
          <h2 class="text-xl md:text-2xl font-semibold mb-3 text-slate-900">
            9. Contact & Institutional DPO
          </h2>
          <p class="text-sm md:text-base text-slate-600 leading-relaxed">
            For privacy-related questions, requests, or concerns, please contact your institution’s designated
            NurSync administrator or Data Protection Officer (DPO), if applicable.
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
