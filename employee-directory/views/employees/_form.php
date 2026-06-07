<?php
// views/employees/_form.php
// Shared between create and edit views
// $isEdit = true when editing, false/unset when creating
// $old = old field values (for repopulation on validation error)
// $errors = validation errors array
// $employee = employee record (edit mode)
// $nextNumber = suggested employee number

$isEdit  = $isEdit ?? false;
$old     = $old ?? [];
$errors  = $errors ?? [];

$action  = $isEdit
    ? BASE_URL . '/employees/' . ($employee['id']) . '/update'
    : BASE_URL . '/employees/store';

$val = fn(string $key, string $default = '') => htmlspecialchars($old[$key] ?? $default);
$err = fn(string $key) => !empty($errors[$key]) ? '<span class="form-error">' . htmlspecialchars($errors[$key][0]) . '</span>' : '';
$grp = fn(string $key) => !empty($errors[$key]) ? 'form-group has-error' : 'form-group';
?>

<form action="<?= $action ?>" method="POST" novalidate>
    <?= CSRF::field() ?>

    <div class="form-section-title">Basic Information</div>
    <div class="form-row">
        <div class="<?= $grp('first_name') ?>">
            <label class="form-label">First Name <span class="required">*</span></label>
            <input type="text" name="first_name" class="form-control" value="<?= $val('first_name') ?>" placeholder="Juan" required>
            <?= $err('first_name') ?>
        </div>
        <div class="<?= $grp('last_name') ?>">
            <label class="form-label">Last Name <span class="required">*</span></label>
            <input type="text" name="last_name" class="form-control" value="<?= $val('last_name') ?>" placeholder="Dela Cruz" required>
            <?= $err('last_name') ?>
        </div>
    </div>

    <div class="form-row">
        <div class="<?= $grp('employee_number') ?>">
            <label class="form-label">Employee Number <span class="required">*</span></label>
            <input type="text" name="employee_number" class="form-control mono"
                   value="<?= $val('employee_number', $nextNumber ?? '') ?>"
                   <?= $isEdit ? 'readonly' : '' ?> required>
            <?= $err('employee_number') ?>
        </div>
        <div class="<?= $grp('status') ?>">
            <label class="form-label">Status</label>
            <select name="status" class="form-control form-select">
                <?php foreach (['active', 'inactive', 'terminated'] as $s): ?>
                <option value="<?= $s ?>" <?= ($old['status'] ?? 'active') === $s ? 'selected' : '' ?>>
                    <?= ucfirst($s) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?= $err('status') ?>
        </div>
    </div>

    <div class="form-section-title">Position & Department</div>
    <div class="form-row">
        <div class="<?= $grp('position') ?>">
            <label class="form-label">Position / Job Title</label>
            <input type="text" name="position" class="form-control" value="<?= $val('position') ?>" placeholder="Software Engineer">
            <?= $err('position') ?>
        </div>
        <div class="<?= $grp('department_id') ?>">
            <label class="form-label">Department</label>
            <select name="department_id" class="form-control form-select">
                <option value="">— None —</option>
                <?php foreach ($departments as $dept): ?>
                <option value="<?= $dept['id'] ?>"
                    <?= (int)($old['department_id'] ?? 0) === (int)$dept['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($dept['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?= $err('department_id') ?>
        </div>
    </div>

    <div class="form-row">
        <div class="<?= $grp('hire_date') ?>">
            <label class="form-label">Hire Date</label>
            <input type="date" name="hire_date" class="form-control"
                   value="<?= htmlspecialchars(substr($old['hire_date'] ?? '', 0, 10)) ?>">
            <?= $err('hire_date') ?>
        </div>
    </div>

    <div class="form-section-title">Contact Information</div>
    <div class="form-row">
        <div class="<?= $grp('email') ?>">
            <label class="form-label">Email Address <span class="required">*</span></label>
            <input type="email" name="email" class="form-control" value="<?= $val('email') ?>" placeholder="juan@company.com" required>
            <?= $err('email') ?>
        </div>
        <div class="<?= $grp('phone') ?>">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control" value="<?= $val('phone') ?>" placeholder="09XXXXXXXXX">
            <?= $err('phone') ?>
        </div>
    </div>

    <div class="<?= $grp('address') ?>">
        <label class="form-label">Address</label>
        <textarea name="address" class="form-control" rows="3" placeholder="Street, City, Province"><?= $val('address') ?></textarea>
        <?= $err('address') ?>
    </div>

    <div class="form-actions">
        <a href="<?= BASE_URL ?>/employees<?= $isEdit ? '/' . $employee['id'] : '' ?>" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <?= $isEdit ? 'Save Changes' : 'Add Employee' ?>
        </button>
    </div>
</form>