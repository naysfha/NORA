<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $edit = $mode === 'edit'; $v = static fn ($k) => old($k, $pengguna[$k] ?? ''); ?>
<div class="page-head"><div><h2><?= $edit ? 'Edit' : 'Tambah' ?> Pengguna</h2></div><a href="<?= base_url('admin/pengguna') ?>" class="btn btn-ghost">Kembali</a></div>
<div class="card-soft accent" style="max-width:720px"><div class="card-body-pad">
<form method="post" action="<?= base_url($edit ? 'admin/pengguna/update/' . $pengguna['id_user'] : 'admin/pengguna/store') ?>"><?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">Nama</label><input class="form-control" name="nama" required maxlength="100" value="<?= esc($v('nama')) ?>"></div>
    <div class="row g-3 mb-3">
        <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?= esc($v('email')) ?>"></div>
        <div class="col-md-6"><label class="form-label">No. Telepon</label><input class="form-control" name="no_telp" value="<?= esc($v('no_telp')) ?>"></div>
    </div>
    <div class="mb-3"><label class="form-label">Peran</label>
        <?php if ($edit): ?><input class="form-control" disabled value="<?= esc(label_role($pengguna['role'])) ?>"><div class="form-text">Peran tidak dapat diubah setelah akun dibuat.</div>
        <?php else: ?><select class="form-select" name="role" id="roleAdmin"><?php foreach ($role as $k => $lb): ?><option value="<?= $k ?>" <?= old('role') === $k ? 'selected' : '' ?>><?= esc($lb) ?></option><?php endforeach; ?></select><?php endif; ?></div>
    <?php if (! $edit): ?><div class="mb-3" id="boxAlamatAdmin" style="display:none"><label class="form-label">Alamat Fasilitas</label><input class="form-control" name="alamat" maxlength="255" value="<?= esc(old('alamat')) ?>"></div><?php endif; ?>
    <div class="mb-4"><label class="form-label">Kata Sandi <?= $edit ? '<span class="fw-normal text-muted">(kosongkan bila tidak diubah)</span>' : '' ?></label><input type="password" class="form-control" name="password" minlength="6" <?= $edit ? '' : 'required' ?>></div>
    <button class="btn btn-nora"><i class="bi bi-save"></i> Simpan</button>
</form></div></div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>var r=document.getElementById('roleAdmin'),b=document.getElementById('boxAlamatAdmin');if(r&&b){var f=function(){b.style.display=r.value==='fasilitas'?'':'none'};r.addEventListener('change',f);f();}</script>
<?= $this->endSection() ?>
