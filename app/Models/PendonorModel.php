<?php

namespace App\Models;

use CodeIgniter\Model;

/** Entitas Pendonor (D2 - Data Pendonor). */
class PendonorModel extends Model
{
    protected $table         = 'pendonor';
    protected $primaryKey    = 'id_pendonor';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_user', 'golongan_darah', 'riwayat_donor', 'lokasi', 'status_verifikasi'];

    /** Daftar pendonor beserta data akunnya (untuk panel admin). */
    public function daftar(array $filter = []): array
    {
        $b = $this->select('pendonor.*, p.nama, p.email, p.no_telp, p.tanggal_daftar')
            ->join('pengguna p', 'p.id_user = pendonor.id_user')
            ->orderBy('p.nama');

        if (! empty($filter['q'])) {
            $b->groupStart()
                ->like('p.nama', $filter['q'])
                ->orLike('pendonor.lokasi', $filter['q'])
                ->groupEnd();
        }
        if (! empty($filter['golongan'])) {
            $b->where('pendonor.golongan_darah', $filter['golongan']);
        }
        if (! empty($filter['status'])) {
            $b->where('pendonor.status_verifikasi', $filter['status']);
        }

        return $b->findAll();
    }

    public function dariUser(int $idUser): ?array
    {
        return $this->where('id_user', $idUser)->first();
    }
}
