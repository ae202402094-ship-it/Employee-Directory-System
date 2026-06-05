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
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
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
                        <div class="emp-avatar"><?= strtoupper(substr($emp['first_name'], 0, 1) . substr($emp['last_name'], 0, 1)) ?></div>
                        <div class="emp-info">
                            <span class="emp-name">
                                <a href="<?= BASE_URL ?>/employees/<?= $emp['id'] ?>">
                                    <?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?>
                                </a>
                            </span>
                            <span class="emp-meta"><?= htmlspecialchars($emp['position'] ?? '—') ?> · <?= htmlspecialchars($emp['department_name'] ?? '—') ?></span>
                        </div>
                        <span class="badge badge-<?= $emp['status'] ?>"><?= $emp['status'] ?></span>
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
// Department chart
(function() {
    const deptData = <?= json_encode($deptBreakdown) ?>;
    const labels   = deptData.map(d => d.department);
    const values   = deptData.map(d => parseInt(d.total));
    const colors   = ['#2563eb','#16a34a','#d97706','#dc2626','#7c3aed','#0891b2'];

    const canvas = document.getElementById('deptChart');
    if (!canvas || !deptData.length) return;

    const ctx = canvas.getContext('2d');
    const total = values.reduce((a, b) => a + b, 0);
    const W = canvas.offsetWidth || 400;
    const H = 260;
    canvas.width  = W;
    canvas.height = H;

    // Simple horizontal bar chart
    const barH   = 28;
    const gap    = 12;
    const labelW = 120;
    const padX   = 16;
    const padY   = 20;
    const maxVal = Math.max(...values, 1);

    ctx.clearRect(0, 0, W, H);
    ctx.font = '500 13px DM Sans, sans-serif';

    labels.forEach((label, i) => {
        const y       = padY + i * (barH + gap);
        const barMaxW = W - labelW - padX * 2 - 40;
        const barW    = (values[i] / maxVal) * barMaxW;
        const color   = colors[i % colors.length];

        // Label
        ctx.fillStyle = '#64748b';
        ctx.textAlign = 'right';
        ctx.fillText(label.length > 14 ? label.slice(0, 13) + '…' : label, labelW, y + barH / 2 + 5);

        // Bar background
        ctx.fillStyle = '#f1f5f9';
        ctx.beginPath();
        ctx.roundRect(labelW + padX, y, barMaxW, barH, 6);
        ctx.fill();

        // Bar fill
        ctx.fillStyle = color;
        ctx.beginPath();
        ctx.roundRect(labelW + padX, y, Math.max(barW, 6), barH, 6);
        ctx.fill();

        // Value label
        ctx.fillStyle = '#1e293b';
        ctx.textAlign = 'left';
        ctx.fillText(values[i], labelW + padX + Math.max(barW, 6) + 8, y + barH / 2 + 5);
    });
})();
</script>
