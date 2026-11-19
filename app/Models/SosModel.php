<?php

namespace App\Models;

use CodeIgniter\Model;

/** Entitas Permintaan SOS (D5 - Data Permintaan SOS). */
class SosModel extends Model
{
    protected $table         = 'permintaan_sos';
    protected $primaryKey    = 'id_sos';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'tanggal_pengajuan';
    protected $updatedField  = '';
    protected $allowedFields = [
        'id_pencari', 'golongan_darah', 'jumlah_dibutuhkan', 'lokasi', 'rumah_sakit',
        'kontak', 'tingkat_urgensi', 'status', 'catatan_admin',
    ];

    /** Daftar SOS + data pencari + jumlah respons pendonor. */
    public function daftar(array $filter = []): array
    {
        $b = $this->select('permintaan_sos.*, p.nama AS nama_pencari, p.no_telp AS telp_pencari,
                (SELECT COUNT(*) FROM respons_sos r WHERE r.id_sos = permintaan_sos.id_sos) AS jumlah_respons', false)
            ->join('pengguna p', 'p.id_user = permintaan_sos.id_pencari')
            ->orderBy('permintaan_sos.tanggal_pengajuan', 'DESC');

        if (! empty($filter['status'])) {
            $b->where('permintaan_sos.status', $filter['status']);
        }
        if (! empty($filter['golongan'])) {
            $b->where('permintaan_sos.golongan_darah', $filter['golongan']);
        }
        if (! empty($filter['id_pencari'])) {
            $b->where('permintaan_sos.id_pencari', $filter['id_pencari']);
        }
        if (! empty($filter['status_in'])) {
            $b->whereIn('permintaan_sos.status', $filter['status_in']);
        }
        if (! empty($filter['q'])) {
            $b->groupStart()
                ->like('permintaan_sos.rumah_sakit', $filter['q'])
                ->orLike('permintaan_sos.lokasi', $filter['q'])
                ->orLike('p.nama', $filter['q'])
                ->groupEnd();
        }

        return $b->findAll();
    }
}
