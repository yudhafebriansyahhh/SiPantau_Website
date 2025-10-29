<?php

namespace App\Controllers\Pemantau;

use App\Controllers\BaseController;
use App\Models\MasterKegiatanDetailModel;
use App\Models\MasterKegiatanDetailProsesModel;
use CodeIgniter\HTTP\ResponseInterface;


class MasterKegiatanDetailProses extends BaseController
{
    protected $masterDetailProsesModel;
    protected $masterDetailModel;
    protected $validation;

    public function __construct()
    {
        $this->masterDetailProsesModel= new MasterKegiatanDetailProsesModel();
        $this->masterDetailModel = new MasterKegiatanDetailModel();
        $this->validation= \config\Services::validation();
    }
 

    public function index()
    {
        $kegiatanDetails = $this->masterDetailProsesModel->getData();

        $data =[
            'title'           => 'Kelola Master Kegiatan Detail Proses',
            'active_menu'     => 'detail-proses',
            'kegiatanDetails' => $kegiatanDetails,
        ];

        return view('Pemantau/DetailProses/index', $data);
    }
}
