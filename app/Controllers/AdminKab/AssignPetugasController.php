<?php

namespace App\Controllers\AdminKab;

use App\Controllers\BaseController;
use App\Models\AdminSurveiKabupatenModel;
use App\Models\MasterKegiatanWilayahModel;
use App\Models\KegiatanWilayahAdminModel;
use App\Models\PMLModel;
use App\Models\PCLModel;
use App\Models\UserModel;
use App\Models\KurvaPetugasModel;
use App\Models\MasterKegiatanDetailProsesModel;
use DateInterval;
use DatePeriod;
use DateTime;

class AssignPetugasController extends BaseController
{
    protected $adminKabModel;
    protected $kegiatanWilayahModel;
    protected $kegiatanWilayahAdminModel;
    protected $pmlModel;
    protected $pclModel;
    protected $userModel;
    protected $kurvaModel;
    protected $prosesModel;

    public function __construct()
    {
        $this->adminKabModel = new AdminSurveiKabupatenModel();
        $this->kegiatanWilayahModel = new MasterKegiatanWilayahModel();
        $this->kegiatanWilayahAdminModel = new KegiatanWilayahAdminModel();
        $this->pmlModel = new PMLModel();
        $this->pclModel = new PCLModel();
        $this->userModel = new UserModel();
        $this->kurvaModel = new KurvaPetugasModel();
        $this->prosesModel = new MasterKegiatanDetailProsesModel();
    }

