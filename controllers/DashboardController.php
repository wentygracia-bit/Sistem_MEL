<?php
require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../models/Project.php';
require_once __DIR__ . '/../models/Activity.php';
require_once __DIR__ . '/../models/FieldUpdate.php';
require_once __DIR__ . '/../models/Evaluation.php';
require_once __DIR__ . '/../models/CorrectionRequest.php';
require_once __DIR__ . '/../models/MELResult.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/ActivityTimeline.php';

class DashboardController {
    public function index(): void {
        AuthHelper::requireLogin();
        $user = AuthHelper::getUser();

        $projectModel = new Project();
        $activityModel = new Activity();
        $updateModel = new FieldUpdate();
        $evalModel = new Evaluation();
        $correctionModel = new CorrectionRequest();
        $melResultModel = new MELResult();

        // Automatic reminder check
        $timelineModel = new ActivityTimeline();
        $dueReminders = $timelineModel->calculateReminder();
        $notifModel = new Notification();
        foreach ($dueReminders as $rem) {
            // Check if reminder was recently notified to avoid flood
            // Send reminder to relevant project staff
        }

        // Khusus Role Direktur (role_id = 4)
        if ((int)$user['role_id'] === 4) {
            $activeProjectsCount = $projectModel->countActiveProjects();
            $pendingMelCount = $melResultModel->countByStatus('Diajukan');
            $approvedMelCount = $melResultModel->countByStatus('Disetujui');
            $needsFollowUpCount = $melResultModel->countByStatus('Ditolak');

            $pendingMelList = $melResultModel->getPendingApproval();
            $activityStats = $activityModel->getStatusCounts();

            $pageTitle = 'Dashboard Eksekutif Direktur';
            $activeMenu = 'dashboard';

            require_once __DIR__ . '/../views/dashboard/director.php';
            return;
        }

        // Khusus Role Staf Lapangan (role_id = 1)
        if ((int)$user['role_id'] === 1) {
            $fieldActivities = $activityModel->getActivitiesForUser($user['user_id'], 1);
            $fieldStats = $updateModel->getStatsForUser($user['user_id']);
            $activeCorrectionsCount = $correctionModel->countActiveForUser($user['user_id']);

            // Calculate 4 KPI Cards for Field Staff:
            // 1. Total Kegiatan
            $totalAssignedActivities = count($fieldActivities);
            // 2. Kegiatan Sedang Berjalan
            $runningActivitiesCount = 0;
            // 3. Laporan Belum Dikirim (Draft)
            $draftReportsCount = $fieldStats['draft'];
            // 4. Permintaan Revisi
            $pendingRevisionsCount = $activeCorrectionsCount;

            $upcomingDeadlines = [];
            $today = date('Y-m-d');

            foreach ($fieldActivities as $fa) {
                if ($fa['status'] === 'Berjalan' || $fa['display_status'] === 'Berjalan') {
                    $runningActivitiesCount++;
                }
                if (!empty($fa['end_date']) && $fa['status'] !== 'Selesai') {
                    $diffDays = (strtotime($fa['end_date']) - strtotime($today)) / 86400;
                    if ($diffDays <= 14) { // deadline dalam 2 minggu atau sudah lewat
                        $upcomingDeadlines[] = [
                            'activity_id'   => $fa['activity_id'],
                            'activity_name' => $fa['activity_name'],
                            'project_name'  => $fa['project_name'],
                            'end_date'      => $fa['end_date'],
                            'diff_days'     => (int)$diffDays,
                            'status'        => $fa['display_status'],
                        ];
                    }
                }
            }

            // Limit 3-5 activities for progress section
            $topActivities = array_slice($fieldActivities, 0, 5);

            // Recent Activities from user's reports & notifications
            $recentStaffUpdates = $updateModel->getAllForUser($user['user_id'], 1, 5);
            $recentNotifications = $notifModel->getForUser($user['user_id'], 5);

            $pageTitle = 'Dashboard Staf Lapangan';
            $activeMenu = 'dashboard';

            require_once __DIR__ . '/../views/dashboard/field_staff.php';
            return;
        }

        // Khusus Role Staf MEL (role_id = 2)
        if ((int)$user['role_id'] === 2) {
            $activeProjectsCount = $projectModel->countActiveProjects();
            $totalReportsCount = $updateModel->countAll();
            $activeCorrectionsCount = $correctionModel->countActiveGlobal();
            $totalMelResultsCount = $melResultModel->countAll();

            $recentUpdates = $updateModel->getAll(5);
            $recentCorrections = $correctionModel->getRecentGlobal(5);
            $recentMelResults = $melResultModel->getRecentWithApproval(4);

            $pageTitle = 'Dashboard Staf MEL';
            $activeMenu = 'dashboard';

            require_once __DIR__ . '/../views/dashboard/mel_staff.php';
            return;
        }

        // Role lainnya (Project Leader)
        $projects = $projectModel->getProjectsForUser($user['user_id'], $user['role_id']);
        $recentUpdates = $updateModel->getAll(5);
        $recentCorrections = $correctionModel->getForUser($user['user_id']);
        $melResults = $melResultModel->getAll();

        // Khusus Role Project Leader (role_id = 3): Siapkan 4 KPI Card spesifik
        $plStats = [];
        if ((int)$user['role_id'] === 3) {
            // 1. Proyek Aktif — jumlah proyek berstatus 'Aktif' dalam kewenangan Project Leader
            $activeProjectsCount = 0;
            $plProjectIds = [];
            foreach ($projects as $p) {
                if (isset($p['status']) && strcasecmp($p['status'], 'Aktif') === 0) {
                    $activeProjectsCount++;
                }
                $plProjectIds[] = (int)$p['project_id'];
            }

            // 2. Progres Kegiatan — persentase agregasi unik per kegiatan berbasis laporan lapangan terbaru
            // Ambil seluruh kegiatan di bawah proyek kewenangan PL
            $plActivities = [];
            if (!empty($plProjectIds)) {
                $dbConn = Database::getConnection();
                $inQuery = implode(',', array_fill(0, count($plProjectIds), '?'));
                
                // Ambil kegiatan beserta progress terbaru dari laporan lapangan unik per kegiatan (menghindari duplikasi)
                $actSql = "
                    SELECT a.activity_id, a.activity_name, a.project_id, a.status,
                           p.project_name,
                           t.start_date, t.end_date,
                           -- Ambil progres persentase terbaru dari laporan lapangan yang sudah diajukan/dievaluasi
                           (
                               SELECT fu.progress_percentage 
                               FROM field_updates fu 
                               WHERE fu.activity_id = a.activity_id 
                               ORDER BY fu.update_date DESC, fu.update_id DESC 
                               LIMIT 1
                           ) as latest_progress_percentage,
                           -- Cek apakah ada laporan berstatus 'Perlu Perbaikan'
                           (
                               SELECT COUNT(*) 
                               FROM field_updates fu 
                               WHERE fu.activity_id = a.activity_id 
                                 AND fu.status = 'Perlu Perbaikan'
                           ) as count_needs_correction_reports
                    FROM activities a
                    JOIN projects p ON a.project_id = p.project_id
                    LEFT JOIN activity_timelines t ON a.activity_id = t.activity_id
                    WHERE a.project_id IN ($inQuery)
                ";
                $actStmt = $dbConn->prepare($actSql);
                $actStmt->execute($plProjectIds);
                $plActivities = $actStmt->fetchAll();
            }

            $totalActivitiesCount = count($plActivities);
            $sumProgress = 0.0;
            $delayedActivitiesCount = 0;
            $today = date('Y-m-d');

            foreach ($plActivities as $act) {
                // Gunakan persentase laporan terbaru jika ada, jika status 'Selesai' anggap 100%
                if ($act['status'] === 'Selesai') {
                    $prog = 100.0;
                } elseif ($act['latest_progress_percentage'] !== null) {
                    $prog = (float)$act['latest_progress_percentage'];
                } else {
                    $prog = 0.0;
                }
                $sumProgress += max(0.0, min(100.0, $prog));

                // Deteksi kegiatan terlambat dari jadwal (end_date sudah lewat & belum selesai)
                if (!empty($act['end_date']) && $act['end_date'] < $today && $act['status'] !== 'Selesai') {
                    $delayedActivitiesCount++;
                }
            }

            $avgActivityProgress = $totalActivitiesCount > 0 ? round($sumProgress / $totalActivitiesCount, 1) : 0.0;

            // 3. Laporan Tindak Lanjut — jumlah laporan lapangan yang butuh pemeriksaan lanjutan (status 'Diajukan')
            $pendingReviewReportsCount = 0;
            if (!empty($plProjectIds)) {
                $dbConn = Database::getConnection();
                $inQuery = implode(',', array_fill(0, count($plProjectIds), '?'));
                $fuSql = "
                    SELECT COUNT(*) 
                    FROM field_updates fu
                    JOIN activities a ON fu.activity_id = a.activity_id
                    WHERE a.project_id IN ($inQuery)
                      AND fu.status IN ('Diajukan', 'Menunggu Verifikasi')
                ";
                $fuStmt = $dbConn->prepare($fuSql);
                $fuStmt->execute($plProjectIds);
                $pendingReviewReportsCount = (int)$fuStmt->fetchColumn();
            }

            // 4. Perlu Perbaikan — laporan berstatus 'Perlu Perbaikan' + kegiatan terlambat (tanpa duplikasi)
            $needsCorrectionReportsCount = 0;
            if (!empty($plProjectIds)) {
                $dbConn = Database::getConnection();
                $inQuery = implode(',', array_fill(0, count($plProjectIds), '?'));
                $corrSql = "
                    SELECT COUNT(*) 
                    FROM field_updates fu
                    JOIN activities a ON fu.activity_id = a.activity_id
                    WHERE a.project_id IN ($inQuery)
                      AND fu.status IN ('Perlu Perbaikan', 'Perlu Direvisi')
                ";
                $corrStmt = $dbConn->prepare($corrSql);
                $corrStmt->execute($plProjectIds);
                $needsCorrectionReportsCount = (int)$corrStmt->fetchColumn();
            }

            $totalAttentionCount = $needsCorrectionReportsCount + $delayedActivitiesCount;

            $plStats = [
                'active_projects'        => $activeProjectsCount,
                'total_projects'         => count($projects),
                'avg_progress'           => $avgActivityProgress,
                'total_activities'       => $totalActivitiesCount,
                'pending_review_reports' => $pendingReviewReportsCount,
                'needs_correction_reports' => $needsCorrectionReportsCount,
                'delayed_activities'     => $delayedActivitiesCount,
                'total_attention'        => $totalAttentionCount,
            ];
        }

        $pageTitle = ((int)$user['role_id'] === 3) ? 'Dashboard Project Leader' : 'Dashboard Ringkasan';
        $activeMenu = 'dashboard';

        require_once __DIR__ . '/../views/dashboard/index.php';
    }


    public function publicView(): void {
        $projectModel = new Project();
        $melResultModel = new MELResult();

        $publicProjects = $projectModel->getAll(true);
        $melResults = $melResultModel->getAll();

        require_once __DIR__ . '/../views/public/index.php';
    }
}
