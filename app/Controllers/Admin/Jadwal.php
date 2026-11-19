<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Notifier;
use App\Models\FasilitasModel;
use App\Models\JadwalModel;

/** Admin: kelola kegiatan donor + persetujuan publikasi (DFD proses 4). */
class Jadwal extends BaseController
{
    protected JadwalModel $model;

    public function __construct()
    {
        $this->model = new JadwalModel();
    }

    private function aturan(): array
    {
        $jam = 'required|regex_match[/^([01][0-9]|2[0-3]):[0-5][0-9]$/]';

        return [
            'nama_kegiatan' => ['label' => 'Nama kegiatan', 'rules' => 'required|max_length[150]'],
            'tanggal'       => ['label' => 'Tanggal', 'rules' => 'required|valid_date[Y-m-d]'],
            'jam_mulai'     => ['label' => 'Jam mulai', 'rules' => $jam],
            'jam_selesai'   => ['label' => 'Jam selesai', 'rules' => $jam],
            'lokasi'        => ['label' => 'Lokasi', 'rules' => 'required|max_length[255]'],
            'penyelenggara' => ['label' => 'Penyelenggara', 'rules' => 'required|max_length[150]'],
        ];
    }

    public function index()
    {
        $status = (string) $this->request->getGet('status');
        $b      = $this->model->orderBy('tanggal', 'DESC');
        if (in_array($status, ['Menunggu', 'Dipublikasikan', 'Ditolak'], true)) {
            $b->where('status', $status);
        } else {
            $status = '';
        }

        return view('admin/jadwal/index', [
            'title'  => 'Jadwal Donor - NORA',
            'jadwal' => $b->findAll(),
            'status' => $status,
        ]);
    }

    public function create()
    {
        return view('admin/jadwal/form', ['title' => 'Tambah Kegiatan Donor - NORA', 'mode' => 'tambah', 'jadwal' => [], 'mulai' => '08:00', 'selesai' => '12:00']);
    }

    public function store()
    {
        if (! $this->validate($this->aturan())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        if ($this->request->getPost('jam_selesai') <= $this->request->getPost('jam_mulai')) {
            return redirect()->back()->withInput()->with('errors', ['jam_selesai' => 'Jam selesai harus setelah jam mulai.']);
        }

        $data = $this->dataForm();
        // Kegiatan yang dibuat admin langsung dapat dipublikasikan.
        $data['status'] = 'Dipublikasikan';
        $id = $this->model->insert($data, true);

        $this->umumkan($data, 'baru');

        return redirect()->to(base_url('admin/jadwal'))->with('success', 'Kegiatan donor dipublikasikan dan notifikasi dikirim ke pengguna.');
    }

    public function edit($id)
    {
        $jadwal = $this->model->find($id);
        if (! $jadwal) {
            return redirect()->to(base_url('admin/jadwal'))->with('error', 'Data kegiatan tidak ditemukan.');
        }

        $bagian = explode(' - ', str_replace(' WITA', '', $jadwal['waktu']));

        return view('admin/jadwal/form', [
            'title'   => 'Edit Kegiatan Donor - NORA',
            'mode'    => 'edit',
            'jadwal'  => $jadwal,
            'mulai'   => $bagian[0] ?? '08:00',
            'selesai' => $bagian[1] ?? '12:00',
        ]);
    }

    public function update($id)
    {
        $lama = $this->model->find($id);
        if (! $lama) {
            return redirect()->to(base_url('admin/jadwal'))->with('error', 'Data kegiatan tidak ditemukan.');
        }
        if (! $this->validate($this->aturan())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        if ($this->request->getPost('jam_selesai') <= $this->request->getPost('jam_mulai')) {
            return redirect()->back()->withInput()->with('errors', ['jam_selesai' => 'Jam selesai harus setelah jam mulai.']);
        }

        $data = $this->dataForm();
        $this->model->update($id, $data);

        // Perubahan jadwal/lokasi pada kegiatan yang sudah tayang -> kirim notifikasi perubahan.
        $berubah = $lama['tanggal'] !== $data['tanggal'] || $lama['waktu'] !== $data['waktu'] || $lama['lokasi'] !== $data['lokasi'];
        if ($lama['status'] === 'Dipublikasikan' && $berubah) {
            $this->umumkan($data, 'ubah');
        }

        return redirect()->to(base_url('admin/jadwal'))->with('success', 'Kegiatan donor berhasil diperbarui.');
    }

    /** Setujui publikasi / tolak pengajuan kegiatan dari fasilitas. */
    public function status($id)
    {
        $jadwal = $this->model->find($id);
        $status = $this->request->getPost('status');

        if (! $jadwal || ! in_array($status, ['Dipublikasikan', 'Ditolak'], true)) {
            return redirect()->back()->with('error', 'Data kegiatan tidak ditemukan.');
        }

        $this->model->update($id, ['status' => $status]);

        if ($status === 'Dipublikasikan') {
            $this->umumkan($jadwal, 'baru');
        }

        if ($jadwal['id_fasilitas']) {
            $f = (new FasilitasModel())->find($jadwal['id_fasilitas']);
            if ($f && $f['id_user']) {
                Notifier::kirim(
                    (int) $f['id_user'],
                    'Jadwal',
                    'Pengajuan kegiatan "' . $jadwal['nama_kegiatan'] . '" ' . ($status === 'Dipublikasikan' ? 'disetujui dan dipublikasikan.' : 'ditolak admin.'),
                    'jadwal/saya'
                );
            }
        }

        return redirect()->back()->with('success', 'Status kegiatan diperbarui.');
    }

    public function delete($id)
    {
        if (! $this->model->find($id)) {
            return redirect()->to(base_url('admin/jadwal'))->with('error', 'Data kegiatan tidak ditemukan.');
        }
        $this->model->delete($id);

        return redirect()->to(base_url('admin/jadwal'))->with('success', 'Kegiatan donor berhasil dihapus.');
    }

    private function dataForm(): array
    {
        return [
            'nama_kegiatan' => trim($this->request->getPost('nama_kegiatan')),
            'tanggal'       => $this->request->getPost('tanggal'),
            'waktu'         => $this->request->getPost('jam_mulai') . ' - ' . $this->request->getPost('jam_selesai') . ' WITA',
            'lokasi'        => trim($this->request->getPost('lokasi')),
            'penyelenggara' => trim($this->request->getPost('penyelenggara')),
        ];
    }

    /** Notifikasi kegiatan baru / perubahan jadwal ke pendonor dan pencari darah. */
    private function umumkan(array $j, string $jenis): void
    {
        $awal = $jenis === 'ubah' ? 'Perubahan jadwal: ' : 'Kegiatan donor baru: ';
        $isi  = $awal . $j['nama_kegiatan'] . ' - ' . tgl_indo($j['tanggal']) . ', ' . $j['waktu'] . ' di ' . $j['lokasi'] . '.';

        Notifier::kirimBanyak(
            array_merge(Notifier::idPenggunaRole('pendonor'), Notifier::idPenggunaRole('pencari')),
            'Jadwal',
            $isi,
            'jadwal'
        );
    }
}
