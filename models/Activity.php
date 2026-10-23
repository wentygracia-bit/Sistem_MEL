<?php
require_once __DIR__ . '/../config/Database.php';

class Activity {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getByProjectId(int $projectId, ?string $search = null, ?string $statusFilter = null): array {
        $where = ["a.project_id = ?"];
        $params = [$projectId];

        if (!empty($search)) {
            $where[] = "a.activity_name LIKE ?";
            $params[] = '%' . trim($search) . '%';
        }

        if (!empty($statusFilter)) {
            $where[] = "a.status = ?";
            $params[] = $statusFilter;
        }

        $whereClause = "WHERE " . implode(" AND ", $where);

        $stmt = $this->db->prepare("
            SELECT a.*, 
                   t.start_date, t.end_date, t.reminder_date, t.status as timeline_status,
                   (SELECT COUNT(*) FROM indicators i WHERE i.activity_id = a.activity_id) as total_indicators,
                   (SELECT COUNT(*) FROM field_updates fu WHERE fu.activity_id = a.activity_id) as total_updates
            FROM activities a
            LEFT JOIN activity_timelines t ON a.activity_id = t.activity_id
            {$whereClause}
            ORDER BY a.activity_id ASC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $activityId): ?array {
        $stmt = $this->db->prepare("
            SELECT a.*, p.project_name, p.project_code,
                   t.start_date, t.end_date, t.reminder_date, t.status as timeline_status,
                   COALESCE((SELECT SUM(target_value) FROM indicators WHERE activity_id = a.activity_id), 0) as total_target,
                   COALESCE((SELECT unit FROM indicators WHERE activity_id = a.activity_id LIMIT 1), 'Satuan') as unit
            FROM activities a
            JOIN projects p ON a.project_id = p.project_id
            LEFT JOIN activity_timelines t ON a.activity_id = t.activity_id
            WHERE a.activity_id = ?
        ");
        $stmt->execute([$activityId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(int $projectId, string $activityName, ?string $description, ?string $location, string $status = 'Perencanaan'): int {
        $stmt = $this->db->prepare("
            INSERT INTO activities (project_id, activity_name, description, location, status, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$projectId, $activityName, $description, $location, $status]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $activityId, string $activityName, ?string $description, ?string $location, string $status): bool {
        $stmt = $this->db->prepare("
            UPDATE activities 
            SET activity_name = ?, description = ?, location = ?, status = ?
            WHERE activity_id = ?
        ");
        return $stmt->execute([$activityName, $description, $location, $status, $activityId]);
    }

    public function getAllWithSummary(): array {
        $sql = "
            SELECT a.*, p.project_name, p.project_code,
                   t.start_date, t.end_date, t.reminder_date, t.status as timeline_status
            FROM activities a
            JOIN projects p ON a.project_id = p.project_id
            LEFT JOIN activity_timelines t ON a.activity_id = t.activity_id
            ORDER BY a.activity_id DESC
        ";
        return $this->db->query($sql)->fetchAll();
    }

    public function getStatusCounts(): array {
        $stmt = $this->db->query("
            SELECT 
                SUM(CASE WHEN status = 'Berjalan' THEN 1 ELSE 0 END) as berjalan,
                SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) as selesai,
                SUM(CASE WHEN status = 'Perencanaan' THEN 1 ELSE 0 END) as perencanaan,
                COUNT(*) as total
            FROM activities
        ");
        $res = $stmt->fetch();
        return [
            'Berjalan'    => (int)($res['berjalan'] ?? 0),
            'Selesai'     => (int)($res['selesai'] ?? 0),
            'Perencanaan' => (int)($res['perencanaan'] ?? 0),
            'Total'       => (int)($res['total'] ?? 0),
        ];
    }

    public function findOrCreateByName(string $activityName, ?int $projectId = null): int {
        $trimmedName = trim($activityName);
        if (empty($trimmedName)) {
            return 0;
        }

        // Search for existing activity by name (case-insensitive)
        $stmt = $this->db->prepare("SELECT activity_id FROM activities WHERE LOWER(TRIM(activity_name)) = LOWER(?) LIMIT 1");
        $stmt->execute([$trimmedName]);
        $existingId = $stmt->fetchColumn();

        if ($existingId) {
            return (int)$existingId;
        }

        // If not found, create new activity under specified project or first project
        if (!$projectId || $projectId <= 0) {
            $projStmt = $this->db->query("SELECT project_id FROM projects ORDER BY project_id ASC LIMIT 1");
            $projectId = (int)$projStmt->fetchColumn();
            if (!$projectId) {
                $projectId = 1;
            }
        }

        return $this->create($projectId, $trimmedName, 'Kegiatan dibuat melalui input pelaporan lapangan', 'Lapangan', 'Berjalan');
    }
    public function getActivitiesForUser(int $userId, int $roleId, ?string $search = null, ?string $statusFilter = null, ?int $projectIdFilter = null): array {
        $params = [];
        $where = [];

        // Staf Lapangan (role 1) views activities of projects they belong to
        if ($roleId === 1) {
            $where[] = "a.project_id IN (SELECT project_id FROM project_members WHERE user_id = ?)";
            $params[] = $userId;
        }

        if (!empty($search)) {
            $where[] = "(a.activity_name LIKE ? OR p.project_name LIKE ? OR a.location LIKE ?)";
            $searchTerm = '%' . trim($search) . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($statusFilter)) {
            $where[] = "a.status = ?";
            $params[] = $statusFilter;
        }

        if (!empty($projectIdFilter)) {
            $where[] = "a.project_id = ?";
            $params[] = $projectIdFilter;
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        $sql = "
            SELECT a.*, p.project_name, p.project_code,
                   t.start_date, t.end_date, t.reminder_date, t.status as timeline_status,
                   -- Target value dari indikator pertama atau sum
                   COALESCE((SELECT SUM(target_value) FROM indicators WHERE activity_id = a.activity_id), 0) as total_target,
                   COALESCE((SELECT unit FROM indicators WHERE activity_id = a.activity_id LIMIT 1), 'Satuan') as unit,
                   -- Realisasi terverifikasi (dari field_updates yang statusnya 'Dievaluasi')
                   COALESCE((
                       SELECT SUM(fu.realization_value) 
                       FROM field_updates fu 
                       WHERE fu.activity_id = a.activity_id 
                         AND (fu.status = 'Dievaluasi' OR fu.status = 'Disetujui' OR fu.status = 'Terverifikasi')
                   ), 0) as verified_realization,
                   -- Total realisasi termasuk yang belum diverifikasi
                   COALESCE((
                       SELECT SUM(fu.realization_value) 
                       FROM field_updates fu 
                       WHERE fu.activity_id = a.activity_id
                   ), 0) as total_submitted_realization,
                   -- Jumlah laporan
                   (SELECT COUNT(*) FROM field_updates WHERE activity_id = a.activity_id) as total_reports,
                   -- Jumlah laporan yang butuh revisi
                   (SELECT COUNT(*) FROM field_updates WHERE activity_id = a.activity_id AND status = 'Perlu Perbaikan') as revision_needed_reports
            FROM activities a
            JOIN projects p ON a.project_id = p.project_id
            LEFT JOIN activity_timelines t ON a.activity_id = t.activity_id
            {$whereClause}
            ORDER BY 
                CASE 
                    WHEN t.end_date IS NOT NULL AND t.end_date < CURDATE() AND a.status != 'Selesai' THEN 1
                    WHEN t.end_date IS NOT NULL THEN 2
                    ELSE 3
                END ASC,
                t.end_date ASC,
                a.activity_id DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        // Calculate progress percentage and dynamic status (Belum Dimulai, Berjalan, Terlambat, Selesai)
        $today = date('Y-m-d');
        foreach ($rows as &$act) {
            $target = (float)$act['total_target'];
            $realized = (float)$act['verified_realization'];
            if ($target > 0) {
                $calc = ($realized / $target) * 100;
                $act['calculated_progress'] = round(min(100, max(0, $calc)), 1);
            } else {
                $act['calculated_progress'] = $act['status'] === 'Selesai' ? 100 : 0;
            }

            // Determine display status
            if ($act['status'] === 'Selesai') {
                $act['display_status'] = 'Selesai';
            } elseif (!empty($act['end_date']) && $act['end_date'] < $today) {
                $act['display_status'] = 'Terlambat';
            } elseif (!empty($act['start_date']) && $act['start_date'] > $today) {
                $act['display_status'] = 'Belum Dimulai';
            } else {
                $act['display_status'] = $act['status'] ?: 'Berjalan';
            }
        }
        unset($act);

        return $rows;
    }

    /**
     * Mengambil daftar kegiatan yang memiliki batas waktu (deadline) dan belum berstatus 'Selesai'
     * Diurutkan dari tenggat waktu terdekat.
     */
    public function getUpcomingDeadlines(int $limit = 5): array {
        $sql = "
            SELECT a.activity_id, a.activity_name, a.status as activity_status,
                   p.project_name, p.project_code,
                   t.start_date, t.end_date, t.status as timeline_status,
                   DATEDIFF(t.end_date, CURDATE()) as diff_days
            FROM activities a
            JOIN projects p ON a.project_id = p.project_id
            JOIN activity_timelines t ON a.activity_id = t.activity_id
            WHERE a.status != 'Selesai' 
              AND t.end_date IS NOT NULL
            ORDER BY t.end_date ASC, a.activity_id ASC
            LIMIT ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $today = date('Y-m-d');
        foreach ($rows as &$act) {
            if ($act['activity_status'] === 'Selesai') {
                $act['display_status'] = 'Selesai';
            } elseif (!empty($act['end_date']) && $act['end_date'] < $today) {
                $act['display_status'] = 'Terlambat';
            } elseif (!empty($act['start_date']) && $act['start_date'] > $today) {
                $act['display_status'] = 'Belum Dimulai';
            } else {
                $act['display_status'] = $act['activity_status'] ?: 'Berjalan';
            }
        }
        unset($act);

        return $rows;
    }
}

