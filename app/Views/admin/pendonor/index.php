<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-head"><div><h2>Data Pendonor</h2><p>Verifikasi profil pendonor sebelum tampil sebagai profil aktif.</p></div></div>
<div class="card-soft mb-4"><div class="card-body-pad"><form method="get" class="row g-2">
    <div class="col-md-5"><div class="search-box"><i class="bi bi-search"></i><input class="form-control" name="q" placeholder="Cari nama atau lokasi..." value="<?= esc($filter['q']) ?>"></div></div>
    <div class="col-md-2"><select class="form-select" name="golongan"><option value="">Golongan</option><?php foreach ($golongan as $g): ?><option <?= $filter['golongan'] === $g ? 'selected' : '' ?>><?= esc($g) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-2"><select class="form-select" name="status"><option value="">Status</option><?php foreach (['Menunggu', 'Terverifikasi'] as $s): ?><option <?= $filter['status'] === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select></div>
    <div class="col-md-3 d-flex gap-2"><button class="btn btn-nora flex-grow-1">Filter</button><a href="<?= base_url('admin/pendonor') ?>" class="btn btn-ghost">Reset</a></div>
</form></div></div>
<div class="card-soft"><div class="table-responsive"><table class="table table-nora">
<thead><tr><th>Pendonor</th><th>Golongan</th><th>Lokasi</th><th>Riwayat Donor</th><th>Status</th><th></th></tr></thead>
<tbody><?php if (! $pendonor): ?><tr><td colspan="6"><div class="empty-state"><i class="bi bi-person-heart"></i>Tidak ada data.</div></td></tr>
<?php else: foreach ($pendonor as $p): ?>
<tr><td><div class="cell-title"><?= esc($p['nama']) ?></div><div class="cell-sub"><?= esc($p['no_telp'] ?: $p['email']) ?></div></td>
<td><?= chip_golongan($p['golongan_darah']) ?></td><td><?= esc($p['lokasi'] ?: '-') ?></td><td style="max-width:260px"><span class="cell-sub"><?= esc(potong($p['riwayat_donor'], 80) ?: '-') ?></span></td>
<td><?= badge_status($p['status_verifikasi']) ?></td>
<td class="text-end"><form method="post" action="<?= base_url('admin/pendonor/verifikasi/' . $p['id_pendonor']) ?>" class="d-inline"><?= csrf_field() ?>
<?php if ($p['status_verifikasi'] === 'Terverifikasi'): ?><input type="hidden" name="status" value="Menunggu"><button class="btn btn-ghost btn-sm">Batalkan</button>
<?php else: ?><input type="hidden" name="status" value="Terverifikasi"><button class="btn btn-nora btn-sm" <?= $p['golongan_darah'] ? '' : 'disabled' ?>><i class="bi bi-patch-check"></i> Verifikasi</button><?php endif; ?></form></td></tr>
<?php endforeach; endif; ?></tbody></table></div></div>
<?= $this->endSection() ?>
