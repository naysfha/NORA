<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-head"><div><h2>Permintaan SOS Saya</h2><p>Pantau status setiap permintaan darah yang Anda ajukan.</p></div><a href="<?= base_url('sos/buat') ?>" class="btn btn-nora"><i class="bi bi-plus-lg"></i> Buat SOS Baru</a></div>
<div class="note-box sos mb-4"><i class="bi bi-exclamation-octagon-fill"></i><div><?= esc(\App\Libraries\Referensi::DISCLAIMER_SOS) ?></div></div>
<div class="card-soft"><div class="table-responsive"><table class="table table-nora">
<thead><tr><th>No</th><th>Golongan</th><th>Rumah Sakit</th><th>Urgensi</th><th>Respons</th><th>Status</th><th></th></tr></thead>
<tbody><?php if (! $sos): ?><tr><td colspan="7"><div class="empty-state"><i class="bi bi-inbox"></i>Anda belum pernah mengajukan SOS.</div></td></tr>
<?php else: foreach ($sos as $s): ?>
<tr><td>#<?= $s['id_sos'] ?></td><td><?= chip_golongan($s['golongan_darah']) ?> <span class="cell-sub">&times;<?= (int) $s['jumlah_dibutuhkan'] ?></span></td>
<td><div class="cell-title"><?= esc($s['rumah_sakit']) ?></div><div class="cell-sub"><?= esc($s['lokasi']) ?> &middot; <?= waktu_lalu($s['tanggal_pengajuan']) ?></div></td>
<td><?= badge_urgensi($s['tingkat_urgensi']) ?></td><td><i class="bi bi-people"></i> <?= (int) $s['jumlah_respons'] ?></td><td><?= badge_status($s['status']) ?></td>
<td class="text-end"><a class="btn-icon" href="<?= base_url('sos/detail/' . $s['id_sos']) ?>"><i class="bi bi-eye"></i></a></td></tr>
<?php endforeach; endif; ?></tbody></table></div></div>
<?= $this->endSection() ?>
