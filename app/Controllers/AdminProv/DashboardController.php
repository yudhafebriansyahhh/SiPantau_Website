<?php

namespace App\Controllers\AdminProv;

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
        // Get role dan admin ID dari session
        $role = session()->get('role');
        $roleType = session()->get('role_type');
        $idAdminProvinsi = session()->get('id_admin_provinsi');
        
        // Jika id_admin_provinsi tidak ada di session, coba ambil dari database
        if (!$idAdminProvinsi && $roleType == 'admin_provinsi') {
            $sobatId = session()->get('sobat_id');
            if ($sobatId) {
                $adminProv = $this->db->table('admin_survei_provinsi')
                    ->where('sobat_id', $sobatId)
                    ->get()
                    ->getRowArray();
                
                if ($adminProv) {
                    $idAdminProvinsi = $adminProv['id_admin_provinsi'];
                    session()->set('id_admin_provinsi', $idAdminProvinsi);
                }
            }
        }
        
        $isSuperAdmin = ($role == 1);
        $isAdminProvinsi = ($role == 2 && $roleType == 'admin_provinsi' && $idAdminProvinsi);
        
        if (!$isSuperAdmin && !$isAdminProvinsi) {
            return redirect()->to(base_url('unauthorized'))
                ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Set default values
        $stats = [
            'total_kegiatan' => 0,
            'kegiatan_aktif' => 0,
            'target_tercapai' => 0
        ];
        $progressKegiatan = [];
        $kegiatanList = [];
        $latestKegiatanId = '';

        // Only get data if we have valid admin ID
        if ($idAdminProvinsi) {
            // Get statistik dashboard
            $stats = $this->getDashboardStats($idAdminProvinsi);

            $prosesModel = new MasterKegiatanDetailProsesModel();

            // Dropdown kegiatan proses - filter berdasarkan assignment
            if ($isSuperAdmin) {
                $kegiatanList = $prosesModel
                    ->select('id_kegiatan_detail_proses, nama_kegiatan_detail_proses')
                    ->orderBy('id_kegiatan_detail_proses', 'DESC')
                    ->findAll();
            } else {
                $kegiatanList = $this->db->table('master_kegiatan_detail_proses kdp')
                    ->select('kdp.id_kegiatan_detail_proses, kdp.nama_kegiatan_detail_proses')
                    ->join('master_kegiatan_detail_admin mkda', 'mkda.id_kegiatan_detail = kdp.id_kegiatan_detail')
                    ->where('mkda.id_admin_provinsi', $idAdminProvinsi)
                    ->orderBy('kdp.id_kegiatan_detail_proses', 'DESC')
                    ->get()
                    ->getResultArray();
            }

            $latest = !empty($kegiatanList) ? $kegiatanList[0] : null;
            $latestKegiatanId = $latest ? $latest['id_kegiatan_detail_proses'] : '';

            // Get progress kegiatan yang sedang berjalan
            $progressKegiatan = $this->getProgressKegiatanBerjalan($idAdminProvinsi);
        }

        $data = [
            'title' => 'Dashboard',
            'active_menu' => 'dashboard',
            'stats' => $stats,
            'kegiatanList' => $kegiatanList,
            'latestKegiatanId' => $latestKegiatanId,
            'isSuperAdmin' => $isSuperAdmin,
            'progressKegiatan' => $progressKegiatan,
            'kegiatanDetailProses' => $kegiatanList
        ];

        return view('AdminSurveiProv/dashboard', $data);
    }

    // ======================================================
    // GET DASHBOARD STATS
    // ======================================================
    private function getDashboardStats($idAdminProvinsi)
    {
        // Total Kegiatan (yang di-assign)
        $totalKegiatan = $this->db->table('master_kegiatan_detail_admin')
            ->where('id_admin_provinsi', $idAdminProvinsi)
            ->countAllResults();

        // Kegiatan Aktif
        $today = date('Y-m-d');
        $kegiatanAktif = $this->db->query("
            SELECT COUNT(*) as total
            FROM master_kegiatan_detail_proses kdp
            JOIN master_kegiatan_detail_admin mkda ON mkda.id_kegiatan_detail = kdp.id_kegiatan_detail
            WHERE mkda.id_admin_provinsi = ?
            AND kdp.tanggal_mulai <= ?
            AND kdp.tanggal_selesai >= ?
        ", [$idAdminProvinsi, $today, $today])->getRowArray();

        // Target Tercapai
        $targetTercapai = $this->calculateOverallProgress($idAdminProvinsi);

        return [
            'total_kegiatan' => $totalKegiatan ?? 0,
            'kegiatan_aktif' => (int)($kegiatanAktif['total'] ?? 0),
            'target_tercapai' => round($targetTercapai, 0)
        ];
    }

    // ======================================================
    // CALCULATE OVERALL PROGRESS
    // ======================================================
    private function calculateOverallProgress($idAdminProvinsi)
    {
        $prosesList = $this->db->query("
            SELECT kdp.id_kegiatan_detail_proses, kdp.target
            FROM master_kegiatan_detail_proses kdp
            JOIN master_kegiatan_detail_admin mkda ON mkda.id_kegiatan_detail = kdp.id_kegiatan_detail
            WHERE mkda.id_admin_provinsi = ?
            AND kdp.target > 0
        ", [$idAdminProvinsi])->getResultArray();

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
    private function getProgressKegiatanBerjalan($idAdminProvinsi)
    {
        $kegiatanDetails = $this->db->query("
            SELECT mkd.*, mk.nama_kegiatan
            FROM master_kegiatan_detail mkd
            JOIN master_kegiatan mk ON mk.id_kegiatan = mkd.id_kegiatan
            JOIN master_kegiatan_detail_admin mkda ON mkda.id_kegiatan_detail = mkd.id_kegiatan_detail
            WHERE mkda.id_admin_provinsi = ?
            ORDER BY mkd.created_at DESC
            LIMIT 4
        ", [$idAdminProvinsi])->getResultArray();

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
        
        $role = session()->get('role');
        $roleType = session()->get('role_type');
        $idAdminProvinsi = session()->get('id_admin_provinsi');
        
        $isSuperAdmin = ($role == 1);
        $isAdminProvinsi = ($role == 2 && $roleType == 'admin_provinsi' && $idAdminProvinsi);
        
        // Validasi akses untuk Admin Provinsi
        if ($isAdminProvinsi && $idProses) {
            $prosesModel = new MasterKegiatanDetailProsesModel();
            $proses = $prosesModel->find($idProses);
            
            if ($proses) {
                $adminModel = new MasterKegiatanDetailAdminModel();
                $hasAccess = $adminModel
                    ->where('id_kegiatan_detail', $proses['id_kegiatan_detail'])
                    ->where('id_admin_provinsi', $idAdminProvinsi)
                    ->first();
                
                if (!$hasAccess) {
                    return $this->response->setJSON([
                        'labels' => [],
                        'targetPersen' => [],
                        'targetAbsolut' => [],
                        'targetHarian' => []
                    ]);
                }
            }
        }
        
        $model = new KurvaSProvinsiModel();
        $builder = $model
            ->select('tanggal_target, target_persen_kumulatif, target_kumulatif_absolut, target_harian_absolut')
            ->orderBy('tanggal_target', 'ASC');

        if ($idProses) {
            $builder->where('id_kegiatan_detail_proses', $idProses);
        } else {
            $prosesModel = new MasterKegiatanDetailProsesModel();
            if ($isSuperAdmin) {
                $latest = $prosesModel->orderBy('id_kegiatan_detail_proses', 'DESC')->first();
            } else {
                $latest = $this->db->table('master_kegiatan_detail_proses kdp')
                    ->select('kdp.*')
                    ->join('master_kegiatan_detail_admin mkda', 'mkda.id_kegiatan_detail = kdp.id_kegiatan_detail')
                    ->where('mkda.id_admin_provinsi', $idAdminProvinsi)
                    ->orderBy('kdp.id_kegiatan_detail_proses', 'DESC')
                    ->get()
                    ->getRowArray();
            }
            
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
        
        $role = session()->get('role');
        $roleType = session()->get('role_type');
        $idAdminProvinsi = session()->get('id_admin_provinsi');
        
        $isSuperAdmin = ($role == 1);
        $isAdminProvinsi = ($role == 2 && $roleType == 'admin_provinsi' && $idAdminProvinsi);
        
        if ($isAdminProvinsi && $idProses) {
            $prosesModel = new MasterKegiatanDetailProsesModel();
            $proses = $prosesModel->find($idProses);
            
            if ($proses) {
                $adminModel = new MasterKegiatanDetailAdminModel();
                $hasAccess = $adminModel
                    ->where('id_kegiatan_detail', $proses['id_kegiatan_detail'])
                    ->where('id_admin_provinsi', $idAdminProvinsi)
                    ->first();
                
                if (!$hasAccess) {
                    return $this->response->setJSON([]);
                }
            }
        }
        
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
        
        $role = session()->get('role');
        $roleType = session()->get('role_type');
        $idAdminProvinsi = session()->get('id_admin_provinsi');
        
        $isSuperAdmin = ($role == 1);
        $isAdminProvinsi = ($role == 2 && $roleType == 'admin_provinsi' && $idAdminProvinsi);
        
        if ($isAdminProvinsi && $idWilayah) {
            $wilayahModel = new MasterKegiatanWilayahModel();
            $wilayah = $wilayahModel->find($idWilayah);
            
            if ($wilayah) {
                $prosesModel = new MasterKegiatanDetailProsesModel();
                $proses = $prosesModel->find($wilayah['id_kegiatan_detail_proses']);
                
                if ($proses) {
                    $adminModel = new MasterKegiatanDetailAdminModel();
                    $hasAccess = $adminModel
                        ->where('id_kegiatan_detail', $proses['id_kegiatan_detail'])
                        ->where('id_admin_provinsi', $idAdminProvinsi)
                        ->first();
                    
                    if (!$hasAccess) {
                        return $this->response->setJSON([
                            'labels' => [],
                            'targetPersen' => [],
                            'targetAbsolut' => [],
                            'targetHarian' => []
                        ]);
                    }
                }
            }
        }
        
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
        $idAdminProvinsi = session()->get('id_admin_provinsi');

        // Validasi akses
        if ($idProses) {
            $prosesModel = new MasterKegiatanDetailProsesModel();
            $proses = $prosesModel->find($idProses);
            
            if ($proses) {
                $adminModel = new MasterKegiatanDetailAdminModel();
                $hasAccess = $adminModel
                    ->where('id_kegiatan_detail', $proses['id_kegiatan_detail'])
                    ->where('id_admin_provinsi', $idAdminProvinsi)
                    ->first();
                
                if (!$hasAccess) {
                    return $this->response->setJSON([
                        'success' => true,
                        'data' => []
                    ]);
                }
            }
        }

        if (!$idProses) {
            return $this->response->setJSON([
                'success' => true,
                'data' => []
            ]);
        }

        // Get detail kegiatan untuk cek tanggal mulai dan selesai
        $prosesModel = new MasterKegiatanDetailProsesModel();
        $detailProses = $prosesModel->find($idProses);

        if (!$detailProses) {
            return $this->response->setJSON([
                'success' => true,
                'data' => []
            ]);
        }

        $tanggalMulai = $detailProses['tanggal_mulai'];
        $tanggalSelesai = $detailProses['tanggal_selesai'];
        $today = date('Y-m-d');

        // Tentukan status kegiatan global
        $statusKegiatanGlobal = 'Belum Dimulai';
        if ($today >= $tanggalMulai && $today <= $tanggalSelesai) {
            $statusKegiatanGlobal = 'Sedang Berjalan';
        } elseif ($today > $tanggalSelesai) {
            $statusKegiatanGlobal = 'Selesai';
        }

        if (!$idWilayah || $idWilayah == 'all') {
            $petugas = $this->db->query("
                SELECT 
                    u.nama_user,
                    u.sobat_id,
                    pcl.id_pcl,
                    mk.nama_kabupaten,
                    pcl.target,
                    COALESCE(MAX(pp.jumlah_realisasi_kumulatif), 0) as realisasi_total,
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
                    pcl.id_pcl,
                    mk.nama_kabupaten,
                    pcl.target,
                    COALESCE(MAX(pp.jumlah_realisasi_kumulatif), 0) as realisasi_total,
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

        // Process setiap petugas
        foreach ($petugas as &$p) {
            $target = (int)$p['target'];
            $realisasiTotal = (int)$p['realisasi_total'];

            // Hitung progress keseluruhan
            $progress = $target > 0 ? round(($realisasiTotal / $target) * 100, 0) : 0;
            $p['progress'] = min(100, $progress);

            // Set status kegiatan (sama dengan global)
            $p['status_kegiatan'] = $statusKegiatanGlobal;
            $p['status_kegiatan_class'] = $this->getStatusKegiatanClass($statusKegiatanGlobal);

            // Cek status harian
            $statusHarian = $this->getStatusHarian(
                $p['id_pcl'],
                $statusKegiatanGlobal,
                $today,
                $target
            );

            $p['status_harian'] = $statusHarian['text'];
            $p['status_harian_class'] = $statusHarian['class'];
            $p['realisasi_hari_ini'] = $statusHarian['realisasi_hari_ini'];
            $p['target_harian'] = $statusHarian['target_harian'];
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $petugas
        ]);
    }

    // Helper: Get Status Kegiatan Class
    private function getStatusKegiatanClass($status)
    {
        switch ($status) {
            case 'Sedang Berjalan':
                return 'badge-success';
            case 'Belum Dimulai':
                return 'badge-warning';
            case 'Selesai':
                return 'badge-secondary';
            default:
                return 'badge-secondary';
        }
    }

    // Helper: Get Status Harian
    private function getStatusHarian($idPCL, $statusKegiatan, $today, $targetTotal)
    {
        // Jika kegiatan belum dimulai atau sudah selesai
        if ($statusKegiatan !== 'Sedang Berjalan') {
            return [
                'text' => 'Tidak Perlu Lapor',
                'class' => 'badge-secondary',
                'realisasi_hari_ini' => 0,
                'target_harian' => 0
            ];
        }

        // Cek apakah sudah lapor hari ini
        $laporanHariIni = $this->db->query("
            SELECT jumlah_realisasi_absolut
            FROM pantau_progress
            WHERE id_pcl = ?
            AND DATE(created_at) = ?
            ORDER BY created_at DESC
            LIMIT 1
        ", [$idPCL, $today])->getRowArray();

        // Get target harian dari kurva_petugas
        $targetHarian = $this->db->query("
            SELECT target_harian_absolut
            FROM kurva_petugas
            WHERE id_pcl = ?
            AND tanggal_target = ?
            AND is_hari_kerja = 1
        ", [$idPCL, $today])->getRowArray();

        $targetHarianValue = $targetHarian ? (int)$targetHarian['target_harian_absolut'] : 0;

        // Jika belum lapor
        if (!$laporanHariIni) {
            return [
                'text' => 'Belum Lapor',
                'class' => 'badge-danger',
                'realisasi_hari_ini' => 0,
                'target_harian' => $targetHarianValue
            ];
        }

        $realisasiHariIni = (int)$laporanHariIni['jumlah_realisasi_absolut'];

        // Jika tidak ada target harian (hari libur atau belum ada kurva)
        if ($targetHarianValue === 0) {
            return [
                'text' => 'Sudah Lapor',
                'class' => 'badge-success',
                'realisasi_hari_ini' => $realisasiHariIni,
                'target_harian' => 0
            ];
        }

        // Bandingkan dengan target harian
        if ($realisasiHariIni < $targetHarianValue) {
            return [
                'text' => 'Di Bawah Target',
                'class' => 'badge-warning',
                'realisasi_hari_ini' => $realisasiHariIni,
                'target_harian' => $targetHarianValue
            ];
        } elseif ($realisasiHariIni > $targetHarianValue) {
            return [
                'text' => 'Melebihi Target',
                'class' => 'badge-info',
                'realisasi_hari_ini' => $realisasiHariIni,
                'target_harian' => $targetHarianValue
            ];
        } else {
            return [
                'text' => 'Sesuai Target',
                'class' => 'badge-success',
                'realisasi_hari_ini' => $realisasiHariIni,
                'target_harian' => $targetHarianValue
            ];
        }
    }
}