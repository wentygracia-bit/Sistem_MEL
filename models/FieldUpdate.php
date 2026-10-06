<?php
require_once __DIR__ . '/../config/Database.php';

class FieldUpdate {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getByActivityId(int $activityId): array {
        $stmt = $this->db->prepare("
            SELECT fu.*, u.name as reporter_name,
                   (SELECT COUNT(*) FROM evaluations e WHERE e.update_id = fu.update_id) as evaluation_count
            FROM field_updates fu
            JOIN users u ON fu.user_id = u.user_id
            WHERE fu.activity_id = ?
            ORDER BY fu.update_id DESC
        ");
        $stmt->execute([$activityId]);
        return $stmt->fetchAll();
    }

    public function findById(int $updateId): ?array {
        $stmt = $this->db->prepare("
            SELECT fu.*, u.name as reporter_name, a.activity_name, a.project_id, p.project_name
            FROM field_updates fu
            JOIN users u ON fu.user_id = u.user_id
            JOIN activities a ON fu.activity_id = a.activity_id
            JOIN projects p ON a.project_id = p.project_id
            WHERE fu.update_id = ?
        ");
        $stmt->execute([$updateId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function getAll(int $limit = 50): array {
        $stmt = $this->db->prepare("
            SELECT fu.*, u.name as reporter_name, a.activity_name, p.project_name
            FROM field_updates fu
            JOIN users u ON fu.user_id = u.user_id
            JOIN activities a ON fu.activity_id = a.activity_id
            JOIN projects p ON a.project_id = p.project_id
            ORDER BY fu.update_id DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function submitUpdate(int $activityId, int $userId, string $updateDate, ?string $description, ?float $realizationValue, ?float $progressPercentage, ?string $evidenceFile, string $status = 'Diajukan'): int {
        $stmt = $this->db->prepare("
            INSERT INTO field_updates (activity_id, user_id, update_date, description, realization_value, progress_percentage, evidence_file, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$activityId, $userId, $updateDate, $description, $realizationValue, $progressPercentage, $evidenceFile, $status]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $updateId, string $updateDate, ?string $description, ?float $realizationValue, ?float $progressPercentage, ?string $evidenceFile, string $status = 'Diajukan'): bool {
        if ($evidenceFile !== null) {
            $stmt = $this->db->prepare("
                UPDATE field_updates
                SET update_date = ?, description = ?, realization_value = ?, progress_percentage = ?, evidence_file = ?, status = ?
                WHERE update_id = ?
            ");
            return $stmt->execute([$updateDate, $description, $realizationValue, $progressPercentage, $evidenceFile, $status, $updateId]);
        } else {
            $stmt = $this->db->prepare("
                UPDATE field_updates
                SET update_date = ?, description = ?, realization_value = ?, progress_percentage = ?, status = ?
                WHERE update_id = ?
            ");
            return $stmt->execute([$updateDate, $description, $realizationValue, $progressPercentage, $status, $updateId]);
        }
    }

    public function updateStatus(int $updateId, string $status): bool {
        $stmt = $this->db->prepare("UPDATE field_updates SET status = ? WHERE update_id = ?");
        return $stmt->execute([$status, $updateId]);
    }
}
