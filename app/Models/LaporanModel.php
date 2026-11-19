<?php

namespace App\Models;

use CodeIgniter\Model;

/** Entitas Laporan (D7 - Data Laporan). */
class LaporanModel extends Model
{
    protected $table         = 'laporan';
    protected $primaryKey    = 'id_laporan';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'tanggal_dibuat';
    protected $updatedField  = '';
    protected $allowedFields = ['jenis_laporan', 'periode', 'isi_laporan', 'id_admin'];
}
