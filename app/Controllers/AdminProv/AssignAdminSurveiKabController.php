<?php

namespace App\Controllers\AdminProv;

use App\Controllers\BaseController;
use App\Models\AdminSurveiKabupatenModel;
use App\Models\UserModel;
use App\Models\MasterKabModel;
use App\Models\MasterKegiatanWilayahModel;
use App\Models\KegiatanWilayahAdminModel;

class AssignAdminSurveiKabController extends BaseController
{
    protected $adminKabupatenModel;
    protected $userModel;
    protected $masterKabModel;
    protected $kegiatanWilayahModel;
    protected $kegiatanWilayahAdminModel;
    protected $db;
    protected $validation;

    public function __construct()
    {
        $this->adminKabupatenModel = new AdminSurveiKabupatenModel();
        $this->userModel = new UserModel();
        $this->masterKabModel = new MasterKabModel();
        $this->kegiatanWilayahModel = new MasterKegiatanWilayahModel();
        $this->kegiatanWilayahAdminModel = new KegiatanWilayahAdminModel();
        $this->validation = \Config\Services::validation();
        $this->db = \Config\Database::connect();
    }

    /**
     * Halaman index - daftar admin kabupaten dengan kegiatan wilayah mereka
     */
    public function index()
    {
        $search = $this->request->getGet('search') ?? '';
        $filterKabupaten = $this->request->getGet('kabupaten') ?? '';

        // Get all admin survei kabupaten dengan informasi user dan kegiatan
        $builder = $this->db->table('admin_survei_kabupaten ask')
            ->select('ask.id_admin_kabupaten, ask.sobat_id, u.nama_user, u.email, u.hp, u.is_active, k.nama_kabupaten, k.id_kabupaten')
            ->join('sipantau_user u', 'ask.sobat_id = u.sobat_id')
            ->join('master_kabupaten k', 'u.id_kabupaten = k.id_kabupaten', 'left')
            ->orderBy('k.nama_kabupaten', 'ASC')
            ->orderBy('u.nama_user', 'ASC');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('u.nama_user', $search)
                ->orLike('u.email', $search)
                ->orLike('k.nama_kabupaten', $search)
                ->groupEnd();
        }

        if (!empty($filterKabupaten)) {
            $builder->where('k.id_kabupaten', $filterKabupaten);
        }

        $adminList = $builder->get()->getResultArray();

        // Get kegiatan wilayah untuk setiap admin
        foreach ($adminList as &$admin) {
            $kegiatanWilayah = $this->db->table('kegiatan_wilayah_admin kwa')
                ->select('kw.id_kegiatan_wilayah, kw.target_wilayah, kw.keterangan, 
                         mkdp.nama_kegiatan_detail_proses, mkdp.tanggal_mulai, mkdp.tanggal_selesai,
                         mkd.nama_kegiatan_detail, mk.nama_kegiatan, kab.nama_kabupaten')
                ->join('kegiatan_wilayah kw', 'kwa.id_kegiatan_wilayah = kw.id_kegiatan_wilayah')
                ->join('master_kegiatan_detail_proses mkdp', 'kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses')
                ->join('master_kegiatan_detail mkd', 'mkdp.id_kegiatan_detail = mkd.id_kegiatan_detail')
                ->join('master_kegiatan mk', 'mkd.id_kegiatan = mk.id_kegiatan')
                ->join('master_kabupaten kab', 'kw.id_kabupaten = kab.id_kabupaten')
                ->where('kwa.id_admin_kabupaten', $admin['id_admin_kabupaten'])
                ->orderBy('mkdp.tanggal_mulai', 'DESC')
                ->get()
                ->getResultArray();

            $admin['kegiatan_wilayah'] = $kegiatanWilayah;
            $admin['jumlah_kegiatan'] = count($kegiatanWilayah);
        }

        // ✅ FIX: Get all kabupaten untuk filter dropdown
        $allKabupaten = $this->masterKabModel->orderBy('nama_kabupaten', 'ASC')->findAll();

        $data = [
            'title' => 'Kelola Admin Survei Kabupaten',
            'active_menu' => 'admin-survei-kab',
            'admin_list' => $adminList,
            'allKabupaten' => $allKabupaten, // ✅ Tambahkan ini
            'search' => $search,
            'filterKabupaten' => $filterKabupaten
        ];

        return view('AdminSurveiProv/AssignAdminSurveiKab/index', $data);
    }

