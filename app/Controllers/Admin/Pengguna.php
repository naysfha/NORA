<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Referensi;
use App\Models\FasilitasModel;
use App\Models\PendonorModel;
use App\Models\UserModel;

/** Admin: pengelolaan data pengguna (DFD proses 1). */
class Pengguna extends BaseController
{
    protected UserModel $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    public function index()
    {
        $q    = trim((string) $this->request->getGet('q'));
        $role = (string) $this->request->getGet('role');

        $b = $this->model->orderBy('role')->orderBy('nama');
        if ($q !== '') {
            $b->groupStart()->like('nama', $q)->orLike('email', $q)->orLike('no_telp', $q)->groupEnd();
        }
        if (isset(Referensi::ROLE[$role])) {
            $b->where('role', $role);
        } else {
            $role = '';
        }

        return view('admin/pengguna/index', [
            'title'    => 'Data Pengguna - NORA',
            'pengguna' => $b->findAll(),
            'filter'   => ['q' => $q, 'role' => $role],
            'role'     => Referensi::ROLE,
        ]);
    }

    public function create()
    {
        return view('admin/pengguna/form', [
            'title'    => 'Tambah Pengguna - NORA',
            'mode'     => 'tambah',
            'pengguna' => [],
            'role'     => Referensi::ROLE,
        ]);
    }

    public function store()
    {
        $role = (string) $this->request->getPost('role');
        $telp = normalisasi_telp($this->request->getPost('no_telp'));

        $rules = $this->aturan(0);
        $rules['password'] = ['label' => 'Kata sandi', 'rules' => 'required|min_length[6]|max_length[72]'];
        $rules['role']     = ['label' => 'Peran', 'rules' => 'required|in_list[' . implode(',', array_keys(Referensi::ROLE)) . ']'];
        if ($role === 'fasilitas') {
            $rules['alamat'] = ['label' => 'Alamat fasilitas', 'rules' => 'required|max_length[255]'];
        }

        $data            = $this->request->getPost();
        $data['no_telp'] = $telp ?? '';

        if (! $this->validateData($data, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = trim((string) $this->request->getPost('email')) ?: null;
        if (! $email && ! $telp) {
            return redirect()->back()->withInput()->with('errors', ['identitas' => 'Isi minimal salah satu: nomor telepon atau email.']);
        }

        $db = db_connect();
        $db->transStart();

        $idUser = $this->model->insert([
            'nama'     => trim($data['nama']),
            'email'    => $email,
            'no_telp'  => $telp,
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role'     => $role,
        ], true);

        // Akun yang dibuat admin langsung dianggap terverifikasi.
        if ($role === 'pendonor') {
            (new PendonorModel())->insert(['id_user' => $idUser, 'status_verifikasi' => 'Terverifikasi']);
        } elseif ($role === 'fasilitas') {
            (new FasilitasModel())->insert([
                'id_user'           => $idUser,
                'nama_fasilitas'    => trim($data['nama']),
                'alamat'            => trim($data['alamat']),
                'kontak'            => $telp,
                'status_verifikasi' => 'Terverifikasi',
            ]);
        }

        $db->transComplete();

        return redirect()->to(base_url('admin/pengguna'))->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pengguna = $this->model->find($id);
        if (! $pengguna) {
            return redirect()->to(base_url('admin/pengguna'))->with('error', 'Data pengguna tidak ditemukan.');
        }

        return view('admin/pengguna/form', [
            'title'    => 'Edit Pengguna - NORA',
            'mode'     => 'edit',
            'pengguna' => $pengguna,
            'role'     => Referensi::ROLE,
        ]);
    }

    public function update($id)
    {
        $pengguna = $this->model->find($id);
        if (! $pengguna) {
            return redirect()->to(base_url('admin/pengguna'))->with('error', 'Data pengguna tidak ditemukan.');
        }

        $telp  = normalisasi_telp($this->request->getPost('no_telp'));
        $rules = $this->aturan((int) $id);
        if ($this->request->getPost('password') !== '') {
            $rules['password'] = ['label' => 'Kata sandi', 'rules' => 'min_length[6]|max_length[72]'];
        }

        $data            = $this->request->getPost();
        $data['no_telp'] = $telp ?? '';

        if (! $this->validateData($data, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = trim((string) $this->request->getPost('email')) ?: null;
        if (! $email && ! $telp) {
            return redirect()->back()->withInput()->with('errors', ['identitas' => 'Isi minimal salah satu: nomor telepon atau email.']);
        }

        $ubah = ['nama' => trim($data['nama']), 'email' => $email, 'no_telp' => $telp];
        if (($data['password'] ?? '') !== '') {
            $ubah['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $this->model->update($id, $ubah);

        if ((int) $id === $this->idUser()) {
            session()->set('nama', $ubah['nama']);
        }

        return redirect()->to(base_url('admin/pengguna'))->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function delete($id)
    {
        if ((int) $id === $this->idUser()) {
            return redirect()->to(base_url('admin/pengguna'))->with('error', 'Akun yang sedang Anda pakai tidak dapat dihapus.');
        }
        if (! $this->model->find($id)) {
            return redirect()->to(base_url('admin/pengguna'))->with('error', 'Data pengguna tidak ditemukan.');
        }

        $this->model->delete($id);

        return redirect()->to(base_url('admin/pengguna'))->with('success', 'Pengguna beserta data terkaitnya berhasil dihapus.');
    }

    private function aturan(int $id): array
    {
        return [
            'nama'    => ['label' => 'Nama', 'rules' => 'required|max_length[100]'],
            'email'   => ['label' => 'Email', 'rules' => "permit_empty|valid_email|max_length[100]|is_unique[pengguna.email,id_user,{$id}]"],
            'no_telp' => ['label' => 'No. telepon', 'rules' => "permit_empty|regex_match[/^0[0-9]{8,14}$/]|is_unique[pengguna.no_telp,id_user,{$id}]"],
        ];
    }
}
