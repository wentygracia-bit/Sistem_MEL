<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../models/Indicator.php';
$indModel = new Indicator();
?>

<div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <a href="<?= BASE_URL ?>/projects" class="btn btn-secondary btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Proyek
    </a>
    <div style="display: flex; gap: 8px;">
        <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Status: <?= htmlspecialchars($project['status']) ?></span>
        <span class="badge badge-info"><i class="fa-solid fa-globe"></i> <?= htmlspecialchars($project['public_status']) ?></span>
    </div>
</div>

<!-- Project Overview Card -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;">
        <div>
            <span style="font-family: monospace; font-weight: 700; color: #1e330c; background: var(--primary-light); padding: 4px 10px; border-radius: 6px; font-size: 0.85rem;">
                <?= htmlspecialchars($project['project_code']) ?>
            </span>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-top: 8px;">
                <?= htmlspecialchars($project['project_name']) ?>
            </h1>
            <p style="color: #475569; margin-top: 8px; max-width: 800px; font-size: 0.95rem;">
                <?= nl2br(htmlspecialchars($project['description'] ?? 'Tidak ada deskripsi.')) ?>
            </p>
        </div>
        <div style="background: #f8fafc; padding: 14px 20px; border-radius: var(--radius-md); border: 1px solid var(--border-color); text-align: right;">
            <div style="font-size: 0.75rem; color: var(--text-muted);">Periode Program</div>
            <div style="font-weight: 700; font-size: 0.9rem; color: #1e293b;">
                <?= $project['start_date'] ? date('d M Y', strtotime($project['start_date'])) : '-' ?> s/d <?= $project['end_date'] ? date('d M Y', strtotime($project['end_date'])) : '-' ?>
            </div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 6px;">Dibuat oleh: <?= htmlspecialchars($project['creator_name'] ?? 'Sistem') ?></div>
        </div>
    </div>
</div>

