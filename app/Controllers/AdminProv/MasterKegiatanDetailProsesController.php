<?php

namespace App\Controllers\AdminProv;

use App\Controllers\BaseController;
use App\Models\MasterKegiatanDetailProsesModel;
use App\Models\MasterKegiatanDetailModel;
USE App\Models\KurvaSProvinsiModel;
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
        $this->masterDetailProsesModel = new MasterKegiatanDetailProsesModel;
        $this->masterDetailModel = new MasterKegiatanDetailModel;
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
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $tanggalMulai   = $this->request->getPost('tanggal_mulai');
        $tanggalSelesai = $this->request->getPost('tanggal_selesai');

        if (strtotime($tanggalSelesai) < strtotime($tanggalMulai)) {
            return redirect()->back()->withInput()->with('error', 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.');
        }

        // --- Simpan Data ke master_kegiatan_detail_proses ---
        $this->masterDetailProsesModel->insert([
            'id_kegiatan_detail'      => $this->request->getPost('kegiatan_detail'),
            'nama_kegiatan_detail_proses' => $this->request->getPost('nama_proses'),
            'satuan'                  => $this->request->getPost('satuan'),
            'tanggal_mulai'           => $tanggalMulai,
            'tanggal_selesai'         => $tanggalSelesai,
            'ket'                     => $this->request->getPost('keterangan'),
            'periode'                 => $this->request->getPost('periode'),
            'target'                  => $this->request->getPost('target'),
            'persentase_hari_pertama' => $this->request->getPost('target_hari_pertama'),
            'target_100_persen'       => $this->request->getPost('target_tanggal_selesai'),
            'created_at'              => date('Y-m-d H:i:s'),
        ]);

        $idProses = $this->masterDetailProsesModel->getInsertID();

        // ===============================
        // Generate Kurva S Provinsi Otomatis
        // ===============================
        // ===============================
// Generate Kurva S Provinsi Otomatis
// ===============================
// ===============================
// Generate Kurva S Provinsi Otomatis (dengan fungsi sigmoid)
// ===============================
$kurvaModel = new KurvaSProvinsiModel();

$totalTarget = (int) $this->request->getPost('target');
$persenAwal  = (float) $this->request->getPost('target_hari_pertama');
$tanggal100  = $this->request->getPost('target_tanggal_selesai');

$start = new DateTime($tanggalMulai);
$end   = new DateTime($tanggalSelesai);
$end->modify('+1 day'); // supaya tanggal selesai ikut dihitung

$interval = new DateInterval('P1D');
$period   = iterator_to_array(new DatePeriod($start, $interval, $end));

$days = count($period);
if ($days <= 1) {
    log_message('error', "Kurva S gagal dibuat karena range tanggal tidak valid: $tanggalMulai - $tanggalSelesai");
    return redirect()->back()->with('error', 'Gagal membuat kurva S: range tanggal tidak valid.');
}

// Parameter sigmoid
$k  = 8;    // tingkat kelengkungan kurva
$x0 = 0.5;  // titik tengah (50% waktu)

$kumulatifAbsolut = 0;

foreach ($period as $i => $date) {
    // Rasio posisi hari (0 - 1)
    $progress = $i / ($days - 1);

    // Hitung nilai sigmoid (hasil 0-1)
    $sigmoid = 1 / (1 + exp(-$k * ($progress - $x0)));

    // Skala dari persenAwal ke 100
    $kumulatifPersen = $persenAwal + (100 - $persenAwal) * $sigmoid;

    if ($kumulatifPersen > 100) $kumulatifPersen = 100;

    // Hitung target absolut
    $harianAbsolut = round(($totalTarget * ($kumulatifPersen / 100)) - $kumulatifAbsolut);
    $kumulatifAbsolut += $harianAbsolut;

    $insertData = [
        'id_kegiatan_detail_proses' => $idProses,
        'tanggal_target'            => $date->format('Y-m-d'),
        'target_persen_kumulatif'   => round($kumulatifPersen, 2),
        'target_harian_absolut'     => $harianAbsolut,
        'target_kumulatif_absolut'  => $kumulatifAbsolut,
        'is_hari_kerja'             => ($date->format('N') <= 5),
        'created_at'                => date('Y-m-d H:i:s'),
    ];

    if (! $kurvaModel->insert($insertData)) {
        log_message('error', 'Gagal insert kurva_s_provinsi: ' . json_encode($kurvaModel->errors()) . ' | Data: ' . json_encode($insertData));
    }
}

log_message('info', "Kurva S (sigmoid) berhasil dibuat untuk id_proses=$idProses, total_hari=$days");

return redirect()
    ->to(base_url('adminsurvei/master-kegiatan-detail-proses'))
    ->with('success', 'Data kegiatan detail proses dan Kurva S Provinsi berhasil dibuat dengan bentuk S.');

}


    /**
     * Hapus data kegiatan detail proses
     */
  public function delete($id)
{
    $prosesModel = new MasterKegiatanDetailProsesModel();
    $kurvaModel  = new KurvaSProvinsiModel();

    // Pastikan data kegiatan detail proses ada
    $detailProses = $prosesModel->find($id);

    if (! $detailProses) {
        return redirect()
            ->back()
            ->with('error', 'Data kegiatan detail proses tidak ditemukan.');
    }

    // Hapus semua kurva S provinsi yang terkait
    $deletedKurva = $kurvaModel->where('id_kegiatan_detail_proses', $id)->delete();

    if ($deletedKurva === false) {
        log_message('error', 'Gagal menghapus kurva S provinsi terkait id_kegiatan_detail_proses=' . $id);
    } else {
        log_message('info', 'Berhasil menghapus ' . $deletedKurva . ' baris dari kurva_s_provinsi untuk id_kegiatan_detail_proses=' . $id);
    }

    // Hapus data master_kegiatan_detail_proses
    $prosesModel->delete($id);

    return redirect()
        ->to(base_url('adminsurvei/master-kegiatan-detail-proses'))
        ->with('success', 'Data kegiatan detail proses dan kurva S provinsi terkait berhasil dihapus.');
}



}
