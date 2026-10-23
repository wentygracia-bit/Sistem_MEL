<?php
require_once __DIR__ . '/../config/Database.php';

class Indicator {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getByActivityId(int $activityId): array {
        $stmt = $this->db->prepare("
            SELECT i.*,
                   (SELECT SUM(fu.realization_value) FROM field_updates fu WHERE fu.activity_id = i.activity_id) as current_realization
            FROM indicators i
            WHERE i.activity_id = ?
            ORDER BY i.indicator_id ASC
        ");
        $stmt->execute([$activityId]);
        return $stmt->fetchAll();
    }

    public function findById(int $indicatorId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM indicators WHERE indicator_id = ?");
        $stmt->execute([$indicatorId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(int $activityId, string $indicatorName, ?float $targetValue, ?string $unit, ?string $description, string $activityType = 'Kuantitatif', ?string $targetDescription = null): int {
        $stmt = $this->db->prepare("
            INSERT INTO indicators (activity_id, activity_type, indicator_name, target_value, unit, description, target_description)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$activityId, $activityType, $indicatorName, $targetValue, $unit, $description, $targetDescription]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $indicatorId, string $indicatorName, ?float $targetValue, ?string $unit, ?string $description, string $activityType = 'Kuantitatif', ?string $targetDescription = null): bool {
        $stmt = $this->db->prepare("
            UPDATE indicators
            SET indicator_name = ?, target_value = ?, unit = ?, description = ?, activity_type = ?, target_description = ?
            WHERE indicator_id = ?
        ");
        return $stmt->execute([$indicatorName, $targetValue, $unit, $description, $activityType, $targetDescription, $indicatorId]);
    }

    public function delete(int $indicatorId): bool {
        $stmt = $this->db->prepare("DELETE FROM indicators WHERE indicator_id = ?");
        return $stmt->execute([$indicatorId]);
    }
}
