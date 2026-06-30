<div class="no-print" style="text-align: center; margin-bottom: 30px;">
    <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 14px;">🖨️ Print All ID Cards</button>
    <a href="<?= BASE_URL ?>/employees" style="display: block; margin-top: 10px; color: #64748b; text-decoration: none;">← Back to Directory</a>
</div>

<div class="print-container" style="display: flex; flex-direction: column; align-items: center; gap: 40px;">
    <?php foreach ($employees as $employee): ?>
    <div class="print-page-break" style="width: 2.125in; height: 3.375in; background: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); position: relative; overflow: hidden; border: 1px solid #cbd5e1; box-sizing: border-box;">
        
        <div style="background: #1e293b; color: white; padding: 16px 10px 40px; text-align: center;">
            <div style="font-weight: 800; font-size: 16px; letter-spacing: 1px;"><?= strtoupper(APP_NAME) ?></div>
        </div>

        <div style="text-align: center; margin-top: -35px; position: relative; z-index: 2;">
            <?php if (!empty($employee['profile_picture'])): ?>
                <img src="<?= BASE_URL ?>/assets/images/profiles/<?= htmlspecialchars($employee['profile_picture']) ?>" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); background: white;">
            <?php else: ?>
                <div style="width: 80px; height: 80px; border-radius: 50%; background: #e2e8f0; color: #475569; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; border: 3px solid white;">
                    <?= strtoupper(substr($employee['first_name'], 0, 1) . substr($employee['last_name'], 0, 1)) ?>
                </div>
            <?php endif; ?>
        </div>

        <div style="text-align: center; padding: 10px;">
            <div style="font-weight: 800; font-size: 16px; color: #0f172a; line-height: 1.2;"><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></div>
            <div style="color: #2563eb; font-weight: 700; font-size: 11px; margin-top: 4px;"><?= strtoupper(htmlspecialchars($employee['position'] ?? 'EMPLOYEE')) ?></div>
            <div style="color: #64748b; font-size: 10px; margin-top: 2px;"><?= htmlspecialchars($employee['employee_number']) ?></div>
        </div>

        <div style="position: absolute; bottom: 12px; width: 100%; display: flex; justify-content: center;">
            <?php if (!empty($employee['qrLink'])): ?>
                <div class="qrcode-item" data-url="<?= htmlspecialchars($employee['qrLink']) ?>" style="padding: 4px; background: white; border: 1px solid #e2e8f0; border-radius: 4px;"></div>
            <?php else: ?>
                <div style="font-size: 10px; color: red;">No Login Link Available</div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.querySelectorAll(".qrcode-item").forEach(elem => {
        new QRCode(elem, {
            text: elem.getAttribute("data-url"),
            width: 60,
            height: 60,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.L
        });
    });
</script>
