<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-head"><div><h2>Data Pengguna</h2><p>Kelola akun pendonor, pencari darah, fasilitas, dan admin.</p></div><a href="<?= base_url('admin/pengguna/create') ?>" class="btn btn-nora"><i class="bi bi-plus-lg"></i> Tambah Pengguna</a></div>
<div class="card-soft mb-4"><div class="card-body-pad"><form method="get" class="row g-2">
    <div class="col-md-6"><div class="search-box"><i class="bi bi-search"></i><input class="form-control" name="q" placeholder="Cari nama, email, atau telepon..." value="<?= esc($filter['q']) ?>"></div></div>
    <div class="col-md-3"><select class="form-select" name="role"><option value="">Semua peran</option><?php foreach ($role as $k => $v): ?><option value="<?= $k ?>" <?= $filter['role'] === $k ? 'selected' : '' ?>><?= esc($v) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-3 d-flex gap-2"><button class="btn btn-nora flex-grow-1">Filter</button><a href="<?= base_url('admin/pengguna') ?>" class="btn btn-ghost">Reset</a></div>
</form></div></div>
<div class="card-soft"><div class="table-responsive"><table class="table table-nora">
<thead><tr><th>Pengguna</th><th>Kontak</th><th>Peran</th><th>Terdaftar</th><th></th></tr></thead>
<tbody><?php if (! $pengguna): ?><tr><td colspan="5"><div class="empty-state"><i class="bi bi-people"></i>Tidak ada data.</div></td></tr>
<?php else: foreach ($pengguna as $p): ?>
<tr><td><div class="d-flex align-items-center gap-3"><div class="avatar" style="background:var(--red-100)"><?= esc(inisial($p['nama'])) ?></div><div class="cell-title"><?= esc($p['nama']) ?></div></div></td>
<td><div><?= esc($p['email'] ?: '-') ?></div><div class="cell-sub"><?= esc($p['no_telp'] ?: '-') ?></div></td>
<td><span class="badge-status <?= $p['role'] === 'admin' ? 'diproses' : 'terverifikasi' ?>"><?= esc(label_role($p['role'])) ?></span></td><td><?= tgl_indo($p['tanggal_daftar']) ?></td>
<td class="text-end text-nowrap"><a class="btn-icon" href="<?= base_url('admin/pengguna/edit/' . $p['id_user']) ?>"><i class="bi bi-pencil"></i></a>
<?php if ((int) $p['id_user'] !== (int) session()->get('id_user')): ?> <button type="button" class="btn-icon del" data-bs-toggle="modal" data-bs-target="#modalHapus" data-action="<?= base_url('admin/pengguna/delete/' . $p['id_user']) ?>" data-nama="<?= esc($p['nama'], 'attr') ?>"><i class="bi bi-trash3"></i></button><?php endif; ?></td></tr>
<?php endforeach; endif; ?></tbody></table></div></div>
<?= $this->endSection() ?>
