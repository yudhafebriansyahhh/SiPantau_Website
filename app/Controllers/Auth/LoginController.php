<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;

class LoginController extends BaseController
{
    public function index()
    {
        $session = session();

        // Jika user sudah login, arahkan langsung ke dashboard sesuai role
        if ($session->get('isLoggedIn')) {
            $role = (int)$session->get('role');

            switch ($role) {
                case 1:
                    return redirect()->to('/superadmin');
                case 2:
                    return redirect()->to('/adminsurvei');
                case 3:
                    return redirect()->to('/adminsurvei-kab');
                case 4:
                    return redirect()->to('/pemantau');
                default:
                    return redirect()->to('/');
            }
        }

        // Jika belum login, tampilkan halaman login
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

        // --- Tangani multi-role ---
        $roleValue = $user['role'];
        $userRoles = [];

        // Decode JSON role
        if (is_string($roleValue) && (str_starts_with($roleValue, '[') || str_starts_with($roleValue, '{'))) {
            $decoded = json_decode($roleValue, true);
            if (is_array($decoded)) {
                $userRoles = array_map('intval', $decoded);
            }
        } else {
            $userRoles = [(int)$roleValue];
        }

        // Jika user hanya punya 1 role, langsung login
        if (count($userRoles) === 1) {
            return $this->loginWithRole($user, $userRoles[0]);
        }

        // Jika multi-role, simpan data sementara dan tampilkan halaman pemilihan role
        $session->setTempdata('temp_user_data', [
            'sobat_id' => $user['sobat_id'],
            'nama_user' => $user['nama_user'],
            'email' => $user['email'],
            'roles' => $userRoles
        ], 300); // Expire dalam 5 menit

        return redirect()->to('/login/select-role');
    }

    /**
     * Halaman pemilihan role untuk user dengan multi-role
     */
    public function selectRole()
    {
        $session = session();
        
        // Cek apakah ada data temporary
        $tempUserData = $session->getTempdata('temp_user_data');
        
        if (!$tempUserData) {
            return redirect()->to('/login')->with('error', 'Session expired. Silakan login kembali.');
        }

        // Get role details
        $roleModel = new RoleModel();
        $userRoles = $roleModel->whereIn('id_roleuser', $tempUserData['roles'])->findAll();

        $data = [
            'user' => $tempUserData,
            'roles' => $userRoles
        ];

        return view('auth/select_role', $data);
    }

    /**
     * Process role selection
     */
    public function processRoleSelection()
    {
        $session = session();
        $selectedRole = (int)$this->request->getPost('selected_role');

        // Get temporary user data
        $tempUserData = $session->getTempdata('temp_user_data');

        if (!$tempUserData) {
            return redirect()->to('/login')->with('error', 'Session expired. Silakan login kembali.');
        }

        // Validasi apakah role yang dipilih valid untuk user ini
        if (!in_array($selectedRole, $tempUserData['roles'])) {
            return redirect()->back()->with('error', 'Role tidak valid.');
        }

        // Get full user data
        $userModel = new UserModel();
        $user = $userModel->find($tempUserData['sobat_id']);

        // Login dengan role yang dipilih
        return $this->loginWithRole($user, $selectedRole);
    }

    /**
     * Login user dengan role tertentu
     */
    private function loginWithRole($user, $role)
    {
        $session = session();

        // Set session
        $session->set([
            'user_id'    => $user['sobat_id'],
            'nama_user'  => $user['nama_user'],
            'email'      => $user['email'],
            'role'       => (int)$role,
            'all_roles'  => json_decode($user['role'], true), // Simpan semua role
            'isLoggedIn' => true
        ]);

        // Hapus temporary data
        $session->removeTempdata('temp_user_data');

        // Redirect berdasarkan role yang dipilih
        switch ($role) {
            case 1:
                return redirect()->to('/superadmin'); // Super Admin
            case 2:
                return redirect()->to('/adminsurvei'); // Admin Provinsi
            case 3:
                return redirect()->to('/adminsurvei-kab'); // Admin Kab/Kota
            case 4:
                return redirect()->to('/pemantau'); // Pemantau
            default:
                $session->setFlashdata('error', 'Role tidak dikenali.');
                return redirect()->to('/');
        }
    }

    /**
     * Switch role untuk user yang sedang login (optional feature)
     */
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

        // Get role details
        $roleModel = new RoleModel();
        $userRoles = $roleModel->whereIn('id_roleuser', $allRoles)->findAll();

        $data = [
            'roles' => $userRoles,
            'current_role' => $session->get('role')
        ];

        return view('auth/switch_role', $data);
    }

    /**
     * Process switch role
     */
    public function processSwitchRole()
    {
        $session = session();
        $selectedRole = (int)$this->request->getPost('selected_role');

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $allRoles = $session->get('all_roles');

        // Validasi role
        if (!in_array($selectedRole, $allRoles)) {
            return redirect()->back()->with('error', 'Role tidak valid.');
        }

        // Update session role
        $session->set('role', $selectedRole);

        // Redirect ke dashboard sesuai role baru
        switch ($selectedRole) {
            case 1:
                return redirect()->to('/superadmin');
            case 2:
                return redirect()->to('/adminsurvei');
            case 3:
                return redirect()->to('/adminsurvei-kab');
            case 4:
                return redirect()->to('/pemantau');
            default:
                return redirect()->to('/');
        }
    }

    public function logout()
    {
        $session = session();

        // Hapus semua data session
        $session->destroy();

        // Redirect ke halaman login
        return redirect()
            ->to(base_url('login'))
            ->with('success', 'Anda telah logout.');
    }
}