<?php

namespace App\Controllers\AdminProv;

use App\Controllers\BaseController;
use App\Models\MasterKabModel;
use App\Models\MasterKegiatanDetailProsesModel;
use App\Models\MasterKegiatanWilayahModel;
use App\Models\MasterKegiatanDetailAdminModel;
use App\Models\MasterKegiatanDetailModel;
use App\Models\KurvaSkabModel;
use DateInterval;
use DatePeriod;
use DateTime;

class MasterKegiatanWilayahController extends BaseController
{
    protected $masterDetailProsesModel;
    protected $masterKegiatanWilayahModel;
    protected $masterDetailAdminModel;
    protected $masterDetailModel;
    protected $masterKab;
    protected $validation;
    protected $db;

    public function __construct()
    {
        $this->masterDetailProsesModel = new MasterKegiatanDetailProsesModel();
        $this->masterKegiatanWilayahModel = new MasterKegiatanWilayahModel();
        $this->masterDetailAdminModel = new MasterKegiatanDetailAdminModel();
        $this->masterDetailModel = new MasterKegiatanDetailModel();
        $this->validation = \Config\Services::validation();
        $this->masterKab = new MasterKabModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // Get role dan admin ID dari session
        $role = session()->get('role');
        $roleType = session()->get('role_type');
        $idAdminProvinsi = session()->get('id_admin_provinsi');

        $isSuperAdmin = ($role == 1);
        $isAdminProvinsi = ($role == 2 && $roleType == 'admin_provinsi' && $idAdminProvinsi);

        if (!$isSuperAdmin && !$isAdminProvinsi) {
            return redirect()->to(base_url('unauthorized'))
                ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Get filter parameters dari GET atau session
        $filterKegiatan = $this->request->getGet('kegiatan_detail') ?? session()->get('kegiatan_wilayah_filter_detail');
        $filterProses = $this->request->getGet('kegiatan_proses') ?? session()->get('kegiatan_wilayah_filter_proses');
        $filterKabupaten = $this->request->getGet('kabupaten') ?? session()->get('kegiatan_wilayah_filter_kabupaten');

        // Simpan filter ke session
        if ($this->request->getGet('kegiatan_detail') !== null) {
            session()->set('kegiatan_wilayah_filter_detail', $filterKegiatan);
        }
        if ($this->request->getGet('kegiatan_proses') !== null) {
            session()->set('kegiatan_wilayah_filter_proses', $filterProses);
        }
        if ($this->request->getGet('kabupaten') !== null) {
            session()->set('kegiatan_wilayah_filter_kabupaten', $filterKabupaten);
        }

        // Build query dengan progress
        $builder = $this->db->table('kegiatan_wilayah kw')
            ->select('kw.*, mkdp.nama_kegiatan_detail_proses, mkdp.target as target_proses, 
                      mkdp.tanggal_mulai, mkdp.tanggal_selesai, mk.nama_kabupaten,
                      mkd.nama_kegiatan_detail, mkg.nama_kegiatan, mkdp.id_kegiatan_detail')
            ->join('master_kegiatan_detail_proses mkdp', 'kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses')
            ->join('master_kegiatan_detail mkd', 'mkdp.id_kegiatan_detail = mkd.id_kegiatan_detail')
            ->join('master_kegiatan mkg', 'mkd.id_kegiatan = mkg.id_kegiatan')
            ->join('master_kabupaten mk', 'kw.id_kabupaten = mk.id_kabupaten');

        // Filter berdasarkan assignment untuk Admin Provinsi
        if ($isAdminProvinsi) {
            $builder->join('master_kegiatan_detail_admin mkda', 'mkda.id_kegiatan_detail = mkdp.id_kegiatan_detail')
                ->where('mkda.id_admin_provinsi', $idAdminProvinsi);
        }

        $builder->orderBy('mkd.nama_kegiatan_detail', 'ASC')
            ->orderBy('mk.nama_kabupaten', 'ASC');

        // Apply filters
        if (!empty($filterKegiatan)) {
            $builder->where('mkdp.id_kegiatan_detail', $filterKegiatan);
        }

        if (!empty($filterProses)) {
            $builder->where('kw.id_kegiatan_detail_proses', $filterProses);
        }

        if (!empty($filterKabupaten)) {
            $builder->where('kw.id_kabupaten', $filterKabupaten);
        }

        $kegiatanWilayah = $builder->get()->getResultArray();

        // Calculate progress for each kegiatan wilayah
        foreach ($kegiatanWilayah as &$kw) {
            $targetWilayah = (int)$kw['target_wilayah'];

            $realisasi = $this->db->table('pantau_progress pp')
                ->select('COALESCE(SUM(pp.jumlah_realisasi_kumulatif), 0) as total_realisasi', false)
                ->join('pcl', 'pp.id_pcl = pcl.id_pcl')
                ->join('pml', 'pcl.id_pml = pml.id_pml')
                ->where('pml.id_kegiatan_wilayah', $kw['id_kegiatan_wilayah'])
                ->groupBy('pp.id_pcl')
                ->get()
                ->getResultArray();

            $totalRealisasi = 0;
            foreach ($realisasi as $item) {
                $totalRealisasi += (int)$item['total_realisasi'];
            }

            if ($targetWilayah > 0) {
                $progressPercentage = min(100, round(($totalRealisasi / $targetWilayah) * 100, 1));
            } else {
                $progressPercentage = 0;
            }

            $kw['realisasi'] = $totalRealisasi;
            $kw['progress'] = $progressPercentage;

            if ($progressPercentage >= 80) {
                $kw['progress_color'] = '#10b981';
            } elseif ($progressPercentage >= 50) {
                $kw['progress_color'] = '#3b82f6';
            } elseif ($progressPercentage >= 25) {
                $kw['progress_color'] = '#f59e0b';
            } else {
                $kw['progress_color'] = '#ef4444';
            }
        }

        // Get all kegiatan detail for filter (filtered by assignment for Admin Provinsi)
        if ($isSuperAdmin) {
            $allKegiatanDetail = $this->db->table('master_kegiatan_detail mkd')
                ->select('mkd.id_kegiatan_detail, mkd.nama_kegiatan_detail, mkg.nama_kegiatan')
                ->join('master_kegiatan mkg', 'mkd.id_kegiatan = mkg.id_kegiatan')
                ->orderBy('mkg.nama_kegiatan', 'ASC')
                ->orderBy('mkd.nama_kegiatan_detail', 'ASC')
                ->get()
                ->getResultArray();
        } else {
            $allKegiatanDetail = $this->db->table('master_kegiatan_detail mkd')
                ->select('mkd.id_kegiatan_detail, mkd.nama_kegiatan_detail, mkg.nama_kegiatan')
                ->join('master_kegiatan mkg', 'mkd.id_kegiatan = mkg.id_kegiatan')
                ->join('master_kegiatan_detail_admin mkda', 'mkda.id_kegiatan_detail = mkd.id_kegiatan_detail')
                ->where('mkda.id_admin_provinsi', $idAdminProvinsi)
                ->orderBy('mkg.nama_kegiatan', 'ASC')
                ->orderBy('mkd.nama_kegiatan_detail', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Get kegiatan detail proses berdasarkan filter kegiatan detail
        $allKegiatanDetailProses = [];
        if (!empty($filterKegiatan)) {
            $allKegiatanDetailProses = $this->masterDetailProsesModel
                ->where('id_kegiatan_detail', $filterKegiatan)
                ->orderBy('nama_kegiatan_detail_proses', 'ASC')
                ->findAll();
        }

        // Get all kabupaten for filter
        $allKabupaten = $this->masterKab->orderBy('id_kabupaten', 'ASC')->findAll();

        // Get all kegiatan detail proses for modal (filtered by assignment for Admin Provinsi)
        $filterAdminId = $isSuperAdmin ? null : $idAdminProvinsi;
        $allKegiatanDetailProsesForModal = $this->masterDetailProsesModel->getData(null, $filterAdminId);

        $data = [
            'title' => 'Kelola Master Kegiatan Wilayah',
            'active_menu' => 'master-kegiatan-wilayah',
            'kegiatanWilayah' => $kegiatanWilayah,
            'allKegiatanDetail' => $allKegiatanDetail,
            'allKegiatanDetailProses' => $allKegiatanDetailProses,
            'allKegiatanDetailProsesForModal' => $allKegiatanDetailProsesForModal,
            'allKabupaten' => $allKabupaten,
            'filterKegiatan' => $filterKegiatan,
            'filterProses' => $filterProses,
            'filterKabupaten' => $filterKabupaten,
            'isSuperAdmin' => $isSuperAdmin
        ];
        return view('AdminSurveiProv/MasterKegiatanWilayah/index', $data);
    }

    public function store()
    {
        // Get role dan admin ID dari session
        $role = session()->get('role');
        $roleType = session()->get('role_type');
        $idAdminProvinsi = session()->get('id_admin_provinsi');

        $isSuperAdmin = ($role == 1);
        $isAdminProvinsi = ($role == 2 && $roleType == 'admin_provinsi' && $idAdminProvinsi);

        if (!$isSuperAdmin && !$isAdminProvinsi) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda tidak memiliki akses.'
            ]);
        }

        $rules = [
            'kegiatan_detail' => 'required|numeric',
            'kabupaten'       => 'required|numeric',
            'target'          => 'required|numeric',
            'keterangan'      => 'required|string|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        $idDetailProses = $this->request->getPost('kegiatan_detail');
        $idKabupaten = $this->request->getPost('kabupaten');
        $targetWilayah = (int)$this->request->getPost('target');
        $keterangan = $this->request->getPost('keterangan');

        // Validasi untuk Admin Provinsi: Pastikan kegiatan detail proses ini accessible
        if ($isAdminProvinsi) {
            $detailProses = $this->masterDetailProsesModel->find($idDetailProses);
            if ($detailProses) {
                $hasAccess = $this->masterDetailAdminModel
                    ->where('id_kegiatan_detail', $detailProses['id_kegiatan_detail'])
                    ->where('id_admin_provinsi', $idAdminProvinsi)
                    ->first();

                if (!$hasAccess) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses ke kegiatan ini.'
                    ]);
                }
            }
        }

