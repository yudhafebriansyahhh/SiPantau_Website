<?php

namespace App\Controllers\AdminProv;

use App\Controllers\BaseController;
use App\Models\MasterKegiatanDetailProsesModel;
use App\Models\MasterKegiatanDetailModel;
use App\Models\KurvaSProvinsiModel;
use DateInterval;
use DatePeriod;
use DateTime;

class MasterKegiatanDetailProsesController extends BaseController
{
    protected $masterDetailProsesModel;
    protected $masterDetailModel;
    protected $validation;

    public function __construct()
    {
        $this->masterDetailProsesModel = new MasterKegiatanDetailProsesModel();
        $this->masterDetailModel = new MasterKegiatanDetailModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $kegiatanDetails = $this->masterDetailProsesModel->getData();

        $data = [
            'title'           => 'Kelola Master Kegiatan Detail Proses',
            'active_menu'     => 'master-kegiatan-detail-proses',
            'kegiatanDetails' => $kegiatanDetails,
            
            
        ];

        return view('AdminSurveiProv/MasterKegiatanDetailProses/index', $data);
    }

    public function create()
    {
        $data = [
            'title'             => 'Tambah Master Kegiatan Detail Proses',
            'active_menu'       => 'master-kegiatan-detail-proses',
            'kegiatanDetailList'=> $this->masterDetailModel->findAll(),
            'validation'        => $this->validation
        ];

        return view('AdminSurveiProv/MasterKegiatanDetailProses/create', $data);
    }

