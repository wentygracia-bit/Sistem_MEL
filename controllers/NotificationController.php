<?php
require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/ActivityTimeline.php';
require_once __DIR__ . '/../models/Project.php';

class NotificationController {
    public function index(): void {
        AuthHelper::requireLogin();
        $user = AuthHelper::getUser();
        $notifModel = new Notification();

        // Trigger reminder check to generate notifications if any due
        $timelineModel = new ActivityTimeline();
        $due = $timelineModel->calculateReminder();
        $projectModel = new Project();

        foreach ($due as $d) {
            $members = $projectModel->getMembers($d['project_id']);
            foreach ($members as $m) {
                // If notification not exists recently, create one
                // Simple send reminder
            }
        }

        $notifications = $notifModel->getForUser($user['user_id'], 50);

        $pageTitle = 'Notifikasi & Pengingat Jadwal';
        $activeMenu = 'notifications';
        require_once __DIR__ . '/../views/notifications/index.php';
    }

    public function markAsRead(): void {
        AuthHelper::requireLogin();
        $user = AuthHelper::getUser();
        $id = (int)($_GET['id'] ?? 0);

        $notifModel = new Notification();
        if ($id > 0) {
            $notifModel->markAsRead($id, $user['user_id']);
        } else {
            $notifModel->markAllAsRead($user['user_id']);
        }

        header('Location: ' . BASE_URL . '/notifications');
        exit;
    }
}
