<?php

namespace App\Libraries;

use App\Models\NotifikasiModel;

/**
 * Pembuat notifikasi (modul Notifikasi - DFD proses 7).
 * Dipanggil dari controller saat status SOS, stok, atau jadwal berubah.
 */
class Notifier
{
    /** Kirim ke satu pengguna. */
    public static function kirim(int $idUser, string $jenis, string $isi, ?string $tautan = null): void
    {
        (new NotifikasiModel())->insert([
            'id_user'          => $idUser,
            'jenis_notifikasi' => $jenis,
            'isi_pesan'        => mb_substr($isi, 0, 500),
            'tautan'           => $tautan,
            'status_baca'      => 0,
        ]);
    }

    /** Kirim ke banyak pengguna sekaligus. */
    public static function kirimBanyak(array $idUsers, string $jenis, string $isi, ?string $tautan = null): int
    {
        $idUsers = array_values(array_unique(array_map('intval', $idUsers)));
        if (! $idUsers) {
            return 0;
        }

        $sekarang = date('Y-m-d H:i:s');
        $baris    = [];
        foreach ($idUsers as $id) {
            $baris[] = [
                'id_user'          => $id,
                'jenis_notifikasi' => $jenis,
                'isi_pesan'        => mb_substr($isi, 0, 500),
                'tautan'           => $tautan,
                'tanggal_kirim'    => $sekarang,
                'status_baca'      => 0,
            ];
        }
        db_connect()->table('notifikasi')->insertBatch($baris);

        return count($baris);
    }

    /** Semua pengguna dengan role tertentu. */
    public static function idPenggunaRole(string $role): array
    {
        return array_column(
            db_connect()->table('pengguna')->select('id_user')->where('role', $role)->get()->getResultArray(),
            'id_user'
        );
    }

    /** Pendonor aktif (profil terverifikasi) dengan golongan tertentu. */
    public static function idPendonorGolongan(string $golongan): array
    {
        return array_column(
            db_connect()->table('pendonor')->select('id_user')
                ->where('golongan_darah', $golongan)
                ->where('status_verifikasi', 'Terverifikasi')
                ->get()->getResultArray(),
            'id_user'
        );
    }
}
