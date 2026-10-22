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
    <div class="card-header" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 14px;">
        <div>
            <h2 class="card-title"><i class="fa-solid fa-list-check" style="color: var(--primary-dark);"></i> Rincian Kegiatan & Indikator Kinerja</h2>
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 2px;">Kelola kegiatan dan target indikator capaian untuk proyek ini.</div>
        </div>
        
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <!-- Form Pencarian & Filter Kegiatan di Proyek Ini -->
            <form action="<?= BASE_URL ?>/projects/detail" method="GET" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                <input type="hidden" name="id" value="<?= (int)$project['project_id'] ?>">
                
                <div style="position: relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem;"></i>
                    <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Cari nama kegiatan..." class="form-control" style="padding-left: 32px; width: 200px; height: 38px;">
                </div>

                <select name="status_act" class="form-control" style="width: 150px; height: 38px;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="Perencanaan" <?= (isset($_GET['status_act']) && $_GET['status_act'] === 'Perencanaan') ? 'selected' : '' ?>>Perencanaan</option>
                    <option value="Berjalan" <?= (isset($_GET['status_act']) && $_GET['status_act'] === 'Berjalan') ? 'selected' : '' ?>>Berjalan</option>
                    <option value="Selesai" <?= (isset($_GET['status_act']) && $_GET['status_act'] === 'Selesai') ? 'selected' : '' ?>>Selesai</option>
                </select>

                <button type="submit" class="btn btn-secondary btn-sm" style="height: 38px;" title="Cari kegiatan">
                    <i class="fa-solid fa-search"></i>
                </button>
                <?php if (!empty($_GET['q']) || !empty($_GET['status_act'])): ?>
                    <a href="<?= BASE_URL ?>/projects/detail?id=<?= (int)$project['project_id'] ?>" class="btn btn-secondary btn-sm" style="height: 38px; display: inline-flex; align-items: center;" title="Reset Filter Kegiatan">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                <?php endif; ?>
            </form>

            <span class="badge badge-info"><?= count($activities) ?> Kegiatan</span>
        </div>
    </div>

    <?php if (!empty($_GET['q']) || !empty($_GET['status_act'])): ?>
        <div style="padding: 10px 16px; background-color: #f8fafc; border-bottom: 1px solid var(--border-color); font-size: 0.85rem; color: #475569; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
            <span>
                Filter aktif kegiatan: 
                <?php if (!empty($_GET['q'])): ?><strong>"<?= htmlspecialchars($_GET['q']) ?>"</strong> <?php endif; ?>
                <?php if (!empty($_GET['status_act'])): ?><span class="badge badge-info" style="margin-left: 4px;"><?= htmlspecialchars($_GET['status_act']) ?></span><?php endif; ?>
                (<?= count($activities) ?> kegiatan ditemukan)
            </span>
            <a href="<?= BASE_URL ?>/projects/detail?id=<?= (int)$project['project_id'] ?>" style="color: #274312; font-weight: 600; text-decoration: underline;">Reset filter kegiatan</a>
        </div>
    <?php endif; ?>

    <?php if (empty($activities)): ?>
        <p style="text-align: center; color: var(--text-muted); padding: 35px 20px;">
            <i class="fa-solid fa-circle-info" style="font-size: 1.8rem; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
            <?php if (!empty($_GET['q']) || !empty($_GET['status_act'])): ?>
                Tidak ada kegiatan yang sesuai dengan kriteria pencarian Anda pada proyek ini.
                <div style="margin-top: 8px;">
                    <a href="<?= BASE_URL ?>/projects/detail?id=<?= (int)$project['project_id'] ?>" class="btn btn-secondary btn-sm">Reset Filter</a>
                </div>
            <?php else: ?>
                Belum ada kegiatan yang didaftarkan pada proyek ini.
            <?php endif; ?>
        </p>
    <?php else: ?>
        <?php foreach ($activities as $act): ?>
            <div id="act-<?= $act['activity_id'] ?>" style="border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 20px; margin-bottom: 20px; background: #ffffff;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 14px;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <h3 style="font-size: 1.1rem; font-weight: 700; color: #1e293b; margin: 0;"><?= htmlspecialchars($act['activity_name']) ?></h3>
                            <span class="badge badge-success"><?= htmlspecialchars($act['status']) ?></span>
                            <?php if (in_array((int)$currentUser['role_id'], [2, 3])): ?>
                                <button type="button" class="btn btn-secondary btn-sm" style="padding: 2px 8px; font-size: 0.75rem; border-color: #cbd5e1;" title="Edit Rincian Kegiatan" onclick='openEditActivityModal(<?= json_encode($act, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                    <i class="fa-solid fa-pen-to-square" style="color: #0284c7;"></i> Edit Kegiatan
                                </button>
                            <?php endif; ?>
                        </div>
                        <div style="font-size: 0.85rem; color: #64748b; margin-top: 6px;">
                            <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($act['location'] ?: 'Lokasi belum diset') ?> | 
                            <i class="fa-regular fa-calendar"></i> Jadwal: <?= $act['start_date'] ? date('d M Y', strtotime($act['start_date'])) : '-' ?> s/d <?= $act['end_date'] ? date('d M Y', strtotime($act['end_date'])) : '-' ?>
                            <?php if ($act['reminder_date']): ?>
                                | <span style="color: #b45309;"><i class="fa-solid fa-bell"></i> Reminder: <?= date('d M Y', strtotime($act['reminder_date'])) ?></span>
                            <?php endif; ?>
                        </div>
                        <p style="font-size: 0.88rem; color: #334155; margin-top: 8px;"><?= nl2br(htmlspecialchars($act['description'] ?? '')) ?></p>
                    </div>

                    <div style="display: flex; gap: 8px; align-items: center;">
                        <a href="<?= BASE_URL ?>/field-updates?activity_id=<?= (int)$act['activity_id'] ?>" class="btn btn-secondary btn-sm" title="Lihat Riwayat Laporan Terkait Kegiatan Ini">
                            <i class="fa-solid fa-file-lines"></i> Riwayat Laporan
                        </a>
                        <?php if ((int)$currentUser['role_id'] === 1): // Khusus Staf Lapangan ?>
                            <a href="<?= BASE_URL ?>/field-updates" class="btn btn-primary btn-sm" title="Input Laporan untuk Kegiatan Ini">
                                <i class="fa-solid fa-plus"></i> Input Laporan
                            </a>
                        <?php endif; ?>
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
                                <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 6px; padding: 10px 14px; position: relative;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;">
                                        <div style="font-size: 0.85rem; font-weight: 600; color: #1e293b;"><?= htmlspecialchars($ind['indicator_name']) ?></div>
                                        <?php if (in_array((int)$currentUser['role_id'], [2, 3])): ?>
                                            <button type="button" class="btn btn-secondary btn-sm" style="padding: 2px 6px; font-size: 0.72rem; border-color: #cbd5e1;" title="Edit Indikator KPI" onclick='openEditIndicatorModal(<?= json_encode($ind, JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= (int)$act['activity_id'] ?>)'>
                                                <i class="fa-solid fa-pen-to-square" style="color: #0284c7;"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
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

                    <?php if (in_array((int)$currentUser['role_id'], [2, 3])): ?>
                    <!-- Inline Form Add Indicator -->
                    <form action="<?= BASE_URL ?>/projects/add-indicator" method="POST" style="margin-top: 12px; display: flex; gap: 8px; flex-wrap: wrap;">
                        <input type="hidden" name="project_id" value="<?= $project['project_id'] ?>">
                        <input type="hidden" name="activity_id" value="<?= $act['activity_id'] ?>">
                        <input type="text" name="indicator_name" class="form-control" placeholder="Nama Indikator (cth: Jumlah Peserta)" style="flex: 2; min-width: 200px;" required>
                        <input type="number" step="any" name="target_value" class="form-control" placeholder="Target Angka" style="flex: 1; min-width: 100px;" required>
                        <input type="text" name="unit" class="form-control" placeholder="Satuan (Orang/Bibit)" style="flex: 1; min-width: 120px;" required>
                        <button type="submit" class="btn btn-secondary btn-sm" style="white-space: nowrap;">
                            <i class="fa-solid fa-plus"></i> Indikator
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal Edit Kegiatan -->
<div id="modalEditActivity" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 600px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 1.15rem; font-weight: 700;"><i class="fa-solid fa-pen-to-square" style="color: var(--primary-dark);"></i> Edit Rincian Kegiatan</h3>
            <button type="button" onclick="toggleModal('modalEditActivity')" style="background: none; border: none; font-size: 1.2rem; cursor: pointer;">&times;</button>
        </div>

        <form action="<?= BASE_URL ?>/projects/edit-activity" method="POST">
            <input type="hidden" name="project_id" value="<?= (int)$project['project_id'] ?>">
            <input type="hidden" name="activity_id" id="edit_act_id">

            <div class="form-group">
                <label class="form-label">Nama Kegiatan *</label>
                <input type="text" name="activity_name" id="edit_act_name" class="form-control" required>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label class="form-label">Lokasi Pelaksanaan</label>
                    <input type="text" name="location" id="edit_act_location" class="form-control" placeholder="Lokasi lapangan">
                </div>
                <div class="form-group">
                    <label class="form-label">Status Kegiatan</label>
                    <select name="status" id="edit_act_status" class="form-control">
                        <option value="Perencanaan">Perencanaan</option>
                        <option value="Berjalan">Berjalan</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
                <div class="form-group">
                    <label class="form-label">Mulai *</label>
                    <input type="date" name="start_date" id="edit_act_start_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Selesai *</label>
                    <input type="date" name="end_date" id="edit_act_end_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Pengingat (Reminder)</label>
                    <input type="date" name="reminder_date" id="edit_act_reminder_date" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi / Ruang Lingkup</label>
                <textarea name="description" id="edit_act_description" class="form-control" rows="3" placeholder="Tujuan dan ruang lingkup kegiatan"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" onclick="toggleModal('modalEditActivity')" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Indikator KPI -->
