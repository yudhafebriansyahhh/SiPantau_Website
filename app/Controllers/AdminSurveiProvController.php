<?php
namespace App\Controllers;

use App\Models\KurvaSProvinsiModel;
use App\Models\MasterKegiatanDetailProsesModel;
use CodeIgniter\Controller;
use DateTime;
use DateInterval;
use DatePeriod;

class AdminSurveiProvController extends Controller
{
    public function index()
    {
        $prosesModel = new MasterKegiatanDetailProsesModel();

        // Ambil semua kegiatan untuk dropdown
        $kegiatanList = $prosesModel
            ->select('id_kegiatan_detail_proses, nama_kegiatan_detail_proses')
            ->orderBy('id_kegiatan_detail_proses', 'DESC')
            ->findAll();

        // Ambil kegiatan detail terbaru untuk default chart
        $latest = $prosesModel
            ->orderBy('id_kegiatan_detail_proses', 'DESC')
            ->first();
        $latestKegiatanId = $latest ? $latest['id_kegiatan_detail_proses'] : '';

        $data = [
            'title' => 'Dashboard',
            'active_menu' => 'dashboard',
            'kegiatanList' => $kegiatanList,
            'latestKegiatanId' => $latestKegiatanId
        ];

        return view('AdminSurveiProv/dashboard', $data);
    }

    public function getKurvaProvinsi()
    {
        $idProses = $this->request->getGet('id_kegiatan_detail_proses');
        $model = new KurvaSProvinsiModel();

        $builder = $model
            ->select('id_kegiatan_detail_proses, tanggal_target, target_persen_kumulatif, target_kumulatif_absolut, target_harian_absolut')
            ->orderBy('tanggal_target', 'ASC');

        if ($idProses) {
            $builder->where('id_kegiatan_detail_proses', $idProses);
        } else {
            // Jika tidak ada filter, gunakan kegiatan terbaru
            $latest = (new MasterKegiatanDetailProsesModel())
                ->orderBy('id_kegiatan_detail_proses', 'DESC')
                ->first();

            if ($latest) {
                $builder->where('id_kegiatan_detail_proses', $latest['id_kegiatan_detail_proses']);
            }
        }

        $records = $builder->findAll();

        if (empty($records)) {
            return $this->response->setJSON([
                'labels' => [],
                'targetPersen' => [],
                'targetAbsolut' => [],
                'targetHarian' => []
            ]);
        }

        $labels = [];
        $targetPersen = [];
        $targetAbsolut = [];
        $targetHarian = [];

        foreach ($records as $row) {
            $labels[] = date('d M', strtotime($row['tanggal_target']));
            $targetPersen[] = (float) $row['target_persen_kumulatif'];
            $targetAbsolut[] = (int) $row['target_kumulatif_absolut'];
            $targetHarian[] = (int) $row['target_harian_absolut'];
        }

        // Pastikan nilai kumulatif tidak menurun
        for ($i = 1; $i < count($targetAbsolut); $i++) {
            if ($targetAbsolut[$i] < $targetAbsolut[$i - 1]) {
                $targetAbsolut[$i] = $targetAbsolut[$i - 1];
            }
        }

        return $this->response->setJSON([
            'labels' => $labels,
            'targetPersen' => $targetPersen,
            'targetAbsolut' => $targetAbsolut,
            'targetHarian' => $targetHarian
        ]);
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