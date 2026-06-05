<!-- views/users/edit.php -->
<div class="page-toolbar">
    <a href="<?= BASE_URL ?>/users" class="btn btn-ghost btn-sm">← Back to Users</a>
</div>

<div class="form-layout">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Edit User: <?= htmlspecialchars($user['username']) ?></h2>
        </div>
        <div class="card-body">
            <form action="<?= BASE_URL ?>/users/<?= $user['id'] ?>/update" method="POST" novalidate>
                <?= CSRF::field() ?>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Username <span class="required">*</span></label>
                        <input type="text" name="username" class="form-control"
                               value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Role <span class="required">*</span></label>
                        <select name="role_id" class="form-control form-select" required>
                            <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id'] ?>"
                                <?= (int)$user['role_id'] === (int)$role['id'] ? 'selected' : '' ?>>
                                <?= ucfirst(str_replace('_', ' ', $role['name'])) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">New Password <span class="text-muted">(leave blank to keep current)</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Min. 8 characters">
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <div class="toggle-group">
                        <label class="toggle">
                            <input type="checkbox" name="is_active" value="1"
                                <?= $user['is_active'] ? 'checked' : '' ?>
                                <?= (int)$user['id'] === Auth::id() ? 'disabled' : '' ?>>
                            <span class="toggle-slider"></span>
                        </label>
                        <span class="toggle-label">Active Account</span>
                    </div>
                    <?php if ((int)$user['id'] === Auth::id()): ?>
                        <p class="form-hint">You cannot deactivate your own account.</p>
                    <?php endif; ?>
                </div>

                <div class="form-actions">
                    <a href="<?= BASE_URL ?>/users" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
