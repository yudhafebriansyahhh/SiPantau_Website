<?php

namespace App\Controllers\PemantauKab;

use App\Controllers\BaseController;
use App\Models\UserModel;

class DataPetugasController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // Get role dan user info dari session
        $role = session()->get('role');
        $roleType = session()->get('role_type');
        $sobatId = session()->get('sobat_id');

        $isPemantauKabupaten = ($role == 3 && $roleType == 'pemantau_kabupaten');

        if (!$isPemantauKabupaten) {
            return redirect()->to(base_url('unauthorized'))
                ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Get kabupaten user dari database
        $user = $this->userModel->find($sobatId);

        if (!$user || !$user['id_kabupaten']) {
            return redirect()->to(base_url('unauthorized'))
                ->with('error', 'Data kabupaten tidak ditemukan.');
        }

        $idKabupaten = $user['id_kabupaten'];

        // Ambil parameter search dari GET
        $search = $this->request->getGet('search');

        // Ambil perPage dari GET, default 10
        $perPage = $this->request->getGet('perPage') ?? 10;

        // Validasi perPage agar hanya nilai yang diizinkan
        $allowedPerPage = [5, 10, 25, 50, 100];
        if (!in_array((int) $perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        // Get data petugas - HANYA untuk kabupaten user ini
        $dataPetugas = $this->userModel->getUsersWithPetugasHistory($idKabupaten, $search, $perPage);

        // Get nama kabupaten untuk ditampilkan
        $kabupaten = $this->db->table('master_kabupaten')
            ->where('id_kabupaten', $idKabupaten)
            ->get()
            ->getRowArray();

        $data = [
            'title' => 'Pemantau Kabupaten - Data Petugas',
            'active_menu' => 'data-petugas',
            'dataPetugas' => $dataPetugas['data'],
            'search' => $search,
            'perPage' => $perPage,
            'pager' => $dataPetugas['pager'],
            'totalData' => $dataPetugas['total'],
            'kabupaten' => $kabupaten
        ];

        return view('PemantauKabupaten/DataPetugas/index', $data);
    }

    /**
     * Detail petugas - list kegiatan yang pernah diikuti
     */
    public function detailPetugas($sobatId)
    {
        // Get petugas info
        $petugas = $this->userModel->where('sobat_id', $sobatId)->first();

        if (!$petugas) {
            return redirect()->to('pemantau-kabupaten/data-petugas')->with('error', 'Petugas tidak ditemukan');
        }

        // Get kegiatan sebagai PML
        $kegiatanPML = $this->db->table('pml')
            ->select('pml.id_pml as id, pml.target, pml.created_at,
             mk.nama_kegiatan, mkd.nama_kegiatan_detail, 
             mkdp.nama_kegiatan_detail_proses, mkdp.tanggal_mulai, mkdp.tanggal_selesai,
             mkab.nama_kabupaten,
             "PML" as role')
            ->join('kegiatan_wilayah kw', 'pml.id_kegiatan_wilayah = kw.id_kegiatan_wilayah')
            ->join('master_kabupaten mkab', 'kw.id_kabupaten = mkab.id_kabupaten')
            ->join('master_kegiatan_detail_proses mkdp', 'kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses')
            ->join('master_kegiatan_detail mkd', 'mkdp.id_kegiatan_detail = mkd.id_kegiatan_detail')
            ->join('master_kegiatan mk', 'mkd.id_kegiatan = mk.id_kegiatan')
            ->where('pml.sobat_id', $sobatId)
            ->orderBy('mkdp.tanggal_mulai', 'DESC')
            ->get()
            ->getResultArray();

        // Get kegiatan sebagai PCL
        $kegiatanPCL = $this->db->table('pcl')
            ->select('pcl.id_pcl as id, pcl.target, pcl.created_at,
             mk.nama_kegiatan, mkd.nama_kegiatan_detail, 
             mkdp.nama_kegiatan_detail_proses, mkdp.tanggal_mulai, mkdp.tanggal_selesai,
             mkab.nama_kabupaten,
             u_pml.nama_user as nama_pml, "PCL" as role')
            ->join('pml', 'pcl.id_pml = pml.id_pml')
            ->join('sipantau_user u_pml', 'pml.sobat_id = u_pml.sobat_id')
            ->join('kegiatan_wilayah kw', 'pml.id_kegiatan_wilayah = kw.id_kegiatan_wilayah')
            ->join('master_kabupaten mkab', 'kw.id_kabupaten = mkab.id_kabupaten')
            ->join('master_kegiatan_detail_proses mkdp', 'kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses')
            ->join('master_kegiatan_detail mkd', 'mkdp.id_kegiatan_detail = mkd.id_kegiatan_detail')
            ->join('master_kegiatan mk', 'mkd.id_kegiatan = mk.id_kegiatan')
            ->where('pcl.sobat_id', $sobatId)
            ->orderBy('mkdp.tanggal_mulai', 'DESC')
            ->get()
            ->getResultArray();

        // Merge dan sort semua kegiatan
        $kegiatanList = array_merge($kegiatanPML, $kegiatanPCL);
        usort($kegiatanList, function ($a, $b) {
            return strtotime($b['tanggal_mulai']) - strtotime($a['tanggal_mulai']);
        });

        $data = [
            'title' => 'Detail Petugas',
            'active_menu' => 'data-petugas',
            'petugas' => $petugas,
            'kegiatanList' => $kegiatanList
        ];

        return view('PemantauKabupaten/DataPetugas/detail_petugas', $data);
    }

    /**
     * Detail PCL - laporan progress dan transaksi
     */
    public function detailPCL($idPCL)
    {
        $pclDetail = $this->db->table('pcl')
            ->select('pcl.*, 
             u.nama_user as nama_pcl, 
             u.email, 
             u.hp,
             u.id_kabupaten,
             u_pml.nama_user as nama_pml,
             mk.nama_kabupaten,
             mkdp.nama_kegiatan_detail_proses,
             mkdp.tanggal_mulai,
             mkdp.tanggal_selesai,
             mkd.nama_kegiatan_detail')
            ->join('sipantau_user u', 'pcl.sobat_id = u.sobat_id')
            ->join('pml', 'pcl.id_pml = pml.id_pml')
            ->join('sipantau_user u_pml', 'pml.sobat_id = u_pml.sobat_id')
            ->join('kegiatan_wilayah kw', 'pml.id_kegiatan_wilayah = kw.id_kegiatan_wilayah')
            ->join('master_kabupaten mk', 'kw.id_kabupaten = mk.id_kabupaten')
            ->join('master_kegiatan_detail_proses mkdp', 'kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses')
            ->join('master_kegiatan_detail mkd', 'mkdp.id_kegiatan_detail = mkd.id_kegiatan_detail')
            ->where('pcl.id_pcl', $idPCL)
            ->get()
            ->getRowArray();

        if (!$pclDetail) {
            return redirect()->to('pemantau-kabupaten/data-petugas')->with('error', 'Data PCL tidak ditemukan');
        }

        $pantauProgressModel = new \App\Models\PantauProgressModel();
        $kurvaPetugasModel = new \App\Models\KurvaPetugasModel();

        $realisasi = $pantauProgressModel
            ->select('COALESCE(MAX(jumlah_realisasi_kumulatif), 0) as total_realisasi')
            ->where('id_pcl', $idPCL)
            ->first();

        $realisasiKumulatif = (int) ($realisasi['total_realisasi'] ?? 0);
        $target = (int) $pclDetail['target'];
        $persentase = $target > 0 ? round(($realisasiKumulatif / $target) * 100, 2) : 0;
        $selisih = $target - $realisasiKumulatif;

        $kurvaData = $this->getKurvaDataPCL($idPCL, $pclDetail, $pantauProgressModel, $kurvaPetugasModel);

        $data = [
            'title' => 'Detail Laporan PCL',
            'active_menu' => 'data-petugas',
            'pcl' => $pclDetail,
            'target' => $target,
            'realisasi' => $realisasiKumulatif,
            'persentase' => $persentase,
            'selisih' => $selisih,
            'kurvaData' => $kurvaData,
            'idPCL' => $idPCL
        ];

        return view('PemantauKabupaten/DataPetugas/detail_pcl', $data);
    }

    /**
     * Detail PML - progress dari PCL yang dipegang
     */
    public function detailPML($idPML)
    {
        $pmlModel = new \App\Models\PMLModel();
        $pmlDetail = $pmlModel->getPMLWithDetails($idPML);

        if (!$pmlDetail) {
            return redirect()->to('pemantau-kabupaten/data-petugas')->with('error', 'Data PML tidak ditemukan');
        }

        $pantauProgressModel = new \App\Models\PantauProgressModel();

        $pclList = $this->db->table('pcl')
            ->select('pcl.id_pcl, pcl.target, pcl.sobat_id,
             u.nama_user as nama_pcl, u.email, u.hp')
            ->join('sipantau_user u', 'pcl.sobat_id = u.sobat_id')
            ->where('pcl.id_pml', $idPML)
            ->orderBy('u.nama_user', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($pclList as &$pcl) {
            $realisasi = $pantauProgressModel
                ->select('COALESCE(MAX(jumlah_realisasi_kumulatif), 0) as realisasi_kumulatif')
                ->where('id_pcl', $pcl['id_pcl'])
                ->first();

            $pcl['realisasi_kumulatif'] = $realisasi['realisasi_kumulatif'] ?? 0;
        }

        $data = [
            'title' => 'Detail PML',
            'active_menu' => 'data-petugas',
            'pml' => $pmlDetail,
            'pclList' => $pclList
        ];

        return view('PemantauKabupaten/DataPetugas/detail_pml', $data);
    }

    // HELPER METHOD
    private function getKurvaDataPCL($idPCL, $pclDetail, $pantauProgressModel, $kurvaPetugasModel)
    {
        $kurvaTarget = $kurvaPetugasModel
            ->where('id_pcl', $idPCL)
            ->orderBy('tanggal_target', 'ASC')
            ->findAll();

        $realisasiHarian = $this->db->query("
        SELECT 
            DATE(created_at) as tanggal,
            SUM(jumlah_realisasi_absolut) as realisasi_harian
        FROM pantau_progress
        WHERE id_pcl = ?
        GROUP BY DATE(created_at)
        ORDER BY DATE(created_at) ASC
    ", [$idPCL])->getResultArray();

        $realisasiLookup = [];
        foreach ($realisasiHarian as $item) {
            $realisasiLookup[$item['tanggal']] = (int) $item['realisasi_harian'];
        }

        $labels = [];
        $targetData = [];
        $realisasiData = [];
        $realisasiKumulatif = 0;

        foreach ($kurvaTarget as $item) {
            $tanggal = $item['tanggal_target'];
            $labels[] = date('d M', strtotime($tanggal));
            $targetData[] = (int) $item['target_kumulatif_absolut'];

            if (isset($realisasiLookup[$tanggal])) {
                $realisasiKumulatif += $realisasiLookup[$tanggal];
            }
            $realisasiData[] = $realisasiKumulatif;
        }

        return [
            'labels' => $labels,
            'target' => $targetData,
            'realisasi' => $realisasiData,
            'config' => [
                'tanggal_mulai' => date('d M', strtotime($pclDetail['tanggal_mulai'])),
                'tanggal_selesai' => date('d M', strtotime($pclDetail['tanggal_selesai']))
            ]
        ];
    }

    // AJAX METHODS
    public function getPantauProgress()
    {
        $idPCL = $this->request->getGet('id_pcl');
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $pantauProgressModel = new \App\Models\PantauProgressModel();

        $total = $pantauProgressModel->where('id_pcl', $idPCL)->countAllResults(false);
        $data = $pantauProgressModel
            ->where('id_pcl', $idPCL)
            ->orderBy('created_at', 'DESC')
            ->limit($perPage, $offset)
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'perPage' => $perPage,
                'currentPage' => (int) $page,
                'totalPages' => ceil($total / $perPage)
            ]
        ]);
    }

    public function getLaporanTransaksi()
    {
        $idPCL = $this->request->getGet('id_pcl');
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $builder = $this->db->table('sipantau_transaksi st');

        $totalBuilder = clone $builder;
        $total = $totalBuilder->where('st.id_pcl', $idPCL)->countAllResults();

        if ($total == 0) {
            return $this->response->setJSON([
                'success' => true,
                'data' => [],
                'pagination' => [
                    'total' => 0,
                    'perPage' => $perPage,
                    'currentPage' => $page,
                    'totalPages' => 0
                ]
            ]);
        }

        $data = $builder
            ->select('st.id_sipantau_transaksi,
             st.resume,
             st.latitude,
             st.longitude,
             st.imagepath,
             st.created_at,
             COALESCE(mk.nama_kecamatan, "-") as nama_kecamatan,
             COALESCE(md.nama_desa, "-") as nama_desa')
            ->join('master_kecamatan mk', 'st.id_kecamatan = mk.id_kecamatan', 'left')
            ->join('master_desa md', 'st.id_desa = md.id_desa', 'left')
            ->where('st.id_pcl', $idPCL)
            ->orderBy('st.created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'perPage' => $perPage,
                'currentPage' => $page,
                'totalPages' => ceil($total / $perPage)
            ]
        ]);
    }
}