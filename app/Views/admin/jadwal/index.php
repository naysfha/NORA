<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-head"><div><h2>Jadwal Kegiatan Donor</h2><p>Setujui pengajuan fasilitas atau kelola kegiatan secara langsung.</p></div><a href="<?= base_url('admin/jadwal/create') ?>" class="btn btn-nora"><i class="bi bi-plus-lg"></i> Tambah Kegiatan</a></div>
<div class="pills mb-4"><?php foreach (['' => 'Semua', 'Menunggu' => 'Menunggu', 'Dipublikasikan' => 'Dipublikasikan', 'Ditolak' => 'Ditolak'] as $k => $lb): ?><a class="pill <?= $status === $k ? 'active' : '' ?>" href="<?= base_url('admin/jadwal' . ($k ? '?status=' . $k : '')) ?>"><?= $lb ?></a><?php endforeach; ?></div>
<div class="card-soft"><div class="table-responsive"><table class="table table-nora">
<thead><tr><th>Kegiatan</th><th>Tanggal &amp; Waktu</th><th>Lokasi</th><th>Penyelenggara</th><th>Status</th><th></th></tr></thead>
<tbody><?php if (! $jadwal): ?><tr><td colspan="6"><div class="empty-state"><i class="bi bi-calendar-x"></i>Tidak ada data.</div></td></tr>
<?php else: foreach ($jadwal as $j): ?>
<tr><td class="cell-title"><?= esc($j['nama_kegiatan']) ?></td><td><?= tgl_indo($j['tanggal']) ?><div class="cell-sub"><?= esc($j['waktu']) ?></div></td><td><?= esc($j['lokasi']) ?></td><td><?= esc($j['penyelenggara']) ?></td><td><?= badge_status($j['status']) ?></td>
<td class="text-end text-nowrap"><?php if ($j['status'] === 'Menunggu'): ?>
<form method="post" action="<?= base_url('admin/jadwal/status/' . $j['id_jadwal']) ?>" class="d-inline"><?= csrf_field() ?><input type="hidden" name="status" value="Dipublikasikan"><button class="btn-icon ok" title="Setujui &amp; publikasikan"><i class="bi bi-check-lg"></i></button></form>
<form method="post" action="<?= base_url('admin/jadwal/status/' . $j['id_jadwal']) ?>" class="d-inline"><?= csrf_field() ?><input type="hidden" name="status" value="Ditolak"><button class="btn-icon del" title="Tolak"><i class="bi bi-x-lg"></i></button></form><?php endif; ?>
<a class="btn-icon" href="<?= base_url('admin/jadwal/edit/' . $j['id_jadwal']) ?>"><i class="bi bi-pencil"></i></a>
<button type="button" class="btn-icon del" data-bs-toggle="modal" data-bs-target="#modalHapus" data-action="<?= base_url('admin/jadwal/delete/' . $j['id_jadwal']) ?>" data-nama="<?= esc($j['nama_kegiatan'], 'attr') ?>"><i class="bi bi-trash3"></i></button></td></tr>
<?php endforeach; endif; ?></tbody></table></div></div>
<?= $this->endSection() ?>
