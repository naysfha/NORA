<?php

namespace App\Controllers;

use App\Libraries\Notifier;
use App\Libraries\Referensi;
use App\Models\FasilitasModel;
use App\Models\PendonorModel;
use App\Models\UserModel;

/**
 * Modul Autentikasi (DFD proses 1): registrasi, login, dan pembagian hak akses berdasarkan peran.
 */
class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }

        return view('auth/login', ['title' => 'Masuk - NORA']);
    }

    public function attemptLogin()
    {
        $rules = [
            'identitas' => ['label' => 'Email / No. telepon', 'rules' => 'required|max_length[100]'],
            'password'  => ['label' => 'Kata sandi', 'rules' => 'required'],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Email/nomor telepon dan kata sandi wajib diisi.');
        }

        $identitas = trim($this->request->getPost('identitas'));
        $user      = (new UserModel())->cariIdentitas($identitas);

        if (! $user || ! password_verify($this->request->getPost('password'), $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Email/nomor telepon atau kata sandi salah.');
        }

        session()->regenerate();
        session()->set([
            'id_user'   => (int) $user['id_user'],
            'nama'      => $user['nama'],
            'role'      => $user['role'],
            'logged_in' => true,
        ]);

        return redirect()->to(base_url('dashboard'));
    }

    public function register()
    {
        if (session()->get('logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }

        return view('auth/register', [
            'title' => 'Daftar - NORA',
            'role'  => Referensi::ROLE_REGISTRASI,
        ]);
    }

    public function attemptRegister()
    {
        $role = (string) $this->request->getPost('role');

        $rules = [
            'nama'     => ['label' => 'Nama', 'rules' => 'required|max_length[100]'],
            'role'     => ['label' => 'Peran', 'rules' => 'required|in_list[' . implode(',', array_keys(Referensi::ROLE_REGISTRASI)) . ']'],
            'email'    => ['label' => 'Email', 'rules' => 'permit_empty|valid_email|max_length[100]|is_unique[pengguna.email]'],
            'password' => ['label' => 'Kata sandi', 'rules' => 'required|min_length[6]|max_length[72]'],
            'konfirmasi' => ['label' => 'Konfirmasi kata sandi', 'rules' => 'required|matches[password]'],
        ];

        // Nomor telepon dinormalkan dulu (08xx) agar pengecekan unik akurat.
        $telp = normalisasi_telp($this->request->getPost('no_telp'));
        $rules['no_telp'] = ['label' => 'No. telepon', 'rules' => 'permit_empty|regex_match[/^0[0-9]{8,14}$/]|is_unique[pengguna.no_telp]'];

        if ($role === 'fasilitas') {
            $rules['alamat'] = ['label' => 'Alamat fasilitas', 'rules' => 'required|max_length[255]'];
        }

        $data            = $this->request->getPost();
        $data['no_telp'] = $telp ?? '';

        if (! $this->validateData($data, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = trim((string) $this->request->getPost('email')) ?: null;

        // Pengguna wajib mengisi nomor telepon ATAU email.
        if (! $email && ! $telp) {
            return redirect()->back()->withInput()
                ->with('errors', ['identitas' => 'Isi minimal salah satu: nomor telepon atau email.']);
        }

        $db = db_connect();
        $db->transStart();

        $idUser = (new UserModel())->insert([
            'nama'     => trim($this->request->getPost('nama')),
            'email'    => $email,
            'no_telp'  => $telp,
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => $role,
        ], true);

        if ($role === 'pendonor') {
            (new PendonorModel())->insert(['id_user' => $idUser, 'status_verifikasi' => 'Menunggu']);
        } elseif ($role === 'fasilitas') {
            (new FasilitasModel())->insert([
                'id_user'           => $idUser,
                'nama_fasilitas'    => trim($this->request->getPost('nama')),
                'alamat'            => trim($this->request->getPost('alamat')),
                'kontak'            => $telp,
                'status_verifikasi' => 'Menunggu',
            ]);
            // Beri tahu admin bahwa ada fasilitas baru yang perlu diperiksa.
            Notifier::kirimBanyak(
                Notifier::idPenggunaRole('admin'),
                'Layanan',
                'Pendaftaran fasilitas baru menunggu verifikasi: ' . trim($this->request->getPost('nama')) . '.',
                'admin/fasilitas'
            );
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Pendaftaran gagal diproses. Silakan coba lagi.');
        }

        return redirect()->to(base_url('login'))
            ->with('success', 'Pendaftaran berhasil. Silakan masuk dengan akun Anda.');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to(base_url('login'))->with('success', 'Anda berhasil keluar.');
    }
}
