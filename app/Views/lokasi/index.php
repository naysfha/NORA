<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-head"><div><h2>Lokasi Layanan</h2><p>PMI, fasilitas kesehatan, dan unit donor darah yang sudah terverifikasi.</p></div></div>
<div class="card-soft mb-4"><div class="card-body-pad">
    <form method="get" class="row g-2"><div class="col-md-9"><div class="search-box"><i class="bi bi-search"></i><input class="form-control" name="q" placeholder="Cari nama atau alamat..." value="<?= esc($q) ?>"></div></div>
    <div class="col-md-3 d-flex gap-2"><button class="btn btn-nora flex-grow-1">Cari</button><a href="<?= base_url('lokasi') ?>" class="btn btn-ghost">Reset</a></div></form>
</div></div>
<?php if (! $lokasi): ?><div class="card-soft"><div class="empty-state"><i class="bi bi-geo"></i>Lokasi tidak ditemukan.</div></div>
<?php else: ?><div class="row g-4"><?php foreach ($lokasi as $l): ?>
    <div class="col-md-6 col-xl-4"><div class="card-soft h-100"><div class="card-body-pad">
        <div class="d-flex gap-3 align-items-start">
            <div class="stat-icon"><i class="bi bi-hospital"></i></div>
            <div><h6 class="fw-bold mb-1"><?= esc($l['nama_fasilitas']) ?></h6><div class="cell-sub"><i class="bi bi-geo-alt"></i> <?= esc($l['alamat']) ?></div>
            <?php if ($l['kontak']): ?><div class="cell-sub"><i class="bi bi-telephone"></i> <?= esc($l['kontak']) ?></div><?php endif; ?></div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <span class="badge-status <?= ($totalStok[$l['id_fasilitas']] ?? 0) > 0 ? 'terverifikasi' : 'menunggu' ?>"><?= (int) ($totalStok[$l['id_fasilitas']] ?? 0) ?> kantong tercatat</span>
            <a class="btn btn-ghost btn-sm" target="_blank" rel="noopener" href="<?= esc(maps_link($l['nama_fasilitas'], $l['alamat']), 'attr') ?>"><i class="bi bi-map"></i> Buka Peta</a>
        </div>
    </div></div></div>
<?php endforeach; ?></div><?php endif; ?>
<?= $this->endSection() ?>
