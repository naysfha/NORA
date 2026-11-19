<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $edit = $mode === 'edit'; $v = static fn ($k) => old($k, $fasilitas[$k] ?? ''); ?>
<div class="page-head"><div><h2><?= $edit ? 'Edit' : 'Tambah' ?> Fasilitas / Lokasi</h2></div><a href="<?= base_url('admin/fasilitas') ?>" class="btn btn-ghost">Kembali</a></div>
<div class="card-soft accent" style="max-width:720px"><div class="card-body-pad">
<form method="post" action="<?= base_url($edit ? 'admin/fasilitas/update/' . $fasilitas['id_fasilitas'] : 'admin/fasilitas/store') ?>"><?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">Nama Fasilitas / Unit</label><input class="form-control" name="nama_fasilitas" required maxlength="150" value="<?= esc($v('nama_fasilitas')) ?>"></div>
    <div class="mb-3"><label class="form-label">Alamat</label><input class="form-control" name="alamat" required maxlength="255" value="<?= esc($v('alamat')) ?>"></div>
    <div class="mb-4"><label class="form-label">Kontak</label><input class="form-control" name="kontak" maxlength="50" value="<?= esc($v('kontak')) ?>"></div>
    <button class="btn btn-nora"><i class="bi bi-save"></i> Simpan</button>
</form></div></div>
<?= $this->endSection() ?>
