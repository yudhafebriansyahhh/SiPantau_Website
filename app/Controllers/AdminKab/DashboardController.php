<?php

namespace App\Controllers\AdminKab;

use App\Controllers\BaseController;
use App\Models\AdminSurveiKabupatenModel;
use App\Models\MasterKegiatanDetailProsesModel;
use App\Models\MasterKegiatanWilayahModel;
use App\Models\KegiatanWilayahAdminModel;
use App\Models\KurvaSkabModel;
use App\Models\PantauProgressModel;
use App\Models\PCLModel;
use App\Models\PMLModel;

class DashboardController extends BaseController
{
    protected $adminKabModel;
    protected $prosesModel;
    protected $kegiatanWilayahModel;
    protected $kegiatanWilayahAdminModel;
    protected $kurvaKabModel;
    protected $pantauProgressModel;
    protected $pclModel;
    protected $pmlModel;
    protected $db;

    public function __construct()
    {
        $this->adminKabModel = new AdminSurveiKabupatenModel();
        $this->prosesModel = new MasterKegiatanDetailProsesModel();
        $this->kegiatanWilayahModel = new MasterKegiatanWilayahModel();
        $this->kegiatanWilayahAdminModel = new KegiatanWilayahAdminModel();
        $this->kurvaKabModel = new KurvaSkabModel();
        $this->pantauProgressModel = new PantauProgressModel();
        $this->pclModel = new PCLModel();
        $this->pmlModel = new PMLModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $sobatId = session()->get('sobat_id');
        
        if (!$sobatId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Get admin data
        $admin = $this->adminKabModel->db->table('admin_survei_kabupaten ask')
            ->select('ask.*, u.nama_user, u.id_kabupaten, k.nama_kabupaten')
            ->join('sipantau_user u', 'ask.sobat_id = u.sobat_id')
            ->join('master_kabupaten k', 'u.id_kabupaten = k.id_kabupaten')
            ->where('ask.sobat_id', $sobatId)
            ->get()
            ->getRowArray();

        if (!$admin) {
            return redirect()->to('/')->with('error', 'Anda tidak memiliki akses sebagai admin kabupaten');
        }

        $idKabupaten = $admin['id_kabupaten'];
        $idAdminKabupaten = $admin['id_admin_kabupaten'];

        // Get statistik dashboard
        $stats = $this->getDashboardStats($idKabupaten, $idAdminKabupaten);

        // Get kegiatan detail proses yang di-assign ke admin ini
        $kegiatanList = $this->getAssignedKegiatan($idKabupaten, $idAdminKabupaten);

        $latest = !empty($kegiatanList) ? $kegiatanList[0] : null;
        $latestKegiatanId = $latest ? $latest['id_kegiatan_detail_proses'] : '';

        // Get progress kegiatan yang sedang berjalan
        $progressKegiatan = $this->getProgressKegiatanBerjalan($idKabupaten, $idAdminKabupaten);

        $data = [
            'title' => 'Dashboard',
            'active_menu' => 'dashboard',
            'admin' => $admin,
            'stats' => $stats,
            'kegiatanList' => $kegiatanList,
            'latestKegiatanId' => $latestKegiatanId,
            'progressKegiatan' => $progressKegiatan,
            'kegiatanDetailProses' => $kegiatanList
        ];

        return view('AdminSurveiKab/dashboard', $data);
    }

    // Get Dashboard Stats
    private function getDashboardStats($idKabupaten, $idAdminKabupaten)
    {
        // Total Kegiatan (yang di-assign)
        $totalKegiatan = $this->db->query("
            SELECT COUNT(DISTINCT kw.id_kegiatan_detail_proses) as total
            FROM kegiatan_wilayah kw
            JOIN kegiatan_wilayah_admin kwa ON kwa.id_kegiatan_wilayah = kw.id_kegiatan_wilayah
            WHERE kw.id_kabupaten = ?
            AND kwa.id_admin_kabupaten = ?
        ", [$idKabupaten, $idAdminKabupaten])->getRowArray();

        // Kegiatan Aktif
        $today = date('Y-m-d');
        $kegiatanAktif = $this->db->query("
            SELECT COUNT(DISTINCT kdp.id_kegiatan_detail_proses) as total
            FROM master_kegiatan_detail_proses kdp
            JOIN kegiatan_wilayah kw ON kw.id_kegiatan_detail_proses = kdp.id_kegiatan_detail_proses
            JOIN kegiatan_wilayah_admin kwa ON kwa.id_kegiatan_wilayah = kw.id_kegiatan_wilayah
            WHERE kw.id_kabupaten = ?
            AND kwa.id_admin_kabupaten = ?
            AND kdp.tanggal_mulai <= ?
            AND kdp.tanggal_selesai >= ?
        ", [$idKabupaten, $idAdminKabupaten, $today, $today])->getRowArray();

        // Target Tercapai
        $targetTercapai = $this->calculateOverallProgress($idKabupaten, $idAdminKabupaten);

        return [
            'total_kegiatan' => (int)($totalKegiatan['total'] ?? 0),
            'kegiatan_aktif' => (int)($kegiatanAktif['total'] ?? 0),
            'target_tercapai' => round($targetTercapai, 0)
        ];
    }

    // Calculate Overall Progress
    private function calculateOverallProgress($idKabupaten, $idAdminKabupaten)
    {
        $kegiatanWilayah = $this->db->query("
            SELECT kw.id_kegiatan_wilayah, kw.target_wilayah
            FROM kegiatan_wilayah kw
            JOIN kegiatan_wilayah_admin kwa ON kwa.id_kegiatan_wilayah = kw.id_kegiatan_wilayah
            WHERE kw.id_kabupaten = ?
            AND kwa.id_admin_kabupaten = ?
            AND kw.target_wilayah > 0
        ", [$idKabupaten, $idAdminKabupaten])->getResultArray();

        if (empty($kegiatanWilayah)) {
            return 0;
        }

        $totalProgress = 0;
        $countKegiatan = 0;

        foreach ($kegiatanWilayah as $kegiatan) {
            $targetTotal = (int)$kegiatan['target_wilayah'];
            $realisasiTotal = $this->getRealisasiByKegiatanWilayah($kegiatan['id_kegiatan_wilayah']);
            
            if ($targetTotal > 0) {
                $progress = ($realisasiTotal / $targetTotal) * 100;
                $totalProgress += min(100, $progress);
                $countKegiatan++;
            }
        }

        return $countKegiatan > 0 ? ($totalProgress / $countKegiatan) : 0;
    }

    // Get Realisasi by Kegiatan Wilayah
    private function getRealisasiByKegiatanWilayah($idKegiatanWilayah)
    {
        $result = $this->db->query("
            SELECT COALESCE(SUM(pp.jumlah_realisasi_kumulatif), 0) as total_realisasi
            FROM pantau_progress pp
            JOIN pcl ON pp.id_pcl = pcl.id_pcl
            JOIN pml ON pcl.id_pml = pml.id_pml
            WHERE pml.id_kegiatan_wilayah = ?
        ", [$idKegiatanWilayah])->getRowArray();

        return (int)($result['total_realisasi'] ?? 0);
    }

    // Get Assigned Kegiatan
    private function getAssignedKegiatan($idKabupaten, $idAdminKabupaten)
    {
        return $this->db->query("
            SELECT DISTINCT kdp.id_kegiatan_detail_proses, kdp.nama_kegiatan_detail_proses
            FROM master_kegiatan_detail_proses kdp
            JOIN kegiatan_wilayah kw ON kw.id_kegiatan_detail_proses = kdp.id_kegiatan_detail_proses
            JOIN kegiatan_wilayah_admin kwa ON kwa.id_kegiatan_wilayah = kw.id_kegiatan_wilayah
            WHERE kw.id_kabupaten = ?
            AND kwa.id_admin_kabupaten = ?
            ORDER BY kdp.id_kegiatan_detail_proses DESC
        ", [$idKabupaten, $idAdminKabupaten])->getResultArray();
    }

    // Get Progress Kegiatan Berjalan - FIXED
    private function getProgressKegiatanBerjalan($idKabupaten, $idAdminKabupaten)
    {
        $kegiatan = $this->db->query("
            SELECT 
                kdp.id_kegiatan_detail_proses, 
                kdp.nama_kegiatan_detail_proses, 
                kdp.created_at,
                kw.id_kegiatan_wilayah, 
                kw.target_wilayah
            FROM master_kegiatan_detail_proses kdp
            JOIN kegiatan_wilayah kw ON kw.id_kegiatan_detail_proses = kdp.id_kegiatan_detail_proses
            JOIN kegiatan_wilayah_admin kwa ON kwa.id_kegiatan_wilayah = kw.id_kegiatan_wilayah
            WHERE kw.id_kabupaten = ?
            AND kwa.id_admin_kabupaten = ?
            GROUP BY kdp.id_kegiatan_detail_proses, 
                     kdp.nama_kegiatan_detail_proses, 
                     kdp.created_at,
                     kw.id_kegiatan_wilayah, 
                     kw.target_wilayah
            ORDER BY kdp.created_at DESC
            LIMIT 4
        ", [$idKabupaten, $idAdminKabupaten])->getResultArray();

        $progressData = [];
        $colors = ['#1e88e5', '#43a047', '#fdd835', '#8e24aa', '#e53935', '#5e35b1'];
        $colorIndex = 0;

        foreach ($kegiatan as $item) {
            $targetTotal = (int)$item['target_wilayah'];
            $realisasiTotal = $this->getRealisasiByKegiatanWilayah($item['id_kegiatan_wilayah']);
            
            if ($targetTotal > 0) {
                $progress = ($realisasiTotal / $targetTotal) * 100;
                
                $progressData[] = [
                    'nama' => $item['nama_kegiatan_detail_proses'],
                    'progress' => min(100, round($progress, 0)),
                    'color' => $colors[$colorIndex % count($colors)]
                ];
                
                $colorIndex++;
            }
        }

        return $progressData;
    }

    // Get Kurva S
    public function getKurvaS()
    {
        $idProses = $this->request->getGet('id_kegiatan_detail_proses');
        $sobatId = session()->get('sobat_id');
        
        if (!$sobatId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        // Get admin data
        $admin = $this->adminKabModel->db->table('admin_survei_kabupaten ask')
            ->select('ask.*, u.id_kabupaten')
            ->join('sipantau_user u', 'ask.sobat_id = u.sobat_id')
            ->where('ask.sobat_id', $sobatId)
            ->get()
            ->getRowArray();

        if (!$admin) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Admin not found'
            ]);
        }

        // Get kegiatan wilayah untuk kabupaten ini yang di-assign
        $kegiatanWilayah = $this->db->query("
            SELECT kw.*
            FROM kegiatan_wilayah kw
            JOIN kegiatan_wilayah_admin kwa ON kwa.id_kegiatan_wilayah = kw.id_kegiatan_wilayah
            WHERE kw.id_kegiatan_detail_proses = ?
            AND kw.id_kabupaten = ?
            AND kwa.id_admin_kabupaten = ?
        ", [$idProses, $admin['id_kabupaten'], $admin['id_admin_kabupaten']])->getRowArray();

        if (!$kegiatanWilayah) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kegiatan tidak ditemukan atau tidak di-assign'
            ]);
        }

        // Get kurva target
        $kurvaTarget = $this->kurvaKabModel
            ->where('id_kegiatan_wilayah', $kegiatanWilayah['id_kegiatan_wilayah'])
            ->orderBy('tanggal_target', 'ASC')
            ->findAll();

        // Get realisasi data
        $realisasiData = $this->getRealisasiData($kegiatanWilayah['id_kegiatan_wilayah']);

        // Get detail proses
        $detailProses = $this->prosesModel->find($idProses);

        // Format data untuk chart
        $chartData = $this->formatKurvaData($kurvaTarget, $realisasiData, $detailProses);

        return $this->response->setJSON([
            'success' => true,
            'data' => $chartData
        ]);
    }

    // Get Realisasi Data
    private function getRealisasiData($idKegiatanWilayah)
    {
        return $this->db->query("
            SELECT DATE(pp.created_at) as tanggal, SUM(pp.jumlah_realisasi_kumulatif) as total_realisasi
            FROM pantau_progress pp
            JOIN pcl ON pp.id_pcl = pcl.id_pcl
            JOIN pml ON pcl.id_pml = pml.id_pml
            WHERE pml.id_kegiatan_wilayah = ?
            GROUP BY DATE(pp.created_at)
            ORDER BY DATE(pp.created_at) ASC
        ", [$idKegiatanWilayah])->getResultArray();
    }

    // Format Kurva Data
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

        return [
            'labels' => $labels,
            'target' => $targetData,
            'realisasi' => $realisasiDataFormatted,
            'config' => [
                'nama' => $detailProses['nama_kegiatan_detail_proses'] ?? '',
                'tanggal_mulai' => date('d', strtotime($detailProses['tanggal_mulai'])),
                'tanggal_selesai' => date('d', strtotime($detailProses['tanggal_selesai']))
            ]
        ];
    }

    // Get Petugas
    public function getPetugas()
    {
        $idProses = $this->request->getGet('id_kegiatan_detail_proses');
        $sobatId = session()->get('sobat_id');
        
        if (!$sobatId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        // Get admin data
        $admin = $this->adminKabModel->db->table('admin_survei_kabupaten ask')
            ->select('ask.*, u.id_kabupaten')
            ->join('sipantau_user u', 'ask.sobat_id = u.sobat_id')
            ->where('ask.sobat_id', $sobatId)
            ->get()
            ->getRowArray();

        if (!$admin) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Admin not found'
            ]);
        }

        // Get kegiatan wilayah yang di-assign
        $kegiatanWilayah = $this->db->query("
            SELECT kw.*
            FROM kegiatan_wilayah kw
            JOIN kegiatan_wilayah_admin kwa ON kwa.id_kegiatan_wilayah = kw.id_kegiatan_wilayah
            WHERE kw.id_kegiatan_detail_proses = ?
            AND kw.id_kabupaten = ?
            AND kwa.id_admin_kabupaten = ?
        ", [$idProses, $admin['id_kabupaten'], $admin['id_admin_kabupaten']])->getRowArray();

        if (!$kegiatanWilayah) {
            return $this->response->setJSON([
                'success' => true,
                'data' => []
            ]);
        }

        // Get petugas (PCL)
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
        ", [$kegiatanWilayah['id_kegiatan_wilayah']])->getResultArray();

        // Calculate progress and status
        foreach ($petugas as &$p) {
            $target = (int)$p['target'];
            $realisasi = (int)$p['realisasi'];
            $progress = $target > 0 ? round(($realisasi / $target) * 100, 0) : 0;
            
            $p['progress'] = min(100, $progress);
            
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