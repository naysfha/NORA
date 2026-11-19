<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Notifier;
use App\Libraries\Referensi;
use App\Models\ResponsSosModel;
use App\Models\SosModel;

/**
 * Admin: menerima, memeriksa, dan memverifikasi permintaan SOS, lalu
 * memperbarui statusnya (diverifikasi -> diproses -> selesai) - DFD proses 6.
 */
class Sos extends BaseController
{
    // Perpindahan status yang diizinkan.
    private const ALUR = [
        'Menunggu Verifikasi' => ['Diverifikasi', 'Ditolak'],
        'Diverifikasi'        => ['Diproses', 'Selesai', 'Ditolak'],
        'Diproses'            => ['Selesai'],
        'Selesai'             => [],
        'Ditolak'             => [],
    ];

    public function index()
    {
        $filter = [
            'status'   => (string) $this->request->getGet('status'),
            'golongan' => (string) $this->request->getGet('golongan'),
            'q'        => trim((string) $this->request->getGet('q')),
        ];

        return view('admin/sos/index', [
            'title'    => 'Permintaan SOS - NORA',
            'sos'      => (new SosModel())->daftar($filter),
            'filter'   => $filter,
            'status'   => Referensi::STATUS_SOS,
            'golongan' => Referensi::GOLONGAN,
        ]);
    }

    public function detail($id)
    {
        $sos = current(array_filter(
            (new SosModel())->daftar([]),
            static fn ($s) => (int) $s['id_sos'] === (int) $id
        ));

        if (! $sos) {
            return redirect()->to(base_url('admin/sos'))->with('error', 'Permintaan SOS tidak ditemukan.');
        }

        return view('admin/sos/detail', [
            'title'   => 'Detail SOS #' . $id . ' - NORA',
            'sos'     => $sos,
            'respons' => (new ResponsSosModel())->dariSos((int) $id),
            'lanjut'  => self::ALUR[$sos['status']] ?? [],
        ]);
    }

    public function status($id)
    {
        $model = new SosModel();
        $sos   = $model->find($id);
        $baru  = $this->request->getPost('status');

        if (! $sos) {
            return redirect()->to(base_url('admin/sos'))->with('error', 'Permintaan SOS tidak ditemukan.');
        }
        if (! in_array($baru, self::ALUR[$sos['status']] ?? [], true)) {
            return redirect()->back()->with('error', "Status tidak dapat diubah dari {$sos['status']} ke {$baru}.");
        }

        $catatan = trim((string) $this->request->getPost('catatan_admin'));
        if ($baru === 'Ditolak' && $catatan === '') {
            return redirect()->back()->with('error', 'Isi catatan alasan penolakan agar pencari darah memahaminya.');
        }
        if (mb_strlen($catatan) > 255) {
            return redirect()->back()->with('error', 'Catatan maksimal 255 karakter.');
        }

        $model->update($id, ['status' => $baru, 'catatan_admin' => $catatan ?: $sos['catatan_admin']]);

        // Notifikasi status ke pencari darah.
        $isi = "Status permintaan SOS #{$id} Anda kini: {$baru}.";
        if ($baru === 'Ditolak' && $catatan) {
            $isi .= ' Catatan admin: ' . $catatan;
        }
        Notifier::kirim((int) $sos['id_pencari'], 'SOS', $isi, 'sos/detail/' . $id);

        // Saat diverifikasi -> beri tahu pendonor dengan golongan yang dibutuhkan.
        if ($baru === 'Diverifikasi') {
            Notifier::kirimBanyak(
                Notifier::idPendonorGolongan($sos['golongan_darah']),
                'Golongan',
                "Dibutuhkan darah golongan {$sos['golongan_darah']} ({$sos['jumlah_dibutuhkan']} kantong) di {$sos['rumah_sakit']} - urgensi {$sos['tingkat_urgensi']}. Anda dapat memberi respons.",
                'sos/detail/' . $id
            );
        }

        return redirect()->to(base_url('admin/sos/detail/' . $id))->with('success', 'Status permintaan SOS diperbarui dan notifikasi terkirim.');
    }

    public function delete($id)
    {
        $model = new SosModel();
        if (! $model->find($id)) {
            return redirect()->to(base_url('admin/sos'))->with('error', 'Permintaan SOS tidak ditemukan.');
        }
        $model->delete($id);

        return redirect()->to(base_url('admin/sos'))->with('success', 'Permintaan SOS berhasil dihapus.');
    }
}
