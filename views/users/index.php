<?php
require_once __DIR__ . '/../layouts/header.php';
$currentUser = $currentUser ?? AuthHelper::getUser();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 800; color: #1e330c; margin-bottom: 4px;">
            <i class="fa-solid fa-users-gear" style="color: var(--primary-dark);"></i> Manajemen Pengguna
        </h1>
        <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
            Kelola data staf dan pengguna sistem, reset kata sandi, serta kontrol hak akses peran.
        </p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
        <button onclick="toggleModal('modalCreateUser')" class="btn btn-primary" style="height: 38px;">
            <i class="fa-solid fa-user-plus"></i> Tambah Pengguna Baru
        </button>
    </div>
</div>

<!-- Filter & Pencarian Pengguna -->
<div class="card" style="margin-bottom: 20px; padding: 16px 20px;">
    <form action="<?= BASE_URL ?>/users" method="GET" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 260px; position: relative;">
            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.9rem;"></i>
            <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="form-control" placeholder="Cari nama, email, organisasi, atau peran..." style="padding-left: 38px;">
        </div>
        <button type="submit" class="btn btn-secondary">
            <i class="fa-solid fa-search"></i> Cari
        </button>
        <?php if (!empty($_GET['search'])): ?>
            <a href="<?= BASE_URL ?>/users" class="btn btn-secondary" style="color: #ef4444;">
                <i class="fa-solid fa-xmark"></i> Reset
            </a>
        <?php endif; ?>
    </form>
</div>

