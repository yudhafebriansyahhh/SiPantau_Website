<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\PCLModel;
use App\Models\SipantauTransaksiModel;

class PelaporanController extends BaseController
{
    use ResponseTrait;

    private $jwtKey;

    public function __construct()
    {
        $this->jwtKey = getenv('JWT_SECRET_KEY');
    }

    /**
     * 🟢 GET /api/pelaporan
     * Menampilkan semua laporan berdasarkan user (PCL) yang login
     */
    public function index()
{
    $authHeader = $this->request->getHeaderLine('Authorization');
    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        return $this->failUnauthorized('Token tidak ditemukan');
    }

    try {
        // 🔐 Decode token JWT
        $decoded = JWT::decode($matches[1], new Key($this->jwtKey, 'HS256'));
        $sobat_id = $decoded->data->sobat_id;

        // 🔎 Pastikan user adalah PCL
        $pclModel = new PCLModel();
        $pclList = $pclModel->where('sobat_id', $sobat_id)->findAll();

        if (empty($pclList)) {
            return $this->failUnauthorized('Hanya PCL yang bisa mengakses pelaporan.');
        }

        // 🔹 Ambil semua id_pcl milik user ini
        $pclIds = array_column($pclList, 'id_pcl');

        // 🔸 Ambil parameter filter dari query string (opsional)
        $filterIdPcl = $this->request->getGet('id_pcl');

        // 🔒 Validasi jika id_pcl dikirim, pastikan milik user
        if (!empty($filterIdPcl) && !in_array($filterIdPcl, $pclIds)) {
            return $this->failForbidden('Anda tidak memiliki akses ke id_pcl tersebut.');
        }

        // 🔍 Model transaksi
        $transaksiModel = new \App\Models\SipantauTransaksiModel();

        // 🔧 Query dasar
        $builder = $transaksiModel
            ->select("
                sipantau_transaksi.id_sipantau_transaksi,
                sipantau_transaksi.resume,
                sipantau_transaksi.latitude,
                sipantau_transaksi.longitude,
                sipantau_transaksi.imagepath,
                sipantau_transaksi.created_at,
                mk.nama_kegiatan,
                mkdp.nama_kegiatan_detail_proses,
                kab.nama_kabupaten,
                kec.nama_kecamatan,
                des.nama_desa
            ")
            ->join('pcl', 'pcl.id_pcl = sipantau_transaksi.id_pcl')
            ->join('pml', 'pml.id_pml = pcl.id_pml')
            ->join('kegiatan_wilayah kw', 'kw.id_kegiatan_wilayah = pml.id_kegiatan_wilayah')
            ->join('master_kegiatan_detail_proses mkdp', 'mkdp.id_kegiatan_detail_proses = sipantau_transaksi.id_kegiatan_detail_proses')
            ->join('master_kegiatan_detail mkd', 'mkd.id_kegiatan_detail = mkdp.id_kegiatan_detail')
            ->join('master_kegiatan mk', 'mk.id_kegiatan = mkd.id_kegiatan')
            ->join('master_kabupaten kab', 'kab.id_kabupaten = kw.id_kabupaten', 'left')
            ->join('master_kecamatan kec', 'kec.id_kecamatan = sipantau_transaksi.id_kecamatan', 'left')
            ->join('master_desa des', 'des.id_desa = sipantau_transaksi.id_desa', 'left')
            ->orderBy('sipantau_transaksi.created_at', 'DESC');

        // 🔹 Jika ada filter id_pcl, gunakan itu
        if (!empty($filterIdPcl)) {
            $builder->where('sipantau_transaksi.id_pcl', $filterIdPcl);
        } else {
            $builder->whereIn('sipantau_transaksi.id_pcl', $pclIds);
        }

        $laporan = $builder->findAll();

        // 🖼️ Ubah imagepath jadi URL lengkap
        foreach ($laporan as &$item) {
            $item['image_url'] = !empty($item['imagepath'])
                ? base_url($item['imagepath'])
                : null;
        }

        return $this->respond([
            'status' => 'success',
            'total_laporan' => count($laporan),
            'data' => $laporan
        ]);

    } catch (\Firebase\JWT\ExpiredException $e) {
        return $this->failUnauthorized('Token kadaluarsa');
    } catch (\Exception $e) {
        return $this->failUnauthorized('Token tidak valid: ' . $e->getMessage());
    }
}




    /**
     *  POST /api/pelaporan
     * Membuat laporan baru oleh PCL
     */
    public function create()
{
    $authHeader = $this->request->getHeaderLine('Authorization');
    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        return $this->failUnauthorized('Token tidak ditemukan');
    }

