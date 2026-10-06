<?php
require_once __DIR__ . '/../config/Database.php';

class ActivityTimeline {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByActivityId(int $activityId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM activity_timelines WHERE activity_id = ?");
        $stmt->execute([$activityId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function save(int $activityId, string $startDate, string $endDate, ?string $reminderDate, string $status = 'Dalam Jadwal'): bool {
        $existing = $this->findByActivityId($activityId);
        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE activity_timelines
                SET start_date = ?, end_date = ?, reminder_date = ?, status = ?
                WHERE activity_id = ?
            ");
            return $stmt->execute([$startDate, $endDate, $reminderDate, $status, $activityId]);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO activity_timelines (activity_id, start_date, end_date, reminder_date, status)
                VALUES (?, ?, ?, ?, ?)
            ");
            return $stmt->execute([$activityId, $startDate, $endDate, $reminderDate, $status]);
        }
    }

    /**
     * Automatic Reminder Calculation (merujuk alur PRD 5.2):
     * Memeriksa timeline yang sudah melewati atau mencapai reminder_date dan belum selesai
     */
    public function calculateReminder(): array {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("
            SELECT t.*, a.activity_name, a.project_id, p.project_name
            FROM activity_timelines t
            JOIN activities a ON t.activity_id = a.activity_id
            JOIN projects p ON a.project_id = p.project_id
            WHERE t.reminder_date IS NOT NULL 
              AND t.reminder_date <= ? 
              AND t.status != 'Selesai'
        ");
        $stmt->execute([$today]);
        return $stmt->fetchAll();
    }
}
