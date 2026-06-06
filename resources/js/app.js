import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// ── Polling notifikasi badge admin ──────────────────────────
function pollAdminNotifBadge() {
    const badge = document.getElementById('admin-notif-badge');
    if (!badge) return;

    const route = badge.dataset.route;

    setInterval(async () => {
        try {
            const res  = await fetch(route, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();

            if (data.count > 0) {
                badge.textContent = data.count > 99 ? '99+' : data.count;
                badge.classList.remove('hidden');
                badge.classList.add('inline-flex');
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('inline-flex');
            }
        } catch (_) {}
    }, 15000); // setiap 15 detik
}

document.addEventListener('DOMContentLoaded', pollAdminNotifBadge);