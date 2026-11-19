<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Entitas Stok Darah (D3 - Data Stok Darah).
 * Setiap pembaruan disimpan sebagai catatan baru sehingga riwayatnya tetap ada;
 * yang ditampilkan ke publik adalah catatan TERBARU yang sudah TERVERIFIKASI
 * untuk setiap pasangan fasilitas + golongan darah.
 */
class StokModel extends Model
{
    protected $table         = 'stok_darah';
    protected $primaryKey    = 'id_stok';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'tanggal_update';
    protected $updatedField  = '';
    protected $allowedFields = ['id_fasilitas', 'golongan_darah', 'jumlah_kantong', 'sumber', 'status_verifikasi'];

    private const TERBARU = "stok_darah.id_stok = (SELECT MAX(s2.id_stok) FROM stok_darah s2
        WHERE s2.id_fasilitas = stok_darah.id_fasilitas
          AND s2.golongan_darah = stok_darah.golongan_darah
          AND s2.status_verifikasi = 'Terverifikasi')";

    /** Pencarian stok berdasarkan golongan darah dan lokasi/fasilitas. */
    public function cari(?string $golongan = null, ?string $lokasi = null): array
    {
        $b = $this->select('stok_darah.*, f.nama_fasilitas, f.alamat, f.kontak')
            ->join('fasilitas_kesehatan f', 'f.id_fasilitas = stok_darah.id_fasilitas')
            ->where('stok_darah.status_verifikasi', 'Terverifikasi')
            ->where('f.status_verifikasi', 'Terverifikasi')
            ->where(self::TERBARU, null, false)
            ->orderBy('f.nama_fasilitas')
            ->orderBy('stok_darah.golongan_darah');

        if ($golongan) {
            $b->where('stok_darah.golongan_darah', $golongan);
        }
        if ($lokasi) {
            $b->groupStart()->like('f.nama_fasilitas', $lokasi)->orLike('f.alamat', $lokasi)->groupEnd();
        }

        return $b->findAll();
    }

    /** Total kantong per golongan (dari data terbaru yang terverifikasi). */
    public function totalPerGolongan(): array
    {
        $baris = $this->select('stok_darah.golongan_darah, SUM(stok_darah.jumlah_kantong) AS total', false)
            ->join('fasilitas_kesehatan f', 'f.id_fasilitas = stok_darah.id_fasilitas')
            ->where('stok_darah.status_verifikasi', 'Terverifikasi')
            ->where('f.status_verifikasi', 'Terverifikasi')
            ->where(self::TERBARU, null, false)
            ->groupBy('stok_darah.golongan_darah')
            ->findAll();

        $hasil = [];
        foreach ($baris as $r) {
            $hasil[$r['golongan_darah']] = (int) $r['total'];
        }

        return $hasil;
    }

    /** Catatan terbaru (status apa pun) per golongan untuk satu fasilitas. */
    public function terbaruFasilitas(int $idFasilitas): array
    {
        $semua = $this->where('id_fasilitas', $idFasilitas)->orderBy('id_stok', 'DESC')->findAll();
        $hasil = [];
        foreach ($semua as $r) {
            if (! isset($hasil[$r['golongan_darah']])) {
                $hasil[$r['golongan_darah']] = $r;
            }
        }

        return $hasil;
    }
}