    //  Halaman Index - Daftar Assignment PML
    //  Hanya tampilkan kegiatan yang di-assign ke admin yang login
    public function index()
    {
        $sobatId = session()->get('sobat_id');

        if (!$sobatId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

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
        $idKegiatanWilayah = $this->request->getGet('kegiatan');

        // Ambil perPage dari GET, default 10
        $perPage = $this->request->getGet('perPage') ?? 10;

        // Validasi perPage agar hanya nilai yang diizinkan
        $allowedPerPage = [5, 10, 25, 50, 100];
        if (!in_array((int) $perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        // Ambil PML dengan pagination hanya untuk kegiatan yang di-assign ke admin ini
        $dataPML = $this->pmlModel->getPMLByKabupatenAndAdminPaginated($idKabupaten, $idAdminKabupaten, $idKegiatanWilayah, $perPage);

        // Ambil kegiatan list hanya yang di-assign ke admin ini
        $kegiatanList = $this->kegiatanWilayahModel->getByKabupatenAndAdmin($idKabupaten, $idAdminKabupaten);

        $data = [
            'title' => 'Assign Petugas Survei',
            'active_menu' => 'assign-admin-kab',
            'admin' => $admin,
            'dataPML' => $dataPML,
            'kegiatanList' => $kegiatanList,
            'selectedKegiatan' => $idKegiatanWilayah,
            'perPage' => $perPage,
            'pager' => $this->pmlModel->pager,
        ];

        return view('AdminSurveiKab/AssignPetugasSurvei/index', $data);
    }

    //  Halaman Create - Form Assign PML dan PCL
    public function create()
    {
        $sobatId = session()->get('sobat_id');

        if (!$sobatId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

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

        // Hanya tampilkan kegiatan yang di-assign ke admin ini
        $kegiatanList = $this->kegiatanWilayahModel->getByKabupatenAndAdmin($idKabupaten, $idAdminKabupaten);

        $data = [
            'title' => 'Assign Petugas Survei',
            'active_menu' => 'assign-admin-kab',
            'admin' => $admin,
            'kegiatanList' => $kegiatanList
        ];

        return view('AdminSurveiKab/AssignPetugasSurvei/create', $data);
    }

    public function edit($id_pml)
    {
        $sobatId = session()->get('sobat_id');

        if (!$sobatId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Ambil data admin kabupaten
        $admin = $this->adminKabModel->join('sipantau_user u', 'admin_survei_kabupaten.sobat_id = u.sobat_id')
            ->join('master_kabupaten k', 'u.id_kabupaten = k.id_kabupaten')
            ->select('admin_survei_kabupaten.*, u.id_kabupaten, u.nama_user, k.nama_kabupaten')
            ->where('admin_survei_kabupaten.sobat_id', $sobatId)
            ->first();

        if (!$admin) {
            return redirect()->to('/')->with('error', 'Anda tidak memiliki akses sebagai admin kabupaten');
        }

        $idKabupaten = $admin['id_kabupaten'];
        $idAdminKabupaten = $admin['id_admin_kabupaten'];

        // Ambil data PML beserta PCL-nya
        $pml = $this->pmlModel->getPMLWithDetails($id_pml);
        if (!$pml || $pml['id_kabupaten'] != $idKabupaten) {
            return redirect()->back()->with('error', 'Data PML tidak ditemukan atau akses ditolak');
        }

        // Cek apakah kegiatan ini di-assign ke admin yang login
        $isAssigned = $this->kegiatanWilayahAdminModel->isAssigned($idAdminKabupaten, $pml['id_kegiatan_wilayah']);
        if (!$isAssigned) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke kegiatan ini');
        }

        // Ambil PCL terkait
        $pclsRaw = $this->pclModel->getPCLByPML($id_pml);
        $pcls = [];
        foreach ($pclsRaw as $p) {
            $user = $this->userModel->find($p['sobat_id']);
            $p['nama_user'] = $user['nama_user'] ?? 'Unknown';
            $pcls[] = $p;
        }

        // Kegiatan yang tersedia untuk admin ini
        $kegiatanList = $this->kegiatanWilayahModel->getByKabupatenAndAdmin($idKabupaten, $idAdminKabupaten);

        // PCL yang bisa dipilih (exclude PML dan admin yang terlibat di kegiatan yang sama)
        $availablePCL = $this->pclModel->getAvailablePCLForKegiatan($idKabupaten, $pml['id_kegiatan_wilayah'], $id_pml);

        return view('AdminSurveiKab/AssignPetugasSurvei/edit', [
            'title' => 'Edit Assign Petugas Survei',
            'active_menu' => 'assign-admin-kab',
            'pml' => $pml,
            'pcls' => $pcls,
            'kegiatanList' => $kegiatanList,
            'availablePCL' => $availablePCL,
            'isEdit' => true
        ]);
    }

    public function update($id_pml)
    {
        $sobatId = session()->get('sobat_id');
        if (!$sobatId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $admin = $this->adminKabModel->join('sipantau_user u', 'admin_survei_kabupaten.sobat_id = u.sobat_id')
            ->join('master_kabupaten k', 'u.id_kabupaten = k.id_kabupaten')
            ->select('admin_survei_kabupaten.*, u.id_kabupaten, u.nama_user, k.nama_kabupaten')
            ->where('admin_survei_kabupaten.sobat_id', $sobatId)
            ->first();

        if (!$admin) {
            return redirect()->to('/')->with('error', 'Anda tidak memiliki akses sebagai admin kabupaten');
        }

        $idKabupaten = $admin['id_kabupaten'];
        $idAdminKabupaten = $admin['id_admin_kabupaten'];

        // Ambil PML beserta id_kabupaten
        $pml = $this->pmlModel->db->table('pml p')
            ->select('p.*, kw.id_kabupaten, kw.id_kegiatan_wilayah')
            ->join('kegiatan_wilayah kw', 'kw.id_kegiatan_wilayah = p.id_kegiatan_wilayah')
            ->where('p.id_pml', $id_pml)
            ->get()
            ->getRowArray();

        if (!$pml || $pml['id_kabupaten'] != $idKabupaten) {
            return redirect()->back()->with('error', 'Data PML tidak ditemukan atau akses ditolak');
        }

        // Cek apakah kegiatan ini di-assign ke admin yang login
        $isAssigned = $this->kegiatanWilayahAdminModel->isAssigned($idAdminKabupaten, $pml['id_kegiatan_wilayah']);
        if (!$isAssigned) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke kegiatan ini');
        }

        // Validasi input
        $pmlTarget = (int) $this->request->getPost('pml_target');
        $kegiatanSurvei = $this->request->getPost('kegiatan_survei');
        $pclData = $this->request->getPost('pcl') ?? [];

        // Validasi kegiatan survei juga harus di-assign ke admin ini
        $isKegiatanAssigned = $this->kegiatanWilayahAdminModel->isAssigned($idAdminKabupaten, $kegiatanSurvei);
        if (!$isKegiatanAssigned) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke kegiatan yang dipilih');
        }

        // Update PML
        $this->pmlModel->update($id_pml, [
            'target' => $pmlTarget,
            'id_kegiatan_wilayah' => $kegiatanSurvei
        ]);

        // Hapus PCL lama beserta Kurva
        $oldPCLs = $this->pclModel->where('id_pml', $id_pml)->findAll();
        foreach ($oldPCLs as $pcl) {
            $this->kurvaModel->where('id_pcl', $pcl['id_pcl'])->delete();
        }
        $this->pclModel->where('id_pml', $id_pml)->delete();

        // Insert PCL baru & generate Kurva
        foreach ($pclData as $p) {
            if (!empty($p['sobat_id']) && !empty($p['target'])) {
                $idPCL = $this->pclModel->insert([
                    'id_pml' => $id_pml,
                    'sobat_id' => $p['sobat_id'],
                    'target' => $p['target']
                ], true);

                // Ambil data proses
                $kegiatan = $this->kegiatanWilayahModel->find($kegiatanSurvei);
                $detailProses = $this->prosesModel->find($kegiatan['id_kegiatan_detail_proses']);

                $this->generateKurvaPetugas(
                    $idPCL,
                    $p['target'],
                    $detailProses['persentase_target_awal'],
                    $detailProses['tanggal_mulai'],
                    $detailProses['tanggal_selesai_target'],
                    $detailProses['tanggal_selesai']
                );
            }
        }

        return redirect()->to('adminsurvei-kab/assign-petugas/detail/' . $id_pml)
            ->with('success', 'Data assignment berhasil diperbarui dan kurva petugas diperbarui.');
    }

    //  AJAX: Get Sisa Target Kegiatan Wilayah
    //  Menghitung sisa target dari kegiatan wilayah dikurangi total target PML yang sudah di-assign
    public function getSisaTargetKegiatanWilayah()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        $sobatId = session()->get('sobat_id');
        $admin = $this->adminKabModel->where('sobat_id', $sobatId)->first();

        if (!$admin) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Unauthorized',
                'csrf_hash' => csrf_hash()
            ]);
        }

