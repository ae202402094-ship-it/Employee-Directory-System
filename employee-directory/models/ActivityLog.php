<?php
// ============================================================
// models/ActivityLog.php
// ============================================================

require_once BASE_PATH . '/core/Model.php';

class ActivityLog extends Model {
    protected string $table = 'activity_logs';

    // Log a new activity trail entry
    public static function log(string $actionType, string $details): bool {
        $logModel = new self();
        $userId = $_SESSION['user_id'] ?? 0;
        if (!$userId) return false;
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        
        return $logModel->execute(
            "INSERT INTO activity_logs (user_id, action_type, details, ip_address) VALUES (?, ?, ?, ?)",
            [(int)$userId, $actionType, $details, $ip]
        );
    }

    // Get all activity logs ordered by date
    public function getAllLogs(): array {
        return $this->query(
            "SELECT a.*, u.username, r.name AS role_name, e.first_name, e.last_name 
             FROM activity_logs a
             LEFT JOIN users u ON u.id = a.user_id
             LEFT JOIN roles r ON r.id = u.role_id
             LEFT JOIN employees e ON e.user_id = u.id
             ORDER BY a.created_at DESC"
        );
    }
}
