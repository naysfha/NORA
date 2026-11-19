<?php

namespace App\Controllers;

use App\Models\FasilitasModel;
use App\Models\PendonorModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    /**
     * Helper yang otomatis dimuat di semua controller dan view.
     * 'nora' = helper buatan sendiri (tgl_indo, badge_status, wa_link, dll).
     */
    protected $helpers = ['url', 'form', 'nora'];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    protected function idUser(): int
    {
        return (int) session()->get('id_user');
    }

    protected function role(): string
    {
        return (string) session()->get('role');
    }

    /** Profil pendonor milik pengguna yang sedang masuk. */
    protected function pendonorSaya(): ?array
    {
        return (new PendonorModel())->dariUser($this->idUser());
    }

    /** Data fasilitas milik pengguna yang sedang masuk. */
    protected function fasilitasSaya(): ?array
    {
        return (new FasilitasModel())->dariUser($this->idUser());
    }
}
