<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-head"><div><h2>Data Fasilitas &amp; Lokasi</h2><p>Kelola PMI, fasilitas kesehatan, dan unit donor darah.</p></div><a href="<?= base_url('admin/fasilitas/create') ?>" class="btn btn-nora"><i class="bi bi-plus-lg"></i> Tambah Lokasi</a></div>
<div class="card-soft mb-4"><div class="card-body-pad"><form method="get" class="row g-2"><div class="col-md-9"><div class="search-box"><i class="bi bi-search"></i><input class="form-control" name="q" placeholder="Cari nama atau alamat..." value="<?= esc($q) ?>"></div></div><div class="col-md-3 d-flex gap-2"><button class="btn btn-nora flex-grow-1">Cari</button><a href="<?= base_url('admin/fasilitas') ?>" class="btn btn-ghost">Reset</a></div></form></div></div>
<div class="card-soft"><div class="table-responsive"><table class="table table-nora">
<thead><tr><th>Fasilitas</th><th>Alamat</th><th>Kontak</th><th>Akun</th><th>Status</th><th></th></tr></thead>
<tbody><?php if (! $fasilitas): ?><tr><td colspan="6"><div class="empty-state"><i class="bi bi-hospital"></i>Tidak ada data.</div></td></tr>
<?php else: foreach ($fasilitas as $f): ?>
<tr><td class="cell-title"><?= esc($f['nama_fasilitas']) ?></td><td><?= esc($f['alamat']) ?></td><td><?= esc($f['kontak'] ?: '-') ?></td><td><?= $f['id_user'] ? '<span class="badge-status diproses">Akun fasilitas</span>' : '<span class="cell-sub">Dibuat admin</span>' ?></td><td><?= badge_status($f['status_verifikasi']) ?></td>
<td class="text-end text-nowrap">
<?php if ($f['status_verifikasi'] !== 'Terverifikasi'): ?><form method="post" action="<?= base_url('admin/fasilitas/verifikasi/' . $f['id_fasilitas']) ?>" class="d-inline"><?= csrf_field() ?><input type="hidden" name="status" value="Terverifikasi"><button class="btn-icon ok" title="Verifikasi"><i class="bi bi-patch-check"></i></button></form>
<?php if ($f['status_verifikasi'] === 'Menunggu'): ?><form method="post" action="<?= base_url('admin/fasilitas/verifikasi/' . $f['id_fasilitas']) ?>" class="d-inline"><?= csrf_field() ?><input type="hidden" name="status" value="Ditolak"><button class="btn-icon del" title="Tolak"><i class="bi bi-x-lg"></i></button></form><?php endif; ?>
<?php endif; ?>
<a class="btn-icon" href="<?= base_url('admin/fasilitas/edit/' . $f['id_fasilitas']) ?>"><i class="bi bi-pencil"></i></a>
<button type="button" class="btn-icon del" data-bs-toggle="modal" data-bs-target="#modalHapus" data-action="<?= base_url('admin/fasilitas/delete/' . $f['id_fasilitas']) ?>" data-nama="<?= esc($f['nama_fasilitas'], 'attr') ?>"><i class="bi bi-trash3"></i></button></td></tr>
<?php endforeach; endif; ?></tbody></table></div></div>
<?= $this->endSection() ?>
