<?php
// ============================================================
// core/Middleware.php — Authentication & Authorization
// ============================================================

class Middleware {

    // Redirect to login if not authenticated
    public static function auth(): void {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['intended'] = $_SERVER['REQUEST_URI'] ?? '/dashboard';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    // Check if user has one of the given roles (by role name)
    public static function role(string ...$roles): void {
        self::auth(); // must be logged in first

        $userRole = $_SESSION['user_role'] ?? '';
        if (!in_array($userRole, $roles)) {
            http_response_code(403);
            require BASE_PATH . '/views/errors/403.php';
            exit;
        }
    }

    // Redirect authenticated users away from guest-only pages (e.g., login)
    public static function guest(): void {
        if (!empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
    }
}
