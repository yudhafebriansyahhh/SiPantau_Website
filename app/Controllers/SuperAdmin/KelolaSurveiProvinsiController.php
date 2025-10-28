<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\AdminSurveiProvinsiModel;
use App\Models\UserModel;
use App\Models\MasterKegiatanDetailModel;
use App\Models\MasterKegiatanDetailAdminModel;
use App\Models\MasterKegiatanDetailProsesModel;

class KelolaSurveiProvinsiController extends BaseController
{
    protected $adminProvinsiModel;
    protected $userModel;
    protected $kegiatanDetailModel;
    protected $kegiatanDetailAdminModel;
    protected $kegiatanDetailProsesModel;
    protected $db;

    public function __construct()
    {
        $this->adminProvinsiModel = new AdminSurveiProvinsiModel();
        $this->userModel = new UserModel();
        $this->kegiatanDetailModel = new MasterKegiatanDetailModel();
        $this->kegiatanDetailAdminModel = new MasterKegiatanDetailAdminModel();
        $this->kegiatanDetailProsesModel = new MasterKegiatanDetailProsesModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Halaman index - daftar admin dengan kegiatan mereka
     */
    public function index()
    {
        $search = $this->request->getGet('search') ?? '';
        $roleFilter = $this->request->getGet('role') ?? '';
        
        // Get all admin survei provinsi dengan informasi user dan kegiatan
        $builder = $this->db->table('admin_survei_provinsi asp')
            ->select('asp.id_admin_provinsi, asp.sobat_id, u.nama_user, u.email, u.hp, u.is_active, k.nama_kabupaten')
            ->join('sipantau_user u', 'asp.sobat_id = u.sobat_id')
            ->join('master_kabupaten k', 'u.id_kabupaten = k.id_kabupaten', 'left')
            ->orderBy('u.nama_user', 'ASC');
        
        if (!empty($search)) {
            $builder->groupStart()
                ->like('u.nama_user', $search)
                ->orLike('u.email', $search)
                ->orLike('u.hp', $search)
                ->groupEnd();
        }
        
        $adminList = $builder->get()->getResultArray();
        
        // Get kegiatan untuk setiap admin
        foreach ($adminList as &$admin) {
            $kegiatan = $this->db->table('master_kegiatan_detail_admin mkda')
                ->select('mkd.id_kegiatan_detail, mkd.nama_kegiatan_detail, mk.nama_kegiatan, mkd.periode, mkd.tahun, mkd.satuan, mkd.tanggal_mulai, mkd.tanggal_selesai')
                ->join('master_kegiatan_detail mkd', 'mkda.id_kegiatan_detail = mkd.id_kegiatan_detail')
                ->join('master_kegiatan mk', 'mkd.id_kegiatan = mk.id_kegiatan')
                ->where('mkda.id_admin_provinsi', $admin['id_admin_provinsi'])
                ->get()
                ->getResultArray();
            
            $admin['kegiatan'] = $kegiatan;
            $admin['jumlah_kegiatan'] = count($kegiatan);
            
            // Get role names
            $userRoles = $this->userModel->getUserWithRoles($admin['sobat_id']);
            $admin['role_names'] = $userRoles['role_names'] ?? [];
        }

        $data = [
            'title' => 'Kelola Admin Survei Provinsi',
            'active_menu' => 'kelola-admin-surveyprov',
            'admin_list' => $adminList,
            'search' => $search
        ];

        return view('SuperAdmin/KelolaAdminSurvey/index', $data);
    }

    /**
     * Halaman assign/edit admin survei provinsi ke kegiatan
     */
    public function assign($idAdminProvinsi = null)
    {
        $isEdit = !is_null($idAdminProvinsi);
        $admin = null;
        $assignedIds = [];
        
        if ($isEdit) {
            // Get admin info
            $admin = $this->db->table('admin_survei_provinsi asp')
                ->select('asp.*, u.nama_user, u.email, u.hp')
                ->join('sipantau_user u', 'asp.sobat_id = u.sobat_id')
                ->where('asp.id_admin_provinsi', $idAdminProvinsi)
                ->get()
                ->getRowArray();
                
            if (!$admin) {
                return redirect()->to(base_url('superadmin/kelola-admin-surveyprov'))
                    ->with('error', 'Admin tidak ditemukan');
            }
            
            // Get assigned kegiatan
            $assigned = $this->db->table('master_kegiatan_detail_admin')
                ->select('id_kegiatan_detail')
                ->where('id_admin_provinsi', $idAdminProvinsi)
                ->get()
                ->getResultArray();
            
            $assignedIds = array_column($assigned, 'id_kegiatan_detail');
        }
        
        // Get all active users (untuk mode create)
        $users = $this->db->table('sipantau_user')
            ->select('sobat_id, nama_user, email, hp')
            ->where('is_active', 1)
            ->orderBy('nama_user', 'ASC')
            ->get()
            ->getResultArray();
        
        // Get all kegiatan detail
        $kegiatanDetails = $this->kegiatanDetailModel->getWithKegiatan();

        $data = [
            'title' => $isEdit ? 'Edit Assignment Admin Survei Provinsi' : 'Assign Admin Survei Provinsi',
            'active_menu' => 'kelola-admin-surveyprov',
            'users' => $users,
            'kegiatan_details' => $kegiatanDetails,
            'is_edit' => $isEdit,
            'admin' => $admin,
            'assigned_ids' => $assignedIds
        ];

        return view('SuperAdmin/KelolaAdminSurvey/assign', $data);
    }

    /**
     * Process assign admin survei provinsi
     */
    public function storeAssign()
    {
        $sobatId = $this->request->getPost('sobat_id');
        $kegiatanDetails = $this->request->getPost('kegiatan_details');
        
        // Validation
        if (empty($sobatId)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Silakan pilih user');
        }
        
        if (empty($kegiatanDetails) || !is_array($kegiatanDetails)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Silakan pilih minimal satu kegiatan detail');
        }

        // Check apakah user exists
        $user = $this->userModel->find($sobatId);
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan');
        }

        $this->db->transStart();

        // Check apakah user sudah menjadi admin provinsi
        $existingAdmin = $this->adminProvinsiModel->where('sobat_id', $sobatId)->first();
        
        if ($existingAdmin) {
            $idAdminProvinsi = $existingAdmin['id_admin_provinsi'];
        } else {
            // Jika belum, buat entry baru di admin_survei_provinsi
            $this->db->table('admin_survei_provinsi')->insert(['sobat_id' => $sobatId]);
            $idAdminProvinsi = $this->db->insertID();
        }

        // Assign multiple kegiatan detail
        $insertedCount = 0;
        foreach ($kegiatanDetails as $idKegiatanDetail) {
            // Check apakah admin sudah di-assign ke kegiatan ini
            $existingAssignment = $this->db->table('master_kegiatan_detail_admin')
                ->where([
                    'id_admin_provinsi' => $idAdminProvinsi,
                    'id_kegiatan_detail' => $idKegiatanDetail
                ])
                ->get()
                ->getRowArray();

            if (!$existingAssignment) {
                $this->db->table('master_kegiatan_detail_admin')->insert([
                    'id_admin_provinsi' => $idAdminProvinsi,
                    'id_kegiatan_detail' => $idKegiatanDetail
                ]);
                $insertedCount++;
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal melakukan assignment');
        }

        $message = $insertedCount > 0 
            ? "Admin berhasil di-assign ke {$insertedCount} kegiatan" 
            : "Admin sudah di-assign ke semua kegiatan yang dipilih";

        return redirect()->to(base_url('superadmin/kelola-admin-surveyprov'))
            ->with('success', $message);
    }

    /**
     * Update assignment admin
     */
    public function update($idAdminProvinsi)
    {
        $kegiatanDetails = $this->request->getPost('kegiatan_details');
        
        // Validation
        if (empty($kegiatanDetails) || !is_array($kegiatanDetails)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Silakan pilih minimal satu kegiatan detail');
        }

        $this->db->transStart();

        // Hapus semua assignment lama
        $this->db->table('master_kegiatan_detail_admin')
            ->where('id_admin_provinsi', $idAdminProvinsi)
            ->delete();

        // Insert assignment baru
        foreach ($kegiatanDetails as $idKegiatanDetail) {
            $this->db->table('master_kegiatan_detail_admin')->insert([
                'id_admin_provinsi' => $idAdminProvinsi,
                'id_kegiatan_detail' => $idKegiatanDetail
            ]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal melakukan update');
        }

        return redirect()->to(base_url('superadmin/kelola-admin-surveyprov'))
            ->with('success', 'Assignment berhasil diupdate');
    }

    /**
     * Delete assignment admin dari kegiatan tertentu
     */
    public function deleteAssignment()
    {
        $idAdminProvinsi = $this->request->getPost('id_admin_provinsi');
        $idKegiatanDetail = $this->request->getPost('id_kegiatan_detail');
        
        if (!$idAdminProvinsi || !$idKegiatanDetail) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }

        $this->db->transStart();

        // Hapus assignment
        $this->db->table('master_kegiatan_detail_admin')
            ->where([
                'id_admin_provinsi' => $idAdminProvinsi,
                'id_kegiatan_detail' => $idKegiatanDetail
            ])
            ->delete();

        // Check apakah admin masih memiliki kegiatan lain
        $remainingAssignments = $this->db->table('master_kegiatan_detail_admin')
            ->where('id_admin_provinsi', $idAdminProvinsi)
            ->countAllResults();

        // Jika tidak ada kegiatan lagi, hapus dari admin_survei_provinsi
        if ($remainingAssignments == 0) {
            $this->db->table('admin_survei_provinsi')
                ->where('id_admin_provinsi', $idAdminProvinsi)
                ->delete();
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus assignment'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Assignment berhasil dihapus'
        ]);
    }
    
