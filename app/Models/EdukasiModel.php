<?php

namespace App\Models;

use CodeIgniter\Model;

/** Konten edukasi donor darah (dikelola admin). */
class EdukasiModel extends Model
{
    protected $table         = 'edukasi';
    protected $primaryKey    = 'id_edukasi';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'tanggal_dibuat';
    protected $updatedField  = '';
    protected $allowedFields = ['judul', 'kategori', 'ringkasan', 'isi', 'id_admin'];
}
