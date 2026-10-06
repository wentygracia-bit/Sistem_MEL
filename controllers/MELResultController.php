<?php
require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../models/MELResult.php';
require_once __DIR__ . '/../models/Approval.php';
require_once __DIR__ . '/../models/Evaluation.php';
require_once __DIR__ . '/../models/Activity.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/Project.php';

class MELResultController {
    public function index(): void {
        AuthHelper::requireLogin();
        $melModel = new MELResult();
        $results = $melModel->getAll();

        $evalModel = new Evaluation();
        $evaluations = $evalModel->getAll();

        $pageTitle = 'Hasil Akhir MEL & Pengesahan';
        $activeMenu = 'mel-results';
        require_once __DIR__ . '/../views/mel_results/index.php';
    }

    public function detail(): void {
        AuthHelper::requireLogin();
        $id = (int)($_GET['id'] ?? 0);
        $melModel = new MELResult();
        $result = $melModel->findById($id);

        if (!$result) {
            AuthHelper::setFlash('error', 'Data hasil MEL tidak ditemukan.');
            header('Location: ' . BASE_URL . '/mel-results');
            exit;
        }

        $pageTitle = 'Dokumen Hasil MEL #' . $result['mel_result_id'];
        $activeMenu = 'mel-results';
        require_once __DIR__ . '/../views/mel_results/detail.php';
    }

    public function create(): void {
        AuthHelper::requireRole([2]); // Staf MEL yang menyusun hasil akhir MEL (PRD 3.2 & 4.6)
        $user = AuthHelper::getUser();

        $activityId = (int)($_POST['activity_id'] ?? 0);
        $evaluationId = (int)($_POST['evaluation_id'] ?? 0);
        $summary = trim($_POST['result_summary'] ?? '');
        $submissionDate = date('Y-m-d');

        if ($activityId > 0 && $evaluationId > 0 && !empty($summary)) {
            $melModel = new MELResult();
            $newId = $melModel->createMELResult($activityId, $evaluationId, $user['user_id'], $submissionDate, $summary, 'Diajukan');

            // Notifikasi ke Direktur (Role ID 4)
            $userModel = new User();
            $directors = $userModel->getByRole(4);
            $notifModel = new Notification();
            foreach ($directors as $dir) {
                $notifModel->sendNotification(
                    $dir['user_id'],
                    $activityId,
                    'Pengesahan MEL',
                    'Dokumen Hasil MEL baru membutuhkan pengesahan dan persetujuan Direktur.'
                );
            }

            AuthHelper::setFlash('success', 'Hasil MEL berhasil disusun dan diajukan ke Direktur.');
            header('Location: ' . BASE_URL . '/mel-results/detail?id=' . $newId);
            exit;
        }

        AuthHelper::setFlash('error', 'Semua kolom wajib diisi.');
        header('Location: ' . BASE_URL . '/mel-results');
        exit;
    }

    public function approve(): void {
        AuthHelper::requireRole([4]); // Khusus Direktur (PRD 3.4 & 4.6)
        $user = AuthHelper::getUser();

        $melResultId = (int)($_POST['mel_result_id'] ?? 0);
        $decision = $_POST['decision'] ?? 'Disetujui';
        $notes = trim($_POST['notes'] ?? '');
        $date = date('Y-m-d');

        if ($melResultId > 0) {
            $approvalModel = new Approval();
            if ($decision === 'Disetujui') {
                $approvalModel->approveResult($melResultId, $user['user_id'], $date, $notes);
                AuthHelper::setFlash('success', 'Hasil MEL resmi disahkan dan disetujui Direksi.');
            } else {
                $approvalModel->rejectResult($melResultId, $user['user_id'], $date, $notes);
                AuthHelper::setFlash('error', 'Hasil MEL ditolak dengan catatan evaluasi ulang.');
            }

            // Notifikasi ke pembuat dokumen MEL
            $melModel = new MELResult();
            $doc = $melModel->findById($melResultId);
            if ($doc) {
                $notifModel = new Notification();
                $notifModel->sendNotification(
                    $doc['submitted_by'],
                    $doc['activity_id'],
                    'Keputusan Direktur',
                    'Direktur telah memberikan keputusan "' . $decision . '" terhadap Hasil MEL kegiatan: ' . $doc['activity_name']
                );
            }
        }

        header('Location: ' . BASE_URL . '/mel-results/detail?id=' . $melResultId);
        exit;
    }
}
