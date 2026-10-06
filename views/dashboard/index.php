<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- KPI Stats Grid -->
<div class="grid-cols-4">
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-folder"></i></div>
        <div>
            <div class="stat-value"><?= count($projects) ?></div>
            <div class="stat-label">Proyek Dikelola</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-file-lines"></i></div>
        <div>
            <div class="stat-value"><?= count($recentUpdates) ?></div>
            <div class="stat-label">Laporan Masuk</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-rotate-left"></i></div>
        <div>
            <div class="stat-value"><?= count($recentCorrections) ?></div>
            <div class="stat-label">Perbaikan Aktif</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-stamp"></i></div>
        <div>
            <div class="stat-value"><?= count($melResults) ?></div>
            <div class="stat-label">Hasil MEL</div>
        </div>
    </div>
</div>

<div class="grid-cols-2">
    <!-- Projects Section -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Proyek & Program Berjalan</h2>
            <?php if (in_array($currentUser['role_id'], [2, 3, 4])): ?>
                <a href="<?= BASE_URL ?>/projects/create" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus"></i> Tambah Proyek
                </a>
            <?php endif; ?>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Proyek</th>
                        <th>Kegiatan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($projects)): ?>
                        <tr><td colspan="5" style="text-align: center; color: var(--text-muted);">Belum ada proyek.</td></tr>
                    <?php else: ?>
                        <?php foreach ($projects as $prj): ?>
                            <tr>
                                <td><span style="font-family: monospace; font-weight: 600;"><?= htmlspecialchars($prj['project_code']) ?></span></td>
                                <td>
                                    <div style="font-weight: 600;"><?= htmlspecialchars($prj['project_name']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?= htmlspecialchars($prj['creator_name'] ?? 'YNKI') ?></div>
                                </td>
                                <td><span class="badge badge-info"><?= $prj['total_activities'] ?> Kegiatan</span></td>
                                <td><span class="badge badge-success"><?= htmlspecialchars($prj['status']) ?></span></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/projects/detail?id=<?= $prj['project_id'] ?>" class="btn btn-secondary btn-sm">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Field Updates -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Laporan Lapangan Terkini</h2>
            <a href="<?= BASE_URL ?>/field-updates" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Pelapor</th>
                        <th>Kegiatan</th>
                        <th>Capaian</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentUpdates)): ?>
                        <tr><td colspan="5" style="text-align: center; color: var(--text-muted);">Belum ada laporan lapangan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentUpdates as $up): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600;"><?= htmlspecialchars($up['reporter_name']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?= date('d M Y', strtotime($up['update_date'])) ?></div>
                                </td>
                                <td>
                                    <div style="font-size: 0.85rem; max-width: 160px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?= htmlspecialchars($up['activity_name']) ?>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight: 700; color: #1e330c;"><?= number_format($up['realization_value'], 0, ',', '.') ?></span>
                                    <?php if ($up['progress_percentage']): ?>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);"><?= $up['progress_percentage'] ?>%</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($up['status'] === 'Dievaluasi'): ?>
                                        <span class="badge badge-success"><?= htmlspecialchars($up['status']) ?></span>
                                    <?php elseif ($up['status'] === 'Perlu Perbaikan'): ?>
                                        <span class="badge badge-danger"><?= htmlspecialchars($up['status']) ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-warning"><?= htmlspecialchars($up['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/field-updates/detail?id=<?= $up['update_id'] ?>" class="btn btn-secondary btn-sm">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
