<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Print') ?> — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #e2e8f0; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        
        @media print {
            @page {
                size: 2.125in 3.375in;
                margin: 0;
            }
            body { 
                background: white; 
                display: block !important; 
                min-height: auto;
            }
            .no-print { 
                display: none !important; 
            }
            .print-container {
                gap: 0 !important;
                display: block !important;
            }
            .print-page-break {
                border: none !important;
                box-shadow: none !important;
                margin: 0 auto !important;
                border-radius: 0 !important;
                page-break-after: always !important;
                break-after: page !important;
            }
        }
    </style>
</head>
<body>
    <?= $content ?>
</body>
</html>