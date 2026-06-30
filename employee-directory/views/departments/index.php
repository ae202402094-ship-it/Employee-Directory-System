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

        <div class="dept-members-preview" style="margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
            <div class="avatar-stack" style="display: flex; align-items: center;">
                <?php 
                $maxAvatars = 4;
                $members = $dept['members'] ?? [];
                $count = count($members);
                $displayed = array_slice($members, 0, $maxAvatars);
                foreach ($displayed as $index => $m):
                    $initials = strtoupper(substr($m['first_name'], 0, 1) . substr($m['last_name'], 0, 1));
                    $avatarUrl = !empty($m['profile_picture']) ? BASE_URL . '/assets/images/profiles/' . $m['profile_picture'] : '';
                    $overlapStyle = $index > 0 ? 'margin-left: -12px;' : '';
                ?>
                    <?php if ($avatarUrl): ?>
                        <img src="<?= $avatarUrl ?>" 
                             title="<?= htmlspecialchars($m['first_name'] . ' ' . $m['last_name']) ?>" 
                             style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid var(--surface); <?= $overlapStyle ?>">
                    <?php else: ?>
                        <div title="<?= htmlspecialchars($m['first_name'] . ' ' . $m['last_name']) ?>" 
                             style="width: 32px; height: 32px; border-radius: 50%; background: var(--brand-light); color: var(--brand); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; border: 2px solid var(--surface); <?= $overlapStyle ?>">
                            <?= $initials ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                
                <?php if ($count > $maxAvatars): ?>
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--border-strong); color: var(--text-primary); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; border: 2px solid var(--surface); margin-left: -12px;" title="View all members">
                        +<?= ($count - $maxAvatars) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($count === 0): ?>
                    <span style="font-size: 12px; color: var(--text-muted);">No members assigned</span>
                <?php endif; ?>
            </div>
            
            <?php if ($count > 0): ?>
            <button type="button" class="btn btn-ghost btn-sm" style="color: var(--brand); font-size: 12px; padding: 4px 8px;" onclick='openViewMembers(<?= json_encode($dept['name'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($dept['members'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                View Members
            </button>
            <?php endif; ?>
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

<div class="modal" id="viewMembersModal" role="dialog" aria-modal="true">
    <div class="modal-overlay" onclick="closeModal('viewMembersModal')"></div>
    <div class="modal-box" style="max-width: 600px;">
        <div class="modal-header" style="border-bottom: 1px solid var(--border); padding-bottom: 16px;">
            <h3 id="viewMembersTitle" style="font-family: 'Syne', sans-serif; font-size: 18px; color: var(--text-primary);">Department Members</h3>
            <button type="button" onclick="closeModal('viewMembersModal')" class="modal-close" style="background: none; border: none; font-size: 24px; color: var(--text-muted); cursor: pointer;">×</button>
        </div>
        <div class="modal-body" style="max-height: 400px; overflow-y: auto; padding: 16px 0;">
            <div id="membersListContainer">
                <!-- Dynamically populated -->
            </div>
        </div>
        <div class="modal-footer" style="padding: 16px 24px; display: flex; justify-content: flex-end; border-top: 1px solid var(--border);">
            <button type="button" onclick="closeModal('viewMembersModal')" class="btn btn-ghost">Close</button>
        </div>
    </div>
</div>

<script>
function openEditDept(dept) {
    document.getElementById('editDeptForm').action = '<?= BASE_URL ?>/departments/' + dept.id + '/update';
    document.getElementById('editDeptName').value  = dept.name;
    document.getElementById('editDeptDesc').value  = dept.description || '';
    openModal('editDeptModal');
}

function openViewMembers(deptName, members) {
    document.getElementById('viewMembersTitle').textContent = deptName + ' Members (' + members.length + ')';
    const container = document.getElementById('membersListContainer');
    container.innerHTML = '';
    
    members.forEach(member => {
        const name = member.first_name + ' ' + member.last_name;
        const avatarSrc = member.profile_picture 
            ? '<?= BASE_URL ?>/assets/images/profiles/' + member.profile_picture 
            : '';
        const initials = (member.first_name.charAt(0) + member.last_name.charAt(0)).toUpperCase();
        
        const avatarHTML = avatarSrc 
            ? `<img src="${avatarSrc}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border);">`
            : `<div style="width: 36px; height: 36px; border-radius: 50%; background: var(--brand-light); color: var(--brand); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 12px; border: 1px solid var(--border);">${initials}</div>`;
        
        const statusLabel = member.status.charAt(0).toUpperCase() + member.status.slice(1);
        
        const row = document.createElement('div');
        row.style = 'display: flex; align-items: center; justify-content: space-between; padding: 12px 24px; border-bottom: 1px solid var(--border);';
        row.innerHTML = `
            <div style="display: flex; align-items: center; gap: 12px;">
                ${avatarHTML}
                <div>
                    <a href="<?= BASE_URL ?>/employees/${member.id}" style="font-weight: 600; color: var(--text-primary); text-decoration: none; hover: underline;">${name}</a>
                    <div style="font-size: 12px; color: var(--text-muted);">${member.position || 'No position'}</div>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <span class="badge badge-${member.status}" style="font-size: 11px; font-weight: 600;">${statusLabel}</span>
            </div>
        `;
        container.appendChild(row);
    });
    
    openModal('viewMembersModal');
}
</script>