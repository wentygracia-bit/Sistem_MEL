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
     * Automatic Reminder Calculation:
     * Memeriksa kegiatan yang mendekati tanggal mulai:
     * - 10 hari sebelum kegiatan (H-10)
     * - 3 hari sebelum kegiatan (H-3)
     * @return array Daftar reminder jatuh tempo dengan penanda reminder_phase ('H-10' atau 'H-3')
     */
    public function calculateReminder(): array {
        $today = date('Y-m-d');
        // Tanggal start_date untuk kegiatan yang jatuh tempo hari ini:
        // H-10 -> start_date = today + 10 days
        $targetH10 = date('Y-m-d', strtotime('+10 days'));
        // H-3  -> start_date = today + 3 days
        $targetH3 = date('Y-m-d', strtotime('+3 days'));

        $stmt = $this->db->prepare("
            SELECT t.*, a.activity_name, a.location, a.project_id, p.project_name, p.project_code,
                   CASE 
                       WHEN t.start_date = ? THEN 'H-10'
                       WHEN t.start_date = ? THEN 'H-3'
                       ELSE 'Jadwal'
                   END as reminder_phase
            FROM activity_timelines t
            JOIN activities a ON t.activity_id = a.activity_id
            JOIN projects p ON a.project_id = p.project_id
            WHERE t.status != 'Selesai'
              AND (
                  t.start_date = ? 
                  OR t.start_date = ?
                  OR (t.reminder_date IS NOT NULL AND t.reminder_date <= ?)
              )
        ");
        $stmt->execute([$targetH10, $targetH3, $targetH10, $targetH3, $today]);
        return $stmt->fetchAll();
    }
}