<!-- Daftar Pengguna -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Nama & Email</th>
                    <th>Organisasi</th>
                    <th>Peran (Role)</th>
                    <th>Status</th>
                    <th style="text-align: center; width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            <i class="fa-solid fa-user-slash" style="font-size: 1.8rem; margin-bottom: 8px; display: block; color: #cbd5e1;"></i>
                            Tidak ada data pengguna ditemukan.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><span style="font-family: monospace; font-weight: 600;">#<?= $u['user_id'] ?></span></td>
                            <td>
                                <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($u['name']) ?></div>
                                <div style="font-size: 0.78rem; color: var(--text-muted);">
                                    <i class="fa-solid fa-envelope" style="font-size: 0.72rem;"></i> <?= htmlspecialchars($u['email']) ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($u['organization'] ?? '-') ?></td>
                            <td>
                                <?php
                                $badgeClass = match((int)$u['role_id']) {
                                    1 => 'badge-info',
                                    2 => 'badge-success',
                                    3 => 'badge-warning',
                                    4 => 'badge-danger',
                                    default => 'badge-secondary'
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($u['role_name'] ?? 'User') ?></span>
                            </td>
                            <td>
                                <?php if ($u['is_active']): ?>
                                    <span class="badge badge-success"><i class="fa-solid fa-check"></i> Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><i class="fa-solid fa-ban"></i> Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                    <button type="button" class="btn btn-secondary btn-sm" title="Edit Pengguna" onclick='openEditModal(<?= json_encode($u, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                        <i class="fa-solid fa-pen-to-square" style="color: #0284c7;"></i>
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" title="Reset Kata Sandi" onclick="openResetModal(<?= $u['user_id'] ?>, '<?= htmlspecialchars(addslashes($u['name'])) ?>')">
                                        <i class="fa-solid fa-key" style="color: #d97706;"></i>
                                    </button>
                                    <?php if ((int)$u['user_id'] !== (int)$currentUser['user_id']): ?>
                                        <button type="button" class="btn btn-secondary btn-sm" title="Hapus Pengguna" onclick="openDeleteModal(<?= $u['user_id'] ?>, '<?= htmlspecialchars(addslashes($u['name'])) ?>')">
                                            <i class="fa-solid fa-trash" style="color: #dc2626;"></i>
                                        </button>
                                    <?php else: ?>
                                        <span title="Akun Anda sendiri" style="color: #94a3b8; font-size: 0.75rem; padding: 4px;">
                                            <i class="fa-solid fa-user-check"></i>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Pengguna -->
<div id="modalCreateUser" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 500px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 1.15rem; font-weight: 700;"><i class="fa-solid fa-user-plus" style="color: var(--primary-dark);"></i> Tambah Pengguna Baru</h3>
            <button onclick="toggleModal('modalCreateUser')" style="background: none; border: none; font-size: 1.2rem; cursor: pointer;">&times;</button>
        </div>

        <form action="<?= BASE_URL ?>/users/create" method="POST">
            <div class="form-group">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" name="name" class="form-control" placeholder="Nama lengkap pengguna" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email Pengguna *</label>
                <input type="email" name="email" class="form-control" placeholder="alamat@email.com" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="form-group">
                    <label class="form-label">Peran (Role) *</label>
                    <select name="role_id" class="form-control" required>
                        <option value="">-- Pilih Role --</option>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['role_id'] ?>"><?= htmlspecialchars($r['role_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Kata Sandi Awal *</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 5 karakter" minlength="5" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Organisasi / Lembaga</label>
                <input type="text" name="organization" value="Yayasan Natural Kapital Indonesia" class="form-control">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" onclick="toggleModal('modalCreateUser')" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Pengguna -->
<div id="modalEditUser" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 500px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 1.15rem; font-weight: 700;"><i class="fa-solid fa-pen-to-square" style="color: #0284c7;"></i> Edit Data Pengguna</h3>
            <button onclick="toggleModal('modalEditUser')" style="background: none; border: none; font-size: 1.2rem; cursor: pointer;">&times;</button>
        </div>

        <form action="<?= BASE_URL ?>/users/edit" method="POST">
            <input type="hidden" name="user_id" id="edit_user_id">

            <div class="form-group">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email Pengguna *</label>
                <input type="email" name="email" id="edit_email" class="form-control" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="form-group">
                    <label class="form-label">Peran (Role) *</label>
                    <select name="role_id" id="edit_role_id" class="form-control" required>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['role_id'] ?>"><?= htmlspecialchars($r['role_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status Akun</label>
                    <select name="is_active" id="edit_is_active" class="form-control">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Organisasi / Lembaga</label>
                <input type="text" name="organization" id="edit_organization" class="form-control">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" onclick="toggleModal('modalEditUser')" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Perbarui Data</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Reset Password -->
<div id="modalResetPassword" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 440px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 1.15rem; font-weight: 700;"><i class="fa-solid fa-key" style="color: #d97706;"></i> Reset Kata Sandi</h3>
            <button onclick="toggleModal('modalResetPassword')" style="background: none; border: none; font-size: 1.2rem; cursor: pointer;">&times;</button>
        </div>

        <form action="<?= BASE_URL ?>/users/reset-password" method="POST">
            <input type="hidden" name="user_id" id="reset_user_id">
            <p style="font-size: 0.88rem; color: #475569; margin-bottom: 14px;">
                Setel ulang kata sandi baru untuk: <strong id="reset_user_name"></strong>
            </p>

            <div class="form-group">
                <label class="form-label">Kata Sandi Baru *</label>
                <input type="password" name="new_password" class="form-control" placeholder="Minimal 5 karakter" minlength="5" required>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" onclick="toggleModal('modalResetPassword')" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary" style="background: #d97706;"><i class="fa-solid fa-check"></i> Simpan Password Baru</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Pengguna -->
<div id="modalDeleteUser" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 440px;">
        <div style="text-align: center; padding: 10px 0 20px 0;">
            <div style="width: 50px; height: 50px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 16px auto;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h3 style="font-size: 1.2rem; font-weight: 700; color: #0f172a; margin-bottom: 8px;">Konfirmasi Hapus Pengguna</h3>
            <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 4px;">Apakah Anda yakin ingin menghapus akun pengguna:</p>
            <p id="delete_user_name_preview" style="font-weight: 700; color: #1e293b; font-size: 1rem;"></p>
            <p style="color: #dc2626; font-size: 0.8rem; margin-top: 6px;">
                Jika akun terkait riwayat data proyek/laporan, akun sebaiknya dinonaktifkan daripada dihapus.
            </p>
        </div>

        <form action="<?= BASE_URL ?>/users/delete" method="POST">
            <input type="hidden" name="user_id" id="delete_user_id">
            <div style="display: flex; justify-content: center; gap: 12px;">
                <button type="button" onclick="toggleModal('modalDeleteUser')" class="btn btn-secondary" style="min-width: 100px;">Batal</button>
                <button type="submit" class="btn btn-danger" style="min-width: 100px;"><i class="fa-solid fa-trash"></i> Hapus</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    const isOpening = (modal.style.display === 'none' || modal.style.display === '');
    modal.style.display = isOpening ? 'flex' : 'none';
}

function openEditModal(data) {
    document.getElementById('edit_user_id').value = data.user_id || '';
    document.getElementById('edit_name').value = data.name || '';
    document.getElementById('edit_email').value = data.email || '';
    document.getElementById('edit_organization').value = data.organization || '';
    document.getElementById('edit_role_id').value = data.role_id || '1';
    document.getElementById('edit_is_active').value = (data.is_active !== undefined) ? data.is_active : '1';
    toggleModal('modalEditUser');
}

function openResetModal(userId, userName) {
    document.getElementById('reset_user_id').value = userId;
    document.getElementById('reset_user_name').textContent = userName;
    toggleModal('modalResetPassword');
}

function openDeleteModal(userId, userName) {
    document.getElementById('delete_user_id').value = userId;
    document.getElementById('delete_user_name_preview').textContent = userName;
    toggleModal('modalDeleteUser');
}
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
