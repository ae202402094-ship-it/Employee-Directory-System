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

<?php if (!empty($errors)): ?>
<div style="margin-bottom: 24px; padding: 16px; border-radius: var(--r-md); background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444; font-size: 14px;">
    <strong style="display: block; margin-bottom: 8px;">Please correct the errors below:</strong>
    <ul style="margin: 0; padding-left: 20px; line-height: 1.6;">
        <?php foreach ($errors as $field => $errs): ?>
            <?php foreach ($errs as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

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

    <div class="form-section-title">Employment & Contact Details</div>
    <div class="form-row">
        <div class="<?= $grp('email') ?>">
            <label class="form-label">Email Address <span class="required">*</span></label>
            <input type="email" name="email" class="form-control" value="<?= $val('email', $employee['email'] ?? '') ?>" required>
            <?= $err('email') ?>
        </div>
        <div class="<?= $grp('phone') ?>">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control" value="<?= $val('phone', $employee['phone'] ?? '') ?>">
            <?= $err('phone') ?>
        </div>
    </div>
    <div class="form-row">
        <div class="<?= $grp('position') ?>">
            <label class="form-label">Position</label>
            <input type="text" name="position" class="form-control" value="<?= $val('position', $employee['position'] ?? '') ?>" placeholder="e.g. Software Engineer">
            <?= $err('position') ?>
        </div>
        <div class="form-group">
            <label class="form-label">Department</label>
            <select name="department_id" class="form-control form-select">
                <option value="">Select Department</option>
                <?php foreach ($departments as $dept): ?>
                <option value="<?= $dept['id'] ?>" <?= ($old['department_id'] ?? $employee['department_id'] ?? '') == $dept['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($dept['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-row">
        <div class="<?= $grp('hire_date') ?>">
            <label class="form-label">Hire Date</label>
            <input type="date" name="hire_date" class="form-control" value="<?= $val('hire_date', $employee['hire_date'] ?? '') ?>">
            <?= $err('hire_date') ?>
        </div>
        <div class="form-group">
            <label class="form-label">Availability Status</label>
            <select name="availability_status" class="form-control form-select">
                <?php 
                $statuses = [
                    'available'   => 'Working / Available',
                    'late'        => 'Late',
                    'absent'      => 'Absent',
                    'half-day'    => 'Take a Half Day',
                    'out-of-town' => 'Out of Town (Business Reason)',
                    'on-break'    => 'On Break',
                    'day-off'     => 'Day Off',
                    'on-leave'    => 'On Leave',
                    'vacation'    => 'Vacation',
                    'holiday'     => 'Holiday',
                    'overtime'    => 'Overtime'
                ];
                foreach ($statuses as $valKey => $valLabel): 
                ?>
                <option value="<?= $valKey ?>" <?= ($old['availability_status'] ?? $employee['availability_status'] ?? 'available') === $valKey ? 'selected' : '' ?>>
                    <?= $valLabel ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-section-title">Address Information</div>
    
    <?php if ($isEdit && (!empty($employee['barangay']) || !empty($employee['city']))): ?>
    <div style="grid-column: 1 / -1; margin-bottom: 16px; padding: 12px 16px; border-radius: var(--r-md); background: rgba(37, 99, 235, 0.05); border: 1px solid rgba(37, 99, 235, 0.1); font-size: 13.5px; color: var(--text-secondary);">
        <strong>Currently Saved Address:</strong> 
        <?= htmlspecialchars(implode(', ', array_filter([$employee['address'] ?? null, $employee['barangay'] ?? null, $employee['city'] ?? null]))) ?>
        <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Only select from the dropdowns below if you want to change this address.</div>
    </div>
    <?php endif; ?>

    <!-- Hidden inputs for text names populated by JS -->
    <input type="hidden" name="region" id="region-text" value="">
    <input type="hidden" name="province" id="province-text" value="">
    <input type="hidden" name="city" id="city-text" value="">
    <input type="hidden" name="barangay" id="barangay-text" value="">

    <div class="form-row">
        <div class="form-group" style="width: 100%;">
            <label class="form-label">Street Address</label>
            <textarea name="address" class="form-control" rows="2" placeholder="e.g. House No., Street Name, Subdivision"><?= htmlspecialchars($old['address'] ?? $employee['address'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Region</label>
            <select name="region_code" id="region" class="form-control form-select"><option value="">Select Region</option></select>
        </div>
        <div class="form-group">
            <label class="form-label">Province</label>
            <select name="province_code" id="province" class="form-control form-select"><option value="">Select Province</option></select>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">City / Municipality</label>
            <select name="city_code" id="city" class="form-control form-select"><option value="">Select City/Mun</option></select>
        </div>
        <div class="form-group">
            <label class="form-label">Barangay</label>
            <select name="barangay_code" id="barangay" class="form-control form-select"><option value="">Select Barangay</option></select>
        </div>
    </div>

    <div class="form-section-title">Talent & Personal Profile</div>
    <div class="form-row">
        <div class="form-group" style="width: 100%;">
            <label class="form-label">Hobbies</label>
            <textarea name="hobbies" class="form-control" rows="2" placeholder="e.g. Reading, cycling, photography"><?= htmlspecialchars($old['hobbies'] ?? $employee['hobbies'] ?? '') ?></textarea>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Strengths (What you are good at)</label>
            <textarea name="strengths" class="form-control" rows="2" placeholder="e.g. Public speaking, leadership, critical thinking"><?= htmlspecialchars($old['strengths'] ?? $employee['strengths'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Weaknesses</label>
            <textarea name="weaknesses" class="form-control" rows="2" placeholder="e.g. Delegating tasks, public speaking anxiety"><?= htmlspecialchars($old['weaknesses'] ?? $employee['weaknesses'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="form-actions">
        <a href="<?= BASE_URL ?>/employees" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Save Changes' : 'Add Employee' ?></button>
    </div>

<script src="<?= BASE_URL ?>/assets/js/ph-address-selector.js"></script>
</form>