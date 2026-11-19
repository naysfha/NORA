<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-head"><div><h2>Laporan</h2><p>Sistem merekap data pengguna, stok, jadwal, dan SOS menjadi laporan.</p></div></div>
<div class="row g-4">
<div class="col-lg-4"><div class="card-soft accent"><div class="card-head"><h5><i class="bi bi-file-earmark-bar-graph me-2 text-danger"></i>Susun Laporan</h5></div><div class="card-body-pad">
<form method="post" action="<?= base_url('admin/laporan/buat') ?>"><?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">Jenis Laporan</label><select class="form-select" name="jenis_laporan"><?php foreach ($jenis as $k => $v): ?><option value="<?= esc($k, 'attr') ?>"><?= esc($v) ?></option><?php endforeach; ?></select></div>
    <div class="mb-4"><label class="form-label">Periode (bulan)</label><input type="month" class="form-control" name="periode" value="<?= date('Y-m') ?>"><div class="form-text">Kosongkan untuk semua periode.</div></div>
    <button class="btn btn-nora w-100"><i class="bi bi-gear-fill"></i> Susun Laporan</button>
</form></div></div></div>
<div class="col-lg-8"><div class="card-soft"><div class="card-head"><h5>Arsip Laporan</h5></div><div class="table-responsive"><table class="table table-nora">
<thead><tr><th>Jenis</th><th>Periode</th><th>Dibuat</th><th></th></tr></thead><tbody>
<?php if (! $laporan): ?><tr><td colspan="4"><div class="empty-state"><i class="bi bi-file-earmark-x"></i>Belum ada laporan.</div></td></tr>
<?php else: foreach ($laporan as $l): ?><tr><td class="cell-title">#<?= $l['id_laporan'] ?> <?= esc($l['jenis_laporan']) ?></td><td><?= esc($l['periode']) ?></td><td><?= tgl_waktu($l['tanggal_dibuat']) ?><div class="cell-sub">oleh <?= esc($l['nama_admin']) ?></div></td>
<td class="text-end text-nowrap"><a class="btn-icon" href="<?= base_url('admin/laporan/lihat/' . $l['id_laporan']) ?>"><i class="bi bi-eye"></i></a> <button type="button" class="btn-icon del" data-bs-toggle="modal" data-bs-target="#modalHapus" data-action="<?= base_url('admin/laporan/delete/' . $l['id_laporan']) ?>" data-nama="Laporan #<?= $l['id_laporan'] ?>"><i class="bi bi-trash3"></i></button></td></tr><?php endforeach; endif; ?>
</tbody></table></div></div></div></div>
<?= $this->endSection() ?>
