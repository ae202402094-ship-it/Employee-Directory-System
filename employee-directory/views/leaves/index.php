<?php require_once BASE_PATH . '/helpers/CSRF.php'; ?>
<div class="container-fluid" style="padding: 24px;">
    <div class="row" style="display: flex; gap: 24px; flex-wrap: wrap;">
        
        <!-- Left: File a Leave Request (For all employees) -->
        <div style="flex: 1; min-width: 320px;">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">File a Leave Request</h2>
                </div>
                <div class="card-body" style="padding: 24px;">
                    <form action="<?= BASE_URL ?>/leaves" method="POST">
                        <?= CSRF::field() ?>
                        <div class="form-group" style="margin-bottom: 16px;">
                            <label class="form-label">Leave Type</label>
                            <select name="leave_type" class="form-control form-select" required>
                                <option value="sick">Sick Leave</option>
                                <option value="vacation">Vacation Leave</option>
                                <option value="casual">Casual Leave</option>
                                <option value="emergency">Emergency Leave</option>
                                <option value="maternity">Maternity Leave</option>
                                <option value="paternity">Paternity Leave</option>
                                <option value="unpaid">Unpaid Leave</option>
                                <option value="absent">Absent / Excuse Notice</option>
                            </select>
                        </div>
                        
                        <div class="form-row" style="display: flex; gap: 16px; margin-bottom: 16px;">
                            <div style="flex: 1;">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                            </div>
                            <div style="flex: 1;">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label class="form-label">Reason / Remarks</label>
                            <textarea name="reason" rows="4" class="form-control" placeholder="Provide details regarding your leave request..." required style="resize: vertical;"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Request</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right: Approval Workflow Panel (Admins/HR/Supervisors only) -->
        <?php if (Auth::isAdmin() || Auth::isHR() || Auth::isDeptHead()): ?>
        <div style="flex: 2; min-width: 500px;">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Pending Leave Approvals (<?= count($pendingRequests) ?>)</h2>
                </div>
                <div class="card-body" style="padding: 0;">
                    <?php if (empty($pendingRequests)): ?>
                        <div style="padding: 40px; text-align: center; color: var(--text-muted); font-size: 14px;">
                            No pending leave requests to review.
                        </div>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Leave Info</th>
                                        <th>Reason</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingRequests as $req): ?>
                                    <tr>
                                        <td>
                                            <div class="table-emp">
                                                <?php if (!empty($req['profile_picture'])): ?>
                                                    <img src="<?= BASE_URL ?>/assets/images/profiles/<?= htmlspecialchars($req['profile_picture']) ?>" class="emp-avatar emp-avatar-sm" style="object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="emp-avatar emp-avatar-sm">
                                                        <?= strtoupper(substr($req['first_name'], 0, 1) . substr($req['last_name'], 0, 1)) ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <span class="emp-fullname"><?= htmlspecialchars($req['first_name'] . ' ' . $req['last_name']) ?></span>
                                                    <span class="emp-email"><?= htmlspecialchars($req['department_name'] ?: 'No Dept') ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-warning" style="margin-bottom: 4px; display: inline-block; font-size: 11px;">
                                                <?= ucfirst($req['leave_type']) ?>
                                            </span>
                                            <div style="font-size: 12px; color: var(--text-secondary);">
                                                <?= date('M d', strtotime($req['start_date'])) ?> - <?= date('M d, Y', strtotime($req['end_date'])) ?>
                                            </div>
                                        </td>
                                        <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($req['reason']) ?>">
                                            <?= htmlspecialchars($req['reason']) ?>
                                        </td>
                                        <td class="text-right">
                                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                                <form action="<?= BASE_URL ?>/leaves/<?= $req['id'] ?>/approve" method="POST" style="margin:0;">
                                                    <?= CSRF::field() ?>
                                                    <button type="submit" class="btn btn-success btn-xs">Approve</button>
                                                </form>
                                                <form action="<?= BASE_URL ?>/leaves/<?= $req['id'] ?>/reject" method="POST" style="margin:0;">
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
            <h2 class="card-title">Leave History & Audit Tracker</h2>
        </div>
        <div class="card-body" style="padding: 0;">
            <?php if (empty($requestsHistory)): ?>
                <div style="padding: 40px; text-align: center; color: var(--text-muted); font-size: 14px;">
                    No leave requests on record.
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Leave Type</th>
                                <th>Duration</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th class="text-right">Processed By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requestsHistory as $hist): ?>
                            <tr>
                                <td>
                                    <span class="emp-fullname" style="font-weight: 600;"><?= htmlspecialchars($hist['first_name'] . ' ' . $hist['last_name']) ?></span>
                                    <div style="font-size: 11px; color: var(--text-muted);"><?= htmlspecialchars($hist['department_name'] ?? '') ?></div>
                                </td>
                                <td>
                                    <span style="font-size:12.5px; font-weight: 500;"><?= ucfirst($hist['leave_type']) ?></span>
                                </td>
                                <td>
                                    <div style="font-size: 13px; font-weight:600; color: var(--text-primary);">
                                        <?= date('M d, Y', strtotime($hist['start_date'])) ?>
                                    </div>
                                    <div style="font-size: 11.5px; color: var(--text-muted);">
                                        to <?= date('M d, Y', strtotime($hist['end_date'])) ?>
                                    </div>
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
