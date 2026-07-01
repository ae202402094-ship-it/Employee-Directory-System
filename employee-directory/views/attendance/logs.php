<div class="container-fluid" style="padding: 24px;">
    
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div>
                <h2 class="card-title">Daily Attendance Logs Registry</h2>
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">Database ledger recording all QR code check-in and check-out events</div>
            </div>
            <!-- Actions -->
            <div>
                <a href="<?= BASE_URL ?>/attendance/scanner" class="btn btn-primary btn-sm">📷 Open Attendance Scanner</a>
            </div>
        </div>
        <div class="card-body" style="padding: 0;">
            
            <?php if (empty($logs)): ?>
                <div style="padding: 50px; text-align: center; color: var(--text-muted); font-size: 14px;">
                    No attendance records found in the database.
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Department & Position</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Punch Status</th>
                                <th class="text-right">Device/Browser Agent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
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
                                            <span class="emp-email"><?= htmlspecialchars($log['employee_number']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight: 500; display: block; font-size: 13.5px;"><?= htmlspecialchars($log['department_name'] ?: 'No Dept') ?></span>
                                    <span style="font-size: 12px; color: var(--text-muted);"><?= htmlspecialchars($log['position'] ?: '—') ?></span>
                                </td>
                                <td>
                                    <div style="font-size: 13.5px; font-weight: 600; color: var(--text-primary);">
                                        <?= date('h:i A', strtotime($log['check_in_time'])) ?>
                                    </div>
                                    <div style="font-size: 11px; color: var(--text-muted);">
                                        <?= date('M d, Y', strtotime($log['check_in_time'])) ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($log['check_out_time'] !== null): ?>
                                        <div style="font-size: 13.5px; font-weight: 600; color: var(--text-primary);">
                                            <?= date('h:i A', strtotime($log['check_out_time'])) ?>
                                        </div>
                                        <div style="font-size: 11px; color: var(--text-muted);">
                                            <?= date('M d, Y', strtotime($log['check_out_time'])) ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="font-size: 12.5px; color: var(--text-muted); font-style: italic;">On Duty</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($log['status'] === 'present'): ?>
                                        <span class="badge badge-success">Present</span>
                                    <?php elseif ($log['status'] === 'late'): ?>
                                        <span class="badge badge-danger">Late Clock-in</span>
                                    <?php else: ?>
                                        <span class="badge badge-pending">Half Day</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right" style="max-width: 200px; font-size: 11px; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($log['device_info'] ?? '—') ?>">
                                    <?= htmlspecialchars($log['device_info'] ?? '—') ?>
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
