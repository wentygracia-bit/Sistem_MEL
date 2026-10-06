<?php
require_once __DIR__ . '/../config/Database.php';

class Notification {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
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
            SELECT n.*, a.activity_name
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
