<?php
namespace App\Controllers;

use App\Models\KurvaSProvinsiModel;
use App\Models\KurvaSkabModel;
use App\Models\MasterKegiatanDetailProsesModel;
use App\Models\MasterKegiatanWilayahModel;
use CodeIgniter\Controller;

class AdminSurveiProvController extends Controller
{
    public function index()
    {
        $prosesModel = new MasterKegiatanDetailProsesModel();

        // Dropdown kegiatan proses
        $kegiatanList = $prosesModel
            ->select('id_kegiatan_detail_proses, nama_kegiatan_detail_proses')
            ->orderBy('id_kegiatan_detail_proses', 'DESC')
            ->findAll();

        $latest = $prosesModel->orderBy('id_kegiatan_detail_proses', 'DESC')->first();
        $latestKegiatanId = $latest ? $latest['id_kegiatan_detail_proses'] : '';

        $data = [
            'title' => 'Dashboard',
            'active_menu' => 'dashboard',
            'kegiatanList' => $kegiatanList,
            'latestKegiatanId' => $latestKegiatanId
        ];

        return view('AdminSurveiProv/dashboard', $data);
    }

    // ======================================================
    // KURVA S PROVINSI
    // ======================================================
    public function getKurvaProvinsi()
    {
        $idProses = $this->request->getGet('id_kegiatan_detail_proses');
        $model = new KurvaSProvinsiModel();

        $builder = $model
            ->select('tanggal_target, target_persen_kumulatif, target_kumulatif_absolut, target_harian_absolut')
            ->orderBy('tanggal_target', 'ASC');

        if ($idProses) {
            $builder->where('id_kegiatan_detail_proses', $idProses);
        } else {
            $latest = (new MasterKegiatanDetailProsesModel())->orderBy('id_kegiatan_detail_proses', 'DESC')->first();
            if ($latest) $builder->where('id_kegiatan_detail_proses', $latest['id_kegiatan_detail_proses']);
        }

        $records = $builder->findAll();
        return $this->response->setJSON($this->formatKurvaData($records));
    }

    // ======================================================
    // 🔹 Kegiatan Wilayah Dropdown (kabupaten)
    // ======================================================
    public function getKegiatanWilayah()
    {
        $idProses = $this->request->getGet('id_kegiatan_detail_proses');
        $wilayahModel = new MasterKegiatanWilayahModel();

        $records = $wilayahModel
            ->select('kegiatan_wilayah.id_kegiatan_wilayah, master_kabupaten.nama_kabupaten')
            ->join('master_kabupaten', 'master_kabupaten.id_kabupaten = kegiatan_wilayah.id_kabupaten', 'left')
            ->where('kegiatan_wilayah.id_kegiatan_detail_proses', $idProses)
            ->findAll();

        return $this->response->setJSON($records);
    }

    // ======================================================
    // 🔹 Kurva S Kabupaten (berdasarkan kegiatan_wilayah)
    // ======================================================
    public function getKurvaKabupaten()
    {
        $idWilayah = $this->request->getGet('id_kegiatan_wilayah');
        $model = new KurvaSkabModel();

        $records = $model
            ->where('id_kegiatan_wilayah', $idWilayah)
            ->orderBy('tanggal_target', 'ASC')
            ->findAll();

        return $this->response->setJSON($this->formatKurvaData($records));
    }

    // ======================================================
    // 🔹 Helper: format JSON kurva
    // ======================================================
    private function formatKurvaData($records)
{
    if (empty($records)) {
        return [
            'labels' => [],
            'targetPersen' => [],
            'targetAbsolut' => [],
            'targetHarian' => []
        ];
    }

    // 🔹 filter duplikat berdasarkan tanggal_target
    $unique = [];
    foreach ($records as $row) {
        $tgl = $row['tanggal_target'];
        if (!isset($unique[$tgl])) {
            $unique[$tgl] = $row;
        }
    }

    // urutkan berdasarkan tanggal (pastikan rapi)
    ksort($unique);

    $labels = $targetPersen = $targetAbsolut = $targetHarian = [];
    foreach ($unique as $row) {
        $labels[] = date('d M', strtotime($row['tanggal_target']));
        $targetPersen[] = (float) $row['target_persen_kumulatif'];
        $targetAbsolut[] = (int) $row['target_kumulatif_absolut'];
        $targetHarian[] = (int) $row['target_harian_absolut'];
    }

    // 🔸 pastikan target kumulatif tidak menurun
    for ($i = 1; $i < count($targetAbsolut); $i++) {
        if ($targetAbsolut[$i] < $targetAbsolut[$i - 1]) {
            $targetAbsolut[$i] = $targetAbsolut[$i - 1];
        }
    }

    return [
        'labels' => array_values($labels),
        'targetPersen' => array_values($targetPersen),
        'targetAbsolut' => array_values($targetAbsolut),
        'targetHarian' => array_values($targetHarian)
    ];
}







    public function master_detail_proses()
    {
        $data = [
            'title' => 'Master Detail Proses',
            'active_menu' => 'master-kegiatan-detail-proses'
        ];
        return view('AdminSurveiProv/MasterKegiatanDetailProses/index', $data);
    }

    public function tambah_detail_proses()
    {
        $data = [
            'title' => 'Master Detail Proses',
            'active_menu' => 'master-kegiatan-detail-proses'
        ];
        return view('AdminSurveiProv/MasterKegiatanDetailProses/create', $data);
    }

    public function edit_master_output()
    {
        $data = [
            'title' => 'Master Output',
            'active_menu' => 'master-output'
        ];
        return view('AdminSurveiProv/Master Output/edit', $data);
    }

    public function master_kegiatan_wilayah()
    {
        $data = [
            'title' => 'Master Kegiatan Wilayah',
            'active_menu' => 'master-kegiatan-wilayah'
        ];
        return view('AdminSurveiProv/MasterKegiatanWilayah/index', $data);
    }

    public function tambah_master_kegiatan_wilayah()
    {
         $data = [
            'title' => 'Master Kegiatan Wilayah',
            'active_menu' => 'master-kegiatan-wilayah'
        ];
        return view('AdminSurveiProv/MasterKegiatanWilayah/create', $data);
    }

    public function edit_master_kegiatan()
    {
         $data = [
            'title' => 'Master Kegiatan',
            'active_menu' => 'master-kegiatan'
        ];
        return view('SuperAdmin/Master Kegiatan/edit', $data);
    }

    public function AssignAdminSurveiKab()
    {
        $data = [
            'title' => 'Assign Petugas Survei',
            'active_menu' => 'assign-admin-kab'
        ];
        return view('AdminSurveiProv/AssignAdminSurveiKab/index', $data);
    }

    public function tambah_AssignAdminSurveiKab()
    {
        $data = [
            'title' => 'Assign Petugas Survei',
            'active_menu' => 'assign-admin-kab/create'
        ];
        return view('AdminSurveiProv/AssignAdminSurveiKab/create', $data);
    }
}