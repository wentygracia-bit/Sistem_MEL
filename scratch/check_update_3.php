<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';

$db = Database::getConnection();
$stmt = $db->query('
    SELECT fu.update_id, fu.activity_id, a.activity_name, i.indicator_name, i.target_value, i.unit,
           fu.realization_value, fu.progress_percentage, fu.description
    FROM field_updates fu
    JOIN activities a ON fu.activity_id = a.activity_id
    LEFT JOIN indicators i ON a.activity_id = i.activity_id
    WHERE fu.update_id = 3
');
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($r);
}
