<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Daftar Proyek & Program</h2>
        <?php if (in_array($currentUser['role_id'], [2, 3, 4])): ?>
            <a href="<?= BASE_URL ?>/projects/create" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Tambah Proyek Baru
            </a>
        <?php endif; ?>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode Proyek</th>
                    <th>Nama Proyek & Deskripsi</th>
                    <th>Periode</th>
                    <th>Kegiatan</th>
                    <th>Status Proyek</th>
                    <th>Visibilitas Publik</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($projects)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 30px;">Belum ada proyek terdaftar.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($projects as $p): ?>
                        <tr>
                            <td><strong style="font-family: monospace; color: #1e330c;"><?= htmlspecialchars($p['project_code']) ?></strong></td>
                            <td>
                                <div style="font-weight: 700; color: #0f172a;"><?= htmlspecialchars($p['project_name']) ?></div>
                                <div style="font-size: 0.8rem; color: var(--text-muted); max-width: 320px;"><?= htmlspecialchars(substr($p['description'] ?? '', 0, 100)) ?>...</div>
                            </td>
                            <td>
                                <div style="font-size: 0.85rem;">
                                    <?= $p['start_date'] ? date('d/m/Y', strtotime($p['start_date'])) : '-' ?> - 
                                    <?= $p['end_date'] ? date('d/m/Y', strtotime($p['end_date'])) : '-' ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-info"><?= $p['total_activities'] ?> Kegiatan</span>
                            </td>
                            <td>
                                <span class="badge badge-success"><?= htmlspecialchars($p['status']) ?></span>
                            </td>
                            <td>
                                <?php if ($p['public_status'] === 'Dipublikasikan'): ?>
                                    <span class="badge badge-success"><i class="fa-solid fa-globe"></i> Publik</span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><i class="fa-solid fa-lock"></i> Internal</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>/projects/detail?id=<?= $p['project_id'] ?>" class="btn btn-secondary btn-sm">
                                    <i class="fa-solid fa-arrow-right"></i> Kelola
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
