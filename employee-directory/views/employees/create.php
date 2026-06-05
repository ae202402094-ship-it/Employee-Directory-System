<!-- views/employees/create.php -->

<div class="page-toolbar">
    <a href="<?= BASE_URL ?>/employees" class="btn btn-ghost btn-sm">← Back</a>
</div>

<div class="form-layout">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">New Employee</h2>
        </div>
        <div class="card-body">
            <?php include __DIR__ . '/_form.php'; ?>
        </div>
    </div>
</div>