    try {
        // 🔐 Decode token JWT
        $decoded = \Firebase\JWT\JWT::decode($matches[1], new \Firebase\JWT\Key($this->jwtKey, 'HS256'));
        $sobat_id = $decoded->data->sobat_id;

        // Ambil data dari multipart form
        $data = $this->request->getPost();
        $image = $this->request->getFile('image');

        // 🧩 Validasi input wajib
        if (empty($data['id_pcl']) || empty($data['id_kegiatan_detail_proses']) || empty($data['resume'])) {
            return $this->failValidationErrors('id_pcl, kegiatan, dan resume wajib diisi.');
        }

        // 🔎 Verifikasi bahwa id_pcl memang milik user yang login
        $pclModel = new \App\Models\PCLModel();
        $pcl = $pclModel
            ->where('id_pcl', $data['id_pcl'])
            ->where('sobat_id', $sobat_id)
            ->first();

        if (!$pcl) {
            return $this->failUnauthorized('id_pcl tidak valid atau bukan milik user ini.');
        }

        // 📸 Proses upload gambar (jika ada)
        $imagePath = '';
        if ($image && $image->isValid() && !$image->hasMoved()) {

            // Path folder upload di public
            $uploadDir = FCPATH . 'uploads/laporan/';

            // Buat folder jika belum ada
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Buat nama unik dan pindahkan file
            $newName = $image->getRandomName();
            $image->move($uploadDir, $newName);

            // Simpan path relatif agar bisa diakses oleh frontend
            $imagePath = 'uploads/laporan/' . $newName;

            log_message('info', 'File uploaded successfully: ' . $imagePath);
        } else {
            log_message('warning', 'Tidak ada file image yang diupload atau tidak valid.');
        }

        // 💾 Simpan data ke database
        $transaksiModel = new \App\Models\SipantauTransaksiModel();
        $insertData = [
            'id_pcl' => $data['id_pcl'],
            'id_kegiatan_detail_proses' => $data['id_kegiatan_detail_proses'],
            'resume' => $data['resume'],
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'id_kecamatan' => $data['id_kecamatan'] ?? null,
            'id_desa' => $data['id_desa'] ?? null,
            'imagepath' => $imagePath,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $transaksiModel->insert($insertData);

        return $this->respondCreated([
            'status' => 'success',
            'message' => 'Pelaporan berhasil disimpan',
            'data' => $insertData
        ]);

    } catch (\Firebase\JWT\ExpiredException $e) {
        return $this->failUnauthorized('Token kadaluarsa');
    } catch (\Exception $e) {
        return $this->failUnauthorized('Token tidak valid: ' . $e->getMessage());
    }
}


   /**
 * 🔴 DELETE /api/pelaporan/{id}
 * Menghapus laporan milik sendiri dan file gambarnya
 */
public function delete($id = null)
{
    $authHeader = $this->request->getHeaderLine('Authorization');
    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        return $this->failUnauthorized('Token tidak ditemukan');
    }

    try {
        $decoded = JWT::decode($matches[1], new Key($this->jwtKey, 'HS256'));
        $sobat_id = $decoded->data->sobat_id;

        $pclModel = new PCLModel();
        $pcl = $pclModel->where('sobat_id', $sobat_id)->first();

        if (!$pcl) {
            return $this->failUnauthorized('Hanya PCL yang bisa menghapus pelaporan.');
        }

        $transaksiModel = new SipantauTransaksiModel();
        $laporan = $transaksiModel->find($id);

        if (!$laporan || $laporan['id_pcl'] != $pcl['id_pcl']) {
            return $this->failForbidden('Laporan tidak ditemukan atau bukan milik Anda.');
        }

        // 🧹 Hapus file gambar dari direktori publik
        if (!empty($laporan['imagepath'])) {
            // Pastikan path mengarah ke public/
            $filePath = FCPATH . $laporan['imagepath'];

            if (file_exists($filePath)) {
                unlink($filePath);
                log_message('info', 'File dihapus: ' . $filePath);
            } else {
                log_message('warning', 'File tidak ditemukan: ' . $filePath);
            }
        }

        // 🗑️ Hapus data dari database
        $transaksiModel->delete($id);

        return $this->respondDeleted([
            'status' => 'success',
            'message' => 'Laporan dan file gambar berhasil dihapus'
        ]);

    } catch (\Firebase\JWT\ExpiredException $e) {
        return $this->failUnauthorized('Token kadaluarsa');
    } catch (\Exception $e) {
        return $this->failUnauthorized('Token tidak valid: ' . $e->getMessage());
    }
}

}
