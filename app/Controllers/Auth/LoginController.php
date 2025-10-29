<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\AdminSurveiProvinsiModel;
use App\Models\AdminSurveiKabupatenModel;

class LoginController extends BaseController
{
    public function index()
    {
        $session = session();

        // Jika user sudah login, arahkan langsung ke dashboard sesuai role
        if ($session->get('isLoggedIn')) {
            return $this->redirectToDashboard($session->get('role'), $session->get('role_type'));
        }

        return view('auth/login');
    }

    public function process()
    {
        $session = session();
        $userModel = new UserModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Cari user berdasarkan email
        $user = $userModel->where('email', $email)->first();

        // Validasi user
        if (!$user) {
            $session->setFlashdata('error', 'Email tidak ditemukan.');
            return redirect()->back()->withInput();
        }

        if (!password_verify($password, $user['password'])) {
            $session->setFlashdata('error', 'Password salah.');
            return redirect()->back()->withInput();
        }

        if ($user['is_active'] == 0) {
            $session->setFlashdata('error', 'Akun belum aktif.');
            return redirect()->back()->withInput();
        }

        // --- Decode roles ---
        $roleValue = $user['role'];
        $userRoles = [];

        if (is_string($roleValue) && (str_starts_with($roleValue, '[') || str_starts_with($roleValue, '{'))) {
            $decoded = json_decode($roleValue, true);
            if (is_array($decoded)) {
                $userRoles = array_map('intval', $decoded);
            }
        } else {
            $userRoles = [(int)$roleValue];
        }

        // --- Check admin status ---
        $adminProvinsiModel = new AdminSurveiProvinsiModel();
        $adminKabupatenModel = new AdminSurveiKabupatenModel();
        
        $isAdminProvinsi = $adminProvinsiModel->isAdminProvinsi($user['sobat_id']);
        $isAdminKabupaten = $adminKabupatenModel->isAdminKabupaten($user['sobat_id']);
        
        // Get admin IDs
        $adminProvinsiId = $isAdminProvinsi ? $adminProvinsiModel->getAdminProvinsiId($user['sobat_id']) : null;
        $adminKabupatenId = $isAdminKabupaten ? $adminKabupatenModel->getAdminKabupatenId($user['sobat_id']) : null;
        
        // Build available roles
        $availableRoles = [];
        
        // 1. Tambahkan role dari tabel user (role asli)
        foreach ($userRoles as $roleId) {
            if ($roleId == 2) {
                // Pemantau Provinsi (role asli)
                $availableRoles[] = [
                    'id' => 2,
                    'type' => 'pemantau_provinsi'
                ];
            } elseif ($roleId == 3) {
                // Pemantau Kabupaten (role asli)
                $availableRoles[] = [
                    'id' => 3,
                    'type' => 'pemantau_kabupaten'
                ];
            } else {
                // Role lain (Super Admin, Pemantau Pusat, dll)
                $availableRoles[] = [
                    'id' => $roleId,
                    'type' => 'default'
                ];
            }
        }
        
        // 2. Tambahkan role admin JIKA terdaftar di tabel admin (role tambahan)
        if ($isAdminProvinsi && $adminProvinsiId) {
            // Tambahkan opsi Admin Survei Provinsi
            $availableRoles[] = [
                'id' => 2,
                'type' => 'admin_provinsi',
                'admin_id' => $adminProvinsiId
            ];
        }
        
        if ($isAdminKabupaten && $adminKabupatenId) {
            // Tambahkan opsi Admin Survei Kabupaten
            $availableRoles[] = [
                'id' => 3,
                'type' => 'admin_kabupaten',
                'admin_id' => $adminKabupatenId
            ];
        }
        
        // Jika tidak ada role yang valid (seharusnya tidak mungkin terjadi)
        if (empty($availableRoles)) {
            $session->setFlashdata('error', 'Anda tidak memiliki akses ke sistem. Hubungi administrator.');
            return redirect()->back();
        }

        // Jika user hanya punya 1 role valid, langsung login
        if (count($availableRoles) === 1) {
            return $this->loginWithRole($user, $availableRoles[0]);
        }

        // Jika multi-role, simpan data sementara dan tampilkan halaman pemilihan role
        $session->setTempdata('temp_user_data', [
            'sobat_id' => $user['sobat_id'],
            'nama_user' => $user['nama_user'],
            'email' => $user['email'],
            'roles' => $availableRoles
        ], 300); // Expire dalam 5 menit

        return redirect()->to('/login/select-role');
    }

