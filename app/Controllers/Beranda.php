<?php

namespace App\Controllers;

use App\Models\FasilitasModel;
use App\Models\JadwalModel;
use App\Models\PendonorModel;
use App\Models\SosModel;
use App\Models\StokModel;

/** Halaman depan publik NORA. */
class Beranda extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }

        $stok = (new StokModel())->totalPerGolongan();

        return view('beranda/index', [
            'title'      => 'NORA - Sistem Informasi Donor Darah Terintegrasi PMI Kota Palu',
            'pendonor'   => (new PendonorModel())->where('status_verifikasi', 'Terverifikasi')->countAllResults(),
            'fasilitas'  => (new FasilitasModel())->where('status_verifikasi', 'Terverifikasi')->countAllResults(),
            'kegiatan'   => count((new JadwalModel())->mendatang()),
            'sosSelesai' => (new SosModel())->where('status', 'Selesai')->countAllResults(),
            'stok'       => $stok,
            'totalStok'  => array_sum($stok),
        ]);
    }
}
