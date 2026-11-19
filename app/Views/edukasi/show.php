<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="row g-4">
    <div class="col-lg-8">
        <a href="<?= base_url('edukasi') ?>" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> Kembali ke edukasi</a>
        <div class="card-soft accent mt-2"><div class="card-body-pad p-4 p-md-5">
            <span class="badge-status terverifikasi"><?= esc($artikel['kategori']) ?></span>
            <h2 class="mt-3 mb-1" style="color:var(--red-900)"><?= esc($artikel['judul']) ?></h2>
            <p class="text-muted small">Diperbarui oleh Admin PMI &middot; <?= tgl_indo($artikel['tanggal_dibuat']) ?></p>
            <hr>
            <div class="edu-body"><?php foreach (preg_split('/\n{2,}/', trim($artikel['isi'])) as $paragraf): ?><p><?= nl2br(esc($paragraf)) ?></p><?php endforeach; ?></div>
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="card-soft"><div class="card-head"><h5>Artikel Terkait</h5></div>
        <?php if (! $lainnya): ?><div class="empty-state">Tidak ada artikel lain di kategori ini.</div>
        <?php else: foreach ($lainnya as $l): ?>
            <a class="notif-item" href="<?= base_url('edukasi/' . $l['id_edukasi']) ?>"><div class="notif-ikon"><i class="bi bi-book"></i></div><div><p class="isi fw-bold"><?= esc($l['judul']) ?></p></div></a>
        <?php endforeach; endif; ?></div>
    </div>
</div>
<?= $this->endSection() ?>
