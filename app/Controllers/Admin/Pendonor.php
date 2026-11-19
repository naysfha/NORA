<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Notifier;
use App\Libraries\Referensi;
use App\Models\PendonorModel;

/** Admin: verifikasi profil pendonor sebelum ditampilkan sebagai profil aktif (DFD 2). */
class Pendonor extends BaseController
{
    public function index()
    {
        $filter = [
            'q'        => trim((string) $this->request->getGet('q')),
            'golongan' => (string) $this->request->getGet('golongan'),
            'status'   => (string) $this->request->getGet('status'),
        ];

        return view('admin/pendonor/index', [
            'title'    => 'Data Pendonor - NORA',
            'pendonor' => (new PendonorModel())->daftar($filter),
            'filter'   => $filter,
            'golongan' => Referensi::GOLONGAN,
        ]);
    }

    public function verifikasi($id)
    {
        $model  = new PendonorModel();
        $profil = $model->find($id);
        $status = $this->request->getPost('status');

        if (! $profil || ! in_array($status, ['Terverifikasi', 'Menunggu'], true)) {
            return redirect()->to(base_url('admin/pendonor'))->with('error', 'Data pendonor tidak ditemukan.');
        }
        if ($status === 'Terverifikasi' && empty($profil['golongan_darah'])) {
            return redirect()->to(base_url('admin/pendonor'))->with('error', 'Profil belum dilengkapi pendonor (golongan darah kosong).');
        }

        $model->update($id, ['status_verifikasi' => $status]);

        if ($status === 'Terverifikasi') {
            Notifier::kirim((int) $profil['id_user'], 'Layanan', 'Profil pendonor Anda telah diverifikasi dan kini aktif. Terima kasih telah bergabung!', 'profil');
        }

        return redirect()->to(base_url('admin/pendonor'))->with('success', 'Status profil pendonor diperbarui.');
    }
}
