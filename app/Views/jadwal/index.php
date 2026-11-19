<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $kartu = static function (array $j) { ob_start(); ?>
    <div class="event-card">
        <div class="event-date"><div class="bln"><?= esc(strtoupper(substr(bulan_indo((int) date('n', strtotime($j['tanggal']))), 0, 3))) ?></div><div class="tgl"><?= date('d', strtotime($j['tanggal'])) ?></div><div class="hari"><?= hari_indo($j['tanggal']) ?></div></div>
        <div class="event-info flex-grow-1"><h6><?= esc($j['nama_kegiatan']) ?></h6>
            <div class="meta"><span><i class="bi bi-clock"></i><?= esc($j['waktu']) ?></span><span><i class="bi bi-geo-alt"></i><?= esc($j['lokasi']) ?></span><span><i class="bi bi-building"></i><?= esc($j['penyelenggara']) ?></span></div></div>
        <a class="btn btn-ghost btn-sm d-none d-md-inline-block" target="_blank" rel="noopener" href="<?= esc(maps_link($j['lokasi']), 'attr') ?>"><i class="bi bi-map"></i> Peta</a>
    </div>
<?php return ob_get_clean(); }; ?>
<div class="page-head"><div><h2>Jadwal &amp; Lokasi Donor</h2><p>Kegiatan donor darah yang sudah dipublikasikan.</p></div></div>
<div class="card-soft mb-4"><div class="card-body-pad"><form method="get" class="row g-2"><div class="col-md-9"><div class="search-box"><i class="bi bi-search"></i><input class="form-control" name="q" placeholder="Cari kegiatan, lokasi, atau penyelenggara..." value="<?= esc($q) ?>"></div></div><div class="col-md-3 d-flex gap-2"><button class="btn btn-nora flex-grow-1">Cari</button><a href="<?= base_url('jadwal') ?>" class="btn btn-ghost">Reset</a></div></form></div></div>

<div class="card-soft mb-4"><div class="card-head"><h5><i class="bi bi-calendar2-heart me-2 text-danger"></i>Kegiatan Mendatang</h5></div>
<?php if (! $jadwal): ?><div class="empty-state"><i class="bi bi-calendar-x"></i>Belum ada kegiatan mendatang.</div><?php else: foreach ($jadwal as $j) { echo $kartu($j); } endif; ?></div>

<?php if ($riwayat): ?><div class="card-soft"><div class="card-head"><h5 class="text-muted">Kegiatan Sebelumnya</h5></div><?php foreach ($riwayat as $j) { echo $kartu($j); } ?></div><?php endif; ?>
<?= $this->endSection() ?>
