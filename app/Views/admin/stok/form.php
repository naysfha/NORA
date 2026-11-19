<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-head"><div><h2>Input Stok Darah</h2><p>Data yang diinput admin langsung berstatus terverifikasi.</p></div><a href="<?= base_url('admin/stok') ?>" class="btn btn-ghost">Kembali</a></div>
<div class="card-soft accent" style="max-width:720px"><div class="card-body-pad">
<form method="post" action="<?= base_url('admin/stok/store') ?>"><?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">Fasilitas</label><select class="form-select" name="id_fasilitas" required><option value="">- pilih fasilitas -</option><?php foreach ($fasilitas as $f): ?><option value="<?= $f['id_fasilitas'] ?>" <?= (string) old('id_fasilitas') === (string) $f['id_fasilitas'] ? 'selected' : '' ?>><?= esc($f['nama_fasilitas']) ?></option><?php endforeach; ?></select></div>
    <label class="form-label">Golongan Darah</label>
    <div class="gol-picker mb-3"><?php foreach ($golongan as $g): ?><input type="radio" name="golongan_darah" id="g_<?= esc($g, 'attr') ?>" value="<?= esc($g, 'attr') ?>" <?= old('golongan_darah') === $g ? 'checked' : '' ?> required><label for="g_<?= esc($g, 'attr') ?>"><?= esc($g) ?></label><?php endforeach; ?></div>
    <div class="mb-4"><label class="form-label">Jumlah Kantong</label><input type="number" min="0" max="10000" class="form-control" name="jumlah_kantong" required value="<?= esc(old('jumlah_kantong')) ?>"></div>
    <button class="btn btn-nora"><i class="bi bi-save"></i> Simpan</button>
</form></div></div>
<?= $this->endSection() ?>
