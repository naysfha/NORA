<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Notifier;
use App\Models\FasilitasModel;
use App\Models\JadwalModel;
use App\Models\StokModel;

/** Admin: kelola data lokasi fasilitas kesehatan / unit donor darah + verifikasinya. */
class Fasilitas extends BaseController
{
    protected FasilitasModel $model;

    public function __construct()
    {
        $this->model = new FasilitasModel();
    }

    private function aturan(): array
    {
        return [
            'nama_fasilitas' => ['label' => 'Nama fasilitas', 'rules' => 'required|max_length[150]'],
            'alamat'         => ['label' => 'Alamat', 'rules' => 'required|max_length[255]'],
            'kontak'         => ['label' => 'Kontak', 'rules' => 'permit_empty|max_length[50]'],
        ];
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $b = $this->model->orderBy('nama_fasilitas');
        if ($q !== '') {
            $b->groupStart()->like('nama_fasilitas', $q)->orLike('alamat', $q)->groupEnd();
        }

        return view('admin/fasilitas/index', [
            'title'     => 'Data Fasilitas - NORA',
            'fasilitas' => $b->findAll(),
            'q'         => $q,
        ]);
    }

    public function create()
    {
        return view('admin/fasilitas/form', ['title' => 'Tambah Fasilitas - NORA', 'mode' => 'tambah', 'fasilitas' => []]);
    }

    public function store()
    {
        if (! $this->validate($this->aturan())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->insert([
            'nama_fasilitas'    => trim($this->request->getPost('nama_fasilitas')),
            'alamat'            => trim($this->request->getPost('alamat')),
            'kontak'            => trim((string) $this->request->getPost('kontak')) ?: null,
            'status_verifikasi' => 'Terverifikasi', // dibuat langsung oleh admin
        ]);

        return redirect()->to(base_url('admin/fasilitas'))->with('success', 'Fasilitas / lokasi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $fasilitas = $this->model->find($id);
        if (! $fasilitas) {
            return redirect()->to(base_url('admin/fasilitas'))->with('error', 'Data fasilitas tidak ditemukan.');
        }

        return view('admin/fasilitas/form', ['title' => 'Edit Fasilitas - NORA', 'mode' => 'edit', 'fasilitas' => $fasilitas]);
    }

    public function update($id)
    {
        if (! $this->model->find($id)) {
            return redirect()->to(base_url('admin/fasilitas'))->with('error', 'Data fasilitas tidak ditemukan.');
        }
        if (! $this->validate($this->aturan())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, [
            'nama_fasilitas' => trim($this->request->getPost('nama_fasilitas')),
            'alamat'         => trim($this->request->getPost('alamat')),
            'kontak'         => trim((string) $this->request->getPost('kontak')) ?: null,
        ]);

        return redirect()->to(base_url('admin/fasilitas'))->with('success', 'Data fasilitas berhasil diperbarui.');
    }

    public function verifikasi($id)
    {
        $fasilitas = $this->model->find($id);
        $status    = $this->request->getPost('status');

        if (! $fasilitas || ! in_array($status, ['Terverifikasi', 'Menunggu', 'Ditolak'], true)) {
            return redirect()->to(base_url('admin/fasilitas'))->with('error', 'Data fasilitas tidak ditemukan.');
        }

        $this->model->update($id, ['status_verifikasi' => $status]);

        if ($fasilitas['id_user']) {
            $pesan = $status === 'Terverifikasi'
                ? 'Fasilitas ' . $fasilitas['nama_fasilitas'] . ' telah diverifikasi. Anda kini dapat mengirim data stok dan mengajukan kegiatan donor.'
                : 'Status verifikasi fasilitas ' . $fasilitas['nama_fasilitas'] . ' diperbarui menjadi ' . $status . '.';
            Notifier::kirim((int) $fasilitas['id_user'], 'Layanan', $pesan, 'dashboard');
        }

        return redirect()->to(base_url('admin/fasilitas'))->with('success', 'Status verifikasi fasilitas diperbarui.');
    }

    public function delete($id)
    {
        if (! $this->model->find($id)) {
            return redirect()->to(base_url('admin/fasilitas'))->with('error', 'Data fasilitas tidak ditemukan.');
        }

        $dipakai = (new StokModel())->where('id_fasilitas', $id)->countAllResults()
            + (new JadwalModel())->where('id_fasilitas', $id)->countAllResults();
        if ($dipakai > 0) {
            return redirect()->to(base_url('admin/fasilitas'))
                ->with('error', 'Fasilitas tidak dapat dihapus karena sudah memiliki data stok/kegiatan. Ubah status verifikasinya saja.');
        }

        $this->model->delete($id);

        return redirect()->to(base_url('admin/fasilitas'))->with('success', 'Fasilitas berhasil dihapus.');
    }
}