    // Halaman pemilihan role untuk user dengan multi-role
    public function selectRole()
    {
        $session = session();
        
        $tempUserData = $session->getTempdata('temp_user_data');
        
        if (!$tempUserData) {
            return redirect()->to('/login')->with('error', 'Session expired. Silakan login kembali.');
        }

        // Get role details dengan label yang sesuai
        $roleModel = new RoleModel();
        $processedRoles = [];
        
        foreach ($tempUserData['roles'] as $roleData) {
            $roleId = $roleData['id'];
            $roleType = $roleData['type'];
            
            // Get basic role info
            $roleInfo = $roleModel->find($roleId);
            
            if ($roleInfo) {
                // Customize nama role berdasarkan tipe
                if ($roleType === 'admin_provinsi') {
                    $roleInfo['roleuser'] = 'Admin Survei Provinsi';
                    $roleInfo['keterangan'] = 'Mengelola survei tingkat provinsi';
                } elseif ($roleType === 'pemantau_provinsi') {
                    $roleInfo['roleuser'] = 'Pemantau Provinsi';
                    $roleInfo['keterangan'] = 'Melihat data provinsi';
                } elseif ($roleType === 'admin_kabupaten') {
                    $roleInfo['roleuser'] = 'Admin Survei Kabupaten';
                    $roleInfo['keterangan'] = 'Mengelola survei kabupaten/kota';
                } elseif ($roleType === 'pemantau_kabupaten') {
                    $roleInfo['roleuser'] = 'Pemantau Kabupaten';
                    $roleInfo['keterangan'] = 'Melihat data kabupaten';
                }
                
                $roleInfo['role_type'] = $roleType;
                $roleInfo['admin_id'] = $roleData['admin_id'] ?? null;
                $processedRoles[] = $roleInfo;
            }
        }

        $data = [
            'user' => $tempUserData,
            'roles' => $processedRoles
        ];

        return view('auth/select_role', $data);
    }

    // Process role selection
    public function processRoleSelection()
    {
        $session = session();
        $selectedRoleId = (int)$this->request->getPost('selected_role');
        $selectedRoleType = $this->request->getPost('selected_role_type');

        $tempUserData = $session->getTempdata('temp_user_data');

        if (!$tempUserData) {
            return redirect()->to('/login')->with('error', 'Session expired. Silakan login kembali.');
        }

        // Validasi apakah role yang dipilih valid untuk user ini
        $selectedRoleData = null;
        foreach ($tempUserData['roles'] as $role) {
            if ($role['id'] == $selectedRoleId && $role['type'] == $selectedRoleType) {
                $selectedRoleData = $role;
                break;
            }
        }
        
        if (!$selectedRoleData) {
            return redirect()->back()->with('error', 'Role tidak valid.');
        }

        // Get full user data
        $userModel = new UserModel();
        $user = $userModel->find($tempUserData['sobat_id']);

        // Login dengan role yang dipilih
        return $this->loginWithRole($user, $selectedRoleData);
    }

