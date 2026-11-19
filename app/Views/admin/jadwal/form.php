<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $edit = $mode === 'edit'; $v = static fn ($k, $d = '') => old($k, $jadwal[$k] ?? $d); ?>
<div class="page-head"><div><h2><?= $edit ? 'Edit' : 'Tambah' ?> Kegiatan Donor</h2><p><?= $edit ? 'Perubahan tanggal/waktu/lokasi pada kegiatan yang sudah tayang akan memicu notifikasi perubahan jadwal.' : 'Kegiatan yang dibuat admin langsung dipublikasikan dan diumumkan lewat notifikasi.' ?></p></div><a href="<?= base_url('admin/jadwal') ?>" class="btn btn-ghost">Kembali</a></div>
<div class="card-soft accent" style="max-width:760px"><div class="card-body-pad">
<form method="post" action="<?= base_url($edit ? 'admin/jadwal/update/' . $jadwal['id_jadwal'] : 'admin/jadwal/store') ?>"><?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">Nama Kegiatan</label><input class="form-control" name="nama_kegiatan" required maxlength="150" value="<?= esc($v('nama_kegiatan')) ?>"></div>
    <div class="row g-3 mb-3">
        <div class="col-md-4"><label class="form-label">Tanggal</label><input type="date" class="form-control" name="tanggal" required value="<?= esc($v('tanggal')) ?>"></div>
        <div class="col-md-4"><label class="form-label">Jam Mulai (WITA)</label><input type="time" class="form-control" name="jam_mulai" required value="<?= esc(old('jam_mulai', $mulai)) ?>"></div>
        <div class="col-md-4"><label class="form-label">Jam Selesai (WITA)</label><input type="time" class="form-control" name="jam_selesai" required value="<?= esc(old('jam_selesai', $selesai)) ?>"></div>
    </div>
    <div class="mb-3"><label class="form-label">Lokasi</label><input class="form-control" name="lokasi" required maxlength="255" value="<?= esc($v('lokasi')) ?>"></div>
    <div class="mb-4"><label class="form-label">Penyelenggara</label><input class="form-control" name="penyelenggara" required maxlength="150" value="<?= esc($v('penyelenggara', 'UTD PMI Kota Palu')) ?>"></div>
    <button class="btn btn-nora"><i class="bi bi-save"></i> Simpan</button>
</form></div></div>
<?= $this->endSection() ?>
