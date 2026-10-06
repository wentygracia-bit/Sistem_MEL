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
        $projects = $projectModel->getProjectsForUser($user['user_id'], $user['role_id']);

        $pageTitle = 'Daftar Proyek & Program';
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

        $activityModel = new Activity();
        $activities = $activityModel->getByProjectId($projectId);
        $members = $projectModel->getMembers($projectId);

        $userModel = new User();
        $allUsers = $userModel->getAll();

        $pageTitle = 'Proyek: ' . $project['project_name'];
        $activeMenu = 'projects';
        require_once __DIR__ . '/../views/projects/detail.php';
    }

    public function create(): void {
        AuthHelper::requireRole([2, 3, 4]); // MEL, PL, Direktur
        $user = AuthHelper::getUser();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['project_code'] ?? '');
            $name = trim($_POST['project_name'] ?? '');
            $desc = trim($_POST['description'] ?? '');
            $startDate = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
            $endDate = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
            $status = $_POST['status'] ?? 'Aktif';
            $publicStatus = $_POST['public_status'] ?? 'Draft';

            if (empty($code) || empty($name)) {
                AuthHelper::setFlash('error', 'Kode dan Nama Proyek wajib diisi.');
                header('Location: ' . BASE_URL . '/projects/create');
                exit;
            }

            $projectModel = new Project();
            try {
                $newId = $projectModel->create($code, $name, $desc, $startDate, $endDate, $status, $publicStatus, $user['user_id']);
                // Automatically add creator as member
                $projectModel->addMember($newId, $user['user_id'], $user['role_name']);

                AuthHelper::setFlash('success', 'Proyek berhasil dibuat.');
                header('Location: ' . BASE_URL . '/projects/detail?id=' . $newId);
                exit;
            } catch (Exception $e) {
                AuthHelper::setFlash('error', 'Gagal membuat proyek: ' . $e->getMessage());
                header('Location: ' . BASE_URL . '/projects/create');
                exit;
            }
        }

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
        $reminderDate = $_POST['reminder_date'] ?? null;

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
}
