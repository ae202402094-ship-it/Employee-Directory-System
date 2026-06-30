<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Print') ?> — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #e2e8f0; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        @media print {
            body { background: white; align-items: flex-start; padding-top: 20px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <?= $content ?>
</body>
</html>