<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $lihatKontak = $pemilik || $responsku; ?>
<div class="page-head"><div><h2>Permintaan SOS #<?= $sos['id_sos'] ?></h2><p>Diajukan <?= tgl_waktu($sos['tanggal_pengajuan']) ?></p></div>
<a href="<?= base_url($pemilik ? 'sos/saya' : 'sos') ?>" class="btn btn-ghost">Kembali</a></div>
<div class="row g-4">
<div class="col-lg-7">
    <div class="card-soft accent mb-4"><div class="card-body-pad">
        <?= view('partials/timeline_sos', ['status' => $sos['status']]) ?>
        <hr>
        <dl class="kv">
            <dt>Golongan darah</dt><dd><?= chip_golongan($sos['golongan_darah']) ?></dd>
            <dt>Jumlah dibutuhkan</dt><dd><?= (int) $sos['jumlah_dibutuhkan'] ?> kantong</dd>
            <dt>Tingkat urgensi</dt><dd><?= badge_urgensi($sos['tingkat_urgensi']) ?></dd>
            <dt>Rumah sakit</dt><dd><?= esc($sos['rumah_sakit']) ?></dd>
            <dt>Lokasi</dt><dd><?= esc($sos['lokasi']) ?></dd>
            <dt>Status</dt><dd><?= badge_status($sos['status']) ?></dd>
            <dt>Kontak</dt><dd><?php if ($lihatKontak): ?><?= esc($sos['kontak']) ?> <a class="btn btn-ghost btn-sm ms-2" target="_blank" rel="noopener" href="<?= esc(wa_link($sos['kontak']), 'attr') ?>"><i class="bi bi-whatsapp"></i> WhatsApp</a><?php else: ?><span class="text-muted fw-normal">Tampil setelah Anda memberi respons</span><?php endif; ?></dd>
            <?php if ($sos['catatan_admin']): ?><dt>Catatan admin</dt><dd><?= esc($sos['catatan_admin']) ?></dd><?php endif; ?>
        </dl>
    </div></div>
    <div class="note-box sos"><i class="bi bi-exclamation-octagon-fill"></i><div><?= esc(\App\Libraries\Referensi::DISCLAIMER_SOS) ?></div></div>
</div>
<div class="col-lg-5">
<?php if ($pemilik): ?>
    <div class="card-soft"><div class="card-head"><h5><i class="bi bi-people-fill me-2 text-danger"></i>Pendonor yang Merespons (<?= count($daftarRespons) ?>)</h5></div>
    <?php if (! $daftarRespons): ?><div class="empty-state"><i class="bi bi-hourglass"></i>Belum ada pendonor yang merespons.<?= $sos['status'] === 'Menunggu Verifikasi' ? '<br>Permintaan masih menunggu verifikasi admin.' : '' ?></div>
    <?php else: foreach ($daftarRespons as $r): ?>
        <div class="notif-item"><div class="avatar" style="background:var(--red-100)"><?= esc(inisial($r['nama'])) ?></div>
        <div class="flex-grow-1"><p class="isi fw-bold mb-0"><?= esc($r['nama']) ?> <?= chip_golongan($r['golongan_darah']) ?></p>
        <span class="waktu"><?= esc($r['lokasi'] ?: '-') ?> &middot; <?= waktu_lalu($r['tanggal_respons']) ?></span>
        <?php if ($r['pesan']): ?><div class="small mt-1">&ldquo;<?= esc($r['pesan']) ?>&rdquo;</div><?php endif; ?></div>
        <?php if ($r['no_telp']): ?><a class="btn-icon ok" target="_blank" rel="noopener" href="<?= esc(wa_link($r['no_telp']), 'attr') ?>" title="Hubungi"><i class="bi bi-whatsapp"></i></a><?php endif; ?></div>
    <?php endforeach; endif; ?></div>
<?php else: ?>
    <div class="card-soft accent"><div class="card-head"><h5>Respons Anda</h5></div><div class="card-body-pad">
    <?php if ($responsku): ?>
        <div class="note-box info mb-3"><i class="bi bi-check-circle-fill"></i><div>Anda sudah menyatakan bersedia membantu pada <?= tgl_waktu($responsku['tanggal_respons']) ?>. Silakan hubungi pencari melalui kontak di sebelah kiri.</div></div>
        <form method="post" action="<?= base_url('sos/batal-respons/' . $sos['id_sos']) ?>"><?= csrf_field() ?><button class="btn btn-ghost">Batalkan Respons</button></form>
    <?php elseif (empty($profil['golongan_darah']) || ($profil['status_verifikasi'] ?? '') !== 'Terverifikasi'): ?>
        <div class="note-box warn"><i class="bi bi-person-exclamation"></i><div>Lengkapi profil pendonor dan tunggu verifikasi admin sebelum dapat merespons. <a href="<?= base_url('profil') ?>" class="fw-bold">Buka profil</a></div></div>
    <?php else: ?>
        <form method="post" action="<?= base_url('sos/respons/' . $sos['id_sos']) ?>"><?= csrf_field() ?>
            <label class="form-label">Pesan untuk pencari (opsional)</label>
            <textarea class="form-control mb-3" name="pesan" rows="3" maxlength="255" placeholder="contoh: Saya bisa datang sore ini."></textarea>
            <button class="btn btn-nora w-100"><i class="bi bi-heart-pulse-fill"></i> Saya Bersedia Membantu</button>
        </form>
    <?php endif; ?></div></div>
<?php endif; ?>
</div></div>
<?= $this->endSection() ?>