    // Login user dengan role tertentu
    private function loginWithRole($user, $roleData)
    {
        $session = session();
        $roleId = $roleData['id'];
        $roleType = $roleData['type'];

        // Set session dasar
        $sessionData = [
            'user_id'    => $user['sobat_id'],
            'sobat_id'   => $user['sobat_id'], // Tambahkan ini untuk konsistensi
            'nama_user'  => $user['nama_user'],
            'email'      => $user['email'],
            'role'       => (int)$roleId,
            'role_type'  => $roleType,
            'all_roles'  => is_string($user['role']) ? json_decode($user['role'], true) : [$user['role']],
            'isLoggedIn' => true
        ];
        
        // Tambahkan admin_id ke session jika tipe admin
        if ($roleType === 'admin_provinsi' && isset($roleData['admin_id'])) {
            $sessionData['id_admin_provinsi'] = $roleData['admin_id'];
            
            // Debug log
            log_message('info', 'Login Admin Provinsi - ID: ' . $roleData['admin_id']);
        } elseif ($roleType === 'admin_kabupaten' && isset($roleData['admin_id'])) {
            $sessionData['id_admin_kabupaten'] = $roleData['admin_id'];
            
            // Debug log
            log_message('info', 'Login Admin Kabupaten - ID: ' . $roleData['admin_id'] . ' | Sobat ID: ' . $user['sobat_id']);
        }

        $session->set($sessionData);

        // Debug: Print session data
        log_message('info', 'Session Data After Login: ' . json_encode($sessionData));

        // Hapus temporary data
        $session->removeTempdata('temp_user_data');

        // Redirect berdasarkan role dan tipe
        return $this->redirectToDashboard($roleId, $roleType);
    }

    // Helper untuk redirect ke dashboard
    private function redirectToDashboard($role, $roleType = 'default')
    {
        switch ($role) {
            case 1:
                return redirect()->to('/superadmin'); // Super Admin
            case 2:
                if ($roleType === 'admin_provinsi') {
                    return redirect()->to('/adminsurvei'); // Admin Provinsi
                } else {
                    // Pemantau Provinsi - route belum ada, redirect ke coming soon atau dashboard default
                    return redirect()->to('/pemantau')->with('info', 'Dashboard Pemantau Provinsi belum tersedia.');
                }
            case 3:
                if ($roleType === 'admin_kabupaten') {
                    return redirect()->to('/adminsurvei-kab'); // Admin Kab/Kota
                } else {
                    // Pemantau Kabupaten - route belum ada
                    return redirect()->to('/pemantau')->with('info', 'Dashboard Pemantau Kabupaten belum tersedia.');
                }
            case 4:
                return redirect()->to('/pemantau'); // Pemantau Pusat
            default:
                session()->setFlashdata('error', 'Role tidak dikenali.');
                return redirect()->to('/login');
        }
    }

    // Switch role untuk user yang sedang login
    public function switchRole()
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $allRoles = $session->get('all_roles');
        
        if (!$allRoles || count($allRoles) <= 1) {
            return redirect()->back()->with('error', 'Anda hanya memiliki satu role.');
        }

        // Check admin status untuk build available roles
        $userModel = new UserModel();
        $user = $userModel->find($session->get('user_id'));
        
        $adminProvinsiModel = new AdminSurveiProvinsiModel();
        $adminKabupatenModel = new AdminSurveiKabupatenModel();
        
        $isAdminProvinsi = $adminProvinsiModel->isAdminProvinsi($user['sobat_id']);
        $isAdminKabupaten = $adminKabupatenModel->isAdminKabupaten($user['sobat_id']);
        
        // Get admin IDs
        $adminProvinsiId = $isAdminProvinsi ? $adminProvinsiModel->getAdminProvinsiId($user['sobat_id']) : null;
        $adminKabupatenId = $isAdminKabupaten ? $adminKabupatenModel->getAdminKabupatenId($user['sobat_id']) : null;
        
        // Build available roles
        $availableRoles = [];
        
        // 1. Tambahkan role dari tabel user (role asli)
        foreach ($allRoles as $roleId) {
            if ($roleId == 2) {
                $availableRoles[] = [
                    'id' => 2,
                    'type' => 'pemantau_provinsi'
                ];
            } elseif ($roleId == 3) {
                $availableRoles[] = [
                    'id' => 3,
                    'type' => 'pemantau_kabupaten'
                ];
            } else {
                $availableRoles[] = [
                    'id' => $roleId,
                    'type' => 'default'
                ];
            }
        }
        
        // 2. Tambahkan role admin JIKA terdaftar di tabel admin
        if ($isAdminProvinsi && $adminProvinsiId) {
            $availableRoles[] = [
                'id' => 2,
                'type' => 'admin_provinsi',
                'admin_id' => $adminProvinsiId
            ];
        }
        
