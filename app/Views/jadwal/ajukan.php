<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-head"><div><h2>Ajukan Kegiatan Donor</h2><p>Penyelenggara: <strong><?= esc($fasilitas['nama_fasilitas']) ?></strong></p></div><a href="<?= base_url('jadwal/saya') ?>" class="btn btn-ghost">Kembali</a></div>
<div class="card-soft accent" style="max-width:720px"><div class="card-body-pad">
<form method="post" action="<?= base_url('jadwal/simpan') ?>"><?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">Nama Kegiatan</label><input class="form-control" name="nama_kegiatan" required maxlength="150" value="<?= esc(old('nama_kegiatan')) ?>"></div>
    <div class="row g-3 mb-3">
        <div class="col-md-4"><label class="form-label">Tanggal</label><input type="date" class="form-control" name="tanggal" required min="<?= date('Y-m-d') ?>" value="<?= esc(old('tanggal')) ?>"></div>
        <div class="col-md-4"><label class="form-label">Jam Mulai (WITA)</label><input type="time" class="form-control" name="jam_mulai" required value="<?= esc(old('jam_mulai', '08:00')) ?>"></div>
        <div class="col-md-4"><label class="form-label">Jam Selesai (WITA)</label><input type="time" class="form-control" name="jam_selesai" required value="<?= esc(old('jam_selesai', '12:00')) ?>"></div>
    </div>
    <div class="mb-4"><label class="form-label">Lokasi Kegiatan</label><input class="form-control" name="lokasi" required maxlength="255" value="<?= esc(old('lokasi')) ?>"></div>
    <button class="btn btn-nora"><i class="bi bi-send-fill"></i> Ajukan untuk Persetujuan</button>
</form></div></div>
<?= $this->endSection() ?>
