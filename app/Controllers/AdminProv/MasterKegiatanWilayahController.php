<?php

namespace App\Controllers\AdminProv;

use App\Controllers\BaseController;
use App\Models\MasterKabModel;
use App\Models\MasterKegiatanDetailProsesModel;
use App\Models\MasterKegiatanWilayahModel;
use App\Models\KurvaSkabModel;
use DateInterval;
use DatePeriod;
use DateTime;

class MasterKegiatanWilayahController extends BaseController
{
    protected $masterDetailProsesModel;
    protected $masterKegiatanWilayahModel;
    protected $masterKab;
    protected $validation;

    public function __construct()
    {
        $this->masterDetailProsesModel = new MasterKegiatanDetailProsesModel();
        $this->masterKegiatanWilayahModel = new MasterKegiatanWilayahModel();
        $this->validation = \Config\Services::validation();
        $this->masterKab = new MasterKabModel();
    }

    public function index()
    {
        $kegiatanWilayah = $this->masterKegiatanWilayahModel->getData();
        $data = [
            'title' => 'Kelola Master Kegiatan Wilayah',
            'active_menu' => 'master-kegiatan-wilayah',
            'kegiatanWilayah' => $kegiatanWilayah,
        ];
        return view('AdminSurveiProv/MasterKegiatanWilayah/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Kegiatan Wilayah',
            'active_menu' => 'master-kegiatan-wilayah',
            'kegiatanDetailProses' => $this->masterDetailProsesModel->findAll(),
            'validation' => $this->validation,
            'Kab' => $this->masterKab->findAll()
        ];
        return view('AdminSurveiProv/MasterKegiatanWilayah/create', $data);
    }

    // ============================================================
    // STORE
    // ============================================================
    public function store()
    {
        $rules = [
            'kegiatan_detail' => 'required|numeric',
            'kabupaten'       => 'required|numeric',
            'target'          => 'required|numeric',
            'keterangan'      => 'required|string|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idDetailProses = $this->request->getPost('kegiatan_detail');
        $idKabupaten = $this->request->getPost('kabupaten');
        $targetWilayah = (int)$this->request->getPost('target');
        $keterangan = $this->request->getPost('keterangan');

        $detailProses = $this->masterDetailProsesModel->find($idDetailProses);
        if (!$detailProses) {
            return redirect()->back()->with('error', 'Data kegiatan detail proses tidak ditemukan.');
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
            return redirect()->back()->withInput()->with(
                'error',
                'Total target wilayah melebihi target provinsi (' . $targetProv . ').'
            );
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

        return redirect()
            ->to(base_url('adminsurvei/master-kegiatan-wilayah'))
            ->with('success', '✅ Data kegiatan wilayah dan Kurva S berhasil dibuat.');
    }

    // ============================================================
    // EDIT
    // ============================================================
    public function edit($id)
    {
        $wilayah = $this->masterKegiatanWilayahModel->find($id);
        if (!$wilayah) {
            return redirect()->back()->with('error', 'Data kegiatan wilayah tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Kegiatan Wilayah',
            'active_menu' => 'master-kegiatan-wilayah',
            'kegiatanDetailProses' => $this->masterDetailProsesModel->findAll(),
            'Kab' => $this->masterKab->findAll(),
            'wilayah' => $wilayah,
            'validation' => $this->validation
        ];

        return view('AdminSurveiProv/MasterKegiatanWilayah/edit', $data);
    }

    // ============================================================
    // UPDATE
    // ============================================================
    public function update($id)
    {
        $rules = [
            'kegiatan_detail' => 'required|numeric',
            'kabupaten'       => 'required|numeric',
            'target'          => 'required|numeric',
            'keterangan'      => 'required|string|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $wilayah = $this->masterKegiatanWilayahModel->find($id);
        if (!$wilayah) {
            return redirect()->back()->with('error', 'Data kegiatan wilayah tidak ditemukan.');
        }

        $idDetailProses = $this->request->getPost('kegiatan_detail');
        $idKabupaten = $this->request->getPost('kabupaten');
        $targetWilayah = (int)$this->request->getPost('target');
        $keterangan = $this->request->getPost('keterangan');

        $detailProses = $this->masterDetailProsesModel->find($idDetailProses);
        $tanggalMulai = $detailProses['tanggal_mulai'];
        $tanggalSelesai = $detailProses['tanggal_selesai'];
        $tanggal100 = $detailProses['tanggal_selesai_target'] ?? $tanggalSelesai;
        $persenAwal = $detailProses['persentase_target_awal'] ?? 0;

        // Update data wilayah
        $this->masterKegiatanWilayahModel->update($id, [
            'id_kegiatan_detail_proses' => $idDetailProses,
            'id_kabupaten'              => $idKabupaten,
            'target_wilayah'            => $targetWilayah,
            'keterangan'                => $keterangan,
            'updated_at'                => date('Y-m-d H:i:s')
        ]);

        // Regenerate kurva S
        $this->generateKurvaSKab($id, $targetWilayah, $persenAwal, $tanggalMulai, $tanggal100, $tanggalSelesai);

        return redirect()
            ->to(base_url('adminsurvei/master-kegiatan-wilayah'))
            ->with('success', '✅ Data kegiatan wilayah dan Kurva S berhasil diperbarui.');
    }

    // ============================================================
    // DELETE
    // ============================================================
    public function delete($id)
    {
        $wilayah = $this->masterKegiatanWilayahModel->find($id);
        if (!$wilayah) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $kurvaModel = new KurvaSkabModel();
        $kurvaModel->where('id_kegiatan_wilayah', $id)->delete();
        $this->masterKegiatanWilayahModel->delete($id);

        return redirect()
            ->to(base_url('adminsurvei/master-kegiatan-wilayah'))
            ->with('success', '✅ Data kegiatan wilayah dan kurva S terkait berhasil dihapus.');
    }

    // ============================================================
    // GENERATE KURVA S KABUPATEN (sampai tanggal_selesai)
    // ============================================================
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
}
