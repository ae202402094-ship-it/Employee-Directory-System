<div class="container-fluid" style="padding: 24px;">
    
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">System Audit Log Trail</h2>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">Comprehensive security ledger tracking all administrative, HR, check-in, and settings changes</div>
        </div>
        <div class="card-body" style="padding: 0;">
            
            <?php if (empty($logs)): ?>
                <div style="padding: 50px; text-align: center; color: var(--text-muted); font-size: 14px;">
                    No audit records logged yet in the system.
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>System Operator</th>
                                <th>Action Type</th>
                                <th>Audit Log Description</th>
                                <th class="text-right">IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                            <tr>
                                <td>
                                    <div style="font-size: 13.5px; font-weight: 600; color: var(--text-primary);">
                                        <?= date('h:i A', strtotime($log['created_at'])) ?>
                                    </div>
                                    <div style="font-size: 11px; color: var(--text-muted);">
                                        <?= date('M d, Y', strtotime($log['created_at'])) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="emp-fullname" style="font-weight: 600;">
                                        <?= $log['first_name'] ? htmlspecialchars($log['first_name'] . ' ' . $log['last_name']) : htmlspecialchars($log['username']) ?>
                                    </span>
                                    <span class="badge badge-secondary" style="font-size: 9.5px; margin-left: 6px; padding: 2px 6px;">
                                        <?= htmlspecialchars(strtoupper($log['role_name'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $actionClass = 'badge-secondary';
                                    if (strpos($log['action_type'], 'APPROVE') !== false || strpos($log['action_type'], 'CHECK_IN') !== false) {
                                        $actionClass = 'badge-success';
                                    } elseif (strpos($log['action_type'], 'REJECT') !== false || strpos($log['action_type'], 'DELETE') !== false) {
                                        $actionClass = 'badge-danger';
                                    } elseif (strpos($log['action_type'], 'CREATE') !== false || strpos($log['action_type'], 'UPDATE') !== false) {
                                        $actionClass = 'badge-primary';
                                    }
                                    ?>
                                    <span class="badge <?= $actionClass ?>" style="font-size: 11px; letter-spacing: 0.5px;">
                                        <?= htmlspecialchars($log['action_type']) ?>
                                    </span>
                                </td>
                                <td style="font-size: 13px; color: var(--text-secondary); max-width: 350px;">
                                    <?= htmlspecialchars($log['details']) ?>
                                </td>
                                <td class="text-right mono" style="font-size: 12px; color: var(--text-muted);">
                                    <?= htmlspecialchars($log['ip_address'] ?? '—') ?>
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
