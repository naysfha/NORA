<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php use App\Libraries\Referensi; $aktif = $fasilitas && $fasilitas['status_verifikasi'] === 'Terverifikasi'; ?>

<div class="page-head">
    <div><h2><?= esc($fasilitas['nama_fasilitas'] ?? 'Fasilitas') ?></h2><p>Sampaikan informasi stok dan kegiatan donor melalui mekanisme yang diverifikasi admin.</p></div>
    <?php if ($aktif): ?><a href="<?= base_url('stok/kelola') ?>" class="btn btn-nora"><i class="bi bi-droplet-half"></i> Perbarui Stok</a><?php endif; ?>
</div>

<?php if (! $aktif): ?>
    <div class="note-box warn mb-4"><i class="bi bi-hourglass-split"></i><div><strong>Fasilitas belum diverifikasi.</strong> Status saat ini: <?= esc($fasilitas['status_verifikasi'] ?? '-') ?>. Anda dapat mengirim data stok dan mengajukan kegiatan setelah admin memverifikasi fasilitas Anda. Pastikan nama, alamat, dan kontak di <a href="<?= base_url('profil') ?>" class="fw-bold">profil</a> sudah benar.</div></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card-soft h-100">
            <div class="card-head"><h5>Catatan Stok Terakhir</h5><?php if ($menunggu): ?><span class="badge-status menunggu"><?= $menunggu ?> menunggu verifikasi</span><?php endif; ?></div>
            <div class="card-body-pad"><div class="blood-grid">
                <?php foreach (Referensi::GOLONGAN as $g): $s = $stok[$g] ?? null; ?>
                    <div class="blood-tile <?= ($s && $s['jumlah_kantong'] > 0) ? '' : 'habis' ?>">
                        <div class="gol"><?= esc($g) ?></div>
                        <div class="jml"><b><?= $s ? (int) $s['jumlah_kantong'] : '-' ?></b> kantong</div>
                        <?php if ($s): ?><div class="mt-1"><?= badge_status($s['status_verifikasi']) ?></div><?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div></div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card-soft h-100">
            <div class="card-head"><h5>Kegiatan Donor Saya</h5><?php if ($aktif): ?><a href="<?= base_url('jadwal/ajukan') ?>" class="btn btn-ghost btn-sm">Ajukan</a><?php endif; ?></div>
            <?php if (! $jadwal): ?><div class="empty-state"><i class="bi bi-calendar-x"></i>Belum ada kegiatan yang diajukan.</div>
            <?php else: foreach ($jadwal as $j): ?>
                <div class="event-card"><div class="event-date"><div class="bln"><?= esc(strtoupper(substr(bulan_indo((int) date('n', strtotime($j['tanggal']))), 0, 3))) ?></div><div class="tgl"><?= date('d', strtotime($j['tanggal'])) ?></div></div>
                <div class="event-info"><h6><?= esc($j['nama_kegiatan']) ?></h6><?= badge_status($j['status']) ?></div></div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
