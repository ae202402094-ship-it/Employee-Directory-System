<?php
// ============================================================
// config/app.php — Application Configuration
// ============================================================

define('APP_NAME',    'Employee Directory System');
define('APP_VERSION', '1.0.0');
define('APP_ENV',     getenv('APP_ENV') ?: 'development');  // 'development' | 'production'
define('BASE_URL', 'http://localhost/Employee-Directory-System/employee-directory/public');
//define('BASE_PATH',   dirname(__DIR__));

// Session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Lax');

// Error display (off in production)
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Default timezone
date_default_timezone_set('Asia/Manila');
