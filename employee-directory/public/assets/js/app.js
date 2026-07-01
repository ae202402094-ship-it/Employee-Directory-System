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
    if ("geolocation" in navigator && typeof IS_TRACKING_ACTIVE !== 'undefined' && IS_TRACKING_ACTIVE) {
        navigator.geolocation.watchPosition(
            (pos) => {
                fetch(`${BASE_URL}/location/sync`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ lat: pos.coords.latitude, lng: pos.coords.longitude })
                }).catch(console.error);
            },
            (err) => {
                console.warn("GPS tracking error: ", err);
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    }

    // ── 3. Initialize Address Selector (From library) ──────────
    if (typeof initAddressSelector === 'function') {
        initAddressSelector();
    }
});

// Global Modal Helpers
window.openModal = function(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
};

window.closeModal = function(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('open');
        document.body.style.overflow = '';
    }
};