<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Masuk') ?></title>
    <link rel="icon" href="<?= base_url('favicon.svg') ?>" type="image/svg+xml">
    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('vendor/bootstrap-icons/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= base_url('css/nora.css') ?>" rel="stylesheet">
</head>
<body>
<div class="login-wrap">
    <section class="login-hero">
        <i class="bi bi-droplet-fill tetes"></i>
        <a href="<?= base_url('/') ?>" class="brand mb-5 px-0" style="position:relative">
            <span class="brand-icon"><i class="bi bi-droplet-fill"></i></span>
            <span><h5>NORA</h5><small>PMI Kota Palu</small></span>
        </a>
        <h1 style="position:relative">Setetes darah,<br><span class="merah">seribu harapan.</span></h1>
        <p class="mt-3" style="position:relative">Sistem informasi donor darah terintegrasi yang menghubungkan pendonor, pencari darah, fasilitas kesehatan, dan PMI Kota Palu dalam satu tempat.</p>
        <div style="position:relative" class="mt-3">
            <div class="poin"><i class="bi bi-search-heart"></i> Cari stok darah berdasarkan golongan &amp; lokasi</div>
            <div class="poin"><i class="bi bi-broadcast-pin"></i> Ajukan &amp; pantau permintaan SOS</div>
            <div class="poin"><i class="bi bi-calendar2-heart"></i> Jadwal dan lokasi kegiatan donor</div>
        </div>
    </section>

    <section class="login-form-side">
        <div class="login-card">
            <h2 class="mb-1">Selamat datang</h2>
            <p class="text-muted mb-4">Masuk untuk melanjutkan ke NORA.</p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>

            <form action="<?= base_url('login') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label" for="identitas">Email atau Nomor Telepon</label>
                    <input type="text" class="form-control" id="identitas" name="identitas" value="<?= esc(old('identitas')) ?>" placeholder="contoh: nama@email.com / 0812xxxxxxx" autofocus required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="password">Kata Sandi</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan kata sandi" required style="border-right:0">
                        <button type="button" class="btn btn-ghost" data-toggle-pass="#password" style="border-radius:0 12px 12px 0;border-color:#ead2d6"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
                <button type="submit" class="btn btn-nora w-100 py-2"><i class="bi bi-box-arrow-in-right me-1"></i> Masuk</button>
            </form>

            <p class="text-center mt-3 mb-0">Belum punya akun? <a href="<?= base_url('register') ?>" class="fw-bold">Daftar sekarang</a></p>
        </div>
    </section>
</div>
<script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('js/nora.js') ?>"></script>
</body>
</html>
