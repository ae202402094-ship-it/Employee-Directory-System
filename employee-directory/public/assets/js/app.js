// ============================================================
// app.js — Employee Directory System
// ============================================================

document.addEventListener('DOMContentLoaded', () => {

    // ── Sidebar toggle (mobile) ────────────────────────────────
    const sidebar        = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarToggle  = document.getElementById('sidebarToggle');

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

    // ── Flash auto-dismiss ─────────────────────────────────────
    const flashMsg = document.getElementById('flashMsg');
    if (flashMsg) {
        setTimeout(() => {
            flashMsg.style.transition = 'opacity .4s';
            flashMsg.style.opacity = '0';
            setTimeout(() => flashMsg.remove(), 400);
        }, 4500);
    }

    // ── Command palette ────────────────────────────────────────
    const cmdPalette = document.getElementById('cmdPalette');
    const cmdInput   = document.getElementById('cmdInput');
    const cmdResults = document.getElementById('cmdResults');
    const openBtn    = document.getElementById('openCmdPalette');

    let debounceTimer = null;

    function showPalette() {
        if (!cmdPalette) return;
        cmdPalette.style.display = 'flex';
        setTimeout(() => cmdInput?.focus(), 50);
    }

    function hidePalette() {
        if (!cmdPalette) return;
        cmdPalette.style.display = 'none';
        if (cmdInput) cmdInput.value = '';
        if (cmdResults) cmdResults.innerHTML = '<div class="cmd-empty">Type to start searching…</div>';
    }

    openBtn?.addEventListener('click', showPalette);

    cmdPalette?.addEventListener('click', (e) => {
        if (e.target === cmdPalette) hidePalette();
    });

    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            cmdPalette?.style.display === 'none' ? showPalette() : hidePalette();
        }
        if (e.key === 'Escape') hidePalette();
    });

    cmdInput?.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        const term = cmdInput.value.trim();

        if (!term) {
            cmdResults.innerHTML = '<div class="cmd-empty">Type to start searching…</div>';
            return;
        }

        cmdResults.innerHTML = '<div class="cmd-empty">Searching…</div>';

        debounceTimer = setTimeout(async () => {
            try {
                const res  = await fetch(`${BASE_URL}/employees/search?q=${encodeURIComponent(term)}`);
                const json = await res.json();

                if (!json.data || json.data.length === 0) {
                    cmdResults.innerHTML = '<div class="cmd-empty">No employees found.</div>';
                    return;
                }

                cmdResults.innerHTML = json.data.map(emp => `
                    <a class="cmd-item" href="${BASE_URL}/employees/${emp.id}">
                        <div class="cmd-item-avatar">${emp.name.split(' ').map(n => n[0]).join('').slice(0,2).toUpperCase()}</div>
                        <div>
                            <div class="cmd-item-name">${escHtml(emp.name)}</div>
                            <div class="cmd-item-meta">${escHtml(emp.employee_number)} · ${escHtml(emp.position || '—')} · ${escHtml(emp.department_name || '—')}</div>
                        </div>
                        <span class="badge badge-${emp.status}" style="margin-left:auto">${capitalize(emp.status)}</span>
                    </a>
                `).join('');
            } catch {
                cmdResults.innerHTML = '<div class="cmd-empty">Search failed. Try again.</div>';
            }
        }, 280);
    });

    // ── Helpers ─────────────────────────────────────────────────
    function escHtml(str) {
        return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function capitalize(str) {
        return String(str ?? '').charAt(0).toUpperCase() + String(str ?? '').slice(1);
    }

});