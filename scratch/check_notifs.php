<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';

$db = Database::getConnection();
$stmt = $db->query("SELECT notification_id, user_id, activity_id, notification_type, message, sent_at FROM notifications ORDER BY notification_id DESC LIMIT 10");
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $r['notification_id'] . " | Type: " . $r['notification_type'] . " | User: " . $r['user_id'] . "\n";
    echo "Msg: " . $r['message'] . "\n---\n";
}
