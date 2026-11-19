<?php

/**
 * Helper NORA: format tanggal Indonesia, label status, tautan WhatsApp/Maps, dll.
 * Dimuat otomatis lewat BaseController::$helpers.
 */

if (! function_exists('tgl_indo')) {
    function tgl_indo(?string $tanggal): string
    {
        if (! $tanggal) {
            return '-';
        }
        $bulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $t     = strtotime($tanggal);

        return date('d', $t) . ' ' . $bulan[(int) date('n', $t)] . ' ' . date('Y', $t);
    }
}

if (! function_exists('bulan_indo')) {
    function bulan_indo(int $nomor): string
    {
        $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        return $bulan[$nomor] ?? '';
    }
}

if (! function_exists('tgl_waktu')) {
    // 2026-11-17 14:30:00 -> 17 Nov 2026, 14:30
    function tgl_waktu(?string $waktu): string
    {
        return $waktu ? tgl_indo($waktu) . ', ' . date('H:i', strtotime($waktu)) : '-';
    }
}

if (! function_exists('waktu_lalu')) {
    function waktu_lalu(?string $waktu): string
    {
        if (! $waktu) {
            return '-';
        }
        $selisih = time() - strtotime($waktu);
        if ($selisih < 60) {
            return 'baru saja';
        }
        if ($selisih < 3600) {
            return floor($selisih / 60) . ' menit lalu';
        }
        if ($selisih < 86400) {
            return floor($selisih / 3600) . ' jam lalu';
        }
        if ($selisih < 86400 * 30) {
            return floor($selisih / 86400) . ' hari lalu';
        }

        return tgl_indo($waktu);
    }
}

if (! function_exists('hari_indo')) {
    function hari_indo(string $tanggal): string
    {
        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return $hari[(int) date('w', strtotime($tanggal))];
    }
}

if (! function_exists('badge_status')) {
    // "Menunggu Verifikasi" -> <span class="badge-status menunggu-verifikasi">
    function badge_status(string $status): string
    {
        $kelas = strtolower(str_replace(' ', '-', $status));

        return '<span class="badge-status ' . esc($kelas, 'attr') . '">' . esc($status) . '</span>';
    }
}

if (! function_exists('badge_urgensi')) {
    function badge_urgensi(string $urgensi): string
    {
        $kelas = strtolower(str_replace(' ', '-', $urgensi));

        return '<span class="badge-urgensi ' . esc($kelas, 'attr') . '"><i class="bi bi-lightning-charge-fill"></i> ' . esc($urgensi) . '</span>';
    }
}

if (! function_exists('chip_golongan')) {
    function chip_golongan(?string $golongan): string
    {
        if (! $golongan) {
            return '<span class="chip-gol kosong">?</span>';
        }

        return '<span class="chip-gol">' . esc($golongan) . '</span>';
    }
}

if (! function_exists('label_role')) {
    function label_role(?string $role): string
    {
        return \App\Libraries\Referensi::ROLE[$role] ?? ucfirst((string) $role);
    }
}

if (! function_exists('inisial')) {
    function inisial(?string $nama): string
    {
        $nama = trim((string) $nama);

        return $nama === '' ? '?' : mb_strtoupper(mb_substr($nama, 0, 1));
    }
}

if (! function_exists('normalisasi_telp')) {
    // "+62 812-3456-789" / "62812..." -> "0812..." ; kosong -> null
    function normalisasi_telp(?string $telp): ?string
    {
        $telp = trim((string) $telp);
        if ($telp === '') {
            return null;
        }
        $angka = preg_replace('/[^0-9+]/', '', $telp);
        if (str_starts_with($angka, '+62')) {
            $angka = '0' . substr($angka, 3);
        } elseif (str_starts_with($angka, '62')) {
            $angka = '0' . substr($angka, 2);
        }

        return $angka;
    }
}

if (! function_exists('wa_link')) {
    function wa_link(?string $telp): string
    {
        $angka = preg_replace('/[^0-9]/', '', (string) $telp);
        if (str_starts_with($angka, '0')) {
            $angka = '62' . substr($angka, 1);
        }

        return 'https://wa.me/' . $angka;
    }
}

if (! function_exists('maps_link')) {
    function maps_link(string $nama, ?string $alamat = null): string
    {
        return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode(trim($nama . ' ' . $alamat));
    }
}

if (! function_exists('potong')) {
    function potong(?string $teks, int $panjang = 120): string
    {
        $teks = trim(strip_tags((string) $teks));

        return mb_strlen($teks) > $panjang ? mb_substr($teks, 0, $panjang) . '…' : $teks;
    }
}
