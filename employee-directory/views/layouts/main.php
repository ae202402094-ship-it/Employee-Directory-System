<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'App') ?> — <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
</head>
<body class="app-body">

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">ED</div>
        <div class="brand-text">
            <span class="brand-name">EmpDir</span>
            <span class="brand-sub">Directory System</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="<?= BASE_URL ?>/dashboard"
           class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/dashboard') ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>

        <a href="<?= BASE_URL ?>/employees"
           class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/employees') ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Employees
        </a>

        <a href="<?= BASE_URL ?>/departments"
           class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/departments') ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Departments
        </a>

        <?php if (Auth::isAdmin()): ?>
        <a href="<?= BASE_URL ?>/users"
           class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/users') ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Users
        </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar"><?= strtoupper(substr($_SESSION['user_username'] ?? 'U', 0, 2)) ?></div>
            <div class="user-info">
                <span class="user-name"><?= htmlspecialchars($_SESSION['user_username'] ?? '') ?></span>
                <span class="user-role"><?= htmlspecialchars(str_replace('_', ' ', $_SESSION['user_role'] ?? '')) ?></span>
            </div>
        </div>
        <a href="<?= BASE_URL ?>/logout" class="logout-btn" title="Logout">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        </a>
    </div>
</aside>

<!-- Main Content -->
<div class="main-wrapper">
    <!-- Top Bar -->
    <header class="topbar">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle menu">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <h1 class="page-title"><?= htmlspecialchars($title ?? '') ?></h1>
        <div class="topbar-right">
            <span class="topbar-date"><?= date('F j, Y') ?></span>
        </div>
    </header>

    <!-- Flash Message -->
    <?php if (!empty($_SESSION['flash'])): ?>
        <?php $flash = $_SESSION['flash']; unset($_SESSION['flash']); ?>
        <div class="flash flash-<?= $flash['type'] ?>" id="flashMsg">
            <?= htmlspecialchars($flash['message']) ?>
            <button onclick="this.parentElement.remove()" class="flash-close">×</button>
        </div>
    <?php endif; ?>

    <!-- Page Content -->
    <main class="main-content">
        <?= $content ?>
    </main>
</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>


<div class="cmd-palette-overlay" id="cmdPalette" style="display: none;">
    <div class="cmd-palette-box">
        <div class="cmd-palette-header">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="cmdInput" placeholder="Search employees by name, number, or position..." autocomplete="off">
            <span class="cmd-hint">ESC to close</span>
        </div>
        <div class="cmd-palette-results" id="cmdResults">
            <div class="cmd-empty">Type to start searching...</div>
        </div>
    </div>
</div>

<script>const BASE_URL = '<?= BASE_URL ?>';</script>

<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>
