<?php

namespace App\Models;

use CodeIgniter\Model;

/** Entitas Fasilitas Kesehatan / Unit Donor Darah / Lokasi layanan. */
class FasilitasModel extends Model
{
    protected $table         = 'fasilitas_kesehatan';
    protected $primaryKey    = 'id_fasilitas';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_user', 'nama_fasilitas', 'alamat', 'kontak', 'status_verifikasi'];

    public function dariUser(int $idUser): ?array
    {
        return $this->where('id_user', $idUser)->first();
    }

    public function terverifikasi(?string $kata = null): array
    {
        $b = $this->where('status_verifikasi', 'Terverifikasi')->orderBy('nama_fasilitas');
        if ($kata) {
            $b->groupStart()->like('nama_fasilitas', $kata)->orLike('alamat', $kata)->groupEnd();
        }

        return $b->findAll();
    }
}
