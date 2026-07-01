<?php
// ============================================================
// models/OvertimeLog.php
// ============================================================

require_once BASE_PATH . '/core/Model.php';

class OvertimeLog extends Model {
    protected string $table = 'overtime_logs';

    // File a new overtime log
    public function logHours(int $employeeId, string $date, float $hours, string $reason): bool {
        return $this->execute(
            "INSERT INTO overtime_logs (employee_id, ot_date, hours, reason) VALUES (?, ?, ?, ?)",
            [$employeeId, $date, $hours, $reason]
        );
    }

    // Get overtime logs for a specific employee
    public function getByEmployee(int $employeeId): array {
        return $this->query(
            "SELECT o.*, e.first_name, e.last_name, d.name AS department_name, u.username AS approver_name
             FROM overtime_logs o
             LEFT JOIN employees e ON e.id = o.employee_id
             LEFT JOIN departments d ON d.id = e.department_id
             LEFT JOIN users u ON u.id = o.approved_by
             WHERE o.employee_id = ?
             ORDER BY o.ot_date DESC",
            [$employeeId]
        );
    }

    // Get all pending overtime logs
    public function getPendingLogs(): array {
        return $this->query(
            "SELECT o.*, e.first_name, e.last_name, e.profile_picture, d.name AS department_name 
             FROM overtime_logs o
             LEFT JOIN employees e ON e.id = o.employee_id
             LEFT JOIN departments d ON d.id = e.department_id
             WHERE o.status = 'pending'
             ORDER BY o.created_at ASC"
        );
    }

    // Get all overtime history
    public function getAllHistory(): array {
        return $this->query(
            "SELECT o.*, e.first_name, e.last_name, e.profile_picture, d.name AS department_name, u.username AS approver_name
             FROM overtime_logs o
             LEFT JOIN employees e ON e.id = o.employee_id
             LEFT JOIN departments d ON d.id = e.department_id
             LEFT JOIN users u ON u.id = o.approved_by
             ORDER BY o.ot_date DESC"
        );
    }

    // Update overtime status (approve/reject)
    public function updateStatus(int $id, string $status, int $approvedBy): bool {
        return $this->execute(
            "UPDATE overtime_logs SET status = ?, approved_by = ? WHERE id = ?",
            [$status, $approvedBy, $id]
        );
    }
}
