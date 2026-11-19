<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Referensi;
use App\Models\EdukasiModel;

/** Admin: memperbarui konten edukasi donor darah. */
class Edukasi extends BaseController
{
    protected EdukasiModel $model;

    public function __construct()
    {
        $this->model = new EdukasiModel();
    }

    private function aturan(): array
    {
        return [
            'judul'     => ['label' => 'Judul', 'rules' => 'required|max_length[150]'],
            'kategori'  => ['label' => 'Kategori', 'rules' => 'required|in_list[' . implode(',', Referensi::KATEGORI_EDUKASI) . ']'],
            'ringkasan' => ['label' => 'Ringkasan', 'rules' => 'permit_empty|max_length[255]'],
            'isi'       => ['label' => 'Isi artikel', 'rules' => 'required|min_length[20]'],
        ];
    }

    private function dataForm(): array
    {
        return [
            'judul'     => trim($this->request->getPost('judul')),
            'kategori'  => $this->request->getPost('kategori'),
            'ringkasan' => trim((string) $this->request->getPost('ringkasan')) ?: null,
            'isi'       => trim($this->request->getPost('isi')),
        ];
    }

    public function index()
    {
        return view('admin/edukasi/index', [
            'title'   => 'Konten Edukasi - NORA',
            'artikel' => $this->model->orderBy('kategori')->orderBy('id_edukasi')->findAll(),
        ]);
    }

    public function create()
    {
        return view('admin/edukasi/form', ['title' => 'Tambah Edukasi - NORA', 'mode' => 'tambah', 'artikel' => [], 'kategori' => Referensi::KATEGORI_EDUKASI]);
    }

    public function store()
    {
        if (! $this->validate($this->aturan())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data             = $this->dataForm();
        $data['id_admin'] = $this->idUser();
        $this->model->insert($data);

        return redirect()->to(base_url('admin/edukasi'))->with('success', 'Konten edukasi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $artikel = $this->model->find($id);
        if (! $artikel) {
            return redirect()->to(base_url('admin/edukasi'))->with('error', 'Konten edukasi tidak ditemukan.');
        }

        return view('admin/edukasi/form', ['title' => 'Edit Edukasi - NORA', 'mode' => 'edit', 'artikel' => $artikel, 'kategori' => Referensi::KATEGORI_EDUKASI]);
    }

    public function update($id)
    {
        if (! $this->model->find($id)) {
            return redirect()->to(base_url('admin/edukasi'))->with('error', 'Konten edukasi tidak ditemukan.');
        }
        if (! $this->validate($this->aturan())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, $this->dataForm());

        return redirect()->to(base_url('admin/edukasi'))->with('success', 'Konten edukasi berhasil diperbarui.');
    }

    public function delete($id)
    {
        if (! $this->model->find($id)) {
            return redirect()->to(base_url('admin/edukasi'))->with('error', 'Konten edukasi tidak ditemukan.');
        }
        $this->model->delete($id);

        return redirect()->to(base_url('admin/edukasi'))->with('success', 'Konten edukasi berhasil dihapus.');
    }
}
