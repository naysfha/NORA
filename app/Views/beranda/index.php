<?php use App\Libraries\Referensi; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title) ?></title>
    <link rel="icon" href="<?= base_url('favicon.svg') ?>" type="image/svg+xml">
    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('vendor/bootstrap-icons/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= base_url('css/nora.css') ?>" rel="stylesheet">
</head>
<body>

<header class="pub-nav">
    <div class="container d-flex align-items-center">
        <a href="<?= base_url('/') ?>" class="brand">
            <span class="brand-icon"><i class="bi bi-droplet-fill"></i></span>
            <span><h5>NORA</h5><small>PMI Kota Palu</small></span>
        </a>
        <div class="ms-auto d-flex gap-2">
            <a href="<?= base_url('login') ?>" class="btn btn-garis btn-sm">Masuk</a>
            <a href="<?= base_url('register') ?>" class="btn btn-putih btn-sm">Daftar</a>
        </div>
    </div>
</header>

<section class="hero">
    <i class="bi bi-droplet-fill tetes-besar"></i>
    <div class="container position-relative">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="badge rounded-pill mb-3 px-3 py-2" style="background:rgba(255,255,255,.15)"><i class="bi bi-heart-pulse-fill me-1"></i> Sistem Informasi Donor Darah Terintegrasi</span>
                <h1 class="mb-3">Temukan darah,<br>hubungkan <em>harapan</em>.</h1>
                <p class="lead mb-4">NORA mempertemukan pendonor, pencari darah, fasilitas kesehatan, dan PMI Kota Palu: cari stok per golongan darah, lihat jadwal donor, dan ajukan permintaan bantuan melalui fitur SOS.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= base_url('register') ?>" class="btn btn-putih"><i class="bi bi-person-plus-fill me-1"></i> Daftar Gratis</a>
                    <a href="<?= base_url('login') ?>" class="btn btn-garis"><i class="bi bi-box-arrow-in-right me-1"></i> Masuk</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong><i class="bi bi-droplet-half me-1"></i> Stok darah terverifikasi</strong>
                        <small style="color:#f5c4cb"><?= (int) $totalStok ?> kantong</small>
                    </div>
                    <div class="hero-gol">
                        <?php foreach (Referensi::GOLONGAN as $g): ?>
                            <div><b><?= esc($g) ?></b><small><?= (int) ($stok[$g] ?? 0) ?> kantong</small></div>
                        <?php endforeach; ?>
                    </div>
                    <small class="d-block mt-3" style="color:#f5c4cb"><i class="bi bi-info-circle"></i> Masuk untuk melihat rincian per fasilitas dan waktu pembaruan.</small>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="stat-strip">
    <div class="container">
        <div class="card-soft"><div class="row g-0 text-center">
            <?php $kotak = [
                ['bi-person-heart', $pendonor, 'Pendonor aktif'],
                ['bi-hospital', $fasilitas, 'Fasilitas & lokasi'],
                ['bi-calendar2-heart', $kegiatan, 'Kegiatan mendatang'],
                ['bi-check2-circle', $sosSelesai, 'SOS terselesaikan'],
            ]; foreach ($kotak as $k): ?>
                <div class="col-6 col-md-3 p-4">
                    <i class="bi <?= $k[0] ?> fs-3" style="color:var(--red-500)"></i>
                    <div class="fs-2 fw-bold" style="color:var(--red-900)"><?= (int) $k[1] ?></div>
                    <div class="text-muted small"><?= $k[2] ?></div>
                </div>
            <?php endforeach; ?>
        </div></div>
    </div>
</section>

<section class="py-5 mt-4">
    <div class="container">
        <div class="section-title">
            <h2>Satu sistem untuk semua pihak</h2>
            <p>Setiap pengguna mendapat fitur sesuai peran dan hak aksesnya.</p>
        </div>
        <div class="row g-4">
            <?php $fitur = [
                ['bi-person-heart', 'Pendonor', 'Lengkapi profil dan golongan darah, baca edukasi, lihat jadwal, lalu beri respons pada permintaan darah.'],
                ['bi-search-heart', 'Pencari Darah', 'Cari stok berdasarkan golongan dan lokasi, ajukan SOS, dan pantau statusnya sampai selesai.'],
                ['bi-hospital', 'Fasilitas Kesehatan', 'Sampaikan informasi stok dan kegiatan donor melalui mekanisme yang diverifikasi admin.'],
                ['bi-shield-check', 'Admin PMI', 'Kelola pengguna, stok, lokasi, kegiatan, edukasi, SOS, notifikasi, dan laporan.'],
            ]; foreach ($fitur as $f): ?>
                <div class="col-sm-6 col-lg-3"><div class="card-soft fitur">
                    <div class="ikon"><i class="bi <?= $f[0] ?>"></i></div>
                    <h6><?= $f[1] ?></h6><p><?= $f[2] ?></p>
                </div></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5" style="background:#fff">
    <div class="container">
        <div class="section-title">
            <h2>Bagaimana alur permintaan SOS?</h2>
            <p>Dari pengajuan hingga terpenuhi, setiap tahap dipantau dan diberi notifikasi.</p>
        </div>
        <div class="row g-4">
            <?php $alur = [
                ['Ajukan', 'Pencari darah mengisi golongan, jumlah, lokasi, rumah sakit, kontak, dan tingkat urgensi.'],
                ['Verifikasi', 'Sistem memeriksa kelengkapan, lalu admin memverifikasi permintaan.'],
                ['Respons', 'Pendonor dengan golongan yang sesuai diberi notifikasi dan dapat menyatakan kesediaan.'],
                ['Proses & Selesai', 'Admin memperbarui status menjadi diproses lalu selesai. Pencari memantau dari aplikasi.'],
            ]; foreach ($alur as $i => $s): ?>
                <div class="col-sm-6 col-lg-3">
                    <div class="alur-nomor"><?= $i + 1 ?></div>
                    <h6 class="fw-bold" style="color:var(--red-900)"><?= $s[0] ?></h6>
                    <p class="text-muted small mb-0"><?= $s[1] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="note-box sos mt-5"><i class="bi bi-exclamation-octagon-fill"></i><div><?= esc(Referensi::DISCLAIMER_SOS) ?></div></div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="cta-band">
            <h2 class="mb-2">Siap menjadi bagian dari penyelamat?</h2>
            <p class="mb-4" style="max-width:520px;color:#fde0e4">Satu kantong darah dapat membantu menyelamatkan nyawa. Daftar sekarang dan lengkapi profil Anda.</p>
            <a href="<?= base_url('register') ?>" class="btn btn-putih">Mulai Sekarang <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</section>

<footer class="pub-footer">
    <div class="container">
        <div class="row g-3">
            <div class="col-lg-6"><strong class="text-white">NORA - Sistem Informasi Donor Darah Terintegrasi</strong><br>Perancangan sistem untuk PMI Kota Palu. Program Studi S1 Sistem Informasi, Universitas Tadulako.</div>
            <div class="col-lg-6"><?= esc(Referensi::DISCLAIMER_UMUM) ?></div>
        </div>
    </div>
</footer>
</body>
</html>
