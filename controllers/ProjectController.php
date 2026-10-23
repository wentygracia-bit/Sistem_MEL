<?php
require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../models/Project.php';
require_once __DIR__ . '/../models/Activity.php';
require_once __DIR__ . '/../models/Indicator.php';
require_once __DIR__ . '/../models/ActivityTimeline.php';
require_once __DIR__ . '/../models/User.php';

class ProjectController {
    public function index(): void {
        AuthHelper::requireLogin();
        $user = AuthHelper::getUser();
        $projectModel = new Project();
        $activityModel = new Activity();

        $search = trim($_GET['search'] ?? '');
        $statusFilter = trim($_GET['status'] ?? '');
        $projectIdFilter = !empty($_GET['project_id']) ? (int)$_GET['project_id'] : null;

        $projects = $projectModel->getProjectsForUser(
            $user['user_id'], 
            $user['role_id'],
            $search !== '' ? $search : null,
            $statusFilter !== '' ? $statusFilter : null
        );

        // Data khusus untuk Staf Lapangan (Role 1): Daftar Kegiatan Penugasan
        $fieldActivities = [];
        if ($user['role_id'] == 1) {
            $fieldActivities = $activityModel->getActivitiesForUser(
                $user['user_id'],
                1,
                $search !== '' ? $search : null,
                $statusFilter !== '' ? $statusFilter : null,
                $projectIdFilter
            );
        }

        $pageTitle = 'Proyek & Kegiatan';
        $activeMenu = 'projects';
        require_once __DIR__ . '/../views/projects/index.php';
    }

    public function detail(): void {
        AuthHelper::requireLogin();
        $projectId = (int)($_GET['id'] ?? 0);
        $projectModel = new Project();
        $project = $projectModel->findById($projectId);

        if (!$project) {
            AuthHelper::setFlash('error', 'Proyek tidak ditemukan.');
            header('Location: ' . BASE_URL . '/projects');
            exit;
        }

        $searchActivity = trim($_GET['q'] ?? '');
        $statusActivity = trim($_GET['status_act'] ?? '');

        $activityModel = new Activity();
        $activities = $activityModel->getByProjectId(
            $projectId,
            $searchActivity !== '' ? $searchActivity : null,
            $statusActivity !== '' ? $statusActivity : null
        );
        $members = $projectModel->getMembers($projectId);

        $userModel = new User();
        $allUsers = $userModel->getAll();

        $pageTitle = 'Proyek: ' . $project['project_name'];
        $activeMenu = 'projects';
        require_once __DIR__ . '/../views/projects/detail.php';
    }

    public function create(): void {
        AuthHelper::requireRole([2, 4]); // Hanya Staf MEL & Direktur
        $user = AuthHelper::getUser();
        $projectModel = new Project();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['project_name'] ?? '');
            $desc = trim($_POST['description'] ?? '');
            $startDate = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
            $endDate = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
            $status = $_POST['status'] ?? 'Aktif';
            $publicStatus = $_POST['public_status'] ?? 'Draft';

            if (empty($name)) {
                AuthHelper::setFlash('error', 'Nama Proyek wajib diisi.');
                header('Location: ' . BASE_URL . '/projects/create');
                exit;
            }

            // Tentukan tahun berdasarkan tanggal mulai proyek jika ada, atau tahun sekarang
            $targetYear = !empty($startDate) ? (int)date('Y', strtotime($startDate)) : (int)date('Y');
            
            // Generate kode di sisi server dengan penanganan konkurensi (retry loop)
            $newId = null;
            $maxRetries = 5;
            $attempt = 0;