    /**
     * Delete admin completely
     */
    public function delete($idAdminProvinsi)
    {
        $this->db->transStart();

        // Hapus semua assignment
        $this->db->table('master_kegiatan_detail_admin')
            ->where('id_admin_provinsi', $idAdminProvinsi)
            ->delete();

        // Hapus dari admin_survei_provinsi
        $this->db->table('admin_survei_provinsi')
            ->where('id_admin_provinsi', $idAdminProvinsi)
            ->delete();

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus admin'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Admin berhasil dihapus'
        ]);
    }

    /**
     * View detail admin dengan daftar kegiatan dan progress
     */
    public function detail($idAdminProvinsi)
    {
        // Get admin info
        $admin = $this->db->table('admin_survei_provinsi asp')
            ->select('asp.*, u.nama_user, u.email, u.hp, k.nama_kabupaten')
            ->join('sipantau_user u', 'asp.sobat_id = u.sobat_id')
            ->join('master_kabupaten k', 'u.id_kabupaten = k.id_kabupaten', 'left')
            ->where('asp.id_admin_provinsi', $idAdminProvinsi)
            ->get()
            ->getRowArray();

        if (!$admin) {
            return redirect()->to(base_url('superadmin/kelola-admin-surveyprov'))
                ->with('error', 'Admin tidak ditemukan');
        }

        // Get kegiatan yang di-assign
        $kegiatan = $this->db->table('master_kegiatan_detail_admin mkda')
            ->select('mkd.*, mk.nama_kegiatan')
            ->join('master_kegiatan_detail mkd', 'mkda.id_kegiatan_detail = mkd.id_kegiatan_detail')
            ->join('master_kegiatan mk', 'mkd.id_kegiatan = mk.id_kegiatan')
            ->where('mkda.id_admin_provinsi', $idAdminProvinsi)
            ->orderBy('mkd.tahun', 'DESC')
            ->get()
            ->getResultArray();

        // Get proses untuk setiap kegiatan dengan progress
        foreach ($kegiatan as &$k) {
            // Get all proses
            $prosesList = $this->db->table('master_kegiatan_detail_proses')
                ->where('id_kegiatan_detail', $k['id_kegiatan_detail'])
                ->orderBy('tanggal_mulai', 'ASC')
                ->get()
                ->getResultArray();
            
            // Calculate progress untuk setiap proses
            $totalProses = count($prosesList);
            $completedProses = 0;
            $totalProgress = 0;
            
            foreach ($prosesList as &$proses) {
                // Hitung progress berdasarkan tanggal
                if (!empty($proses['tanggal_mulai']) && !empty($proses['tanggal_selesai_target'])) {
                    $today = date('Y-m-d');
                    $start = strtotime($proses['tanggal_mulai']);
                    $end = strtotime($proses['tanggal_selesai_target']);
                    $current = strtotime($today);
                    
                    if ($current < $start) {
                        $proses['progress'] = 0;
                        $proses['status'] = 'Belum Dimulai';
                        $proses['status_class'] = 'gray';
                    } elseif ($current > $end) {
                        $proses['progress'] = 100;
                        $proses['status'] = 'Selesai';
                        $proses['status_class'] = 'green';
                        $completedProses++;
                    } else {
                        $totalDays = ($end - $start) / (60 * 60 * 24);
                        $elapsedDays = ($current - $start) / (60 * 60 * 24);
                        $proses['progress'] = min(100, round(($elapsedDays / $totalDays) * 100, 1));
                        $proses['status'] = 'Sedang Berlangsung';
                        $proses['status_class'] = 'blue';
                    }
                } else {
                    $proses['progress'] = 0;
                    $proses['status'] = 'Tidak Ada Target';
                    $proses['status_class'] = 'gray';
                }
                
                $totalProgress += $proses['progress'];
            }
            
            $k['proses_list'] = $prosesList;
            $k['total_proses'] = $totalProses;
            $k['completed_proses'] = $completedProses;
            $k['overall_progress'] = $totalProses > 0 ? round($totalProgress / $totalProses, 1) : 0;
        }

        $data = [
            'title' => 'Detail Admin Survei Provinsi',
            'active_menu' => 'kelola-admin-surveyprov',
            'admin' => $admin,
            'kegiatan' => $kegiatan
        ];

        return view('SuperAdmin/KelolaAdminSurvey/detail', $data);
    }
}