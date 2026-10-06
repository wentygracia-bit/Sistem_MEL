<?php
require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../models/FieldUpdate.php';
require_once __DIR__ . '/../models/Activity.php';
require_once __DIR__ . '/../models/Indicator.php';
require_once __DIR__ . '/../models/Evaluation.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/Project.php';

class FieldUpdateController {
    public function index(): void {
        AuthHelper::requireLogin();
        $user = AuthHelper::getUser();
        $updateModel = new FieldUpdate();
        $updates = $updateModel->getAll(100);

        $activityModel = new Activity();
        $activities = $activityModel->getAllWithSummary();

        $pageTitle = 'Pelaporan Lapangan (Field Updates)';
        $activeMenu = 'field-updates';
        require_once __DIR__ . '/../views/field_updates/index.php';
    }

    public function detail(): void {
        AuthHelper::requireLogin();
        $updateId = (int)($_GET['id'] ?? 0);
        $updateModel = new FieldUpdate();
        $update = $updateModel->findById($updateId);

        if (!$update) {
            AuthHelper::setFlash('error', 'Data pelaporan tidak ditemukan.');
            header('Location: ' . BASE_URL . '/field-updates');
            exit;
        }

        $indicatorModel = new Indicator();
        $indicators = $indicatorModel->getByActivityId($update['activity_id']);

        $evalModel = new Evaluation();
        $evaluations = $evalModel->getByUpdateId($updateId);

        $pageTitle = 'Detail Laporan Lapangan #' . $update['update_id'];
        $activeMenu = 'field-updates';
        require_once __DIR__ . '/../views/field_updates/detail.php';
    }

    public function submit(): void {
        AuthHelper::requireLogin();
        $user = AuthHelper::getUser();

        $activityId = (int)($_POST['activity_id'] ?? 0);
        $updateDate = $_POST['update_date'] ?? date('Y-m-d');
        $realizationValue = !empty($_POST['realization_value']) ? (float)$_POST['realization_value'] : 0.0;
        $progressPercentage = !empty($_POST['progress_percentage']) ? (float)$_POST['progress_percentage'] : 0.0;
        $description = trim($_POST['description'] ?? '');

        $evidenceFileName = null;
        if (isset($_FILES['evidence_file']) && $_FILES['evidence_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../public/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = pathinfo($_FILES['evidence_file']['name'], PATHINFO_EXTENSION);
            $fileName = 'evidence_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            if (move_uploaded_file($_FILES['evidence_file']['tmp_name'], $uploadDir . $fileName)) {
                $evidenceFileName = $fileName;
            }
        }

        if ($activityId > 0 && !empty($description)) {
            $updateModel = new FieldUpdate();
            $newId = $updateModel->submitUpdate(
                $activityId,
                $user['user_id'],
                $updateDate,
                $description,
                $realizationValue,
                $progressPercentage,
                $evidenceFileName,
                'Diajukan'
            );

            // Notify Project Leader & MEL Staff
            $projectModel = new Project();
            $activityModel = new Activity();
            $act = $activityModel->findById($activityId);
            $notifModel = new Notification();
            if ($act) {
                $members = $projectModel->getMembers($act['project_id']);
                foreach ($members as $mem) {
                    if ($mem['member_role'] === 'Project Leader' || $mem['role_id'] == 3 || $mem['role_id'] == 2) {
                        $notifModel->sendNotification(
                            $mem['user_id'],
                            $activityId,
                            'Laporan Baru',
                            'Laporan lapangan baru diajukan oleh ' . $user['name'] . ' pada kegiatan: ' . $act['activity_name']
                        );
                    }
                }
            }

            AuthHelper::setFlash('success', 'Laporan lapangan berhasil dikirim.');
        } else {
            AuthHelper::setFlash('error', 'Kegiatan dan deskripsi laporan wajib diisi.');
        }

        header('Location: ' . BASE_URL . '/field-updates');
        exit;
    }
}
