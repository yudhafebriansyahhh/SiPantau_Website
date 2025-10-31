<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\PMLModel;
use App\Models\PCLModel;

class KegiatanController extends BaseController
{
    use ResponseTrait;

    private $jwtKey;

    public function __construct()
    {
        $this->jwtKey = getenv('JWT_SECRET_KEY');
    }

    public function index()
    {
        // 🔑 Ambil token dari header Authorization
        $authHeader = $this->request->getHeaderLine('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $this->failUnauthorized('Token tidak ditemukan');
        }

        $token = $matches[1];

        try {
            // 🔍 Decode token JWT
            $decoded = JWT::decode($token, new Key($this->jwtKey, 'HS256'));
            $sobat_id = $decoded->data->sobat_id;

            $pmlModel = new PMLModel();
            $pclModel = new PCLModel();

            // 🔹 Cek apakah user PML / PCL
            $isPML = $pmlModel->where('sobat_id', $sobat_id)->first();
            $isPCL = $pclModel->where('sobat_id', $sobat_id)->first();

            if (!$isPML && !$isPCL) {
                return $this->failUnauthorized('User ini bukan PML atau PCL');
            }

            // Fungsi untuk menentukan status aktif/tidak aktif
            $checkStatus = function ($start, $end) {
                $today = date('Y-m-d');
                return ($today >= $start && $today <= $end) ? 'aktif' : 'tidak aktif';
            };

            // 🔹 Jika user adalah PML
            if ($isPML) {
                $dataKegiatan = $pmlModel->select('
                        pml.id_pml, pml.target, pml.status_approval,
                        mk.nama_kegiatan, mkdp.nama_kegiatan_detail_proses,
                        mkdp.tanggal_mulai, mkdp.tanggal_selesai,
                        kab.nama_kabupaten
                    ')
                    ->join('kegiatan_wilayah kw', 'pml.id_kegiatan_wilayah = kw.id_kegiatan_wilayah')
                    ->join('master_kegiatan_detail_proses mkdp', 'kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses')
                    ->join('master_kegiatan_detail mkd', 'mkdp.id_kegiatan_detail = mkd.id_kegiatan_detail')
                    ->join('master_kegiatan mk', 'mkd.id_kegiatan = mk.id_kegiatan')
                    ->join('master_kabupaten kab', 'kw.id_kabupaten = kab.id_kabupaten')
                    ->where('pml.sobat_id', $sobat_id)
                    ->findAll();

                // Tambahkan status_kegiatan
                foreach ($dataKegiatan as &$kegiatan) {
                    $kegiatan['status_kegiatan'] = $checkStatus($kegiatan['tanggal_mulai'], $kegiatan['tanggal_selesai']);
                }

                return $this->respond([
                    'status' => 'success',
                    'role_aktif' => 'PML',
                    'kegiatan' => $dataKegiatan
                ]);
            }

            // 🔹 Jika user adalah PCL
            if ($isPCL) {
                $dataKegiatan = $pclModel->select('
                        pcl.id_pcl, pcl.target, pcl.status_approval,
                        mk.nama_kegiatan, mkdp.nama_kegiatan_detail_proses,
                        mkdp.tanggal_mulai, mkdp.tanggal_selesai,
                        kab.nama_kabupaten,
                        u_pml.nama_user as nama_pml
                    ')
                    ->join('pml', 'pcl.id_pml = pml.id_pml')
                    ->join('sipantau_user u_pml', 'pml.sobat_id = u_pml.sobat_id')
                    ->join('kegiatan_wilayah kw', 'pml.id_kegiatan_wilayah = kw.id_kegiatan_wilayah')
                    ->join('master_kegiatan_detail_proses mkdp', 'kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses')
                    ->join('master_kegiatan_detail mkd', 'mkdp.id_kegiatan_detail = mkd.id_kegiatan_detail')
                    ->join('master_kegiatan mk', 'mkd.id_kegiatan = mk.id_kegiatan')
                    ->join('master_kabupaten kab', 'kw.id_kabupaten = kab.id_kabupaten')
                    ->where('pcl.sobat_id', $sobat_id)
                    ->findAll();

                // Tambahkan status_kegiatan
                foreach ($dataKegiatan as &$kegiatan) {
                    $kegiatan['status_kegiatan'] = $checkStatus($kegiatan['tanggal_mulai'], $kegiatan['tanggal_selesai']);
                }

                return $this->respond([
                    'status' => 'success',
                    'role_aktif' => 'PCL',
                    'kegiatan' => $dataKegiatan
                ]);
            }

        } catch (\Exception $e) {
            return $this->failUnauthorized('Token tidak valid: ' . $e->getMessage());
        }
    }
}
