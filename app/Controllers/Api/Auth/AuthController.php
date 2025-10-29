<?php

namespace App\Controllers\Api\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use CodeIgniter\API\ResponseTrait;

class AuthController extends BaseController
{
    use ResponseTrait;

    private $jwtKey;
    private $jwtExpiry;

    public function __construct()
    {
        $this->jwtKey = getenv('JWT_SECRET_KEY');
        $this->jwtExpiry = getenv('JWT_EXPIRY') ?: 604800; // default 7 hari
    }

    /**
     * LOGIN (Hanya untuk role Petugas Survei)
     * Endpoint: POST /api/auth/login
     */
    public function login()
    {
        $data = $this->request->getPost();

        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        if (!$email || !$password) {
            return $this->failValidationErrors('Email dan password wajib diisi');
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)
                          ->where('is_active', 1)
                          ->first();

        if (!$user) {
            return $this->failNotFound('User tidak ditemukan atau tidak aktif');
        }

        // 🔍 Decode role JSON dan cek apakah punya role id 5 (Petugas Survei)
        $roleIds = json_decode($user['role'], true) ?? [];
        if (!in_array(5, $roleIds)) {
            return $this->failUnauthorized('Akses hanya untuk Petugas Survei');
        }

        // 🔑 Verifikasi password hash
        if (!password_verify($password, $user['password'])) {
            return $this->failUnauthorized('Password salah');
        }

        // 🔐 Payload JWT
        $issuedAt = time();
        $expireAt = $issuedAt + $this->jwtExpiry;

        $payload = [
            'iss' => base_url(),  // issuer
            'iat' => $issuedAt,
            'exp' => $expireAt,
            'data' => [
                'sobat_id'     => $user['sobat_id'],
                'nama_user'    => $user['nama_user'],
                'email'        => $user['email'],
                'hp'           => $user['hp'],
                'id_kabupaten' => $user['id_kabupaten'],
                'roles'        => $roleIds
            ]
        ];

        $token = JWT::encode($payload, $this->jwtKey, 'HS256');

        return $this->respond([
            'status'  => 'success',
            'message' => 'Login berhasil',
            'token'   => $token,
            'user'    => $payload['data']
        ]);
    }

    /**
     * 🔹 Endpoint untuk cek profil login (verifikasi token)
     * Endpoint: GET /api/auth/me
     */
    public function me()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $this->failUnauthorized('Token tidak ditemukan');
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key($this->jwtKey, 'HS256'));
            return $this->respond([
                'status' => 'success',
                'user'   => $decoded->data
            ]);
        } catch (\Firebase\JWT\ExpiredException $e) {
            return $this->failUnauthorized('Token kadaluarsa');
        } catch (\Exception $e) {
            return $this->failUnauthorized('Token tidak valid: ' . $e->getMessage());
        }
    }
}
