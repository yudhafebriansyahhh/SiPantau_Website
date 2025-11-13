<?php

namespace App\Controllers\PemantauProv;

use App\Models\KurvaSProvinsiModel;
use App\Models\KurvaSkabModel;
use App\Models\MasterKegiatanDetailProsesModel;
use App\Models\MasterKegiatanWilayahModel;
use App\Models\MasterKegiatanDetailAdminModel;
use App\Models\MasterKegiatanDetailModel;
use App\Models\PantauProgressModel;
use CodeIgniter\Controller;

class DashboardController extends Controller
{   
    protected $db; 

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // Get role dari session
        $role = session()->get('role');
        $roleType = session()->get('role_type');
        
        $isPemantauProvinsi = ($role == 2 && $roleType == 'pemantau_provinsi');
        
        if (!$isPemantauProvinsi) {
            return redirect()->to(base_url('unauthorized'))
                ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Get statistik dashboard - SEMUA kegiatan
        $stats = $this->getDashboardStats();

        $prosesModel = new MasterKegiatanDetailProsesModel();

        // Dropdown kegiatan proses - SEMUA kegiatan (tidak ada filter)
        $kegiatanList = $prosesModel
            ->select('id_kegiatan_detail_proses, nama_kegiatan_detail_proses')
            ->orderBy('id_kegiatan_detail_proses', 'DESC')
            ->findAll();

        $latest = !empty($kegiatanList) ? $kegiatanList[0] : null;
        $latestKegiatanId = $latest ? $latest['id_kegiatan_detail_proses'] : '';

        // Get progress kegiatan yang sedang berjalan - SEMUA kegiatan
        $progressKegiatan = $this->getProgressKegiatanBerjalan();

        $data = [
            'title' => 'Dashboard',
            'active_menu' => 'dashboard',
            'stats' => $stats,
            'kegiatanList' => $kegiatanList,
            'latestKegiatanId' => $latestKegiatanId,
            'progressKegiatan' => $progressKegiatan,
            'kegiatanDetailProses' => $kegiatanList
        ];

        return view('PemantauProvinsi/dashboard', $data);
    }