        if ($isAdminKabupaten && $adminKabupatenId) {
            $availableRoles[] = [
                'id' => 3,
                'type' => 'admin_kabupaten',
                'admin_id' => $adminKabupatenId
            ];
        }

        // Get role details dengan label yang sesuai
        $roleModel = new RoleModel();
        $processedRoles = [];
        
        foreach ($availableRoles as $roleData) {
            $roleId = $roleData['id'];
            $roleType = $roleData['type'];
            
            $roleInfo = $roleModel->find($roleId);
            
            if ($roleInfo) {
                if ($roleType === 'admin_provinsi') {
                    $roleInfo['roleuser'] = 'Admin Survei Provinsi';
                    $roleInfo['keterangan'] = 'Mengelola survei tingkat provinsi';
                } elseif ($roleType === 'pemantau_provinsi') {
                    $roleInfo['roleuser'] = 'Pemantau Provinsi';
                    $roleInfo['keterangan'] = 'Melihat data provinsi';
                } elseif ($roleType === 'admin_kabupaten') {
                    $roleInfo['roleuser'] = 'Admin Survei Kabupaten';
                    $roleInfo['keterangan'] = 'Mengelola survei kabupaten/kota';
                } elseif ($roleType === 'pemantau_kabupaten') {
                    $roleInfo['roleuser'] = 'Pemantau Kabupaten';
                    $roleInfo['keterangan'] = 'Melihat data kabupaten';
                }
                
                $roleInfo['role_type'] = $roleType;
                $roleInfo['admin_id'] = $roleData['admin_id'] ?? null;
                $processedRoles[] = $roleInfo;
            }
        }

        $data = [
            'roles' => $processedRoles,
            'current_role' => $session->get('role'),
            'current_role_type' => $session->get('role_type')
        ];

        return view('auth/switch_role', $data);
    }

    // Process switch role
    public function processSwitchRole()
    {
        $session = session();
        $selectedRoleId = (int)$this->request->getPost('selected_role');
        $selectedRoleType = $this->request->getPost('selected_role_type');

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $allRoles = $session->get('all_roles');

        // Validasi role ID
        if (!in_array($selectedRoleId, $allRoles)) {
            return redirect()->back()->with('error', 'Role tidak valid.');
        }
        
        // Validasi admin status berdasarkan role type
        $userModel = new UserModel();
        $user = $userModel->find($session->get('user_id'));
        
        $adminId = null;
        
        if ($selectedRoleType === 'admin_provinsi') {
            $adminModel = new AdminSurveiProvinsiModel();
            if (!$adminModel->isAdminProvinsi($user['sobat_id'])) {
                return redirect()->back()->with('error', 'Anda bukan Admin Provinsi.');
            }
            $adminId = $adminModel->getAdminProvinsiId($user['sobat_id']);
            $session->set('id_admin_provinsi', $adminId);
            $session->remove('id_admin_kabupaten');
        } elseif ($selectedRoleType === 'admin_kabupaten') {
            $adminModel = new AdminSurveiKabupatenModel();
            if (!$adminModel->isAdminKabupaten($user['sobat_id'])) {
                return redirect()->back()->with('error', 'Anda bukan Admin Kabupaten.');
            }
            $adminId = $adminModel->getAdminKabupatenId($user['sobat_id']);
            $session->set('id_admin_kabupaten', $adminId);
            $session->remove('id_admin_provinsi');
        } else {
            // Remove admin IDs untuk role pemantau biasa
            $session->remove('id_admin_provinsi');
            $session->remove('id_admin_kabupaten');
        }

        // Update session role
        $session->set('role', $selectedRoleId);
        $session->set('role_type', $selectedRoleType);

        // Redirect ke dashboard sesuai role baru
        return $this->redirectToDashboard($selectedRoleId, $selectedRoleType);
    }

    public function logout()
    {
        $session = session();
        $session->destroy();

        return redirect()
            ->to(base_url('login'))
            ->with('success', 'Anda telah logout.');
    }
}