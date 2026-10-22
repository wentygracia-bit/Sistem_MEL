<?php
require_once __DIR__ . '/../config/Database.php';

class FieldUpdate {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getByActivityId(int $activityId): array {
        $stmt = $this->db->prepare("
            SELECT fu.*, u.name as reporter_name,
                   (SELECT COUNT(*) FROM evaluations e WHERE e.update_id = fu.update_id) as evaluation_count
            FROM field_updates fu
            JOIN users u ON fu.user_id = u.user_id
            WHERE fu.activity_id = ?
            ORDER BY fu.update_id DESC
        ");
        $stmt->execute([$activityId]);
        return $stmt->fetchAll();
    }

    public function findById(int $updateId): ?array {
        $stmt = $this->db->prepare("
            SELECT fu.*, u.name as reporter_name, a.activity_name, a.project_id, p.project_name
            FROM field_updates fu
            JOIN users u ON fu.user_id = u.user_id
            JOIN activities a ON fu.activity_id = a.activity_id
            JOIN projects p ON a.project_id = p.project_id
            WHERE fu.update_id = ?
        ");
        $stmt->execute([$updateId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function getAll(int $limit = 50): array {
        $stmt = $this->db->prepare("
            SELECT fu.*, u.name as reporter_name, a.activity_name, p.project_name
            FROM field_updates fu
            JOIN users u ON fu.user_id = u.user_id
            JOIN activities a ON fu.activity_id = a.activity_id
            JOIN projects p ON a.project_id = p.project_id
            ORDER BY fu.update_id DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function submitUpdate(int $activityId, int $userId, string $updateDate, ?string $description, ?float $realizationValue, ?float $progressPercentage, ?string $evidenceFile, string $status = 'Diajukan'): int {
        $stmt = $this->db->prepare("
            INSERT INTO field_updates (activity_id, user_id, update_date, description, realization_value, progress_percentage, evidence_file, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$activityId, $userId, $updateDate, $description, $realizationValue, $progressPercentage, $evidenceFile, $status]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $updateId, string $updateDate, ?string $description, ?float $realizationValue, ?float $progressPercentage, ?string $evidenceFile, string $status = 'Diajukan'): bool {
        if ($evidenceFile !== null) {
            $stmt = $this->db->prepare("
                UPDATE field_updates
                SET update_date = ?, description = ?, realization_value = ?, progress_percentage = ?, evidence_file = ?, status = ?
                WHERE update_id = ?
            ");
            return $stmt->execute([$updateDate, $description, $realizationValue, $progressPercentage, $evidenceFile, $status, $updateId]);
        } else {
            $stmt = $this->db->prepare("
                UPDATE field_updates
                SET update_date = ?, description = ?, realization_value = ?, progress_percentage = ?, status = ?
                WHERE update_id = ?
            ");
            return $stmt->execute([$updateDate, $description, $realizationValue, $progressPercentage, $status, $updateId]);
        }
    }

    public function search(string $keyword, int $limit = 50): array {
        $term = '%' . trim($keyword) . '%';
        $stmt = $this->db->prepare("
            SELECT fu.*, u.name as reporter_name, a.activity_name, p.project_name
            FROM field_updates fu
            JOIN users u ON fu.user_id = u.user_id
            JOIN activities a ON fu.activity_id = a.activity_id
            JOIN projects p ON a.project_id = p.project_id
            WHERE a.activity_name LIKE ?
               OR p.project_name LIKE ?
               OR u.name LIKE ?
               OR fu.description LIKE ?
               OR fu.status LIKE ?
            ORDER BY fu.update_id DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $term, PDO::PARAM_STR);
        $stmt->bindValue(2, $term, PDO::PARAM_STR);
        $stmt->bindValue(3, $term, PDO::PARAM_STR);
        $stmt->bindValue(4, $term, PDO::PARAM_STR);
        $stmt->bindValue(5, $term, PDO::PARAM_STR);
        $stmt->bindValue(6, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateFull(int $updateId, int $activityId, string $updateDate, ?string $description, ?float $realizationValue, ?float $progressPercentage, ?string $evidenceFile, ?string $status = null): bool {
        if ($evidenceFile !== null) {
            if ($status !== null) {
                $stmt = $this->db->prepare("
                    UPDATE field_updates
                    SET activity_id = ?, update_date = ?, description = ?, realization_value = ?, progress_percentage = ?, evidence_file = ?, status = ?
                    WHERE update_id = ?
                ");
                return $stmt->execute([$activityId, $updateDate, $description, $realizationValue, $progressPercentage, $evidenceFile, $status, $updateId]);
            } else {
                $stmt = $this->db->prepare("
                    UPDATE field_updates
                    SET activity_id = ?, update_date = ?, description = ?, realization_value = ?, progress_percentage = ?, evidence_file = ?
                    WHERE update_id = ?
                ");
                return $stmt->execute([$activityId, $updateDate, $description, $realizationValue, $progressPercentage, $evidenceFile, $updateId]);
            }
        } else {
            if ($status !== null) {
                $stmt = $this->db->prepare("
                    UPDATE field_updates
                    SET activity_id = ?, update_date = ?, description = ?, realization_value = ?, progress_percentage = ?, status = ?
                    WHERE update_id = ?
                ");
                return $stmt->execute([$activityId, $updateDate, $description, $realizationValue, $progressPercentage, $status, $updateId]);
            } else {
                $stmt = $this->db->prepare("
                    UPDATE field_updates
                    SET activity_id = ?, update_date = ?, description = ?, realization_value = ?, progress_percentage = ?
                    WHERE update_id = ?
                ");
                return $stmt->execute([$activityId, $updateDate, $description, $realizationValue, $progressPercentage, $updateId]);
            }
        }
    }

    public function updateStatus(int $updateId, string $status): bool {
        $stmt = $this->db->prepare("UPDATE field_updates SET status = ? WHERE update_id = ?");
        return $stmt->execute([$status, $updateId]);
    }

    public function delete(int $updateId): bool {
        $stmt = $this->db->prepare("DELETE FROM field_updates WHERE update_id = ?");
        return $stmt->execute([$updateId]);
    }

    public function getAllForUser(int $userId, int $roleId, int $limit = 100, ?string $search = null, ?string $statusFilter = null, ?int $activityIdFilter = null): array {
        $params = [];
        $where = [];

        if ($roleId === 1) { // Staf lapangan: hanya laporan yang dibuatnya
            $where[] = "fu.user_id = ?";
            $params[] = $userId;
        }

        if (!empty($search)) {
            $where[] = "(a.activity_name LIKE ? OR p.project_name LIKE ? OR u.name LIKE ? OR fu.description LIKE ?)";
            $term = '%' . trim($search) . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($statusFilter)) {
            $where[] = "fu.status = ?";
            $params[] = $statusFilter;
        }

        if (!empty($activityIdFilter)) {
            $where[] = "fu.activity_id = ?";
            $params[] = $activityIdFilter;
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        $sql = "
            SELECT fu.*, u.name as reporter_name, a.activity_name, p.project_name, p.project_code,
                   (SELECT COUNT(*) FROM evaluations e WHERE e.update_id = fu.update_id) as evaluation_count,
                   (SELECT e.notes FROM evaluations e WHERE e.update_id = fu.update_id ORDER BY e.evaluation_id DESC LIMIT 1) as latest_eval_notes
            FROM field_updates fu
            JOIN users u ON fu.user_id = u.user_id
            JOIN activities a ON fu.activity_id = a.activity_id
            JOIN projects p ON a.project_id = p.project_id
            {$whereClause}
            ORDER BY fu.update_id DESC
            LIMIT ?
        ";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $idx => $val) {
            $stmt->bindValue($idx + 1, $val);
        }
        $stmt->bindValue(count($params) + 1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getStatsForUser(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_reports,
                SUM(CASE WHEN status = 'Draft' THEN 1 ELSE 0 END) as draft_reports,
                SUM(CASE WHEN status = 'Diajukan' OR status = 'Menunggu Verifikasi' THEN 1 ELSE 0 END) as pending_reports,
                SUM(CASE WHEN status = 'Perlu Perbaikan' OR status = 'Perlu Direvisi' THEN 1 ELSE 0 END) as revision_needed_reports,
                SUM(CASE WHEN status = 'Dievaluasi' OR status = 'Disetujui' OR status = 'Terverifikasi' THEN 1 ELSE 0 END) as approved_reports
            FROM field_updates
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $res = $stmt->fetch();
        return [
            'total'           => (int)($res['total_reports'] ?? 0),
            'draft'           => (int)($res['draft_reports'] ?? 0),
            'pending'         => (int)($res['pending_reports'] ?? 0),
            'revision_needed' => (int)($res['revision_needed_reports'] ?? 0),
            'approved'        => (int)($res['approved_reports'] ?? 0),
        ];
    }

    public function countAll(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM field_updates");
        return (int)$stmt->fetchColumn();
    }
}

