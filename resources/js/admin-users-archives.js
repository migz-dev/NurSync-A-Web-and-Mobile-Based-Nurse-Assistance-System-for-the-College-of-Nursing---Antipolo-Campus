// resources/js/admin-users-archives.js
// Archived Users — Restore & Delete with SweetAlert2 (no modals)

document.addEventListener('DOMContentLoaded', () => {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  const request = async (url, method = 'GET', body = null) => {
    const res = await fetch(url, {
      method,
      headers: {
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json',
        ...(body ? { 'Content-Type': 'application/json' } : {})
      },
      credentials: 'same-origin',
      body: body ? JSON.stringify(body) : null
    });
    let payload = null;
    try { payload = await res.json(); } catch {}
    if (!res.ok) {
      // Common Laravel CSRF response
      if (res.status === 419) throw new Error('Session expired. Please refresh the page and try again.');
      throw new Error(payload?.message || `Request failed (${res.status})`);
    }
    return payload;
  };

  const toast = (icon, title, text = '') => {
    if (window.Swal) {
      window.Swal.fire({ icon, title, text, timer: 1400, showConfirmButton: false });
    } else {
      alert(`${title}${text ? '\n' + text : ''}`);
    }
  };

  const setBusy = (btn, busy) => {
    if (!btn) return;
    btn.disabled = !!busy;
    btn.setAttribute('aria-busy', busy ? 'true' : 'false');
  };

  // Restore handler (POST)
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.js-restore');
    if (!btn) return;

    const row = btn.closest('tr');
    const id  = btn.dataset.userId || row?.dataset.rowId;
    const url = btn.dataset.restoreUrl;
    if (!url || !id) return;

    let confirmed = true;
    if (window.Swal) {
      const res = await Swal.fire({
        icon: 'question',
        title: 'Restore this user?',
        text: 'The account will be moved back to active users.',
        showCancelButton: true,
        confirmButtonText: 'Yes, restore',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#059669' // emerald-600
      });
      confirmed = res.isConfirmed;
    } else {
      confirmed = confirm('Restore this user?');
    }
    if (!confirmed) return;

    try {
      setBusy(btn, true);
      await request(url, 'POST');
      (document.querySelector(`tr[data-row-id="${CSS.escape(id)}"]`) || row)?.remove();
      toast('success', 'Restored');
    } catch (err) {
      toast('error', 'Restore failed', err.message || 'Something went wrong.');
    } finally {
      setBusy(btn, false);
    }
  });

  // Delete handler (POST — your route doesn’t accept DELETE)
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.js-delete');
    if (!btn) return;

    const row = btn.closest('tr');
    const id  = btn.dataset.userId || row?.dataset.rowId;
    const url = btn.dataset.destroyUrl;
    if (!url || !id) return;

    let confirmed = true;
    if (window.Swal) {
      const res = await Swal.fire({
        icon: 'warning',
        title: 'Delete this user permanently?',
        text: 'This action cannot be undone.',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#e11d48' // rose-600
      });
      confirmed = res.isConfirmed;
    } else {
      confirmed = confirm('Delete this user permanently? This cannot be undone.');
    }
    if (!confirmed) return;

    try {
      setBusy(btn, true);
      // Use POST (matches your route’s allowed methods)
      await request(url, 'POST');
      (document.querySelector(`tr[data-row-id="${CSS.escape(id)}"]`) || row)?.remove();
      toast('success', 'Deleted', 'The user has been permanently deleted.');
    } catch (err) {
      toast('error', 'Delete failed', err.message || 'Something went wrong.');
    } finally {
      setBusy(btn, false);
    }
  });
});