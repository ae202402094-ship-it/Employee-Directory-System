<div class="dashboard">

    <!-- KPI Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-blue">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?= number_format($stats['total_employees']) ?></span>
                <span class="stat-label">Total Employees</span>
            </div>
        </div>

        <div class="stat-card stat-green">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?= number_format($stats['active_employees']) ?></span>
                <span class="stat-label">Active Employees</span>
            </div>
        </div>

        <div class="stat-card stat-amber">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?= number_format($stats['total_depts']) ?></span>
                <span class="stat-label">Departments</span>
            </div>
        </div>

        <div class="stat-card stat-purple">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?= number_format($stats['new_this_month']) ?></span>
                <span class="stat-label">New This Month</span>
            </div>
        </div>
    </div>

    <!-- Chart + Recent Employees -->
    <div class="dashboard-grid">

        <!-- Department Breakdown Chart -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Employees by Department</h2>
            </div>
            <div class="card-body">
                <canvas id="deptChart" height="260"></canvas>
            </div>
        </div>

        <!-- Recent Employees -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Recently Added</h2>
                <a href="<?= BASE_URL ?>/employees" class="card-link">View all →</a>
            </div>
            <div class="card-body p-0">
                <ul class="recent-list">
                    <?php foreach ($recentEmployees as $emp): ?>
                    <li class="recent-item">
                        <div class="emp-avatar">
                            <?= strtoupper(substr($emp['first_name'], 0, 1) . substr($emp['last_name'], 0, 1)) ?>
                        </div>
                        <div class="emp-info">
                            <span class="emp-name">
                                <a href="<?= BASE_URL ?>/employees/<?= $emp['id'] ?>">
                                    <?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?>
                                </a>
                            </span>
                            <span class="emp-meta"><?= htmlspecialchars($emp['position'] ?? '—') ?> · <?= htmlspecialchars($emp['department_name'] ?? '—') ?></span>
                        </div>
                        <span class="badge badge-<?= $emp['status'] ?>"><?= ucfirst($emp['status']) ?></span>
                    </li>
                    <?php endforeach; ?>
                    <?php if (empty($recentEmployees)): ?>
                    <li class="recent-empty">No employees yet.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

    </div>
</div>

<script>
(function() {
    const deptData = <?= json_encode($deptBreakdown) ?>;
    const labels   = deptData.map(d => d.department);
    const values   = deptData.map(d => parseInt(d.total));
    const colors   = ['#4f46e5','#10b981','#f59e0b','#ef4444','#7c3aed','#0891b2','#db2777'];

    const canvas = document.getElementById('deptChart');
    if (!canvas || !deptData.length) {
        if (canvas) {
            const ctx = canvas.getContext('2d');
            canvas.width = canvas.offsetWidth || 400;
            canvas.height = 260;
            ctx.fillStyle = '#94a3b8';
            ctx.font = '14px Plus Jakarta Sans, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('No department data yet.', canvas.width / 2, 130);
        }
        return;
    }

    const ctx    = canvas.getContext('2d');
    const W      = canvas.offsetWidth || 400;
    const H      = 260;
    canvas.width  = W;
    canvas.height = H;

    const barH   = 30;
    const gap    = 14;
    const labelW = 130;
    const padX   = 16;
    const padY   = 12;
    const maxVal = Math.max(...values, 1);

    ctx.clearRect(0, 0, W, H);

    labels.forEach((label, i) => {
        const y       = padY + i * (barH + gap);
        const barMaxW = W - labelW - padX * 2 - 44;
        const barW    = (values[i] / maxVal) * barMaxW;
        const color   = colors[i % colors.length];

        // Label
        ctx.fillStyle = '#64748b';
        ctx.font = '500 12.5px "Plus Jakarta Sans", sans-serif';
        ctx.textAlign = 'right';
        const shortLabel = label.length > 16 ? label.slice(0, 15) + '…' : label;
        ctx.fillText(shortLabel, labelW, y + barH / 2 + 4.5);

        // Background track
        ctx.fillStyle = '#f1f5f9';
        ctx.beginPath();
        ctx.roundRect(labelW + padX, y, barMaxW, barH, 7);
        ctx.fill();

        // Colored bar
        ctx.fillStyle = color;
        ctx.beginPath();
        ctx.roundRect(labelW + padX, y, Math.max(barW, 8), barH, 7);
        ctx.fill();

        // Value
        ctx.fillStyle = '#1e293b';
        ctx.font = '600 12.5px "Plus Jakarta Sans", sans-serif';
        ctx.textAlign = 'left';
        ctx.fillText(values[i], labelW + padX + Math.max(barW, 8) + 9, y + barH / 2 + 4.5);
    });
})();
</script>