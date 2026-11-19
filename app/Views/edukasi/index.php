<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-head"><div><h2>Edukasi Donor Darah</h2><p>Pelajari persyaratan, persiapan, proses, dan perawatan setelah donor.</p></div></div>

<div class="card-soft mb-4"><div class="card-body-pad">
    <form method="get" class="row g-3 align-items-center">
        <div class="col-md-5"><div class="search-box"><i class="bi bi-search"></i><input class="form-control" name="q" placeholder="Cari artikel edukasi..." value="<?= esc($filter['q']) ?>"></div></div>
        <input type="hidden" name="kategori" value="<?= esc($filter['kategori']) ?>">
        <div class="col-md-7"><div class="pills">
            <a class="pill <?= $filter['kategori'] === '' ? 'active' : '' ?>" href="<?= base_url('edukasi' . ($filter['q'] ? '?q=' . urlencode($filter['q']) : '')) ?>">Semua</a>
            <?php foreach ($kategori as $k): ?>
                <a class="pill <?= $filter['kategori'] === $k ? 'active' : '' ?>" href="<?= base_url('edukasi?kategori=' . urlencode($k) . ($filter['q'] ? '&q=' . urlencode($filter['q']) : '')) ?>"><?= esc($k) ?></a>
            <?php endforeach; ?>
        </div></div>
    </form>
</div></div>

<?php if (! $artikel): ?>
    <div class="card-soft"><div class="empty-state"><i class="bi bi-journal-x"></i>Tidak ada artikel yang cocok.</div></div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($artikel as $a): ?>
            <div class="col-md-6 col-xl-4"><a class="card-soft edu-card" href="<?= base_url('edukasi/' . $a['id_edukasi']) ?>">
                <span class="kat"><?= esc($a['kategori']) ?></span>
                <h6><?= esc($a['judul']) ?></h6>
                <p><?= esc($a['ringkasan'] ?: potong($a['isi'], 110)) ?></p>
            </a></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
