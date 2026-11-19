<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php use App\Libraries\Referensi; $role = session()->get('role'); ?>
<div class="page-head"><div><h2>Profil Saya</h2><p>Kelola data akun<?= $role === 'pendonor' ? ', golongan darah, dan riwayat donor' : ($role === 'fasilitas' ? ' dan data fasilitas' : '') ?>.</p></div></div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card-soft mb-4">
            <div class="card-head"><h5><i class="bi bi-person-circle me-2 text-danger"></i>Data Akun</h5><span class="badge-status terverifikasi"><?= esc(label_role($role)) ?></span></div>
            <div class="card-body-pad">
                <form method="post" action="<?= base_url('profil/akun') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3"><label class="form-label">Nama</label><input class="form-control" name="nama" required maxlength="100" value="<?= esc(old('nama', $akun['nama'])) ?>"></div>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6"><label class="form-label">No. Telepon</label><input class="form-control" name="no_telp" value="<?= esc(old('no_telp', $akun['no_telp'])) ?>"></div>
                        <div class="col-sm-6"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?= esc(old('email', $akun['email'])) ?>"></div>
                    </div>
                    <div class="form-text mb-3">Terdaftar sejak <?= tgl_indo($akun['tanggal_daftar']) ?>.</div>
                    <button class="btn btn-nora"><i class="bi bi-save"></i> Simpan Akun</button>
                </form>
            </div>
        </div>
        <div class="card-soft">
            <div class="card-head"><h5><i class="bi bi-key-fill me-2 text-danger"></i>Ubah Kata Sandi</h5></div>
            <div class="card-body-pad">
                <form method="post" action="<?= base_url('profil/password') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3"><label class="form-label">Kata Sandi Lama</label><input type="password" class="form-control" name="password_lama" required></div>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6"><label class="form-label">Kata Sandi Baru</label><input type="password" class="form-control" name="password_baru" required minlength="6"></div>
                        <div class="col-sm-6"><label class="form-label">Ulangi Kata Sandi Baru</label><input type="password" class="form-control" name="konfirmasi" required></div>
                    </div>
                    <button class="btn btn-ghost"><i class="bi bi-shield-lock"></i> Ubah Kata Sandi</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
    <?php if ($pendonor): ?>
        <div class="card-soft accent">
            <div class="card-head"><h5><i class="bi bi-droplet-fill me-2 text-danger"></i>Profil Pendonor</h5><?= badge_status($pendonor['status_verifikasi']) ?></div>
            <div class="card-body-pad">
                <form method="post" action="<?= base_url('profil/pendonor') ?>">
                    <?= csrf_field() ?>
                    <label class="form-label">Golongan Darah</label>
                    <div class="gol-picker mb-3">
                        <?php $sel = old('golongan_darah', $pendonor['golongan_darah']); foreach ($golongan as $g): ?>
                            <input type="radio" name="golongan_darah" id="g_<?= esc($g, 'attr') ?>" value="<?= esc($g, 'attr') ?>" <?= $sel === $g ? 'checked' : '' ?>>
                            <label for="g_<?= esc($g, 'attr') ?>"><?= esc($g) ?></label>
                        <?php endforeach; ?>
                    </div>
                    <div class="mb-3"><label class="form-label">Lokasi / Domisili</label><input class="form-control" name="lokasi" required maxlength="150" placeholder="contoh: Palu Selatan" value="<?= esc(old('lokasi', $pendonor['lokasi'])) ?>"></div>
                    <div class="mb-3"><label class="form-label">Riwayat Donor</label><textarea class="form-control" name="riwayat_donor" rows="4" maxlength="1000" placeholder="contoh: sudah 2 kali donor, terakhir bulan lalu di PMI"><?= esc(old('riwayat_donor', $pendonor['riwayat_donor'])) ?></textarea></div>
                    <div class="note-box info mb-3"><i class="bi bi-info-circle-fill"></i><div>Profil baru atau perubahan golongan darah akan diverifikasi admin sebelum menjadi profil aktif. Data hanya dilihat pihak yang berwenang.</div></div>
                    <button class="btn btn-nora"><i class="bi bi-save"></i> Simpan Profil Pendonor</button>
                </form>
            </div>
        </div>
    <?php elseif ($fasilitas): ?>
        <div class="card-soft accent">
            <div class="card-head"><h5><i class="bi bi-hospital me-2 text-danger"></i>Data Fasilitas</h5><?= badge_status($fasilitas['status_verifikasi']) ?></div>
            <div class="card-body-pad">
                <form method="post" action="<?= base_url('profil/fasilitas') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3"><label class="form-label">Nama Fasilitas</label><input class="form-control" name="nama_fasilitas" required maxlength="150" value="<?= esc(old('nama_fasilitas', $fasilitas['nama_fasilitas'])) ?>"></div>
                    <div class="mb-3"><label class="form-label">Alamat</label><input class="form-control" name="alamat" required maxlength="255" value="<?= esc(old('alamat', $fasilitas['alamat'])) ?>"></div>
                    <div class="mb-3"><label class="form-label">Kontak</label><input class="form-control" name="kontak" maxlength="50" value="<?= esc(old('kontak', $fasilitas['kontak'])) ?>"></div>
                    <button class="btn btn-nora"><i class="bi bi-save"></i> Simpan Data Fasilitas</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="card-soft"><div class="empty-state"><i class="bi bi-person-check"></i>Tidak ada data profil tambahan untuk peran <?= esc(label_role($role)) ?>.</div></div>
    <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
