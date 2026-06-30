<?php
// views/employees/_form.php
$isEdit  = $isEdit ?? false;
$old     = $old ?? [];
$errors  = $errors ?? [];

$action  = $isEdit
    ? BASE_URL . '/employees/' . ($employee['id']) . '/update'
    : BASE_URL . '/employees/store';

$val = fn(string $key, string $default = '') => htmlspecialchars((string)($old[$key] ?? $default));
$err = fn(string $key) => !empty($errors[$key]) ? '<span class="form-error">' . htmlspecialchars($errors[$key][0]) . '</span>' : '';
$grp = fn(string $key) => !empty($errors[$key]) ? 'form-group has-error' : 'form-group';
?>

<form action="<?= $action ?>" method="POST" enctype="multipart/form-data" novalidate>
    <?= CSRF::field() ?>
    <input type="hidden" name="ip_address" value="<?= $_SERVER['REMOTE_ADDR'] ?>">

    <div class="form-section-title">Personal Profile & Media</div>
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Profile Picture (JPG/PNG, Max 2MB)</label>
            <?php if($isEdit && !empty($employee['profile_picture'])): ?>
                <div style="margin-bottom: 10px;">
                    <img src="<?= BASE_URL ?>/assets/images/profiles/<?= htmlspecialchars($employee['profile_picture']) ?>" 
                         style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border);">
                </div>
            <?php endif; ?>
            <input type="file" name="profile_picture" class="form-control" accept="image/jpeg, image/png, image/webp">
        </div>
        <div class="<?= $grp('quote') ?>">
            <label class="form-label">Favorite Quote / Motto</label>
            <input type="text" name="quote" class="form-control" value="<?= $val('quote') ?>" placeholder="e.g. Keep moving forward.">
            <?= $err('quote') ?>
        </div>
    </div>
    
    <div class="<?= $grp('bio') ?>">
        <label class="form-label">Professional Bio</label>
        <textarea name="bio" class="form-control" rows="4"><?= $val('bio') ?></textarea>
        <?= $err('bio') ?>
    </div>

    <div class="form-section-title">Basic Information</div>
    <div class="form-row">
        <div class="<?= $grp('first_name') ?>">
            <label class="form-label">First Name <span class="required">*</span></label>
            <input type="text" name="first_name" class="form-control" value="<?= $val('first_name') ?>" required>
            <?= $err('first_name') ?>
        </div>
        <div class="<?= $grp('last_name') ?>">
            <label class="form-label">Last Name <span class="required">*</span></label>
            <input type="text" name="last_name" class="form-control" value="<?= $val('last_name') ?>" required>
            <?= $err('last_name') ?>
        </div>
    </div>

    <div class="form-row">
        <div class="<?= $grp('employee_number') ?>">
            <label class="form-label">Employee Number <span class="required">*</span></label>
            <input type="text" name="employee_number" class="form-control mono" value="<?= $val('employee_number', $nextNumber ?? '') ?>" <?= $isEdit ? 'readonly' : '' ?> required>
            <?= $err('employee_number') ?>
        </div>
        <div class="<?= $grp('status') ?>">
            <label class="form-label">Status</label>
            <select name="status" class="form-control form-select">
                <?php foreach (['active', 'inactive', 'terminated'] as $s): ?>
                <option value="<?= $s ?>" <?= ($old['status'] ?? 'active') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

   <form action="<?= $action ?>" method="POST" enctype="multipart/form-data" novalidate>
    <?= CSRF::field() ?>
    <input type="hidden" name="ip_address" value="<?= $_SERVER['REMOTE_ADDR'] ?>">

    <div class="form-section-title">Address Information</div>
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Region</label>
            <select name="region" id="region" class="form-control form-select"><option value="">Select Region</option></select>
        </div>
        <div class="form-group">
            <label class="form-label">Province</label>
            <select name="province" id="province" class="form-control form-select"><option value="">Select Province</option></select>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">City / Municipality</label>
            <select name="city" id="city" class="form-control form-select"><option value="">Select City/Mun</option></select>
        </div>
        <div class="form-group">
            <label class="form-label">Barangay</label>
            <select name="barangay" id="barangay" class="form-control form-select"><option value="">Select Barangay</option></select>
        </div>
    </div>

    <div class="form-actions">
        <a href="<?= BASE_URL ?>/employees" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Save Changes' : 'Add Employee' ?></button>
    </div>
</form>

<script src="<?= BASE_URL ?>/assets/js/ph-address-selector.js"></script>
</form>