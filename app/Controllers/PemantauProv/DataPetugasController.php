<?php

namespace App\Controllers\PemantauProv;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\MasterKabModel;

class DataPetugasController extends BaseController
{
    protected $userModel;
    protected $masterKab;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->masterKab = new MasterKabModel();
    }

    public function index()
    {
        // Ambil parameter filter dari GET
        $kabupatenId = $this->request->getGet('kabupaten');
        $search = $this->request->getGet('search');
        
        // Ambil perPage dari GET, default 10
        $perPage = $this->request->getGet('perPage') ?? 10;
        
        // Validasi perPage agar hanya nilai yang diizinkan
        $allowedPerPage = [5, 10, 25, 50, 100];
        if (!in_array((int)$perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        // Ambil semua kabupaten untuk dropdown
        $kabupatenList = $this->masterKab->orderBy('id_kabupaten', 'ASC')->findAll();

        // Get data petugas (user yang pernah jadi PML atau PCL)
        $dataPetugas = $this->userModel->getUsersWithPetugasHistory($kabupatenId, $search, $perPage);

        $data = [
            'title' => 'Pemantau - Data Petugas',
            'active_menu' => 'data-petugas',
            'dataPetugas' => $dataPetugas['data'],
            'kabupatenList' => $kabupatenList,
            'selectedKabupaten' => $kabupatenId,
            'search' => $search,
            'perPage' => $perPage,
            'pager' => $dataPetugas['pager'],
            'totalData' => $dataPetugas['total']
        ];

        return view('PemantauProvinsi/DataPetugas/index', $data);
    }
}