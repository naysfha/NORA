<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Notifier;
use App\Libraries\Referensi;
use App\Models\FasilitasModel;
use App\Models\StokModel;

/** Admin: memperbarui & memverifikasi data stok darah (DFD proses 3). */
class Stok extends BaseController
{
    protected StokModel $model;

    public function __construct()
    {
        $this->model = new StokModel();
    }

    public function index()
    {
        $status = (string) $this->request->getGet('status');
        $b      = $this->model->select('stok_darah.*, f.nama_fasilitas')
            ->join('fasilitas_kesehatan f', 'f.id_fasilitas = stok_darah.id_fasilitas')
            ->orderBy('stok_darah.id_stok', 'DESC');

        if (in_array($status, ['Menunggu', 'Terverifikasi', 'Ditolak'], true)) {
            $b->where('stok_darah.status_verifikasi', $status);
        } else {
            $status = '';
        }

        return view('admin/stok/index', [
            'title'  => 'Stok Darah - NORA',
            'stok'   => $b->findAll(150),
            'status' => $status,
            'total'  => $this->model->totalPerGolongan(),
        ]);
    }

    public function create()
    {
        return view('admin/stok/form', [
            'title'     => 'Input Stok Darah - NORA',
            'fasilitas' => (new FasilitasModel())->where('status_verifikasi', 'Terverifikasi')->orderBy('nama_fasilitas')->findAll(),
            'golongan'  => Referensi::GOLONGAN,
        ]);
    }

    /** Admin menginput stok langsung -> otomatis terverifikasi (sumber: Admin). */
    public function store()
    {
        $rules = [
            'id_fasilitas'   => ['label' => 'Fasilitas', 'rules' => 'required|is_natural_no_zero'],
            'golongan_darah' => ['label' => 'Golongan darah', 'rules' => 'required|in_list[' . implode(',', Referensi::GOLONGAN) . ']'],
            'jumlah_kantong' => ['label' => 'Jumlah kantong', 'rules' => 'required|is_natural|less_than_equal_to[10000]'],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fasilitas = (new FasilitasModel())->find($this->request->getPost('id_fasilitas'));
        if (! $fasilitas) {
            return redirect()->back()->withInput()->with('error', 'Fasilitas tidak ditemukan.');
        }

        $gol = $this->request->getPost('golongan_darah');
        $this->model->insert([
            'id_fasilitas'      => $fasilitas['id_fasilitas'],
            'golongan_darah'    => $gol,
            'jumlah_kantong'    => (int) $this->request->getPost('jumlah_kantong'),
            'sumber'            => 'Admin',
            'status_verifikasi' => 'Terverifikasi',
        ]);

        $this->beritahuPencari($gol, $fasilitas['nama_fasilitas']);

        return redirect()->to(base_url('admin/stok'))->with('success', 'Data stok disimpan dan langsung tampil sebagai terverifikasi.');
    }

    public function verifikasi($id)
    {
        $stok   = $this->model->find($id);
        $status = $this->request->getPost('status');

        if (! $stok || ! in_array($status, ['Terverifikasi', 'Ditolak'], true)) {
            return redirect()->back()->with('error', 'Data stok tidak ditemukan.');
        }

        $this->model->update($id, ['status_verifikasi' => $status]);
        $fasilitas = (new FasilitasModel())->find($stok['id_fasilitas']);

        if ($fasilitas) {
            if ($status === 'Terverifikasi') {
                $this->beritahuPencari($stok['golongan_darah'], $fasilitas['nama_fasilitas']);
            }
            if ($fasilitas['id_user']) {
                Notifier::kirim(
                    (int) $fasilitas['id_user'],
                    'Stok',
                    "Pembaruan stok golongan {$stok['golongan_darah']} Anda berstatus {$status}.",
                    'stok/kelola'
                );
            }
        }

        return redirect()->back()->with('success', 'Status verifikasi stok diperbarui.');
    }

    public function delete($id)
    {
        if (! $this->model->find($id)) {
            return redirect()->back()->with('error', 'Data stok tidak ditemukan.');
        }
        $this->model->delete($id);

        return redirect()->back()->with('success', 'Catatan stok berhasil dihapus.');
    }

    /** Pencari dengan SOS aktif golongan yang sama diberi tahu ada perubahan informasi stok. */
    private function beritahuPencari(string $golongan, string $namaFasilitas): void
    {
        $rows = db_connect()->table('permintaan_sos')->select('id_pencari')
            ->where('golongan_darah', $golongan)
            ->whereIn('status', ['Menunggu Verifikasi', 'Diverifikasi', 'Diproses'])
            ->get()->getResultArray();

        Notifier::kirimBanyak(
            array_column($rows, 'id_pencari'),
            'Stok',
            "Informasi stok golongan {$golongan} di {$namaFasilitas} diperbarui. Cek ketersediaannya.",
            'stok?golongan=' . urlencode($golongan)
        );
    }
}
