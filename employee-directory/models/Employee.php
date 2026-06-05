<?php
// ============================================================
// models/Employee.php
// ============================================================

require_once BASE_PATH . '/core/Model.php';

class Employee extends Model {
    protected string $table = 'employees';

    // All employees with department name
    public function allWithDepartment(string $orderBy = 'e.last_name', string $dir = 'ASC'): array {
        $dir = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';
        return $this->query(
            "SELECT e.*, d.name AS department_name
               FROM employees e
               LEFT JOIN departments d ON d.id = e.department_id
              ORDER BY {$orderBy} {$dir}"
        );
    }

    // Single employee with department
    public function findWithDepartment(int $id): ?array {
        return $this->queryOne(
            "SELECT e.*, d.name AS department_name
               FROM employees e
               LEFT JOIN departments d ON d.id = e.department_id
              WHERE e.id = ?",
            [$id]
        );
    }

    // Search by name, employee number, or position
    public function search(string $term, ?string $status = null, ?int $departmentId = null): array {
        $term  = '%' . $term . '%';
        $sql   = "SELECT e.*, d.name AS department_name
                    FROM employees e
                    LEFT JOIN departments d ON d.id = e.department_id
                   WHERE (e.first_name LIKE ? OR e.last_name LIKE ?
                          OR e.employee_number LIKE ? OR e.position LIKE ?
                          OR e.email LIKE ?)";
        $params = [$term, $term, $term, $term, $term];

        if ($status) {
            $sql .= ' AND e.status = ?';
            $params[] = $status;
        }

        if ($departmentId) {
            $sql .= ' AND e.department_id = ?';
            $params[] = $departmentId;
        }

        $sql .= ' ORDER BY e.last_name ASC, e.first_name ASC';
        return $this->query($sql, $params);
    }

    // Count by status
    public function countByStatus(string $status): int {
        $result = $this->queryOne(
            "SELECT COUNT(*) AS cnt FROM employees WHERE status = ?",
            [$status]
        );
        return (int)($result['cnt'] ?? 0);
    }

    // Count new employees this month
    public function countNewThisMonth(): int {
        $result = $this->queryOne(
            "SELECT COUNT(*) AS cnt FROM employees
              WHERE MONTH(created_at) = MONTH(NOW())
                AND YEAR(created_at) = YEAR(NOW())"
        );
        return (int)($result['cnt'] ?? 0);
    }

    // Count per department (for dashboard chart)
    public function countPerDepartment(): array {
        return $this->query(
            "SELECT d.name AS department, COUNT(e.id) AS total
               FROM departments d
               LEFT JOIN employees e ON e.department_id = d.id AND e.status = 'active'
              GROUP BY d.id, d.name
              ORDER BY total DESC"
        );
    }

    // Recent 5 employees
    public function recent(int $limit = 5): array {
        return $this->query(
            "SELECT e.*, d.name AS department_name
               FROM employees e
               LEFT JOIN departments d ON d.id = e.department_id
              ORDER BY e.created_at DESC
              LIMIT ?",
            [$limit]
        );
    }

    // Generate next employee number
    public function nextEmployeeNumber(): string {
        $result = $this->queryOne(
            "SELECT employee_number FROM employees ORDER BY id DESC LIMIT 1"
        );
        if ($result) {
            // Extract numeric part and increment
            preg_match('/(\d+)$/', $result['employee_number'], $m);
            $next = ((int)($m[1] ?? 0)) + 1;
        } else {
            $next = 1;
        }
        return 'EMP-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    // Check email uniqueness
    public function emailExists(string $email, int $excludeId = 0): bool {
        $result = $this->queryOne(
            "SELECT id FROM employees WHERE email = ? AND id != ?",
            [$email, $excludeId]
        );
        return $result !== null;
    }
}
