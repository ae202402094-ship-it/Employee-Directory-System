<div class="no-print" style="text-align: center; margin-bottom: 30px; font-family: system-ui, -apple-system, sans-serif;">
    <button onclick="window.print()" style="padding: 11px 24px; background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 14.5px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);">🖨️ Print All ID Cards</button>
    <a href="<?= BASE_URL ?>/employees" style="display: block; margin-top: 10px; color: #64748b; text-decoration: none; font-size: 13.5px;">← Back to Directory</a>
</div>

<div class="print-container" style="display: flex; flex-direction: column; align-items: center; gap: 40px; padding: 10px;">
    <?php foreach ($employees as $employee): ?>
    <div style="display: flex; gap: 24px; flex-wrap: wrap; justify-content: center; margin-bottom: 20px;" class="print-page-break">
        
        <!-- FRONT SIDE -->
        <div style="width: 2.25in; height: 3.5in; background: #ffffff; border-radius: 12px; position: relative; overflow: hidden; border: 1px solid #cbd5e1; box-sizing: border-box; font-family: 'Inter', system-ui, sans-serif; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
            <!-- Top Band -->
            <div style="background: linear-gradient(135deg, #1e1b4b, #312e81); color: #ffffff; padding: 18px 10px 35px; text-align: center; position: relative;">
                <div style="font-weight: 900; font-size: 12px; letter-spacing: 1.5px; text-transform: uppercase; color: #e0e7ff;"><?= htmlspecialchars(APP_NAME) ?></div>
                <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 12px; background: #4f46e5; clip-path: polygon(0 100%, 100% 0, 100% 100%);"></div>
            </div>

            <!-- Photo -->
            <div style="text-align: center; margin-top: -30px; position: relative; z-index: 5;">
                <?php if (!empty($employee['profile_picture'])): ?>
                    <img src="<?= BASE_URL ?>/assets/images/profiles/<?= htmlspecialchars($employee['profile_picture']) ?>" style="width: 82px; height: 82px; border-radius: 50%; object-fit: cover; border: 3px solid #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.15); background: #f8fafc;">
                <?php else: ?>
                    <div style="width: 82px; height: 82px; border-radius: 50%; background: #e2e8f0; color: #475569; display: inline-flex; align-items: center; justify-content: center; font-size: 26px; font-weight: 800; border: 3px solid #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                        <?= strtoupper(substr($employee['first_name'], 0, 1) . substr($employee['last_name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Details -->
            <div style="text-align: center; padding: 12px 10px 5px;">
                <div style="font-weight: 850; font-size: 15px; color: #1e293b; line-height: 1.15;"><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></div>
                
                <div style="margin-top: 6px; display: inline-block; background: rgba(79, 70, 229, 0.08); color: #4f46e5; font-weight: 750; font-size: 9.5px; padding: 2px 10px; border-radius: 9999px; text-transform: uppercase;">
                    <?= htmlspecialchars($employee['position'] ?? 'Employee') ?>
                </div>
                
                <div style="color: #64748b; font-size: 9px; font-weight: 600; margin-top: 12px;">
                    ID: <span style="color: #0f172a; font-weight: 700;"><?= htmlspecialchars($employee['employee_number']) ?></span>
                </div>
                <div style="color: #64748b; font-size: 9px; font-weight: 600; margin-top: 2px;">
                    DEPT: <span style="color: #0f172a; font-weight: 700;"><?= htmlspecialchars($employee['department_name'] ?? 'General') ?></span>
                </div>
            </div>

            <!-- Footer -->
            <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 6px; background: linear-gradient(90deg, #4f46e5, #7c3aed);"></div>
        </div>

        <!-- BACK SIDE -->
        <div style="width: 2.25in; height: 3.5in; background: #0f172a; border-radius: 12px; position: relative; overflow: hidden; border: 1px solid #1e293b; box-sizing: border-box; font-family: 'Inter', system-ui, sans-serif; color: #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
            <!-- Title -->
            <div style="padding: 14px 10px; text-align: center; border-bottom: 1px solid #1e293b; background: rgba(255,255,255,0.02);">
                <div style="font-weight: 800; font-size: 11px; letter-spacing: 1.5px; text-transform: uppercase; color: #6366f1;">Security Authorization</div>
            </div>

            <!-- QR -->
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; margin-top: 20px;">
                <?php if (!empty($employee['qrLink'])): ?>
                    <div class="qrcode-item" data-url="<?= htmlspecialchars($employee['qrLink']) ?>" style="padding: 6px; background: #ffffff; border-radius: 8px; display: inline-block;"></div>
                    <div style="font-size: 8.5px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; margin-top: 10px;">Scan to Clock-in / Login</div>
                <?php else: ?>
                    <div style="font-size: 9px; color: #ef4444; font-weight: bold; border: 1px dashed #ef4444; padding: 10px; border-radius: 6px;">Secure Token Missing</div>
                <?php endif; ?>
            </div>

            <!-- Notice -->
            <div style="position: absolute; bottom: 20px; left: 10px; right: 10px; text-align: center; font-size: 7px; color: #64748b; line-height: 1.35; border-top: 1px solid #1e293b; padding-top: 10px;">
                This card is official property of the organization. If found, please return to the Human Resources Department immediately.
            </div>

            <!-- Footer -->
            <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 6px; background: linear-gradient(90deg, #7c3aed, #4f46e5);"></div>
        </div>

    </div>
    <?php endforeach; ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.querySelectorAll(".qrcode-item").forEach(elem => {
        new QRCode(elem, {
            text: elem.getAttribute("data-url"),
            width: 80,
            height: 80,
            colorDark : "#0f172a",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.M
        });
    });
</script>
