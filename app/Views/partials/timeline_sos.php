<?php
// Linimasa status SOS. Variabel: $status
$tahap = ['Menunggu Verifikasi' => 'bi-hourglass-split', 'Diverifikasi' => 'bi-patch-check', 'Diproses' => 'bi-arrow-repeat', 'Selesai' => 'bi-check2-circle'];
$urut  = array_keys($tahap);
$pos   = array_search($status, $urut, true);
?>
<div class="steps">
<?php foreach ($tahap as $nama => $ikon): $i = array_search($nama, $urut, true);
    if ($status === 'Ditolak') { $kelas = $i === 0 ? 'selesai' : ''; }
    else { $kelas = $i < $pos ? 'selesai' : ($i === $pos ? ($status === 'Selesai' ? 'selesai' : 'berjalan') : ''); } ?>
    <div class="step <?= $kelas ?>"><div class="bulat"><i class="bi <?= $ikon ?>"></i></div><?= $nama ?></div>
<?php endforeach; ?>
<?php if ($status === 'Ditolak'): ?><div class="step gagal"><div class="bulat"><i class="bi bi-x-lg"></i></div>Ditolak</div><?php endif; ?>
</div>
