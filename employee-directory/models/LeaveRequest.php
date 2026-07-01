<?php
// ============================================================
// models/LeaveRequest.php
// ============================================================

require_once BASE_PATH . '/core/Model.php';

class LeaveRequest extends Model {
    protected string $table = 'leave_requests';

    // File a new leave request
    public function fileRequest(int $employeeId, string $type, string $start, string $end, string $reason): bool {
        return $this->execute(
            "INSERT INTO leave_requests (employee_id, leave_type, start_date, end_date, reason) VALUES (?, ?, ?, ?, ?)",
            [$employeeId, $type, $start, $end, $reason]
        );
    }

    // Get requests for a specific employee
    public function getByEmployee(int $employeeId): array {
        return $this->query(
            "SELECT l.*, e.first_name, e.last_name, d.name AS department_name, u.username AS approver_name
             FROM leave_requests l
             LEFT JOIN employees e ON e.id = l.employee_id
             LEFT JOIN departments d ON d.id = e.department_id
             LEFT JOIN users u ON u.id = l.approved_by
             WHERE l.employee_id = ?
             ORDER BY l.created_at DESC",
            [$employeeId]
        );
    }

    // Get all pending requests
    public function getPendingRequests(): array {
        return $this->query(
            "SELECT l.*, e.first_name, e.last_name, e.profile_picture, d.name AS department_name 
             FROM leave_requests l
             LEFT JOIN employees e ON e.id = l.employee_id
             LEFT JOIN departments d ON d.id = e.department_id
             WHERE l.status = 'pending'
             ORDER BY l.created_at ASC"
        );
    }

    // Get all requests history (approvals/rejections)
    public function getAllRequestsHistory(): array {
        return $this->query(
            "SELECT l.*, e.first_name, e.last_name, e.profile_picture, d.name AS department_name, u.username AS approver_name
             FROM leave_requests l
             LEFT JOIN employees e ON e.id = l.employee_id
             LEFT JOIN departments d ON d.id = e.department_id
             LEFT JOIN users u ON u.id = l.approved_by
             ORDER BY l.created_at DESC"
        );
    }

    // Update request status (approve/reject)
    public function updateStatus(int $id, string $status, int $approvedBy): bool {
        return $this->execute(
            "UPDATE leave_requests SET status = ?, approved_by = ? WHERE id = ?",
            [$status, $approvedBy, $id]
        );
    }
}
