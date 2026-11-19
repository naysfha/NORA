<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $ikon = ['SOS' => 'bi-broadcast-pin', 'Golongan' => 'bi-droplet-fill', 'Jadwal' => 'bi-calendar-event', 'Stok' => 'bi-droplet-half', 'Layanan' => 'bi-megaphone']; ?>
<div class="page-head"><div><h2>Notifikasi</h2><p><?= $belum ?> belum dibaca.</p></div>
<?php if ($belum): ?><form method="post" action="<?= base_url('notifikasi/baca-semua') ?>"><?= csrf_field() ?><button class="btn btn-ghost"><i class="bi bi-check2-all"></i> Tandai semua dibaca</button></form><?php endif; ?></div>
<div class="card-soft">
<?php if (! $notifikasi): ?><div class="empty-state"><i class="bi bi-bell-slash"></i>Belum ada notifikasi.</div>
<?php else: foreach ($notifikasi as $n): ?>
    <a class="notif-item <?= $n['status_baca'] ? '' : 'baru' ?>" href="<?= base_url('notifikasi/buka/' . $n['id_notifikasi']) ?>">
        <div class="notif-ikon"><i class="bi <?= $ikon[$n['jenis_notifikasi']] ?? 'bi-bell' ?>"></i></div>
        <div class="flex-grow-1"><p class="isi"><?= esc($n['isi_pesan']) ?></p><span class="waktu"><?= esc(\App\Libraries\Referensi::JENIS_NOTIFIKASI[$n['jenis_notifikasi']] ?? $n['jenis_notifikasi']) ?> &middot; <?= waktu_lalu($n['tanggal_kirim']) ?></span></div>
        <?php if (! $n['status_baca']): ?><span class="badge-status diproses">Baru</span><?php endif; ?>
    </a>
<?php endforeach; endif; ?>
</div>
<?= $this->endSection() ?>
