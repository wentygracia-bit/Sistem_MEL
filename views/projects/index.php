<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<?php if ($currentUser['role_id'] == 1): // Tampilan Khusus Staf Lapangan: Daftar Kegiatan Penugasan ?>
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 14px;">
        <div>
            <h2 class="card-title"><i class="fa-solid fa-list-check" style="color: var(--primary-dark);"></i> Kegiatan Penugasan Lapangan</h2>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">Daftar seluruh kegiatan di bawah proyek tempat Anda ditugaskan, lengkap dengan target, capaian terverifikasi, dan tenggat waktu.</p>
        </div>

        <!-- Filter & Pencarian Kegiatan -->
        <form action="<?= BASE_URL ?>/projects" method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <div style="position: relative;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem;"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Cari kegiatan/lokasi..." class="form-control" style="padding-left: 32px; width: 220px; height: 38px;">
            </div>

            <select name="status" class="form-control" style="width: 140px; height: 38px;" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="Berjalan" <?= ($_GET['status'] ?? '') === 'Berjalan' ? 'selected' : '' ?>>Berjalan</option>
                <option value="Selesai" <?= ($_GET['status'] ?? '') === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                <option value="Perencanaan" <?= ($_GET['status'] ?? '') === 'Perencanaan' ? 'selected' : '' ?>>Perencanaan</option>
            </select>

            <select name="project_id" class="form-control" style="width: 180px; height: 38px;" onchange="this.form.submit()">
                <option value="">Semua Proyek</option>
                <?php foreach ($projects as $pr): ?>
                    <option value="<?= $pr['project_id'] ?>" <?= (isset($_GET['project_id']) && (int)$_GET['project_id'] === (int)$pr['project_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($pr['project_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn btn-secondary btn-sm" style="height: 38px;">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
            <?php if (!empty($_GET['search']) || !empty($_GET['status']) || !empty($_GET['project_id'])): ?>
                <a href="<?= BASE_URL ?>/projects" class="btn btn-secondary btn-sm" style="height: 38px; display: inline-flex; align-items: center;" title="Reset Filter">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Kegiatan & Proyek</th>
                    <th>Lokasi</th>
                    <th>Jadwal & Deadline</th>
                    <th>Target</th>
                    <th>Realisasi Terverifikasi</th>
                    <th>Progress Capaian</th>
                    <th>Status</th>
                    <th style="text-align: center; min-width: 130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($fieldActivities)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 40px 20px;">
                            <i class="fa-solid fa-circle-info" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
                            Tidak ada kegiatan yang sesuai dengan kriteria pencarian/filter Anda.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($fieldActivities as $act): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #0f172a; font-size: 0.95rem;"><?= htmlspecialchars($act['activity_name']) ?></div>
                                <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">
                                    <span style="font-family: monospace; font-weight: 600; color: #2e4a16;"><?= htmlspecialchars($act['project_code']) ?></span> &bull; <?= htmlspecialchars($act['project_name']) ?>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.85rem; color: #334155;">
                                    <i class="fa-solid fa-location-dot" style="color: #94a3b8; font-size: 0.8rem;"></i>
                                    <?= htmlspecialchars($act['location'] ?: 'Lapangan') ?>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.85rem;">
                                    <div><i class="fa-regular fa-calendar" style="font-size: 0.75rem; color: #64748b;"></i> <?= $act['start_date'] ? date('d/m/Y', strtotime($act['start_date'])) : '-' ?></div>
                                    <div style="font-weight: 600; color: #b45309; margin-top: 2px;">
                                        <i class="fa-solid fa-flag-checkered" style="font-size: 0.75rem;"></i> <?= $act['end_date'] ? date('d/m/Y', strtotime($act['end_date'])) : '-' ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: #1e293b;">
                                    <?= number_format($act['total_target'], 0, ',', '.') ?>
                                </span>
                                <span style="font-size: 0.75rem; color: var(--text-muted);"><?= htmlspecialchars($act['unit']) ?></span>
                            </td>
                            <td>
                                <span style="font-weight: 800; color: #2e4a16; font-size: 0.95rem;">
                                    <?= number_format($act['verified_realization'], 0, ',', '.') ?>
                                </span>
                                <span style="font-size: 0.75rem; color: var(--text-muted);"><?= htmlspecialchars($act['unit']) ?></span>
                                <?php if ($act['total_submitted_realization'] > $act['verified_realization']): ?>
                                    <div style="font-size: 0.72rem; color: #d97706;" title="Menunggu evaluasi/verifikasi resmi">
                                        (<?= number_format($act['total_submitted_realization'], 0, ',', '.') ?> diajukan)
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-weight: 700; font-size: 0.85rem; color: #1e330c;"><?= $act['calculated_progress'] ?>%</div>
                                <div class="progress-bar-bg" style="width: 80px; height: 6px; margin-top: 4px;">
                                    <div class="progress-bar-fill" style="width: <?= min(100, $act['calculated_progress']) ?>%;"></div>
                                </div>
                            </td>
                            <td>
                                <?php if ($act['display_status'] === 'Selesai'): ?>
                                    <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Selesai</span>
                                <?php elseif ($act['display_status'] === 'Terlambat'): ?>
                                    <span class="badge badge-danger"><i class="fa-solid fa-triangle-exclamation"></i> Terlambat</span>
                                <?php elseif ($act['display_status'] === 'Belum Dimulai'): ?>
                                    <span class="badge badge-secondary"><i class="fa-solid fa-clock"></i> Belum Dimulai</span>
                                <?php else: ?>
                                    <span class="badge badge-info"><i class="fa-solid fa-person-running"></i> Berjalan</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                    <a href="<?= BASE_URL ?>/projects/detail?id=<?= $act['project_id'] ?>#act-<?= $act['activity_id'] ?>" class="btn btn-secondary btn-sm" title="Detail Kegiatan">
                                        <i class="fa-solid fa-circle-info"></i> Detail
                                    </a>
                                    <a href="<?= BASE_URL ?>/field-updates?search=<?= urlencode($act['activity_name']) ?>" class="btn btn-secondary btn-sm" title="Lihat Laporan Terkait">
                                        <i class="fa-solid fa-file-lines"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Daftar Proyek & Program</h2>
        <?php if (in_array($currentUser['role_id'], [2, 4])): // Hanya Staf MEL & Direktur ?>
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
                    <th class="text-center">Kegiatan</th>
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
                            <td class="text-center">
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
