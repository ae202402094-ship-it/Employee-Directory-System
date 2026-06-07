/* ============================================================
   Employee Directory System — app.js
   Vanilla JS: Sidebar toggle, modals, flash auto-dismiss,
   password toggle, form validation, live search, Command Palette
   ============================================================ */

'use strict';

document.addEventListener('DOMContentLoaded', function () {

    // ── 1. SIDEBAR & MOBILE NAVIGATION ───────────────────────
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

    // ── 2. MODAL SYSTEM ──────────────────────────────────────
    function closeAllModals() {
        document.querySelectorAll('.modal.open').forEach(m => m.classList.remove('open'));
    }

    window.openModal = function (id) {
        closeAllModals();
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('open');
            const first = modal.querySelector('input:not([type="hidden"]), select, textarea');
            setTimeout(() => first?.focus(), 100);
        }
    };

    window.closeModal = function (id) {
        document.getElementById(id)?.classList.remove('open');
    };

    window.openEditDept = window.openEditDept || function () {};

    // ── 3. AUTH & UTILITIES ──────────────────────────────────
    
    // Flash Message Auto-dismiss
    const flashMsg = document.getElementById('flashMsg');
    if (flashMsg) {
        setTimeout(() => {
            flashMsg.style.transition = 'opacity .4s ease';
            flashMsg.style.opacity = '0';
            setTimeout(() => flashMsg.remove(), 400);
        }, 4000);
    }

    // Password Toggle
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput  = document.getElementById('password');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            
            this.innerHTML = isPassword
                ? `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>`
                : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`;
        });
    }

    // ── 4. FORM VALIDATION & UI INTERACTION ──────────────────
    
    // Client-side Form Validation
    document.querySelectorAll('form[novalidate]').forEach(form => {
        form.addEventListener('submit', function (e) {
            let valid = true;
            this.querySelectorAll('[required]').forEach(input => {
                const group = input.closest('.form-group');
                if (!input.value.trim()) {
                    if (group) group.classList.add('has-error');
                    if (group && !group.querySelector('.form-error')) {
                        const err = document.createElement('span');
                        err.className = 'form-error js-error';
                        err.textContent = (input.labels?.[0]?.textContent?.replace(' *', '') || 'This field') + ' is required.';
                        group.appendChild(err);
                    }
                    valid = false;
                } else {
                    group?.classList.remove('has-error');
                    group?.querySelector('.form-error.js-error')?.remove();
                }
            });
            if (!valid) e.preventDefault();
        });

        form.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function () {
                const group = this.closest('.form-group');
                if (group && this.value.trim()) {
                    group.classList.remove('has-error');
                    group.querySelector('.form-error.js-error')?.remove();
                }
            });
        });
    });

    // Confirm Delete Buttons
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', (e) => {
            if (!confirm(el.dataset.confirm)) e.preventDefault();
        });
    });

    // ── 5. SEARCH & NAVIGATION ───────────────────────────────
    
    // Live Search
    const searchInput = document.querySelector('input[name="search"]');
    let searchTimer   = null;
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            const q = this.value.trim();
            if (q.length < 2 && q.length > 0) return;
            searchTimer = setTimeout(() => this.closest('form')?.submit(), 350);
        });
    }

    // Auto-highlight active nav
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-item').forEach(item => {
        const href = item.getAttribute('href');
        if (href && href !== '/' && currentPath.startsWith(href)) item.classList.add('active');
    });

    // Table Row Clickable
    document.querySelectorAll('.data-table tbody tr[data-href]').forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', (e) => {
            if (!e.target.closest('button, a, form')) window.location.href = row.dataset.href;
        });
    });

    // ── 6. COMMAND PALETTE (CTRL+K) ──────────────────────────
    const cmdPalette = document.getElementById('cmdPalette');
    const cmdInput   = document.getElementById('cmdInput');
    const cmdResults = document.getElementById('cmdResults');

    if (cmdPalette) {
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault(); 
                cmdPalette.style.display = 'flex';
                cmdInput.value = '';
                cmdResults.innerHTML = '<div class="cmd-empty">Type to start searching...</div>';
                cmdInput.focus();
            }
            if (e.key === 'Escape' && cmdPalette.style.display === 'flex') cmdPalette.style.display = 'none';
        });

        cmdPalette.addEventListener('click', (e) => {
            if (e.target === cmdPalette) cmdPalette.style.display = 'none';
        });

        let cmdTimer;
        cmdInput.addEventListener('input', function() {
            clearTimeout(cmdTimer);
            const query = this.value.trim();
            if (query.length < 2) {
                cmdResults.innerHTML = '<div class="cmd-empty">Type at least 2 characters...</div>';
                return;
            }
            cmdResults.innerHTML = '<div class="cmd-empty">Searching...</div>';
            cmdTimer = setTimeout(() => {
                fetch(`${BASE_URL}/employees/search?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(json => {
                        if (!json.data || json.data.length === 0) {
                            cmdResults.innerHTML = '<div class="cmd-empty">No employees found.</div>';
                            return;
                        }
                        cmdResults.innerHTML = json.data.map(emp => `
                            <a href="${BASE_URL}/employees/${emp.id}" class="cmd-result-item">
                                <div class="table-emp">
                                    <div class="emp-avatar emp-avatar-sm">${emp.name.substring(0, 2).toUpperCase()}</div>
                                    <div>
                                        <span class="emp-fullname" style="color: var(--text);">${emp.name}</span>
                                        <span class="emp-email">${emp.position || 'No Position'} · ${emp.department_name}</span>
                                    </div>
                                </div>
                                <span class="badge badge-${emp.status}">${emp.status}</span>
                            </a>
                        `).join('');
                    })
                    .catch(() => cmdResults.innerHTML = '<div class="cmd-empty" style="color: red;">Error fetching data.</div>');
            }, 300);
        });
    }

    console.log('[EmpDir] App initialized ✓');
});