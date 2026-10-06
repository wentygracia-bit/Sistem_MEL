<?php
require_once __DIR__ . '/../config/Database.php';

class Activity {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getByProjectId(int $projectId): array {
        $stmt = $this->db->prepare("
            SELECT a.*, 
                   t.start_date, t.end_date, t.reminder_date, t.status as timeline_status,
                   (SELECT COUNT(*) FROM indicators i WHERE i.activity_id = a.activity_id) as total_indicators,
                   (SELECT COUNT(*) FROM field_updates fu WHERE fu.activity_id = a.activity_id) as total_updates
            FROM activities a
            LEFT JOIN activity_timelines t ON a.activity_id = t.activity_id
            WHERE a.project_id = ?
            ORDER BY a.activity_id ASC
        ");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }

    public function findById(int $activityId): ?array {
        $stmt = $this->db->prepare("
            SELECT a.*, p.project_name, p.project_code,
                   t.start_date, t.end_date, t.reminder_date, t.status as timeline_status
            FROM activities a
            JOIN projects p ON a.project_id = p.project_id
            LEFT JOIN activity_timelines t ON a.activity_id = t.activity_id
            WHERE a.activity_id = ?
        ");
        $stmt->execute([$activityId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(int $projectId, string $activityName, ?string $description, ?string $location, string $status = 'Perencanaan'): int {
        $stmt = $this->db->prepare("
            INSERT INTO activities (project_id, activity_name, description, location, status, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$projectId, $activityName, $description, $location, $status]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $activityId, string $activityName, ?string $description, ?string $location, string $status): bool {
        $stmt = $this->db->prepare("
            UPDATE activities 
            SET activity_name = ?, description = ?, location = ?, status = ?
            WHERE activity_id = ?
        ");
        return $stmt->execute([$activityName, $description, $location, $status, $activityId]);
    }

    public function getAllWithSummary(): array {
        $sql = "
            SELECT a.*, p.project_name, p.project_code,
                   t.start_date, t.end_date, t.reminder_date, t.status as timeline_status
            FROM activities a
            JOIN projects p ON a.project_id = p.project_id
            LEFT JOIN activity_timelines t ON a.activity_id = t.activity_id
            ORDER BY a.activity_id DESC
        ";
        return $this->db->query($sql)->fetchAll();
    }
}
