<?php
require_once __DIR__ . '/../config/Database.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function authenticate(string $email, string $password): ?array {
        $stmt = $this->db->prepare("
            SELECT u.*, r.role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.role_id 
            WHERE u.email = ? AND u.is_active = 1
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }

    public function findById(int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT u.*, r.role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.role_id 
            WHERE u.user_id = ?
        ");
        $stmt->execute([$userId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT u.user_id, u.name, u.email, u.organization, u.is_active, r.role_name, r.role_id
            FROM users u
            JOIN roles r ON u.role_id = r.role_id
            ORDER BY u.user_id ASC
        ");
        return $stmt->fetchAll();
    }

    public function getByRole(int $roleId): array {
        $stmt = $this->db->prepare("
            SELECT user_id, name, email, organization 
            FROM users 
            WHERE role_id = ? AND is_active = 1
            ORDER BY name ASC
        ");
        $stmt->execute([$roleId]);
        return $stmt->fetchAll();
    }

    public function create(int $roleId, string $name, string $email, string $password, string $organization = ''): int {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("
            INSERT INTO users (role_id, name, email, password, organization, is_active, created_at)
            VALUES (?, ?, ?, ?, ?, 1, NOW())
        ");
        $stmt->execute([$roleId, $name, $email, $hash, $organization]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $userId, int $roleId, string $name, string $email, string $organization = '', int $isActive = 1): bool {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET role_id = ?, name = ?, email = ?, organization = ?, is_active = ?
            WHERE user_id = ?
        ");
        return $stmt->execute([$roleId, $name, $email, $organization, $isActive, $userId]);
    }

    public function resetPassword(int $userId, string $newPassword): bool {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        return $stmt->execute([$hash, $userId]);
    }

    public function delete(int $userId): bool {
        $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }

    public function search(string $term): array {
        $termLike = "%$term%";
        $stmt = $this->db->prepare("
            SELECT u.user_id, u.name, u.email, u.organization, u.is_active, r.role_name, r.role_id
            FROM users u
            JOIN roles r ON u.role_id = r.role_id
            WHERE u.name LIKE ? OR u.email LIKE ? OR u.organization LIKE ? OR r.role_name LIKE ?
            ORDER BY u.user_id ASC
        ");
        $stmt->execute([$termLike, $termLike, $termLike, $termLike]);
        return $stmt->fetchAll();
    }

    public function getAllRoles(): array {
        $stmt = $this->db->query("SELECT * FROM roles ORDER BY role_id ASC");
        return $stmt->fetchAll();
    }
}
