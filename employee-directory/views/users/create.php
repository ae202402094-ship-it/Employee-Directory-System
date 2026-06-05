<!-- views/users/create.php -->
<div class="page-toolbar">
    <a href="<?= BASE_URL ?>/users" class="btn btn-ghost btn-sm">← Back</a>
</div>

<div class="form-layout">
    <div class="card">
        <div class="card-header"><h2 class="card-title">Create System User</h2></div>
        <div class="card-body">
            <form action="<?= BASE_URL ?>/users/store" method="POST" novalidate>
                <?= CSRF::field() ?>

                <div class="form-row">
                    <div class="form-group <?= !empty($errors['username']) ? 'has-error' : '' ?>">
                        <label class="form-label">Username <span class="required">*</span></label>
                        <input type="text" name="username" class="form-control"
                               value="<?= htmlspecialchars($old['username'] ?? '') ?>" required>
                        <?php if (!empty($errors['username'])): ?><span class="form-error"><?= htmlspecialchars($errors['username'][0]) ?></span><?php endif; ?>
                    </div>
                    <div class="form-group <?= !empty($errors['role_id']) ? 'has-error' : '' ?>">
                        <label class="form-label">Role <span class="required">*</span></label>
                        <select name="role_id" class="form-control form-select" required>
                            <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id'] ?>" <?= (int)($old['role_id'] ?? 0) === (int)$role['id'] ? 'selected' : '' ?>>
                                <?= ucfirst(str_replace('_', ' ', $role['name'])) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group <?= !empty($errors['email']) ? 'has-error' : '' ?>">
                    <label class="form-label">Email <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                    <?php if (!empty($errors['email'])): ?><span class="form-error"><?= htmlspecialchars($errors['email'][0]) ?></span><?php endif; ?>
                </div>

                <div class="form-group <?= !empty($errors['password']) ? 'has-error' : '' ?>">
                    <label class="form-label">Password <span class="required">*</span></label>
                    <input type="password" name="password" class="form-control"
                           placeholder="Min. 8 characters" required>
                    <?php if (!empty($errors['password'])): ?><span class="form-error"><?= htmlspecialchars($errors['password'][0]) ?></span><?php endif; ?>
                </div>

                <div class="form-actions">
                    <a href="<?= BASE_URL ?>/users" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>
