<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fa-solid fa-scale-balanced" style="color: var(--primary-dark);"></i> Rekapitulasi Evaluasi Capaian</h2>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID Eval</th>
                    <th>Kegiatan & Proyek</th>
                    <th>Indikator Target</th>
                    <th>Evaluator (PL)</th>
                    <th>Tanggal Evaluasi</th>
                    <th>Kesesuaian</th>
                    <th>Catatan Evaluasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($evaluations)): ?>
                    <tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 30px;">Belum ada data evaluasi.</td></tr>
                <?php else: ?>
                    <?php foreach ($evaluations as $e): ?>
                        <tr>
                            <td><strong>#<?= $e['evaluation_id'] ?></strong></td>
                            <td>
                                <div style="font-weight: 700; color: #0f172a; max-width: 220px;"><?= htmlspecialchars($e['activity_name']) ?></div>
                                <div style="font-size: 0.78rem; color: var(--text-muted);"><?= htmlspecialchars($e['project_name']) ?></div>
                            </td>
                            <td>
                                <div style="font-weight: 600;"><?= htmlspecialchars($e['indicator_name']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);"><?= htmlspecialchars($e['unit']) ?></div>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($e['evaluator_name']) ?></div>
                            </td>
                            <td>
                                <div style="font-size: 0.85rem;"><?= date('d M Y', strtotime($e['evaluation_date'])) ?></div>
                            </td>
                            <td>
                                <?php if ($e['result'] === 'Sesuai'): ?>
                                    <span class="badge badge-success"><i class="fa-solid fa-check"></i> Sesuai</span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><i class="fa-solid fa-xmark"></i> Tidak Sesuai</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-size: 0.85rem; max-width: 260px;"><?= htmlspecialchars($e['notes']) ?></div>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>/field-updates/detail?id=<?= $e['update_id'] ?>" class="btn btn-secondary btn-sm">
                                    <i class="fa-solid fa-eye"></i> Laporan
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
