<?php
require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../models/CorrectionRequest.php';
require_once __DIR__ . '/../models/FieldUpdate.php';
require_once __DIR__ . '/../models/Notification.php';

class CorrectionController {
    public function index(): void {
        AuthHelper::requireLogin();
        $user = AuthHelper::getUser();
        $corrModel = new CorrectionRequest();

        if ($user['role_id'] == 1) { // Staf Lapangan
            $corrections = $corrModel->getForUser($user['user_id']);
        } else {
            // PL, MEL, Direktur can view requests
            $stmt = Database::getConnection()->query("
                SELECT cr.*, req.name as requested_by_name, assign.name as assigned_to_name,
                       a.activity_name, p.project_name
                FROM correction_requests cr
                JOIN users req ON cr.requested_by = req.user_id
                JOIN users assign ON cr.assigned_to = assign.user_id
                JOIN evaluations e ON cr.evaluation_id = e.evaluation_id
                JOIN field_updates fu ON e.update_id = fu.update_id
                JOIN activities a ON fu.activity_id = a.activity_id
                JOIN projects p ON a.project_id = p.project_id
                ORDER BY cr.correction_id DESC
            ");
            $corrections = $stmt->fetchAll();
        }

        $pageTitle = 'Permintaan Perbaikan (Correction Requests)';
        $activeMenu = 'corrections';
        require_once __DIR__ . '/../views/corrections/index.php';
    }

    public function detail(): void {
        AuthHelper::requireLogin();
        $id = (int)($_GET['id'] ?? 0);
        $corrModel = new CorrectionRequest();
        $correction = $corrModel->findById($id);

        if (!$correction) {
            AuthHelper::setFlash('error', 'Permintaan perbaikan tidak ditemukan.');
            header('Location: ' . BASE_URL . '/corrections');
            exit;
        }

        $pageTitle = 'Detail Klarifikasi Perbaikan #' . $correction['correction_id'];
        $activeMenu = 'corrections';
        require_once __DIR__ . '/../views/corrections/detail.php';
    }

    public function respond(): void {
        AuthHelper::requireLogin();
        $user = AuthHelper::getUser();

        $correctionId = (int)($_POST['correction_id'] ?? 0);
        $response = trim($_POST['response'] ?? '');
        $responseDate = date('Y-m-d');

        if ($correctionId > 0 && !empty($response)) {
            $corrModel = new CorrectionRequest();
            $corr = $corrModel->findById($correctionId);

            if ($corr && ($corr['assigned_to'] == $user['user_id'] || $user['role_id'] == 1)) {
                $corrModel->submitResponse($correctionId, $response, $responseDate, 'Direspons');

                // Notifikasi kembali ke Project Leader
                $notifModel = new Notification();
                $notifModel->sendNotification(
                    $corr['requested_by'],
                    $corr['activity_id'],
                    'Tanggapan Perbaikan',
                    $user['name'] . ' telah merespons perbaikan untuk kegiatan: ' . $corr['activity_name']
                );

                AuthHelper::setFlash('success', 'Tanggapan dan klarifikasi perbaikan berhasil dikirim.');
            }
        }

        header('Location: ' . BASE_URL . '/corrections/detail?id=' . $correctionId);
        exit;
    }
}
