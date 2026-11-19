<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $edit = $mode === 'edit'; $v = static fn ($k) => old($k, $artikel[$k] ?? ''); ?>
<div class="page-head"><div><h2><?= $edit ? 'Edit' : 'Tambah' ?> Artikel Edukasi</h2></div><a href="<?= base_url('admin/edukasi') ?>" class="btn btn-ghost">Kembali</a></div>
<div class="card-soft accent" style="max-width:860px"><div class="card-body-pad">
<form method="post" action="<?= base_url($edit ? 'admin/edukasi/update/' . $artikel['id_edukasi'] : 'admin/edukasi/store') ?>"><?= csrf_field() ?>
    <div class="row g-3 mb-3"><div class="col-md-8"><label class="form-label">Judul</label><input class="form-control" name="judul" required maxlength="150" value="<?= esc($v('judul')) ?>"></div>
    <div class="col-md-4"><label class="form-label">Kategori</label><select class="form-select" name="kategori"><?php foreach ($kategori as $k): ?><option <?= $v('kategori') === $k ? 'selected' : '' ?>><?= esc($k) ?></option><?php endforeach; ?></select></div></div>
    <div class="mb-3"><label class="form-label">Ringkasan</label><input class="form-control" name="ringkasan" maxlength="255" value="<?= esc($v('ringkasan')) ?>"></div>
    <div class="mb-4"><label class="form-label">Isi Artikel</label><textarea class="form-control" name="isi" rows="12" required><?= esc($v('isi')) ?></textarea><div class="form-text">Pisahkan paragraf dengan satu baris kosong.</div></div>
    <button class="btn btn-nora"><i class="bi bi-save"></i> Simpan</button>
</form></div></div>
<?= $this->endSection() ?>
