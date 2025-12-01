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
            'kegiatan_aktif' => (int) ($kegiatanAktif['total'] ?? 0),
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
            $targetTotal = (int) $proses['target'];
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

        return (int) ($result['total_realisasi'] ?? 0);
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
                $totalTarget += (int) $proses['target'];
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

            if ($latest)
                $builder->where('id_kegiatan_detail_proses', $latest['id_kegiatan_detail_proses']);
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
            $target = (int) $p['target'];
            $realisasiTotal = (int) $p['realisasi_total'];

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

        $targetHarianValue = $targetHarian ? (int) $targetHarian['target_harian_absolut'] : 0;

        // Jika belum lapor
        if (!$laporanHariIni) {
            return [
                'text' => 'Belum Lapor',
                'class' => 'badge-danger',
                'realisasi_hari_ini' => 0,
                'target_harian' => $targetHarianValue
            ];
        }

        $realisasiHariIni = (int) $laporanHariIni['jumlah_realisasi_absolut'];

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

    // ======================================================
// GET KURVA S WITH REALISASI (NEW METHOD)
// ======================================================
    public function getKurvaSWithRealisasi()
    {
        $idProses = $this->request->getGet('id_kegiatan_detail_proses');
        $idWilayah = $this->request->getGet('id_kegiatan_wilayah');

        if (!$idProses) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID Proses tidak valid'
            ]);
        }

        // Get detail proses untuk config
        $prosesModel = new MasterKegiatanDetailProsesModel();
        $detailProses = $prosesModel->find($idProses);

        if (!$detailProses) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data proses tidak ditemukan'
            ]);
        }

        // Get data kurva target
        if ($idWilayah) {
            $kurvaTarget = $this->db->table('kurva_kabupaten')
                ->where('id_kegiatan_wilayah', $idWilayah)
                ->orderBy('tanggal_target', 'ASC')
                ->get()
                ->getResultArray();
        } else {
            $kurvaTarget = $this->db->table('kurva_provinsi')
                ->where('id_kegiatan_detail_proses', $idProses)
                ->orderBy('tanggal_target', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Get data realisasi
        $realisasiData = $this->getRealisasiDataForChart($idProses, $idWilayah);

        // Format data untuk chart
        $chartData = $this->formatKurvaDataWithRealisasi($kurvaTarget, $realisasiData, $detailProses);

        return $this->response->setJSON([
            'success' => true,
            'data' => $chartData
        ]);
    }

    // ======================================================
    // GET REALISASI DATA FOR CHART
    // ======================================================
    private function getRealisasiDataForChart($idProses, $idWilayah = null)
    {
        $builder = $this->db->table('pantau_progress pp')
            ->select('DATE(pp.created_at) as tanggal, MAX(pp.jumlah_realisasi_kumulatif) as realisasi_kumulatif')
            ->join('pcl', 'pp.id_pcl = pcl.id_pcl')
            ->join('pml', 'pcl.id_pml = pml.id_pml')
            ->join('kegiatan_wilayah kw', 'pml.id_kegiatan_wilayah = kw.id_kegiatan_wilayah')
            ->where('kw.id_kegiatan_detail_proses', $idProses);

        if ($idWilayah) {
            $builder->where('kw.id_kegiatan_wilayah', $idWilayah);
        }

        $builder->groupBy('DATE(pp.created_at)')
            ->orderBy('DATE(pp.created_at)', 'ASC');

        return $builder->get()->getResultArray();
    }

    // ======================================================
    // FORMAT KURVA DATA WITH REALISASI
    // ======================================================
    private function formatKurvaDataWithRealisasi($kurvaTarget, $realisasiData, $detailProses)
    {
        if (empty($kurvaTarget)) {
            return [
                'labels' => [],
                'target' => [],
                'realisasi' => [],
                'config' => [
                    'nama' => $detailProses['nama_kegiatan_detail_proses'],
                    'tanggal_mulai' => date('d', strtotime($detailProses['tanggal_mulai'])),
                    'tanggal_selesai' => date('d', strtotime($detailProses['tanggal_selesai']))
                ]
            ];
        }

        // Build realisasi lookup
        $realisasiLookup = [];
        foreach ($realisasiData as $item) {
            $realisasiLookup[$item['tanggal']] = (int) $item['realisasi_kumulatif'];
        }

        // Remove duplicate dates
        $unique = [];
        foreach ($kurvaTarget as $row) {
            $tgl = $row['tanggal_target'];
            $unique[$tgl] = $row;
        }
        ksort($unique);

        $labels = [];
        $targetData = [];
        $realisasiDataFormatted = [];

        $lastRealisasi = 0;

        foreach ($unique as $tanggal => $row) {
            $labels[] = date('d M', strtotime($tanggal));
            $targetData[] = (int) $row['target_kumulatif_absolut'];

            if (isset($realisasiLookup[$tanggal])) {
                $lastRealisasi = $realisasiLookup[$tanggal];
            }

            $realisasiDataFormatted[] = $lastRealisasi;
        }

        // Ensure monotonic increase for target
        for ($i = 1; $i < count($targetData); $i++) {
            if ($targetData[$i] < $targetData[$i - 1]) {
                $targetData[$i] = $targetData[$i - 1];
            }
        }

        return [
            'labels' => array_values($labels),
            'target' => array_values($targetData),
            'realisasi' => array_values($realisasiDataFormatted),
            'config' => [
                'nama' => $detailProses['nama_kegiatan_detail_proses'],
                'tanggal_mulai' => date('d', strtotime($detailProses['tanggal_mulai'])),
                'tanggal_selesai' => date('d', strtotime($detailProses['tanggal_selesai']))
            ]
        ];
    }
}