<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fa-solid fa-rotate-left" style="color: var(--primary-dark);"></i> Permintaan Perbaikan & Klarifikasi (Correction Requests)</h2>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kegiatan & Proyek</th>
                    <th>Pemohon (PL)</th>
                    <th>Ditugaskan ke (Staf)</th>
                    <th>Tanggal Permintaan</th>
                    <th>Uraian Permintaan Perbaikan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($corrections)): ?>
                    <tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 30px;">Tidak ada permintaan perbaikan aktif.</td></tr>
                <?php else: ?>
                    <?php foreach ($corrections as $c): ?>
                        <tr>
                            <td><strong>#<?= $c['correction_id'] ?></strong></td>
                            <td>
                                <div style="font-weight: 700; color: #0f172a; max-width: 200px;"><?= htmlspecialchars($c['activity_name']) ?></div>
                                <div style="font-size: 0.78rem; color: var(--text-muted);"><?= htmlspecialchars($c['project_name']) ?></div>
                            </td>
                            <td><?= htmlspecialchars($c['requested_by_name']) ?></td>
                            <td><?= htmlspecialchars($c['assigned_to_name']) ?></td>
                            <td><?= date('d M Y', strtotime($c['request_date'])) ?></td>
                            <td>
                                <div style="font-size: 0.85rem; max-width: 250px;"><?= htmlspecialchars($c['description']) ?></div>
                            </td>
                            <td>
                                <?php if ($c['status'] === 'Direspons'): ?>
                                    <span class="badge badge-success"><i class="fa-solid fa-reply"></i> Direspons</span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><i class="fa-solid fa-clock"></i> Diminta</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>/corrections/detail?id=<?= $c['correction_id'] ?>" class="btn btn-secondary btn-sm">
                                    <i class="fa-solid fa-comments"></i> Rincian & Respons
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
