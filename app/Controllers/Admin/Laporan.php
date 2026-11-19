<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Referensi;
use App\Models\LaporanModel;
use App\Models\StokModel;

/**
 * Admin: permintaan laporan -> sistem merekap data pengguna, stok, jadwal, dan SOS
 * menjadi laporan rekapitulasi (DFD proses 8).
 */
class Laporan extends BaseController
{
    public function index()
    {
        return view('admin/laporan/index', [
            'title'   => 'Laporan - NORA',
            'laporan' => (new LaporanModel())
                ->select('laporan.*, p.nama AS nama_admin')
                ->join('pengguna p', 'p.id_user = laporan.id_admin')
                ->orderBy('id_laporan', 'DESC')->findAll(),
            'jenis'   => Referensi::JENIS_LAPORAN,
        ]);
    }

    public function buat()
    {
        $rules = [
            'jenis_laporan' => ['label' => 'Jenis laporan', 'rules' => 'required|in_list[' . implode(',', array_keys(Referensi::JENIS_LAPORAN)) . ']'],
            'periode'       => ['label' => 'Periode', 'rules' => 'permit_empty|regex_match[/^[0-9]{4}-(0[1-9]|1[0-2])$/]'],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $jenis   = $this->request->getPost('jenis_laporan');
        $periode = trim((string) $this->request->getPost('periode'));

        if ($periode !== '') {
            [$th, $bl] = explode('-', $periode);
            $awal      = "{$periode}-01";
            $akhir     = date('Y-m-t', strtotime($awal));
            $label     = bulan_indo((int) $bl) . ' ' . $th;
        } else {
            $awal  = '2000-01-01';
            $akhir = '2999-12-31';
            $label = 'Semua Periode';
        }

        $isi = $this->rekap($jenis, $awal . ' 00:00:00', $akhir . ' 23:59:59', $awal, $akhir);

        $id = (new LaporanModel())->insert([
            'jenis_laporan' => $jenis,
            'periode'       => $label,
            'isi_laporan'   => json_encode($isi, JSON_UNESCAPED_UNICODE),
            'id_admin'      => $this->idUser(),
        ], true);

        return redirect()->to(base_url('admin/laporan/lihat/' . $id))->with('success', 'Laporan berhasil disusun.');
    }

    public function lihat($id)
    {
        $laporan = (new LaporanModel())
            ->select('laporan.*, p.nama AS nama_admin')
            ->join('pengguna p', 'p.id_user = laporan.id_admin')
            ->find($id);

        if (! $laporan) {
            return redirect()->to(base_url('admin/laporan'))->with('error', 'Laporan tidak ditemukan.');
        }

        return view('admin/laporan/lihat', [
            'title'   => 'Laporan ' . $laporan['jenis_laporan'] . ' - NORA',
            'laporan' => $laporan,
            'isi'     => json_decode((string) $laporan['isi_laporan'], true) ?: ['ringkasan' => [], 'tabel' => []],
        ]);
    }

    public function delete($id)
    {
        $model = new LaporanModel();
        if (! $model->find($id)) {
            return redirect()->to(base_url('admin/laporan'))->with('error', 'Laporan tidak ditemukan.');
        }
        $model->delete($id);

        return redirect()->to(base_url('admin/laporan'))->with('success', 'Laporan berhasil dihapus.');
    }

    /* ------------ penyusunan isi laporan ------------ */

    /**
     * @param string $dt1 batas awal DATETIME, $dt2 batas akhir DATETIME
     * @param string $d1  batas awal DATE,     $d2  batas akhir DATE
     */
    private function rekap(string $jenis, string $dt1, string $dt2, string $d1, string $d2): array
    {
        $db      = db_connect();
        $isi     = ['ringkasan' => [], 'tabel' => []];
        $semua   = $jenis === 'Rekapitulasi Umum';

        if ($semua || $jenis === 'Data Pengguna') {
            $perRole = $db->table('pengguna')->select('role, COUNT(*) AS jml')->groupBy('role')->get()->getResultArray();
            $baru    = $db->table('pengguna')->where('tanggal_daftar >=', $dt1)->where('tanggal_daftar <=', $dt2)->countAllResults();

            $isi['ringkasan']['Total pengguna terdaftar'] = array_sum(array_column($perRole, 'jml'));
            $isi['ringkasan']['Pengguna baru pada periode'] = $baru;
            $isi['ringkasan']['Pendonor terverifikasi'] = $db->table('pendonor')->where('status_verifikasi', 'Terverifikasi')->countAllResults();

            $isi['tabel'][] = [
                'judul'  => 'Pengguna berdasarkan peran',
                'kolom'  => ['Peran', 'Jumlah'],
                'baris'  => array_map(static fn ($r) => [label_role($r['role']), (int) $r['jml']], $perRole),
            ];
            $gol = $db->table('pendonor')->select('golongan_darah, COUNT(*) AS jml')->where('golongan_darah IS NOT NULL', null, false)
                ->groupBy('golongan_darah')->orderBy('golongan_darah')->get()->getResultArray();
            $isi['tabel'][] = [
                'judul' => 'Pendonor berdasarkan golongan darah',
                'kolom' => ['Golongan darah', 'Jumlah pendonor'],
                'baris' => array_map(static fn ($r) => [$r['golongan_darah'], (int) $r['jml']], $gol),
            ];
        }

        if ($semua || $jenis === 'Stok Darah') {
            $total = (new StokModel())->totalPerGolongan();
            $isi['ringkasan']['Total kantong (terverifikasi saat ini)'] = array_sum($total);
            $isi['ringkasan']['Pembaruan stok pada periode'] = $db->table('stok_darah')->where('tanggal_update >=', $dt1)->where('tanggal_update <=', $dt2)->countAllResults();

            $baris = [];
            foreach (Referensi::GOLONGAN as $g) {
                $baris[] = [$g, $total[$g] ?? 0];
            }
            $isi['tabel'][] = ['judul' => 'Total stok per golongan darah', 'kolom' => ['Golongan darah', 'Jumlah kantong'], 'baris' => $baris];

            $perFas = (new StokModel())->cari();
            $isi['tabel'][] = [
                'judul' => 'Stok terbaru per fasilitas',
                'kolom' => ['Fasilitas', 'Golongan', 'Kantong', 'Diperbarui'],
                'baris' => array_map(static fn ($r) => [$r['nama_fasilitas'], $r['golongan_darah'], (int) $r['jumlah_kantong'], tgl_waktu($r['tanggal_update'])], $perFas),
            ];
        }

        if ($semua || $jenis === 'Jadwal Donor') {
            $keg = $db->table('jadwal_donor')->where('tanggal >=', $d1)->where('tanggal <=', $d2)->orderBy('tanggal')->get()->getResultArray();
            $isi['ringkasan']['Kegiatan donor pada periode'] = count($keg);
            $isi['ringkasan']['Kegiatan dipublikasikan'] = count(array_filter($keg, static fn ($k) => $k['status'] === 'Dipublikasikan'));

            $isi['tabel'][] = [
                'judul' => 'Daftar kegiatan donor',
                'kolom' => ['Tanggal', 'Kegiatan', 'Lokasi', 'Penyelenggara', 'Status'],
                'baris' => array_map(static fn ($k) => [tgl_indo($k['tanggal']), $k['nama_kegiatan'], $k['lokasi'], $k['penyelenggara'], $k['status']], $keg),
            ];
        }

        if ($semua || $jenis === 'Permintaan SOS') {
            $sos = $db->table('permintaan_sos')->where('tanggal_pengajuan >=', $dt1)->where('tanggal_pengajuan <=', $dt2)
                ->orderBy('tanggal_pengajuan')->get()->getResultArray();

            $isi['ringkasan']['Permintaan SOS pada periode'] = count($sos);
            $isi['ringkasan']['SOS selesai'] = count(array_filter($sos, static fn ($s) => $s['status'] === 'Selesai'));
            $isi['ringkasan']['Total kantong diminta'] = array_sum(array_column($sos, 'jumlah_dibutuhkan'));

            $perStatus = array_fill_keys(Referensi::STATUS_SOS, 0);
            $perGol    = [];
            foreach ($sos as $s) {
                $perStatus[$s['status']]++;
                $perGol[$s['golongan_darah']] = ($perGol[$s['golongan_darah']] ?? 0) + 1;
            }
            ksort($perGol);

            $isi['tabel'][] = ['judul' => 'SOS berdasarkan status', 'kolom' => ['Status', 'Jumlah'], 'baris' => array_map(null, array_keys($perStatus), array_values($perStatus))];
            $isi['tabel'][] = ['judul' => 'SOS berdasarkan golongan darah', 'kolom' => ['Golongan darah', 'Jumlah'], 'baris' => array_map(null, array_keys($perGol), array_values($perGol))];
            $isi['tabel'][] = [
                'judul' => 'Rincian permintaan SOS',
                'kolom' => ['No', 'Tanggal', 'Golongan', 'Kantong', 'Rumah sakit', 'Urgensi', 'Status'],
                'baris' => array_map(static fn ($s) => ['#' . $s['id_sos'], tgl_indo($s['tanggal_pengajuan']), $s['golongan_darah'], (int) $s['jumlah_dibutuhkan'], $s['rumah_sakit'], $s['tingkat_urgensi'], $s['status']], $sos),
            ];
        }

        return $isi;
    }
}
