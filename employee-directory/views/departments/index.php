<div class="page-toolbar" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2 style="font-family: 'Syne', sans-serif; font-size: 24px; color: var(--text-primary);">Department Directory</h2>
        <p style="color: var(--text-muted); font-size: 14px;">Manage company divisions and teams.</p>
    </div>
    
    <?php if (Auth::isAdmin()): ?>
    <button type="button" class="btn btn-primary" onclick="openModal('createDeptModal')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Department
    </button>
    <?php endif; ?>
</div>

<div class="dept-grid">
    <?php foreach ($departments as $dept): ?>
    <div class="dept-card">
        <div class="dept-header">
            <div class="dept-icon">
                <?= strtoupper(substr($dept['name'], 0, 2)) ?>
            </div>
            <div>
                <h3 class="dept-name"><?= htmlspecialchars($dept['name']) ?></h3>
                <p class="dept-desc"><?= htmlspecialchars($dept['description'] ?? 'No description provided.') ?></p>
            </div>
        </div>
        
        <div class="dept-meta">
            <div class="dept-count">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                <?= $dept['employee_count'] ?> Active Member<?= $dept['employee_count'] != 1 ? 's' : '' ?>
            </div>
            
            <?php if (Auth::isAdmin()): ?>
            <div class="action-btns" style="display: flex; gap: 8px;">
                <button type="button" class="btn btn-secondary btn-sm" onclick='openEditDept(<?= json_encode($dept, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>Edit</button>
                
                <?php if (!$dept['employee_count']): ?>
                <form method="POST" action="<?= BASE_URL ?>/departments/<?= $dept['id'] ?>/delete" onsubmit="return confirm('Delete this department?')">
                    <?= CSRF::field() ?>
                    <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--red);">Delete</button>
                </form>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if (empty($departments)): ?>
    <div class="empty-state" style="grid-column: 1 / -1;">
        <p>No departments yet.</p>
    </div>
    <?php endif; ?>
</div>

<div class="modal" id="createDeptModal" role="dialog" aria-modal="true">
    <div class="modal-overlay" onclick="closeModal('createDeptModal')"></div>
    <div class="modal-box">
        <div class="modal-header">
            <h3>New Department</h3>
            <button type="button" onclick="closeModal('createDeptModal')" class="modal-close">×</button>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/departments/store">
            <?= CSRF::field() ?>
            <div class="form-group">
                <label class="form-label">Name <span class="required">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Engineering" required autofocus>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Brief description…"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('createDeptModal')" class="btn btn-ghost">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="editDeptModal" role="dialog" aria-modal="true">
    <div class="modal-overlay" onclick="closeModal('editDeptModal')"></div>
    <div class="modal-box">
        <div class="modal-header">
            <h3>Edit Department</h3>
            <button type="button" onclick="closeModal('editDeptModal')" class="modal-close">×</button>
        </div>
        <form id="editDeptForm" method="POST">
            <?= CSRF::field() ?>
            <div class="form-group">
                <label class="form-label">Name <span class="required">*</span></label>
                <input type="text" name="name" id="editDeptName" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" id="editDeptDesc" class="form-control" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('editDeptModal')" class="btn btn-ghost">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditDept(dept) {
    document.getElementById('editDeptForm').action = '<?= BASE_URL ?>/departments/' + dept.id + '/update';
    document.getElementById('editDeptName').value  = dept.name;
    document.getElementById('editDeptDesc').value  = dept.description || '';
    openModal('editDeptModal');
}
</script>