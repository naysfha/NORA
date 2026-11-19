<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Pembatasan hak akses berdasarkan peran (role-based access).
 * Pemakaian di rute: ['filter' => 'role:admin'] atau 'role:pendonor,pencari'.
 */
class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('logged_in')) {
            return redirect()->to(base_url('login'))
                ->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $roleDiizinkan = $arguments ?? [];

        if (! in_array(session()->get('role'), $roleDiizinkan, true)) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada proses tambahan.
    }
}
