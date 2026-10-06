<?php
require_once __DIR__ . '/../config/Database.php';

class Approval {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getByMelResultId(int $melResultId): ?array {
        $stmt = $this->db->prepare("
            SELECT ap.*, u.name as approver_name 
            FROM approvals ap
            JOIN users u ON ap.approved_by = u.user_id
            WHERE ap.mel_result_id = ?
        ");
        $stmt->execute([$melResultId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function approveResult(int $melResultId, int $approvedBy, string $approvalDate, ?string $notes = ''): int {
        // Upsert approval record
        $existing = $this->getByMelResultId($melResultId);
        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE approvals
                SET approved_by = ?, approval_date = ?, decision = 'Disetujui', notes = ?
                WHERE mel_result_id = ?
            ");
            $stmt->execute([$approvedBy, $approvalDate, $notes, $melResultId]);
            $id = $existing['approval_id'];
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO approvals (mel_result_id, approved_by, approval_date, decision, notes)
                VALUES (?, ?, ?, 'Disetujui', ?)
            ");
            $stmt->execute([$melResultId, $approvedBy, $approvalDate, $notes]);
            $id = (int)$this->db->lastInsertId();
        }

        // Update mel_results status
        $stmt2 = $this->db->prepare("UPDATE mel_results SET status = 'Disetujui' WHERE mel_result_id = ?");
        $stmt2->execute([$melResultId]);

        return $id;
    }

    public function rejectResult(int $melResultId, int $approvedBy, string $approvalDate, ?string $notes = ''): int {
        $existing = $this->getByMelResultId($melResultId);
        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE approvals
                SET approved_by = ?, approval_date = ?, decision = 'Ditolak', notes = ?
                WHERE mel_result_id = ?
            ");
            $stmt->execute([$approvedBy, $approvalDate, $notes, $melResultId]);
            $id = $existing['approval_id'];
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO approvals (mel_result_id, approved_by, approval_date, decision, notes)
                VALUES (?, ?, ?, 'Ditolak', ?)
            ");
            $stmt->execute([$melResultId, $approvedBy, $approvalDate, $notes]);
            $id = (int)$this->db->lastInsertId();
        }

        // Update mel_results status
        $stmt2 = $this->db->prepare("UPDATE mel_results SET status = 'Ditolak' WHERE mel_result_id = ?");
        $stmt2->execute([$melResultId]);

        return $id;
    }
}
