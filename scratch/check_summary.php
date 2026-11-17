<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';

$db = Database::getConnection();
$stmt = $db->query('SELECT mel_result_id, result_summary, status FROM mel_results');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['mel_result_id'] . " | Status: " . $row['status'] . "\n";
    echo $row['result_summary'] . "\n---\n";
}
