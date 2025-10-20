<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class LoginController extends BaseController
{
    public function index()
    {
        $session = session();

        // Jika user sudah login, arahkan langsung ke dashboard sesuai role
        if ($session->get('isLoggedIn')) {
            switch ($session->get('id_role')) {
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

        // Set session
        $session->set([
            'user_id'   => $user['sobat_id'],
            'nama_user' => $user['nama_user'],
            'email'     => $user['email'],
            'id_role'   => $user['id_role'],
            'is_admin'  => $user['is_admin'],
            'isLoggedIn'=> true
        ]);

        // Redirect berdasarkan role
        switch ($user['id_role']) {
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

    public function logout()
    {
        $session = session();

        // Hapus semua data session
        $session->destroy();

        // Redirect ke halaman login
        return redirect()->to(base_url('login'))->with('success', 'Anda telah logout.');
    }
}
