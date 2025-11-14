<?php

namespace App\Controllers\PemantauProv;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\MasterKabModel;
use App\Models\MasterKegiatanDetailProsesModel;
use App\Models\PantauProgressModel;
use App\Models\PCLModel;

class LaporanPetugasController extends BaseController
{
    protected $userModel;
    protected $masterKab;
    protected $kegiatanProsesModel;
    protected $pantauProgressModel;
    protected $pclModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->masterKab = new MasterKabModel();
        $this->kegiatanProsesModel = new MasterKegiatanDetailProsesModel();
        $this->pantauProgressModel = new PantauProgressModel();
        $this->pclModel = new PCLModel();
    }

    public function index()
    {
        // Ambil parameter filter dari GET
        $idKegiatanProses = $this->request->getGet('kegiatan_proses');
        $kabupatenId = $this->request->getGet('kabupaten');
        $search = $this->request->getGet('search');
        $perPage = $this->request->getGet('perPage') ?? 10;
        
        // Validasi perPage
        $allowedPerPage = [5, 10, 25, 50, 100];
        if (!in_array((int)$perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        // Ambil list kabupaten
        $kabupatenList = $this->masterKab->orderBy('id_kabupaten', 'ASC')->findAll();

        // Ambil list kegiatan proses dengan nama kegiatan detail
        $kegiatanProsesList = $this->kegiatanProsesModel
            ->select('master_kegiatan_detail_proses.*, master_kegiatan_detail.nama_kegiatan_detail')
            ->join('master_kegiatan_detail', 'master_kegiatan_detail_proses.id_kegiatan_detail = master_kegiatan_detail.id_kegiatan_detail')
            ->orderBy('master_kegiatan_detail_proses.tanggal_mulai', 'DESC')
            ->findAll();

        $dataPetugas = [];
        $dateHeaders = [];
        $totalData = 0;

        // Jika kegiatan proses dipilih, ambil data laporan
        if ($idKegiatanProses) {
            // Ambil detail kegiatan untuk tanggal
            $kegiatanDetail = $this->kegiatanProsesModel->find($idKegiatanProses);
            
            if ($kegiatanDetail) {
                // Generate tanggal (exclude Sabtu & Minggu)
                $dateHeaders = $this->generateWorkingDates(
                    $kegiatanDetail['tanggal_mulai'], 
                    $kegiatanDetail['tanggal_selesai']
                );

                // Ambil data petugas dan progress
                $dataPetugas = $this->getLaporanPetugas(
                    $idKegiatanProses, 
                    $kabupatenId, 
                    $search, 
                    $dateHeaders,
                    $perPage
                );
                
                $totalData = $dataPetugas['total'];
                $dataPetugas = $dataPetugas['data'];
            }
        }

        $data = [
            'title' => 'Pemantau - Laporan Petugas',
            'active_menu' => 'laporan-petugas',
            'kegiatanProsesList' => $kegiatanProsesList,
            'kabupatenList' => $kabupatenList,
            'dataPetugas' => $dataPetugas,
            'dateHeaders' => $dateHeaders,
            'selectedKegiatanProses' => $idKegiatanProses,
            'selectedKabupaten' => $kabupatenId,
            'search' => $search,
            'perPage' => $perPage,
            'totalData' => $totalData,
            'currentPage' => $this->request->getGet('page') ?? 1
        ];

        return view('PemantauProvinsi/LaporanPetugas/index', $data);
    }

    /**
     * Generate working dates (exclude Sabtu & Minggu)
     */
    private function generateWorkingDates($startDate, $endDate)
    {
        $dates = [];
        $current = strtotime($startDate);
        $end = strtotime($endDate);

        while ($current <= $end) {
            $dayOfWeek = date('N', $current); // 1=Senin, 7=Minggu
            
            // Skip Sabtu (6) dan Minggu (7)
            if ($dayOfWeek != 6 && $dayOfWeek != 7) {
                $dates[] = [
                    'date' => date('Y-m-d', $current),
                    'display' => date('d/m', $current)
                ];
            }
            
            $current = strtotime('+1 day', $current);
        }

        return $dates;
    }

    /**
     * Get laporan petugas dengan pagination
     */
    private function getLaporanPetugas($idKegiatanProses, $kabupatenId, $search, $dateHeaders, $perPage)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pcl')
            ->select('pcl.id_pcl, pcl.sobat_id, pcl.target, sipantau_user.nama_user, master_kabupaten.nama_kabupaten, sipantau_user.id_kabupaten')
            ->join('sipantau_user', 'pcl.sobat_id = sipantau_user.sobat_id')
            ->join('master_kabupaten', 'sipantau_user.id_kabupaten = master_kabupaten.id_kabupaten')
            ->join('pml', 'pcl.id_pml = pml.id_pml')
            ->join('kegiatan_wilayah', 'pml.id_kegiatan_wilayah = kegiatan_wilayah.id_kegiatan_wilayah')
            ->where('kegiatan_wilayah.id_kegiatan_detail_proses', $idKegiatanProses);

        // Filter kabupaten
        if ($kabupatenId) {
            $builder->where('sipantau_user.id_kabupaten', $kabupatenId);
        }

        // Search
        if ($search) {
            $builder->groupStart()
                ->like('sipantau_user.nama_user', $search)
                ->orLike('pcl.sobat_id', $search)
                ->groupEnd();
        }

        // Clone builder untuk count total sebelum pagination
        $builderCount = clone $builder;
        $total = $builderCount->countAllResults();

        // Pagination
        $page = $this->request->getGet('page') ?? 1;
        $offset = ($page - 1) * $perPage;
        
        $petugas = $builder
            ->orderBy('sipantau_user.nama_user', 'ASC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        // Ambil data progress untuk setiap petugas
        foreach ($petugas as &$p) {
            $progressData = [];
            $totalRealisasi = 0;

            foreach ($dateHeaders as $dateInfo) {
                $count = $this->pantauProgressModel
                    ->where('id_pcl', $p['id_pcl'])
                    ->where('DATE(created_at)', $dateInfo['date'])
                    ->countAllResults();
                
                $progressData[] = $count;
                $totalRealisasi += $count;
            }

            $p['progress_data'] = $progressData;
            $p['total_realisasi'] = $totalRealisasi;
            $p['status_complete'] = $totalRealisasi >= $p['target'];
        }

        return [
            'data' => $petugas,
            'total' => $total
        ];
    }

    /**
     * Export to CSV
     */
    public function exportCSV()
    {
        $idKegiatanProses = $this->request->getGet('kegiatan_proses');
        $kabupatenId = $this->request->getGet('kabupaten');
        $search = $this->request->getGet('search');

        if (!$idKegiatanProses) {
            return redirect()->back()->with('error', 'Pilih kegiatan proses terlebih dahulu');
        }

        // Ambil detail kegiatan
        $kegiatanDetail = $this->kegiatanProsesModel
            ->select('master_kegiatan_detail_proses.*, master_kegiatan_detail.nama_kegiatan_detail')
            ->join('master_kegiatan_detail', 'master_kegiatan_detail_proses.id_kegiatan_detail = master_kegiatan_detail.id_kegiatan_detail')
            ->find($idKegiatanProses);

        // Generate tanggal
        $dateHeaders = $this->generateWorkingDates(
            $kegiatanDetail['tanggal_mulai'], 
            $kegiatanDetail['tanggal_selesai']
        );

        // Ambil semua data (no pagination)
        $dataPetugas = $this->getLaporanPetugasAll($idKegiatanProses, $kabupatenId, $search, $dateHeaders);

        // Set headers untuk download CSV
        $filename = 'laporan_petugas_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Output CSV
        $output = fopen('php://output', 'w');
        
        // UTF-8 BOM untuk Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Header CSV
        $headers = array_merge(['No', 'Nama Petugas', 'Kabupaten'], array_column($dateHeaders, 'display'), ['TOTAL', 'Target', 'Status']);
        fputcsv($output, $headers);

        // Data rows
        $no = 1;
        foreach ($dataPetugas as $petugas) {
            $status = $petugas['total_realisasi'] >= $petugas['target'] ? 'Complete' : 'Incomplete';
            $row = array_merge(
                [$no++, $petugas['nama_user'], $petugas['nama_kabupaten']],
                $petugas['progress_data'],
                [$petugas['total_realisasi'], $petugas['target'], $status]
            );
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    /**
     * Get all laporan petugas (untuk export)
     */
    private function getLaporanPetugasAll($idKegiatanProses, $kabupatenId, $search, $dateHeaders)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pcl')
            ->select('pcl.id_pcl, pcl.sobat_id, pcl.target, sipantau_user.nama_user, master_kabupaten.nama_kabupaten')
            ->join('sipantau_user', 'pcl.sobat_id = sipantau_user.sobat_id')
            ->join('master_kabupaten', 'sipantau_user.id_kabupaten = master_kabupaten.id_kabupaten')
            ->join('pml', 'pcl.id_pml = pml.id_pml')
            ->join('kegiatan_wilayah', 'pml.id_kegiatan_wilayah = kegiatan_wilayah.id_kegiatan_wilayah')
            ->where('kegiatan_wilayah.id_kegiatan_detail_proses', $idKegiatanProses);

        if ($kabupatenId) {
            $builder->where('sipantau_user.id_kabupaten', $kabupatenId);
        }

        if ($search) {
            $builder->groupStart()
                ->like('sipantau_user.nama_user', $search)
                ->orLike('pcl.sobat_id', $search)
                ->groupEnd();
        }

        $petugas = $builder
            ->orderBy('sipantau_user.nama_user', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($petugas as &$p) {
            $progressData = [];
            $totalRealisasi = 0;

            foreach ($dateHeaders as $dateInfo) {
                $count = $this->pantauProgressModel
                    ->where('id_pcl', $p['id_pcl'])
                    ->where('DATE(created_at)', $dateInfo['date'])
                    ->countAllResults();
                
                $progressData[] = $count;
                $totalRealisasi += $count;
            }

            $p['progress_data'] = $progressData;
            $p['total_realisasi'] = $totalRealisasi;
        }

        return $petugas;
    }
}