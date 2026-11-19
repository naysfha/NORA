<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-head"><div><h2>Permintaan SOS</h2><p>Periksa kelengkapan, verifikasi, lalu perbarui status permintaan darah.</p></div></div>
<div class="card-soft mb-4"><div class="card-body-pad"><form method="get" class="row g-2">
    <div class="col-md-4"><div class="search-box"><i class="bi bi-search"></i><input class="form-control" name="q" placeholder="Cari RS, lokasi, atau pencari..." value="<?= esc($filter['q']) ?>"></div></div>
    <div class="col-md-3"><select class="form-select" name="status"><option value="">Semua status</option><?php foreach ($status as $s): ?><option <?= $filter['status'] === $s ? 'selected' : '' ?>><?= esc($s) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-2"><select class="form-select" name="golongan"><option value="">Golongan</option><?php foreach ($golongan as $g): ?><option <?= $filter['golongan'] === $g ? 'selected' : '' ?>><?= esc($g) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-3 d-flex gap-2"><button class="btn btn-nora flex-grow-1">Filter</button><a href="<?= base_url('admin/sos') ?>" class="btn btn-ghost">Reset</a></div>
</form></div></div>
<div class="card-soft"><div class="table-responsive"><table class="table table-nora">
<thead><tr><th>No</th><th>Gol.</th><th>Pencari &amp; Rumah Sakit</th><th>Urgensi</th><th>Respons</th><th>Status</th><th></th></tr></thead>
<tbody><?php if (! $sos): ?><tr><td colspan="7"><div class="empty-state"><i class="bi bi-inbox"></i>Tidak ada permintaan.</div></td></tr>
<?php else: foreach ($sos as $s): ?>
<tr><td>#<?= $s['id_sos'] ?></td><td><?= chip_golongan($s['golongan_darah']) ?> <span class="cell-sub">&times;<?= (int) $s['jumlah_dibutuhkan'] ?></span></td>
<td><div class="cell-title"><?= esc($s['rumah_sakit']) ?></div><div class="cell-sub"><?= esc($s['nama_pencari']) ?> &middot; <?= waktu_lalu($s['tanggal_pengajuan']) ?></div></td>
<td><?= badge_urgensi($s['tingkat_urgensi']) ?></td><td><i class="bi bi-people"></i> <?= (int) $s['jumlah_respons'] ?></td><td><?= badge_status($s['status']) ?></td>
<td class="text-end"><a class="btn-icon" href="<?= base_url('admin/sos/detail/' . $s['id_sos']) ?>"><i class="bi bi-arrow-right"></i></a></td></tr>
<?php endforeach; endif; ?></tbody></table></div></div>
<?= $this->endSection() ?>
