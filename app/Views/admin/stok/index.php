<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-head"><div><h2>Data Stok Darah</h2><p>Verifikasi pembaruan stok dari fasilitas atau input langsung sebagai admin.</p></div><a href="<?= base_url('admin/stok/create') ?>" class="btn btn-nora"><i class="bi bi-plus-lg"></i> Input Stok</a></div>
<div class="pills mb-4"><?php foreach (['' => 'Semua', 'Menunggu' => 'Menunggu', 'Terverifikasi' => 'Terverifikasi', 'Ditolak' => 'Ditolak'] as $k => $lb): ?><a class="pill <?= $status === $k ? 'active' : '' ?>" href="<?= base_url('admin/stok' . ($k ? '?status=' . $k : '')) ?>"><?= $lb ?></a><?php endforeach; ?></div>
<div class="card-soft"><div class="table-responsive"><table class="table table-nora">
<thead><tr><th>Fasilitas</th><th>Golongan</th><th>Kantong</th><th>Sumber</th><th>Waktu</th><th>Status</th><th></th></tr></thead>
<tbody><?php if (! $stok): ?><tr><td colspan="7"><div class="empty-state"><i class="bi bi-droplet"></i>Tidak ada data.</div></td></tr>
<?php else: foreach ($stok as $s): ?>
<tr><td class="cell-title"><?= esc($s['nama_fasilitas']) ?></td><td><?= chip_golongan($s['golongan_darah']) ?></td><td class="cell-title"><?= (int) $s['jumlah_kantong'] ?></td><td><?= esc($s['sumber']) ?></td><td class="cell-sub"><?= tgl_waktu($s['tanggal_update']) ?></td><td><?= badge_status($s['status_verifikasi']) ?></td>
<td class="text-end text-nowrap"><?php if ($s['status_verifikasi'] === 'Menunggu'): ?>
<form method="post" action="<?= base_url('admin/stok/verifikasi/' . $s['id_stok']) ?>" class="d-inline"><?= csrf_field() ?><input type="hidden" name="status" value="Terverifikasi"><button class="btn-icon ok" title="Verifikasi"><i class="bi bi-check-lg"></i></button></form>
<form method="post" action="<?= base_url('admin/stok/verifikasi/' . $s['id_stok']) ?>" class="d-inline"><?= csrf_field() ?><input type="hidden" name="status" value="Ditolak"><button class="btn-icon del" title="Tolak"><i class="bi bi-x-lg"></i></button></form><?php endif; ?>
<button type="button" class="btn-icon del" data-bs-toggle="modal" data-bs-target="#modalHapus" data-action="<?= base_url('admin/stok/delete/' . $s['id_stok']) ?>" data-nama="Catatan stok <?= esc($s['golongan_darah'], 'attr') ?> - <?= esc($s['nama_fasilitas'], 'attr') ?>"><i class="bi bi-trash3"></i></button></td></tr>
<?php endforeach; endif; ?></tbody></table></div></div>
<?= $this->endSection() ?>
