<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">ED</div>
            <h1 class="auth-title">Employee Directory</h1>
            <p class="auth-sub">Sign in to your account</p>
        </div>

        <?php if (!empty($errors['auth'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($errors['auth'][0]) ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/login" method="POST" class="auth-form" novalidate>
            <?= CSRF::field() ?>

            <div class="form-group <?= !empty($errors['email']) ? 'has-error' : '' ?>">
                <label for="email" class="form-label">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                    placeholder="you@company.com"
                    autocomplete="email"
                    required
                >
                <?php if (!empty($errors['email'])): ?>
                    <span class="form-error"><?= htmlspecialchars($errors['email'][0]) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group <?= !empty($errors['password']) ? 'has-error' : '' ?>">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                    >
                    <button type="button" class="input-addon" id="togglePassword" aria-label="Show password">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                <?php if (!empty($errors['password'])): ?>
                    <span class="form-error"><?= htmlspecialchars($errors['password'][0]) ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-full">
                Sign In
            </button>
        </form>

        <p class="auth-hint">Default: admin@company.com / Admin@123</p>
    </div>
</div>
