<?php
// ============================================================
// models/AttendanceLog.php
// ============================================================

require_once BASE_PATH . '/core/Model.php';

class AttendanceLog extends Model {
    protected string $table = 'attendance_logs';

    // Record check-in event
    public function checkIn(int $employeeId, string $time, string $status, ?string $deviceInfo): bool {
        return $this->execute(
            "INSERT INTO attendance_logs (employee_id, check_in_time, status, device_info) VALUES (?, ?, ?, ?)",
            [$employeeId, $time, $status, $deviceInfo]
        );
    }

    // Record check-out event
    public function checkOut(int $employeeId, string $time): bool {
        return $this->execute(
            "UPDATE attendance_logs SET check_out_time = ? WHERE employee_id = ? AND check_out_time IS NULL ORDER BY check_in_time DESC LIMIT 1",
            [$time, $employeeId]
        );
    }

    // Get today's logs
    public function getTodayLogs(): array {
        return $this->query(
            "SELECT a.*, e.first_name, e.last_name, e.profile_picture, e.employee_number, e.position, d.name AS department_name
             FROM attendance_logs a
             LEFT JOIN employees e ON e.id = a.employee_id
             LEFT JOIN departments d ON d.id = e.department_id
             WHERE DATE(a.check_in_time) = CURDATE()
             ORDER BY a.check_in_time DESC"
        );
    }

    // Get employee attendance history
    public function getByEmployee(int $employeeId): array {
        return $this->query(
            "SELECT a.*, e.first_name, e.last_name, e.profile_picture, e.employee_number, e.position, d.name AS department_name
             FROM attendance_logs a
             LEFT JOIN employees e ON e.id = a.employee_id
             LEFT JOIN departments d ON d.id = e.department_id
             WHERE a.employee_id = ?
             ORDER BY a.check_in_time DESC",
            [$employeeId]
        );
    }

    // Get check-in log for today for a specific employee
    public function getTodayLogByEmployee(int $employeeId): ?array {
        return $this->queryOne(
            "SELECT * FROM attendance_logs 
             WHERE employee_id = ? AND DATE(check_in_time) = CURDATE() 
             ORDER BY check_in_time DESC LIMIT 1",
            [$employeeId]
        );
    }

    // Get all attendance logs
    public function getAllLogs(): array {
        return $this->query(
            "SELECT a.*, e.first_name, e.last_name, e.profile_picture, e.employee_number, e.position, d.name AS department_name
             FROM attendance_logs a
             LEFT JOIN employees e ON e.id = a.employee_id
             LEFT JOIN departments d ON d.id = e.department_id
             ORDER BY a.check_in_time DESC"
        );
    }

    // Get logs by department id
    public function getLogsByDepartment(int $deptId): array {
        return $this->query(
            "SELECT a.*, e.first_name, e.last_name, e.profile_picture, e.employee_number, e.position, d.name AS department_name
             FROM attendance_logs a
             LEFT JOIN employees e ON e.id = a.employee_id
             LEFT JOIN departments d ON d.id = e.department_id
             WHERE e.department_id = ?
             ORDER BY a.check_in_time DESC",
            [$deptId]
        );
    }
}
