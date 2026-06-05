<?php
// ============================================================
// core/Controller.php — Base Controller
// ============================================================

class Controller {

    // Render a view with optional data
    protected function view(string $view, array $data = []): void {
        // Extract data to local variables
        extract($data);

        $viewPath = BASE_PATH . '/views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(500);
            die("View [{$view}] not found at {$viewPath}");
        }

        // Determine layout: auth pages vs main app
        $layout = $data['layout'] ?? 'main';
        $layoutPath = BASE_PATH . '/views/layouts/' . $layout . '.php';

        if (!file_exists($layoutPath)) {
            http_response_code(500);
            die("Layout [{$layout}] not found.");
        }

        // Capture the view content
        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        // Render inside layout
        require $layoutPath;
    }

    // Redirect to a URL
    protected function redirect(string $url): void {
        if (!headers_sent()) {
            header('Location: ' . BASE_URL . $url);
            exit;
        }
        echo "<script>window.location='" . BASE_URL . $url . "';</script>";
        exit;
    }

    // Return JSON response (for AJAX)
    protected function json(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Get POST input, sanitized
    protected function input(string $key, mixed $default = null): mixed {
        $value = $_POST[$key] ?? $default;
        if (is_string($value)) {
            return trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        }
        return $value;
    }

    // Get GET input, sanitized
    protected function query(string $key, mixed $default = null): mixed {
        $value = $_GET[$key] ?? $default;
        if (is_string($value)) {
            return trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        }
        return $value;
    }

    // Flash a session message
    protected function flash(string $type, string $message): void {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    // Check if authenticated (redirect to login if not)
    protected function requireAuth(): void {
        Middleware::auth();
    }

    // Check if user has required role
    protected function requireRole(string ...$roles): void {
        Middleware::role(...$roles);
    }
}
