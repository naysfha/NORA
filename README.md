# NORA - Sistem Informasi Donor Darah Terintegrasi pada PMI Kota Palu berbasis Web

Aplikasi web berbasis **CodeIgniter 4 (MVC)** yang dibuat mengikuti laporan perancangan NORA:
4 aktor (Pendonor, Pencari Darah/Penerima, Admin PMI, Fasilitas Kesehatan/Unit Donor) dan 8 modul
(Autentikasi, Profil, Stok Darah, Jadwal & Lokasi, Edukasi, SOS, Notifikasi, Laporan).

## Alur utama 
- **Registrasi/Login** -> pembagian hak akses per peran (filter `auth` & `role`).
- **Pendonor**: lengkapi profil (golongan darah, riwayat, lokasi) -> diverifikasi admin -> baca edukasi, lihat jadwal, respons SOS.
- **Pencari darah**: cari stok (golongan + lokasi) -> buat SOS (validasi kelengkapan) -> status *Menunggu Verifikasi > Diverifikasi > Diproses > Selesai*.
- **Fasilitas**: kirim stok & ajukan kegiatan -> menunggu verifikasi/persetujuan admin -> tampil ke publik.
- **Admin**: kelola pengguna, pendonor, fasilitas, stok, jadwal, edukasi, SOS, kirim notifikasi, susun laporan.
- **Notifikasi** otomatis: status SOS, kebutuhan golongan darah, jadwal baru/berubah, pembaruan stok.

## Kelompok Pengembang: 11
1. Ibnaty Naeela Adjani (F5212510008)
2. Nayla Syafha         (F5212510020)
3. Nadini Putri         (F5212510022)
