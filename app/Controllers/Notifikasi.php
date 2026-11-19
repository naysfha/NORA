<?php

namespace App\Controllers;

use App\Models\NotifikasiModel;

/** Modul Notifikasi (DFD proses 7): kotak masuk notifikasi milik pengguna. */
class Notifikasi extends BaseController
{
    public function index()
    {
        $model = new NotifikasiModel();

        return view('notifikasi/index', [
            'title'      => 'Notifikasi - NORA',
            'notifikasi' => $model->where('id_user', $this->idUser())->orderBy('id_notifikasi', 'DESC')->findAll(100),
            'belum'      => $model->jumlahBelumDibaca($this->idUser()),
        ]);
    }

    /** Tandai dibaca lalu arahkan ke halaman terkait. */
    public function buka($id)
    {
        $model = new NotifikasiModel();
        $n     = $model->where('id_user', $this->idUser())->find($id);

        if (! $n) {
            return redirect()->to(base_url('notifikasi'))->with('error', 'Notifikasi tidak ditemukan.');
        }

        $model->update($id, ['status_baca' => 1]);

        return redirect()->to(base_url($n['tautan'] ?: 'notifikasi'));
    }

    public function bacaSemua()
    {
        (new NotifikasiModel())->where('id_user', $this->idUser())->set('status_baca', 1)->update();

        return redirect()->to(base_url('notifikasi'))->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
