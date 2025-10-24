<?php

namespace App\Controllers\AdminProv;

use App\Controllers\BaseController;
use App\Models\MasterKabModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterKegiatanDetailProsesModel;
use App\Models\MasterKegiatanWilayahModel;


class MasterKegiatanWilayahController extends BaseController
{
    protected $masterDetailProsesModel;
    protected $masterKegiatanWilayahModel;
    protected $masterKab;

    protected $validation;


    public function __construct() {
        $this->masterDetailProsesModel = new MasterKegiatanDetailProsesModel();
        $this->masterKegiatanWilayahModel = new MasterKegiatanWilayahModel();
        $this->validation = \Config\Services::validation();
        $this->masterKab = new MasterKabModel();
    }

    public function index()
    {
        $kegiatanWilayah = $this->masterKegiatanWilayahModel->getData();
        $data = [
            'title'           => 'Kelola Master Kegiatan Wilayah',
            'active_menu'     => 'master-kegiatan-wilayah',
            'kegiatanWilayah' => $kegiatanWilayah,
        ];
        return view('AdminSurveiProv/MasterKegiatanWilayah/index', $data);
    
    }

    public function create(){
        $data =[
            'title' => 'Tambah Kegiatan Wilayah',
            'active_menu' => 'master-kegiatan-wilayah',
            'kegiatanDetailProses' => $this->masterDetailProsesModel->findAll(),
            'validation' => $this->validation,
            'Kab' => $this->masterKab->findAll()
        ];

        return view ('AdminSurveiProv/MasterKegiatanWilayah/create', $data);
    }

}
 

