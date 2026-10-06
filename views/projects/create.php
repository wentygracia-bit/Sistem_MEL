<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div style="margin-bottom: 20px;">
    <a href="<?= BASE_URL ?>/projects" class="btn btn-secondary btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Proyek
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Form Tambah Proyek Baru</h2>
    </div>

    <form action="<?= BASE_URL ?>/projects/create" method="POST" style="max-width: 720px;">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label">Kode Proyek *</label>
                <input type="text" name="project_code" class="form-control" placeholder="Contoh: PRJ-YNKI-2026-02" required>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Proyek / Program *</label>
                <input type="text" name="project_name" class="form-control" placeholder="Nama program lengkap" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi Lengkap</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Tujuan, sasaran wilayah, dan latar belakang program"></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="end_date" class="form-control">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label">Status Proyek</label>
                <select name="status" class="form-control">
                    <option value="Aktif">Aktif</option>
                    <option value="Perencanaan">Perencanaan</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Ditunda">Ditunda</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Visibilitas Publik</label>
                <select name="public_status" class="form-control">
                    <option value="Dipublikasikan">Dipublikasikan (Dapat dilihat umum di Portal Publik)</option>
                    <option value="Draft">Draft / Internal (Hanya internal tim)</option>
                </select>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Simpan Proyek
            </button>
        </div>
    </form>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
