<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Notifier;
use App\Libraries\Referensi;

/** Admin: mengirim notifikasi manual (mis. kebutuhan golongan darah tertentu) - DFD proses 7. */
class Notifikasi extends BaseController
{
    private const TARGET = [
        'semua'     => 'Semua pengguna',
        'pendonor'  => 'Semua pendonor',
        'pencari'   => 'Semua pencari darah',
        'fasilitas' => 'Semua fasilitas kesehatan',
        'golongan'  => 'Pendonor golongan darah tertentu',
    ];

    public function index()
    {
        $terkirim = db_connect()->table('notifikasi n')
            ->select('n.*, p.nama, p.role')
            ->join('pengguna p', 'p.id_user = n.id_user')
            ->orderBy('n.id_notifikasi', 'DESC')
            ->limit(40)->get()->getResultArray();

        return view('admin/notifikasi/index', [
            'title'    => 'Notifikasi - NORA',
            'target'   => self::TARGET,
            'golongan' => Referensi::GOLONGAN,
            'jenis'    => Referensi::JENIS_NOTIFIKASI,
            'terkirim' => $terkirim,
        ]);
    }

    public function kirim()
    {
        $rules = [
            'target' => ['label' => 'Penerima', 'rules' => 'required|in_list[' . implode(',', array_keys(self::TARGET)) . ']'],
            'jenis'  => ['label' => 'Jenis notifikasi', 'rules' => 'required|in_list[' . implode(',', array_keys(Referensi::JENIS_NOTIFIKASI)) . ']'],
            'isi'    => ['label' => 'Isi pesan', 'rules' => 'required|min_length[5]|max_length[500]'],
        ];

        $target = (string) $this->request->getPost('target');
        if ($target === 'golongan') {
            $rules['golongan'] = ['label' => 'Golongan darah', 'rules' => 'required|in_list[' . implode(',', Referensi::GOLONGAN) . ']'];
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $penerima = match ($target) {
            'pendonor'  => Notifier::idPenggunaRole('pendonor'),
            'pencari'   => Notifier::idPenggunaRole('pencari'),
            'fasilitas' => Notifier::idPenggunaRole('fasilitas'),
            'golongan'  => Notifier::idPendonorGolongan((string) $this->request->getPost('golongan')),
            default     => array_merge(
                Notifier::idPenggunaRole('pendonor'),
                Notifier::idPenggunaRole('pencari'),
                Notifier::idPenggunaRole('fasilitas')
            ),
        };

        if (! $penerima) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada penerima yang sesuai dengan target tersebut.');
        }

        $jumlah = Notifier::kirimBanyak($penerima, (string) $this->request->getPost('jenis'), trim($this->request->getPost('isi')));

        return redirect()->to(base_url('admin/notifikasi'))->with('success', "Notifikasi terkirim ke {$jumlah} pengguna.");
    }
}
