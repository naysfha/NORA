<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Daftar') ?></title>
    <link rel="icon" href="<?= base_url('favicon.svg') ?>" type="image/svg+xml">
    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('vendor/bootstrap-icons/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= base_url('css/nora.css') ?>" rel="stylesheet">
</head>
<body>
<?php
$errors = session()->getFlashdata('errors') ?? [];
$ikon   = ['pendonor' => 'bi-person-heart', 'pencari' => 'bi-search-heart', 'fasilitas' => 'bi-hospital'];
$pilih  = old('role') ?: 'pendonor';
?>
<div class="login-wrap">
    <section class="login-hero">
        <i class="bi bi-droplet-fill tetes"></i>
        <a href="<?= base_url('/') ?>" class="brand mb-5 px-0" style="position:relative">
            <span class="brand-icon"><i class="bi bi-droplet-fill"></i></span>
            <span><h5>NORA</h5><small>PMI Kota Palu</small></span>
        </a>
        <h1 style="position:relative">Bergabung,<br><span class="merah">selamatkan nyawa.</span></h1>
        <p class="mt-3" style="position:relative">Daftarkan diri sebagai pendonor, pencari darah, atau fasilitas kesehatan. Setiap peran mendapat fitur sesuai hak aksesnya.</p>
        <div style="position:relative" class="mt-3">
            <div class="poin"><i class="bi bi-person-heart"></i> Pendonor: profil, jadwal, dan respons permintaan darah</div>
            <div class="poin"><i class="bi bi-broadcast-pin"></i> Pencari darah: cari stok &amp; ajukan SOS</div>
            <div class="poin"><i class="bi bi-hospital"></i> Fasilitas: kirim informasi stok &amp; kegiatan</div>
        </div>
    </section>

    <section class="login-form-side">
        <div class="login-card">
            <h2 class="mb-1">Buat akun baru</h2>
            <p class="text-muted mb-3">Isi data dasar Anda di bawah ini.</p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>
            <?php if (! empty($errors)): ?>
                <div class="alert alert-danger">
                    <strong>Periksa kembali isian Anda:</strong>
                    <ul class="mb-0 mt-1"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('register') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Daftar sebagai</label>
                    <div class="role-choice">
                        <?php foreach ($role as $kode => $label): ?>
                            <div>
                                <input type="radio" name="role" id="role_<?= $kode ?>" value="<?= $kode ?>" <?= $pilih === $kode ? 'checked' : '' ?>>
                                <label for="role_<?= $kode ?>"><i class="bi <?= $ikon[$kode] ?>"></i><?= esc($label) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="nama">Nama Lengkap <span class="text-muted fw-normal" id="hintNama">(untuk fasilitas: nama fasilitas)</span></label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?= esc(old('nama')) ?>" required maxlength="100">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label" for="no_telp">Nomor Telepon</label>
                        <input type="text" class="form-control" id="no_telp" name="no_telp" value="<?= esc(old('no_telp')) ?>" placeholder="0812xxxxxxxx" inputmode="tel">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= esc(old('email')) ?>" placeholder="nama@email.com">
                    </div>
                    <div class="col-12"><div class="form-text mt-0">Isi minimal salah satu: nomor telepon atau email.</div></div>
                </div>

                <div class="mb-3" id="boxAlamat" style="display:none">
                    <label class="form-label" for="alamat">Alamat Fasilitas</label>
                    <input type="text" class="form-control" id="alamat" name="alamat" value="<?= esc(old('alamat')) ?>" maxlength="255">
                    <div class="form-text">Akun fasilitas akan diperiksa dan diverifikasi admin sebelum dapat mengirim data stok.</div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <label class="form-label" for="password">Kata Sandi</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="6" placeholder="Min. 6 karakter">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label" for="konfirmasi">Ulangi Kata Sandi</label>
                        <input type="password" class="form-control" id="konfirmasi" name="konfirmasi" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-nora w-100 py-2"><i class="bi bi-person-plus-fill me-1"></i> Daftar</button>
            </form>

            <p class="text-center mt-3 mb-0">Sudah punya akun? <a href="<?= base_url('login') ?>" class="fw-bold">Masuk</a></p>
        </div>
    </section>
</div>
<script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('js/nora.js') ?>"></script>
</body>
</html>
