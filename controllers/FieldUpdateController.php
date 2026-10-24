<?php
require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../helpers/ImageHelper.php';
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

        $activityModel = new Activity();
        $filteredActivity = null;

        // Validasi jika melihat riwayat laporan berdasarkan ID kegiatan tertentu
        if ($activityFilter !== null) {
            $filteredActivity = $activityModel->findById($activityFilter);
            if (!$filteredActivity) {
                AuthHelper::setFlash('error', 'Kegiatan tidak ditemukan.');
                header('Location: ' . BASE_URL . '/field-updates');
                exit;
            }

            // Otorisasi akses: Staf Lapangan (Role 1) hanya boleh melihat kegiatan proyek yang menjadi penugasannya
            if ((int)$user['role_id'] === 1) {
                $projectModel = new Project();
                $members = $projectModel->getMembers((int)$filteredActivity['project_id']);
                $isAssigned = false;
                foreach ($members as $mem) {
                    if ((int)$mem['user_id'] === (int)$user['user_id']) {
                        $isAssigned = true;
                        break;
                    }
                }
                if (!$isAssigned) {
                    AuthHelper::setFlash('error', 'Akses ditolak: Anda tidak ditugaskan pada proyek kegiatan ini.');
                    header('Location: ' . BASE_URL . '/projects');
                    exit;
                }
            }
        }

        $updates = $updateModel->getAllForUser(
            $user['user_id'],
            $user['role_id'],
            100,
            $search !== '' ? $search : null,
            $statusFilter !== '' ? $statusFilter : null,
            $activityFilter
        );

        if ((int)$user['role_id'] === 1) {
            $activities = $activityModel->getActivitiesForUser((int)$user['user_id'], 1);
        } else {
            $activities = $activityModel->getAllWithSummary();
        }

        $pageTitle = $filteredActivity 
            ? 'Riwayat Laporan: ' . $filteredActivity['activity_name']
            : 'Pelaporan Lapangan (Field Updates)';
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

        // Otorisasi ketat di sisi server: Hanya Staf Lapangan (Role 1) yang diizinkan menginput laporan
        if ((int)$user['role_id'] !== 1) {
            AuthHelper::setFlash('error', 'Akses ditolak: Hanya Staf Lapangan yang memiliki hak akses untuk menginput laporan pekerjaan.');
            header('Location: ' . BASE_URL . '/field-updates');
            exit;
        }

        $activityId = (int)($_POST['activity_id'] ?? 0);
        $activityModel = new Activity();

        // Validasi: Wajib memilih kegiatan yang valid dari autocomplete
        if ($activityId <= 0) {
            AuthHelper::setFlash('error', 'Silakan pilih kegiatan yang valid dari daftar pencarian.');
            header('Location: ' . BASE_URL . '/field-updates');
            exit;
        }

        // Validasi keberadaan kegiatan dan hak akses Staf Lapangan
        $activityData = $activityModel->findById($activityId);
        if (!$activityData) {
            AuthHelper::setFlash('error', 'Kegiatan tidak ditemukan.');
            header('Location: ' . BASE_URL . '/field-updates');
            exit;
        }

        // Periksa apakah Staf Lapangan ditugaskan pada proyek kegiatan ini
        $projectModel = new Project();
        $members = $projectModel->getMembers((int)$activityData['project_id']);
        $isAssigned = false;
        foreach ($members as $mem) {
            if ((int)$mem['user_id'] === (int)$user['user_id']) {
                $isAssigned = true;
                break;
            }
        }

        if (!$isAssigned) {
            AuthHelper::setFlash('error', 'Akses ditolak: Anda tidak ditugaskan pada proyek untuk kegiatan ini.');
            header('Location: ' . BASE_URL . '/field-updates');
            exit;
        }

        $updateDate = $_POST['update_date'] ?? date('Y-m-d');
        $realizationValue = !empty($_POST['realization_value']) ? (float)$_POST['realization_value'] : 0.0;
        
        // Akumulasi / perhitungan otomatis persentase capaian oleh sistem
        $totalTarget = (float)($activityData['total_target'] ?? 0);
        if ($totalTarget > 0) {
            $calcPercent = ($realizationValue / $totalTarget) * 100.0;
            $progressPercentage = round(max(0.0, min(100.0, $calcPercent)), 1);
        } else {
            // Jika kegiatan belum memiliki target terdefinisi atau target = 0
            $progressPercentage = $realizationValue > 0 ? 100.0 : 0.0;
        }

        $description = trim($_POST['description'] ?? '');

        // Determine status based on button clicked: "save_draft" or "submit_verification"
        $actionType = $_POST['action_type'] ?? 'submit_verification';
        $status = ($actionType === 'save_draft') ? 'Draft' : 'Diajukan';

        $evidenceFileName = $this->handleEvidenceUpload();

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

        // Access check: Project Leader (role 3) tidak boleh mengedit laporan kegiatan (hanya melihat / mengevaluasi)
        if ($user['role_id'] == 3) {
            AuthHelper::setFlash('error', 'Project Leader hanya memiliki izin melihat dan mengevaluasi laporan, tidak dapat mengedit laporan.');
            header('Location: ' . BASE_URL . '/field-updates');
            exit;
        }

        // Access check: Hanya pemilik laporan atau Staf MEL (role 2) yang dapat mengedit
        if ($existing['user_id'] != $user['user_id'] && $user['role_id'] != 2) {
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

        $postedActivityId = (int)($_POST['activity_id'] ?? 0);
        $activityName = trim($_POST['activity_name'] ?? '');
        $activityModel = new Activity();
        $activityId = $existing['activity_id'];

        if ($postedActivityId > 0) {
            $activityId = $postedActivityId;
        } elseif (!empty($activityName) && strcasecmp($activityName, $existing['activity_name']) !== 0) {
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

        $evidenceFileName = $this->handleEvidenceUpload();

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

        // Access check: Project Leader (role 3) tidak boleh menghapus laporan kegiatan
        if ($user['role_id'] == 3) {
            AuthHelper::setFlash('error', 'Project Leader hanya memiliki izin melihat dan mengevaluasi laporan, tidak dapat menghapus laporan.');
            header('Location: ' . BASE_URL . '/field-updates');
            exit;
        }

        // Access check: hanya pemilik laporan atau Staf MEL (role 2) yang dapat menghapus
        if ($existing['user_id'] != $user['user_id'] && $user['role_id'] != 2) {
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

    public function saveDirectorNote(): void {
        AuthHelper::requireRole([4]); // Hanya Direktur
        $user = AuthHelper::getUser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/evaluations');
            exit;
        }

        $updateId = (int)($_POST['update_id'] ?? 0);
        $directorNotes = trim($_POST['director_notes'] ?? '');

        $updateModel = new FieldUpdate();
        $update = $updateModel->findById($updateId);

        if (!$update) {
            AuthHelper::setFlash('error', 'Laporan lapangan tidak ditemukan.');
            header('Location: ' . BASE_URL . '/evaluations');
            exit;
        }

        // Simpan catatan direktur (bisa null jika dikosongkan)
        $notesValue = !empty($directorNotes) ? $directorNotes : null;
        $updateModel->updateDirectorNotes($updateId, $notesValue);

        // Jika ada catatan yang diisi/diperbarui, kirimkan notifikasi ke aktor-aktor terkait
        if (!empty($notesValue)) {
            $notifModel = new Notification();
            $db = Database::getConnection();

            $activityId = (int)$update['activity_id'];
            $activityName = $update['activity_name'];
            $notifMsg = "Direktur memberikan catatan atensi pada Laporan Lapangan #{$updateId} (Kegiatan: {$activityName}): \"" . mb_strimwidth($notesValue, 0, 75, '...') . "\"";

            // 1. Notifikasi ke Pelapor / Staf Lapangan
            $reporterId = (int)$update['user_id'];
            if ($reporterId > 0 && $reporterId !== (int)$user['user_id']) {
                $notifModel->sendNotification($reporterId, $activityId, 'DirectorNote', $notifMsg);
            }

            // 2. Notifikasi ke Staf MEL (Role 2)
            $melUsers = $db->query("SELECT user_id FROM users WHERE role_id = 2 AND is_active = 1")->fetchAll();
            foreach ($melUsers as $mu) {
                if ((int)$mu['user_id'] !== (int)$user['user_id']) {
                    $notifModel->sendNotification((int)$mu['user_id'], $activityId, 'DirectorNote', $notifMsg);
                }
            }

            // 3. Notifikasi ke Project Leader (Role 3) yang terkait dengan proyek ini
            $projectId = (int)$update['project_id'];
            $plStmt = $db->prepare("
                SELECT DISTINCT u.user_id 
                FROM users u
                JOIN project_members pm ON u.user_id = pm.user_id
                WHERE pm.project_id = ? AND u.role_id = 3 AND u.is_active = 1
            ");
            $plStmt->execute([$projectId]);
            $plUsers = $plStmt->fetchAll();

            // Jika tidak ditemukan di project_members, cari creator proyek jika role 3
            if (empty($plUsers)) {
                $creatorStmt = $db->prepare("SELECT user_id FROM users WHERE user_id = (SELECT created_by FROM projects WHERE project_id = ?) AND role_id = 3 AND is_active = 1");
                $creatorStmt->execute([$projectId]);
                $plUsers = $creatorStmt->fetchAll();
            }

            foreach ($plUsers as $pu) {
                if ((int)$pu['user_id'] !== (int)$user['user_id']) {
                    $notifModel->sendNotification((int)$pu['user_id'], $activityId, 'DirectorNote', $notifMsg);
                }
            }

            AuthHelper::setFlash('success', 'Catatan Direktur berhasil disimpan dan telah dinotifikasikan kepada tim pelaksana.');
        } else {
            AuthHelper::setFlash('success', 'Catatan Direktur berhasil diperbarui.');
        }

        header('Location: ' . BASE_URL . '/field-updates/detail?id=' . $updateId);
        exit;
    }

    private function handleEvidenceUpload(): ?string {
        $uploadDir = __DIR__ . '/../public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Prioritas 1: Gambar bukti (dikompres otomatis)
        if (isset($_FILES['evidence_image']) && $_FILES['evidence_image']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['evidence_image']['name'], PATHINFO_EXTENSION);
            $fileName = 'img_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            if (ImageHelper::compressAndSave($_FILES['evidence_image']['tmp_name'], $uploadDir . $fileName)) {
                return $fileName;
            }
        }

        // Prioritas 2: Dokumen pendukung
        if (isset($_FILES['evidence_doc']) && $_FILES['evidence_doc']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['evidence_doc']['name'], PATHINFO_EXTENSION);
            $fileName = 'doc_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            if (move_uploaded_file($_FILES['evidence_doc']['tmp_name'], $uploadDir . $fileName)) {
                return $fileName;
            }
        }

        // Backward compatibility jika form lain mengirim 'evidence_file'
        if (isset($_FILES['evidence_file']) && $_FILES['evidence_file']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['evidence_file']['name'], PATHINFO_EXTENSION);
            $fileName = 'evidence_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            if (ImageHelper::compressAndSave($_FILES['evidence_file']['tmp_name'], $uploadDir . $fileName)) {
                return $fileName;
            }
        }

        return null;
    }
}