            while ($attempt < $maxRetries) {
                $attempt++;
                $code = $projectModel->generateNextProjectCode($targetYear);
                try {
                    $newId = $projectModel->create($code, $name, $desc, $startDate, $endDate, $status, $publicStatus, $user['user_id']);
                    break;
                } catch (PDOException $e) {
                    // Kode 23000 adalah integrity constraint violation (e.g. duplicate key)
                    if ($e->getCode() == '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                        if ($attempt >= $maxRetries) {
                            AuthHelper::setFlash('error', 'Gagal membuat kode proyek unik. Silakan coba lagi.');
                            header('Location: ' . BASE_URL . '/projects/create');
                            exit;
                        }
                        // Lanjutkan iterasi berikutnya untuk mengambil kode urutan berikutnya
                        usleep(50000); // 50ms pause
                        continue;
                    }
                    throw $e;
                } catch (Exception $e) {
                    throw $e;
                }
            }

            try {
                // Automatically add creator as member
                $projectModel->addMember($newId, $user['user_id'], $user['role_name']);

                AuthHelper::setFlash('success', 'Proyek berhasil dibuat dengan kode ' . htmlspecialchars($code) . '.');
                header('Location: ' . BASE_URL . '/projects/detail?id=' . $newId);
                exit;
            } catch (Exception $e) {
                AuthHelper::setFlash('error', 'Gagal membuat proyek: ' . $e->getMessage());
                header('Location: ' . BASE_URL . '/projects/create');
                exit;
            }
        }

        $autoProjectCode = $projectModel->generateNextProjectCode();
        $pageTitle = 'Tambah Proyek Baru';
        $activeMenu = 'projects';
        require_once __DIR__ . '/../views/projects/create.php';
    }

    public function addMember(): void {
        AuthHelper::requireRole([2, 3, 4]);
        $projectId = (int)($_POST['project_id'] ?? 0);
        $userId = (int)($_POST['user_id'] ?? 0);
        $role = trim($_POST['member_role'] ?? 'Anggota');

        if ($projectId > 0 && $userId > 0) {
            $projectModel = new Project();
            $projectModel->addMember($projectId, $userId, $role);
            AuthHelper::setFlash('success', 'Anggota berhasil ditambahkan ke proyek.');
        }
        header('Location: ' . BASE_URL . '/projects/detail?id=' . $projectId);
        exit;
    }

    public function createActivity(): void {
        AuthHelper::requireRole([2, 3, 4]); // MEL, PL, Direktur
        $projectId = (int)($_POST['project_id'] ?? 0);
        $activityName = trim($_POST['activity_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $status = $_POST['status'] ?? 'Perencanaan';

        $startDate = $_POST['start_date'] ?? null;
        $endDate = $_POST['end_date'] ?? null;
        // Hitung reminder otomatis 10 hari sebelum kegiatan dimulai
        $reminderDate = !empty($startDate) ? date('Y-m-d', strtotime($startDate . ' -10 days')) : null;

        if ($projectId > 0 && !empty($activityName)) {
            $activityModel = new Activity();
            $activityId = $activityModel->create($projectId, $activityName, $description, $location, $status);

            if ($startDate && $endDate) {
                $timelineModel = new ActivityTimeline();
                $timelineModel->save($activityId, $startDate, $endDate, $reminderDate, 'Dalam Jadwal');
            }

            AuthHelper::setFlash('success', 'Kegiatan baru berhasil ditambahkan.');
        } else {
            AuthHelper::setFlash('error', 'Nama kegiatan wajib diisi.');
        }

        header('Location: ' . BASE_URL . '/projects/detail?id=' . $projectId);
        exit;
    }

    public function addIndicator(): void {
        AuthHelper::requireRole([2, 3, 4]);
        $activityId = (int)($_POST['activity_id'] ?? 0);
        $projectId = (int)($_POST['project_id'] ?? 0);
        $indicatorName = trim($_POST['indicator_name'] ?? '');
        $targetValue = (float)($_POST['target_value'] ?? 0);
        $unit = trim($_POST['unit'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($activityId > 0 && !empty($indicatorName)) {
            $indicatorModel = new Indicator();
            $indicatorModel->create($activityId, $indicatorName, $targetValue, $unit, $description);
            AuthHelper::setFlash('success', 'Indikator kinerja berhasil ditambahkan.');
        }

        header('Location: ' . BASE_URL . '/projects/detail?id=' . $projectId);
        exit;
    }

    public function editActivity(): void {
        AuthHelper::requireRole([2, 3]); // Hanya Staf MEL & Project Leader
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/projects');
            exit;
        }

        $projectId = (int)($_POST['project_id'] ?? 0);
        $activityId = (int)($_POST['activity_id'] ?? 0);
        $activityName = trim($_POST['activity_name'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status = trim($_POST['status'] ?? 'Perencanaan');

        $startDate = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
        $endDate = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
        // Hitung reminder otomatis 10 hari sebelum kegiatan dimulai jika tidak diisi
        $reminderDate = !empty($startDate) ? date('Y-m-d', strtotime($startDate . ' -10 days')) : null;

        // Validasi status hanya menerima status yang didukung sistem saat ini
        $validStatuses = ['Perencanaan', 'Berjalan', 'Selesai'];
        if (!in_array($status, $validStatuses)) {
            $status = 'Perencanaan';
        }

        if ($activityId <= 0 || empty($activityName)) {
            AuthHelper::setFlash('error', 'Nama kegiatan wajib diisi.');
            header('Location: ' . BASE_URL . '/projects/detail?id=' . $projectId . '#act-' . $activityId);
            exit;
        }

        $activityModel = new Activity();
        $existing = $activityModel->findById($activityId);
        if (!$existing) {
            AuthHelper::setFlash('error', 'Kegiatan tidak ditemukan.');
            header('Location: ' . BASE_URL . '/projects/detail?id=' . $projectId);
            exit;
        }

        // Simpan perubahan data kegiatan
        $activityModel->update($activityId, $activityName, $description, $location, $status);

        // Perbarui data jadwal pada activity_timelines
        if ($startDate && $endDate) {
            $timelineModel = new ActivityTimeline();
            $timelineStatus = ($status === 'Selesai') ? 'Selesai' : 'Dalam Jadwal';
            $timelineModel->save($activityId, $startDate, $endDate, $reminderDate, $timelineStatus);
        }

        AuthHelper::setFlash('success', 'Rincian kegiatan berhasil diperbarui.');
        header('Location: ' . BASE_URL . '/projects/detail?id=' . $projectId . '#act-' . $activityId);
        exit;
    }

    public function editIndicator(): void {
        AuthHelper::requireRole([2, 3]); // Hanya Staf MEL & Project Leader
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/projects');
            exit;
        }

        $projectId = (int)($_POST['project_id'] ?? 0);
        $activityId = (int)($_POST['activity_id'] ?? 0);
        $indicatorId = (int)($_POST['indicator_id'] ?? 0);
        $indicatorName = trim($_POST['indicator_name'] ?? '');
        $targetValue = (float)($_POST['target_value'] ?? 0);
        $unit = trim($_POST['unit'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($indicatorId <= 0 || empty($indicatorName) || empty($unit)) {
            AuthHelper::setFlash('error', 'Nama indikator dan satuan target wajib diisi.');
            header('Location: ' . BASE_URL . '/projects/detail?id=' . $projectId . '#act-' . $activityId);
            exit;
        }

        $indicatorModel = new Indicator();
        $existing = $indicatorModel->findById($indicatorId);
        if (!$existing) {
            AuthHelper::setFlash('error', 'Indikator tidak ditemukan.');
            header('Location: ' . BASE_URL . '/projects/detail?id=' . $projectId . '#act-' . $activityId);
            exit;
        }

        // Pastikan indikator memang terdaftar di bawah kegiatan yang sesuai
        if ((int)$existing['activity_id'] !== $activityId) {
            AuthHelper::setFlash('error', 'Relasi indikator dan kegiatan tidak valid.');
            header('Location: ' . BASE_URL . '/projects/detail?id=' . $projectId . '#act-' . $activityId);
            exit;
        }

        $indicatorModel->update($indicatorId, $indicatorName, $targetValue, $unit, $description);

        AuthHelper::setFlash('success', 'Indikator KPI berhasil diperbarui.');
        header('Location: ' . BASE_URL . '/projects/detail?id=' . $projectId . '#act-' . $activityId);
        exit;
    }
}

