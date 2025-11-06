<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\PantauProgressModel;
use App\Models\PCLModel;

class PantauProgressController extends BaseController
{
    use ResponseTrait;

    private $jwtKey;

    public function __construct()
    {
        $this->jwtKey = getenv('JWT_SECRET_KEY');
    }

    /**
     * GET /api/pantau-progress
     * Ambil daftar progress PCL (dengan optional filter id_pcl)
     */
    public function index()
{
    $authHeader = $this->request->getHeaderLine('Authorization');
    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        return $this->failUnauthorized('Token tidak ditemukan');
    }

    try {
        // 🔐 Decode JWT
        $decoded = JWT::decode($matches[1], new Key($this->jwtKey, 'HS256'));
        $sobat_id = $decoded->data->sobat_id;

        // 🔎 Pastikan user adalah PCL
        $pclModel = new PCLModel();
        $pclList = $pclModel->where('sobat_id', $sobat_id)->findAll();

        if (empty($pclList)) {
            return $this->failUnauthorized('Hanya PCL yang bisa mengakses data progress.');
        }

        $pclIds = array_column($pclList, 'id_pcl');

        // 🔸 Ambil parameter filter id_pcl
        $filterIdPcl = $this->request->getGet('id_pcl');

        // 🔒 Validasi kepemilikan id_pcl
        if (!empty($filterIdPcl) && !in_array($filterIdPcl, $pclIds)) {
            return $this->failForbidden('Anda tidak memiliki akses ke id_pcl tersebut.');
        }

        $progressModel = new PantauProgressModel();

        // 🔧 Jika ada filter id_pcl → tampilkan semua progress milik id_pcl tersebut
        if (!empty($filterIdPcl)) {
            $data = $progressModel
                ->where('id_pcl', $filterIdPcl)
                ->orderBy('created_at', 'DESC')
                ->findAll();

            // 🔹 Hitung total kumulatif untuk id_pcl ini
            $totalKumulatif = (int) $progressModel
                ->where('id_pcl', $filterIdPcl)
                ->selectSum('jumlah_realisasi_absolut')
                ->first()['jumlah_realisasi_absolut'] ?? 0;

            return $this->respond([
                'status' => 'success',
                'id_pcl' => (int) $filterIdPcl,
                'total_kumulatif' => $totalKumulatif,
                'total_entry' => count($data),
                'data' => $data
            ]);
        }

        // 🔧 Jika tanpa filter → ambil semua progress milik user (bisa banyak kegiatan)
        $data = [];
        foreach ($pclIds as $idPcl) {
            $records = $progressModel
                ->where('id_pcl', $idPcl)
                ->orderBy('created_at', 'DESC')
                ->findAll();

            $totalKumulatif = (int) $progressModel
                ->where('id_pcl', $idPcl)
                ->selectSum('jumlah_realisasi_absolut')
                ->first()['jumlah_realisasi_absolut'] ?? 0;

            $data[] = [
                'id_pcl' => $idPcl,
                'total_kumulatif' => $totalKumulatif,
                'total_entry' => count($records),
                'records' => $records
            ];
        }

        return $this->respond([
            'status' => 'success',
            'total_pcl' => count($data),
            'data' => $data
        ]);

    } catch (\Firebase\JWT\ExpiredException $e) {
        return $this->failUnauthorized('Token kadaluarsa');
    } catch (\Exception $e) {
        return $this->failUnauthorized('Token tidak valid: ' . $e->getMessage());
    }
}


    /**
     * POST /api/pantau-progress
     * Simpan progress baru oleh PCL
     */
    public function create()
{
    $authHeader = $this->request->getHeaderLine('Authorization');
    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        return $this->failUnauthorized('Token tidak ditemukan');
    }

    try {
        $decoded = JWT::decode($matches[1], new Key($this->jwtKey, 'HS256'));
        $sobat_id = $decoded->data->sobat_id;

        $pclModel = new PCLModel();
        $pclList = $pclModel->where('sobat_id', $sobat_id)->findAll();

        if (empty($pclList)) {
            return $this->failUnauthorized('Hanya PCL yang bisa menambahkan progress.');
        }

        $pclIds = array_column($pclList, 'id_pcl');

        // Bisa JSON atau FormData
        $data = $this->request->getJSON(true);
        if (empty($data)) {
            $data = $this->request->getPost();
        }

        // 🧩 Validasi input wajib
        if (empty($data['id_pcl']) || empty($data['jumlah_realisasi_absolut'])) {
            return $this->failValidationErrors('Field id_pcl dan jumlah_realisasi_absolut wajib diisi.');
        }

        // 🔒 Pastikan id_pcl milik user login
        if (!in_array($data['id_pcl'], $pclIds)) {
            return $this->failForbidden('Anda tidak memiliki akses ke id_pcl tersebut.');
        }

        $progressModel = new PantauProgressModel();

        // 🔢 Hitung total kumulatif baru
        $totalRealisasi = (int) $progressModel
            ->where('id_pcl', $data['id_pcl'])
            ->selectSum('jumlah_realisasi_absolut')
            ->first()['jumlah_realisasi_absolut'];

        $newKumulatif = $totalRealisasi + (int) $data['jumlah_realisasi_absolut'];

        // 🟢 Simpan data baru
        $insertData = [
            'id_pcl' => $data['id_pcl'],
            'jumlah_realisasi_absolut' => $data['jumlah_realisasi_absolut'],
            'jumlah_realisasi_kumulatif' => $newKumulatif,
            'catatan_aktivitas' => $data['catatan_aktivitas'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $progressModel->insert($insertData);

        return $this->respondCreated([
            'status' => 'success',
            'message' => 'Progress berhasil disimpan.',
            'data' => $insertData
        ]);

    } catch (\Firebase\JWT\ExpiredException $e) {
        return $this->failUnauthorized('Token kadaluarsa');
    } catch (\Exception $e) {
        return $this->failUnauthorized('Token tidak valid: ' . $e->getMessage());
    }
}


}
