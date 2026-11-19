<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php use App\Libraries\Referensi; $maks = max(1, max($stokGol ?: [1])); ?>

<div class="page-head">
    <div><h2>Dashboard Pencari Darah</h2><p>Cari stok darah, ajukan permintaan SOS, dan pantau statusnya.</p></div>
    <a href="<?= base_url('sos/buat') ?>" class="btn btn-nora"><i class="bi bi-broadcast-pin"></i> Buat Permintaan SOS</a>
</div>

<div class="note-box sos mb-4"><i class="bi bi-exclamation-octagon-fill"></i><div><?= esc(Referensi::DISCLAIMER_SOS) ?></div></div>

<div class="row g-3 mb-4">
    <?php foreach (['Menunggu Verifikasi' => ['bi-hourglass-split', 'warn'], 'Diverifikasi' => ['bi-patch-check-fill', 'ok'], 'Diproses' => ['bi-arrow-repeat', 'info'], 'Selesai' => ['bi-check2-circle', '']] as $st => $i): ?>
        <div class="col-6 col-xl-3"><div class="card-soft stat-card"><div class="stat-icon <?= $i[1] ?>"><i class="bi <?= $i[0] ?>"></i></div><div><div class="angka"><?= (int) $hitung[$st] ?></div><div class="label"><?= $st ?></div></div></div></div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card-soft mb-4">
            <div class="card-head"><h5>Permintaan SOS Terbaru</h5><a href="<?= base_url('sos/saya') ?>" class="btn btn-ghost btn-sm">Semua</a></div>
            <?php if (! $sosSaya): ?><div class="empty-state"><i class="bi bi-inbox"></i>Anda belum pernah mengajukan permintaan SOS.</div>
            <?php else: foreach ($sosSaya as $s): ?>
                <a class="notif-item" href="<?= base_url('sos/detail/' . $s['id_sos']) ?>">
                    <?= chip_golongan($s['golongan_darah']) ?>
                    <div class="flex-grow-1"><p class="isi fw-bold"><?= esc($s['rumah_sakit']) ?> &middot; <?= (int) $s['jumlah_dibutuhkan'] ?> kantong</p><span class="waktu">#<?= $s['id_sos'] ?> &middot; <?= waktu_lalu($s['tanggal_pengajuan']) ?> &middot; <?= (int) $s['jumlah_respons'] ?> pendonor merespons</span></div>
                    <?= badge_status($s['status']) ?>
                </a>
            <?php endforeach; endif; ?>
        </div>
        <div class="card-soft">
            <div class="card-head"><h5>Kegiatan Donor Terdekat</h5><a href="<?= base_url('jadwal') ?>" class="btn btn-ghost btn-sm">Semua</a></div>
            <?php if (! $jadwal): ?><div class="empty-state"><i class="bi bi-calendar-x"></i>Belum ada jadwal.</div>
            <?php else: foreach ($jadwal as $j): ?>
                <div class="event-card"><div class="event-date"><div class="bln"><?= esc(strtoupper(substr(bulan_indo((int) date('n', strtotime($j['tanggal']))), 0, 3))) ?></div><div class="tgl"><?= date('d', strtotime($j['tanggal'])) ?></div></div>
                <div class="event-info"><h6><?= esc($j['nama_kegiatan']) ?></h6><div class="meta"><span><i class="bi bi-geo-alt"></i><?= esc($j['lokasi']) ?></span></div></div></div>
            <?php endforeach; endif; ?>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card-soft">
            <div class="card-head"><h5>Cari Stok per Golongan</h5></div>
            <div class="card-body-pad"><div class="blood-grid">
                <?php foreach (Referensi::GOLONGAN as $g): $n = $stokGol[$g] ?? 0; ?>
                    <a class="blood-tile <?= $n ? '' : 'habis' ?>" href="<?= base_url('stok?golongan=' . urlencode($g)) ?>"><div class="gol"><?= esc($g) ?></div><div class="jml"><b><?= (int) $n ?></b> kantong</div><div class="level"><span style="width:<?= round($n / $maks * 100) ?>%"></span></div></a>
                <?php endforeach; ?>
            </div><p class="text-muted small mt-3 mb-0">Jumlah adalah total dari seluruh fasilitas terverifikasi dan dapat berubah sewaktu-waktu.</p></div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
