<?php
require_once __DIR__ . '/../config/Database.php';

class CorrectionRequest {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getByEvaluationId(int $evaluationId): array {
        $stmt = $this->db->prepare("
            SELECT cr.*, 
                   req.name as requested_by_name, 
                   assign.name as assigned_to_name
            FROM correction_requests cr
            JOIN users req ON cr.requested_by = req.user_id
            JOIN users assign ON cr.assigned_to = assign.user_id
            WHERE cr.evaluation_id = ?
            ORDER BY cr.correction_id DESC
        ");
        $stmt->execute([$evaluationId]);
        return $stmt->fetchAll();
    }

    public function findById(int $correctionId): ?array {
        $stmt = $this->db->prepare("
            SELECT cr.*, 
                   req.name as requested_by_name, 
                   assign.name as assigned_to_name,
                   e.result as eval_result, e.update_id,
                   fu.activity_id, a.activity_name, p.project_name
            FROM correction_requests cr
            JOIN users req ON cr.requested_by = req.user_id
            JOIN users assign ON cr.assigned_to = assign.user_id
            JOIN evaluations e ON cr.evaluation_id = e.evaluation_id
            JOIN field_updates fu ON e.update_id = fu.update_id
            JOIN activities a ON fu.activity_id = a.activity_id
            JOIN projects p ON a.project_id = p.project_id
            WHERE cr.correction_id = ?
        ");
        $stmt->execute([$correctionId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function createCorrection(int $evaluationId, int $requestedBy, int $assignedTo, string $requestDate, string $description, string $status = 'Diminta'): int {
        $stmt = $this->db->prepare("
            INSERT INTO correction_requests (evaluation_id, requested_by, assigned_to, request_date, description, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$evaluationId, $requestedBy, $assignedTo, $requestDate, $description, $status]);
        return (int)$this->db->lastInsertId();
    }

    public function submitResponse(int $correctionId, string $response, string $responseDate, string $status = 'Direspons'): bool {
        $stmt = $this->db->prepare("
            UPDATE correction_requests
            SET response = ?, response_date = ?, status = ?
            WHERE correction_id = ?
        ");
        return $stmt->execute([$response, $responseDate, $status, $correctionId]);
    }

    public function getForUser(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT cr.*, req.name as requested_by_name, a.activity_name, p.project_name
            FROM correction_requests cr
            JOIN users req ON cr.requested_by = req.user_id
            JOIN evaluations e ON cr.evaluation_id = e.evaluation_id
            JOIN field_updates fu ON e.update_id = fu.update_id
            JOIN activities a ON fu.activity_id = a.activity_id
            JOIN projects p ON a.project_id = p.project_id
            WHERE cr.assigned_to = ?
            ORDER BY cr.correction_id DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
