<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fa-solid fa-bell" style="color: var(--primary-dark);"></i> Notifikasi & Pengingat Jadwal (Reminders)</h2>
        <div>
            <a href="<?= BASE_URL ?>/notifications/read?id=0" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-check-double"></i> Tandai Semua Sudah Dibaca
            </a>
        </div>
    </div>

    <div style="display: flex; flex-direction: column; gap: 12px;">
        <?php if (empty($notifications)): ?>
            <p style="text-align: center; color: var(--text-muted); padding: 30px;">Tidak ada notifikasi saat ini.</p>
        <?php else: ?>
            <?php foreach ($notifications as $n): ?>
                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; padding: 16px; border-radius: var(--radius-md); background: <?= $n['is_read'] ? '#ffffff' : 'var(--primary-light)' ?>; border: 1px solid <?= $n['is_read'] ? 'var(--border-color)' : '#b5ce88' ?>;">
                    <div style="display: flex; gap: 14px; align-items: flex-start;">
                        <div style="width: 38px; height: 38px; border-radius: 50%; background: #ffffff; display: flex; align-items: center; justify-content: center; color: #2e4a16; box-shadow: var(--shadow-sm); flex-shrink: 0;">
                            <?php if ($n['notification_type'] === 'Reminder'): ?>
                                <i class="fa-solid fa-clock"></i>
                            <?php elseif ($n['notification_type'] === 'Permintaan Perbaikan'): ?>
                                <i class="fa-solid fa-triangle-exclamation" style="color: #dc2626;"></i>
                            <?php else: ?>
                                <i class="fa-solid fa-bell"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span class="badge <?= $n['notification_type'] === 'Reminder' ? 'badge-warning' : ($n['notification_type'] === 'Permintaan Perbaikan' ? 'badge-danger' : 'badge-info') ?>">
                                    <?= htmlspecialchars($n['notification_type']) ?>
                                </span>
                                <?php if (!empty($n['activity_name'])): ?>
                                    <span style="font-size: 0.85rem; font-weight: 600; color: #1e293b;">
                                        Kegiatan: <?= htmlspecialchars($n['activity_name']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div style="font-size: 0.9rem; color: #334155; margin-top: 6px;">
                                <?= htmlspecialchars($n['message']) ?>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">
                                <i class="fa-regular fa-clock"></i> <?= date('d M Y, H:i', strtotime($n['sent_at'])) ?>
                            </div>
                        </div>
                    </div>

                    <?php if (!$n['is_read']): ?>
                        <div>
                            <a href="<?= BASE_URL ?>/notifications/read?id=<?= $n['notification_id'] ?>" class="btn btn-secondary btn-sm" title="Tandai Sudah Dibaca">
                                <i class="fa-solid fa-check"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
