<div class="page-toolbar" style="margin-bottom: 24px;">
    <a href="<?= BASE_URL ?>/employees" class="btn btn-secondary btn-sm">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Back to Directory
    </a>
</div>

<div class="form-layout">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Register New Employee</h2>
        </div>
        <div class="card-body">
            <?php include __DIR__ . '/_form.php'; ?>
        </div>
    </div>
</div>