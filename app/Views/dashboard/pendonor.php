<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php use App\Libraries\Referensi; $maks = max(1, max($stokGol ?: [1])); ?>

<div class="page-head">
    <div><h2>Halo, <?= esc(explode(' ', session()->get('nama'))[0]) ?> <i class="bi bi-heart-fill text-danger"></i></h2><p>Terima kasih telah menjadi pendonor. Berikut informasi terbaru untuk Anda.</p></div>
</div>

<?php if (! $lengkap): ?>
    <div class="note-box warn mb-4"><i class="bi bi-person-exclamation"></i><div><strong>Profil belum lengkap.</strong> Lengkapi golongan darah dan lokasi agar Anda dapat menerima notifikasi kebutuhan darah dan memberi respons. <a href="<?= base_url('profil') ?>" class="fw-bold">Lengkapi profil</a></div></div>
<?php elseif ($profil['status_verifikasi'] !== 'Terverifikasi'): ?>
    <div class="note-box info mb-4"><i class="bi bi-hourglass-split"></i><div>Profil Anda sedang menunggu verifikasi admin. Setelah terverifikasi, profil menjadi aktif dan Anda dapat memberi respons pada permintaan darah.</div></div>
<?php endif; ?>

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-soft accent h-100"><div class="card-body-pad text-center">
            <?= $profil['golongan_darah'] ? '<span class="chip-gol besar">' . esc($profil['golongan_darah']) . '</span>' : '<span class="chip-gol kosong besar">?</span>' ?>
            <h5 class="mt-3 mb-1">Golongan Darah</h5>
            <div class="mb-2"><?= badge_status($profil['status_verifikasi']) ?></div>
            <div class="text-muted small"><i class="bi bi-geo-alt"></i> <?= esc($profil['lokasi'] ?: 'Lokasi belum diisi') ?></div>
            <a href="<?= base_url('profil') ?>" class="btn btn-ghost btn-sm mt-3">Kelola Profil</a>
        </div></div>
    </div>
    <div class="col-lg-8">
        <div class="card-soft h-100">
            <div class="card-head"><h5><i class="bi bi-broadcast-pin me-2 text-danger"></i>Permintaan untuk Golongan Anda</h5><a href="<?= base_url('sos') ?>" class="btn btn-ghost btn-sm">Semua</a></div>
            <?php if (! $cocok): ?><div class="empty-state"><i class="bi bi-emoji-smile"></i>Belum ada permintaan terverifikasi untuk golongan Anda.</div>
            <?php else: foreach ($cocok as $s): ?>
                <a class="notif-item" href="<?= base_url('sos/detail/' . $s['id_sos']) ?>">
                    <?= chip_golongan($s['golongan_darah']) ?>
                    <div class="flex-grow-1"><p class="isi fw-bold"><?= esc($s['rumah_sakit']) ?> &middot; <?= (int) $s['jumlah_dibutuhkan'] ?> kantong</p><span class="waktu"><?= esc($s['lokasi']) ?> &middot; <?= waktu_lalu($s['tanggal_pengajuan']) ?></span></div>
                    <?= badge_urgensi($s['tingkat_urgensi']) ?>
                </a>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card-soft h-100">
            <div class="card-head"><h5><i class="bi bi-calendar2-heart me-2 text-danger"></i>Kegiatan Donor Terdekat</h5><a href="<?= base_url('jadwal') ?>" class="btn btn-ghost btn-sm">Semua</a></div>
            <?php if (! $jadwal): ?><div class="empty-state"><i class="bi bi-calendar-x"></i>Belum ada jadwal dipublikasikan.</div>
            <?php else: foreach ($jadwal as $j): ?>
                <div class="event-card">
                    <div class="event-date"><div class="bln"><?= esc(strtoupper(substr(bulan_indo((int) date('n', strtotime($j['tanggal']))), 0, 3))) ?></div><div class="tgl"><?= date('d', strtotime($j['tanggal'])) ?></div><div class="hari"><?= hari_indo($j['tanggal']) ?></div></div>
                    <div class="event-info"><h6><?= esc($j['nama_kegiatan']) ?></h6><div class="meta"><span><i class="bi bi-clock"></i><?= esc($j['waktu']) ?></span><span><i class="bi bi-geo-alt"></i><?= esc($j['lokasi']) ?></span></div></div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card-soft mb-4">
            <div class="card-head"><h5>Stok Darah Saat Ini</h5><a href="<?= base_url('stok') ?>" class="btn btn-ghost btn-sm">Cari</a></div>
            <div class="card-body-pad"><div class="blood-grid">
                <?php foreach (Referensi::GOLONGAN as $g): $n = $stokGol[$g] ?? 0; ?>
                    <a class="blood-tile <?= $n ? '' : 'habis' ?>" href="<?= base_url('stok?golongan=' . urlencode($g)) ?>"><div class="gol"><?= esc($g) ?></div><div class="jml"><b><?= (int) $n ?></b> kantong</div><div class="level"><span style="width:<?= round($n / $maks * 100) ?>%"></span></div></a>
                <?php endforeach; ?>
            </div></div>
        </div>
        <?php if ($edukasi): ?>
        <div class="card-soft"><div class="card-head"><h5>Baca Edukasi</h5><a href="<?= base_url('edukasi') ?>" class="btn btn-ghost btn-sm">Semua</a></div>
            <?php foreach ($edukasi as $e): ?>
                <a class="notif-item" href="<?= base_url('edukasi/' . $e['id_edukasi']) ?>"><div class="notif-ikon"><i class="bi bi-book"></i></div><div><p class="isi fw-bold"><?= esc($e['judul']) ?></p><span class="waktu"><?= esc($e['kategori']) ?></span></div></a>
            <?php endforeach; ?></div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
