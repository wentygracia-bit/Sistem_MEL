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

        $search = trim($_GET['search'] ?? '');
        $statusFilter = trim($_GET['status'] ?? '');
        $activityFilter = !empty($_GET['activity_id']) ? (int)$_GET['activity_id'] : null;

        $updates = $updateModel->getAllForUser(
            $user['user_id'],
            $user['role_id'],
            100,
            $search !== '' ? $search : null,
            $statusFilter !== '' ? $statusFilter : null,
            $activityFilter
        );

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

        $activityName = trim($_POST['activity_name'] ?? '');
        $activityId = (int)($_POST['activity_id'] ?? 0);

        // Resolve or create activity from typed name
        $activityModel = new Activity();
        if (!empty($activityName)) {
            $activityId = $activityModel->findOrCreateByName($activityName);
        }

        $updateDate = $_POST['update_date'] ?? date('Y-m-d');
        $realizationValue = !empty($_POST['realization_value']) ? (float)$_POST['realization_value'] : 0.0;
        $progressPercentage = !empty($_POST['progress_percentage']) ? (float)$_POST['progress_percentage'] : 0.0;
        $progressPercentage = max(0.0, min(100.0, $progressPercentage));
        $description = trim($_POST['description'] ?? '');

        // Determine status based on button clicked: "save_draft" or "submit_verification"
        $actionType = $_POST['action_type'] ?? 'submit_verification';
        $status = ($actionType === 'save_draft') ? 'Draft' : 'Diajukan';

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
                $status
            );

            // Notify Project Leader & MEL Staff only if submitted for verification
            if ($status === 'Diajukan') {
                $projectModel = new Project();
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
                AuthHelper::setFlash('success', 'Laporan lapangan berhasil dikirim untuk verifikasi.');
            } else {
                AuthHelper::setFlash('success', 'Laporan lapangan berhasil disimpan sebagai Draft.');
            }
        } else {
            AuthHelper::setFlash('error', 'Nama kegiatan dan deskripsi laporan wajib diisi.');
        }

        header('Location: ' . BASE_URL . '/field-updates');
        exit;
    }

    public function publish(): void {
        AuthHelper::requireLogin();
        $user = AuthHelper::getUser();
        $updateId = (int)($_POST['update_id'] ?? 0);

        $updateModel = new FieldUpdate();
        $existing = $updateModel->findById($updateId);

        if (!$existing) {
            AuthHelper::setFlash('error', 'Data pelaporan tidak ditemukan.');
            header('Location: ' . BASE_URL . '/field-updates');
            exit;
        }

        if ($existing['user_id'] != $user['user_id'] && !in_array($user['role_id'], [2, 3])) {
            AuthHelper::setFlash('error', 'Anda tidak memiliki hak akses untuk mengirim laporan ini.');
            header('Location: ' . BASE_URL . '/field-updates');
            exit;
        }

        $updateModel->updateStatus($updateId, 'Diajukan');

        // Notify PL & MEL
        $activityModel = new Activity();
        $act = $activityModel->findById($existing['activity_id']);
        if ($act) {
            $projectModel = new Project();
            $notifModel = new Notification();
            $members = $projectModel->getMembers($act['project_id']);
            foreach ($members as $mem) {
                if ($mem['member_role'] === 'Project Leader' || $mem['role_id'] == 3 || $mem['role_id'] == 2) {
                    $notifModel->sendNotification(
                        $mem['user_id'],
                        $existing['activity_id'],
                        'Laporan Baru',
                        'Laporan draft #' . $updateId . ' telah dikirim untuk verifikasi oleh ' . $user['name'] . ' pada kegiatan: ' . $act['activity_name']
                    );
                }
            }
        }

        AuthHelper::setFlash('success', 'Laporan #' . $updateId . ' berhasil dikirim untuk proses verifikasi.');
        header('Location: ' . BASE_URL . '/field-updates');
        exit;
    }

    public function edit(): void {
        AuthHelper::requireLogin();
        $user = AuthHelper::getUser();
        $updateId = (int)($_POST['update_id'] ?? 0);

        $updateModel = new FieldUpdate();
        $existing = $updateModel->findById($updateId);

        if (!$existing) {
            AuthHelper::setFlash('error', 'Data pelaporan tidak ditemukan.');
            header('Location: ' . BASE_URL . '/field-updates');
            exit;
        }

        // Access check: Only the reporter or MEL Staff/Admin (role 2) or PL (role 3) can edit
        if ($existing['user_id'] != $user['user_id'] && !in_array($user['role_id'], [2, 3])) {
            AuthHelper::setFlash('error', 'Anda tidak memiliki hak akses untuk mengubah laporan ini.');
            header('Location: ' . BASE_URL . '/field-updates');
            exit;
        }

        // Status check: if already 'Dievaluasi', Field Staff cannot edit arbitrarily
        if ($existing['status'] === 'Dievaluasi' && $user['role_id'] == 1) {
            AuthHelper::setFlash('error', 'Laporan yang telah diverifikasi dan disetujui tidak dapat diedit kembali.');
            header('Location: ' . BASE_URL . '/field-updates');
            exit;
        }

        $activityName = trim($_POST['activity_name'] ?? '');
        $activityModel = new Activity();
        $activityId = $existing['activity_id'];

        if (!empty($activityName) && strcasecmp($activityName, $existing['activity_name']) !== 0) {
            $activityId = $activityModel->findOrCreateByName($activityName, $existing['project_id'] ?? null);
        }

        $updateDate = $_POST['update_date'] ?? $existing['update_date'];
        $realizationValue = isset($_POST['realization_value']) && $_POST['realization_value'] !== '' ? (float)$_POST['realization_value'] : 0.0;
        $progressPercentage = isset($_POST['progress_percentage']) && $_POST['progress_percentage'] !== '' ? (float)$_POST['progress_percentage'] : 0.0;
        $progressPercentage = max(0.0, min(100.0, $progressPercentage));
        $description = trim($_POST['description'] ?? '');

        if (empty($description)) {
            AuthHelper::setFlash('error', 'Deskripsi laporan wajib diisi.');
            header('Location: ' . BASE_URL . '/field-updates');
            exit;
        }

        $actionType = $_POST['action_type'] ?? '';
        $newStatus = null;
        if ($actionType === 'submit_verification' && in_array($existing['status'], ['Draft', 'Perlu Perbaikan'])) {
            $newStatus = 'Diajukan';
        }

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

        $success = $updateModel->updateFull(
            $updateId,
            $activityId,
            $updateDate,
            $description,
            $realizationValue,
            $progressPercentage,
            $evidenceFileName,
            $newStatus
        );

        if ($success) {
            if ($newStatus === 'Diajukan') {
                AuthHelper::setFlash('success', 'Data laporan #' . $updateId . ' berhasil diperbarui dan dikirim untuk verifikasi ulang.');
            } else {
                AuthHelper::setFlash('success', 'Data laporan lapangan #' . $updateId . ' berhasil diperbarui.');
            }
        } else {
            AuthHelper::setFlash('error', 'Gagal memperbarui data laporan lapangan.');
        }

        header('Location: ' . BASE_URL . '/field-updates');
        exit;
    }

    public function delete(): void {
        AuthHelper::requireLogin();
        $user = AuthHelper::getUser();
        $updateId = (int)($_POST['update_id'] ?? 0);

        $updateModel = new FieldUpdate();
        $existing = $updateModel->findById($updateId);

        if (!$existing) {
            AuthHelper::setFlash('error', 'Data pelaporan tidak ditemukan.');
            header('Location: ' . BASE_URL . '/field-updates');
            exit;
        }

        // Access check: only reporter or role 2/3 can delete
        if ($existing['user_id'] != $user['user_id'] && !in_array($user['role_id'], [2, 3])) {
            AuthHelper::setFlash('error', 'Anda tidak memiliki hak akses untuk menghapus laporan ini.');
            header('Location: ' . BASE_URL . '/field-updates');
            exit;
        }

        // Status check: Verified reports cannot be deleted by Field Staff
        if ($existing['status'] === 'Dievaluasi' && $user['role_id'] == 1) {
            AuthHelper::setFlash('error', 'Laporan yang telah diverifikasi dan disetujui tidak dapat dihapus.');
            header('Location: ' . BASE_URL . '/field-updates');
            exit;
        }

        try {
            $deleted = $updateModel->delete($updateId);
            if ($deleted) {
                // Delete evidence file if exists
                if (!empty($existing['evidence_file'])) {
                    $filePath = __DIR__ . '/../public/uploads/' . $existing['evidence_file'];
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }
                AuthHelper::setFlash('success', 'Laporan lapangan #' . $updateId . ' berhasil dihapus.');
            } else {
                AuthHelper::setFlash('error', 'Gagal menghapus laporan lapangan.');
            }
        } catch (PDOException $e) {
            AuthHelper::setFlash('error', 'Laporan tidak dapat dihapus karena terkait dengan data evaluasi atau hasil MEL.');
        }

        header('Location: ' . BASE_URL . '/field-updates');
        exit;
    }
}

