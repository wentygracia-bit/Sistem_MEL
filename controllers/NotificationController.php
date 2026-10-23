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
            $phase = $d['reminder_phase'] ?? 'Jadwal';
            $formattedStart = date('d M Y', strtotime($d['start_date']));
            $msg = ($phase === 'H-10')
                ? "Pengingat Jadwal (H-10): Kegiatan '{$d['activity_name']}' pada proyek '{$d['project_name']}' akan dimulai dalam 10 hari ({$formattedStart}). Harap siapkan rencana pelaksanaan teknis."
                : (($phase === 'H-3')
                    ? "Pengingat Jadwal (H-3): Kegiatan '{$d['activity_name']}' pada proyek '{$d['project_name']}' akan segera dimulai dalam 3 hari ({$formattedStart}). Pastikan kesiapan lapangan dan tim."
                    : "Pengingat Jadwal: Kegiatan '{$d['activity_name']}' mendekati jadwal ({$formattedStart}).");

            $members = $projectModel->getMembers($d['project_id']);
            foreach ($members as $m) {
                $targetUserId = (int)$m['user_id'];
                if (!$notifModel->reminderExists($targetUserId, (int)$d['activity_id'], $phase)) {
                    $notifModel->sendNotification($targetUserId, (int)$d['activity_id'], 'Reminder', $msg);
                }
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
