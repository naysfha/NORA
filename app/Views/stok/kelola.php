<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $aktif = $fasilitas && $fasilitas['status_verifikasi'] === 'Terverifikasi'; ?>
<div class="page-head"><div><h2>Kelola Stok Darah</h2><p>Kirim jumlah kantong terbaru. Data tampil ke publik setelah diverifikasi admin.</p></div></div>

<?php if (! $aktif): ?>
    <div class="note-box warn mb-4"><i class="bi bi-hourglass-split"></i><div>Fasilitas Anda belum diverifikasi admin sehingga belum dapat mengirim data stok.</div></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card-soft accent"><div class="card-head"><h5>Formulir Pembaruan Stok</h5></div>
        <div class="card-body-pad">
            <form method="post" action="<?= base_url('stok/simpan') ?>">
                <?= csrf_field() ?>
                <p class="text-muted small">Kosongkan golongan yang tidak diperbarui. Hanya angka yang berubah yang akan dikirim.</p>
                <div class="row g-3">
                <?php foreach ($golonganList as $g): $s = $terbaru[$g] ?? null; ?>
                    <div class="col-6">
                        <label class="form-label"><?= chip_golongan($g) ?> <span class="fw-normal text-muted">saat ini: <?= $s ? (int) $s['jumlah_kantong'] . ' (' . esc($s['status_verifikasi']) . ')' : '-' ?></span></label>
                        <input type="number" min="0" max="10000" class="form-control" name="jumlah[<?= esc($g, 'attr') ?>]" value="<?= esc(old('jumlah.' . $g)) ?>" placeholder="Jumlah kantong" <?= $aktif ? '' : 'disabled' ?>>
                    </div>
                <?php endforeach; ?>
                </div>
                <button class="btn btn-nora mt-4" <?= $aktif ? '' : 'disabled' ?>><i class="bi bi-send-fill"></i> Kirim untuk Verifikasi</button>
            </form>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card-soft"><div class="card-head"><h5>Riwayat Pengiriman</h5></div>
        <div class="table-responsive"><table class="table table-nora">
            <thead><tr><th>Gol.</th><th>Kantong</th><th>Status</th><th>Waktu</th></tr></thead>
            <tbody><?php if (! $riwayat): ?><tr><td colspan="4"><div class="empty-state"><i class="bi bi-clock-history"></i>Belum ada riwayat.</div></td></tr>
            <?php else: foreach ($riwayat as $r): ?>
                <tr><td><?= chip_golongan($r['golongan_darah']) ?></td><td class="cell-title"><?= (int) $r['jumlah_kantong'] ?></td><td><?= badge_status($r['status_verifikasi']) ?></td><td class="cell-sub"><?= tgl_waktu($r['tanggal_update']) ?></td></tr>
            <?php endforeach; endif; ?></tbody>
        </table></div></div>
    </div>
</div>
<?= $this->endSection() ?>
