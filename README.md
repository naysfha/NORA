# NORA - Sistem Informasi Donor Darah Terintegrasi (PMI Kota Palu)

Aplikasi web berbasis **CodeIgniter 4 (MVC)** yang dibuat mengikuti laporan perancangan NORA:
4 aktor (Pendonor, Pencari Darah/Penerima, Admin PMI, Fasilitas Kesehatan/Unit Donor) dan 8 modul
(Autentikasi, Profil, Stok Darah, Jadwal & Lokasi, Edukasi, SOS, Notifikasi, Laporan). Tema tampilan merah darah.

## Cara menjalankan
Kebutuhan: PHP 8.2+ (ekstensi `intl`, `mbstring`, `mysqli`), MySQL/MariaDB. Folder `vendor/` sudah disertakan.

1. Buat database: `CREATE DATABASE nora_donor_darah;`
2. Sesuaikan `.env` (host, user, password database). `app.baseURL` default `http://localhost:8080/`.
3. Jalankan di terminal dari folder proyek:
   ```
   php spark migrate
   php spark db:seed NoraSeeder
   php spark serve
   ```
   Buka http://localhost:8080
   (Alternatif tanpa terminal: import `database/nora_donor_darah.sql` lewat phpMyAdmin.)

## Akun contoh
| Peran | Email | Kata sandi |
|---|---|---|
| Admin PMI | admin@nora.id | Admin123! |
| Pendonor | pendonor@nora.id | Pendonor123! |
| Pencari darah | pencari@nora.id | Pencari123! |
| Fasilitas | fasilitas@nora.id | Fasilitas123! |

Login dapat memakai email **atau** nomor telepon. Data fasilitas/alamat/telepon pada seeder hanyalah data contoh.

## Alur utama (sesuai laporan)
- **Registrasi/Login** -> pembagian hak akses per peran (filter `auth` & `role`).
- **Pendonor**: lengkapi profil (golongan darah, riwayat, lokasi) -> diverifikasi admin -> baca edukasi, lihat jadwal, respons SOS.
- **Pencari darah**: cari stok (golongan + lokasi) -> buat SOS (validasi kelengkapan) -> status *Menunggu Verifikasi > Diverifikasi > Diproses > Selesai*.
- **Fasilitas**: kirim stok & ajukan kegiatan -> menunggu verifikasi/persetujuan admin -> tampil ke publik.
- **Admin**: kelola pengguna, pendonor, fasilitas, stok, jadwal, edukasi, SOS, kirim notifikasi, susun laporan.
- **Notifikasi** otomatis: status SOS, kebutuhan golongan darah, jadwal baru/berubah, pembaruan stok.

## Struktur MVC
```
app/
  Controllers/   Auth, Beranda, Dashboard, Profil, Edukasi, Stok, Lokasi, Jadwal, Sos, Notifikasi
  Controllers/Admin/   Pengguna, Pendonor, Fasilitas, Stok, Jadwal, Edukasi, Sos, Notifikasi, Laporan
  Models/        User, Pendonor, Fasilitas, Stok, Jadwal, Sos, ResponsSos, Notifikasi, Laporan, Edukasi
  Views/         layouts, auth, beranda, dashboard, profil, stok, jadwal, sos, ... , admin/*
  Filters/       AuthFilter, RoleFilter
  Libraries/     Referensi (daftar nilai tetap), Notifier (pembuat notifikasi)
  Database/      Migrations (10 tabel) + Seeds/NoraSeeder
```

## Tabel
Sesuai ERD laporan: `pengguna`, `pendonor`, `fasilitas_kesehatan`, `stok_darah`, `jadwal_donor`,
`permintaan_sos`, `notifikasi`, `laporan`.

Tambahan agar alur di laporan dapat berjalan (sebaiknya ditambahkan juga ke ERD/kamus data laporan):
- tabel `respons_sos` (satu SOS dapat memperoleh banyak respons pendonor) dan `edukasi` (konten edukasi),
- kolom `status_verifikasi` pada `pendonor` dan `stok_darah`, `sumber` pada `stok_darah`,
- kolom `status` dan `id_fasilitas` pada `jadwal_donor`, `catatan_admin` pada `permintaan_sos`, `tautan` pada `notifikasi`,
- `fasilitas_kesehatan.id_user` boleh kosong (lokasi/unit yang dibuat langsung oleh admin).

## Catatan
- Informasi stok dan SOS bersifat informatif; kelayakan donor dan kecocokan darah ditentukan tenaga kesehatan (ditampilkan sebagai disclaimer di aplikasi).
- Ikon memakai Bootstrap Icons lokal; font Plus Jakarta Sans dimuat dari Google Fonts (ada font cadangan bila offline).
