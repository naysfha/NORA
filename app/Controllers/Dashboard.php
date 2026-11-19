<?php

namespace App\Controllers;

use App\Libraries\Referensi;
use App\Models\EdukasiModel;
use App\Models\FasilitasModel;
use App\Models\JadwalModel;
use App\Models\NotifikasiModel;
use App\Models\PendonorModel;
use App\Models\SosModel;
use App\Models\StokModel;
use App\Models\UserModel;

/** Beranda setelah login. Isinya menyesuaikan hak akses (peran) pengguna. */
class Dashboard extends BaseController
{
    public function index()
    {
        return match ($this->role()) {
            'admin'     => $this->admin(),
            'pendonor'  => $this->pendonor(),
            'pencari'   => $this->pencari(),
            'fasilitas' => $this->fasilitas(),
            default     => redirect()->to(base_url('logout')),
        };
    }

    private function admin()
    {
        $sos       = new SosModel();
        $stokModel = new StokModel();

        return view('dashboard/admin', [
            'title'    => 'Dashboard Admin - NORA',
            'total'    => [
                'pengguna'  => (new UserModel())->countAllResults(),
                'pendonor'  => (new PendonorModel())->countAllResults(),
                'fasilitas' => (new FasilitasModel())->countAllResults(),
                'kantong'   => array_sum($stokModel->totalPerGolongan()),
            ],
            // Antrean pekerjaan admin (hal-hal yang menunggu verifikasi)
            'antrean'  => [
                'sos'       => $sos->where('status', 'Menunggu Verifikasi')->countAllResults(),
                'stok'      => $stokModel->where('status_verifikasi', 'Menunggu')->countAllResults(),
                'jadwal'    => (new JadwalModel())->where('status', 'Menunggu')->countAllResults(),
                'fasilitas' => (new FasilitasModel())->where('status_verifikasi', 'Menunggu')->countAllResults(),
                'pendonor'  => (new PendonorModel())->where('status_verifikasi', 'Menunggu')->countAllResults(),
            ],
            'stokGol'  => $stokModel->totalPerGolongan(),
            'sosBaru'  => (new SosModel())->daftar(['status_in' => ['Menunggu Verifikasi', 'Diverifikasi', 'Diproses']]),
            'jadwal'   => (new JadwalModel())->mendatang(4),
        ]);
    }

    private function pendonor()
    {
        $profil = $this->pendonorSaya();
        $sos    = new SosModel();

        $cocok = [];
        if (! empty($profil['golongan_darah'])) {
            $cocok = $sos->daftar([
                'status_in' => ['Diverifikasi', 'Diproses'],
                'golongan'  => $profil['golongan_darah'],
            ]);
        }

        return view('dashboard/pendonor', [
            'title'    => 'Dashboard Pendonor - NORA',
            'profil'   => $profil,
            'lengkap'  => ! empty($profil['golongan_darah']) && ! empty($profil['lokasi']),
            'cocok'    => array_slice($cocok, 0, 3),
            'jadwal'   => (new JadwalModel())->mendatang(3),
            'stokGol'  => (new StokModel())->totalPerGolongan(),
            'edukasi'  => (new EdukasiModel())->orderBy('id_edukasi')->findAll(3),
        ]);
    }

    private function pencari()
    {
        $sos   = new SosModel();
        $milik = $sos->daftar(['id_pencari' => $this->idUser()]);

        $hitung = array_fill_keys(Referensi::STATUS_SOS, 0);
        foreach ($milik as $s) {
            $hitung[$s['status']]++;
        }

        return view('dashboard/pencari', [
            'title'   => 'Dashboard Pencari Darah - NORA',
            'sosSaya' => array_slice($milik, 0, 4),
            'hitung'  => $hitung,
            'totalSos' => count($milik),
            'stokGol' => (new StokModel())->totalPerGolongan(),
            'jadwal'  => (new JadwalModel())->mendatang(3),
        ]);
    }

    private function fasilitas()
    {
        $fasilitas = $this->fasilitasSaya();
        $stok      = $fasilitas ? (new StokModel())->terbaruFasilitas((int) $fasilitas['id_fasilitas']) : [];
        $jadwal    = $fasilitas
            ? (new JadwalModel())->where('id_fasilitas', $fasilitas['id_fasilitas'])->orderBy('tanggal', 'DESC')->findAll(4)
            : [];

        return view('dashboard/fasilitas', [
            'title'     => 'Dashboard Fasilitas - NORA',
            'fasilitas' => $fasilitas,
            'stok'      => $stok,
            'jadwal'    => $jadwal,
            'menunggu'  => count(array_filter($stok, static fn ($s) => $s['status_verifikasi'] === 'Menunggu')),
        ]);
    }
}
