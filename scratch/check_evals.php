<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';

$db = Database::getConnection();
$stmt = $db->query('
    SELECT e.evaluation_id, e.result, mr.mel_result_id, mr.status as mel_status, a.activity_name
    FROM evaluations e
    JOIN field_updates fu ON e.update_id = fu.update_id
    JOIN activities a ON fu.activity_id = a.activity_id
    LEFT JOIN mel_results mr ON mr.evaluation_id = e.evaluation_id
');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "Eval #" . $row['evaluation_id'] . " (" . $row['result'] . ") | Activity: " . $row['activity_name'] . " | MEL #" . ($row['mel_result_id'] ?? 'none') . " (" . ($row['mel_status'] ?? '-') . ")\n";
}

