<?php

namespace App\Controllers;

use App\Libraries\Notifier;
use App\Libraries\Referensi;
use App\Models\FasilitasModel;
use App\Models\PendonorModel;
use App\Models\UserModel;

/** Pengelolaan akun + profil pendonor / data fasilitas (DFD proses 2). */
class Profil extends BaseController
{
    public function index()
    {
        return view('profil/index', [
            'title'     => 'Profil Saya - NORA',
            'akun'      => (new UserModel())->find($this->idUser()),
            'pendonor'  => $this->role() === 'pendonor' ? $this->pendonorSaya() : null,
            'fasilitas' => $this->role() === 'fasilitas' ? $this->fasilitasSaya() : null,
            'golongan'  => Referensi::GOLONGAN,
        ]);
    }

    public function akun()
    {
        $id   = $this->idUser();
        $telp = normalisasi_telp($this->request->getPost('no_telp'));

        $rules = [
            'nama'    => ['label' => 'Nama', 'rules' => 'required|max_length[100]'],
            'email'   => ['label' => 'Email', 'rules' => "permit_empty|valid_email|max_length[100]|is_unique[pengguna.email,id_user,{$id}]"],
            'no_telp' => ['label' => 'No. telepon', 'rules' => "permit_empty|regex_match[/^0[0-9]{8,14}$/]|is_unique[pengguna.no_telp,id_user,{$id}]"],
        ];

        $data            = $this->request->getPost();
        $data['no_telp'] = $telp ?? '';

        if (! $this->validateData($data, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = trim((string) $this->request->getPost('email')) ?: null;
        if (! $email && ! $telp) {
            return redirect()->back()->withInput()
                ->with('errors', ['identitas' => 'Isi minimal salah satu: nomor telepon atau email.']);
        }

        $nama = trim($this->request->getPost('nama'));
        (new UserModel())->update($id, ['nama' => $nama, 'email' => $email, 'no_telp' => $telp]);
        session()->set('nama', $nama);

        return redirect()->to(base_url('profil'))->with('success', 'Data akun berhasil diperbarui.');
    }

    public function password()
    {
        $rules = [
            'password_lama' => ['label' => 'Kata sandi lama', 'rules' => 'required'],
            'password_baru' => ['label' => 'Kata sandi baru', 'rules' => 'required|min_length[6]|max_length[72]'],
            'konfirmasi'    => ['label' => 'Konfirmasi kata sandi', 'rules' => 'required|matches[password_baru]'],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $model = new UserModel();
        $user  = $model->find($this->idUser());

        if (! password_verify($this->request->getPost('password_lama'), $user['password'])) {
            return redirect()->back()->with('errors', ['password_lama' => 'Kata sandi lama tidak sesuai.']);
        }

        $model->update($this->idUser(), ['password' => password_hash($this->request->getPost('password_baru'), PASSWORD_DEFAULT)]);

        return redirect()->to(base_url('profil'))->with('success', 'Kata sandi berhasil diubah.');
    }

    /** Pendonor melengkapi golongan darah, riwayat donor, dan lokasi. */
    public function pendonor()
    {
        $rules = [
            'golongan_darah' => ['label' => 'Golongan darah', 'rules' => 'required|in_list[' . implode(',', Referensi::GOLONGAN) . ']'],
            'lokasi'         => ['label' => 'Lokasi', 'rules' => 'required|max_length[150]'],
            'riwayat_donor'  => ['label' => 'Riwayat donor', 'rules' => 'permit_empty|max_length[1000]'],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model  = new PendonorModel();
        $profil = $this->pendonorSaya();
        $gol    = $this->request->getPost('golongan_darah');

        $data = [
            'golongan_darah' => $gol,
            'lokasi'         => trim($this->request->getPost('lokasi')),
            'riwayat_donor'  => trim((string) $this->request->getPost('riwayat_donor')) ?: null,
        ];

        // Profil baru / golongan darah berubah -> perlu diverifikasi admin lagi sebelum aktif.
        $perluVerifikasi = empty($profil['golongan_darah']) || $profil['golongan_darah'] !== $gol;
        if ($perluVerifikasi) {
            $data['status_verifikasi'] = 'Menunggu';
        }

        $model->update($profil['id_pendonor'], $data);

        if ($perluVerifikasi) {
            Notifier::kirimBanyak(
                Notifier::idPenggunaRole('admin'),
                'Layanan',
                'Profil pendonor ' . session()->get('nama') . ' (golongan ' . $gol . ') menunggu verifikasi.',
                'admin/pendonor'
            );
        }

        return redirect()->to(base_url('profil'))->with(
            'success',
            $perluVerifikasi
                ? 'Profil pendonor disimpan dan menunggu verifikasi admin.'
                : 'Profil pendonor berhasil diperbarui.'
        );
    }

    /** Fasilitas memperbarui nama, alamat, dan kontak layanan. */
    public function fasilitas()
    {
        $rules = [
            'nama_fasilitas' => ['label' => 'Nama fasilitas', 'rules' => 'required|max_length[150]'],
            'alamat'         => ['label' => 'Alamat', 'rules' => 'required|max_length[255]'],
            'kontak'         => ['label' => 'Kontak', 'rules' => 'permit_empty|max_length[50]'],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fasilitas = $this->fasilitasSaya();
        (new FasilitasModel())->update($fasilitas['id_fasilitas'], [
            'nama_fasilitas' => trim($this->request->getPost('nama_fasilitas')),
            'alamat'         => trim($this->request->getPost('alamat')),
            'kontak'         => trim((string) $this->request->getPost('kontak')) ?: null,
        ]);

        return redirect()->to(base_url('profil'))->with('success', 'Data fasilitas berhasil diperbarui.');
    }
}
