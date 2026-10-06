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
