<?php

namespace App\Controllers;

use App\Libraries\Notifier;
use App\Libraries\Referensi;
use App\Models\ResponsSosModel;
use App\Models\SosModel;

/**
 * Modul Permintaan SOS (DFD proses 6).
 *  Pencari darah : buat (validasi kelengkapan) -> status "Menunggu Verifikasi" -> pantau status
 *  Pendonor      : melihat permintaan terverifikasi dan memberi respons
 */
class Sos extends BaseController
{
    /* =================== PENCARI DARAH =================== */

    public function saya()
    {
        return view('sos/saya', [
            'title' => 'Permintaan SOS Saya - NORA',
            'sos'   => (new SosModel())->daftar(['id_pencari' => $this->idUser()]),
        ]);
    }

    public function buat()
    {
        return view('sos/buat', [
            'title'    => 'Buat Permintaan SOS - NORA',
            'golongan' => Referensi::GOLONGAN,
            'urgensi'  => Referensi::URGENSI,
        ]);
    }

    public function simpan()
    {
        $kontak = normalisasi_telp($this->request->getPost('kontak'));

        $rules = [
            'golongan_darah'    => ['label' => 'Golongan darah', 'rules' => 'required|in_list[' . implode(',', Referensi::GOLONGAN) . ']'],
            'jumlah_dibutuhkan' => ['label' => 'Jumlah kantong', 'rules' => 'required|is_natural_no_zero|less_than_equal_to[50]'],
            'lokasi'            => ['label' => 'Lokasi', 'rules' => 'required|max_length[255]'],
            'rumah_sakit'       => ['label' => 'Rumah sakit', 'rules' => 'required|max_length[150]'],
            'kontak'            => ['label' => 'Nomor kontak', 'rules' => 'required|regex_match[/^0[0-9]{8,14}$/]'],
            'tingkat_urgensi'   => ['label' => 'Tingkat urgensi', 'rules' => 'required|in_list[' . implode(',', Referensi::URGENSI) . ']'],
        ];

        $data           = $this->request->getPost();
        $data['kontak'] = $kontak ?? '';

        // Pemeriksaan kelengkapan (proses 6.2): bila belum lengkap, permintaan ditolak sementara.
        if (! $this->validateData($data, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $id = (new SosModel())->insert([
            'id_pencari'        => $this->idUser(),
            'golongan_darah'    => $data['golongan_darah'],
            'jumlah_dibutuhkan' => (int) $data['jumlah_dibutuhkan'],
            'lokasi'            => trim($data['lokasi']),
            'rumah_sakit'       => trim($data['rumah_sakit']),
            'kontak'            => $kontak,
            'tingkat_urgensi'   => $data['tingkat_urgensi'],
            'status'            => 'Menunggu Verifikasi',
        ], true);

        Notifier::kirimBanyak(
            Notifier::idPenggunaRole('admin'),
            'SOS',
            "Permintaan SOS #{$id} golongan {$data['golongan_darah']} ({$data['tingkat_urgensi']}) di " . trim($data['rumah_sakit']) . ' menunggu verifikasi.',
            'admin/sos/detail/' . $id
        );
        Notifier::kirim(
            $this->idUser(),
            'SOS',
            "Permintaan SOS #{$id} Anda telah diterima dan menunggu verifikasi admin.",
            'sos/detail/' . $id
        );

        return redirect()->to(base_url('sos/detail/' . $id))
            ->with('success', 'Permintaan SOS tersimpan dengan status Menunggu Verifikasi.');
    }

    /* =================== PENDONOR =================== */

    public function index()
    {
        $profil = $this->pendonorSaya();
        $saja   = $this->request->getGet('golongan') === 'saya';

        $filter = ['status_in' => ['Diverifikasi', 'Diproses']];
        if ($saja && ! empty($profil['golongan_darah'])) {
            $filter['golongan'] = $profil['golongan_darah'];
        }

        $sudah = [];
        if ($profil) {
            foreach ((new ResponsSosModel())->where('id_pendonor', $profil['id_pendonor'])->findAll() as $r) {
                $sudah[$r['id_sos']] = true;
            }
        }

        return view('sos/index', [
            'title'  => 'Permintaan Darah - NORA',
            'sos'    => (new SosModel())->daftar($filter),
            'profil' => $profil,
            'sudah'  => $sudah,
            'saja'   => $saja,
        ]);
    }

    public function respons($id)
    {
        $profil = $this->pendonorSaya();
        $sos    = (new SosModel())->find($id);

        if (! $sos || ! in_array($sos['status'], ['Diverifikasi', 'Diproses'], true)) {
            return redirect()->to(base_url('sos'))->with('error', 'Permintaan tidak ditemukan atau belum dapat direspons.');
        }
        if (empty($profil['golongan_darah']) || $profil['status_verifikasi'] !== 'Terverifikasi') {
            return redirect()->back()->with('error', 'Lengkapi profil pendonor dan tunggu verifikasi admin sebelum memberi respons.');
        }

        $pesan = trim((string) $this->request->getPost('pesan'));
        if (mb_strlen($pesan) > 255) {
            return redirect()->back()->with('error', 'Pesan maksimal 255 karakter.');
        }

        $model = new ResponsSosModel();
        if ($model->where('id_sos', $id)->where('id_pendonor', $profil['id_pendonor'])->first()) {
            return redirect()->back()->with('error', 'Anda sudah memberi respons pada permintaan ini.');
        }

        $model->insert(['id_sos' => $id, 'id_pendonor' => $profil['id_pendonor'], 'pesan' => $pesan ?: null]);

        Notifier::kirim(
            (int) $sos['id_pencari'],
            'SOS',
            session()->get('nama') . ' (golongan ' . $profil['golongan_darah'] . ') bersedia merespons permintaan SOS #' . $id . '.',
            'sos/detail/' . $id
        );

        return redirect()->to(base_url('sos/detail/' . $id))->with('success', 'Respons Anda terkirim. Terima kasih telah bersedia membantu.');
    }

    public function batalRespons($id)
    {
        $profil = $this->pendonorSaya();
        (new ResponsSosModel())->where('id_sos', $id)->where('id_pendonor', $profil['id_pendonor'])->delete();

        return redirect()->back()->with('success', 'Respons Anda dibatalkan.');
    }

    /* =================== DETAIL (pencari pemilik & pendonor) =================== */

    public function detail($id)
    {
        $sos = (new SosModel())->daftar([]);
        $sos = current(array_filter($sos, static fn ($s) => (int) $s['id_sos'] === (int) $id));

        if (! $sos) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Permintaan SOS tidak ditemukan.');
        }

        $respons   = new ResponsSosModel();
        $pemilik   = $this->role() === 'pencari';
        $profil    = $this->role() === 'pendonor' ? $this->pendonorSaya() : null;
        $responsku = null;

        if ($pemilik && (int) $sos['id_pencari'] !== $this->idUser()) {
            return redirect()->to(base_url('sos/saya'))->with('error', 'Anda tidak berhak melihat permintaan tersebut.');
        }
        if (! $pemilik) {
            // Pendonor hanya boleh melihat permintaan yang sudah diverifikasi.
            if (! in_array($sos['status'], ['Diverifikasi', 'Diproses'], true)) {
                return redirect()->to(base_url('sos'))->with('error', 'Permintaan ini belum/tidak lagi dapat dilihat.');
            }
            $responsku = $profil
                ? $respons->where('id_sos', $id)->where('id_pendonor', $profil['id_pendonor'])->first()
                : null;
        }

        return view('sos/detail', [
            'title'     => 'Detail Permintaan SOS #' . $id . ' - NORA',
            'sos'       => $sos,
            'pemilik'   => $pemilik,
            'profil'    => $profil,
            'responsku' => $responsku,
            'daftarRespons' => $pemilik ? $respons->dariSos((int) $id) : [],
        ]);
    }
}
