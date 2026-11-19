<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-head"><div><h2>Kirim Notifikasi</h2><p>Umumkan kebutuhan golongan darah tertentu, pembaruan layanan, atau informasi lain.</p></div></div>
<div class="row g-4">
<div class="col-lg-5"><div class="card-soft accent"><div class="card-head"><h5><i class="bi bi-megaphone-fill me-2 text-danger"></i>Notifikasi Baru</h5></div><div class="card-body-pad">
<form method="post" action="<?= base_url('admin/notifikasi/kirim') ?>"><?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">Penerima</label><select class="form-select" name="target" id="target"><?php foreach ($target as $k => $v): ?><option value="<?= $k ?>" <?= old('target') === $k ? 'selected' : '' ?>><?= esc($v) ?></option><?php endforeach; ?></select></div>
    <div class="mb-3" id="boxGolongan" style="display:none"><label class="form-label">Golongan Darah</label><select class="form-select" name="golongan"><?php foreach ($golongan as $g): ?><option <?= old('golongan') === $g ? 'selected' : '' ?>><?= esc($g) ?></option><?php endforeach; ?></select><div class="form-text">Hanya pendonor terverifikasi dengan golongan ini.</div></div>
    <div class="mb-3"><label class="form-label">Jenis Notifikasi</label><select class="form-select" name="jenis"><?php foreach ($jenis as $k => $v): ?><option value="<?= $k ?>" <?= old('jenis') === $k ? 'selected' : '' ?>><?= esc($v) ?></option><?php endforeach; ?></select></div>
    <div class="mb-4"><label class="form-label">Isi Pesan</label><textarea class="form-control" name="isi" rows="4" maxlength="500" required><?= esc(old('isi')) ?></textarea></div>
    <button class="btn btn-nora w-100"><i class="bi bi-send-fill"></i> Kirim Notifikasi</button>
</form></div></div></div>
<div class="col-lg-7"><div class="card-soft"><div class="card-head"><h5>Notifikasi Terkirim (40 terbaru)</h5></div>
<div class="table-responsive"><table class="table table-nora"><thead><tr><th>Penerima</th><th>Jenis</th><th>Pesan</th><th>Status</th></tr></thead><tbody>
<?php if (! $terkirim): ?><tr><td colspan="4"><div class="empty-state"><i class="bi bi-bell-slash"></i>Belum ada.</div></td></tr>
<?php else: foreach ($terkirim as $n): ?><tr><td><div class="cell-title"><?= esc($n['nama']) ?></div><div class="cell-sub"><?= esc(label_role($n['role'])) ?></div></td><td><span class="badge-status diproses"><?= esc($n['jenis_notifikasi']) ?></span></td><td style="max-width:300px"><div class="small"><?= esc(potong($n['isi_pesan'], 110)) ?></div><div class="cell-sub"><?= waktu_lalu($n['tanggal_kirim']) ?></div></td><td><?= $n['status_baca'] ? '<span class="badge-status selesai">Dibaca</span>' : '<span class="badge-status menunggu">Belum</span>' ?></td></tr><?php endforeach; endif; ?>
</tbody></table></div></div></div>
</div>
<?= $this->endSection() ?>
