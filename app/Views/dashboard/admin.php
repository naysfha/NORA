<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php use App\Libraries\Referensi; $maks = max(1, max($stokGol ?: [1])); ?>

<div class="page-head">
    <div><h2>Dashboard Admin</h2><p>Ringkasan operasional NORA dan antrean verifikasi.</p></div>
</div>

<div class="row g-3 mb-4">
    <?php $kartu = [
        ['bi-people-fill', '', $total['pengguna'], 'Pengguna terdaftar'],
        ['bi-person-heart', 'ok', $total['pendonor'], 'Pendonor'],
        ['bi-hospital', 'info', $total['fasilitas'], 'Fasilitas & lokasi'],
        ['bi-droplet-fill', 'warn', $total['kantong'], 'Kantong darah (terverifikasi)'],
    ]; foreach ($kartu as $k): ?>
        <div class="col-6 col-xl-3"><div class="card-soft stat-card">
            <div class="stat-icon <?= $k[1] ?>"><i class="bi <?= $k[0] ?>"></i></div>
            <div><div class="angka"><?= (int) $k[2] ?></div><div class="label"><?= $k[3] ?></div></div>
        </div></div>
    <?php endforeach; ?>
</div>

<div class="card-soft accent mb-4">
    <div class="card-head"><h5><i class="bi bi-inbox-fill me-2 text-danger"></i>Antrean Verifikasi</h5></div>
    <div class="card-body-pad">
        <div class="row g-2">
            <?php $q = [
                ['admin/sos?status=Menunggu+Verifikasi', 'Permintaan SOS', $antrean['sos'], 'bi-broadcast-pin'],
                ['admin/stok?status=Menunggu', 'Pembaruan stok', $antrean['stok'], 'bi-droplet-half'],
                ['admin/jadwal?status=Menunggu', 'Pengajuan kegiatan', $antrean['jadwal'], 'bi-calendar-event'],
                ['admin/fasilitas', 'Fasilitas baru', $antrean['fasilitas'], 'bi-hospital'],
                ['admin/pendonor?status=Menunggu', 'Profil pendonor', $antrean['pendonor'], 'bi-person-heart'],
            ]; foreach ($q as $i): ?>
                <div class="col-6 col-md"><a href="<?= base_url($i[0]) ?>" class="blood-tile <?= $i[2] ? 'aktif' : '' ?>">
                    <div class="drop"><i class="bi <?= $i[3] ?>"></i></div>
                    <div class="gol"><?= (int) $i[2] ?></div>
                    <div class="jml"><?= $i[1] ?></div>
                </a></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card-soft h-100">
            <div class="card-head"><h5>Permintaan SOS Aktif</h5><a href="<?= base_url('admin/sos') ?>" class="btn btn-ghost btn-sm">Semua</a></div>
            <div class="table-responsive"><table class="table table-nora">
                <thead><tr><th>Gol.</th><th>Rumah Sakit</th><th>Urgensi</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php if (! $sosBaru): ?><tr><td colspan="5"><div class="empty-state"><i class="bi bi-inbox"></i>Tidak ada SOS aktif.</div></td></tr>
                <?php else: foreach (array_slice($sosBaru, 0, 6) as $s): ?>
                    <tr>
                        <td><?= chip_golongan($s['golongan_darah']) ?></td>
                        <td><div class="cell-title"><?= esc($s['rumah_sakit']) ?></div><div class="cell-sub"><?= esc($s['nama_pencari']) ?> &middot; <?= (int) $s['jumlah_dibutuhkan'] ?> kantong</div></td>
                        <td><?= badge_urgensi($s['tingkat_urgensi']) ?></td>
                        <td><?= badge_status($s['status']) ?></td>
                        <td class="text-end"><a class="btn-icon" href="<?= base_url('admin/sos/detail/' . $s['id_sos']) ?>"><i class="bi bi-arrow-right"></i></a></td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table></div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card-soft mb-4">
            <div class="card-head"><h5>Stok per Golongan</h5></div>
            <div class="card-body-pad">
                <?php foreach (Referensi::GOLONGAN as $g): $n = $stokGol[$g] ?? 0; ?>
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <?= chip_golongan($g) ?>
                        <div class="flex-grow-1"><div class="level" style="margin:0;height:9px"><span style="width:<?= round($n / $maks * 100) ?>%"></span></div></div>
                        <strong style="width:34px;text-align:right"><?= (int) $n ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="card-soft">
            <div class="card-head"><h5>Kegiatan Mendatang</h5></div>
            <?php if (! $jadwal): ?><div class="empty-state"><i class="bi bi-calendar-x"></i>Belum ada kegiatan.</div>
            <?php else: foreach ($jadwal as $j): ?>
                <div class="event-card">
                    <div class="event-date"><div class="bln"><?= esc(strtoupper(substr(bulan_indo((int) date('n', strtotime($j['tanggal']))), 0, 3))) ?></div><div class="tgl"><?= date('d', strtotime($j['tanggal'])) ?></div></div>
                    <div class="event-info"><h6><?= esc($j['nama_kegiatan']) ?></h6><div class="meta"><span><i class="bi bi-clock"></i><?= esc($j['waktu']) ?></span></div></div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
