<!-- views/employees/edit.php -->

<div class="page-toolbar">
    <a href="<?= BASE_URL ?>/employees/<?= $employee['id'] ?>" class="btn btn-ghost btn-sm">← Back to Profile</a>
</div>

<div class="form-layout">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Edit: <?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></h2>
        </div>
        <div class="card-body">
            <?php
            // Normalize $old from employee data for the shared form
            $old = $old ?? $employee;
            $nextNumber = $employee['employee_number'];
            $isEdit = true;
            include __DIR__ . '/_form.php';
            ?>
        </div>
    </div>
</div>
