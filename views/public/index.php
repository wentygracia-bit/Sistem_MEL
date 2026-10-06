<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Transparansi Publik - Sistem MEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body style="background-color: var(--bg-main);">
    <!-- Public Header -->
    <header class="public-header">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div class="brand-badge" style="width: 44px; height: 44px;">
                <img src="<?= BASE_URL ?>/gambar%20logo/ynki-icon.webp" alt="Logo YNKI" style="width: 100%; height: 100%; object-fit: contain; border-radius: inherit;">
            </div>
            <div>
                <div style="font-weight: 800; font-size: 1.15rem; color: #1e293b;">Sistem MEL YNKI</div>
                <div style="font-size: 0.78rem; color: var(--text-muted);">Publikasi Monitoring & Evaluasi Terbuka</div>
            </div>
        </div>
        <div>
            <a href="<?= BASE_URL ?>/login" class="btn btn-primary">
                <i class="fa-solid fa-lock"></i> Masuk Staf / Tim
            </a>
        </div>
    </header>

    <div style="max-width: 1180px; margin: 0 auto; padding: 40px 20px;">
        <!-- Hero Section -->
        <div style="background: linear-gradient(135deg, #ffffff 0%, #f4faeb 100%); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 40px; margin-bottom: 32px; box-shadow: var(--shadow-sm);">
            <div style="display: inline-block; padding: 6px 14px; background: var(--primary-light); color: #274112; border-radius: 9999px; font-weight: 600; font-size: 0.82rem; margin-bottom: 12px; border: 1px solid #b5ce88;">
                <i class="fa-solid fa-seedling"></i> Akuntabilitas & Transparansi Program
            </div>
            <h1 style="font-size: 2.2rem; font-weight: 800; color: #0f172a; margin-bottom: 12px; line-height: 1.25;">
                Portal Informasi Capaian Program Lembaga
            </h1>
            <p style="font-size: 1.05rem; color: #475569; max-width: 780px;">
                Platform pemantauan kinerja, ketercapaian target indikator, dan hasil evaluasi program yang telah disahkan secara resmi untuk keterbukaan informasi publik.
            </p>
        </div>

        <!-- Metric Summary -->
        <div class="grid-cols-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-layer-group"></i></div>
                <div>
                    <div class="stat-value"><?= count($publicProjects) ?></div>
                    <div class="stat-label">Proyek Terpublikasi</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-stamp"></i></div>
                <div>
                    <div class="stat-value"><?= count($melResults) ?></div>
                    <div class="stat-label">Hasil MEL Tersahkan</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-check-double"></i></div>
                <div>
                    <div class="stat-value">100%</div>
                    <div class="stat-label">Verifikasi Berjenjang</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                <div>
                    <div class="stat-value">YNKI</div>
                    <div class="stat-label">Lembaga Penyelenggara</div>
                </div>
            </div>
        </div>

        <!-- Public Projects List -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Daftar Proyek & Program Terbuka</h2>
                <span class="badge badge-success"><?= count($publicProjects) ?> Proyek Aktif</span>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode Proyek</th>
                            <th>Nama Proyek</th>
                            <th>Periode Pelaksanaan</th>
                            <th>Jumlah Kegiatan</th>
                            <th>Status Publikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($publicProjects)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 30px;">
                                    Belum ada proyek yang berstatus dipublikasikan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($publicProjects as $p): ?>
                                <tr>
                                    <td>
                                        <span style="font-family: monospace; font-weight: 700; color: #1e330c; background: var(--primary-light); padding: 3px 8px; border-radius: 4px;">
                                            <?= htmlspecialchars($p['project_code']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #0f172a;"><?= htmlspecialchars($p['project_name']) ?></div>
                                        <div style="font-size: 0.8rem; color: var(--text-muted);"><?= htmlspecialchars(substr($p['description'] ?? '', 0, 90)) ?>...</div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.85rem;">
                                            <i class="fa-regular fa-calendar" style="color: #94a3b8;"></i>
                                            <?= $p['start_date'] ? date('d M Y', strtotime($p['start_date'])) : '-' ?> s/d 
                                            <?= $p['end_date'] ? date('d M Y', strtotime($p['end_date'])) : '-' ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info"><?= $p['total_activities'] ?> Kegiatan</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($p['public_status']) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Disahkan MEL Results List -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Ringkasan Hasil Evaluasi & Pengesahan Direksi</h2>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID MEL</th>
                            <th>Kegiatan</th>
                            <th>Proyek</th>
                            <th>Tanggal Pengesahan</th>
                            <th>Hasil / Ringkasan</th>
                            <th>Status Pengesahan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($melResults)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 30px;">
                                    Belum ada hasil evaluasi MEL yang terbit.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($melResults as $r): ?>
                                <tr>
                                    <td><strong>#<?= $r['mel_result_id'] ?></strong></td>
                                    <td>
                                        <strong><?= htmlspecialchars($r['activity_name']) ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($r['project_name']) ?></td>
                                    <td><?= $r['approval_date'] ? date('d M Y', strtotime($r['approval_date'])) : '-' ?></td>
                                    <td>
                                        <div style="font-size: 0.85rem; max-width: 380px;"><?= htmlspecialchars($r['result_summary']) ?></div>
                                    </td>
                                    <td>
                                        <?php if ($r['status'] === 'Disetujui'): ?>
                                            <span class="badge badge-success"><i class="fa-solid fa-stamp"></i> Disahkan Direksi</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning"><?= htmlspecialchars($r['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
