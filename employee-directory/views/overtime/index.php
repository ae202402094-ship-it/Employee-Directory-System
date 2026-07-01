<?php require_once BASE_PATH . '/helpers/CSRF.php'; ?>
<div class="container-fluid" style="padding: 24px;">
    <div class="row" style="display: flex; gap: 24px; flex-wrap: wrap;">
        
        <!-- Left: Log Overtime (For all employees) -->
        <div style="flex: 1; min-width: 320px;">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Log Overtime (OT) Hours</h2>
                </div>
                <div class="card-body" style="padding: 24px;">
                    <form action="<?= BASE_URL ?>/overtime" method="POST">
                        <?= CSRF::field() ?>
                        <div class="form-group" style="margin-bottom: 16px;">
                            <label class="form-label">Overtime Date</label>
                            <input type="date" name="ot_date" class="form-control" required max="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="form-group" style="margin-bottom: 16px;">
                            <label class="form-label">OT Duration (Hours)</label>
                            <input type="number" name="hours" min="0.5" max="12.0" step="0.5" class="form-control" placeholder="e.g. 2.5" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label class="form-label">Tasks Done / Reason</label>
                            <textarea name="reason" rows="4" class="form-control" placeholder="Specify project work or operational reason..." required style="resize: vertical;"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">Log Overtime</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right: OT Approval Workflow Panel (Admins/HR/Supervisors only) -->
        <?php 
        $isHRDept = isset($employee) && (int)($employee['department_id'] ?? 0) === 2;
        if (Auth::isAdmin() || Auth::isHR() || Auth::isDeptHead() || $isHRDept): 
        ?>
        <div style="flex: 2; min-width: 500px;">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Pending OT Approvals (<?= count($pendingLogs) ?>)</h2>
                </div>
                <div class="card-body" style="padding: 0;">
                    <?php if (empty($pendingLogs)): ?>
                        <div style="padding: 40px; text-align: center; color: var(--text-muted); font-size: 14px;">
                            No pending overtime logs to review.
                        </div>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>OT Info</th>
                                        <th>Reason</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingLogs as $log): ?>
                                    <tr>
                                        <td>
                                            <div class="table-emp">
                                                <?php if (!empty($log['profile_picture'])): ?>
                                                    <img src="<?= BASE_URL ?>/assets/images/profiles/<?= htmlspecialchars($log['profile_picture']) ?>" class="emp-avatar emp-avatar-sm" style="object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="emp-avatar emp-avatar-sm">
                                                        <?= strtoupper(substr($log['first_name'], 0, 1) . substr($log['last_name'], 0, 1)) ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <span class="emp-fullname"><?= htmlspecialchars($log['first_name'] . ' ' . $log['last_name']) ?></span>
                                                    <span class="emp-email"><?= htmlspecialchars($log['department_name'] ?: 'No Dept') ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-success" style="margin-bottom: 4px; display: inline-block; font-size: 11px;">
                                                <?= number_format($log['hours'], 1) ?> hrs
                                            </span>
                                            <div style="font-size: 12px; color: var(--text-secondary);">
                                                <?= date('M d, Y', strtotime($log['ot_date'])) ?>
                                            </div>
                                        </td>
                                        <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($log['reason']) ?>">
                                            <?= htmlspecialchars($log['reason']) ?>
                                        </td>
                                        <td class="text-right">
                                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                                <form action="<?= BASE_URL ?>/overtime/<?= $log['id'] ?>/approve" method="POST" style="margin:0;">
                                                    <?= CSRF::field() ?>
                                                    <button type="submit" class="btn btn-success btn-xs">Approve</button>
                                                </form>
                                                <form action="<?= BASE_URL ?>/overtime/<?= $log['id'] ?>/reject" method="POST" style="margin:0;">
                                                    <?= CSRF::field() ?>
                                                    <button type="submit" class="btn btn-danger btn-xs">Reject</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- History Panel -->
    <div class="card" style="margin-top: 24px;">
        <div class="card-header">
            <h2 class="card-title">Overtime Audit Registry</h2>
        </div>
        <div class="card-body" style="padding: 0;">
            <?php if (empty($historyLogs)): ?>
                <div style="padding: 40px; text-align: center; color: var(--text-muted); font-size: 14px;">
                    No overtime logs on record.
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Overtime Date</th>
                                <th>Duration</th>
                                <th>Reason / Tasks</th>
                                <th>Status</th>
                                <th class="text-right">Approved By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historyLogs as $hist): ?>
                            <tr>
                                <td>
                                    <span class="emp-fullname" style="font-weight: 600;"><?= htmlspecialchars($hist['first_name'] . ' ' . $hist['last_name']) ?></span>
                                    <div style="font-size: 11px; color: var(--text-muted);"><?= htmlspecialchars($hist['department_name'] ?? '') ?></div>
                                </td>
                                <td>
                                    <div style="font-size: 13.5px; font-weight: 600; color: var(--text-primary);">
                                        <?= date('M d, Y', strtotime($hist['ot_date'])) ?>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size:13px; font-weight: 700; color: var(--green);"><?= number_format($hist['hours'], 1) ?> Hours</span>
                                </td>
                                <td style="max-width: 250px; font-size: 13px; color: var(--text-secondary);">
                                    <?= htmlspecialchars($hist['reason']) ?>
                                </td>
                                <td>
                                    <?php if ($hist['status'] === 'approved'): ?>
                                        <span class="badge badge-success">Approved</span>
                                    <?php elseif ($hist['status'] === 'rejected'): ?>
                                        <span class="badge badge-danger">Rejected</span>
                                    <?php else: ?>
                                        <span class="badge badge-pending">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right" style="font-size: 13px; color: var(--text-secondary);">
                                    <?= $hist['approver_name'] ? htmlspecialchars($hist['approver_name']) : '—' ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
