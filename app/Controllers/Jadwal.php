<?php

namespace App\Controllers;

use App\Libraries\Notifier;
use App\Models\JadwalModel;

/**
 * Modul Jadwal & Lokasi Kegiatan Donor (DFD proses 4).
 *  - index  : semua pengguna melihat kegiatan yang sudah dipublikasikan
 *  - saya / ajukan / simpan : fasilitas mengajukan kegiatan (perlu persetujuan publikasi admin)
 */
class Jadwal extends BaseController
{
    public function index()
    {
        $q     = trim((string) $this->request->getGet('q'));
        $model = new JadwalModel();

        return view('jadwal/index', [
            'title'    => 'Jadwal Donor Darah - NORA',
            'jadwal'   => $model->mendatang(null, $q ?: null),
            'riwayat'  => $model->riwayat(6),
            'q'        => $q,
        ]);
    }

    public function saya()
    {
        $fasilitas = $this->fasilitasSaya();

        return view('jadwal/saya', [
            'title'     => 'Kegiatan Donor Saya - NORA',
            'fasilitas' => $fasilitas,
            'jadwal'    => $fasilitas
                ? (new JadwalModel())->where('id_fasilitas', $fasilitas['id_fasilitas'])->orderBy('tanggal', 'DESC')->findAll()
                : [],
        ]);
    }

    public function ajukan()
    {
        $fasilitas = $this->fasilitasSaya();

        if (! $fasilitas || $fasilitas['status_verifikasi'] !== 'Terverifikasi') {
            return redirect()->to(base_url('jadwal/saya'))
                ->with('error', 'Fasilitas Anda belum diverifikasi admin, sehingga belum dapat mengajukan kegiatan.');
        }

        return view('jadwal/ajukan', ['title' => 'Ajukan Kegiatan Donor - NORA', 'fasilitas' => $fasilitas]);
    }

    public function simpan()
    {
        $fasilitas = $this->fasilitasSaya();

        if (! $fasilitas || $fasilitas['status_verifikasi'] !== 'Terverifikasi') {
            return redirect()->to(base_url('jadwal/saya'))
                ->with('error', 'Fasilitas Anda belum diverifikasi admin.');
        }

        $rules = [
            'nama_kegiatan' => ['label' => 'Nama kegiatan', 'rules' => 'required|max_length[150]'],
            'tanggal'       => ['label' => 'Tanggal', 'rules' => 'required|valid_date[Y-m-d]'],
            'jam_mulai'     => ['label' => 'Jam mulai', 'rules' => 'required|regex_match[/^([01][0-9]|2[0-3]):[0-5][0-9]$/]'],
            'jam_selesai'   => ['label' => 'Jam selesai', 'rules' => 'required|regex_match[/^([01][0-9]|2[0-3]):[0-5][0-9]$/]'],
            'lokasi'        => ['label' => 'Lokasi', 'rules' => 'required|max_length[255]'],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tanggal = $this->request->getPost('tanggal');
        $mulai   = $this->request->getPost('jam_mulai');
        $selesai = $this->request->getPost('jam_selesai');

        if ($tanggal < date('Y-m-d')) {
            return redirect()->back()->withInput()->with('errors', ['tanggal' => 'Tanggal kegiatan tidak boleh sebelum hari ini.']);
        }
        if ($selesai <= $mulai) {
            return redirect()->back()->withInput()->with('errors', ['jam_selesai' => 'Jam selesai harus setelah jam mulai.']);
        }

        (new JadwalModel())->insert([
            'id_fasilitas'  => $fasilitas['id_fasilitas'],
            'nama_kegiatan' => trim($this->request->getPost('nama_kegiatan')),
            'tanggal'       => $tanggal,
            'waktu'         => $mulai . ' - ' . $selesai . ' WITA',
            'lokasi'        => trim($this->request->getPost('lokasi')),
            'penyelenggara' => $fasilitas['nama_fasilitas'],
            'status'        => 'Menunggu',
        ]);

        Notifier::kirimBanyak(
            Notifier::idPenggunaRole('admin'),
            'Jadwal',
            $fasilitas['nama_fasilitas'] . ' mengajukan kegiatan donor "' . trim($this->request->getPost('nama_kegiatan')) . '" yang menunggu persetujuan publikasi.',
            'admin/jadwal?status=Menunggu'
        );

        return redirect()->to(base_url('jadwal/saya'))
            ->with('success', 'Kegiatan diajukan dan menunggu persetujuan publikasi admin.');
    }
}
