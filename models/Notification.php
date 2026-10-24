<?php
require_once __DIR__ . '/../config/Database.php';

class Notification {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findById(int $notificationId, int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT n.*, a.activity_name
            FROM notifications n
            LEFT JOIN activities a ON n.activity_id = a.activity_id
            WHERE n.notification_id = ? AND n.user_id = ?
            LIMIT 1
        ");
        $stmt->execute([$notificationId, $userId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function reminderExists(int $userId, int $activityId, string $phase): bool {
        $stmt = $this->db->prepare("
            SELECT 1 FROM notifications 
            WHERE user_id = ? 
              AND activity_id = ? 
              AND notification_type = 'Reminder'
              AND message LIKE ?
            LIMIT 1
        ");
        $stmt->execute([$userId, $activityId, "%{$phase}%"]);
        return (bool)$stmt->fetchColumn();
    }

    public function sendNotification(int $userId, ?int $activityId, string $notificationType, string $message): int {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (user_id, activity_id, notification_type, message, sent_at, is_read)
            VALUES (?, ?, ?, ?, NOW(), 0)
        ");
        $stmt->execute([$userId, $activityId, $notificationType, $message]);
        return (int)$this->db->lastInsertId();
    }

    public function getForUser(int $userId, int $limit = 20): array {
        $stmt = $this->db->prepare("
            SELECT n.*, a.activity_name,
                   (
                       SELECT mr.mel_result_id 
                       FROM mel_results mr 
                       WHERE mr.activity_id = n.activity_id 
                       ORDER BY mr.mel_result_id DESC 
                       LIMIT 1
                   ) AS mel_result_id,
                   (
                       SELECT cr.correction_id 
                       FROM correction_requests cr 
                       JOIN evaluations e ON cr.evaluation_id = e.evaluation_id
                       JOIN field_updates fu ON e.update_id = fu.update_id
                       WHERE fu.activity_id = n.activity_id 
                       ORDER BY cr.correction_id DESC 
                       LIMIT 1
                   ) AS correction_id,
                   (
                       SELECT fu.update_id 
                       FROM field_updates fu 
                       WHERE fu.activity_id = n.activity_id 
                       ORDER BY fu.update_id DESC 
                       LIMIT 1
                   ) AS update_id
            FROM notifications n
            LEFT JOIN activities a ON n.activity_id = a.activity_id
            WHERE n.user_id = ?
            ORDER BY n.sent_at DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUnreadCount(int $userId): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0
        ");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    public function markAsRead(int $notificationId, int $userId): bool {
        $stmt = $this->db->prepare("
            UPDATE notifications SET is_read = 1 WHERE notification_id = ? AND user_id = ?
        ");
        return $stmt->execute([$notificationId, $userId]);
    }

    public function markAllAsRead(int $userId): bool {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
}
