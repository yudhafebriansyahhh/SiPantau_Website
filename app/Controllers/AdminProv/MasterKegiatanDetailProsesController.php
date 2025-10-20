<?php

namespace App\Controllers\AdminProv;

use App\Controllers\BaseController;
use App\Models\MasterKegiatanDetailProsesModel;
use App\Models\MasterKegiatanDetailModel;

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

        $messages = [
            'kegiatan_detail' => [
                'required' => 'Kegiatan detail wajib dipilih.',
                'numeric'  => 'Kegiatan detail tidak valid.'
            ],
            'nama_proses' => [
                'required'   => 'Nama proses wajib diisi.',
                'min_length' => 'Nama proses minimal 3 karakter.',
                'max_length' => 'Nama proses maksimal 255 karakter.'
            ],
            'tanggal_mulai' => [
                'required'   => 'Tanggal mulai wajib diisi.',
                'valid_date' => 'Format tanggal mulai tidak valid.'
            ],
            'tanggal_selesai' => [
                'required'   => 'Tanggal selesai wajib diisi.',
                'valid_date' => 'Format tanggal selesai tidak valid.'
            ],
            'satuan' => [
                'required' => 'Satuan wajib diisi.'
            ],
            'keterangan' => [
                'required' => 'Keterangan wajib diisi.'
            ],
            'periode' => [
                'required' => 'Periode wajib diisi.'
            ],
            'target' => [
                'required' => 'Target wajib diisi.',
                'numeric'  => 'Target harus berupa angka.'
            ],
            'target_hari_pertama' => [
                'required' => 'Target hari pertama wajib diisi.',
                'numeric'  => 'Target hari pertama harus berupa angka.'
            ],
            'target_tanggal_selesai' => [
                'required'   => 'Tanggal target selesai wajib diisi.',
                'valid_date' => 'Tanggal target selesai tidak valid.'
            ],
        ];

        // --- VALIDATE INPUT ---
        if (! $this->validate($rules, $messages)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // --- VALID DATE CHECK ---
        $tanggalMulai = $this->request->getPost('tanggal_mulai');
        $tanggalSelesai = $this->request->getPost('tanggal_selesai');

        if (strtotime($tanggalSelesai) < strtotime($tanggalMulai)) {
            return redirect()->back()->withInput()->with('error', 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.');
        }

        // --- SAVE DATA ---
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

        return redirect()
            ->to(base_url('adminsurvei/master-kegiatan-detail-proses'))
            ->with('success', 'Data kegiatan detail proses berhasil disimpan.');
    }

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
        return redirect()
            ->back()
            ->withInput()
            ->with('errors', $this->validator->getErrors());
    }

    $tanggalMulai = $this->request->getPost('tanggal_mulai');
    $tanggalSelesai = $this->request->getPost('tanggal_selesai');

    if (strtotime($tanggalSelesai) < strtotime($tanggalMulai)) {
        return redirect()->back()->withInput()->with('error', 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.');
    }

    $this->masterDetailProsesModel->update($id, [
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
        'updated_at'              => date('Y-m-d H:i:s'),
    ]);

    return redirect()
        ->to(base_url('adminsurvei/master-kegiatan-detail-proses'))
        ->with('success', 'Data kegiatan detail proses berhasil diperbarui.');
}


    /**
     * Hapus data kegiatan detail proses
     */
    public function delete($id)
{
    $model = new MasterKegiatanDetailProsesModel();

    if ($model->find($id)) {
        $model->delete($id);
        return redirect()
            ->to(base_url('adminsurvei/master-kegiatan-detail-proses'))
            ->with('success', 'Data berhasil dihapus.');
    }

    return redirect()
        ->back()
        ->with('error', 'Data tidak ditemukan.');
}


}
