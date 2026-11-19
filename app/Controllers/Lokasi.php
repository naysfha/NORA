<?php

namespace App\Controllers;

use App\Models\FasilitasModel;
use App\Models\StokModel;

/** Daftar lokasi layanan donor: PMI, fasilitas kesehatan, dan unit donor (DFD proses 4). */
class Lokasi extends BaseController
{
    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));

        $stokPerFasilitas = [];
        foreach ((new StokModel())->cari() as $r) {
            $stokPerFasilitas[$r['id_fasilitas']] = ($stokPerFasilitas[$r['id_fasilitas']] ?? 0) + (int) $r['jumlah_kantong'];
        }

        return view('lokasi/index', [
            'title'     => 'Lokasi Layanan - NORA',
            'lokasi'    => (new FasilitasModel())->terverifikasi($q ?: null),
            'totalStok' => $stokPerFasilitas,
            'q'         => $q,
        ]);
    }
}
