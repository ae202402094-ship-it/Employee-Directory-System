document.addEventListener('DOMContentLoaded', () => {
    // ── 1. Sidebar Toggle ────────────────────────────────
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarToggle = document.getElementById('sidebarToggle');

    sidebarToggle?.addEventListener('click', () => {
        sidebar?.classList.add('open');
        sidebarOverlay?.classList.add('open');
        document.body.style.overflow = 'hidden';
    });

    sidebarOverlay?.addEventListener('click', () => {
        sidebar?.classList.remove('open');
        sidebarOverlay?.classList.remove('open');
        document.body.style.overflow = '';
    });

    // ── 2. Background Location Sync ────────────────────────
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                fetch(`${BASE_URL}/location/sync`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ lat: pos.coords.latitude, lng: pos.coords.longitude, status: 'available' })
                }).catch(console.error);
            },
            () => {}, // Silent failure
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }

    // ── 3. Initialize Address Selector (From library) ──────────
    if (typeof initAddressSelector === 'function') {
        initAddressSelector();
    }
});