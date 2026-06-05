<?php
// ============================================================
// models/User.php
// ============================================================

require_once BASE_PATH . '/core/Model.php';

class User extends Model {
    protected string $table = 'users';

    // Find user with role name (for login)
    public function findWithRole(string $email): ?array {
        return $this->queryOne(
            "SELECT u.*, r.name AS role_name
               FROM users u
               JOIN roles r ON r.id = u.role_id
              WHERE u.email = ?
              LIMIT 1",
            [$email]
        );
    }

    // Get all users with role info
    public function allWithRoles(): array {
        return $this->query(
            "SELECT u.*, r.name AS role_name
               FROM users u
               JOIN roles r ON r.id = u.role_id
              ORDER BY u.created_at DESC"
        );
    }

    // Get user with role by ID
    public function findWithRoleById(int $id): ?array {
        return $this->queryOne(
            "SELECT u.*, r.name AS role_name
               FROM users u
               JOIN roles r ON r.id = u.role_id
              WHERE u.id = ?",
            [$id]
        );
    }

    // Check email uniqueness (exclude a specific user for edit)
    public function emailExists(string $email, int $excludeId = 0): bool {
        $result = $this->queryOne(
            "SELECT id FROM users WHERE email = ? AND id != ?",
            [$email, $excludeId]
        );
        return $result !== null;
    }

    // Check username uniqueness
    public function usernameExists(string $username, int $excludeId = 0): bool {
        $result = $this->queryOne(
            "SELECT id FROM users WHERE username = ? AND id != ?",
            [$username, $excludeId]
        );
        return $result !== null;
    }

    // Toggle active status
    public function toggleStatus(int $id): bool {
        return $this->execute(
            "UPDATE users SET is_active = NOT is_active WHERE id = ?",
            [$id]
        );
    }

    // Count active users
    public function countActive(): int {
        $result = $this->queryOne("SELECT COUNT(*) AS cnt FROM users WHERE is_active = 1");
        return (int)($result['cnt'] ?? 0);
    }
}
