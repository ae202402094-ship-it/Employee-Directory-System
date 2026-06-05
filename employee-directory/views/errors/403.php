<!-- views/errors/403.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', sans-serif; background: #f8fafc; display: flex;
               align-items: center; justify-content: center; min-height: 100vh; }
        .error-card { background: #fff; border-radius: 16px; padding: 48px 40px;
                      text-align: center; max-width: 400px; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
        .error-code { font-size: 72px; font-weight: 700; color: #dc2626; line-height: 1; }
        .error-title { font-size: 20px; font-weight: 600; color: #1e293b; margin: 12px 0 8px; }
        .error-msg { color: #64748b; font-size: 14px; line-height: 1.6; }
        .btn { display: inline-block; margin-top: 24px; padding: 10px 24px;
               background: #2563eb; color: #fff; border-radius: 8px;
               text-decoration: none; font-weight: 500; font-size: 14px; }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-code">403</div>
        <div class="error-title">Access Forbidden</div>
        <p class="error-msg">You don't have permission to access this page. Contact your administrator if you believe this is an error.</p>
        <a href="javascript:history.back()" class="btn">Go Back</a>
    </div>
</body>
</html>
