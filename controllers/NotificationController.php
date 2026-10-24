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
        $redirect = trim($_GET['redirect'] ?? '');

        $notifModel = new Notification();
        if ($id > 0) {
            $notif = $notifModel->findById($id, $user['user_id']);
            if ($notif) {
                // Tandai notifikasi sebagai terbaca di database
                $notifModel->markAsRead($id, $user['user_id']);

                // Jika parameter redirect kosong, tentukan tujuan berdasarkan tipe notifikasi dan aktor
                if (empty($redirect)) {
                    $db = Database::getConnection();
                    if ($notif['notification_type'] === 'Pengesahan MEL') {
                        $stmt = $db->prepare("
                            SELECT mel_result_id FROM mel_results 
                            WHERE activity_id = ? 
                            ORDER BY mel_result_id DESC 
                            LIMIT 1
                        ");
                        $stmt->execute([$notif['activity_id']]);
                        $melId = $stmt->fetchColumn();
                        if ($melId) {
                            $redirect = '/mel-results/detail?id=' . $melId;
                        } else {
                            $redirect = '/mel-results';
                        }
                    } elseif ($notif['notification_type'] === 'Keputusan Direktur') {
                        $stmt = $db->prepare("
                            SELECT mel_result_id FROM mel_results 
                            WHERE activity_id = ? 
                            ORDER BY mel_result_id DESC 
                            LIMIT 1
                        ");
                        $stmt->execute([$notif['activity_id']]);
                        $melId = $stmt->fetchColumn();
                        if ($melId) {
                            $redirect = '/mel-results/detail?id=' . $melId;
                        } else {
                            $redirect = '/mel-results';
                        }
                    } elseif ($notif['notification_type'] === 'Permintaan Perbaikan' || $notif['notification_type'] === 'Tanggapan Perbaikan') {
                        $stmt = $db->prepare("
                            SELECT cr.correction_id 
                            FROM correction_requests cr 
                            JOIN evaluations e ON cr.evaluation_id = e.evaluation_id
                            JOIN field_updates fu ON e.update_id = fu.update_id
                            WHERE fu.activity_id = ? 
                            ORDER BY cr.correction_id DESC 
                            LIMIT 1
                        ");
                        $stmt->execute([$notif['activity_id']]);
                        $corrId = $stmt->fetchColumn();
                        if ($corrId) {
                            $redirect = '/corrections/detail?id=' . $corrId;
                        } else {
                            $redirect = '/corrections';
                        }
                    } elseif ($notif['notification_type'] === 'Laporan Baru') {
                        $stmt = $db->prepare("
                            SELECT update_id FROM field_updates 
                            WHERE activity_id = ? 
                            ORDER BY update_id DESC 
                            LIMIT 1
                        ");
                        $stmt->execute([$notif['activity_id']]);
                        $updId = $stmt->fetchColumn();
                        if ($updId) {
                            $redirect = '/field-updates/detail?id=' . $updId;
                        } else {
                            $redirect = '/field-updates';
                        }
                    } elseif ($notif['notification_type'] === 'Reminder') {
                        // Untuk Pengingat Jadwal, arahkan sesuai peran aktor
                        if ($user['role_id'] == 4) {
                            // Direktur: ringkasan capaian atau detail proyek
                            $redirect = '/evaluations';
                        } elseif ($user['role_id'] == 1) {
                            $redirect = '/field-updates';
                        } else {
                            $redirect = '/projects';
                        }
                    }
                }
            } else {
                AuthHelper::setFlash('error', 'Notifikasi tidak ditemukan.');
                header('Location: ' . BASE_URL . '/notifications');
                exit;
            }
        } else {
            $notifModel->markAllAsRead($user['user_id']);
        }

        if (!empty($redirect) && str_starts_with($redirect, '/')) {
            header('Location: ' . BASE_URL . $redirect);
            exit;
        }

        header('Location: ' . BASE_URL . '/notifications');
        exit;
    }
}
