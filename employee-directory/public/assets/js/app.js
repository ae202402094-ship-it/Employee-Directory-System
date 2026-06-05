/* ============================================================
   Employee Directory System — app.js
   Vanilla JS: Sidebar toggle, modals, flash auto-dismiss,
   password toggle, confirm dialogs, live search
   ============================================================ */

'use strict';

document.addEventListener('DOMContentLoaded', function () {

    // ── Sidebar Toggle (mobile) ──────────────────────────────
    const sidebarToggle  = document.getElementById('sidebarToggle');
    const sidebar        = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    function openSidebar() {
        sidebar?.classList.add('open');
        sidebarOverlay?.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar?.classList.remove('open');
        sidebarOverlay?.classList.remove('open');
        document.body.style.overflow = '';
    }

    sidebarToggle?.addEventListener('click', openSidebar);
    sidebarOverlay?.addEventListener('click', closeSidebar);

    // Close sidebar on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeSidebar();
            closeAllModals();
        }
    });

    // ── Flash Message Auto-dismiss ───────────────────────────
    const flashMsg = document.getElementById('flashMsg');
    if (flashMsg) {
        setTimeout(() => {
            flashMsg.style.transition = 'opacity .4s ease';
            flashMsg.style.opacity = '0';
            setTimeout(() => flashMsg.remove(), 400);
        }, 4000);
    }

    // ── Password Toggle ──────────────────────────────────────
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput  = document.getElementById('password');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';

            // Swap icon
            this.innerHTML = isPassword
                ? `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                     <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                     <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                     <line x1="1" y1="1" x2="23" y2="23"/>
                   </svg>`
                : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                     <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                     <circle cx="12" cy="12" r="3"/>
                   </svg>`;
        });
    }

    // ── Modal System ─────────────────────────────────────────
    function closeAllModals() {
        document.querySelectorAll('.modal.open').forEach(m => m.classList.remove('open'));
    }

    // Expose globally for onclick attributes in views
    window.openModal = function (id) {
        closeAllModals();
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('open');
            // Focus first input
            const first = modal.querySelector('input:not([type="hidden"]), select, textarea');
            setTimeout(() => first?.focus(), 100);
        }
    };

    window.closeModal = function (id) {
        document.getElementById(id)?.classList.remove('open');
    };

    window.openEditDept = window.openEditDept || function () {}; // defined in view

    // ── Client-side Form Validation Feedback ─────────────────
    document.querySelectorAll('form[novalidate]').forEach(form => {
        form.addEventListener('submit', function (e) {
            let valid = true;

            this.querySelectorAll('[required]').forEach(input => {
                const group = input.closest('.form-group');
                if (!input.value.trim()) {
                    if (group) group.classList.add('has-error');
                    if (group && !group.querySelector('.form-error')) {
                        const err = document.createElement('span');
                        err.className = 'form-error';
                        err.textContent = (input.labels?.[0]?.textContent?.replace(' *', '') || 'This field') + ' is required.';
                        group.appendChild(err);
                    }
                    valid = false;
                } else {
                    if (group) {
                        group.classList.remove('has-error');
                        group.querySelector('.form-error.js-error')?.remove();
                    }
                }
            });

            if (!valid) e.preventDefault();
        });

        // Clear error on input
        form.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function () {
                const group = this.closest('.form-group');
                if (group && this.value.trim()) {
                    group.classList.remove('has-error');
                }
            });
        });
    });

    // ── Confirm Delete Buttons (extra safety) ────────────────
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', function (e) {
            if (!confirm(this.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });

    // ── Live Search (AJAX — employees) ───────────────────────
    const searchInput = document.querySelector('input[name="search"]');
    let searchTimer   = null;

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            const q = this.value.trim();
            if (q.length < 2 && q.length > 0) return;

            // Submit the filter form after debounce (300ms)
            searchTimer = setTimeout(() => {
                this.closest('form')?.submit();
            }, 350);
        });
    }

    // ── Auto-highlight active nav on subpages ────────────────
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-item').forEach(item => {
        const href = item.getAttribute('href');
        if (href && href !== '/' && currentPath.startsWith(href)) {
            item.classList.add('active');
        }
    });

    // ── Table Row Clickable (click row to view employee) ─────
    document.querySelectorAll('.data-table tbody tr[data-href]').forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function (e) {
            if (e.target.closest('button, a, form')) return;
            window.location.href = this.dataset.href;
        });
    });

    console.log('[EmpDir] App initialized ✓');
});
