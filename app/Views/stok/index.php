<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $maks = max(1, max($totalGol ?: [1])); ?>
<div class="page-head"><div><h2>Cari Stok Darah</h2><p>Cari ketersediaan berdasarkan golongan darah dan lokasi fasilitas.</p></div></div>

<div class="card-soft mb-4"><div class="card-body-pad">
    <div class="blood-grid mb-3" style="grid-template-columns:repeat(auto-fit,minmax(110px,1fr))">
        <?php foreach ($golonganList as $g): $n = $totalGol[$g] ?? 0; ?>
            <a class="blood-tile <?= $n ? '' : 'habis' ?> <?= $filter['golongan'] === $g ? 'aktif' : '' ?>" href="<?= base_url('stok?golongan=' . urlencode($g) . ($filter['lokasi'] ? '&lokasi=' . urlencode($filter['lokasi']) : '')) ?>">
                <div class="drop"><i class="bi bi-droplet-fill"></i></div><div class="gol"><?= esc($g) ?></div><div class="jml"><b><?= (int) $n ?></b> kantong</div>
                <div class="level"><span style="width:<?= round($n / $maks * 100) ?>%"></span></div>
            </a>
        <?php endforeach; ?>
    </div>
    <form method="get" class="row g-2">
        <div class="col-md-3"><select class="form-select" name="golongan"><option value="">Semua golongan</option><?php foreach ($golonganList as $g): ?><option value="<?= esc($g, 'attr') ?>" <?= $filter['golongan'] === $g ? 'selected' : '' ?>><?= esc($g) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-6"><div class="search-box"><i class="bi bi-geo-alt"></i><input class="form-control" name="lokasi" placeholder="Nama fasilitas atau lokasi..." value="<?= esc($filter['lokasi']) ?>"></div></div>
        <div class="col-md-3 d-flex gap-2"><button class="btn btn-nora flex-grow-1"><i class="bi bi-search"></i> Cari</button><a href="<?= base_url('stok') ?>" class="btn btn-ghost">Reset</a></div>
    </form>
</div></div>

<?php if (! $perFasilitas): ?>
    <div class="card-soft"><div class="empty-state"><i class="bi bi-droplet"></i>Tidak ada data stok terverifikasi yang cocok dengan pencarian Anda.</div></div>
<?php else: foreach ($perFasilitas as $grup): $info = $grup['info']; ?>
    <div class="card-soft mb-3">
        <div class="card-head">
            <div><h5><i class="bi bi-hospital me-2 text-danger"></i><?= esc($info['nama_fasilitas']) ?></h5><div class="cell-sub"><?= esc($info['alamat']) ?><?= $info['kontak'] ? ' &middot; ' . esc($info['kontak']) : '' ?></div></div>
            <a class="btn btn-ghost btn-sm" target="_blank" rel="noopener" href="<?= esc(maps_link($info['nama_fasilitas'], $info['alamat']), 'attr') ?>"><i class="bi bi-map"></i> Peta</a>
        </div>
        <div class="table-responsive"><table class="table table-nora">
            <thead><tr><th>Golongan</th><th>Jumlah Kantong</th><th>Sumber Informasi</th><th>Diperbarui</th></tr></thead>
            <tbody><?php foreach ($grup['stok'] as $s): ?>
                <tr>
                    <td><?= chip_golongan($s['golongan_darah']) ?></td>
                    <td><span class="cell-title"><?= (int) $s['jumlah_kantong'] ?></span> <?= $s['jumlah_kantong'] == 0 ? '<span class="badge-status ditolak">Kosong</span>' : '' ?></td>
                    <td><?= $s['sumber'] === 'Admin' ? 'Admin PMI' : 'Fasilitas (terverifikasi admin)' ?></td>
                    <td><?= tgl_waktu($s['tanggal_update']) ?> <span class="cell-sub">(<?= waktu_lalu($s['tanggal_update']) ?>)</span></td>
                </tr>
            <?php endforeach; ?></tbody>
        </table></div>
    </div>
<?php endforeach; endif; ?>

<div class="note-box info mt-3"><i class="bi bi-info-circle-fill"></i><div>Stok dapat berubah sewaktu-waktu dan tidak menjamin darah selalu tersedia. Hubungi fasilitas untuk memastikan, dan ikuti arahan tenaga kesehatan.</div></div>
<?= $this->endSection() ?>
