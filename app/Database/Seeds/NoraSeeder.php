<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Data contoh NORA. Jalankan: php spark db:seed NoraSeeder
 * (seluruh nama fasilitas, alamat, dan nomor telepon di bawah ini hanya data contoh/dummy).
 */
class NoraSeeder extends Seeder
{
    public function run()
    {
        $db  = $this->db;
        $now = static fn (string $ubah = 'now') => date('Y-m-d H:i:s', strtotime($ubah));
        $tgl = static fn (string $ubah = 'now') => date('Y-m-d', strtotime($ubah));
        $hash = static fn (string $p) => password_hash($p, PASSWORD_DEFAULT);

        // ------------------------------------------------------------ PENGGUNA
        $akun = [
            // [nama, email, telp, password, role]
            ['Administrator PMI Palu', 'admin@nora.id',     '081200000001', 'Admin123!',     'admin'],
            ['Rizky Pratama',          'pendonor@nora.id',  '081200000002', 'Pendonor123!',  'pendonor'],
            ['Nurul Hidayah',          'nurul@nora.id',     '081200000003', 'Pendonor123!',  'pendonor'],
            ['Andi Mahendra',          'andi@nora.id',      '081200000004', 'Pendonor123!',  'pendonor'],
            ['Dewi Lestari',           'dewi@nora.id',      '081200000005', 'Pendonor123!',  'pendonor'],
            ['Fajar Ramadhan',         'fajar@nora.id',     '081200000006', 'Pendonor123!',  'pendonor'],
            ['Siti Aisyah',            'pencari@nora.id',   '081200000010', 'Pencari123!',   'pencari'],
            ['Budi Santoso',           'budi@nora.id',      '081200000011', 'Pencari123!',   'pencari'],
            ['UTD PMI Kota Palu',      'fasilitas@nora.id', '081200000020', 'Fasilitas123!', 'fasilitas'],
            ['RSUD Undata Palu',       'rsud@nora.id',      '081200000021', 'Fasilitas123!', 'fasilitas'],
            ['Klinik Pratama Harapan', 'klinik@nora.id',    '081200000022', 'Fasilitas123!', 'fasilitas'],
        ];
        $id = [];
        foreach ($akun as $i => [$nama, $email, $telp, $pass, $role]) {
            $db->table('pengguna')->insert([
                'nama' => $nama, 'email' => $email, 'no_telp' => $telp,
                'password' => $hash($pass), 'role' => $role,
                'tanggal_daftar' => $now('-' . (40 - $i * 2) . ' days'),
            ]);
            $id[$email] = (int) $db->insertID();
        }

        // ------------------------------------------------------------ PENDONOR
        $pendonor = [
            ['pendonor@nora.id', 'O+',  'Palu Selatan', 'Sudah 3 kali mendonor di UTD PMI Kota Palu. Tidak ada keluhan setelah donor.', 'Terverifikasi'],
            ['nurul@nora.id',    'A+',  'Palu Barat',   'Donor pertama kali pada kegiatan kampus.', 'Terverifikasi'],
            ['andi@nora.id',     'B+',  'Palu Timur',   'Rutin mendonor setiap beberapa bulan.', 'Terverifikasi'],
            ['dewi@nora.id',     'AB+', 'Mantikulore',  null, 'Menunggu'],
            ['fajar@nora.id',    'O-',  'Palu Utara',   'Pernah 2 kali mendonor.', 'Terverifikasi'],
        ];
        $idPendonor = [];
        foreach ($pendonor as [$email, $gol, $lokasi, $riwayat, $status]) {
            $db->table('pendonor')->insert([
                'id_user' => $id[$email], 'golongan_darah' => $gol, 'lokasi' => $lokasi,
                'riwayat_donor' => $riwayat, 'status_verifikasi' => $status,
            ]);
            $idPendonor[$email] = (int) $db->insertID();
        }

        // ------------------------------------------------------------ FASILITAS
        $fas = [
            ['fasilitas@nora.id', 'UTD PMI Kota Palu',          'Kota Palu, Sulawesi Tengah (alamat contoh)', '081200000020', 'Terverifikasi'],
            ['rsud@nora.id',      'RSUD Undata Palu',           'Kota Palu, Sulawesi Tengah (alamat contoh)', '081200000021', 'Terverifikasi'],
            ['klinik@nora.id',    'Klinik Pratama Harapan',     'Kota Palu, Sulawesi Tengah (alamat contoh)', '081200000022', 'Menunggu'],
            [null,                'Unit Mobil Donor PMI Kota Palu', 'Berpindah lokasi - lihat jadwal kegiatan donor', '081200000020', 'Terverifikasi'],
        ];
        $idFas = [];
        foreach ($fas as $k => [$email, $nama, $alamat, $kontak, $status]) {
            $db->table('fasilitas_kesehatan')->insert([
                'id_user' => $email ? $id[$email] : null, 'nama_fasilitas' => $nama,
                'alamat' => $alamat, 'kontak' => $kontak, 'status_verifikasi' => $status,
            ]);
            $idFas[$k] = (int) $db->insertID();
        }
        [$pmi, $rsud, $klinik, $mobil] = $idFas;

        // ------------------------------------------------------------ STOK DARAH
        $stok = [];
        $dataPmi = ['A+' => 12, 'A-' => 2, 'B+' => 15, 'B-' => 1, 'AB+' => 5, 'AB-' => 0, 'O+' => 18, 'O-' => 3];
        foreach ($dataPmi as $g => $n) {
            $stok[] = [$pmi, $g, $n, 'Fasilitas', 'Terverifikasi', $now('-1 day')];
        }
        // Catatan lama yang digantikan data yang lebih baru (riwayat tetap tersimpan)
        $stok[] = [$rsud, 'O+', 12, 'Fasilitas', 'Terverifikasi', $now('-3 days')];
        foreach (['A+' => 4, 'B+' => 6, 'O+' => 9, 'AB+' => 2, 'O-' => 1] as $g => $n) {
            $stok[] = [$rsud, $g, $n, 'Fasilitas', 'Terverifikasi', $now('-5 hours')];
        }
        $stok[] = [$mobil, 'O+', 3, 'Admin', 'Terverifikasi', $now('-2 days')];
        $stok[] = [$pmi, 'O-', 5, 'Fasilitas', 'Menunggu', $now('-30 minutes')]; // menunggu verifikasi admin
        foreach ($stok as [$f, $g, $n, $sumber, $status, $waktu]) {
            $db->table('stok_darah')->insert([
                'id_fasilitas' => $f, 'golongan_darah' => $g, 'jumlah_kantong' => $n,
                'sumber' => $sumber, 'status_verifikasi' => $status, 'tanggal_update' => $waktu,
            ]);
        }

        // ------------------------------------------------------------ JADWAL DONOR
        $jadwal = [
            [$pmi,   'Donor Darah Massal Kampus Universitas Tadulako', $tgl('+3 days'),  '08:00 - 14:00 WITA', 'Kampus Universitas Tadulako, Palu', 'UTD PMI Kota Palu', 'Dipublikasikan'],
            [$pmi,   'Donor Darah Rutin UTD PMI Kota Palu',             $tgl('+7 days'),  '08:00 - 15:00 WITA', 'Kantor UTD PMI Kota Palu',           'UTD PMI Kota Palu', 'Dipublikasikan'],
            [$mobil, 'Donor Darah Keliling Mobil Unit',                 $tgl('+12 days'), '09:00 - 13:00 WITA', 'Pusat Kota Palu (titik lokasi menyusul)', 'Unit Mobil Donor PMI Kota Palu', 'Dipublikasikan'],
            [$pmi,   'Donor Darah Peringatan Hari Relawan',             $tgl('-12 days'), '08:00 - 14:00 WITA', 'Lapangan Kota Palu',                 'UTD PMI Kota Palu', 'Dipublikasikan'],
            [$rsud,  'Bakti Sosial Donor Darah RSUD Undata',            $tgl('+10 days'), '08:30 - 12:00 WITA', 'Aula RSUD Undata Palu',              'RSUD Undata Palu', 'Menunggu'],
        ];
        foreach ($jadwal as [$f, $nama, $tanggal, $waktu, $lokasi, $penyelenggara, $status]) {
            $db->table('jadwal_donor')->insert([
                'id_fasilitas' => $f, 'nama_kegiatan' => $nama, 'tanggal' => $tanggal,
                'waktu' => $waktu, 'lokasi' => $lokasi, 'penyelenggara' => $penyelenggara, 'status' => $status,
            ]);
        }

        // ------------------------------------------------------------ PERMINTAAN SOS
        $sos = [
            // [pencari, gol, jml, lokasi, rs, kontak, urgensi, status, catatan, waktu]
            ['pencari@nora.id', 'O+', 2, 'Palu Barat',  'RSUD Undata Palu',       '081200000010', 'Mendesak',        'Diverifikasi',        null, $now('-6 hours')],
            ['pencari@nora.id', 'A+', 1, 'Palu Timur',  'RS Anutapura Palu',      '081200000010', 'Biasa',           'Menunggu Verifikasi', null, $now('-1 hour')],
            ['budi@nora.id',    'B+', 3, 'Palu Selatan', 'RSUD Undata Palu',      '081200000011', 'Sangat Mendesak', 'Diproses',            null, $now('-1 day')],
            ['budi@nora.id',    'O-', 1, 'Palu Utara',  'RS Anutapura Palu',      '081200000011', 'Mendesak',        'Selesai',             null, $now('-9 days')],
        ];
        $idSos = [];
        foreach ($sos as $k => [$pencari, $gol, $jml, $lokasi, $rs, $kontak, $urgensi, $status, $catatan, $waktu]) {
            $db->table('permintaan_sos')->insert([
                'id_pencari' => $id[$pencari], 'golongan_darah' => $gol, 'jumlah_dibutuhkan' => $jml,
                'lokasi' => $lokasi, 'rumah_sakit' => $rs, 'kontak' => $kontak,
                'tingkat_urgensi' => $urgensi, 'status' => $status, 'catatan_admin' => $catatan,
                'tanggal_pengajuan' => $waktu,
            ]);
            $idSos[$k] = (int) $db->insertID();
        }

        // Respons pendonor
        $db->table('respons_sos')->insertBatch([
            ['id_sos' => $idSos[0], 'id_pendonor' => $idPendonor['pendonor@nora.id'], 'pesan' => 'Saya bersedia, bisa datang sore ini.', 'tanggal_respons' => $now('-4 hours')],
            ['id_sos' => $idSos[2], 'id_pendonor' => $idPendonor['andi@nora.id'], 'pesan' => null, 'tanggal_respons' => $now('-20 hours')],
        ]);

        // ------------------------------------------------------------ NOTIFIKASI
        $notif = [
            ['pencari@nora.id',  'SOS',      "Status permintaan SOS #{$idSos[0]} Anda kini: Diverifikasi.", 'sos/detail/' . $idSos[0], 0, '-5 hours'],
            ['pencari@nora.id',  'SOS',      'Rizky Pratama (golongan O+) bersedia merespons permintaan SOS #' . $idSos[0] . '.', 'sos/detail/' . $idSos[0], 0, '-4 hours'],
            ['pendonor@nora.id', 'Golongan', "Dibutuhkan darah golongan O+ (2 kantong) di RSUD Undata Palu - urgensi Mendesak. Anda dapat memberi respons.", 'sos/detail/' . $idSos[0], 1, '-5 hours'],
            ['pendonor@nora.id', 'Jadwal',   'Kegiatan donor baru: Donor Darah Massal Kampus Universitas Tadulako.', 'jadwal', 0, '-2 hours'],
            ['andi@nora.id',     'Jadwal',   'Kegiatan donor baru: Donor Darah Rutin UTD PMI Kota Palu.', 'jadwal', 0, '-3 hours'],
            ['fasilitas@nora.id', 'Stok',    'Pembaruan stok golongan O- Anda berstatus Menunggu verifikasi admin.', 'stok/kelola', 0, '-25 minutes'],
            ['admin@nora.id',    'SOS',      "Permintaan SOS #{$idSos[1]} golongan A+ (Biasa) di RS Anutapura Palu menunggu verifikasi.", 'admin/sos/detail/' . $idSos[1], 0, '-1 hour'],
            ['admin@nora.id',    'Layanan',  'Pendaftaran fasilitas baru menunggu verifikasi: Klinik Pratama Harapan.', 'admin/fasilitas', 0, '-2 days'],
        ];
        foreach ($notif as [$email, $jenis, $isi, $tautan, $baca, $waktu]) {
            $db->table('notifikasi')->insert([
                'id_user' => $id[$email], 'jenis_notifikasi' => $jenis, 'isi_pesan' => $isi,
                'tautan' => $tautan, 'tanggal_kirim' => $now($waktu), 'status_baca' => $baca,
            ]);
        }

        // ------------------------------------------------------------ EDUKASI
        $edukasi = [
            ['Apa Itu Donor Darah?', 'Pengertian Donor Darah',
             'Mengenal donor darah, manfaatnya bagi penerima, dan mengapa pendonor sukarela sangat dibutuhkan.',
             "Donor darah adalah proses pengambilan sejumlah darah dari seseorang yang sehat secara sukarela untuk kemudian diberikan kepada orang lain yang membutuhkan melalui transfusi.\n\nDarah dibutuhkan oleh pasien talasemia, korban kecelakaan, ibu yang mengalami perdarahan saat melahirkan, pasien operasi, dan berbagai kondisi medis lain. Darah belum dapat dibuat di laboratorium sehingga persediaannya sangat bergantung pada kesediaan para pendonor.\n\nDarah yang didonorkan tidak langsung diberikan kepada pasien. Darah terlebih dahulu diperiksa dan diolah oleh unit transfusi darah agar aman digunakan. Karena itu, kegiatan donor yang dilakukan secara rutin sangat membantu menjaga ketersediaan darah di PMI Kota Palu."],
            ['Mengenal Golongan Darah dan Rhesus', 'Pengertian Donor Darah',
             'Penjelasan singkat tentang golongan darah A, B, AB, O dan rhesus positif/negatif.',
             "Golongan darah manusia dikelompokkan menjadi A, B, AB, dan O. Selain itu terdapat faktor rhesus yang dibedakan menjadi positif (+) dan negatif (-), sehingga ada delapan kelompok: A+, A-, B+, B-, AB+, AB-, O+, dan O-.\n\nKecocokan golongan darah antara pendonor dan penerima sangat penting. Pemeriksaan golongan dan uji kecocokan darah selalu dilakukan oleh tenaga kesehatan atau unit transfusi darah sebelum transfusi. Informasi pada aplikasi NORA hanya bersifat informatif dan tidak menggantikan pemeriksaan tersebut."],
            ['Persyaratan Umum Menjadi Pendonor', 'Persyaratan Donor',
             'Syarat kesehatan yang umumnya harus dipenuhi calon pendonor.',
             "Persyaratan berikut bersifat umum dan dapat berbeda sesuai ketentuan PMI serta kondisi kesehatan Anda saat pemeriksaan:\n\n1. Sehat jasmani dan rohani pada hari donor.\n2. Berusia 17 sampai 60 tahun (usia 17 tahun memerlukan persetujuan orang tua).\n3. Berat badan minimal 45 kg.\n4. Tekanan darah dan kadar hemoglobin berada dalam rentang yang ditentukan petugas.\n5. Jarak donor sebelumnya umumnya minimal 8 minggu (sekitar 2 bulan).\n6. Tidak sedang hamil, menyusui, atau dalam kondisi/obat tertentu yang ditentukan petugas.\n\nKeputusan akhir mengenai kelayakan donor selalu ditentukan oleh tenaga kesehatan yang memeriksa Anda."],
            ['Persiapan Sebelum Donor', 'Persiapan Sebelum Donor',
             'Hal-hal yang sebaiknya dilakukan sebelum datang ke lokasi donor.',
             "Agar proses donor berjalan lancar, sebaiknya Anda:\n\n- Tidur cukup pada malam sebelum donor.\n- Makan dengan teratur dan jangan datang dalam kondisi perut kosong.\n- Minum air putih yang cukup.\n- Menghindari alkohol dan merokok sebelum donor.\n- Membawa identitas diri (misalnya KTP) dan kartu donor bila ada.\n- Mengenakan pakaian yang nyaman dengan lengan yang mudah digulung.\n\nBila sedang tidak enak badan, tundalah donor dan sampaikan kondisi Anda kepada petugas."],
            ['Alur Proses Donor Darah', 'Proses Donor',
             'Tahapan donor darah dari pendaftaran hingga selesai.',
             "Secara umum, proses donor darah berlangsung sebagai berikut:\n\n1. Pendaftaran dan pengisian formulir data diri serta riwayat kesehatan.\n2. Pemeriksaan awal oleh petugas, misalnya tekanan darah, kadar hemoglobin, dan golongan darah.\n3. Wawancara singkat mengenai kondisi kesehatan.\n4. Pengambilan darah oleh petugas terlatih, umumnya sekitar 350 - 450 ml dan memakan waktu kurang lebih 10 - 15 menit.\n5. Istirahat sejenak dan mendapatkan makanan/minuman ringan.\n\nSeluruh peralatan yang digunakan bersifat steril dan sekali pakai."],
            ['Perawatan Setelah Donor', 'Perawatan Setelah Donor',
             'Hal yang perlu diperhatikan setelah mendonorkan darah.',
             "Setelah mendonor, sebaiknya Anda:\n\n- Beristirahat beberapa saat di lokasi donor sebelum pulang.\n- Memperbanyak minum air putih dan mengonsumsi makanan bergizi.\n- Menghindari aktivitas fisik yang berat selama sisa hari tersebut.\n- Menjaga bekas tusukan tetap bersih dan tidak membuka plester terlalu cepat.\n\nJika Anda merasa pusing berkepanjangan, mual, atau ada pembengkakan di bekas tusukan, segera hubungi petugas kesehatan."],
        ];
        foreach ($edukasi as [$judul, $kategori, $ringkasan, $isi]) {
            $db->table('edukasi')->insert([
                'judul' => $judul, 'kategori' => $kategori, 'ringkasan' => $ringkasan, 'isi' => $isi,
                'id_admin' => $id['admin@nora.id'], 'tanggal_dibuat' => $now('-20 days'),
            ]);
        }
    }
}
