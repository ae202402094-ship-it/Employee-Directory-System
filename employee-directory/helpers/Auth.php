<?php
// ============================================================
// helpers/Auth.php — Authentication Utility Functions
// ============================================================

class Auth {

    // Hash a plain-text password
    public static function hash(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    // Verify plain-text against hash
    public static function verify(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    // Set session data on successful login
    public static function login(array $user): void {
        session_regenerate_id(true); // prevent session fixation
        $_SESSION['user_id']       = $user['id'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_email']    = $user['email'];
        $_SESSION['user_role']     = $user['role_name'];
        $_SESSION['user_role_id']  = $user['role_id'];
    }

    // Destroy session on logout
    public static function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }

    // Check if currently logged in
    public static function check(): bool {
        return !empty($_SESSION['user_id']);
    }

    // Get current user's role
    public static function role(): string {
        return $_SESSION['user_role'] ?? 'guest';
    }

    // Get current user ID
    public static function id(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    // Check if current user is admin
    public static function isAdmin(): bool {
        return self::role() === 'admin';
    }

    // Check if current user is hr_staff or admin
    public static function isHR(): bool {
        return in_array(self::role(), ['admin', 'hr_staff']);
    }
}
