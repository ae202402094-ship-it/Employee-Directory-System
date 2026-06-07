<div class="page-toolbar">
    <a href="<?= BASE_URL ?>/employees" class="btn btn-ghost btn-sm">← Back to Employees</a>
    <div class="toolbar-actions">
        <?php if (Auth::isHR()): ?>
        <a href="<?= BASE_URL ?>/employees/<?= $employee['id'] ?>/edit" class="btn btn-primary btn-sm">Edit Profile</a>
        <?php endif; ?>
    </div>
</div>

<div class="profile-layout">
    <!-- Left: Identity Card -->
    <div class="card profile-card">
        <div class="profile-hero">
            <div class="profile-avatar">
                <?= strtoupper(substr($employee['first_name'], 0, 1) . substr($employee['last_name'], 0, 1)) ?>
            </div>
            <h2 class="profile-name">
                <?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?>
            </h2>
            <p class="profile-position"><?= htmlspecialchars($employee['position'] ?? '—') ?></p>
            <span class="badge badge-<?= $employee['status'] ?> badge-lg"><?= $employee['status'] ?></span>
        </div>

        <div class="profile-meta">
            <div class="meta-row">
                <span class="meta-label">Employee #</span>
                <span class="meta-value mono"><?= htmlspecialchars($employee['employee_number']) ?></span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Department</span>
                <span class="meta-value"><?= htmlspecialchars($employee['department_name'] ?? '—') ?></span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Hire Date</span>
                <span class="meta-value">
                    <?= $employee['hire_date'] ? date('F j, Y', strtotime($employee['hire_date'])) : '—' ?>
                </span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Added</span>
                <span class="meta-value"><?= date('M j, Y', strtotime($employee['created_at'])) ?></span>
            </div>
        </div>
    </div>

    <!-- Right: Contact Info -->
    <div class="profile-details">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Contact Information</h3></div>
            <div class="card-body">
                <div class="detail-grid">
                    <div class="detail-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <div>
                            <span class="detail-label">Email</span>
                            <a href="mailto:<?= htmlspecialchars($employee['email']) ?>" class="detail-value link">
                                <?= htmlspecialchars($employee['email']) ?>
                            </a>
                        </div>
                    </div>

                    <div class="detail-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.72 12 19.79 19.79 0 0 1 1.62 3.54 2 2 0 0 1 3.59 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.6a16 16 0 0 0 5.92 5.92l.87-.87a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <div>
                            <span class="detail-label">Phone</span>
                            <span class="detail-value"><?= htmlspecialchars($employee['phone'] ?? '—') ?></span>
                        </div>
                    </div>

                    <div class="detail-item detail-item-full">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <div>
                            <span class="detail-label">Address</span>
                            <span class="detail-value"><?= htmlspecialchars($employee['address'] ?? '—') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (Auth::isAdmin()): ?>
        <div class="card card-danger-zone">
            <div class="card-header"><h3 class="card-title">Danger Zone</h3></div>
            <div class="card-body">
                <p class="text-muted">Permanently delete this employee record. This action cannot be undone.</p>
                <form method="POST" action="<?= BASE_URL ?>/employees/<?= $employee['id'] ?>/delete"
                      onsubmit="return confirm('Permanently delete <?= addslashes($employee['first_name']) ?>? This cannot be undone.')">
                    <?= CSRF::field() ?>
                    <button type="submit" class="btn btn-danger btn-sm">Delete Employee</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

