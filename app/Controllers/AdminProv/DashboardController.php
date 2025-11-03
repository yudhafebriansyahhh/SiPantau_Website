<?php

namespace App\Controllers\AdminProv;

use App\Models\KurvaSProvinsiModel;
use App\Models\KurvaSkabModel;
use App\Models\MasterKegiatanDetailProsesModel;
use App\Models\MasterKegiatanWilayahModel;
use App\Models\MasterKegiatanDetailAdminModel;
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
        
        $isSuperAdmin = ($role == 1);
        $isAdminProvinsi = ($role == 2 && $roleType == 'admin_provinsi' && $idAdminProvinsi);
        
        if (!$isSuperAdmin && !$isAdminProvinsi) {
            return redirect()->to(base_url('unauthorized'))
                ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $prosesModel = new MasterKegiatanDetailProsesModel();

        // Dropdown kegiatan proses - filter berdasarkan assignment
        if ($isSuperAdmin) {
            // Super Admin melihat semua
            $kegiatanList = $prosesModel
                ->select('id_kegiatan_detail_proses, nama_kegiatan_detail_proses')
                ->orderBy('id_kegiatan_detail_proses', 'DESC')
                ->findAll();
        } else {
            // Admin Provinsi hanya melihat yang di-assign
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

        $data = [
            'title' => 'Dashboard',
            'active_menu' => 'dashboard',
            'kegiatanList' => $kegiatanList,
            'latestKegiatanId' => $latestKegiatanId,
            'isSuperAdmin' => $isSuperAdmin
        ];

        return view('AdminSurveiProv/dashboard', $data);
    }

    // ======================================================
    // KURVA S PROVINSI
    // ======================================================
    public function getKurvaProvinsi()
    {
        $idProses = $this->request->getGet('id_kegiatan_detail_proses');
        
        // Get role dan admin ID dari session
        $role = session()->get('role');
        $roleType = session()->get('role_type');
        $idAdminProvinsi = session()->get('id_admin_provinsi');
        
        $isSuperAdmin = ($role == 1);
        $isAdminProvinsi = ($role == 2 && $roleType == 'admin_provinsi' && $idAdminProvinsi);
        
        // Validasi akses untuk Admin Provinsi
        if ($isAdminProvinsi && $idProses) {
            // Check apakah kegiatan ini di-assign ke admin ini
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
            // Get latest kegiatan yang accessible
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
    // 🔹 Kegiatan Wilayah Dropdown (kabupaten)
    // ======================================================
    public function getKegiatanWilayah()
    {
        $idProses = $this->request->getGet('id_kegiatan_detail_proses');
        
        // Get role dan admin ID dari session
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
    // 🔹 Kurva S Kabupaten (berdasarkan kegiatan_wilayah)
    // ======================================================
    public function getKurvaKabupaten()
    {
        $idWilayah = $this->request->getGet('id_kegiatan_wilayah');
        
        // Get role dan admin ID dari session
        $role = session()->get('role');
        $roleType = session()->get('role_type');
        $idAdminProvinsi = session()->get('id_admin_provinsi');
        
        $isSuperAdmin = ($role == 1);
        $isAdminProvinsi = ($role == 2 && $roleType == 'admin_provinsi' && $idAdminProvinsi);
        
        // Validasi akses untuk Admin Provinsi
        if ($isAdminProvinsi && $idWilayah) {
            // Check apakah kegiatan wilayah ini terkait dengan kegiatan yang di-assign
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
