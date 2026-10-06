<?php
require_once __DIR__ . '/../config/Database.php';

class MELResult {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT mr.*, 
                   a.activity_name, p.project_name, p.project_code,
                   u.name as submitter_name,
                   ap.approval_id, ap.decision as approval_decision, ap.approval_date, ap.notes as approval_notes,
                   approver.name as approver_name
            FROM mel_results mr
            JOIN activities a ON mr.activity_id = a.activity_id
            JOIN projects p ON a.project_id = p.project_id
            JOIN users u ON mr.submitted_by = u.user_id
            LEFT JOIN approvals ap ON mr.mel_result_id = ap.mel_result_id
            LEFT JOIN users approver ON ap.approved_by = approver.user_id
            ORDER BY mr.mel_result_id DESC
        ");
        return $stmt->fetchAll();
    }

    public function findById(int $melResultId): ?array {
        $stmt = $this->db->prepare("
            SELECT mr.*, 
                   a.activity_name, a.location, p.project_name, p.project_code,
                   u.name as submitter_name,
                   e.result as eval_result, e.notes as eval_notes, e.evaluation_date,
                   i.indicator_name, i.target_value, i.unit,
                   fu.realization_value, fu.progress_percentage, fu.evidence_file,
                   ap.approval_id, ap.decision as approval_decision, ap.approval_date, ap.notes as approval_notes,
                   approver.name as approver_name
            FROM mel_results mr
            JOIN activities a ON mr.activity_id = a.activity_id
            JOIN projects p ON a.project_id = p.project_id
            JOIN users u ON mr.submitted_by = u.user_id
            LEFT JOIN evaluations e ON mr.evaluation_id = e.evaluation_id
            LEFT JOIN indicators i ON e.indicator_id = i.indicator_id
            LEFT JOIN field_updates fu ON e.update_id = fu.update_id
            LEFT JOIN approvals ap ON mr.mel_result_id = ap.mel_result_id
            LEFT JOIN users approver ON ap.approved_by = approver.user_id
            WHERE mr.mel_result_id = ?
        ");
        $stmt->execute([$melResultId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function createMELResult(int $activityId, int $evaluationId, int $submittedBy, string $submissionDate, string $resultSummary, string $status = 'Diajukan'): int {
        $stmt = $this->db->prepare("
            INSERT INTO mel_results (activity_id, evaluation_id, submitted_by, submission_date, result_summary, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$activityId, $evaluationId, $submittedBy, $submissionDate, $resultSummary, $status]);
        return (int)$this->db->lastInsertId();
    }

    public function updateStatus(int $melResultId, string $status): bool {
        $stmt = $this->db->prepare("UPDATE mel_results SET status = ? WHERE mel_result_id = ?");
        return $stmt->execute([$status, $melResultId]);
    }
}
