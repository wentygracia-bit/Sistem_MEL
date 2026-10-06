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

    public function create(int $activityId, string $indicatorName, float $targetValue, string $unit, ?string $description): int {
        $stmt = $this->db->prepare("
            INSERT INTO indicators (activity_id, indicator_name, target_value, unit, description)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$activityId, $indicatorName, $targetValue, $unit, $description]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $indicatorId, string $indicatorName, float $targetValue, string $unit, ?string $description): bool {
        $stmt = $this->db->prepare("
            UPDATE indicators
            SET indicator_name = ?, target_value = ?, unit = ?, description = ?
            WHERE indicator_id = ?
        ");
        return $stmt->execute([$indicatorName, $targetValue, $unit, $description, $indicatorId]);
    }

    public function delete(int $indicatorId): bool {
        $stmt = $this->db->prepare("DELETE FROM indicators WHERE indicator_id = ?");
        return $stmt->execute([$indicatorId]);
    }
}