        // Check duplicate
        $existingWilayah = $this->masterKegiatanWilayahModel
            ->where('id_kegiatan_detail_proses', $idDetailProses)
            ->where('id_kabupaten', $idKabupaten)
            ->first();

        if ($existingWilayah) {
            $kabupatenInfo = $this->masterKab->find($idKabupaten);
            $kabupatenName = $kabupatenInfo['nama_kabupaten'] ?? 'Kabupaten ini';

            return $this->response->setJSON([
                'success' => false,
                'message' => $kabupatenName . ' sudah ditambahkan pada kegiatan detail proses ini.'
            ]);
        }

        $detailProses = $this->masterDetailProsesModel->find($idDetailProses);
        if (!$detailProses) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data kegiatan detail proses tidak ditemukan.'
            ]);
        }

        $targetProv = (int)$detailProses['target'];
        $tanggalMulai = $detailProses['tanggal_mulai'];
        $tanggalSelesai = $detailProses['tanggal_selesai'];
        $tanggal100 = $detailProses['tanggal_selesai_target'] ?? $tanggalSelesai;
        $persenAwal = $detailProses['persentase_target_awal'] ?? 0;

        // Validasi total target
        $totalExisting = $this->masterKegiatanWilayahModel
            ->where('id_kegiatan_detail_proses', $idDetailProses)
            ->selectSum('target_wilayah')
            ->get()
            ->getRow()
            ->target_wilayah ?? 0;

        if (($totalExisting + $targetWilayah) > $targetProv) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Total target wilayah melebihi target provinsi (' . $targetProv . '). Sisa target: ' . ($targetProv - $totalExisting) . '.'
            ]);
        }

        $this->masterKegiatanWilayahModel->insert([
            'id_kegiatan_detail_proses' => $idDetailProses,
            'id_kabupaten'              => $idKabupaten,
            'target_wilayah'            => $targetWilayah,
            'keterangan'                => $keterangan,
            'status'                    => 'Aktif'
        ]);

        $idWilayah = $this->masterKegiatanWilayahModel->getInsertID();

        // Generate kurva S
        $this->generateKurvaSKab($idWilayah, $targetWilayah, $persenAwal, $tanggalMulai, $tanggal100, $tanggalSelesai);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data kegiatan wilayah dan Kurva S berhasil dibuat.'
        ]);
    }

    public function edit($id)
    {
        // Tambahkan validasi akses
        $role = session()->get('role');
        $roleType = session()->get('role_type');
        $idAdminProvinsi = session()->get('id_admin_provinsi');

        $isSuperAdmin = ($role == 1);
        $isAdminProvinsi = ($role == 2 && $roleType == 'admin_provinsi' && $idAdminProvinsi);

        $wilayah = $this->masterKegiatanWilayahModel->find($id);
        if (!$wilayah) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        // Validasi akses untuk Admin Provinsi
        if ($isAdminProvinsi) {
            $detailProses = $this->masterDetailProsesModel->find($wilayah['id_kegiatan_detail_proses']);
            if ($detailProses) {
                $hasAccess = $this->masterDetailAdminModel
                    ->where('id_kegiatan_detail', $detailProses['id_kegiatan_detail'])
                    ->where('id_admin_provinsi', $idAdminProvinsi)
                    ->first();

                if (!$hasAccess) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses ke kegiatan ini.'
                    ]);
                }
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $wilayah
        ]);
    }

    public function update($id)
    {
        // Get role dan admin ID dari session
        $role = session()->get('role');
        $roleType = session()->get('role_type');
        $idAdminProvinsi = session()->get('id_admin_provinsi');

        $isSuperAdmin = ($role == 1);
        $isAdminProvinsi = ($role == 2 && $roleType == 'admin_provinsi' && $idAdminProvinsi);

        if (!$isSuperAdmin && !$isAdminProvinsi) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda tidak memiliki akses.'
            ]);
        }

        $rules = [
            'kegiatan_detail' => 'required|numeric',
            'kabupaten'       => 'required|numeric',
            'target'          => 'required|numeric',
            'keterangan'      => 'required|string|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        $wilayah = $this->masterKegiatanWilayahModel->find($id);
        if (!$wilayah) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data kegiatan wilayah tidak ditemukan.'
            ]);
        }

        $idDetailProses = $this->request->getPost('kegiatan_detail');
        $idKabupaten = $this->request->getPost('kabupaten');
        $targetWilayah = (int)$this->request->getPost('target');
        $keterangan = $this->request->getPost('keterangan');

        // Validasi untuk Admin Provinsi: Pastikan kegiatan detail proses ini accessible
        if ($isAdminProvinsi) {
            $detailProses = $this->masterDetailProsesModel->find($idDetailProses);
            if ($detailProses) {
                $hasAccess = $this->masterDetailAdminModel
                    ->where('id_kegiatan_detail', $detailProses['id_kegiatan_detail'])
                    ->where('id_admin_provinsi', $idAdminProvinsi)
                    ->first();

                if (!$hasAccess) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses ke kegiatan ini.'
                    ]);
                }
            }
        }

        // Check duplicate
        $existingWilayah = $this->masterKegiatanWilayahModel
            ->where('id_kegiatan_detail_proses', $idDetailProses)
            ->where('id_kabupaten', $idKabupaten)
            ->where('id_kegiatan_wilayah !=', $id)
            ->first();

        if ($existingWilayah) {
            $kabupatenInfo = $this->masterKab->find($idKabupaten);
            $kabupatenName = $kabupatenInfo['nama_kabupaten'] ?? 'Kabupaten ini';

            return $this->response->setJSON([
                'success' => false,
                'message' => $kabupatenName . ' sudah ditambahkan pada kegiatan detail proses ini.'
            ]);
        }

        $detailProses = $this->masterDetailProsesModel->find($idDetailProses);
        if (!$detailProses) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data kegiatan detail proses tidak ditemukan.'
            ]);
        }

        $targetProv = (int)$detailProses['target'];
        $tanggalMulai = $detailProses['tanggal_mulai'];
        $tanggalSelesai = $detailProses['tanggal_selesai'];
        $tanggal100 = $detailProses['tanggal_selesai_target'] ?? $tanggalSelesai;
        $persenAwal = $detailProses['persentase_target_awal'] ?? 0;

        // Validasi total target (exclude current)
        $totalExisting = $this->masterKegiatanWilayahModel
            ->where('id_kegiatan_detail_proses', $idDetailProses)
            ->where('id_kegiatan_wilayah !=', $id)
            ->selectSum('target_wilayah')
            ->get()
            ->getRow()
            ->target_wilayah ?? 0;

        if (($totalExisting + $targetWilayah) > $targetProv) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Total target wilayah melebihi target provinsi (' . number_format($targetProv) . '). Sisa target: ' . number_format($targetProv - $totalExisting) . '.'
            ]);
        }

        // Update data
        $this->masterKegiatanWilayahModel->update($id, [
            'id_kegiatan_detail_proses' => $idDetailProses,
            'id_kabupaten'              => $idKabupaten,
            'target_wilayah'            => $targetWilayah,
            'keterangan'                => $keterangan,
            'updated_at'                => date('Y-m-d H:i:s')
        ]);

        // Regenerate kurva S
        $this->generateKurvaSKab($id, $targetWilayah, $persenAwal, $tanggalMulai, $tanggal100, $tanggalSelesai);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data kegiatan wilayah dan Kurva S berhasil diperbarui.'
        ]);
    }

    public function delete($id)
    {
        // Get role dan admin ID dari session
        $role = session()->get('role');
        $roleType = session()->get('role_type');
        $idAdminProvinsi = session()->get('id_admin_provinsi');

        $isSuperAdmin = ($role == 1);
        $isAdminProvinsi = ($role == 2 && $roleType == 'admin_provinsi' && $idAdminProvinsi);

        if (!$isSuperAdmin && !$isAdminProvinsi) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda tidak memiliki akses.'
            ]);
        }

        $wilayah = $this->masterKegiatanWilayahModel->find($id);
        if (!$wilayah) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ]);
        }

        // Validasi akses untuk Admin Provinsi
        if ($isAdminProvinsi) {
            $detailProses = $this->masterDetailProsesModel->find($wilayah['id_kegiatan_detail_proses']);
            if ($detailProses) {
                $hasAccess = $this->masterDetailAdminModel
                    ->where('id_kegiatan_detail', $detailProses['id_kegiatan_detail'])
                    ->where('id_admin_provinsi', $idAdminProvinsi)
                    ->first();

                if (!$hasAccess) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses ke kegiatan ini.'
                    ]);
                }
            }
        }

        // Delete kurva S terlebih dahulu
        $kurvaModel = new KurvaSkabModel();
        $kurvaModel->where('id_kegiatan_wilayah', $id)->delete();

        // Delete kegiatan wilayah
        $this->masterKegiatanWilayahModel->delete($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data kegiatan wilayah dan kurva S terkait berhasil dihapus.'
        ]);
    }

    private function generateKurvaSKab($idWilayah, $target, $persenAwal, $tanggalMulai, $tanggal100, $tanggalSelesai)
    {
        $kurvaModel = new KurvaSkabModel();
        $kurvaModel->where('id_kegiatan_wilayah', $idWilayah)->delete();

        $totalTarget = (int)$target;
        $persenAwal  = (float)$persenAwal;

        $start   = new DateTime($tanggalMulai);
        $tgl100  = new DateTime($tanggal100);
        $end     = new DateTime($tanggalSelesai);
        $end->modify('+1 day');

        $interval = new DateInterval('P1D');
        $periodTotal = iterator_to_array(new DatePeriod($start, $interval, $end));
        $workdays = array_filter($periodTotal, fn($d) => $d->format('N') <= 5);
        $workdays = array_values($workdays);
        $workdaysUntil100 = array_filter($workdays, fn($d) => $d <= $tgl100);
        $workdaysUntil100 = array_values($workdaysUntil100);

        $daysSigmoid = max(count($workdaysUntil100), 2);
        $k = 8;
        $x0 = 0.5;
        $sigmoidMin = 1 / (1 + exp(-$k * (0 - $x0)));
        $sigmoidMax = 1 / (1 + exp(-$k * (1 - $x0)));

        $workdayData = [];
        foreach ($workdaysUntil100 as $i => $date) {
            $progress = $i / ($daysSigmoid - 1);
            $sigmoid = 1 / (1 + exp(-$k * ($progress - $x0)));
            $normalizedSigmoid = ($sigmoid - $sigmoidMin) / ($sigmoidMax - $sigmoidMin);
            $kumulatifPersen = $persenAwal + (100 - $persenAwal) * $normalizedSigmoid;
            $workdayData[$date->format('Y-m-d')] = min($kumulatifPersen, 100);
        }

        $kumulatifAbsolut = 0;
        $insertData = [];
        foreach ($workdaysUntil100 as $date) {
            $current = $date->format('Y-m-d');
            $kumulatifPersen = $workdayData[$current];
            $harianAbsolut = round(($totalTarget * ($kumulatifPersen / 100)) - $kumulatifAbsolut);
            $kumulatifAbsolut += $harianAbsolut;

            $insertData[] = [
                'id_kegiatan_wilayah' => $idWilayah,
                'tanggal_target' => $current,
                'target_persen_kumulatif' => round($kumulatifPersen, 2),
                'target_harian_absolut' => $harianAbsolut,
                'target_kumulatif_absolut' => $kumulatifAbsolut,
                'is_hari_kerja' => 1,
            ];
        }

        $selisih = $totalTarget - $kumulatifAbsolut;
        if (!empty($insertData) && $selisih !== 0) {
            $insertData[count($insertData) - 1]['target_harian_absolut'] += $selisih;
            $insertData[count($insertData) - 1]['target_kumulatif_absolut'] += $selisih;
        }

        $workdaysAfter100 = array_filter($workdays, fn($d) => $d > $tgl100);
        foreach ($workdaysAfter100 as $date) {
            $insertData[] = [
                'id_kegiatan_wilayah' => $idWilayah,
                'tanggal_target' => $date->format('Y-m-d'),
                'target_persen_kumulatif' => 100,
                'target_harian_absolut' => 0,
                'target_kumulatif_absolut' => $totalTarget,
                'is_hari_kerja' => 1,
            ];
        }

        $kurvaModel->insertBatch($insertData);
    }

    public function getSisaTarget($idKegiatanDetailProses)
    {
        $detail = $this->masterDetailProsesModel->find($idKegiatanDetailProses);
        if (!$detail) {
            return $this->response->setJSON(['error' => 'Data kegiatan detail proses tidak ditemukan.']);
        }

        $targetProv = (int) $detail['target'];
        $terpakai = (int) $this->masterKegiatanWilayahModel
            ->where('id_kegiatan_detail_proses', $idKegiatanDetailProses)
            ->selectSum('target_wilayah')
            ->get()
            ->getRow()
            ->target_wilayah ?? 0;

        $sisa = max($targetProv - $terpakai, 0);

        return $this->response->setJSON([
            'target_prov' => $targetProv,
            'terpakai'    => $terpakai,
            'sisa'        => $sisa
        ]);
    }

    // Method untuk mendapatkan kegiatan detail proses berdasarkan kegiatan detail
    public function getKegiatanDetailProses($idKegiatanDetail)
    {
        $prosesList = $this->masterDetailProsesModel
            ->where('id_kegiatan_detail', $idKegiatanDetail)
            ->orderBy('nama_kegiatan_detail_proses', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $prosesList
        ]);
    }

    // Clear filter
    public function clearFilter()
    {
        session()->remove('kegiatan_wilayah_filter_detail');
        session()->remove('kegiatan_wilayah_filter_proses');
        session()->remove('kegiatan_wilayah_filter_kabupaten');
        return redirect()->to(base_url('adminsurvei/master-kegiatan-wilayah'));
    }
}