    // ======================================================
    // GET DASHBOARD STATS
    // ======================================================
    private function getDashboardStats()
    {
        // Total Kegiatan - SEMUA kegiatan
        $totalKegiatan = $this->db->table('master_kegiatan_detail_proses')
            ->countAllResults();

        // Kegiatan Aktif - SEMUA kegiatan yang sedang berjalan
        $today = date('Y-m-d');
        $kegiatanAktif = $this->db->query("
            SELECT COUNT(*) as total
            FROM master_kegiatan_detail_proses kdp
            WHERE kdp.tanggal_mulai <= ?
            AND kdp.tanggal_selesai >= ?
        ", [$today, $today])->getRowArray();

        // Target Tercapai - rata-rata dari SEMUA kegiatan
        $targetTercapai = $this->calculateOverallProgress();

        return [
            'total_kegiatan' => $totalKegiatan ?? 0,
            'kegiatan_aktif' => (int)($kegiatanAktif['total'] ?? 0),
            'target_tercapai' => round($targetTercapai, 0)
        ];
    }

    // ======================================================
    // CALCULATE OVERALL PROGRESS
    // ======================================================
    private function calculateOverallProgress()
    {
        // Ambil SEMUA kegiatan proses
        $prosesList = $this->db->query("
            SELECT id_kegiatan_detail_proses, target
            FROM master_kegiatan_detail_proses
            WHERE target > 0
        ")->getResultArray();

        if (empty($prosesList)) {
            return 0;
        }

        $totalProgress = 0;
        $countKegiatan = 0;

        foreach ($prosesList as $proses) {
            $targetTotal = (int)$proses['target'];
            $realisasiTotal = $this->getRealisasiByProses($proses['id_kegiatan_detail_proses']);
            
            if ($targetTotal > 0) {
                $progress = ($realisasiTotal / $targetTotal) * 100;
                $totalProgress += min(100, $progress);
                $countKegiatan++;
            }
        }

        return $countKegiatan > 0 ? ($totalProgress / $countKegiatan) : 0;
    }

    // ======================================================
    // GET REALISASI BY PROSES
    // ======================================================
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

    // ======================================================
    // GET PROGRESS KEGIATAN BERJALAN
    // ======================================================
    private function getProgressKegiatanBerjalan()
    {
        // Ambil SEMUA kegiatan detail (tidak ada filter admin)
        $kegiatanDetails = $this->db->query("
            SELECT mkd.*, mk.nama_kegiatan
            FROM master_kegiatan_detail mkd
            JOIN master_kegiatan mk ON mk.id_kegiatan = mkd.id_kegiatan
            ORDER BY mkd.created_at DESC
            LIMIT 4
        ")->getResultArray();

        $progressData = [];
        $colors = ['#1e88e5', '#43a047', '#fdd835', '#8e24aa', '#e53935', '#5e35b1'];
        $colorIndex = 0;

        foreach ($kegiatanDetails as $detail) {
            $prosesList = $this->db->query("
                SELECT * FROM master_kegiatan_detail_proses
                WHERE id_kegiatan_detail = ?
            ", [$detail['id_kegiatan_detail']])->getResultArray();

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

    // ======================================================
    // KURVA S PROVINSI
    // ======================================================
    public function getKurvaProvinsi()
    {
        $idProses = $this->request->getGet('id_kegiatan_detail_proses');
        
        // Pemantau Provinsi bisa melihat SEMUA kegiatan, tidak perlu validasi access
        
        $model = new KurvaSProvinsiModel();
        $builder = $model
            ->select('tanggal_target, target_persen_kumulatif, target_kumulatif_absolut, target_harian_absolut')
            ->orderBy('tanggal_target', 'ASC');

        if ($idProses) {
            $builder->where('id_kegiatan_detail_proses', $idProses);
        } else {
            // Ambil kegiatan terakhir (tidak ada filter admin)
            $prosesModel = new MasterKegiatanDetailProsesModel();
            $latest = $prosesModel->orderBy('id_kegiatan_detail_proses', 'DESC')->first();
            
            if ($latest) $builder->where('id_kegiatan_detail_proses', $latest['id_kegiatan_detail_proses']);
        }

        $records = $builder->findAll();
        return $this->response->setJSON($this->formatKurvaData($records));
    }

    // ======================================================
    // KEGIATAN WILAYAH DROPDOWN
    // ======================================================
    public function getKegiatanWilayah()
    {
        $idProses = $this->request->getGet('id_kegiatan_detail_proses');
        
        // Pemantau Provinsi bisa melihat SEMUA kegiatan wilayah, tidak perlu validasi
        
        $wilayahModel = new MasterKegiatanWilayahModel();
        $records = $wilayahModel
            ->select('kegiatan_wilayah.id_kegiatan_wilayah, master_kabupaten.nama_kabupaten')
            ->join('master_kabupaten', 'master_kabupaten.id_kabupaten = kegiatan_wilayah.id_kabupaten', 'left')
            ->where('kegiatan_wilayah.id_kegiatan_detail_proses', $idProses)
            ->findAll();

        return $this->response->setJSON($records);
    }

    // ======================================================
    // KURVA S KABUPATEN
    // ======================================================
    public function getKurvaKabupaten()
    {
        $idWilayah = $this->request->getGet('id_kegiatan_wilayah');
        
        // Pemantau Provinsi bisa melihat SEMUA kurva kabupaten, tidak perlu validasi
        
        $model = new KurvaSkabModel();
        $records = $model
            ->where('id_kegiatan_wilayah', $idWilayah)
            ->orderBy('tanggal_target', 'ASC')
            ->findAll();

        return $this->response->setJSON($this->formatKurvaData($records));
    }

    // ======================================================
    // FORMAT KURVA DATA
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

        $unique = [];
        foreach ($records as $row) {
            $tgl = $row['tanggal_target'];
            if (!isset($unique[$tgl])) {
                $unique[$tgl] = $row;
            }
        }

        ksort($unique);

        $labels = $targetPersen = $targetAbsolut = $targetHarian = [];
        foreach ($unique as $row) {
            $labels[] = date('d M', strtotime($row['tanggal_target']));
            $targetPersen[] = (float) $row['target_persen_kumulatif'];
            $targetAbsolut[] = (int) $row['target_kumulatif_absolut'];
            $targetHarian[] = (int) $row['target_harian_absolut'];
        }

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

    // ======================================================
    // GET PETUGAS
    // ======================================================
    public function getPetugas()
    {
        $idWilayah = $this->request->getGet('id_kegiatan_wilayah');
        $idProses = $this->request->getGet('id_kegiatan_detail_proses');

        // Pemantau Provinsi bisa melihat SEMUA petugas, tidak perlu validasi access

        if (!$idWilayah || $idWilayah == 'all') {
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