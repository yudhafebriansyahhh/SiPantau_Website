<?php

namespace App\Controllers\PemantauKab;

use App\Models\KurvaSkabModel;
use App\Models\MasterKegiatanDetailProsesModel;
use App\Models\MasterKegiatanWilayahModel;
use App\Models\UserModel;
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
        // Get role dan user info dari session
        $role = session()->get('role');
        $roleType = session()->get('role_type');
        $sobatId = session()->get('sobat_id');
        
        $isPemantauKabupaten = ($role == 3 && $roleType == 'pemantau_kabupaten');
        
        if (!$isPemantauKabupaten) {
            return redirect()->to(base_url('unauthorized'))
                ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Get kabupaten user dari database
        $userModel = new UserModel();
        $user = $userModel->find($sobatId);
        
        if (!$user || !$user['id_kabupaten']) {
            return redirect()->to(base_url('unauthorized'))
                ->with('error', 'Data kabupaten tidak ditemukan.');
        }

        $idKabupaten = $user['id_kabupaten'];
        
        // Simpan ke session untuk digunakan di method lain
        session()->set('user_kabupaten_id', $idKabupaten);

        // Get statistik dashboard - HANYA untuk kabupaten ini
        $stats = $this->getDashboardStats($idKabupaten);

        // Dropdown kegiatan wilayah - HANYA untuk kabupaten ini
        $kegiatanList = $this->getKegiatanWilayahList($idKabupaten);

        $latest = !empty($kegiatanList) ? $kegiatanList[0] : null;
        $latestKegiatanWilayahId = $latest ? $latest['id_kegiatan_wilayah'] : '';

        // Get progress kegiatan yang sedang berjalan - HANYA kabupaten ini
        $progressKegiatan = $this->getProgressKegiatanBerjalan($idKabupaten);

        // Get nama kabupaten untuk ditampilkan
        $kabupaten = $this->db->table('master_kabupaten')
            ->where('id_kabupaten', $idKabupaten)
            ->get()
            ->getRowArray();

        $data = [
            'title' => 'Dashboard Pemantau Kabupaten',
            'active_menu' => 'dashboard',
            'stats' => $stats,
            'kegiatanList' => $kegiatanList,
            'latestKegiatanWilayahId' => $latestKegiatanWilayahId,
            'progressKegiatan' => $progressKegiatan,
            'kabupaten' => $kabupaten,
            'id_kabupaten' => $idKabupaten
        ];

        return view('PemantauKabupaten/dashboard', $data);
    }

    // ======================================================
    // GET DASHBOARD STATS
    // ======================================================
    private function getDashboardStats($idKabupaten)
    {
        // Total Kegiatan - HANYA di kabupaten ini
        $totalKegiatan = $this->db->table('kegiatan_wilayah')
            ->where('id_kabupaten', $idKabupaten)
            ->countAllResults();

        // Kegiatan Aktif - HANYA di kabupaten ini yang sedang berjalan
        $today = date('Y-m-d');
        $kegiatanAktif = $this->db->query("
            SELECT COUNT(*) as total
            FROM kegiatan_wilayah kw
            JOIN master_kegiatan_detail_proses mkdp ON kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses
            WHERE kw.id_kabupaten = ?
            AND mkdp.tanggal_mulai <= ?
            AND mkdp.tanggal_selesai >= ?
        ", [$idKabupaten, $today, $today])->getRowArray();

        // Target Tercapai - rata-rata dari kegiatan di kabupaten ini
        $targetTercapai = $this->calculateOverallProgress($idKabupaten);

        return [
            'total_kegiatan' => $totalKegiatan ?? 0,
            'kegiatan_aktif' => (int)($kegiatanAktif['total'] ?? 0),
            'target_tercapai' => round($targetTercapai, 0)
        ];
    }

    // ======================================================
    // CALCULATE OVERALL PROGRESS
    // ======================================================
    private function calculateOverallProgress($idKabupaten)
    {
        // Ambil semua kegiatan wilayah di kabupaten ini
        $wilayahList = $this->db->query("
            SELECT id_kegiatan_wilayah, target_wilayah
            FROM kegiatan_wilayah
            WHERE id_kabupaten = ?
            AND target_wilayah > 0
        ", [$idKabupaten])->getResultArray();

        if (empty($wilayahList)) {
            return 0;
        }

        $totalProgress = 0;
        $countKegiatan = 0;

        foreach ($wilayahList as $wilayah) {
            $targetTotal = (int)$wilayah['target_wilayah'];
            $realisasiTotal = $this->getRealisasiByKegiatanWilayah($wilayah['id_kegiatan_wilayah']);
            
            if ($targetTotal > 0) {
                $progress = ($realisasiTotal / $targetTotal) * 100;
                $totalProgress += min(100, $progress);
                $countKegiatan++;
            }
        }

        return $countKegiatan > 0 ? ($totalProgress / $countKegiatan) : 0;
    }

    // ======================================================
    // GET REALISASI BY KEGIATAN WILAYAH
    // ======================================================
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

    // ======================================================
    // GET KEGIATAN WILAYAH LIST
    // ======================================================
    private function getKegiatanWilayahList($idKabupaten)
    {
        return $this->db->query("
            SELECT 
                kw.id_kegiatan_wilayah,
                mkdp.nama_kegiatan_detail_proses,
                mkd.nama_kegiatan_detail,
                mk.nama_kegiatan,
                mkdp.tanggal_mulai,
                mkdp.tanggal_selesai
            FROM kegiatan_wilayah kw
            JOIN master_kegiatan_detail_proses mkdp ON kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses
            JOIN master_kegiatan_detail mkd ON mkdp.id_kegiatan_detail = mkd.id_kegiatan_detail
            JOIN master_kegiatan mk ON mkd.id_kegiatan = mk.id_kegiatan
            WHERE kw.id_kabupaten = ?
            ORDER BY mkdp.tanggal_mulai DESC
        ", [$idKabupaten])->getResultArray();
    }

    // ======================================================
    // GET PROGRESS KEGIATAN BERJALAN
    // ======================================================
    private function getProgressKegiatanBerjalan($idKabupaten)
    {
        // Ambil kegiatan wilayah di kabupaten ini (limit 4)
        $kegiatanWilayah = $this->db->query("
            SELECT 
                kw.id_kegiatan_wilayah,
                kw.target_wilayah,
                mkdp.nama_kegiatan_detail_proses,
                mkd.nama_kegiatan_detail
            FROM kegiatan_wilayah kw
            JOIN master_kegiatan_detail_proses mkdp ON kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses
            JOIN master_kegiatan_detail mkd ON mkdp.id_kegiatan_detail = mkd.id_kegiatan_detail
            WHERE kw.id_kabupaten = ?
            ORDER BY mkdp.tanggal_mulai DESC
            LIMIT 4
        ", [$idKabupaten])->getResultArray();

        $progressData = [];
        $colors = ['#1e88e5', '#43a047', '#fdd835', '#8e24aa', '#e53935', '#5e35b1'];
        $colorIndex = 0;

        foreach ($kegiatanWilayah as $wilayah) {
            $targetTotal = (int)$wilayah['target_wilayah'];
            $realisasiTotal = $this->getRealisasiByKegiatanWilayah($wilayah['id_kegiatan_wilayah']);

            if ($targetTotal > 0) {
                $progress = ($realisasiTotal / $targetTotal) * 100;
                
                $progressData[] = [
                    'nama' => $wilayah['nama_kegiatan_detail_proses'],
                    'progress' => min(100, round($progress, 0)),
                    'color' => $colors[$colorIndex % count($colors)]
                ];
                
                $colorIndex++;
            }
        }

        return $progressData;
    }

    // ======================================================
    // KURVA S KABUPATEN
    // ======================================================
    public function getKurvaKabupaten()
    {
        $idKegiatanWilayah = $this->request->getGet('id_kegiatan_wilayah');
        $idKabupaten = session()->get('user_kabupaten_id');
        
        // Validasi: pastikan kegiatan wilayah ini milik kabupaten user
        $kegiatan = $this->db->table('kegiatan_wilayah')
            ->where('id_kegiatan_wilayah', $idKegiatanWilayah)
            ->where('id_kabupaten', $idKabupaten)
            ->get()
            ->getRowArray();
        
        if (!$kegiatan) {
            return $this->response->setJSON([
                'labels' => [],
                'targetPersen' => [],
                'targetAbsolut' => [],
                'targetHarian' => []
            ]);
        }
        
        $model = new KurvaSkabModel();
        $records = $model
            ->where('id_kegiatan_wilayah', $idKegiatanWilayah)
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
        $idKegiatanWilayah = $this->request->getGet('id_kegiatan_wilayah');
        $idKabupaten = session()->get('user_kabupaten_id');

        // Validasi: pastikan kegiatan wilayah ini milik kabupaten user
        $kegiatan = $this->db->table('kegiatan_wilayah')
            ->where('id_kegiatan_wilayah', $idKegiatanWilayah)
            ->where('id_kabupaten', $idKabupaten)
            ->get()
            ->getRowArray();
        
        if (!$kegiatan) {
            return $this->response->setJSON([
                'success' => true,
                'data' => []
            ]);
        }

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
        ", [$idKegiatanWilayah])->getResultArray();

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