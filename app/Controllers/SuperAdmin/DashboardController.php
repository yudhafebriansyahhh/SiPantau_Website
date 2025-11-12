<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\MasterKegiatanDetailModel;
use App\Models\MasterKegiatanDetailProsesModel;
use App\Models\MasterKegiatanWilayahModel;
use App\Models\KurvaSProvinsiModel;
use App\Models\PantauProgressModel;
use App\Models\PCLModel;
use App\Models\PMLModel;

class DashboardController extends BaseController
{
    protected $masterKegiatanDetailModel;
    protected $masterKegiatanDetailProsesModel;
    protected $masterKegiatanWilayahModel;
    protected $kurvaSProvinsiModel;
    protected $pantauProgressModel;
    protected $pclModel;
    protected $pmlModel;
    protected $db;

    public function __construct()
    {
        $this->masterKegiatanDetailModel = new MasterKegiatanDetailModel();
        $this->masterKegiatanDetailProsesModel = new MasterKegiatanDetailProsesModel();
        $this->masterKegiatanWilayahModel = new MasterKegiatanWilayahModel();
        $this->kurvaSProvinsiModel = new KurvaSProvinsiModel();
        $this->pantauProgressModel = new PantauProgressModel();
        $this->pclModel = new PCLModel();
        $this->pmlModel = new PMLModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // Get statistik dashboard
        $stats = $this->getDashboardStats();
        
        // Get kegiatan detail proses untuk filter
        $kegiatanDetailProses = $this->masterKegiatanDetailProsesModel
            ->select('master_kegiatan_detail_proses.*, master_kegiatan_detail.nama_kegiatan_detail')
            ->join('master_kegiatan_detail', 'master_kegiatan_detail.id_kegiatan_detail = master_kegiatan_detail_proses.id_kegiatan_detail')
            ->orderBy('master_kegiatan_detail_proses.tanggal_mulai', 'DESC')
            ->findAll();

        // Get progress kegiatan yang sedang berjalan
        $progressKegiatan = $this->getProgressKegiatanBerjalan();

        $data = [
            'title' => 'Dashboard',
            'active_menu' => 'dashboard',
            'stats' => $stats,
            'kegiatanDetailProses' => $kegiatanDetailProses,
            'progressKegiatan' => $progressKegiatan
        ];

        return view('SuperAdmin/dashboard', $data);
    }

    private function getDashboardStats()
    {
        // Total Pengguna
        $totalPengguna = $this->db->table('sipantau_user')
            ->where('is_active', 1)
            ->countAllResults();

        // Total Kegiatan (dari master_kegiatan_detail)
        $totalKegiatan = $this->masterKegiatanDetailModel->countAll();

        // Kegiatan Aktif (kegiatan yang sedang berjalan berdasarkan tanggal)
        $today = date('Y-m-d');
        $kegiatanAktif = $this->db->table('master_kegiatan_detail_proses')
            ->where('tanggal_mulai <=', $today)
            ->where('tanggal_selesai >=', $today)
            ->countAllResults();

        // Target Tercapai (rata-rata persentase dari semua kegiatan detail)
        $targetTercapai = $this->calculateOverallProgress();

        return [
            'total_pengguna' => $totalPengguna,
            'total_kegiatan' => $totalKegiatan,
            'kegiatan_aktif' => $kegiatanAktif,
            'target_tercapai' => round($targetTercapai, 0)
        ];
    }

    private function calculateOverallProgress()
    {
        $kegiatanDetails = $this->masterKegiatanDetailModel->findAll();
        
        if (empty($kegiatanDetails)) {
            return 0;
        }

        $totalProgress = 0;
        $countKegiatan = 0;

        foreach ($kegiatanDetails as $detail) {
            // Get semua proses dari kegiatan detail ini
            $prosesList = $this->masterKegiatanDetailProsesModel
                ->where('id_kegiatan_detail', $detail['id_kegiatan_detail'])
                ->findAll();

            foreach ($prosesList as $proses) {
                $targetTotal = (int)$proses['target'];
                
                if ($targetTotal > 0) {
                    $realisasiTotal = $this->getRealisasiByProses($proses['id_kegiatan_detail_proses']);
                    $progress = ($realisasiTotal / $targetTotal) * 100;
                    $totalProgress += min(100, $progress);
                    $countKegiatan++;
                }
            }
        }

        return $countKegiatan > 0 ? ($totalProgress / $countKegiatan) : 0;
    }

