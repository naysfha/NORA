<?php

namespace App\Models;

use CodeIgniter\Model;

/** Entitas Jadwal Donor / Kegiatan Donor (D4 - Data Jadwal & Lokasi). */
class JadwalModel extends Model
{
    protected $table         = 'jadwal_donor';
    protected $primaryKey    = 'id_jadwal';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_fasilitas', 'nama_kegiatan', 'tanggal', 'waktu', 'lokasi', 'penyelenggara', 'status'];

    /** Kegiatan yang sudah dipublikasikan dan belum lewat. */
    public function mendatang(?int $limit = null, ?string $kata = null): array
    {
        $b = $this->where('status', 'Dipublikasikan')
            ->where('tanggal >=', date('Y-m-d'))
            ->orderBy('tanggal')->orderBy('waktu');
        if ($kata) {
            $b->groupStart()->like('nama_kegiatan', $kata)->orLike('lokasi', $kata)->orLike('penyelenggara', $kata)->groupEnd();
        }
        if ($limit) {
            $b->limit($limit);
        }

        return $b->findAll();
    }

    public function riwayat(int $limit = 10): array
    {
        return $this->where('status', 'Dipublikasikan')
            ->where('tanggal <', date('Y-m-d'))
            ->orderBy('tanggal', 'DESC')->limit($limit)->findAll();
    }
}