<div id="modalEditIndicator" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 520px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 1.15rem; font-weight: 700;"><i class="fa-solid fa-pen-to-square" style="color: var(--primary-dark);"></i> Edit Indikator Kinerja (KPI)</h3>
            <button type="button" onclick="toggleModal('modalEditIndicator')" style="background: none; border: none; font-size: 1.2rem; cursor: pointer;">&times;</button>
        </div>

        <form action="<?= BASE_URL ?>/projects/edit-indicator" method="POST">
            <input type="hidden" name="project_id" value="<?= (int)$project['project_id'] ?>">
            <input type="hidden" name="activity_id" id="edit_ind_activity_id">
            <input type="hidden" name="indicator_id" id="edit_ind_id">

            <div class="form-group">
                <label class="form-label">Nama Indikator *</label>
                <input type="text" name="indicator_name" id="edit_ind_name" class="form-control" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label class="form-label">Target Nilai *</label>
                    <input type="number" step="any" name="target_value" id="edit_ind_target" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Satuan Target *</label>
                    <input type="text" name="unit" id="edit_ind_unit" class="form-control" placeholder="Misal: Bibit, Orang" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi Indikator</label>
                <textarea name="description" id="edit_ind_description" class="form-control" rows="2" placeholder="Keterangan tambahan parameter"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" onclick="toggleModal('modalEditIndicator')" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan Indikator</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.style.display = (modal.style.display === 'none' || modal.style.display === '') ? 'flex' : 'none';
}

function openEditActivityModal(data) {
    document.getElementById('edit_act_id').value = data.activity_id || '';
    document.getElementById('edit_act_name').value = data.activity_name || '';
    document.getElementById('edit_act_location').value = data.location || '';
    document.getElementById('edit_act_status').value = data.status || 'Perencanaan';
    document.getElementById('edit_act_start_date').value = data.start_date || '';
    document.getElementById('edit_act_end_date').value = data.end_date || '';
    document.getElementById('edit_act_reminder_date').value = data.reminder_date || '';
    document.getElementById('edit_act_description').value = data.description || '';
    toggleModal('modalEditActivity');
}

function openEditIndicatorModal(ind, activityId) {
    document.getElementById('edit_ind_id').value = ind.indicator_id || '';
    document.getElementById('edit_ind_activity_id').value = activityId || '';
    document.getElementById('edit_ind_name').value = ind.indicator_name || '';
    document.getElementById('edit_ind_target').value = ind.target_value || '';
    document.getElementById('edit_ind_unit').value = ind.unit || '';
    document.getElementById('edit_ind_description').value = ind.description || '';
    toggleModal('modalEditIndicator');
}
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
