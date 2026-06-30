<?php
// ============================================================
// models/Employee.php
// ============================================================

require_once BASE_PATH . '/core/Model.php';

class Employee extends Model {
    protected string $table = 'employees';

    // ── ORIGINAL METHODS (RESTORED) ───────────────────────────

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


    // ── NEW METHODS FOR EXTENDED PROFILE & TRACKING ───────────

    // Fetch full profile details (One-to-Many relationships)
    public function getEducation(int $employeeId): array {
        return $this->query("SELECT * FROM employee_educations WHERE employee_id = ? ORDER BY year_graduated DESC", [$employeeId]);
    }

    public function getSkills(int $employeeId): array {
        return $this->query("SELECT * FROM employee_skills WHERE employee_id = ? ORDER BY proficiency DESC", [$employeeId]);
    }

    public function getHealthRecord(int $employeeId): ?array {
        return $this->queryOne("SELECT * FROM employee_health_records WHERE employee_id = ? LIMIT 1", [$employeeId]);
    }

    public function getPerformance(int $employeeId): array {
        return $this->query(
            "SELECT p.*, u.username as evaluator_name 
             FROM employee_performance p 
             LEFT JOIN users u ON p.evaluator_id = u.id 
             WHERE p.employee_id = ? 
             ORDER BY p.evaluation_date DESC", 
            [$employeeId]
        );
    }

    // MAP TRACKER: Update Employee Location
    public function updateLocation(int $employeeId, float $lat, float $lng, string $status = 'available'): bool {
        return $this->execute(
            "UPDATE employees SET latitude = ?, longitude = ?, location_updated_at = NOW(), availability_status = ? WHERE id = ?",
            [$lat, $lng, $status, $employeeId]
        );
    }

    // MAP TRACKER: Get all recent locations for the map dashboard
    public function getAllLocations(): array {
        return $this->query(
            "SELECT e.id, e.first_name, e.last_name, e.profile_picture, e.latitude, e.longitude, e.availability_status, e.location_updated_at, d.name AS department_name
             FROM employees e
             LEFT JOIN departments d ON d.id = e.department_id
             WHERE e.latitude IS NOT NULL AND e.longitude IS NOT NULL"
        );
    }

    public function getExperiences(int $employeeId): array {
        return $this->query("SELECT * FROM employee_experiences WHERE employee_id = ? ORDER BY start_date DESC", [$employeeId]);
    }

    public function getCertificates(int $employeeId): array {
        return $this->query("SELECT * FROM employee_certificates WHERE employee_id = ? ORDER BY issue_date DESC", [$employeeId]);
    }

    public function getFamily(int $employeeId): array {
        return $this->query("SELECT * FROM employee_family WHERE employee_id = ? ORDER BY relation ASC", [$employeeId]);
    }

    public function getAchievements(int $employeeId): array {
        return $this->query("SELECT * FROM employee_achievements WHERE employee_id = ? ORDER BY date_awarded DESC", [$employeeId]);
    }

    // Insert new professional experience
    public function addExperienceRecord(int $employeeId, string $company, string $position, string $startDate, ?string $endDate, string $description): bool {
        return $this->execute(
            "INSERT INTO employee_experiences (employee_id, company_name, position, start_date, end_date, description) 
             VALUES (?, ?, ?, ?, ?, ?)",
            [$employeeId, $company, $position, $startDate, $endDate, $description]
        );
    }

    // Insert new education record
    public function addEducationRecord(int $employeeId, string $degree, string $institution, string $yearGraduated): bool {
        return $this->execute(
            "INSERT INTO employee_educations (employee_id, degree, institution, year_graduated) VALUES (?, ?, ?, ?)",
            [$employeeId, $degree, $institution, $yearGraduated]
        );
    }

    // Insert new family record
    public function addFamilyRecord(int $employeeId, string $relation, string $fullName, ?string $contactNumber, ?string $occupation): bool {
        return $this->execute(
            "INSERT INTO employee_family (employee_id, relation, full_name, contact_number, occupation) VALUES (?, ?, ?, ?, ?)",
            [$employeeId, $relation, $fullName, $contactNumber, $occupation]
        );
    }

    // Insert new certificate record
    public function addCertificateRecord(int $employeeId, string $certificateName, string $issuingOrganization, string $issueDate): bool {
        return $this->execute(
            "INSERT INTO employee_certificates (employee_id, certificate_name, issuing_organization, issue_date) VALUES (?, ?, ?, ?)",
            [$employeeId, $certificateName, $issuingOrganization, $issueDate]
        );
    }
}