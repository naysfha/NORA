<?php

namespace App\Models;

use CodeIgniter\Model;

/** Entitas Notifikasi (D6 - Data Notifikasi). */
class NotifikasiModel extends Model
{
    protected $table         = 'notifikasi';
    protected $primaryKey    = 'id_notifikasi';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'tanggal_kirim';
    protected $updatedField  = '';
    protected $allowedFields = ['id_user', 'jenis_notifikasi', 'isi_pesan', 'tautan', 'tanggal_kirim', 'status_baca'];

    public function jumlahBelumDibaca(int $idUser): int
    {
        return $this->where('id_user', $idUser)->where('status_baca', 0)->countAllResults();
    }
}
