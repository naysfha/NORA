<?php

namespace App\Models;

use CodeIgniter\Model;

/** Respons pendonor terhadap permintaan SOS (satu SOS dapat memperoleh banyak respons). */
class ResponsSosModel extends Model
{
    protected $table         = 'respons_sos';
    protected $primaryKey    = 'id_respons';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'tanggal_respons';
    protected $updatedField  = '';
    protected $allowedFields = ['id_sos', 'id_pendonor', 'pesan'];

    /** Daftar respons beserta identitas pendonor. */
    public function dariSos(int $idSos): array
    {
        return $this->select('respons_sos.*, d.golongan_darah, d.lokasi, p.nama, p.no_telp')
            ->join('pendonor d', 'd.id_pendonor = respons_sos.id_pendonor')
            ->join('pengguna p', 'p.id_user = d.id_user')
            ->where('respons_sos.id_sos', $idSos)
            ->orderBy('respons_sos.tanggal_respons')
            ->findAll();
    }
}
