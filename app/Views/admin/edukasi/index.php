<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-head"><div><h2>Konten Edukasi</h2><p>Kelola artikel edukasi donor darah.</p></div><a href="<?= base_url('admin/edukasi/create') ?>" class="btn btn-nora"><i class="bi bi-plus-lg"></i> Tambah Artikel</a></div>
<div class="card-soft"><div class="table-responsive"><table class="table table-nora">
<thead><tr><th>Judul</th><th>Kategori</th><th>Dibuat</th><th></th></tr></thead>
<tbody><?php if (! $artikel): ?><tr><td colspan="4"><div class="empty-state"><i class="bi bi-journal-x"></i>Belum ada artikel.</div></td></tr>
<?php else: foreach ($artikel as $a): ?>
<tr><td><div class="cell-title"><?= esc($a['judul']) ?></div><div class="cell-sub"><?= esc(potong($a['ringkasan'] ?: $a['isi'], 90)) ?></div></td><td><span class="badge-status terverifikasi"><?= esc($a['kategori']) ?></span></td><td><?= tgl_indo($a['tanggal_dibuat']) ?></td>
<td class="text-end text-nowrap"><a class="btn-icon" href="<?= base_url('edukasi/' . $a['id_edukasi']) ?>" title="Lihat"><i class="bi bi-eye"></i></a> <a class="btn-icon" href="<?= base_url('admin/edukasi/edit/' . $a['id_edukasi']) ?>"><i class="bi bi-pencil"></i></a>
<button type="button" class="btn-icon del" data-bs-toggle="modal" data-bs-target="#modalHapus" data-action="<?= base_url('admin/edukasi/delete/' . $a['id_edukasi']) ?>" data-nama="<?= esc($a['judul'], 'attr') ?>"><i class="bi bi-trash3"></i></button></td></tr>
<?php endforeach; endif; ?></tbody></table></div></div>
<?= $this->endSection() ?>
