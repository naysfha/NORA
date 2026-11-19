<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $aktif = $fasilitas && $fasilitas['status_verifikasi'] === 'Terverifikasi'; ?>
<div class="page-head"><div><h2>Kegiatan Donor Saya</h2><p>Pengajuan kegiatan dipublikasikan setelah disetujui admin.</p></div>
<?php if ($aktif): ?><a href="<?= base_url('jadwal/ajukan') ?>" class="btn btn-nora"><i class="bi bi-plus-lg"></i> Ajukan Kegiatan</a><?php endif; ?></div>
<?php if (! $aktif): ?><div class="note-box warn mb-4"><i class="bi bi-hourglass-split"></i><div>Fasilitas Anda belum diverifikasi admin.</div></div><?php endif; ?>
<div class="card-soft"><div class="table-responsive"><table class="table table-nora">
<thead><tr><th>Kegiatan</th><th>Tanggal</th><th>Waktu</th><th>Lokasi</th><th>Status</th></tr></thead>
<tbody><?php if (! $jadwal): ?><tr><td colspan="5"><div class="empty-state"><i class="bi bi-calendar-x"></i>Belum ada pengajuan.</div></td></tr>
<?php else: foreach ($jadwal as $j): ?><tr><td class="cell-title"><?= esc($j['nama_kegiatan']) ?></td><td><?= tgl_indo($j['tanggal']) ?></td><td><?= esc($j['waktu']) ?></td><td><?= esc($j['lokasi']) ?></td><td><?= badge_status($j['status']) ?></td></tr><?php endforeach; endif; ?></tbody>
</table></div></div>
<?= $this->endSection() ?>
