<?php

namespace App\Controllers\Pemantau;

use App\Controllers\BaseController;
use App\Models\MasterKabModel;
use App\Models\MasterKegiatanWilayahModel;

class MasterKegiatanWilayah extends BaseController
{
    protected $masterKegiatanWilayah;
    protected $masterKab;

    public function __construct()
    {
        $this->masterKegiatanWilayah = new MasterKegiatanWilayahModel();
        $this->masterKab = new MasterKabModel();
    }

    public function index()
    {
        // Ambil parameter filter dari GET
        $kabupatenId = $this->request->getGet('kabupaten');

        // Ambil semua kabupaten untuk dropdown
        $kabupatenList = $this->masterKab->orderBy('nama_kabupaten', 'ASC')->findAll();

        // Pagination setup
        $perPage = 3; // jumlah data per halaman
        $page = $this->request->getVar('page') ?? 1;

        // Query dengan pagination
        $query = $this->masterKegiatanWilayah
            ->getAllWithDetailsQuery($kabupatenId)
            ->paginate($perPage, 'kegiatanWilayah');

        $data = [
            'title' => 'Pemantau - Kegiatan Wilayah',
            'active_menu' => 'kegiatan-wilayah-pemantau',
            'kegiatanWilayah' => $query,
            'kabupatenList' => $kabupatenList,
            'selectedKabupaten' => $kabupatenId,
            'pager' => $this->masterKegiatanWilayah->pager,
        ];

        return view('Pemantau/KegiatanWilayah/index', $data);
    }
}
