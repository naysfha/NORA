<?php

namespace App\Controllers;

use App\Libraries\Notifier;
use App\Libraries\Referensi;
use App\Models\StokModel;

/**
 * Modul Stok Darah (DFD proses 3).
 *  - index  : semua pengguna mencari stok berdasarkan golongan darah + lokasi
 *  - kelola : fasilitas mengirim data stok (menunggu verifikasi admin)
 */
class Stok extends BaseController
{
    public function index()
    {
        $golongan = $this->request->getGet('golongan');
        $lokasi   = trim((string) $this->request->getGet('lokasi'));

        if (! in_array($golongan, Referensi::GOLONGAN, true)) {
            $golongan = '';
        }

        $model = new StokModel();

        // Kelompokkan hasil per fasilitas agar mudah dibaca.
        $perFasilitas = [];
        foreach ($model->cari($golongan ?: null, $lokasi ?: null) as $r) {
            $perFasilitas[$r['id_fasilitas']]['info']    = $r;
            $perFasilitas[$r['id_fasilitas']]['stok'][]  = $r;
        }

        return view('stok/index', [
            'title'        => 'Cari Stok Darah - NORA',
            'golonganList' => Referensi::GOLONGAN,
            'filter'       => ['golongan' => $golongan, 'lokasi' => $lokasi],
            'perFasilitas' => $perFasilitas,
            'totalGol'     => $model->totalPerGolongan(),
        ]);
    }

    /** Form pembaruan stok oleh fasilitas. */
    public function kelola()
    {
        $fasilitas = $this->fasilitasSaya();

        return view('stok/kelola', [
            'title'        => 'Kelola Stok Darah - NORA',
            'fasilitas'    => $fasilitas,
            'golonganList' => Referensi::GOLONGAN,
            'terbaru'      => $fasilitas ? (new StokModel())->terbaruFasilitas((int) $fasilitas['id_fasilitas']) : [],
            'riwayat'      => $fasilitas
                ? (new StokModel())->where('id_fasilitas', $fasilitas['id_fasilitas'])->orderBy('id_stok', 'DESC')->findAll(12)
                : [],
        ]);
    }

    public function simpan()
    {
        $fasilitas = $this->fasilitasSaya();

        if (! $fasilitas || $fasilitas['status_verifikasi'] !== 'Terverifikasi') {
            return redirect()->to(base_url('stok/kelola'))
                ->with('error', 'Fasilitas Anda belum diverifikasi admin, sehingga belum dapat mengirim data stok.');
        }

        $model   = new StokModel();
        $terbaru = $model->terbaruFasilitas((int) $fasilitas['id_fasilitas']);
        $input   = (array) $this->request->getPost('jumlah');
        $errors  = [];
        $baris   = [];

        foreach (Referensi::GOLONGAN as $gol) {
            $nilai = trim((string) ($input[$gol] ?? ''));
            if ($nilai === '') {
                continue; // dikosongkan = tidak diperbarui
            }
            if (! ctype_digit($nilai) || (int) $nilai > 10000) {
                $errors[$gol] = "Jumlah kantong golongan {$gol} harus berupa angka 0 - 10000.";
                continue;
            }
            // Hanya simpan bila berbeda dari catatan terakhir.
            if (isset($terbaru[$gol]) && (int) $terbaru[$gol]['jumlah_kantong'] === (int) $nilai) {
                continue;
            }
            $baris[$gol] = (int) $nilai;
        }

        if ($errors) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }
        if (! $baris) {
            return redirect()->back()->with('error', 'Tidak ada perubahan jumlah stok yang dikirim.');
        }

        foreach ($baris as $gol => $jumlah) {
            $model->insert([
                'id_fasilitas'      => $fasilitas['id_fasilitas'],
                'golongan_darah'    => $gol,
                'jumlah_kantong'    => $jumlah,
                'sumber'            => 'Fasilitas',
                'status_verifikasi' => 'Menunggu',
            ]);
        }

        Notifier::kirimBanyak(
            Notifier::idPenggunaRole('admin'),
            'Stok',
            $fasilitas['nama_fasilitas'] . ' mengirim pembaruan stok (' . count($baris) . ' golongan) yang menunggu verifikasi.',
            'admin/stok?status=Menunggu'
        );

        return redirect()->to(base_url('stok/kelola'))
            ->with('success', 'Data stok terkirim dan menunggu verifikasi admin sebelum ditampilkan.');
    }
}