    private function getRealisasiByProses($idProses)
    {
        $result = $this->db->query("
            SELECT COALESCE(SUM(pp.jumlah_realisasi_kumulatif), 0) as total_realisasi
            FROM pantau_progress pp
            JOIN pcl ON pp.id_pcl = pcl.id_pcl
            JOIN pml ON pcl.id_pml = pml.id_pml
            JOIN kegiatan_wilayah kw ON pml.id_kegiatan_wilayah = kw.id_kegiatan_wilayah
            WHERE kw.id_kegiatan_detail_proses = ?
        ", [$idProses])->getRowArray();

        return (int)($result['total_realisasi'] ?? 0);
    }

    private function getProgressKegiatanBerjalan()
    {
        $kegiatanDetails = $this->masterKegiatanDetailModel
            ->select('master_kegiatan_detail.*, master_kegiatan.nama_kegiatan')
            ->join('master_kegiatan', 'master_kegiatan.id_kegiatan = master_kegiatan_detail.id_kegiatan')
            ->orderBy('master_kegiatan_detail.created_at', 'DESC')
            ->findAll();

        $progressData = [];
        $colors = ['#1e88e5', '#43a047', '#fdd835', '#8e24aa', '#e53935', '#5e35b1'];
        $colorIndex = 0;

        foreach ($kegiatanDetails as $detail) {
            // Get total target dan realisasi dari semua proses
            $prosesList = $this->masterKegiatanDetailProsesModel
                ->where('id_kegiatan_detail', $detail['id_kegiatan_detail'])
                ->findAll();

            $totalTarget = 0;
            $totalRealisasi = 0;

            foreach ($prosesList as $proses) {
                $totalTarget += (int)$proses['target'];
                $totalRealisasi += $this->getRealisasiByProses($proses['id_kegiatan_detail_proses']);
            }

            if ($totalTarget > 0) {
                $progress = ($totalRealisasi / $totalTarget) * 100;
                
                $progressData[] = [
                    'nama' => $detail['nama_kegiatan_detail'],
                    'progress' => min(100, round($progress, 0)),
                    'color' => $colors[$colorIndex % count($colors)]
                ];
                
                $colorIndex++;
            }
        }

        return $progressData;
    }

    // AJAX endpoint untuk get kurva S
    public function getKurvaS()
    {
        $idProses = $this->request->getGet('id_kegiatan_detail_proses');
        $idWilayah = $this->request->getGet('id_kegiatan_wilayah');

        if (!$idProses) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID Proses tidak valid'
            ]);
        }

        // Get detail proses
        $detailProses = $this->masterKegiatanDetailProsesModel->find($idProses);
        
        if (!$detailProses) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data proses tidak ditemukan'
            ]);
        }

        // Get data kurva target
        $kurvaTarget = $this->kurvaSProvinsiModel
            ->where('id_kegiatan_detail_proses', $idProses)
            ->orderBy('tanggal_target', 'ASC')
            ->findAll();

        // Get data realisasi
        $realisasiData = $this->getRealisasiData($idProses, $idWilayah);

        // Format data untuk chart
        $chartData = $this->formatKurvaData($kurvaTarget, $realisasiData, $detailProses);

        return $this->response->setJSON([
            'success' => true,
            'data' => $chartData
        ]);
    }

    private function getRealisasiData($idProses, $idWilayah = null)
    {
        $builder = $this->db->table('pantau_progress pp')
            ->select('DATE(pp.created_at) as tanggal, SUM(pp.jumlah_realisasi_kumulatif) as total_realisasi')
            ->join('pcl', 'pp.id_pcl = pcl.id_pcl')
            ->join('pml', 'pcl.id_pml = pml.id_pml')
            ->join('kegiatan_wilayah kw', 'pml.id_kegiatan_wilayah = kw.id_kegiatan_wilayah')
            ->where('kw.id_kegiatan_detail_proses', $idProses);

        if ($idWilayah && $idWilayah != 'all') {
            $builder->where('kw.id_kegiatan_wilayah', $idWilayah);
        }

        $builder->groupBy('DATE(pp.created_at)')
                ->orderBy('DATE(pp.created_at)', 'ASC');

        return $builder->get()->getResultArray();
    }

    private function formatKurvaData($kurvaTarget, $realisasiData, $detailProses)
    {
        $labels = [];
        $targetData = [];
        $realisasiDataFormatted = [];

        // Build realisasi lookup
        $realisasiLookup = [];
        foreach ($realisasiData as $item) {
            $realisasiLookup[$item['tanggal']] = (int)$item['total_realisasi'];
        }

        // Build chart data
        $realisasiKumulatif = 0;
        foreach ($kurvaTarget as $item) {
            $tanggal = $item['tanggal_target'];
            $labels[] = date('d M', strtotime($tanggal));
            $targetData[] = (int)$item['target_kumulatif_absolut'];

            // Add realisasi for this date
            if (isset($realisasiLookup[$tanggal])) {
                $realisasiKumulatif += $realisasiLookup[$tanggal];
            }
            $realisasiDataFormatted[] = $realisasiKumulatif;
        }

        // Calculate stats
        $totalTarget = !empty($targetData) ? end($targetData) : 0;
        $totalRealisasi = !empty($realisasiDataFormatted) ? end($realisasiDataFormatted) : 0;
        $persentase = $totalTarget > 0 ? round(($totalRealisasi / $totalTarget) * 100, 1) : 0;
        $selisih = $totalRealisasi - $totalTarget;

        return [
            'labels' => $labels,
            'target' => $targetData,
            'realisasi' => $realisasiDataFormatted,
            'stats' => [
                'target' => $totalTarget,
                'realisasi' => $totalRealisasi,
                'persentase' => $persentase,
                'selisih' => $selisih
            ],
            'config' => [
                'nama' => $detailProses['nama_kegiatan_detail_proses'],
                'tanggal_mulai' => date('d', strtotime($detailProses['tanggal_mulai'])),
                'tanggal_selesai' => date('d', strtotime($detailProses['tanggal_selesai']))
            ]
        ];
    }

    // AJAX endpoint untuk get wilayah by proses
    public function getKegiatanWilayah()
    {
        $idProses = $this->request->getGet('id_kegiatan_detail_proses');

        if (!$idProses) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID Proses tidak valid'
            ]);
        }

        $wilayah = $this->db->table('kegiatan_wilayah kw')
            ->select('kw.id_kegiatan_wilayah, mk.nama_kabupaten')
            ->join('master_kabupaten mk', 'mk.id_kabupaten = kw.id_kabupaten')
            ->where('kw.id_kegiatan_detail_proses', $idProses)
            ->orderBy('mk.nama_kabupaten', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'data' => $wilayah
        ]);
    }

    // AJAX endpoint untuk get petugas by kegiatan wilayah
    public function getPetugas()
    {
        $idWilayah = $this->request->getGet('id_kegiatan_wilayah');

        if (!$idWilayah || $idWilayah == 'all') {
            // Get all petugas from all wilayah in this proses
            $idProses = $this->request->getGet('id_kegiatan_detail_proses');
            
            $petugas = $this->db->query("
                SELECT 
                    u.nama_user,
                    u.sobat_id,
                    mk.nama_kabupaten,
                    pcl.target,
                    COALESCE(MAX(pp.jumlah_realisasi_kumulatif), 0) as realisasi,
                    'PCL' as role
                FROM pcl
                JOIN sipantau_user u ON pcl.sobat_id = u.sobat_id
                JOIN pml ON pcl.id_pml = pml.id_pml
                JOIN kegiatan_wilayah kw ON pml.id_kegiatan_wilayah = kw.id_kegiatan_wilayah
                JOIN master_kabupaten mk ON kw.id_kabupaten = mk.id_kabupaten
                LEFT JOIN pantau_progress pp ON pp.id_pcl = pcl.id_pcl
                WHERE kw.id_kegiatan_detail_proses = ?
                GROUP BY pcl.id_pcl, u.nama_user, u.sobat_id, mk.nama_kabupaten, pcl.target
                ORDER BY mk.nama_kabupaten, u.nama_user
            ", [$idProses])->getResultArray();
        } else {
            // Get petugas for specific wilayah
            $petugas = $this->db->query("
                SELECT 
                    u.nama_user,
                    u.sobat_id,
                    mk.nama_kabupaten,
                    pcl.target,
                    COALESCE(MAX(pp.jumlah_realisasi_kumulatif), 0) as realisasi,
                    'PCL' as role
                FROM pcl
                JOIN sipantau_user u ON pcl.sobat_id = u.sobat_id
                JOIN pml ON pcl.id_pml = pml.id_pml
                JOIN kegiatan_wilayah kw ON pml.id_kegiatan_wilayah = kw.id_kegiatan_wilayah
                JOIN master_kabupaten mk ON kw.id_kabupaten = mk.id_kabupaten
                LEFT JOIN pantau_progress pp ON pp.id_pcl = pcl.id_pcl
                WHERE kw.id_kegiatan_wilayah = ?
                GROUP BY pcl.id_pcl, u.nama_user, u.sobat_id, mk.nama_kabupaten, pcl.target
                ORDER BY u.nama_user
            ", [$idWilayah])->getResultArray();
        }

        // Calculate progress and status for each petugas
        foreach ($petugas as &$p) {
            $target = (int)$p['target'];
            $realisasi = (int)$p['realisasi'];
            $progress = $target > 0 ? round(($realisasi / $target) * 100, 0) : 0;
            
            $p['progress'] = min(100, $progress);
            
            // Determine status
            if ($realisasi >= $target) {
                $p['status'] = 'Sudah Lapor';
                $p['status_class'] = 'badge-success';
            } elseif ($progress >= 50) {
                $p['status'] = 'Sedang Berjalan';
                $p['status_class'] = 'badge-warning';
            } else {
                $p['status'] = 'Belum Lapor';
                $p['status_class'] = 'badge-warning';
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $petugas
        ]);
    }
}