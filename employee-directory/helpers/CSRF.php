<?php
// ============================================================
// helpers/CSRF.php — Cross-Site Request Forgery Protection
// ============================================================

class CSRF {

    // Generate or retrieve the CSRF token for this session
    public static function token(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Output a hidden input field with the CSRF token
    public static function field(): string {
        return '<input type="hidden" name="_csrf_token" value="' . self::token() . '">';
    }

    // Verify that the submitted token matches the session token
    public static function verify(): bool {
        $submitted = $_POST['_csrf_token'] ?? '';
        $session   = $_SESSION['csrf_token'] ?? '';

        if (empty($submitted) || empty($session)) return false;

        // Timing-safe comparison
        return hash_equals($session, $submitted);
    }

    // Abort if CSRF verification fails
    public static function protect(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!self::verify()) {
                http_response_code(419);
                die('CSRF token mismatch. Please go back and try again.');
            }
        }
    }
}
