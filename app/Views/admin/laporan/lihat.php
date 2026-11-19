<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-head no-print"><div><h2>Laporan <?= esc($laporan['jenis_laporan']) ?></h2></div>
<div class="d-flex gap-2"><a href="<?= base_url('admin/laporan') ?>" class="btn btn-ghost">Kembali</a><button onclick="window.print()" class="btn btn-nora"><i class="bi bi-printer"></i> Cetak / PDF</button></div></div>
<div class="card-soft accent mb-4"><div class="card-body-pad">
    <div class="d-flex align-items-center gap-3 mb-3"><span class="brand-icon" style="width:50px;height:50px"><i class="bi bi-droplet-fill"></i></span>
    <div><h4 class="mb-0" style="color:var(--red-900)">NORA - Laporan <?= esc($laporan['jenis_laporan']) ?></h4><div class="text-muted small">PMI Kota Palu &middot; Periode: <?= esc($laporan['periode']) ?> &middot; Disusun <?= tgl_waktu($laporan['tanggal_dibuat']) ?> oleh <?= esc($laporan['nama_admin']) ?></div></div></div>
    <?php if (! empty($isi['ringkasan'])): ?><div class="row g-3"><?php foreach ($isi['ringkasan'] as $label => $nilai): ?>
        <div class="col-6 col-lg-4"><div class="card-soft stat-card" style="box-shadow:none"><div><div class="angka"><?= esc($nilai) ?></div><div class="label"><?= esc($label) ?></div></div></div></div>
    <?php endforeach; ?></div><?php endif; ?>
</div></div>
<?php foreach ($isi['tabel'] as $t): ?>
<div class="card-soft mb-4"><div class="card-head"><h5><?= esc($t['judul']) ?></h5></div><div class="table-responsive"><table class="table table-nora">
<thead><tr><?php foreach ($t['kolom'] as $k): ?><th><?= esc($k) ?></th><?php endforeach; ?></tr></thead><tbody>
<?php if (! $t['baris']): ?><tr><td colspan="<?= count($t['kolom']) ?>" class="text-center text-muted py-3">Tidak ada data pada periode ini.</td></tr>
<?php else: foreach ($t['baris'] as $b): ?><tr><?php foreach ($b as $sel): ?><td><?= esc($sel) ?></td><?php endforeach; ?></tr><?php endforeach; endif; ?>
</tbody></table></div></div>
<?php endforeach; ?>
<?= $this->endSection() ?>
