<?php

namespace App\Libraries;

/**
 * Daftar nilai tetap (referensi) yang dipakai di seluruh sistem NORA.
 */
class Referensi
{
    public const GOLONGAN = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

    public const ROLE_REGISTRASI = [
        'pendonor'  => 'Pendonor',
        'pencari'   => 'Pencari Darah / Penerima',
        'fasilitas' => 'Fasilitas Kesehatan / Unit Donor',
    ];

    public const ROLE = [
        'admin'     => 'Admin PMI',
        'pendonor'  => 'Pendonor',
        'pencari'   => 'Pencari Darah',
        'fasilitas' => 'Fasilitas Kesehatan',
    ];

    public const URGENSI = ['Biasa', 'Mendesak', 'Sangat Mendesak'];

    // Siklus status permintaan SOS: Menunggu Verifikasi > Diverifikasi > Diproses > Selesai (atau Ditolak)
    public const STATUS_SOS = ['Menunggu Verifikasi', 'Diverifikasi', 'Diproses', 'Selesai', 'Ditolak'];

    public const KATEGORI_EDUKASI = [
        'Pengertian Donor',
        'Persyaratan Donor',
        'Persiapan Sebelum Donor',
        'Proses Donor',
        'Perawatan Setelah Donor',
    ];

    public const JENIS_NOTIFIKASI = [
        'SOS'      => 'Status Permintaan SOS',
        'Golongan' => 'Kebutuhan Golongan Darah',
        'Jadwal'   => 'Jadwal Kegiatan Donor',
        'Stok'     => 'Informasi Stok Darah',
        'Layanan'  => 'Pembaruan Layanan',
    ];

    public const JENIS_LAPORAN = [
        'Rekapitulasi Umum'  => 'Rekapitulasi Umum',
        'Data Pengguna'      => 'Data Pengguna',
        'Stok Darah'         => 'Stok Darah',
        'Jadwal Donor'       => 'Jadwal Kegiatan Donor',
        'Permintaan SOS'     => 'Permintaan SOS',
    ];

    public const DISCLAIMER_SOS = 'Fitur SOS hanyalah sarana pemberitahuan awal dan bukan pengganti layanan medis darurat, ambulans, rumah sakit, atau tenaga kesehatan. Untuk kondisi gawat darurat, segera hubungi layanan darurat atau fasilitas kesehatan terdekat.';

    public const DISCLAIMER_UMUM = 'Informasi pada NORA bersifat edukatif dan informatif. NORA tidak menjamin stok darah selalu tersedia, pendonor pasti dapat mendonor, atau permintaan pasti terpenuhi. Kelayakan donor, pemeriksaan golongan dan kecocokan darah, penyimpanan, distribusi, serta transfusi tetap ditentukan oleh tenaga kesehatan atau unit transfusi darah yang berwenang.';
}
