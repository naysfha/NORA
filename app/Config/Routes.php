<?php

use CodeIgniter\Router\RouteCollection;

/**
 * NORA - Sistem Informasi Donor Darah Terintegrasi (PMI Kota Palu)
 *
 * @var RouteCollection $routes
 */

// ---------- Publik ----------
$routes->get('/', 'Beranda::index');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::attemptRegister');
$routes->get('logout', 'Auth::logout');

// ---------- Semua pengguna yang sudah masuk ----------
$auth = ['filter' => 'auth'];

$routes->get('dashboard', 'Dashboard::index', $auth);

$routes->get('profil', 'Profil::index', $auth);
$routes->post('profil/akun', 'Profil::akun', $auth);
$routes->post('profil/password', 'Profil::password', $auth);
$routes->post('profil/pendonor', 'Profil::pendonor', ['filter' => 'role:pendonor']);
$routes->post('profil/fasilitas', 'Profil::fasilitas', ['filter' => 'role:fasilitas']);

$routes->get('edukasi', 'Edukasi::index', $auth);
$routes->get('edukasi/(:num)', 'Edukasi::show/$1', $auth);

$routes->get('stok', 'Stok::index', $auth);
$routes->get('lokasi', 'Lokasi::index', $auth);
$routes->get('jadwal', 'Jadwal::index', $auth);

$routes->get('notifikasi', 'Notifikasi::index', $auth);
$routes->get('notifikasi/buka/(:num)', 'Notifikasi::buka/$1', $auth);
$routes->post('notifikasi/baca-semua', 'Notifikasi::bacaSemua', $auth);

// ---------- Fasilitas kesehatan / unit donor ----------
$fasilitas = ['filter' => 'role:fasilitas'];

$routes->get('stok/kelola', 'Stok::kelola', $fasilitas);
$routes->post('stok/simpan', 'Stok::simpan', $fasilitas);
$routes->get('jadwal/saya', 'Jadwal::saya', $fasilitas);
$routes->get('jadwal/ajukan', 'Jadwal::ajukan', $fasilitas);
$routes->post('jadwal/simpan', 'Jadwal::simpan', $fasilitas);

// ---------- Pencari darah / penerima ----------
$pencari = ['filter' => 'role:pencari'];

$routes->get('sos/saya', 'Sos::saya', $pencari);
$routes->get('sos/buat', 'Sos::buat', $pencari);
$routes->post('sos/simpan', 'Sos::simpan', $pencari);

// ---------- Pendonor ----------
$pendonor = ['filter' => 'role:pendonor'];

$routes->get('sos', 'Sos::index', $pendonor);
$routes->post('sos/respons/(:num)', 'Sos::respons/$1', $pendonor);
$routes->post('sos/batal-respons/(:num)', 'Sos::batalRespons/$1', $pendonor);

// Detail SOS: pencari (pemilik) dan pendonor
$routes->get('sos/detail/(:num)', 'Sos::detail/$1', ['filter' => 'role:pencari,pendonor']);

// ---------- Panel Admin ----------
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'role:admin'], static function ($routes) {
    $routes->get('pengguna', 'Pengguna::index');
    $routes->get('pengguna/create', 'Pengguna::create');
    $routes->post('pengguna/store', 'Pengguna::store');
    $routes->get('pengguna/edit/(:num)', 'Pengguna::edit/$1');
    $routes->post('pengguna/update/(:num)', 'Pengguna::update/$1');
    $routes->post('pengguna/delete/(:num)', 'Pengguna::delete/$1');

    $routes->get('pendonor', 'Pendonor::index');
    $routes->post('pendonor/verifikasi/(:num)', 'Pendonor::verifikasi/$1');

    $routes->get('fasilitas', 'Fasilitas::index');
    $routes->get('fasilitas/create', 'Fasilitas::create');
    $routes->post('fasilitas/store', 'Fasilitas::store');
    $routes->get('fasilitas/edit/(:num)', 'Fasilitas::edit/$1');
    $routes->post('fasilitas/update/(:num)', 'Fasilitas::update/$1');
    $routes->post('fasilitas/verifikasi/(:num)', 'Fasilitas::verifikasi/$1');
    $routes->post('fasilitas/delete/(:num)', 'Fasilitas::delete/$1');

    $routes->get('stok', 'Stok::index');
    $routes->get('stok/create', 'Stok::create');
    $routes->post('stok/store', 'Stok::store');
    $routes->post('stok/verifikasi/(:num)', 'Stok::verifikasi/$1');
    $routes->post('stok/delete/(:num)', 'Stok::delete/$1');

    $routes->get('jadwal', 'Jadwal::index');
    $routes->get('jadwal/create', 'Jadwal::create');
    $routes->post('jadwal/store', 'Jadwal::store');
    $routes->get('jadwal/edit/(:num)', 'Jadwal::edit/$1');
    $routes->post('jadwal/update/(:num)', 'Jadwal::update/$1');
    $routes->post('jadwal/status/(:num)', 'Jadwal::status/$1');
    $routes->post('jadwal/delete/(:num)', 'Jadwal::delete/$1');

    $routes->get('edukasi', 'Edukasi::index');
    $routes->get('edukasi/create', 'Edukasi::create');
    $routes->post('edukasi/store', 'Edukasi::store');
    $routes->get('edukasi/edit/(:num)', 'Edukasi::edit/$1');
    $routes->post('edukasi/update/(:num)', 'Edukasi::update/$1');
    $routes->post('edukasi/delete/(:num)', 'Edukasi::delete/$1');

    $routes->get('sos', 'Sos::index');
    $routes->get('sos/detail/(:num)', 'Sos::detail/$1');
    $routes->post('sos/status/(:num)', 'Sos::status/$1');
    $routes->post('sos/delete/(:num)', 'Sos::delete/$1');

    $routes->get('notifikasi', 'Notifikasi::index');
    $routes->post('notifikasi/kirim', 'Notifikasi::kirim');

    $routes->get('laporan', 'Laporan::index');
    $routes->post('laporan/buat', 'Laporan::buat');
    $routes->get('laporan/lihat/(:num)', 'Laporan::lihat/$1');
    $routes->post('laporan/delete/(:num)', 'Laporan::delete/$1');
});
