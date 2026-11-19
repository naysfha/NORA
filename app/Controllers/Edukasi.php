<?php

namespace App\Controllers;

use App\Libraries\Referensi;
use App\Models\EdukasiModel;

/** Modul Edukasi: pengertian, persyaratan, persiapan, proses, dan perawatan setelah donor. */
class Edukasi extends BaseController
{
    public function index()
    {
        $model    = new EdukasiModel();
        $kategori = $this->request->getGet('kategori');
        $q        = trim((string) $this->request->getGet('q'));

        if ($kategori && in_array($kategori, Referensi::KATEGORI_EDUKASI, true)) {
            $model->where('kategori', $kategori);
        } else {
            $kategori = '';
        }
        if ($q !== '') {
            $model->groupStart()->like('judul', $q)->orLike('ringkasan', $q)->orLike('isi', $q)->groupEnd();
        }

        $semua = $model->orderBy('id_edukasi')->findAll();

        // Urutkan sesuai urutan kategori pada laporan.
        $urutan = array_flip(Referensi::KATEGORI_EDUKASI);
        usort($semua, static fn ($a, $b) => ($urutan[$a['kategori']] ?? 99) <=> ($urutan[$b['kategori']] ?? 99));

        return view('edukasi/index', [
            'title'    => 'Edukasi Donor Darah - NORA',
            'artikel'  => $semua,
            'kategori' => Referensi::KATEGORI_EDUKASI,
            'filter'   => ['kategori' => $kategori, 'q' => $q],
        ]);
    }

    public function show($id)
    {
        $model   = new EdukasiModel();
        $artikel = $model->find($id);

        if (! $artikel) {
            return redirect()->to(base_url('edukasi'))->with('error', 'Artikel edukasi tidak ditemukan.');
        }

        return view('edukasi/show', [
            'title'   => $artikel['judul'] . ' - NORA',
            'artikel' => $artikel,
            'lainnya' => $model->where('id_edukasi !=', $id)->where('kategori', $artikel['kategori'])->findAll(3),
        ]);
    }
}