    // ============================================================
    // STORE
    // ============================================================
    public function store()
    {
        $rules = [
            'kegiatan_detail'        => 'required|numeric',
            'nama_proses'            => 'required|min_length[3]|max_length[255]',
            'tanggal_mulai'          => 'required|valid_date',
            'tanggal_selesai'        => 'required|valid_date',
            'satuan'                 => 'required|max_length[50]',
            'keterangan'             => 'required|max_length[255]',
            'periode'                => 'required|max_length[50]',
            'target'                 => 'required|numeric',
            'target_hari_pertama'    => 'required|numeric',
            'target_tanggal_selesai' => 'required|valid_date',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tanggalMulai   = $this->request->getPost('tanggal_mulai');
        $tanggalSelesai = $this->request->getPost('tanggal_selesai');
        $tanggal100Persen = $this->request->getPost('target_tanggal_selesai');

        // ✅ Validasi hubungan antar tanggal
        if (strtotime($tanggalSelesai) < strtotime($tanggalMulai)) {
            return redirect()->back()->withInput()->with('error', '❌ Tanggal selesai tidak boleh lebih awal dari tanggal mulai.');
        }

        if (strtotime($tanggal100Persen) < strtotime($tanggalMulai)) {
            return redirect()->back()->withInput()->with('error', '❌ Target tanggal selesai (100%) tidak boleh lebih awal dari tanggal mulai.');
        }

        if (strtotime($tanggal100Persen) > strtotime($tanggalSelesai)) {
            return redirect()->back()->withInput()->with('error', '❌ Target tanggal selesai (100%) tidak boleh melebihi tanggal selesai kegiatan.');
        }

        // Simpan ke tabel master_kegiatan_detail_proses
        $this->masterDetailProsesModel->insert([
            'id_kegiatan_detail'          => $this->request->getPost('kegiatan_detail'),
            'nama_kegiatan_detail_proses' => $this->request->getPost('nama_proses'),
            'satuan'                      => $this->request->getPost('satuan'),
            'tanggal_mulai'               => $tanggalMulai,
            'tanggal_selesai'             => $tanggalSelesai,
            'ket'                         => $this->request->getPost('keterangan'),
            'periode'                     => $this->request->getPost('periode'),
            'target'                      => $this->request->getPost('target'),
            'persentase_hari_pertama'     => $this->request->getPost('target_hari_pertama'),
            'target_100_persen'           => $tanggal100Persen,
            'created_at'                  => date('Y-m-d H:i:s'),
        ]);

        $idProses = $this->masterDetailProsesModel->getInsertID();

        // Generate Kurva S
        $this->generateKurvaS(
            $idProses,
            $this->request->getPost('target'),
            $this->request->getPost('target_hari_pertama'),
            $tanggalMulai,
            $tanggal100Persen,
            $tanggalSelesai
        );

        return redirect()
            ->to(base_url('adminsurvei/master-kegiatan-detail-proses'))
            ->with('success', '✅ Data kegiatan detail proses dan Kurva S Provinsi berhasil dibuat.');
    }

    // ============================================================
    // EDIT
    // ============================================================
    public function edit($id)
    {
        $detailProses = $this->masterDetailProsesModel->find($id);

        if (! $detailProses) {
            return redirect()
                ->to(base_url('adminsurvei/master-kegiatan-detail-proses'))
                ->with('error', 'Data tidak ditemukan.');
        }

        $data = [
            'title'              => 'Edit Master Kegiatan Detail Proses',
            'active_menu'        => 'master-kegiatan-detail-proses',
            'kegiatanDetailList' => $this->masterDetailModel->findAll(),
            'detailProses'       => $detailProses,
            'validation'         => $this->validation
        ];

        return view('AdminSurveiProv/MasterKegiatanDetailProses/edit', $data);
    }

    // ============================================================
    // UPDATE
    // ============================================================
    public function update($id)
    {
        $rules = [
            'kegiatan_detail'        => 'required|numeric',
            'nama_proses'            => 'required|min_length[3]|max_length[255]',
            'tanggal_mulai'          => 'required|valid_date',
            'tanggal_selesai'        => 'required|valid_date',
            'satuan'                 => 'required|max_length[50]',
            'keterangan'             => 'required|max_length[255]',
            'periode'                => 'required|max_length[50]',
            'target'                 => 'required|numeric',
            'target_hari_pertama'    => 'required|numeric',
            'target_tanggal_selesai' => 'required|valid_date',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $existingData = $this->masterDetailProsesModel->find($id);
        if (! $existingData) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $tanggalMulai   = $this->request->getPost('tanggal_mulai');
        $tanggalSelesai = $this->request->getPost('tanggal_selesai');
        $tanggal100Persen = $this->request->getPost('target_tanggal_selesai');

        // Validasi hubungan antar tanggal
        if (strtotime($tanggalSelesai) < strtotime($tanggalMulai) ||
            strtotime($tanggal100Persen) < strtotime($tanggalMulai) ||
            strtotime($tanggal100Persen) > strtotime($tanggalSelesai)) {
            return redirect()->back()->withInput()->with('error', '❌ Tanggal tidak valid.');
        }

        $input = [
            'id_kegiatan_detail'          => $this->request->getPost('kegiatan_detail'),
            'nama_kegiatan_detail_proses' => $this->request->getPost('nama_proses'),
            'tanggal_mulai'               => $tanggalMulai,
            'tanggal_selesai'             => $tanggalSelesai,
            'satuan'                      => $this->request->getPost('satuan'),
            'ket'                         => $this->request->getPost('keterangan'),
            'periode'                     => $this->request->getPost('periode'),
            'target'                      => $this->request->getPost('target'),
            'persentase_hari_pertama'     => $this->request->getPost('target_hari_pertama'),
            'target_100_persen'           => $tanggal100Persen,
            'updated_at'                  => date('Y-m-d H:i:s'),
        ];

        $this->masterDetailProsesModel->update($id, $input);

        $isKurvaNeedsUpdate = (
            $existingData['target'] != $input['target'] ||
            $existingData['persentase_hari_pertama'] != $input['persentase_hari_pertama'] ||
            $existingData['target_100_persen'] != $input['target_100_persen']
        );

        if ($isKurvaNeedsUpdate) {
            $kurvaModel = new KurvaSProvinsiModel();
            $kurvaModel->where('id_kegiatan_detail_proses', $id)->delete();

            $this->generateKurvaS(
                $id,
                $input['target'],
                $input['persentase_hari_pertama'],
                $tanggalMulai,
                $tanggal100Persen,
                $tanggalSelesai
            );
        }

        return redirect()
            ->to(base_url('adminsurvei/master-kegiatan-detail-proses'))
            ->with('success', $isKurvaNeedsUpdate
                ? '✅ Data kegiatan dan Kurva S Provinsi berhasil diperbarui.'
                : 'Data kegiatan berhasil diperbarui tanpa perubahan Kurva S.');
    }

    // ============================================================
    // GENERATE KURVA S (dengan skip hari libur)
    // ============================================================
 private function generateKurvaS($idProses, $target, $persenAwal, $tanggalMulai, $tanggal100, $tanggalSelesai)
{
    $kurvaModel = new KurvaSProvinsiModel();

    $totalTarget = (int) $target;
    $persenAwal  = (float) $persenAwal;

    $start   = new DateTime($tanggalMulai);
    $tgl100  = new DateTime($tanggal100);
    $end     = new DateTime($tanggalSelesai);
    $end->modify('+1 day');

    $interval = new DateInterval('P1D');
    $periodTotal = iterator_to_array(new DatePeriod($start, $interval, $end));

    // Ambil hanya hari kerja (Senin–Jumat)
    $workdays = array_filter($periodTotal, fn($d) => $d->format('N') <= 5);
    $workdays = array_values($workdays);

    // Filter hanya sampai tanggal 100%
    $workdaysUntil100 = array_filter($workdays, fn($d) => $d <= $tgl100);
    $workdaysUntil100 = array_values($workdaysUntil100);

    $daysSigmoid = max(count($workdaysUntil100), 2);
    $k = 8;   // kelengkungan
    $x0 = 0.5; // titik tengah

    $workdayData = [];
    foreach ($workdaysUntil100 as $i => $date) {
        $progress = $i / ($daysSigmoid - 1);
        $sigmoid = 1 / (1 + exp(-$k * ($progress - $x0)));
        $kumulatifPersen = $persenAwal + (100 - $persenAwal) * $sigmoid;
        if ($kumulatifPersen > 100) $kumulatifPersen = 100;
        $workdayData[$date->format('Y-m-d')] = $kumulatifPersen;
    }

    $kumulatifAbsolut = 0;
    $insertData = [];

    // Tahap 1: hitung dulu semua agar bisa dikoreksi sebelum insert
    foreach ($workdaysUntil100 as $date) {
        $currentDate = $date->format('Y-m-d');
        $kumulatifPersen = $workdayData[$currentDate];
        $harianAbsolut = round(($totalTarget * ($kumulatifPersen / 100)) - $kumulatifAbsolut);
        $kumulatifAbsolut += $harianAbsolut;

        $insertData[] = [
            'tanggal' => $currentDate,
            'persen' => round($kumulatifPersen, 2),
            'harian' => $harianAbsolut,
            'kumulatif' => $kumulatifAbsolut
        ];
    }

    // 🟢 Koreksi hari terakhir agar total tepat = totalTarget
    $selisih = $totalTarget - $kumulatifAbsolut;
    if (!empty($insertData) && $selisih !== 0) {
        $insertData[count($insertData) - 1]['harian'] += $selisih;
        $insertData[count($insertData) - 1]['kumulatif'] += $selisih;
        $kumulatifAbsolut = $totalTarget;
    }

    // Simpan ke DB
    foreach ($insertData as $row) {
        $kurvaModel->insert([
            'id_kegiatan_detail_proses' => $idProses,
            'tanggal_target'            => $row['tanggal'],
            'target_persen_kumulatif'   => $row['persen'],
            'target_harian_absolut'     => $row['harian'],
            'target_kumulatif_absolut'  => $row['kumulatif'],
            'is_hari_kerja'             => 1,
            'created_at'                => date('Y-m-d H:i:s'),
        ]);
    }

    // Tahap 2: setelah tanggal100 → mendatar
    $workdaysAfter100 = array_filter($workdays, fn($d) => $d > $tgl100);
    $workdaysAfter100 = array_values($workdaysAfter100);

    foreach ($workdaysAfter100 as $date) {
        $currentDate = $date->format('Y-m-d');
        $kurvaModel->insert([
            'id_kegiatan_detail_proses' => $idProses,
            'tanggal_target'            => $currentDate,
            'target_persen_kumulatif'   => 100,
            'target_harian_absolut'     => 0,
            'target_kumulatif_absolut'  => $totalTarget, // 🟢 pastikan mendatar di total target
            'is_hari_kerja'             => 1,
            'created_at'                => date('Y-m-d H:i:s'),
        ]);
    }

    log_message('info', "Kurva S dibuat untuk id_proses=$idProses (total=$totalTarget, 100% di $tanggal100, mendatar sampai $tanggalSelesai)");
}


    // ============================================================
    // DELETE
    // ============================================================
    public function delete($id)
    {
        $prosesModel = new MasterKegiatanDetailProsesModel();
        $kurvaModel  = new KurvaSProvinsiModel();

        $detailProses = $prosesModel->find($id);

        if (! $detailProses) {
            return redirect()->back()->with('error', 'Data kegiatan detail proses tidak ditemukan.');
        }

        $kurvaModel->where('id_kegiatan_detail_proses', $id)->delete();
        $prosesModel->delete($id);

        return redirect()
            ->to(base_url('adminsurvei/master-kegiatan-detail-proses'))
            ->with('success', '✅ Data kegiatan detail proses dan kurva S provinsi terkait berhasil dihapus.');
    }
}
