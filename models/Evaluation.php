<?php
require_once __DIR__ . '/../config/Database.php';

class Evaluation {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getByUpdateId(int $updateId): array {
        $stmt = $this->db->prepare("
            SELECT e.*, i.indicator_name, i.target_value, i.unit, u.name as evaluator_name,
                   (SELECT COUNT(*) FROM correction_requests cr WHERE cr.evaluation_id = e.evaluation_id) as correction_count
            FROM evaluations e
            JOIN indicators i ON e.indicator_id = i.indicator_id
            JOIN users u ON e.evaluator_id = u.user_id
            WHERE e.update_id = ?
            ORDER BY e.evaluation_id DESC
        ");
        $stmt->execute([$updateId]);
        return $stmt->fetchAll();
    }

    public function findById(int $evaluationId): ?array {
        $stmt = $this->db->prepare("
            SELECT e.*, i.indicator_name, i.target_value, i.unit, u.name as evaluator_name,
                   fu.activity_id, fu.user_id as submitter_id, fu.realization_value, fu.progress_percentage,
                   a.activity_name, p.project_name
            FROM evaluations e
            JOIN indicators i ON e.indicator_id = i.indicator_id
            JOIN users u ON e.evaluator_id = u.user_id
            JOIN field_updates fu ON e.update_id = fu.update_id
            JOIN activities a ON fu.activity_id = a.activity_id
            JOIN projects p ON a.project_id = p.project_id
            WHERE e.evaluation_id = ?
        ");
        $stmt->execute([$evaluationId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function evaluateResult(int $updateId, int $indicatorId, int $evaluatorId, string $evaluationDate, string $result, ?string $notes): int {
        $stmt = $this->db->prepare("
            INSERT INTO evaluations (update_id, indicator_id, evaluator_id, evaluation_date, result, notes)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$updateId, $indicatorId, $evaluatorId, $evaluationDate, $result, $notes]);
        return (int)$this->db->lastInsertId();
    }

    public function getAll(int $limit = 50, ?string $resultFilter = null): array {
        $whereClause = "";
        $params = [];

        if (!empty($resultFilter)) {
            $whereClause = "WHERE e.result = ?";
            $params[] = $resultFilter;
        }

        $sql = "
            SELECT e.*, i.indicator_name, i.target_value, i.unit, u.name as evaluator_name,
                   a.activity_name, a.activity_id, p.project_name,
                   fu.realization_value, fu.progress_percentage, fu.status as report_status, fu.evidence_file,
                   fu.director_notes, fu.director_notes_updated_at,
                   mr.mel_result_id, mr.status as mel_status,
                   ap.decision as approval_decision
            FROM evaluations e
            JOIN indicators i ON e.indicator_id = i.indicator_id
            JOIN users u ON e.evaluator_id = u.user_id
            JOIN field_updates fu ON e.update_id = fu.update_id
            JOIN activities a ON fu.activity_id = a.activity_id
            JOIN projects p ON a.project_id = p.project_id
            LEFT JOIN mel_results mr ON mr.evaluation_id = e.evaluation_id
            LEFT JOIN approvals ap ON ap.mel_result_id = mr.mel_result_id
            {$whereClause}
            ORDER BY e.evaluation_id DESC
            LIMIT ?
        ";

        $stmt = $this->db->prepare($sql);
        $paramIndex = 1;
        foreach ($params as $param) {
            $stmt->bindValue($paramIndex++, $param, PDO::PARAM_STR);
        }
        $stmt->bindValue($paramIndex, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Mengambil evaluasi yang telah disetujui oleh Project Leader (result = 'Sesuai')
     * untuk digunakan pada dropdown formulir Susun Hasil Akhir MEL.
     */
    public function getApprovedEvaluations(int $limit = 50): array {
        return $this->getAll($limit, 'Sesuai');
    }
}
