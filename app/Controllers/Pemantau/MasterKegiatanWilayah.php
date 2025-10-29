<?php

namespace App\Controllers\Pemantau;

use App\Controllers\BaseController;
use App\Models\MasterKabModel;
use App\Models\MasterKegiatanDetailProsesModel;
use App\Models\MasterKegiatanWilayahModel;
use CodeIgniter\HTTP\ResponseInterface;

class MasterKegiatanWilayah extends BaseController
{

    protected $masterDetailProses;
    protected $masterKegiatanWilayah;
    protected $masterKab;

    public function __construct()
    {
        $this->masterDetailProses = new MasterKegiatanDetailProsesModel();
        $this->masterKegiatanWilayah = new MasterKegiatanWilayahModel();
        $this->masterKab = new MasterKabModel();
    }

    public function index()
    {

        $KegiatanWilayah = $this->masterKegiatanWilayah->getData();

        $data = [
            'title' => 'Pemantau - Kegiatan Wilayah',
            'active_menu' => 'kegiatan-wilayah-pemantau',
            'kegiatanWilayah' => $KegiatanWilayah,
        ];

        return view('Pemantau/KegiatanWilayah/index', $data);
    }
}