<!-- Grid Members & Activities -->
<div class="grid-cols-2">
    <!-- Project Members -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-users" style="color: var(--primary-dark);"></i> Tim / Anggota Proyek</h2>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>Peran Sistem</th>
                        <th>Peran Proyek</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($members)): ?>
                        <tr><td colspan="3" style="text-align: center; color: var(--text-muted);">Belum ada anggota.</td></tr>
                    <?php else: ?>
                        <?php foreach ($members as $m): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($m['name']) ?></strong>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?= htmlspecialchars($m['email']) ?></div>
                                </td>
                                <td><span class="badge badge-info"><?= htmlspecialchars($m['role_name']) ?></span></td>
                                <td><span class="badge badge-success"><?= htmlspecialchars($m['member_role']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (in_array($currentUser['role_id'], [2, 3, 4])): ?>
        <div style="margin-top: 20px; padding-top: 16px; border-top: 1px dashed var(--border-color);">
            <form action="<?= BASE_URL ?>/projects/add-member" method="POST" style="display: flex; gap: 10px; align-items: flex-end;">
                <input type="hidden" name="project_id" value="<?= $project['project_id'] ?>">
                <div style="flex: 2;">
                    <label class="form-label" style="font-size: 0.78rem;">Pilih Pengguna</label>
                    <select name="user_id" class="form-control" required>
                        <?php foreach ($allUsers as $u): ?>
                            <option value="<?= $u['user_id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['role_name']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 2;">
                    <label class="form-label" style="font-size: 0.78rem;">Peran dalam Proyek</label>
                    <input type="text" name="member_role" class="form-control" placeholder="Contoh: Field Coordinator" required>
                </div>
                <button type="submit" class="btn btn-secondary btn-sm" style="height: 42px;">
                    <i class="fa-solid fa-user-plus"></i> Tambah
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <!-- Quick Add Activity -->
    <?php if (in_array($currentUser['role_id'], [2, 3, 4])): ?>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-calendar-plus" style="color: var(--primary-dark);"></i> Tambah Rincian Kegiatan</h2>
        </div>
        <form action="<?= BASE_URL ?>/projects/create-activity" method="POST">
            <input type="hidden" name="project_id" value="<?= $project['project_id'] ?>">
            <div class="form-group">
                <label class="form-label">Nama Kegiatan *</label>
                <input type="text" name="activity_name" class="form-control" placeholder="Nama kegiatan teknis lapangan" required>
            </div>
            <div class="form-group">
                <label class="form-label">Lokasi Pelaksanaan</label>
                <input type="text" name="location" class="form-control" placeholder="Misal: Muara Gembong / Balai Desa">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
                <div class="form-group">
                    <label class="form-label">Mulai</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Selesai</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Pengingat (Reminder)</label>
                    <input type="date" name="reminder_date" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi / Ruang Lingkup</label>
                <textarea name="description" class="form-control" rows="2" placeholder="Tujuan & pelaksanaan"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <i class="fa-solid fa-plus"></i> Tambahkan Kegiatan
            </button>
        </form>
    </div>
    <?php endif; ?>
</div>

<!-- Activities & Indicators Breakdown -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fa-solid fa-list-check" style="color: var(--primary-dark);"></i> Rincian Kegiatan & Indikator Kinerja</h2>
        <span class="badge badge-info"><?= count($activities) ?> Kegiatan</span>
    </div>

    <?php if (empty($activities)): ?>
        <p style="text-align: center; color: var(--text-muted); padding: 30px;">Belum ada kegiatan yang didaftarkan pada proyek ini.</p>
    <?php else: ?>
        <?php foreach ($activities as $act): ?>
            <div style="border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 20px; margin-bottom: 20px; background: #ffffff;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <h3 style="font-size: 1.1rem; font-weight: 700; color: #1e293b;"><?= htmlspecialchars($act['activity_name']) ?></h3>
                            <span class="badge badge-success"><?= htmlspecialchars($act['status']) ?></span>
                        </div>
                        <div style="font-size: 0.85rem; color: #64748b; margin-top: 4px;">
                            <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($act['location'] ?: 'Lokasi belum diset') ?> | 
                            <i class="fa-regular fa-calendar"></i> Jadwal: <?= $act['start_date'] ? date('d M Y', strtotime($act['start_date'])) : '-' ?> s/d <?= $act['end_date'] ? date('d M Y', strtotime($act['end_date'])) : '-' ?>
                            <?php if ($act['reminder_date']): ?>
                                | <span style="color: #b45309;"><i class="fa-solid fa-bell"></i> Reminder: <?= date('d M Y', strtotime($act['reminder_date'])) ?></span>
                            <?php endif; ?>
                        </div>
                        <p style="font-size: 0.88rem; color: #334155; margin-top: 8px;"><?= nl2br(htmlspecialchars($act['description'] ?? '')) ?></p>
                    </div>
                </div>

                <!-- Indicators for this activity -->
                <div style="margin-top: 16px; background: #f8fafc; border-radius: var(--radius-sm); padding: 14px; border: 1px solid #e2e8f0;">
                    <div style="font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 8px; display: flex; justify-content: space-between;">
                        <span><i class="fa-solid fa-bullseye"></i> Indikator Capaian Kinerja (KPI)</span>
                    </div>

                    <?php
                    $indicators = $indModel->getByActivityId($act['activity_id']);
                    if (empty($indicators)):
                    ?>
                        <div style="font-size: 0.8rem; color: var(--text-muted); font-style: italic;">Belum ada target indikator.</div>
                    <?php else: ?>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 12px;">
                            <?php foreach ($indicators as $ind): ?>
                                <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 6px; padding: 10px 14px;">
                                    <div style="font-size: 0.85rem; font-weight: 600; color: #1e293b;"><?= htmlspecialchars($ind['indicator_name']) ?></div>
                                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-top: 4px;">
                                        <div style="font-size: 0.78rem; color: var(--text-muted);">Target:</div>
                                        <div style="font-size: 1rem; font-weight: 800; color: #2e4a16;">
                                            <?= number_format($ind['target_value'], 0, ',', '.') ?> <?= htmlspecialchars($ind['unit']) ?>
                                        </div>
                                    </div>
                                    <?php if ($ind['description']): ?>
                                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;"><?= htmlspecialchars($ind['description']) ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (in_array($currentUser['role_id'], [2, 3, 4])): ?>
                    <!-- Inline Form Add Indicator -->
                    <form action="<?= BASE_URL ?>/projects/add-indicator" method="POST" style="margin-top: 12px; display: flex; gap: 8px; flex-wrap: wrap;">
                        <input type="hidden" name="project_id" value="<?= $project['project_id'] ?>">
                        <input type="hidden" name="activity_id" value="<?= $act['activity_id'] ?>">
                        <input type="text" name="indicator_name" class="form-control" placeholder="Nama Indikator (cth: Jumlah Peserta)" style="flex: 2; min-width: 200px;" required>
                        <input type="number" step="any" name="target_value" class="form-control" placeholder="Target Angka" style="flex: 1; min-width: 100px;" required>
                        <input type="text" name="unit" class="form-control" placeholder="Satuan (Orang/Bibit)" style="flex: 1; min-width: 120px;" required>
                        <button type="submit" class="btn btn-secondary btn-sm" style="white-space: nowrap;">
                            <i class="fa-solid fa-plus"></i> Tambah KPI
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
