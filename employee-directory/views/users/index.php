<div class="page-toolbar">
    <a href="<?= BASE_URL ?>/users/create" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New User
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><?= count($users) ?> System User<?= count($users) !== 1 ? 's' : '' ?></h2>
    </div>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr class="<?= !$user['is_active'] ? 'row-muted' : '' ?>">
                    <td>
                        <div class="table-emp">
                            <div class="emp-avatar emp-avatar-sm">
                                <?= strtoupper(substr($user['username'], 0, 2)) ?>
                            </div>
                            <span><?= htmlspecialchars($user['email']) ?></span>
                        </div>
                    </td>
                    <td class="mono"><?= htmlspecialchars($user['username']) ?></td>
                    <td><span class="badge badge-role-<?= $user['role_name'] ?>"><?= str_replace('_', ' ', $user['role_name']) ?></span></td>
                    <td>
                        <span class="badge <?= $user['is_active'] ? 'badge-active' : 'badge-inactive' ?>">
                            <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                    <td class="text-right">
                        <div class="action-btns">
                            <a href="<?= BASE_URL ?>/users/<?= $user['id'] ?>/edit" class="btn btn-ghost btn-xs">Edit</a>
                            <?php if ((int)$user['id'] !== Auth::id()): ?>
                            <form method="POST" action="<?= BASE_URL ?>/users/<?= $user['id'] ?>/delete"
                                  onsubmit="return confirm('Delete user <?= addslashes($user['username']) ?>?')">
                                <?= CSRF::field() ?>
                                <button type="submit" class="btn btn-ghost btn-xs btn-danger-ghost">Delete</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
