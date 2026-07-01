<div class="page-toolbar">
    <?php if (Auth::isHR()): ?>
    <a href="<?= BASE_URL ?>/employees/create" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="15" height="15"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Employee
    </a>
    <a href="<?= BASE_URL ?>/employees/export" class="btn btn-secondary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Export CSV
    </a>
    <button type="button" class="btn btn-secondary" id="printBulkBtn" style="display: none; align-items: center; gap: 8px;">
        🖨️ Print Selected IDs (<span id="selectedCount">0</span>)
    </button>
    <?php endif; ?>
</div>

<!-- Filters -->
<div class="card filter-card">
    <form method="GET" action="<?= BASE_URL ?>/employees" class="filter-form">
        <div class="filter-group">
            <input
                type="search"
                name="search"
                class="form-control"
                placeholder="Search by name, employee number, position…"
                value="<?= htmlspecialchars($filters['search']) ?>"
            >
        </div>
        <div class="filter-group filter-group-sm">
            <select name="status" class="form-control form-select">
                <option value="">All Status</option>
                <option value="active"     <?= $filters['status'] === 'active'     ? 'selected' : '' ?>>Active</option>
                <option value="inactive"   <?= $filters['status'] === 'inactive'   ? 'selected' : '' ?>>Inactive</option>
                <option value="terminated" <?= $filters['status'] === 'terminated' ? 'selected' : '' ?>>Terminated</option>
            </select>
        </div>
        <div class="filter-group filter-group-sm">
            <select name="department" class="form-control form-select">
                <option value="">All Departments</option>
                <?php foreach ($departments as $dept): ?>
                <option value="<?= $dept['id'] ?>" <?= (int)$filters['department'] === (int)$dept['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($dept['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <input
                type="search"
                name="skill"
                class="form-control"
                placeholder="Search by skill..."
                value="<?= htmlspecialchars($filters['skill'] ?? '') ?>"
            >
        </div>
        <div class="filter-group filter-group-sm">
            <select name="proficiency" class="form-control form-select">
                <option value="">All Levels</option>
                <option value="beginner"     <?= ($filters['proficiency'] ?? '') === 'beginner'     ? 'selected' : '' ?>>Beginner</option>
                <option value="intermediate" <?= ($filters['proficiency'] ?? '') === 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
                <option value="advanced"     <?= ($filters['proficiency'] ?? '') === 'advanced'     ? 'selected' : '' ?>>Advanced</option>
                <option value="expert"       <?= ($filters['proficiency'] ?? '') === 'expert'       ? 'selected' : '' ?>>Expert</option>
            </select>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                Filter
            </button>
            <a href="<?= BASE_URL ?>/employees" class="btn btn-ghost">Clear</a>
        </div>
    </form>
</div>

<!-- Employee Table -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <?= count($employees) ?> Employee<?= count($employees) !== 1 ? 's' : '' ?>
        </h2>
    </div>

    <?php if (empty($employees)): ?>
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="52" height="52"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            <p>No employees found.</p>
            <?php if (Auth::isHR()): ?>
            <a href="<?= BASE_URL ?>/employees/create" class="btn btn-primary btn-sm">Add First Employee</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <?php if (Auth::isHR()): ?>
                    <th style="width: 40px; text-align: center;"><input type="checkbox" id="selectAllEmployees"></th>
                    <?php endif; ?>
                    <th>Employee</th>
                    <th>Number</th>
                    <th>Position</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $emp): ?>
                <tr>
                    <?php if (Auth::isHR()): ?>
                    <td style="text-align: center;"><input type="checkbox" class="employee-checkbox" value="<?= $emp['id'] ?>"></td>
                    <?php endif; ?>
                    <td>
                        <div class="table-emp">
                            <div class="emp-avatar emp-avatar-sm">
                                <?= strtoupper(substr($emp['first_name'], 0, 1) . substr($emp['last_name'], 0, 1)) ?>
                            </div>
                            <div>
                                <span class="emp-fullname"><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></span>
                                <span class="emp-email"><?= htmlspecialchars($emp['email']) ?></span>
                            </div>
                        </div>
                    </td>
                    <td class="mono"><?= htmlspecialchars($emp['employee_number']) ?></td>
                    <td><?= htmlspecialchars($emp['position'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($emp['department_name'] ?? '—') ?></td>
                    <td><span class="badge badge-<?= $emp['status'] ?>"><?= ucfirst($emp['status']) ?></span></td>
                    <td class="text-right">
                        <div class="action-btns">
                            <a href="<?= BASE_URL ?>/employees/<?= $emp['id'] ?>" class="btn btn-ghost btn-xs">View</a>
                            <?php if (Auth::isHR()): ?>
                            <a href="<?= BASE_URL ?>/employees/<?= $emp['id'] ?>/edit" class="btn btn-ghost btn-xs">Edit</a>
                            <?php endif; ?>
                            <?php if (Auth::isAdmin()): ?>
                            <form method="POST" action="<?= BASE_URL ?>/employees/<?= $emp['id'] ?>/delete"
                                  onsubmit="return confirm('Delete this employee? This cannot be undone.')" style="display:inline">
                                <?= CSRF::field() ?>
                                <button type="submit" class="btn btn-xs btn-danger-ghost">Delete</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const selectAll = document.getElementById('selectAllEmployees');
    const checkboxes = document.querySelectorAll('.employee-checkbox');
    const printBtn = document.getElementById('printBulkBtn');
    const countSpan = document.getElementById('selectedCount');

    function updatePrintButton() {
        const checked = document.querySelectorAll('.employee-checkbox:checked');
        countSpan.textContent = checked.length;
        if (checked.length > 0) {
            printBtn.style.display = 'inline-flex';
        } else {
            printBtn.style.display = 'none';
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', () => {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updatePrintButton();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updatePrintButton);
    });

    if (printBtn) {
        printBtn.addEventListener('click', () => {
            const checked = document.querySelectorAll('.employee-checkbox:checked');
            const ids = Array.from(checked).map(cb => cb.value).join(',');
            if (ids) {
                window.open(`<?= BASE_URL ?>/employees/print-bulk?ids=${ids}`, '_blank');
            }
        });
    }
});
</script>