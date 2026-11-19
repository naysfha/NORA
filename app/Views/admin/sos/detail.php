<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-head"><div><h2>Detail SOS #<?= $sos['id_sos'] ?></h2><p>Diajukan <?= tgl_waktu($sos['tanggal_pengajuan']) ?> oleh <?= esc($sos['nama_pencari']) ?></p></div><a href="<?= base_url('admin/sos') ?>" class="btn btn-ghost">Kembali</a></div>
<div class="row g-4">
<div class="col-lg-7">
    <div class="card-soft accent mb-4"><div class="card-body-pad">
        <?= view('partials/timeline_sos', ['status' => $sos['status']]) ?><hr>
        <dl class="kv">
            <dt>Golongan darah</dt><dd><?= chip_golongan($sos['golongan_darah']) ?></dd>
            <dt>Jumlah dibutuhkan</dt><dd><?= (int) $sos['jumlah_dibutuhkan'] ?> kantong</dd>
            <dt>Tingkat urgensi</dt><dd><?= badge_urgensi($sos['tingkat_urgensi']) ?></dd>
            <dt>Rumah sakit</dt><dd><?= esc($sos['rumah_sakit']) ?></dd>
            <dt>Lokasi</dt><dd><?= esc($sos['lokasi']) ?></dd>
            <dt>Kontak pencari</dt><dd><?= esc($sos['kontak']) ?> <a class="btn btn-ghost btn-sm ms-2" target="_blank" rel="noopener" href="<?= esc(wa_link($sos['kontak']), 'attr') ?>"><i class="bi bi-whatsapp"></i></a></dd>
            <dt>Akun pencari</dt><dd><?= esc($sos['nama_pencari']) ?> <span class="cell-sub"><?= esc($sos['telp_pencari']) ?></span></dd>
            <dt>Status</dt><dd><?= badge_status($sos['status']) ?></dd>
            <?php if ($sos['catatan_admin']): ?><dt>Catatan admin</dt><dd><?= esc($sos['catatan_admin']) ?></dd><?php endif; ?>
        </dl>
    </div></div>
    <?php if ($lanjut): ?>
    <div class="card-soft"><div class="card-head"><h5>Perbarui Status</h5></div><div class="card-body-pad">
        <form method="post" action="<?= base_url('admin/sos/status/' . $sos['id_sos']) ?>"><?= csrf_field() ?>
            <label class="form-label">Catatan untuk pencari <span class="fw-normal text-muted">(wajib bila menolak)</span></label>
            <textarea class="form-control mb-3" name="catatan_admin" rows="2" maxlength="255"></textarea>
            <div class="d-flex flex-wrap gap-2"><?php foreach ($lanjut as $st): ?>
                <button name="status" value="<?= esc($st, 'attr') ?>" class="btn <?= $st === 'Ditolak' ? 'btn-ghost' : 'btn-nora' ?>"><?= $st === 'Ditolak' ? '<i class="bi bi-x-lg"></i> Tolak' : '<i class="bi bi-arrow-right-circle"></i> ' . esc($st) ?></button>
            <?php endforeach; ?></div>
            <div class="form-text mt-2">Setiap perubahan status mengirim notifikasi ke pencari. Saat "Diverifikasi", pendonor dengan golongan yang sama ikut diberi tahu.</div>
        </form>
    </div></div>
    <?php endif; ?>
</div>
<div class="col-lg-5">
    <div class="card-soft"><div class="card-head"><h5><i class="bi bi-people-fill me-2 text-danger"></i>Respons Pendonor (<?= count($respons) ?>)</h5></div>
    <?php if (! $respons): ?><div class="empty-state"><i class="bi bi-hourglass"></i>Belum ada respons.</div>
    <?php else: foreach ($respons as $r): ?>
        <div class="notif-item"><div class="avatar" style="background:var(--red-100)"><?= esc(inisial($r['nama'])) ?></div>
        <div class="flex-grow-1"><p class="isi fw-bold mb-0"><?= esc($r['nama']) ?> <?= chip_golongan($r['golongan_darah']) ?></p><span class="waktu"><?= esc($r['no_telp'] ?: '-') ?> &middot; <?= waktu_lalu($r['tanggal_respons']) ?></span><?php if ($r['pesan']): ?><div class="small mt-1">&ldquo;<?= esc($r['pesan']) ?>&rdquo;</div><?php endif; ?></div></div>
    <?php endforeach; endif; ?></div>
    <button type="button" class="btn btn-ghost btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#modalHapus" data-action="<?= base_url('admin/sos/delete/' . $sos['id_sos']) ?>" data-nama="Permintaan SOS #<?= $sos['id_sos'] ?>"><i class="bi bi-trash3"></i> Hapus permintaan</button>
</div></div>
<?= $this->endSection() ?>
