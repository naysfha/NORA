<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-head"><div><h2>Buat Permintaan SOS</h2><p>Lengkapi seluruh data agar permintaan dapat diverifikasi admin.</p></div><a href="<?= base_url('sos/saya') ?>" class="btn btn-ghost">Kembali</a></div>
<div class="note-box sos mb-4"><i class="bi bi-exclamation-octagon-fill"></i><div><?= esc(\App\Libraries\Referensi::DISCLAIMER_SOS) ?></div></div>
<div class="card-soft accent" style="max-width:780px"><div class="card-body-pad">
<form method="post" action="<?= base_url('sos/simpan') ?>"><?= csrf_field() ?>
    <label class="form-label">Golongan Darah yang Dibutuhkan</label>
    <div class="gol-picker mb-3"><?php foreach ($golongan as $g): ?>
        <input type="radio" name="golongan_darah" id="g_<?= esc($g, 'attr') ?>" value="<?= esc($g, 'attr') ?>" <?= old('golongan_darah') === $g ? 'checked' : '' ?> required><label for="g_<?= esc($g, 'attr') ?>"><?= esc($g) ?></label>
    <?php endforeach; ?></div>
    <div class="row g-3 mb-3">
        <div class="col-md-4"><label class="form-label">Jumlah Kantong</label><input type="number" min="1" max="50" class="form-control" name="jumlah_dibutuhkan" required value="<?= esc(old('jumlah_dibutuhkan', 1)) ?>"></div>
        <div class="col-md-8"><label class="form-label">Tingkat Urgensi</label><select class="form-select" name="tingkat_urgensi" required><?php foreach ($urgensi as $u): ?><option <?= old('tingkat_urgensi') === $u ? 'selected' : '' ?>><?= esc($u) ?></option><?php endforeach; ?></select></div>
    </div>
    <div class="mb-3"><label class="form-label">Rumah Sakit / Fasilitas Tujuan</label><input class="form-control" name="rumah_sakit" required maxlength="150" value="<?= esc(old('rumah_sakit')) ?>"></div>
    <div class="mb-3"><label class="form-label">Lokasi (kecamatan / alamat)</label><input class="form-control" name="lokasi" required maxlength="255" value="<?= esc(old('lokasi')) ?>"></div>
    <div class="mb-4"><label class="form-label">Nomor Kontak yang Bisa Dihubungi</label><input class="form-control" name="kontak" required inputmode="tel" placeholder="0812xxxxxxxx" value="<?= esc(old('kontak')) ?>"><div class="form-text">Nomor hanya dilihat admin dan pendonor yang merespons permintaan Anda.</div></div>
    <button class="btn btn-nora"><i class="bi bi-broadcast-pin"></i> Kirim Permintaan SOS</button>
</form></div></div>
<?= $this->endSection() ?>
