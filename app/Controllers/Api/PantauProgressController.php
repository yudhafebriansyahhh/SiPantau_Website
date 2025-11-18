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
     */
    public function index()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $this->failUnauthorized('Token tidak ditemukan');
        }

        try {
            // Decode JWT
            $decoded = JWT::decode($matches[1], new Key($this->jwtKey, 'HS256'));
            $sobat_id = $decoded->data->sobat_id;

            // Validasi PCL
            $pclModel = new PCLModel();
            $pclList = $pclModel->where('sobat_id', $sobat_id)->findAll();

            if (empty($pclList)) {
                return $this->failUnauthorized('Hanya PCL yang bisa mengakses data progress.');
            }

            $pclIds = array_column($pclList, 'id_pcl');

            // Optional filter
            $filterIdPcl = $this->request->getGet('id_pcl');

            if (!empty($filterIdPcl) && !in_array($filterIdPcl, $pclIds)) {
                return $this->failForbidden('Anda tidak memiliki akses ke id_pcl tersebut.');
            }

            $progressModel = new PantauProgressModel();

            // Jika ada filter id_pcl → ambil data progres id_pcl tsb
            if (!empty($filterIdPcl)) {
                $data = $progressModel
                    ->where('id_pcl', $filterIdPcl)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();

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

            // Tanpa filter → tampilkan seluruh PCL user
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
     * Insert atau update jika masih tanggal yang sama
     */
    public function create()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $this->failUnauthorized('Token tidak ditemukan');
        }

        try {
            // Decode JWT
            $token = trim(str_replace('"', '', $matches[1]));
            $decoded = JWT::decode($token, new Key($this->jwtKey, 'HS256'));
            $sobat_id = $decoded->data->sobat_id;

            // Validasi PCL
            $pclModel = new PCLModel();
            $pclList = $pclModel->where('sobat_id', $sobat_id)->findAll();

            if (empty($pclList)) {
                return $this->failUnauthorized('Hanya PCL yang bisa menambahkan progress.');
            }

            $pclIds = array_column($pclList, 'id_pcl');

            // Ambil data POST
            $data = $this->request->getPost();
            if (empty($data)) $data = $_POST;

            if (empty($data['id_pcl']) || empty($data['jumlah_realisasi_absolut'])) {
                return $this->failValidationErrors('Field id_pcl dan jumlah_realisasi_absolut wajib diisi.');
            }

            if (!in_array($data['id_pcl'], $pclIds)) {
                return $this->failForbidden('Anda tidak memiliki akses ke id_pcl tersebut.');
            }

            $progressModel = new PantauProgressModel();

            // Ambil kumulatif terakhir
            $last = $progressModel
                ->where('id_pcl', $data['id_pcl'])
                ->orderBy('created_at', 'DESC')
                ->first();

            $lastKumulatif = $last['jumlah_realisasi_kumulatif'] ?? 0;

            $inputAbsolut = (int)$data['jumlah_realisasi_absolut'];

            // Batas 2x kumulatif terakhir
            $maxAllowed = $lastKumulatif * 2;

            if ($lastKumulatif > 0 && $inputAbsolut > $maxAllowed) {
                return $this->failValidationErrors(
                    "Jumlah realisasi absolut maksimal {$maxAllowed} berdasarkan aturan (2x kumulatif terakhir = {$lastKumulatif})."
                );
            }

            // ================================
            // CEK DATA TANGGAL YANG SAMA
            // ================================
            $today = date('Y-m-d');

            $existingToday = $progressModel
                ->where('id_pcl', $data['id_pcl'])
                ->where("DATE(created_at)", $today)
                ->orderBy('created_at', 'DESC')
                ->first();

            // Jika SUDAH ADA progres hari ini → UPDATE
            if ($existingToday) {

                $newAbsolut = $existingToday['jumlah_realisasi_absolut'] + $inputAbsolut;
                $newKumulatif = $existingToday['jumlah_realisasi_kumulatif'] + $inputAbsolut;

                $updateData = [
                    'jumlah_realisasi_absolut' => $newAbsolut,
                    'jumlah_realisasi_kumulatif' => $newKumulatif,
                    'catatan_aktivitas' => $data['catatan_aktivitas'] ?? $existingToday['catatan_aktivitas'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                $progressModel->update($existingToday['id_pantau_progess'], $updateData);

                return $this->respond([
                    'status' => 'success',
                    'message' => 'Progress hari ini berhasil diperbarui.',
                    'data' => $updateData
                ]);
            }

            // ================================
            // TIDAK ADA progres hari ini → INSERT baru
            // ================================
            $newKumulatif = $lastKumulatif + $inputAbsolut;

            $insertData = [
                'id_pcl' => $data['id_pcl'],
                'jumlah_realisasi_absolut' => $inputAbsolut,
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

    /**
     * DELETE /api/pantau-progress/{id}
     */
    public function delete($id = null)
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $this->failUnauthorized('Token tidak ditemukan');
        }

        try {
            // Verifikasi token
            JWT::decode($matches[1], new Key($this->jwtKey, 'HS256'));

            if (empty($id) || !is_numeric($id)) {
                return $this->failValidationErrors('Parameter id_pantau_progess tidak valid.');
            }

            $progressModel = new PantauProgressModel();
            $progress = $progressModel->find($id);

            if (!$progress) {
                return $this->failNotFound('Data progress tidak ditemukan.');
            }

            if (!$progressModel->where('id_pantau_progess', $id)->delete()) {
                return $this->fail('Gagal menghapus data progress.');
            }

            return $this->respondDeleted([
                'status' => 'success',
                'message' => 'Data progress berhasil dihapus.',
                'deleted_id' => $id
            ]);

        } catch (\Firebase\JWT\ExpiredException $e) {
            return $this->failUnauthorized('Token kadaluarsa');
        } catch (\Exception $e) {
            return $this->failUnauthorized('Token tidak valid: ' . $e->getMessage());
        }
    }
}
