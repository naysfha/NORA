<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-head"><div><h2>Permintaan Darah</h2><p>Permintaan SOS yang sudah diverifikasi admin dan membutuhkan bantuan pendonor.</p></div>
<div class="pills"><a class="pill <?= $saja ? '' : 'active' ?>" href="<?= base_url('sos') ?>">Semua golongan</a><a class="pill <?= $saja ? 'active' : '' ?>" href="<?= base_url('sos?golongan=saya') ?>">Golongan saya<?= ! empty($profil['golongan_darah']) ? ' (' . esc($profil['golongan_darah']) . ')' : '' ?></a></div></div>
<div class="note-box sos mb-4"><i class="bi bi-exclamation-octagon-fill"></i><div>Kelayakan donor dan kecocokan golongan darah tetap diperiksa tenaga kesehatan. Merespons bukan jaminan Anda dapat mendonor.</div></div>
<?php if (! $sos): ?><div class="card-soft"><div class="empty-state"><i class="bi bi-emoji-smile"></i>Tidak ada permintaan aktif saat ini.</div></div>
<?php else: ?><div class="row g-4"><?php foreach ($sos as $s): $sama = ($profil['golongan_darah'] ?? null) === $s['golongan_darah']; ?>
<div class="col-md-6 col-xl-4"><div class="card-soft h-100 <?= $sama ? 'accent' : '' ?>"><div class="card-body-pad">
    <div class="d-flex justify-content-between align-items-start mb-3"><span class="chip-gol besar"><?= esc($s['golongan_darah']) ?></span><?= badge_urgensi($s['tingkat_urgensi']) ?></div>
    <h6 class="fw-bold mb-1"><?= esc($s['rumah_sakit']) ?></h6>
    <div class="cell-sub mb-1"><i class="bi bi-geo-alt"></i> <?= esc($s['lokasi']) ?></div>
    <div class="cell-sub mb-3"><i class="bi bi-droplet"></i> <?= (int) $s['jumlah_dibutuhkan'] ?> kantong &middot; <?= waktu_lalu($s['tanggal_pengajuan']) ?> &middot; <?= badge_status($s['status']) ?></div>
    <?php if ($sama): ?><div class="mb-3"><span class="badge-status terverifikasi"><i class="bi bi-check2"></i> Golongan sama dengan Anda</span></div><?php endif; ?>
    <div class="d-flex gap-2"><a href="<?= base_url('sos/detail/' . $s['id_sos']) ?>" class="btn btn-ghost btn-sm flex-grow-1">Detail</a>
    <?php if (isset($sudah[$s['id_sos']])): ?><span class="badge-status selesai align-self-center"><i class="bi bi-check2-circle"></i> Sudah merespons</span><?php endif; ?></div>
</div></div></div><?php endforeach; ?></div><?php endif; ?>
<?= $this->endSection() ?>
