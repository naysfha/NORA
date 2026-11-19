<?php
use App\Libraries\Referensi;
use App\Models\NotifikasiModel;

$path   = trim(uri_string(), '/');
$role   = session()->get('role');
$nama   = session()->get('nama') ?? '';
$errors = session()->getFlashdata('errors') ?? [];
$belum  = (new NotifikasiModel())->jumlahBelumDibaca((int) session()->get('id_user'));

// $a('stok') -> aktif bila path persis 'stok'; $a('admin/sos', true) -> aktif bila diawali 'admin/sos'
$a = static function (string $p, bool $awalan = false) use ($path): string {
    $cocok = $awalan ? ($path === $p || str_starts_with($path, $p . '/')) : $path === $p;

    return $cocok ? 'active' : '';
};

$antrean = ['sos' => 0, 'stok' => 0, 'jadwal' => 0, 'fasilitas' => 0, 'pendonor' => 0];
if ($role === 'admin') {
    $db = db_connect();
    $antrean = [
        'sos'       => $db->table('permintaan_sos')->where('status', 'Menunggu Verifikasi')->countAllResults(),
        'stok'      => $db->table('stok_darah')->where('status_verifikasi', 'Menunggu')->countAllResults(),
        'jadwal'    => $db->table('jadwal_donor')->where('status', 'Menunggu')->countAllResults(),
        'fasilitas' => $db->table('fasilitas_kesehatan')->where('status_verifikasi', 'Menunggu')->countAllResults(),
        'pendonor'  => $db->table('pendonor')->where('status_verifikasi', 'Menunggu')->where('golongan_darah IS NOT NULL', null, false)->countAllResults(),
    ];
}
$hitung = static fn (int $n): string => $n > 0 ? '<span class="count">' . $n . '</span>' : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'NORA') ?></title>
    <link rel="icon" href="<?= base_url('favicon.svg') ?>" type="image/svg+xml">

    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('vendor/bootstrap-icons/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= base_url('css/nora.css') ?>" rel="stylesheet">
</head>
<body>

<!-- ================= SIDEBAR ================= -->
<aside class="sidebar" id="sidebar">
    <a href="<?= base_url('dashboard') ?>" class="brand">
        <span class="brand-icon"><i class="bi bi-droplet-fill"></i></span>
        <span>
            <h5>NORA</h5>
            <small>Donor Darah Terintegrasi</small>
        </span>
    </a>

    <div class="nav-label">Menu Utama</div>
    <a class="side-link <?= $a('dashboard') ?>" href="<?= base_url('dashboard') ?>"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>

    <?php if ($role === 'admin'): ?>
        <div class="nav-label">Verifikasi</div>
        <a class="side-link <?= $a('admin/sos', true) ?>" href="<?= base_url('admin/sos') ?>"><i class="bi bi-broadcast-pin"></i> Permintaan SOS <?= $hitung($antrean['sos']) ?></a>
        <a class="side-link <?= $a('admin/stok', true) ?>" href="<?= base_url('admin/stok') ?>"><i class="bi bi-droplet-half"></i> Stok Darah <?= $hitung($antrean['stok']) ?></a>
        <a class="side-link <?= $a('admin/jadwal', true) ?>" href="<?= base_url('admin/jadwal') ?>"><i class="bi bi-calendar-event"></i> Jadwal Donor <?= $hitung($antrean['jadwal']) ?></a>
        <a class="side-link <?= $a('admin/fasilitas', true) ?>" href="<?= base_url('admin/fasilitas') ?>"><i class="bi bi-hospital"></i> Fasilitas <?= $hitung($antrean['fasilitas']) ?></a>
        <a class="side-link <?= $a('admin/pendonor', true) ?>" href="<?= base_url('admin/pendonor') ?>"><i class="bi bi-person-heart"></i> Pendonor <?= $hitung($antrean['pendonor']) ?></a>

        <div class="nav-label">Pengelolaan</div>
        <a class="side-link <?= $a('admin/pengguna', true) ?>" href="<?= base_url('admin/pengguna') ?>"><i class="bi bi-people-fill"></i> Pengguna</a>
        <a class="side-link <?= $a('admin/edukasi', true) ?>" href="<?= base_url('admin/edukasi') ?>"><i class="bi bi-journal-medical"></i> Konten Edukasi</a>
        <a class="side-link <?= $a('admin/notifikasi', true) ?>" href="<?= base_url('admin/notifikasi') ?>"><i class="bi bi-megaphone"></i> Kirim Notifikasi</a>
        <a class="side-link <?= $a('admin/laporan', true) ?>" href="<?= base_url('admin/laporan') ?>"><i class="bi bi-file-earmark-bar-graph"></i> Laporan</a>

        <div class="nav-label">Tampilan Pengguna</div>
        <a class="side-link <?= $a('stok') ?>" href="<?= base_url('stok') ?>"><i class="bi bi-search-heart"></i> Cari Stok</a>
        <a class="side-link <?= $a('jadwal') ?>" href="<?= base_url('jadwal') ?>"><i class="bi bi-calendar2-week"></i> Jadwal Publik</a>
        <a class="side-link <?= $a('lokasi') ?>" href="<?= base_url('lokasi') ?>"><i class="bi bi-geo-alt-fill"></i> Lokasi</a>
        <a class="side-link <?= $a('edukasi', true) ?>" href="<?= base_url('edukasi') ?>"><i class="bi bi-book"></i> Edukasi</a>

    <?php elseif ($role === 'pendonor'): ?>
        <a class="side-link <?= $a('sos', true) ?>" href="<?= base_url('sos') ?>"><i class="bi bi-broadcast-pin"></i> Permintaan Darah</a>
        <a class="side-link <?= $a('stok') ?>" href="<?= base_url('stok') ?>"><i class="bi bi-search-heart"></i> Cari Stok Darah</a>
        <div class="nav-label">Informasi</div>
        <a class="side-link <?= $a('jadwal') ?>" href="<?= base_url('jadwal') ?>"><i class="bi bi-calendar2-week"></i> Jadwal Donor</a>
        <a class="side-link <?= $a('lokasi') ?>" href="<?= base_url('lokasi') ?>"><i class="bi bi-geo-alt-fill"></i> Lokasi Layanan</a>
        <a class="side-link <?= $a('edukasi', true) ?>" href="<?= base_url('edukasi') ?>"><i class="bi bi-book"></i> Edukasi Donor</a>

    <?php elseif ($role === 'pencari'): ?>
        <a class="side-link <?= $a('sos/buat') ?>" href="<?= base_url('sos/buat') ?>"><i class="bi bi-plus-circle-fill"></i> Buat Permintaan SOS</a>
        <a class="side-link <?= ($path === 'sos/saya' || str_starts_with($path, 'sos/detail')) ? 'active' : '' ?>" href="<?= base_url('sos/saya') ?>"><i class="bi bi-broadcast-pin"></i> SOS Saya</a>
        <a class="side-link <?= $a('stok') ?>" href="<?= base_url('stok') ?>"><i class="bi bi-search-heart"></i> Cari Stok Darah</a>
        <div class="nav-label">Informasi</div>
        <a class="side-link <?= $a('jadwal') ?>" href="<?= base_url('jadwal') ?>"><i class="bi bi-calendar2-week"></i> Jadwal Donor</a>
        <a class="side-link <?= $a('lokasi') ?>" href="<?= base_url('lokasi') ?>"><i class="bi bi-geo-alt-fill"></i> Lokasi Layanan</a>
        <a class="side-link <?= $a('edukasi', true) ?>" href="<?= base_url('edukasi') ?>"><i class="bi bi-book"></i> Edukasi Donor</a>

    <?php elseif ($role === 'fasilitas'): ?>
        <div class="nav-label">Layanan Fasilitas</div>
        <a class="side-link <?= $a('stok/kelola') ?>" href="<?= base_url('stok/kelola') ?>"><i class="bi bi-droplet-half"></i> Kelola Stok Darah</a>
        <a class="side-link <?= ($path === 'jadwal/saya' || $path === 'jadwal/ajukan') ? 'active' : '' ?>" href="<?= base_url('jadwal/saya') ?>"><i class="bi bi-calendar-plus"></i> Kegiatan Donor Saya</a>
        <div class="nav-label">Informasi</div>
        <a class="side-link <?= $a('stok') ?>" href="<?= base_url('stok') ?>"><i class="bi bi-search-heart"></i> Cari Stok Darah</a>
        <a class="side-link <?= $a('jadwal') ?>" href="<?= base_url('jadwal') ?>"><i class="bi bi-calendar2-week"></i> Jadwal Donor</a>
        <a class="side-link <?= $a('lokasi') ?>" href="<?= base_url('lokasi') ?>"><i class="bi bi-geo-alt-fill"></i> Lokasi Layanan</a>
        <a class="side-link <?= $a('edukasi', true) ?>" href="<?= base_url('edukasi') ?>"><i class="bi bi-book"></i> Edukasi Donor</a>
    <?php endif; ?>

    <div class="nav-label">Akun</div>
    <a class="side-link <?= $a('notifikasi', true) ?>" href="<?= base_url('notifikasi') ?>"><i class="bi bi-bell-fill"></i> Notifikasi <?= $hitung($belum) ?></a>
    <a class="side-link <?= $a('profil', true) ?>" href="<?= base_url('profil') ?>"><i class="bi bi-person-circle"></i> Profil Saya</a>

    <div class="user-card mt-4">
        <div class="avatar"><?= esc(inisial($nama)) ?></div>
        <div>
            <div class="nama"><?= esc($nama) ?></div>
            <div class="role"><?= esc(label_role($role)) ?></div>
        </div>
        <a href="<?= base_url('logout') ?>" title="Keluar"><i class="bi bi-box-arrow-right"></i></a>
    </div>
</aside>

<!-- ================= KONTEN ================= -->
<div class="main">
    <header class="topbar">
        <button class="menu-btn" id="menuBtn" aria-label="Menu"><i class="bi bi-list"></i></button>
        <span class="tanggal d-none d-sm-inline"><i class="bi bi-calendar3"></i> <?= esc(hari_indo(date('Y-m-d')) . ', ' . tgl_indo(date('Y-m-d'))) ?></span>

        <div class="ms-auto d-flex align-items-center gap-2">
            <?php if ($role === 'pencari'): ?>
                <a href="<?= base_url('sos/buat') ?>" class="btn btn-nora btn-sm"><i class="bi bi-broadcast-pin"></i> Buat SOS</a>
            <?php elseif ($role === 'fasilitas'): ?>
                <a href="<?= base_url('stok/kelola') ?>" class="btn btn-nora btn-sm"><i class="bi bi-droplet-half"></i> Perbarui Stok</a>
            <?php elseif ($role === 'pendonor'): ?>
                <a href="<?= base_url('sos') ?>" class="btn btn-nora btn-sm"><i class="bi bi-heart-pulse-fill"></i> Lihat Permintaan</a>
            <?php elseif ($role === 'admin'): ?>
                <a href="<?= base_url('admin/sos?status=Menunggu+Verifikasi') ?>" class="btn btn-nora btn-sm"><i class="bi bi-shield-check"></i> Verifikasi SOS</a>
            <?php endif; ?>
            <a href="<?= base_url('notifikasi') ?>" class="bell" title="Notifikasi">
                <i class="bi bi-bell"></i>
                <?php if ($belum > 0): ?><span class="dot"><?= $belum > 99 ? '99+' : $belum ?></span><?php endif; ?>
            </a>
        </div>
    </header>

    <main class="content">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-auto"><i class="bi bi-check-circle-fill me-2"></i><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-auto"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>
        <?php if (! empty($errors)): ?>
            <div class="alert alert-danger">
                <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Periksa kembali isian Anda:</strong>
                <ul class="mb-0 mt-1">
                    <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>

    <div class="footer-note">
        <i class="bi bi-shield-check me-1"></i> <?= esc(Referensi::DISCLAIMER_UMUM) ?>
    </div>
</div>

<!-- ================= MODAL HAPUS ================= -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:20px;border:0">
            <form method="post" action="">
                <?= csrf_field() ?>
                <div class="modal-body text-center p-4">
                    <div class="stat-icon mx-auto mb-3"><i class="bi bi-trash3"></i></div>
                    <h5>Hapus data ini?</h5>
                    <p class="text-muted mb-4"><strong class="nama-hapus"></strong> akan dihapus permanen dan tidak bisa dikembalikan.</p>
                    <button type="button" class="btn btn-ghost me-2" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-nora">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('js/nora.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
