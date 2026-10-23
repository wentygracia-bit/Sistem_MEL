<?php
require_once __DIR__ . '/../config/Database.php';

class Project {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(bool $onlyPublic = false, ?string $search = null, ?string $statusFilter = null): array {
        $where = [];
        $params = [];

        if ($onlyPublic) {
            $where[] = "p.public_status = 'Dipublikasikan'";
        }

        if (!empty($search)) {
            $where[] = "(p.project_name LIKE ? OR p.project_code LIKE ?)";
            $term = '%' . trim($search) . '%';
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($statusFilter)) {
            $where[] = "p.status = ?";
            $params[] = $statusFilter;
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        $sql = "
            SELECT p.*, u.name as creator_name,
                   (SELECT COUNT(*) FROM activities a WHERE a.project_id = p.project_id) as total_activities,
                   (SELECT COUNT(*) FROM project_members pm WHERE pm.project_id = p.project_id) as total_members
            FROM projects p
            LEFT JOIN users u ON p.created_by = u.user_id
            {$whereClause}
            ORDER BY p.project_id DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $projectId): ?array {
        $stmt = $this->db->prepare("
            SELECT p.*, u.name as creator_name 
            FROM projects p
            LEFT JOIN users u ON p.created_by = u.user_id
            WHERE p.project_id = ?
        ");
        $stmt->execute([$projectId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function generateNextProjectCode(?int $year = null): string {
        $targetYear = $year ?? (int)date('Y');
        $prefix = "PRJ-YNKI-{$targetYear}-";

        // Cari semua project_code dengan prefix tahun yang bersangkutan
        $stmt = $this->db->prepare("
            SELECT project_code 
            FROM projects 
            WHERE project_code LIKE ?
        ");
        $stmt->execute([$prefix . '%']);
        $codes = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $maxNumber = 0;
        foreach ($codes as $code) {
            // Ambil bagian nomor setelah prefix PRJ-YNKI-YYYY-
            $suffix = substr($code, strlen($prefix));
            if (is_numeric($suffix)) {
                $num = (int)$suffix;
                if ($num > $maxNumber) {
                    $maxNumber = $num;
                }
            }
        }

        $nextNumber = $maxNumber + 1;
        // Format minimal 3 digit dengan leading zero, jika >= 1000 tetap utuh
        $formattedNumber = str_pad((string)$nextNumber, 3, '0', STR_PAD_LEFT);

        return $prefix . $formattedNumber;
    }

    public function codeExists(string $projectCode): bool {
        $stmt = $this->db->prepare("SELECT 1 FROM projects WHERE project_code = ? LIMIT 1");
        $stmt->execute([$projectCode]);
        return (bool)$stmt->fetchColumn();
    }

    public function create(string $projectCode, string $projectName, string $description, ?string $startDate, ?string $endDate, string $status, string $publicStatus, int $createdBy): int {
        $stmt = $this->db->prepare("
            INSERT INTO projects (project_code, project_name, description, start_date, end_date, status, public_status, created_by, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$projectCode, $projectName, $description, $startDate, $endDate, $status, $publicStatus, $createdBy]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $projectId, string $projectCode, string $projectName, string $description, ?string $startDate, ?string $endDate, string $status, string $publicStatus): bool {
        $stmt = $this->db->prepare("
            UPDATE projects 
            SET project_code = ?, project_name = ?, description = ?, start_date = ?, end_date = ?, status = ?, public_status = ?
            WHERE project_id = ?
        ");
        return $stmt->execute([$projectCode, $projectName, $description, $startDate, $endDate, $status, $publicStatus, $projectId]);
    }

    public function getMembers(int $projectId): array {
        $stmt = $this->db->prepare("
            SELECT pm.*, u.name, u.email, u.organization, r.role_name
            FROM project_members pm
            JOIN users u ON pm.user_id = u.user_id
            JOIN roles r ON u.role_id = r.role_id
            WHERE pm.project_id = ?
            ORDER BY pm.project_member_id ASC
        ");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }

    public function addMember(int $projectId, int $userId, string $memberRole): bool {
        $stmt = $this->db->prepare("
            INSERT INTO project_members (project_id, user_id, member_role)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE member_role = VALUES(member_role)
        ");
        return $stmt->execute([$projectId, $userId, $memberRole]);
    }

    public function removeMember(int $projectMemberId): bool {
        $stmt = $this->db->prepare("DELETE FROM project_members WHERE project_member_id = ?");
        return $stmt->execute([$projectMemberId]);
    }

    public function getProjectsForUser(int $userId, int $roleId, ?string $search = null, ?string $statusFilter = null): array {
        // Direktur & Staf MEL can view all projects
        if ($roleId === 4 || $roleId === 2) {
            return $this->getAll(false, $search, $statusFilter);
        }

        // Others view projects where they are creator or assigned member
        $where = ["(p.created_by = ? OR pm.user_id = ?)"];
        $params = [$userId, $userId];

        if (!empty($search)) {
            $where[] = "(p.project_name LIKE ? OR p.project_code LIKE ?)";
            $term = '%' . trim($search) . '%';
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($statusFilter)) {
            $where[] = "p.status = ?";
            $params[] = $statusFilter;
        }

        $whereClause = "WHERE " . implode(" AND ", $where);

        $stmt = $this->db->prepare("
            SELECT DISTINCT p.*, u.name as creator_name,
                   (SELECT COUNT(*) FROM activities a WHERE a.project_id = p.project_id) as total_activities
            FROM projects p
            LEFT JOIN users u ON p.created_by = u.user_id
            LEFT JOIN project_members pm ON p.project_id = pm.project_id
            {$whereClause}
            ORDER BY p.project_id DESC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countActiveProjects(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM projects WHERE status = 'Aktif'");
        return (int)$stmt->fetchColumn();
    }
}

