<?php
// ============================================================
// models/Department.php
// ============================================================

require_once BASE_PATH . '/core/Model.php';

class Department extends Model {
    protected string $table = 'departments';

    // All departments with employee count
    public function allWithCount(): array {
        return $this->query(
            "SELECT d.*, COUNT(e.id) AS employee_count
               FROM departments d
               LEFT JOIN employees e ON e.department_id = d.id AND e.status = 'active'
              GROUP BY d.id
              ORDER BY d.name ASC"
        );
    }

    // Check if name is unique (exclude self on edit)
    public function nameExists(string $name, int $excludeId = 0): bool {
        $result = $this->queryOne(
            "SELECT id FROM departments WHERE name = ? AND id != ?",
            [$name, $excludeId]
        );
        return $result !== null;
    }

    // Check if department has employees (before delete)
    public function hasEmployees(int $id): bool {
        $result = $this->queryOne(
            "SELECT COUNT(*) AS cnt FROM employees WHERE department_id = ?",
            [$id]
        );
        return ((int)($result['cnt'] ?? 0)) > 0;
    }

    // Get active and inactive employees in a specific department
    public function getMembers(int $deptId): array {
        return $this->query(
            "SELECT id, first_name, last_name, position, profile_picture, employee_number, email, status 
               FROM employees 
              WHERE department_id = ?
              ORDER BY CASE WHEN status = 'active' THEN 0 ELSE 1 END, first_name ASC, last_name ASC",
            [$deptId]
        );
    }
}