    /**
     * Halaman assign admin kabupaten ke kegiatan wilayah
     */
    /**
     * Halaman assign admin kabupaten ke kegiatan wilayah
     */
    public function assign($idAdminKabupaten = null)
    {
        $isEdit = !is_null($idAdminKabupaten);
        $admin = null;
        $assignedIds = [];

        // ✅ FIX: Pindahkan ke atas sebelum pengecekan apapun
        $allKabupaten = $this->masterKabModel->orderBy('nama_kabupaten', 'ASC')->findAll();

        if ($isEdit) {
            // Get admin info
            $admin = $this->db->table('admin_survei_kabupaten ask')
                ->select('ask.*, u.nama_user, u.email, u.hp, k.nama_kabupaten, k.id_kabupaten')
                ->join('sipantau_user u', 'ask.sobat_id = u.sobat_id')
                ->join('master_kabupaten k', 'u.id_kabupaten = k.id_kabupaten', 'left')
                ->where('ask.id_admin_kabupaten', $idAdminKabupaten)
                ->get()
                ->getRowArray();

            if (!$admin) {
                return redirect()->to(base_url('adminsurvei/admin-survei-kab'))
                    ->with('error', 'Admin tidak ditemukan');
            }

            // Get assigned kegiatan wilayah
            $assigned = $this->db->table('kegiatan_wilayah_admin')
                ->select('id_kegiatan_wilayah')
                ->where('id_admin_kabupaten', $idAdminKabupaten)
                ->get()
                ->getResultArray();

            $assignedIds = array_column($assigned, 'id_kegiatan_wilayah');
        }

        // Get all active users
        $users = $this->db->table('sipantau_user u')
            ->select('u.sobat_id, u.nama_user, u.email, u.hp, k.nama_kabupaten, k.id_kabupaten')
            ->join('master_kabupaten k', 'u.id_kabupaten = k.id_kabupaten', 'left')
            ->where('u.is_active', 1)
            ->orderBy('k.nama_kabupaten', 'ASC')
            ->orderBy('u.nama_user', 'ASC')
            ->get()
            ->getResultArray();

        // Get all kegiatan wilayah dengan detail
        $kegiatanWilayah = $this->db->table('kegiatan_wilayah kw')
            ->select('kw.id_kegiatan_wilayah, kw.target_wilayah, kw.keterangan,
                 mkdp.nama_kegiatan_detail_proses, mkdp.tanggal_mulai, mkdp.tanggal_selesai,
                 mkd.nama_kegiatan_detail, mk.nama_kegiatan, 
                 kab.nama_kabupaten, kab.id_kabupaten')
            ->join('master_kegiatan_detail_proses mkdp', 'kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses')
            ->join('master_kegiatan_detail mkd', 'mkdp.id_kegiatan_detail = mkd.id_kegiatan_detail')
            ->join('master_kegiatan mk', 'mkd.id_kegiatan = mk.id_kegiatan')
            ->join('master_kabupaten kab', 'kw.id_kabupaten = kab.id_kabupaten')
            ->orderBy('kab.nama_kabupaten', 'ASC')
            ->orderBy('mkdp.tanggal_mulai', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => $isEdit ? 'Edit Assignment Admin Survei Kabupaten' : 'Assign Admin Survei Kabupaten',
            'active_menu' => 'admin-survei-kab',
            'users' => $users,
            'kegiatan_wilayah' => $kegiatanWilayah,
            'is_edit' => $isEdit,
            'admin' => $admin,
            'assigned_ids' => $assignedIds,
            'allKabupaten' => $allKabupaten
        ];

        return view('AdminSurveiProv/AssignAdminSurveiKab/assign', $data);
    }

    /**
     * Process assign admin kabupaten
     */
    public function storeAssign()
    {
        $sobatId = $this->request->getPost('sobat_id');
        $kegiatanWilayahIds = $this->request->getPost('kegiatan_wilayah');

        // Validation
        if (empty($sobatId)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Silakan pilih user');
        }

        if (empty($kegiatanWilayahIds) || !is_array($kegiatanWilayahIds)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Silakan pilih minimal satu kegiatan wilayah');
        }

        // Check apakah user exists
        $user = $this->userModel->find($sobatId);
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan');
        }

        $this->db->transStart();

        // Check apakah user sudah menjadi admin kabupaten
        $existingAdmin = $this->adminKabupatenModel->where('sobat_id', $sobatId)->first();

        if ($existingAdmin) {
            $idAdminKabupaten = $existingAdmin['id_admin_kabupaten'];
        } else {
            // Jika belum, buat entry baru di admin_survei_kabupaten
            $this->db->table('admin_survei_kabupaten')->insert(['sobat_id' => $sobatId]);
            $idAdminKabupaten = $this->db->insertID();
        }

        // Assign multiple kegiatan wilayah
        $insertedCount = 0;
        foreach ($kegiatanWilayahIds as $idKegiatanWilayah) {
            // Check apakah admin sudah di-assign ke kegiatan wilayah ini
            $existingAssignment = $this->db->table('kegiatan_wilayah_admin')
                ->where([
                    'id_admin_kabupaten' => $idAdminKabupaten,
                    'id_kegiatan_wilayah' => $idKegiatanWilayah
                ])
                ->get()
                ->getRowArray();

            if (!$existingAssignment) {
                $this->db->table('kegiatan_wilayah_admin')->insert([
                    'id_admin_kabupaten' => $idAdminKabupaten,
                    'id_kegiatan_wilayah' => $idKegiatanWilayah
                ]);
                $insertedCount++;
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal melakukan assignment');
        }

        $message = $insertedCount > 0
            ? "Admin berhasil di-assign ke {$insertedCount} kegiatan wilayah"
            : "Admin sudah di-assign ke semua kegiatan wilayah yang dipilih";

        return redirect()->to(base_url('adminsurvei/admin-survei-kab'))
            ->with('success', $message);
    }

    /**
     * Update assignment admin
     */
    public function update($idAdminKabupaten)
    {
        $kegiatanWilayahIds = $this->request->getPost('kegiatan_wilayah');

        // Validation
        if (empty($kegiatanWilayahIds) || !is_array($kegiatanWilayahIds)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Silakan pilih minimal satu kegiatan wilayah');
        }

        $this->db->transStart();

        // Hapus semua assignment lama
        $this->db->table('kegiatan_wilayah_admin')
            ->where('id_admin_kabupaten', $idAdminKabupaten)
            ->delete();

        // Insert assignment baru
        foreach ($kegiatanWilayahIds as $idKegiatanWilayah) {
            $this->db->table('kegiatan_wilayah_admin')->insert([
                'id_admin_kabupaten' => $idAdminKabupaten,
                'id_kegiatan_wilayah' => $idKegiatanWilayah
            ]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal melakukan update');
        }

        return redirect()->to(base_url('adminsurvei/admin-survei-kab'))
            ->with('success', 'Assignment berhasil diupdate');
    }

    /**
     * Delete assignment admin dari kegiatan wilayah tertentu
     */
    public function deleteAssignment()
    {
        $idAdminKabupaten = $this->request->getPost('id_admin_kabupaten');
        $idKegiatanWilayah = $this->request->getPost('id_kegiatan_wilayah');

        if (!$idAdminKabupaten || !$idKegiatanWilayah) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }

        $this->db->transStart();

        // Hapus assignment
        $this->db->table('kegiatan_wilayah_admin')
            ->where([
                'id_admin_kabupaten' => $idAdminKabupaten,
                'id_kegiatan_wilayah' => $idKegiatanWilayah
            ])
            ->delete();

        // Check apakah admin masih memiliki kegiatan lain
        $remainingAssignments = $this->db->table('kegiatan_wilayah_admin')
            ->where('id_admin_kabupaten', $idAdminKabupaten)
            ->countAllResults();

        // Jika tidak ada kegiatan lagi, hapus dari admin_survei_kabupaten
        if ($remainingAssignments == 0) {
            $this->db->table('admin_survei_kabupaten')
                ->where('id_admin_kabupaten', $idAdminKabupaten)
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
    public function delete($idAdminKabupaten)
    {
        $this->db->transStart();

        // Hapus semua assignment
        $this->db->table('kegiatan_wilayah_admin')
            ->where('id_admin_kabupaten', $idAdminKabupaten)
            ->delete();

        // Hapus dari admin_survei_kabupaten
        $this->db->table('admin_survei_kabupaten')
            ->where('id_admin_kabupaten', $idAdminKabupaten)
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
     * Get kegiatan wilayah by kabupaten (AJAX)
     */
    public function getKegiatanByKabupaten($idKabupaten)
    {
        $kegiatanWilayah = $this->db->table('kegiatan_wilayah kw')
            ->select('kw.id_kegiatan_wilayah, kw.target_wilayah, kw.keterangan,
                     mkdp.nama_kegiatan_detail_proses, mkdp.tanggal_mulai, mkdp.tanggal_selesai,
                     mkd.nama_kegiatan_detail, mk.nama_kegiatan')
            ->join('master_kegiatan_detail_proses mkdp', 'kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses')
            ->join('master_kegiatan_detail mkd', 'mkdp.id_kegiatan_detail = mkd.id_kegiatan_detail')
            ->join('master_kegiatan mk', 'mkd.id_kegiatan = mk.id_kegiatan')
            ->where('kw.id_kabupaten', $idKabupaten)
            ->orderBy('mkdp.tanggal_mulai', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON($kegiatanWilayah);
    }
}