/* =========================================================
   Fast partial transitions between /login and /register
   - Same-origin fetch + DOM swap of #page-root only
   - View Transitions when available; graceful fallback
   - In-memory cache + hover prefetch
   - Auth page init: animations + register wizard

   Plus:
   - Landing page fade-up animations for sections
   - Header scroll state (CodeCred-style)
   - Smooth in-page anchor scrolling with header offset
   ========================================================= */
(() => {
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const canVT = !!document.startViewTransition && !prefersReduced;
  const isAuthPath = p => p === '/login' || p === '/register';
  const cache = new Map();                  // href -> { html, t }
  const CACHE_TTL = 30_000;                 // 30s is plenty for these pages
  let inflight = null;                      // AbortController for current fetch

  const $root = () => document.getElementById('page-root');
  const headerHeight = () => (document.querySelector('header.sticky')?.getBoundingClientRect().height || 0);

  /* -----------------------------------------
     Auth page init (animations + wizard)
     ----------------------------------------- */
  const initAuthPage = () => {
    // Entrance animation (login & register)
    const quote = document.querySelector('[data-anim="quote"]');
    const panel = document.querySelector('[data-anim="panel"]');

    window.requestAnimationFrame(() => {
      if (quote) {
        quote.style.transition = 'opacity 600ms ease, transform 600ms ease';
        quote.style.opacity = '1';
        quote.style.transform = 'translateY(0)';
      }

      if (panel) {
        panel.style.transition = 'opacity 600ms ease, transform 600ms ease';
        panel.style.opacity = '1';
        panel.style.transform = 'translateX(0)';
      }
    });

    // Registration wizard (only present on /register)
    const registerForm = document.getElementById('registerForm');
    if (!registerForm) return; // we're on /login, no wizard

    // Prevent double-binding on repeated partial navs
    if (registerForm.dataset.wizardInit === '1') return;
    registerForm.dataset.wizardInit = '1';

    const track     = document.getElementById('paneTrack');
    const btnNext   = document.getElementById('btnNext');
    const btnBack   = document.getElementById('btnBack');
    const btnSubmit = document.getElementById('btnSubmit');
    const stepDot1  = document.getElementById('stepDot1');
    const stepDot2  = document.getElementById('stepDot2');
    const preview   = document.getElementById('regcardPreview');
    const fileInput = document.getElementById('regcard_file');

    if (!track || !btnNext || !btnBack || !btnSubmit || !stepDot1 || !stepDot2 || !fileInput) {
      return;
    }

    // Initial step comes from Blade: data-open-step="1" or "2"
    let step = Number(registerForm.dataset.openStep || '1');

    function go(stepNum) {
      step = stepNum;
      const offset = (step - 1) * -100;
      track.style.transform = `translateX(${offset}%)`;

      // dots
      stepDot1.classList.toggle('bg-slate-900', step === 1);
      stepDot1.classList.toggle('bg-slate-300', step !== 1);
      stepDot2.classList.toggle('bg-slate-900', step === 2);
      stepDot2.classList.toggle('bg-slate-300', step !== 2);

      // buttons
      btnBack.classList.toggle('hidden', step === 1);
      btnNext.classList.toggle('hidden', step === 2);
      btnSubmit.classList.toggle('hidden', step === 1);

      // required toggling
      if (step === 2) {
        fileInput.setAttribute('required', 'required');
      } else {
        fileInput.removeAttribute('required');
      }
    }

    function step1Valid() {
      const need = ['full_name', 'email', 'student_no', 'password', 'password_confirmation'];
      for (const id of need) {
        const el = document.getElementById(id);
        if (!el || !el.value.trim()) return false;
      }
      return true;
    }

    btnNext.addEventListener('click', () => {
      if (!step1Valid()) {
        alert('Please complete all fields on Step 1.');
        return;
      }
      go(2);
    });

    btnBack.addEventListener('click', () => go(1));

    fileInput.addEventListener('change', (e) => {
      const f = e.target.files && e.target.files[0];
      if (!f) {
        preview.src = '';
        preview.classList.add('hidden');
        return;
      }
      if (/image\/(png|jpe?g)/i.test(f.type)) {
        const url = URL.createObjectURL(f);
        preview.src = url;
        preview.classList.remove('hidden');
      } else {
        preview.src = '';
        preview.classList.add('hidden');
      }
    });

    // Kick off initial step
    go(step);
  };

  /* -----------------------------------------
     Landing page animations (CodeCred-style)
     ----------------------------------------- */
  const initLandingAnimations = () => {
    // 1) Fade-up on scroll for any element with data-animate="fade-up"
    const els = document.querySelectorAll('[data-animate="fade-up"]');

    if (!('IntersectionObserver' in window) || els.length === 0) {
      // Fallback: just activate everything
      els.forEach(el => {
        el.classList.remove('fade-up-init');
        el.classList.add('fade-up-active');
      });
    } else {
      const observer = new IntersectionObserver(
        entries => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              const el = entry.target;
              el.classList.remove('fade-up-init');
              el.classList.add('fade-up-active');
              observer.unobserve(el);
            }
          });
        },
        { threshold: 0.15 }
      );

      els.forEach(el => observer.observe(el));
    }

    // 2) Header scroll treatment (shadow + slightly stronger bg after scroll)
    const header = document.querySelector('header.sticky');
    if (header) {
      const updateHeader = () => {
        const scrolled = window.scrollY > 8;
        header.classList.toggle('shadow-sm', scrolled);
        header.classList.toggle('bg-white/90', !scrolled);
        header.classList.toggle('bg-white/95', scrolled);
        header.classList.toggle('backdrop-blur', scrolled);
      };

      updateHeader();
      window.addEventListener('scroll', updateHeader, { passive: true });
    }

    // 3) Smooth in-page anchor scrolling (e.g. #how, #about from header/footer)
    document.addEventListener('click', (e) => {
      const a = e.target.closest('a[href]');
      if (!a) return;

      const rawHref = a.getAttribute('href');
      if (!rawHref) return;

      let url;
      try {
        url = new URL(rawHref, location.href);
      } catch {
        return;
      }

      // Only handle same-page hash links
      if (url.pathname !== location.pathname) return;
      if (!url.hash) return;

      const id = url.hash.slice(1);
      if (!id) return;

      const target = document.getElementById(id);
      if (!target) return;

      // Let the partial-transition logic handle /login,/register; here we only care for normal content
      // If it's an auth link (# in login/register), ignore.
      if (isAuthPath(url.pathname)) return;

      e.preventDefault();

      const top =
        window.scrollY +
        target.getBoundingClientRect().top -
        headerHeight() -
        8;

      window.scrollTo({
        top,
        left: 0,
        behavior: prefersReduced ? 'auto' : 'smooth',
      });
    }, { passive: false });
  };

  /* -----------------------------------------
     Cache helpers
     ----------------------------------------- */
  const readCached = (href) => {
    const hit = cache.get(href);
    if (!hit) return null;
    if (Date.now() - hit.t > CACHE_TTL) {
      cache.delete(href);
      return null;
    }
    return hit.html;
  };

  const writeCache = (href, html) => cache.set(href, { html, t: Date.now() });

  const prefetch = (href) => {
    try {
      const url = new URL(href, location.href);
      if (!isAuthPath(url.pathname)) return;
      if (readCached(url.href)) return;
      fetch(url.href, { credentials: 'same-origin', mode: 'same-origin' })
        .then(r => (r.ok ? r.text() : ''))
        .then(html => { if (html) writeCache(url.href, html); })
        .catch(() => {});
    } catch { /* ignore */ }
  };

  // Hover prefetch (for auth links)
  document.addEventListener('mouseenter', (e) => {
    const a = e.target.closest('a[href]');
    if (!a) return;
    prefetch(a.getAttribute('href') || '');
  }, true);

  /* -----------------------------------------
     Core swap: parse + replace #page-root
     ----------------------------------------- */
  const swapContent = (nextHTML, url) => {
    const parser = new DOMParser();
    const doc = parser.parseFromString(nextHTML, 'text/html');

    const nextRoot = doc.getElementById('page-root');
    const currRoot = $root();
    if (!nextRoot || !currRoot) {
      location.href = url;
      return;
    }

    // Update title
    const newTitle = doc.querySelector('title')?.textContent ?? document.title;
    if (newTitle) document.title = newTitle;

    const performSwap = () => {
      // Replace content
      currRoot.replaceWith(nextRoot);

      // Re-run any inline scripts in new #page-root (if ever used)
      nextRoot.querySelectorAll('script').forEach((oldS) => {
        const s = document.createElement('script');
        if (oldS.src) {
          s.src = oldS.src;
          s.defer = oldS.defer;
          s.async = oldS.async;
        } else {
          s.textContent = oldS.textContent;
        }
        if (oldS.type) s.type = oldS.type;
        oldS.replaceWith(s);
      });

      // Accessibility & scroll restore (offset by sticky header)
      const firstFocusable = nextRoot.querySelector('[autofocus], input, button, [tabindex]:not([tabindex="-1"])');
      if (firstFocusable) {
        firstFocusable.focus({ preventScroll: true });
      }
      window.scrollTo({ top: 0, left: 0 });

      // If there was a hash, scroll to it after paint
      if (url.hash) {
        setTimeout(() => {
          const id = url.hash.slice(1);
          const target = document.getElementById(id);
          if (target) {
            const top =
              window.scrollY +
              target.getBoundingClientRect().top -
              headerHeight() -
              8;

            window.scrollTo({
              top,
              left: 0,
              behavior: prefersReduced ? 'auto' : 'smooth',
            });

            target.setAttribute('tabindex', '-1');
            target.focus({ preventScroll: true });
          }
        }, 0);
      }

      // Let app know we swapped
      document.dispatchEvent(new CustomEvent('partial:navigated', { detail: { url: url.href } }));

      // 🔁 Re-init auth animations + wizard after partial swap
      if (isAuthPath(url.pathname)) {
        initAuthPage();
      }
    };

    if (canVT) {
      document.startViewTransition(performSwap);
    } else {
      const root = currRoot;
      root.style.transition = 'opacity .14s ease, transform .14s ease';
      root.style.opacity = '0';
      root.style.transform = 'translateY(6px)';
      setTimeout(() => {
        performSwap();
      }, 120);
    }
  };

  const shouldIntercept = (from, to) =>
    isAuthPath(from.pathname) && isAuthPath(to.pathname);

  /* -----------------------------------------
     Click interception for auth links
     ----------------------------------------- */
  document.addEventListener('click', (e) => {
    if (e.defaultPrevented || e.button !== 0) return;

    const a = e.target.closest('a[href]');
    if (!a) return;

    if (a.hasAttribute('data-no-transition')) return;
    if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
    if (a.target && a.target !== '_self') return;
    if (a.hasAttribute('download')) return;

    let url;
    try {
      url = new URL(a.getAttribute('href'), location.href);
    } catch {
      return;
    }
    if (url.origin !== location.origin) return;

    // Ignore true in-page anchors
    if (url.pathname === location.pathname && url.hash) return;

    if (!shouldIntercept(new URL(location.href), url)) return;

    e.preventDefault();

    // Abort any previous fetch
    if (inflight) inflight.abort();
    inflight = new AbortController();

    const cached = readCached(url.href);
    if (cached) {
      history.pushState(null, '', url.href);
      swapContent(cached, url);
      return;
    }

    fetch(url.href, { signal: inflight.signal, credentials: 'same-origin', mode: 'same-origin' })
      .then(r => (r.ok ? r.text() : Promise.reject(new Error('Bad status'))))
      .then(html => {
        writeCache(url.href, html);
        history.pushState(null, '', url.href);
        swapContent(html, url);
      })
      .catch(() => {
        location.href = url.href;
      }); // hard navigate on failures
  });

  /* -----------------------------------------
     Back/forward handling with cache
     ----------------------------------------- */
  window.addEventListener('popstate', () => {
    const url = new URL(location.href);
    if (!shouldIntercept(url, url)) return; // only handle auth pages
    const cached = readCached(url.href);
    if (cached) {
      swapContent(cached, url);
    } else {
      fetch(url.href, { credentials: 'same-origin', mode: 'same-origin' })
        .then(r => (r.ok ? r.text() : ''))
        .then(html => (html ? swapContent(html, url) : location.reload()));
    }
  });

  /* -----------------------------------------
     Initial init on first load
     ----------------------------------------- */
  const runInitial = () => {
    if (isAuthPath(location.pathname)) {
      initAuthPage();
    }
    // Landing animations are harmless if the page
    // doesn’t have any of the selectors we use.
    initLandingAnimations();
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', runInitial);
  } else {
    runInitial();
  }
})();
