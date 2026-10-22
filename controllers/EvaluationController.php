<?php
require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../models/Evaluation.php';
require_once __DIR__ . '/../models/FieldUpdate.php';
require_once __DIR__ . '/../models/Indicator.php';
require_once __DIR__ . '/../models/CorrectionRequest.php';
require_once __DIR__ . '/../models/Notification.php';

class EvaluationController {
    public function index(): void {
        AuthHelper::requireRole([2, 3, 4]); // MEL, PL, Direktur
        $user = AuthHelper::getUser();

        $evalModel = new Evaluation();
        $evaluations = $evalModel->getAll(100);

        // Ambil data koreksi untuk tab Permintaan Perbaikan pada Staf MEL
        $corrections = [];
        if ($user['role_id'] == 2) {
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

        $activeTab = $_GET['tab'] ?? 'evaluasi';
        if ($user['role_id'] == 2) {
            $pageTitle = 'Evaluasi & Tindak Lanjut';
        } elseif ($user['role_id'] == 4) {
            $pageTitle = 'Ringkasan Capaian';
        } else {
            $pageTitle = 'Evaluasi Capaian Lapangan';
        }

        $activeMenu = 'evaluations';
        require_once __DIR__ . '/../views/evaluations/index.php';
    }

    public function submit(): void {
        AuthHelper::requireRole([3]); // Project Leader strictly evaluates
        $user = AuthHelper::getUser();

        $updateId = (int)($_POST['update_id'] ?? 0);
        $indicatorId = (int)($_POST['indicator_id'] ?? 0);
        $result = $_POST['result'] ?? 'Sesuai';
        $notes = trim($_POST['notes'] ?? '');
        $evalDate = date('Y-m-d');

        if ($updateId > 0 && $indicatorId > 0) {
            $evalModel = new Evaluation();
            $evalId = $evalModel->evaluateResult($updateId, $indicatorId, $user['user_id'], $evalDate, $result, $notes);

            $updateModel = new FieldUpdate();
            $update = $updateModel->findById($updateId);

            if ($result === 'Sesuai') {
                $updateModel->updateStatus($updateId, 'Dievaluasi');
            } else {
                $updateModel->updateStatus($updateId, 'Perlu Perbaikan');

                // Otomatis buat Correction Request jika Tidak Sesuai (merujuk Alur 89 PRD)
                $correctionModel = new CorrectionRequest();
                $assignedTo = $update['user_id'];
                $corrId = $correctionModel->createCorrection(
                    $evalId,
                    $user['user_id'],
                    $assignedTo,
                    $evalDate,
                    'Perbaikan data dibutuhkan: ' . $notes,
                    'Diminta'
                );

                // Notifikasi ke Staf Lapangan
                $notifModel = new Notification();
                $notifModel->sendNotification(
                    $assignedTo,
                    $update['activity_id'],
                    'Permintaan Perbaikan',
                    'Project Leader meminta perbaikan/klarifikasi pada laporan kegiatan "' . $update['activity_name'] . '".'
                );
            }

            AuthHelper::setFlash('success', 'Evaluasi capaian berhasil disimpan.');
        } else {
            AuthHelper::setFlash('error', 'Data evaluasi tidak lengkap.');
        }

        header('Location: ' . BASE_URL . '/field-updates/detail?id=' . $updateId);
        exit;
    }
}
