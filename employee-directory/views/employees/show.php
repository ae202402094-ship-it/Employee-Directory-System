<div class="page-toolbar">
    <a href="<?= BASE_URL ?>/employees" class="btn btn-ghost btn-sm">← Back to Directory</a>
    
    <div class="toolbar-actions">
        
        <?php if ($isOwner): ?>
        <button onclick="forceLocationSync()" class="btn btn-sm" style="background-color: #10b981; color: white; margin-right: 8px;">
            📍 Broadcast My Location
        </button>
        <?php endif; ?>

        <a href="<?= BASE_URL ?>/employees/<?= $employee['id'] ?>/id-card" class="btn btn-secondary btn-sm" target="_blank" style="margin-right: 8px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" style="margin-right: 4px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>
            Generate ID Card
        </a>

        <?php if ($canEdit): ?>
        <a href="<?= BASE_URL ?>/employees/<?= $employee['id'] ?>/edit" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" style="margin-right: 4px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit Base Profile
        </a>
        <?php endif; ?>
        
    </div>
</div>


<div class="profile-layout" style="grid-template-columns: 340px 1fr;">
    <div class="profile-sidebar">
        <div class="card profile-card" style="margin-bottom: 24px;">
            <div class="profile-hero" style="padding-top: 32px;">
                <?php if (!empty($employee['profile_picture'])): ?>
                    <img src="<?= BASE_URL ?>/assets/images/profiles/<?= htmlspecialchars($employee['profile_picture']) ?>" alt="Profile" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin: 0 auto 20px; display: block;">
                <?php else: ?>
                    <div class="profile-avatar" style="width: 120px; height: 120px; font-size: 40px;">
                        <?= strtoupper(substr($employee['first_name'], 0, 1) . substr($employee['last_name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>

                <h2 class="profile-name"><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></h2>
                <p class="profile-position" style="font-weight: 600; color: var(--brand);"><?= htmlspecialchars($employee['position'] ?? '—') ?></p>
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;"><?= htmlspecialchars($employee['department_name'] ?? 'No Department') ?></p>
                
                <span class="badge badge-<?= $employee['status'] ?> badge-lg"><?= $employee['status'] ?></span>
            </div>

            <?php if (!empty($employee['quote'])): ?>
                <div style="padding: 0 24px 24px; text-align: center; font-style: italic; color: var(--text-secondary); font-size: 13.5px;">
                    "<?= htmlspecialchars($employee['quote']) ?>"
                </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title">Contact Info</h3></div>
            <div class="card-body" style="padding: 16px 24px;">
                <div style="margin-bottom: 12px;">
                    <span style="display: block; font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Email</span>
                    <a href="mailto:<?= htmlspecialchars($employee['email']) ?>" style="color: var(--brand); font-size: 14px;"><?= htmlspecialchars($employee['email']) ?></a>
                </div>
                <div style="margin-bottom: 12px;">
                    <span style="display: block; font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Phone</span>
                    <span style="font-size: 14px;"><?= htmlspecialchars($employee['phone'] ?? '—') ?></span>
                </div>
                <div>
                    <span style="display: block; font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Address</span>
                    <span style="font-size: 14px;">
                        <?php 
                        $fullAddress = array_filter([
                            $employee['address'] ?? null,
                            $employee['barangay'] ?? null,
                            $employee['city'] ?? null
                        ]);
                        echo htmlspecialchars(!empty($fullAddress) ? implode(', ', $fullAddress) : '—');
                        ?>
                    </span>
                </div>
            </div>
        </div>
        
        <?php if (Auth::isAdmin()): ?>
        <div class="card card-danger-zone" style="margin-top: 24px;">
            <div class="card-header"><h3 class="card-title">Danger Zone</h3></div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/employees/<?= $employee['id'] ?>/delete" onsubmit="return confirm('Permanently delete this record?')">
                    <?= CSRF::field() ?>
                    <button type="submit" class="btn btn-danger btn-sm" style="width: 100%;">Delete Employee</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="profile-details">
        
        <?php if (!empty($employee['bio'])): ?>
        <div class="card">
            <div class="card-header"><h3 class="card-title">About</h3></div>
            <div class="card-body">
                <p style="white-space: pre-wrap; font-size: 14px; color: var(--text-secondary);"><?= htmlspecialchars($employee['bio']) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title">Professional Experience</h3>
               <?php if ($canEdit): ?><button type="button" onclick="openModal('addExperienceModal')" class="btn btn-ghost btn-xs">Add +</button><?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($experiences)): ?>
                    <div style="padding: 24px; text-align: center; color: var(--text-muted); font-size: 13px;">No experience records added yet.</div>
                <?php else: ?>
                    <?php foreach ($experiences as $exp): ?>
                    <div style="padding: 16px 24px; border-bottom: 1px solid var(--border);">
                        <div style="font-weight: 600; font-size: 15px; color: var(--text-primary);"><?= htmlspecialchars($exp['position']) ?></div>
                        <div style="font-size: 13px; color: var(--brand); margin-bottom: 4px;"><?= htmlspecialchars($exp['company_name']) ?></div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 8px;">
                            <?= date('M Y', strtotime($exp['start_date'])) ?> - <?= $exp['end_date'] ? date('M Y', strtotime($exp['end_date'])) : 'Present' ?>
                        </div>
                        <p style="font-size: 13px; color: var(--text-secondary); margin: 0;"><?= htmlspecialchars($exp['description'] ?? '') ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title">Education</h3>
                <?php if ($canEdit): ?><button type="button" onclick="openModal('addEducationModal')" class="btn btn-ghost btn-xs">Add +</button><?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($education)): ?>
                    <div style="text-align: center; color: var(--text-muted); font-size: 13px;">No education records.</div>
                <?php else: ?>
                    <?php foreach ($education as $edu): ?>
                    <div style="margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid var(--border);">
                        <div style="font-weight: 600; font-size: 14px;"><?= htmlspecialchars($edu['degree']) ?></div>
                        <div style="font-size: 13px; color: var(--text-secondary);"><?= htmlspecialchars($edu['institution']) ?></div>
                        <div style="font-size: 12px; color: var(--text-muted);">Class of <?= htmlspecialchars($edu['year_graduated']) ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title">Family Background</h3>
                <?php if ($canEdit): ?><button type="button" onclick="openModal('addFamilyModal')" class="btn btn-ghost btn-xs">Add +</button><?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($family)): ?>
                    <div style="text-align: center; color: var(--text-muted); font-size: 13px;">No family records.</div>
                <?php else: ?>
                    <?php foreach ($family as $fam): ?>
                    <div style="margin-bottom: 16px;">
                        <div style="font-weight: 600; font-size: 14px;"><?= htmlspecialchars($fam['full_name']) ?> <span style="font-weight: normal; color: var(--text-muted); font-size: 12px;">(<?= htmlspecialchars($fam['relation']) ?>)</span></div>
                        <div style="font-size: 13px; color: var(--text-secondary);"><?= htmlspecialchars($fam['contact_number'] ?? '') ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title">Certifications & Seminars</h3>
                <?php if ($canEdit): ?><button type="button" onclick="openModal('addCertificateModal')" class="btn btn-ghost btn-xs">Add +</button><?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($certificates)): ?>
                    <div style="padding: 24px; text-align: center; color: var(--text-muted); font-size: 13px;">No certificates added yet.</div>
                <?php else: ?>
                    <?php foreach ($certificates as $cert): ?>
                    <div style="padding: 16px 24px; border-bottom: 1px solid var(--border);">
                        <div style="font-weight: 600; font-size: 15px; color: var(--text-primary);"><?= htmlspecialchars($cert['certificate_name']) ?></div>
                        <div style="font-size: 13px; color: var(--text-secondary);"><?= htmlspecialchars($cert['issuing_organization']) ?></div>
                        <div style="font-size: 12px; color: var(--text-muted);">Issued: <?= date('M Y', strtotime($cert['issue_date'])) ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Skills Section -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title">Skills & Proficiencies</h3>
                <?php if ($canEdit): ?><button type="button" onclick="openModal('addSkillModal')" class="btn btn-ghost btn-xs">Add +</button><?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($skills)): ?>
                    <div style="text-align: center; color: var(--text-muted); font-size: 13px; padding: 12px 0;">No skills added yet.</div>
                <?php else: ?>
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        <?php foreach ($skills as $skill): ?>
                            <?php 
                            $badgeClass = 'badge-secondary';
                            if ($skill['proficiency'] === 'expert') $badgeClass = 'badge-success';
                            elseif ($skill['proficiency'] === 'advanced') $badgeClass = 'badge-primary';
                            elseif ($skill['proficiency'] === 'intermediate') $badgeClass = 'badge-warning';
                            ?>
                            <span class="badge <?= $badgeClass ?>" style="padding: 6px 12px; font-size: 13.5px; display: inline-flex; align-items: center; gap: 6px;">
                                <?= htmlspecialchars($skill['skill_name']) ?>
                                <span style="font-size: 10px; opacity: 0.8; font-weight: normal; text-transform: uppercase;">
                                    (<?= htmlspecialchars($skill['proficiency']) ?>)
                                </span>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Personal & Talent Profile Section -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Talent & Personal Profile</h3>
            </div>
            <div class="card-body" style="padding: 24px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                            <span style="color: var(--brand); font-size: 16px;">🎨</span>
                            <strong style="font-size: 14.5px; color: var(--text-primary);">Hobbies & Interests</strong>
                        </div>
                        <p style="font-size: 13.5px; color: var(--text-secondary); line-height: 1.5; margin: 0; white-space: pre-wrap;"><?= htmlspecialchars($employee['hobbies'] ?: 'Not specified') ?></p>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                            <span style="color: #10b981; font-size: 16px;">⚡</span>
                            <strong style="font-size: 14.5px; color: var(--text-primary);">Key Strengths</strong>
                        </div>
                        <p style="font-size: 13.5px; color: var(--text-secondary); line-height: 1.5; margin: 0; white-space: pre-wrap;"><?= htmlspecialchars($employee['strengths'] ?: 'Not specified') ?></p>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                            <span style="color: #ef4444; font-size: 16px;">🎯</span>
                            <strong style="font-size: 14.5px; color: var(--text-primary);">Areas of Growth (Weaknesses)</strong>
                        </div>
                        <p style="font-size: 13.5px; color: var(--text-secondary); line-height: 1.5; margin: 0; white-space: pre-wrap;"><?= htmlspecialchars($employee['weaknesses'] ?: 'Not specified') ?></p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal" id="addExperienceModal" role="dialog" aria-modal="true">
    <div class="modal-overlay" onclick="closeModal('addExperienceModal')"></div>
    <div class="modal-box">
        <div class="modal-header">
            <h3>Add Professional Experience</h3>
            <button type="button" onclick="closeModal('addExperienceModal')" class="modal-close">×</button>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/employees/<?= $employee['id'] ?>/experience">
            <?= CSRF::field() ?>
            <div class="form-group" style="padding: 0 24px; margin-top: 16px;">
                <label class="form-label">Company Name <span class="required">*</span></label>
                <input type="text" name="company_name" class="form-control" required>
            </div>
            <div class="form-group" style="padding: 0 24px;">
                <label class="form-label">Position / Job Title <span class="required">*</span></label>
                <input type="text" name="position" class="form-control" required>
            </div>
            <div class="form-row" style="padding: 0 24px;">
                <div class="form-group">
                    <label class="form-label">Start Date <span class="required">*</span></label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">End Date <span class="text-muted">(Leave blank if Present)</span></label>
                    <input type="date" name="end_date" class="form-control">
                </div>
            </div>
            <div class="form-group" style="padding: 0 24px;">
                <label class="form-label">Description / Duties</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('addExperienceModal')" class="btn btn-ghost">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Experience</button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="addEducationModal" role="dialog" aria-modal="true">
    <div class="modal-overlay" onclick="closeModal('addEducationModal')"></div>
    <div class="modal-box">
        <div class="modal-header">
            <h3>Add Education</h3>
            <button type="button" onclick="closeModal('addEducationModal')" class="modal-close">×</button>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/employees/<?= $employee['id'] ?>/education">
            <?= CSRF::field() ?>
            <div class="form-group" style="padding: 0 24px; margin-top: 16px;">
                <label class="form-label">Degree / Course <span class="required">*</span></label>
                <input type="text" name="degree" class="form-control" placeholder="e.g. BS Computer Science" required>
            </div>
            <div class="form-group" style="padding: 0 24px;">
                <label class="form-label">Institution / School <span class="required">*</span></label>
                <input type="text" name="institution" class="form-control" required>
            </div>
            <div class="form-group" style="padding: 0 24px;">
                <label class="form-label">Year Graduated <span class="required">*</span></label>
                <input type="number" name="year_graduated" class="form-control" min="1950" max="<?= date('Y') + 5 ?>" required>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('addEducationModal')" class="btn btn-ghost">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Education</button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="addFamilyModal" role="dialog" aria-modal="true">
    <div class="modal-overlay" onclick="closeModal('addFamilyModal')"></div>
    <div class="modal-box">
        <div class="modal-header">
            <h3>Add Family Member</h3>
            <button type="button" onclick="closeModal('addFamilyModal')" class="modal-close">×</button>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/employees/<?= $employee['id'] ?>/family">
            <?= CSRF::field() ?>
            <div class="form-group" style="padding: 0 24px; margin-top: 16px;">
                <label class="form-label">Full Name <span class="required">*</span></label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="form-row" style="padding: 0 24px;">
                <div class="form-group">
                    <label class="form-label">Relation <span class="required">*</span></label>
                    <select name="relation" class="form-control form-select" required>
                        <option value="Spouse">Spouse</option>
                        <option value="Child">Child</option>
                        <option value="Father">Father</option>
                        <option value="Mother">Mother</option>
                        <option value="Sibling">Sibling</option>
                        <option value="Emergency Contact">Emergency Contact</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" class="form-control">
                </div>
            </div>
            <div class="form-group" style="padding: 0 24px;">
                <label class="form-label">Occupation</label>
                <input type="text" name="occupation" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('addFamilyModal')" class="btn btn-ghost">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Family Record</button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="addCertificateModal" role="dialog" aria-modal="true">
    <div class="modal-overlay" onclick="closeModal('addCertificateModal')"></div>
    <div class="modal-box">
        <div class="modal-header">
            <h3>Add Certificate / Seminar</h3>
            <button type="button" onclick="closeModal('addCertificateModal')" class="modal-close">×</button>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/employees/<?= $employee['id'] ?>/certificate">
            <?= CSRF::field() ?>
            <div class="form-group" style="padding: 0 24px; margin-top: 16px;">
                <label class="form-label">Certificate / Seminar Name <span class="required">*</span></label>
                <input type="text" name="certificate_name" class="form-control" required>
            </div>
            <div class="form-group" style="padding: 0 24px;">
                <label class="form-label">Issuing Organization <span class="required">*</span></label>
                <input type="text" name="issuing_organization" class="form-control" required>
            </div>
            <div class="form-group" style="padding: 0 24px;">
                <label class="form-label">Issue Date <span class="required">*</span></label>
                <input type="date" name="issue_date" class="form-control" required>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('addCertificateModal')" class="btn btn-ghost">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Certificate</button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="addSkillModal" role="dialog" aria-modal="true">
    <div class="modal-overlay" onclick="closeModal('addSkillModal')"></div>
    <div class="modal-box">
        <div class="modal-header">
            <h3>Add Skill & Proficiency</h3>
            <button type="button" onclick="closeModal('addSkillModal')" class="modal-close">×</button>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/employees/<?= $employee['id'] ?>/skills">
            <?= CSRF::field() ?>
            <div class="form-group" style="padding: 0 24px; margin-top: 16px;">
                <label class="form-label">Skill Name <span class="required">*</span></label>
                <input type="text" name="skill_name" class="form-control" placeholder="e.g. PHP, Graphic Design, Public Speaking" required>
            </div>
            <div class="form-group" style="padding: 0 24px;">
                <label class="form-label">Proficiency Level <span class="required">*</span></label>
                <select name="proficiency" class="form-control form-select" required>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate" selected>Intermediate</option>
                    <option value="advanced">Advanced</option>
                    <option value="expert">Expert</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('addSkillModal')" class="btn btn-ghost">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Skill</button>
            </div>
        </form>
    </div>
</div>


<script>
function forceLocationSync() {
    if (!navigator.geolocation) {
        alert("Your browser does not support Geolocation.");
        return;
    }

    // Show loading state
    const btn = event.currentTarget;
    const originalText = btn.innerHTML;
    btn.innerHTML = "⏳ Syncing...";
    btn.disabled = true;

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const payload = {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
                status: 'available' 
            };

            fetch(`${BASE_URL}/location/sync`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Location successfully updated on the map!");
                } else {
                    alert("Failed to update location in database.");
                }
            })
            .catch(err => alert("Network error while syncing location."))
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        },
        function(error) {
            alert("Could not get location. Please ensure your device GPS is turned on and you have granted permission.");
            btn.innerHTML = originalText;
            btn.disabled = false;
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}
</script>