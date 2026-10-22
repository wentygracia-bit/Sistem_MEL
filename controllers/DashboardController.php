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

        // Role lainnya (Staf MEL, Project Leader)
        $projects = $projectModel->getProjectsForUser($user['user_id'], $user['role_id']);
        $recentUpdates = $updateModel->getAll(5);
        $recentCorrections = $correctionModel->getForUser($user['user_id']);
        $melResults = $melResultModel->getAll();

        $pageTitle = 'Dashboard Ringkasan';
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
