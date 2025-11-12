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
        $this->masterDetailProsesModel = new MasterKegiatanDetailProsesModel();
        $this->masterDetailModel = new MasterKegiatanDetailModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $kegiatanDetailFilter = $this->request->getGet('kegiatan_detail');
        $perPage = 3; // Jumlah data per halaman
        
        // Gunakan query builder dari model
        $builder = $this->masterDetailProsesModel
            ->select('master_kegiatan_detail_proses.*, master_kegiatan_detail.nama_kegiatan_detail')
            ->join('master_kegiatan_detail', 'master_kegiatan_detail.id_kegiatan_detail = master_kegiatan_detail_proses.id_kegiatan_detail');
        
        // Filter jika ada
        if ($kegiatanDetailFilter) {
            $builder->where('master_kegiatan_detail_proses.id_kegiatan_detail', $kegiatanDetailFilter);
        }

        $data = [
            'title'                  => 'Kelola Master Kegiatan Detail Proses',
            'active_menu'            => 'detail-proses',
            'kegiatanDetails'        => $builder->paginate($perPage, 'default'),
            'pager'                  => $builder->pager,
            'kegiatanDetailList'     => $this->masterDetailModel->findAll(),
            'selectedKegiatanDetail' => $kegiatanDetailFilter
        ];

        return view('Pemantau/DetailProses/index', $data);
    }
}