        $idKegiatanWilayah = $this->request->getPost('id_kegiatan_wilayah');

        if (!$idKegiatanWilayah) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'ID Kegiatan Wilayah tidak ditemukan',
                'csrf_hash' => csrf_hash()
            ]);
        }

        // Cek apakah admin punya akses ke kegiatan ini
        $isAssigned = $this->kegiatanWilayahAdminModel->isAssigned($admin['id_admin_kabupaten'], $idKegiatanWilayah);
        if (!$isAssigned) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Anda tidak memiliki akses ke kegiatan ini',
                'csrf_hash' => csrf_hash()
            ]);
        }

        try {
            // Get kegiatan wilayah
            $kegiatanWilayah = $this->kegiatanWilayahModel->find($idKegiatanWilayah);

            if (!$kegiatanWilayah) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Kegiatan wilayah tidak ditemukan',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            if (!isset($kegiatanWilayah['target_wilayah'])) {
                log_message('error', 'Field target_wilayah tidak ditemukan. Data: ' . json_encode($kegiatanWilayah));

                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Field target tidak ditemukan di database. Silakan periksa struktur tabel.',
                    'csrf_hash' => csrf_hash(),
                    'debug_data' => array_keys($kegiatanWilayah)
                ]);
            }

            $targetWilayah = (int) $kegiatanWilayah['target_wilayah'];

            // Hitung total target PML yang sudah di-assign untuk kegiatan ini
            $totalTargetPML = $this->pmlModel->db->table('pml')
                ->selectSum('target', 'total_target')
                ->where('id_kegiatan_wilayah', $idKegiatanWilayah)
                ->get()
                ->getRow()
                ->total_target ?? 0;

            $sisaTarget = $targetWilayah - (int) $totalTargetPML;

            return $this->response->setJSON([
                'success' => true,
                'target_wilayah' => $targetWilayah,
                'target_terpakai' => (int) $totalTargetPML,
                'sisa_target' => max(0, $sisaTarget),
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error getSisaTargetKegiatanWilayah: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    //  AJAX: Get Available PML
    //  Exclude admin yang sedang login dan user yang sudah terlibat di kegiatan ini
    public function getAvailablePML()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        $sobatId = session()->get('sobat_id');
        $admin = $this->adminKabModel->db->table('admin_survei_kabupaten ask')
            ->select('ask.*, u.id_kabupaten')
            ->join('sipantau_user u', 'ask.sobat_id = u.sobat_id')
            ->where('ask.sobat_id', $sobatId)
            ->get()
            ->getRowArray();

        if (!$admin) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Unauthorized',
                'csrf_hash' => csrf_hash()
            ]);
        }

        $idKegiatanWilayah = $this->request->getPost('id_kegiatan_wilayah');

        // Cek akses admin ke kegiatan ini
        $isAssigned = $this->kegiatanWilayahAdminModel->isAssigned($admin['id_admin_kabupaten'], $idKegiatanWilayah);
        if (!$isAssigned) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Anda tidak memiliki akses ke kegiatan ini',
                'csrf_hash' => csrf_hash()
            ]);
        }

        // Get available PML (exclude admin yang sedang login dan yang terlibat di kegiatan ini)
        $users = $this->pmlModel->getAvailablePMLForKegiatan($admin['id_kabupaten'], $idKegiatanWilayah, $sobatId);

        return $this->response->setJSON([
            'success' => true,
            'data' => $users,
            'csrf_hash' => csrf_hash()
        ]);
    }

    //  AJAX: Get Available PCL
    //  Exclude PML yang dipilih dan admin yang terlibat di kegiatan ini
    public function getAvailablePCL()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        $sobatId = session()->get('sobat_id');
        $admin = $this->adminKabModel->db->table('admin_survei_kabupaten ask')
            ->select('ask.*, u.id_kabupaten')
            ->join('sipantau_user u', 'ask.sobat_id = u.sobat_id')
            ->where('ask.sobat_id', $sobatId)
            ->get()
            ->getRowArray();

        if (!$admin) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Unauthorized',
                'csrf_hash' => csrf_hash()
            ]);
        }

        $idPML = $this->request->getPost('id_pml');
        $pmlSobatId = $this->request->getPost('pml_sobat_id'); // PML yang dipilih
        $idKegiatanWilayah = $this->request->getPost('id_kegiatan_wilayah');

        // Cek akses admin ke kegiatan ini
        if ($idKegiatanWilayah) {
            $isAssigned = $this->kegiatanWilayahAdminModel->isAssigned($admin['id_admin_kabupaten'], $idKegiatanWilayah);
            if (!$isAssigned) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Anda tidak memiliki akses ke kegiatan ini',
                    'csrf_hash' => csrf_hash()
                ]);
            }
        }

        // Get available PCL (exclude PML yang dipilih dan yang terlibat di kegiatan ini)
        $users = $this->pclModel->getAvailablePCLForKegiatan(
            $admin['id_kabupaten'],
            $idKegiatanWilayah,
            $idPML,
            $pmlSobatId
        );

        return $this->response->setJSON([
            'success' => true,
            'data' => $users,
            'csrf_hash' => csrf_hash()
        ]);
    }

    //  AJAX: Get Sisa Target PML
    public function getSisaTargetPML()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        $idPML = $this->request->getPost('id_pml');
        $excludePCLId = $this->request->getPost('exclude_pcl_id');

        $sisaTarget = $this->pclModel->getSisaTargetPML($idPML, $excludePCLId);

        return $this->response->setJSON([
            'success' => true,
            'sisa_target' => $sisaTarget,
            'csrf_hash' => csrf_hash()
        ]);
    }

    //  Store Assignment - Updated with validation
    public function store()
    {
        $sobatId = session()->get('sobat_id');

        if (!$sobatId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $admin = $this->adminKabModel->db->table('admin_survei_kabupaten ask')
            ->select('ask.*, u.id_kabupaten')
            ->join('sipantau_user u', 'ask.sobat_id = u.sobat_id')
            ->where('ask.sobat_id', $sobatId)
            ->get()
            ->getRowArray();

        if (!$admin) {
            return redirect()->to('/')->with('error', 'Anda tidak memiliki akses sebagai admin kabupaten');
        }

        $rules = [
            'kegiatan_survei' => 'required|numeric',
            'pml_sobat_id' => 'required|numeric',
            'pml_target' => 'required|numeric|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idKegiatanWilayah = $this->request->getPost('kegiatan_survei');
        $pmlSobatId = $this->request->getPost('pml_sobat_id');
        $pmlTarget = (int) $this->request->getPost('pml_target');
        $pclData = $this->request->getPost('pcl');

        // Validasi: Cek apakah admin punya akses ke kegiatan ini
        $isAssigned = $this->kegiatanWilayahAdminModel->isAssigned($admin['id_admin_kabupaten'], $idKegiatanWilayah);
        if (!$isAssigned) {
            return redirect()->back()->withInput()->with('error', 'Anda tidak memiliki akses ke kegiatan yang dipilih');
        }

        // Validasi: PML tidak boleh admin yang sedang login
        if ($pmlSobatId == $sobatId) {
            return redirect()->back()->withInput()->with('error', 'Anda tidak dapat mengassign diri sendiri sebagai PML');
        }

        // Validasi 1: Cek sisa target kegiatan wilayah
        $kegiatanWilayah = $this->kegiatanWilayahModel->find($idKegiatanWilayah);
        if (!$kegiatanWilayah) {
            return redirect()->back()->withInput()->with('error', 'Kegiatan wilayah tidak ditemukan');
        }

        $targetWilayah = (int) $kegiatanWilayah['target_wilayah'];

        // Hitung total target PML yang sudah ada
        $totalTargetPMLExisting = $this->pmlModel->db->table('pml')
            ->selectSum('target', 'total_target')
            ->where('id_kegiatan_wilayah', $idKegiatanWilayah)
            ->get()
            ->getRow()
            ->total_target ?? 0;

        $sisaTargetWilayah = $targetWilayah - (int) $totalTargetPMLExisting;

        if ($pmlTarget > $sisaTargetWilayah) {
            return redirect()->back()->withInput()->with(
                'error',
                "Target PML ($pmlTarget) melebihi sisa target kegiatan wilayah yang tersedia ($sisaTargetWilayah)"
            );
        }

        // Validasi 2: total target PCL tidak melebihi target PML
        $totalTargetPCL = 0;
        if ($pclData && is_array($pclData)) {
            foreach ($pclData as $pcl) {
                if (!empty($pcl['target'])) {
                    $totalTargetPCL += (int) $pcl['target'];
                }
            }
        }

        if ($totalTargetPCL > $pmlTarget) {
            return redirect()->back()->withInput()->with(
                'error',
                "Total target PCL ($totalTargetPCL) melebihi target PML ($pmlTarget)"
            );
        }

        $this->pmlModel->db->transStart();

        try {
            // Insert PML
            $pmlId = $this->pmlModel->insert([
                'sobat_id' => $pmlSobatId,
                'id_kegiatan_wilayah' => $idKegiatanWilayah,
                'target' => $pmlTarget,
                'status_approval' => 0,
                'tanggal_approval' => null
            ]);

            // Get kegiatan detail proses untuk mendapatkan tanggal
            $detailProses = $this->prosesModel->find($kegiatanWilayah['id_kegiatan_detail_proses']);

            // Insert PCL dan generate Kurva S
            if ($pclData && is_array($pclData)) {
                foreach ($pclData as $pcl) {
                    if (!empty($pcl['sobat_id']) && !empty($pcl['target'])) {
                        $pclId = $this->pclModel->insert([
                            'sobat_id' => $pcl['sobat_id'],
                            'id_pml' => $pmlId,
                            'target' => $pcl['target'],
                            'status_approval' => 0,
                            'tanggal_approval' => null
                        ]);

                        // Generate Kurva S untuk PCL
                        $this->generateKurvaPetugas(
                            $pclId,
                            $pcl['target'],
                            $detailProses['persentase_target_awal'],
                            $detailProses['tanggal_mulai'],
                            $detailProses['tanggal_selesai_target'],
                            $detailProses['tanggal_selesai']
                        );
                    }
                }
            }

            $this->pmlModel->db->transComplete();

            if ($this->pmlModel->db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data');
            }

            return redirect()->to('/adminsurvei-kab/assign-petugas')
                ->with('success', 'Berhasil assign petugas survei dan generate Kurva S');

        } catch (\Exception $e) {
            $this->pmlModel->db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    //  Generate Kurva S untuk PCL
    private function generateKurvaPetugas($idPCL, $target, $persenAwal, $tanggalMulai, $tanggal100, $tanggalSelesai)
    {
        $totalTarget = (int) $target;
        $persenAwal = (float) $persenAwal;

        $start = new DateTime($tanggalMulai);
        $tgl100 = new DateTime($tanggal100);
        $end = new DateTime($tanggalSelesai);
        $end->modify('+1 day');

        $interval = new DateInterval('P1D');
        $periodTotal = iterator_to_array(new DatePeriod($start, $interval, $end));

        // Ambil hanya hari kerja (Senin–Jumat)
        $workdays = array_filter($periodTotal, fn($d) => $d->format('N') <= 5);
        $workdays = array_values($workdays);

        // Filter hanya sampai tanggal 100%
        $workdaysUntil100 = array_filter($workdays, fn($d) => $d <= $tgl100);
        $workdaysUntil100 = array_values($workdaysUntil100);

        $daysSigmoid = max(count($workdaysUntil100), 2);
        $k = 8;   // kelengkungan
        $x0 = 0.5; // titik tengah

        // Hitung batas normalisasi sigmoid
        $sigmoidMin = 1 / (1 + exp(-$k * (0 - $x0)));
        $sigmoidMax = 1 / (1 + exp(-$k * (1 - $x0)));

        $workdayData = [];
        foreach ($workdaysUntil100 as $i => $date) {
            $progress = $i / ($daysSigmoid - 1);

            // Normalisasi sigmoid
            $sigmoid = 1 / (1 + exp(-$k * ($progress - $x0)));
            $normalizedSigmoid = ($sigmoid - $sigmoidMin) / ($sigmoidMax - $sigmoidMin);

            $kumulatifPersen = $persenAwal + (100 - $persenAwal) * $normalizedSigmoid;
            if ($kumulatifPersen > 100)
                $kumulatifPersen = 100;

            $workdayData[$date->format('Y-m-d')] = $kumulatifPersen;
        }

        $kumulatifAbsolut = 0;
        $insertData = [];

        // Tahap 1: hitung semua data
        foreach ($workdaysUntil100 as $date) {
            $currentDate = $date->format('Y-m-d');
            $kumulatifPersen = $workdayData[$currentDate];
            $harianAbsolut = round(($totalTarget * ($kumulatifPersen / 100)) - $kumulatifAbsolut);
            $kumulatifAbsolut += $harianAbsolut;

            $insertData[] = [
                'tanggal' => $currentDate,
                'persen' => round($kumulatifPersen, 2),
                'harian' => $harianAbsolut,
                'kumulatif' => $kumulatifAbsolut
            ];
        }

        // Koreksi hari terakhir agar total tepat = totalTarget
        $selisih = $totalTarget - $kumulatifAbsolut;
        if (!empty($insertData) && $selisih !== 0) {
            $insertData[count($insertData) - 1]['harian'] += $selisih;
            $insertData[count($insertData) - 1]['kumulatif'] += $selisih;
            $kumulatifAbsolut = $totalTarget;
        }

        // Simpan ke DB
        foreach ($insertData as $row) {
            $this->kurvaModel->insert([
                'id_pcl' => $idPCL,
                'tanggal_target' => $row['tanggal'],
                'target_persen_kumulatif' => $row['persen'],
                'target_harian_absolut' => $row['harian'],
                'target_kumulatif_absolut' => $row['kumulatif'],
                'is_hari_kerja' => 1
            ]);
        }

        // Tahap 2: setelah tanggal100 → mendatar
        $workdaysAfter100 = array_filter($workdays, fn($d) => $d > $tgl100);
        $workdaysAfter100 = array_values($workdaysAfter100);

        foreach ($workdaysAfter100 as $date) {
            $currentDate = $date->format('Y-m-d');
            $this->kurvaModel->insert([
                'id_pcl' => $idPCL,
                'tanggal_target' => $currentDate,
                'target_persen_kumulatif' => 100,
                'target_harian_absolut' => 0,
                'target_kumulatif_absolut' => $totalTarget,
                'is_hari_kerja' => 1
            ]);
        }

        log_message('info', "Kurva S PCL dibuat untuk id_pcl=$idPCL (total=$totalTarget)");
    }

    //  Detail PML dan PCL-nya
    public function detail($idPML)
    {
        $sobatId = session()->get('sobat_id');

        if (!$sobatId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $admin = $this->adminKabModel->db->table('admin_survei_kabupaten ask')
            ->select('ask.*, u.id_kabupaten')
            ->join('sipantau_user u', 'ask.sobat_id = u.sobat_id')
            ->where('ask.sobat_id', $sobatId)
            ->get()
            ->getRowArray();

        if (!$admin) {
            return redirect()->to('/')->with('error', 'Anda tidak memiliki akses sebagai admin kabupaten');
        }

        $pml = $this->pmlModel->getPMLWithDetails($idPML);

        if (!$pml) {
            return redirect()->to('/adminsurvei-kab/assign-petugas')->with('error', 'Data PML tidak ditemukan');
        }

        if ($admin['id_kabupaten'] != $pml['id_kabupaten']) {
            return redirect()->to('/adminsurvei-kab/assign-petugas')->with('error', 'Akses ditolak');
        }

        // Cek apakah kegiatan ini di-assign ke admin yang login
        $isAssigned = $this->kegiatanWilayahAdminModel->isAssigned($admin['id_admin_kabupaten'], $pml['id_kegiatan_wilayah']);
        if (!$isAssigned) {
            return redirect()->to('/adminsurvei-kab/assign-petugas')->with('error', 'Anda tidak memiliki akses ke kegiatan ini');
        }

        $dataPCL = $this->pclModel->getPCLByPML($idPML);

        $data = [
            'title' => 'Detail Assignment PML',
            'active_menu' => 'assign-admin-kab',
            'pml' => $pml,
            'dataPCL' => $dataPCL
        ];

        return view('AdminSurveiKab/AssignPetugasSurvei/detail', $data);
    }

    //  Delete PML beserta PCL dan Kurva-nya
    public function delete($idPML)
    {
        $sobatId = session()->get('sobat_id');

        if (!$sobatId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $admin = $this->adminKabModel->db->table('admin_survei_kabupaten ask')
            ->select('ask.*, u.id_kabupaten')
            ->join('sipantau_user u', 'ask.sobat_id = u.sobat_id')
            ->where('ask.sobat_id', $sobatId)
            ->get()
            ->getRowArray();

        if (!$admin) {
            return redirect()->to('/')->with('error', 'Anda tidak memiliki akses');
        }

        $pml = $this->pmlModel->getPMLWithDetails($idPML);

        if (!$pml) {
            return redirect()->to('/adminsurvei-kab/assign-petugas')->with('error', 'Data tidak ditemukan');
        }

        if ($admin['id_kabupaten'] != $pml['id_kabupaten']) {
            return redirect()->to('/adminsurvei-kab/assign-petugas')->with('error', 'Akses ditolak');
        }

        // Cek apakah kegiatan ini di-assign ke admin yang login
        $isAssigned = $this->kegiatanWilayahAdminModel->isAssigned($admin['id_admin_kabupaten'], $pml['id_kegiatan_wilayah']);
        if (!$isAssigned) {
            return redirect()->to('/adminsurvei-kab/assign-petugas')->with('error', 'Anda tidak memiliki akses ke kegiatan ini');
        }

        // Delete dengan cascade (PCL dan Kurva akan terhapus otomatis jika FK ON DELETE CASCADE)
        if ($this->pmlModel->deletePMLWithPCL($idPML)) {
            return redirect()->to('/adminsurvei-kab/assign-petugas')
                ->with('success', 'Berhasil menghapus assignment PML, PCL, dan Kurva S');
        }

        return redirect()->to('/adminsurvei-kab/assign-petugas')->with('error', 'Gagal menghapus data');
    }

    public function detailPML($id_pml)
    {
        $sobatId = session()->get('sobat_id');

        if (!$sobatId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $admin = $this->adminKabModel->db->table('admin_survei_kabupaten ask')
            ->select('ask.*, u.id_kabupaten')
            ->join('sipantau_user u', 'ask.sobat_id = u.sobat_id')
            ->where('ask.sobat_id', $sobatId)
            ->get()
            ->getRowArray();

        if (!$admin) {
            return redirect()->to('/')->with('error', 'Anda tidak memiliki akses');
        }

        // Ambil data PML
        $pml = $this->pmlModel
            ->select('pml.*, 
                      u.nama_user AS nama_pml, u.email AS email_pml,
                      kw.id_kegiatan_wilayah, 
                      kd.nama_kegiatan_detail, 
                      kdp.nama_kegiatan_detail_proses')
            ->join('sipantau_user u', 'u.sobat_id = pml.sobat_id')
            ->join('kegiatan_wilayah kw', 'kw.id_kegiatan_wilayah = pml.id_kegiatan_wilayah')
            ->join('master_kegiatan_detail_proses kdp', 'kdp.id_kegiatan_detail_proses = kw.id_kegiatan_detail_proses')
            ->join('master_kegiatan_detail kd', 'kd.id_kegiatan_detail = kdp.id_kegiatan_detail')
            ->where('pml.id_pml', $id_pml)
            ->first();

        if (!$pml) {
            return redirect()->back()->with('error', 'Data PML tidak ditemukan.');
        }

        // Cek apakah kegiatan ini di-assign ke admin yang login
        $isAssigned = $this->kegiatanWilayahAdminModel->isAssigned($admin['id_admin_kabupaten'], $pml['id_kegiatan_wilayah']);
        if (!$isAssigned) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke kegiatan ini');
        }

        // Ambil semua PCL di bawah PML ini
        $pclList = $this->pclModel
            ->select('pcl.*, u.nama_user AS nama_pcl, u.email AS email_pcl')
            ->join('sipantau_user u', 'u.sobat_id = pcl.sobat_id')
            ->where('pcl.id_pml', $id_pml)
            ->findAll();

        // Ringkasan sederhana
        $summary = [
            'total_pcl' => count($pclList),
            'total_target' => array_sum(array_column($pclList, 'target'))
        ];

        return view('AdminSurveiKab/AssignPetugasSurvei/detail', [
            'pml' => $pml,
            'active_menu' => 'assign-admin-kab',
            'pclList' => $pclList,
            'summary' => $summary
        ]);
    }

    public function pclDetail($id_pcl)
    {
        $sobatId = session()->get('sobat_id');

        if (!$sobatId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $admin = $this->adminKabModel->db->table('admin_survei_kabupaten ask')
            ->select('ask.*, u.id_kabupaten')
            ->join('sipantau_user u', 'ask.sobat_id = u.sobat_id')
            ->where('ask.sobat_id', $sobatId)
            ->get()
            ->getRowArray();

        if (!$admin) {
            return redirect()->to('/')->with('error', 'Anda tidak memiliki akses');
        }

        // Get PCL detail
        $pclDetail = $this->pclModel->db->table('pcl')
            ->select('pcl.*, 
                 u.nama_user as nama_pcl, 
                 u.email, 
                 u.hp,
                 u.id_kabupaten,
                 u_pml.nama_user as nama_pml,
                 pml.id_pml,
                 kw.id_kegiatan_wilayah,
                 mk.nama_kabupaten,
                 mkdp.nama_kegiatan_detail_proses,
                 mkdp.tanggal_mulai,
                 mkdp.tanggal_selesai,
                 mkd.nama_kegiatan_detail')
            ->join('sipantau_user u', 'pcl.sobat_id = u.sobat_id')
            ->join('pml', 'pcl.id_pml = pml.id_pml')
            ->join('sipantau_user u_pml', 'pml.sobat_id = u_pml.sobat_id')
            ->join('kegiatan_wilayah kw', 'pml.id_kegiatan_wilayah = kw.id_kegiatan_wilayah')
            ->join('master_kabupaten mk', 'kw.id_kabupaten = mk.id_kabupaten')
            ->join('master_kegiatan_detail_proses mkdp', 'kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses')
            ->join('master_kegiatan_detail mkd', 'mkdp.id_kegiatan_detail = mkd.id_kegiatan_detail')
            ->where('pcl.id_pcl', $id_pcl)
            ->get()
            ->getRowArray();

        if (!$pclDetail) {
            return redirect()->back()->with('error', 'Data PCL tidak ditemukan.');
        }

        // Validasi kabupaten
        if ($pclDetail['id_kabupaten'] != $admin['id_kabupaten']) {
            return redirect()->to('unauthorized')->with('error', 'Anda tidak memiliki akses ke data ini');
        }

        // Cek apakah kegiatan ini di-assign ke admin yang login
        $isAssigned = $this->kegiatanWilayahAdminModel->isAssigned($admin['id_admin_kabupaten'], $pclDetail['id_kegiatan_wilayah']);
        if (!$isAssigned) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke kegiatan ini');
        }

        // Load additional models
        $pantauProgressModel = new \App\Models\PantauProgressModel();
        $kurvaPetugasModel = new \App\Models\KurvaPetugasModel();

        // Get realisasi data
        $realisasi = $pantauProgressModel
            ->select('COALESCE(MAX(jumlah_realisasi_kumulatif), 0) as total_realisasi')
            ->where('id_pcl', $id_pcl)
            ->first();

        $realisasiKumulatif = (int) ($realisasi['total_realisasi'] ?? 0);
        $target = (int) $pclDetail['target'];
        $persentase = $target > 0 ? round(($realisasiKumulatif / $target) * 100, 2) : 0;
        $selisih = $target - $realisasiKumulatif;

        // Get Kurva S data
        $kurvaData = $this->getKurvaDataPCL($id_pcl, $pclDetail, $pantauProgressModel, $kurvaPetugasModel);

        $from = $this->request->getGet('from');
        $data = [
            'title' => 'Detail Laporan PCL',
            'active_menu' => 'assign-admin-kab',
            'pcl' => $pclDetail,
            'target' => $target,
            'realisasi' => $realisasiKumulatif,
            'persentase' => $persentase,
            'selisih' => $selisih,
            'kurvaData' => $kurvaData,
            'idPCL' => $id_pcl,
            'from' => $from
        ];

        return view('AdminSurveiKab/AssignPetugasSurvei/kurva_s', $data);
    }

    private function getKurvaDataPCL($idPCL, $pclDetail, $pantauProgressModel, $kurvaPetugasModel)
    {
        // Get kurva target
        $kurvaTarget = $kurvaPetugasModel
            ->where('id_pcl', $idPCL)
            ->orderBy('tanggal_target', 'ASC')
            ->findAll();

        // Get realisasi harian
        $realisasiHarian = $this->pmlModel->db->query("
        SELECT 
            DATE(created_at) as tanggal,
            SUM(jumlah_realisasi_absolut) as realisasi_harian
        FROM pantau_progress
        WHERE id_pcl = ?
        GROUP BY DATE(created_at)
        ORDER BY DATE(created_at) ASC
    ", [$idPCL])->getResultArray();

        // Build realisasi lookup
        $realisasiLookup = [];
        foreach ($realisasiHarian as $item) {
            $realisasiLookup[$item['tanggal']] = (int) $item['realisasi_harian'];
        }

        // Format data untuk chart
        $labels = [];
        $targetData = [];
        $realisasiData = [];
        $realisasiKumulatif = 0;

        foreach ($kurvaTarget as $item) {
            $tanggal = $item['tanggal_target'];
            $labels[] = date('d M', strtotime($tanggal));
            $targetData[] = (int) $item['target_kumulatif_absolut'];

            if (isset($realisasiLookup[$tanggal])) {
                $realisasiKumulatif += $realisasiLookup[$tanggal];
            }
            $realisasiData[] = $realisasiKumulatif;
        }

        return [
            'labels' => $labels,
            'target' => $targetData,
            'realisasi' => $realisasiData,
            'config' => [
                'tanggal_mulai' => date('d M', strtotime($pclDetail['tanggal_mulai'])),
                'tanggal_selesai' => date('d M', strtotime($pclDetail['tanggal_selesai']))
            ]
        ];
    }
}