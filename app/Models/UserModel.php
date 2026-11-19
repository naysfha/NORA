<?php

namespace App\Models;

use CodeIgniter\Model;

/** Entitas Pengguna (D1 - Data Pengguna). */
class UserModel extends Model
{
    protected $table         = 'pengguna';
    protected $primaryKey    = 'id_user';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'tanggal_daftar';
    protected $updatedField  = '';
    protected $allowedFields = ['nama', 'email', 'no_telp', 'password', 'role'];

    /** Cari pengguna berdasarkan email ATAU nomor telepon. */
    public function cariIdentitas(string $identitas): ?array
    {
        return $this->groupStart()
            ->where('email', $identitas)
            ->orWhere('no_telp', normalisasi_telp($identitas))
            ->groupEnd()
            ->first();
    }
}
