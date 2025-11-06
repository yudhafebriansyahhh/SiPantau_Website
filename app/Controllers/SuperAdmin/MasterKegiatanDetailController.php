<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\MasterKegiatanDetailModel;
use App\Models\MasterKegiatanModel;
use App\Models\MasterOutputModel;
use App\Models\MasterKegiatanDetailAdminModel;

class MasterKegiatanDetailController extends BaseController
{
    protected $masterKegiatanDetailModel;
    protected $masterKegiatanModel;
    protected $masterOutputModel;
    protected $masterKegiatanDetailAdminModel;
    protected $validation;

    public function __construct()
    {
        $this->masterKegiatanDetailModel = new MasterKegiatanDetailModel();
        $this->masterKegiatanModel = new MasterKegiatanModel();
        $this->masterOutputModel = new MasterOutputModel();
        $this->masterKegiatanDetailAdminModel = new MasterKegiatanDetailAdminModel();
        $this->validation = \Config\Services::validation();
    }

    // ====================================================================
    // Index - Menampilkan halaman daftar master kegiatan detail
    // ====================================================================
    public function index()
    {
        $filterKegiatan = $this->request->getGet('kegiatan');

        // Get all master kegiatan untuk filter
        $masterKegiatans = $this->masterKegiatanModel->getWithOutput();

        // Get kegiatan detail dengan filter
        if ($filterKegiatan && $filterKegiatan != 'all') {
            $details = $this->masterKegiatanDetailModel->getByKegiatan($filterKegiatan);
            // Add master kegiatan info manually for filtered data
            foreach ($details as &$detail) {
                $kegiatan = $this->masterKegiatanModel->getWithOutputById($detail['id_kegiatan']);
                if ($kegiatan) {
                    $detail['nama_kegiatan'] = $kegiatan['nama_kegiatan'];
                    $detail['nama_output'] = $kegiatan['nama_output'];
                }
            }
        } else {
            $details = $this->masterKegiatanDetailModel->getWithKegiatan();
        }

        // Get admin untuk setiap kegiatan detail
        foreach ($details as &$detail) {
            $detail['admin_list'] = $this->masterKegiatanDetailAdminModel->getAdminByKegiatanDetail($detail['id_kegiatan_detail']);
        }

        $data = [
            'title'            => 'Kelola Master Kegiatan Detail',
            'active_menu'      => 'master-kegiatan-detail',
            'details'          => $details,
            'masterKegiatans'  => $masterKegiatans,
            'filterKegiatan'   => $filterKegiatan ?? 'all'
        ];

        return view('SuperAdmin/MasterKegiatanDetail/index', $data);
    }

    // ====================================================================
    // Create - Menampilkan form tambah master kegiatan detail
    // ====================================================================
    public function create()
    {
        $idKegiatan = $this->request->getGet('id_kegiatan');
        $masterKegiatans = $this->masterKegiatanModel->getWithOutput();

        $data = [
            'title'            => 'Tambah Master Kegiatan Detail',
            'active_menu'      => 'master-kegiatan-detail',
            'validation'       => $this->validation,
            'masterKegiatans'  => $masterKegiatans,
            'idKegiatan'       => $idKegiatan
        ];

        return view('SuperAdmin/MasterKegiatanDetail/create', $data);
    }

    // ====================================================================
    // Store - Menyimpan data master kegiatan detail baru
    // ====================================================================
    public function store()
    {
        $rules = [
            'id_kegiatan' => [
                'rules'  => 'required|numeric',
                'errors' => [
                    'required' => 'Master kegiatan harus dipilih',
                    'numeric'  => 'Master kegiatan tidak valid'
                ]
            ],
            'nama_kegiatan_detail' => [
                'rules'  => 'required|max_length[255]',
                'errors' => [
                    'required'   => 'Nama kegiatan detail harus diisi',
                    'max_length' => 'Nama kegiatan detail maksimal 255 karakter'
                ]
            ],
            'satuan' => [
                'rules'  => 'required|max_length[100]',
                'errors' => [
                    'required'   => 'Satuan harus diisi',
                    'max_length' => 'Satuan maksimal 100 karakter'
                ]
            ],
            'periode' => [
                'rules'  => 'required|max_length[50]',
                'errors' => [
                    'required'   => 'Periode harus diisi',
                    'max_length' => 'Periode maksimal 50 karakter'
                ]
            ],
            'tahun' => [
                'rules'  => 'required|numeric|exact_length[4]',
                'errors' => [
                    'required'     => 'Tahun harus diisi',
                    'numeric'      => 'Tahun harus berupa angka',
                    'exact_length' => 'Tahun harus 4 digit'
                ]
            ],
            'tanggal_mulai' => [
                'rules'  => 'permit_empty|valid_date',
                'errors' => [
                    'valid_date' => 'Format tanggal mulai tidak valid'
                ]
            ],
            'tanggal_selesai' => [
                'rules'  => 'permit_empty|valid_date',
                'errors' => [
                    'valid_date' => 'Format tanggal selesai tidak valid'
                ]
            ],
            'keterangan' => [
                'rules'  => 'permit_empty',
                'errors' => []
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'id_kegiatan'          => $this->request->getPost('id_kegiatan'),
            'nama_kegiatan_detail' => $this->request->getPost('nama_kegiatan_detail'),
            'satuan'               => $this->request->getPost('satuan'),
            'periode'              => $this->request->getPost('periode'),
            'tahun'                => $this->request->getPost('tahun'),
            'tanggal_mulai'        => $this->request->getPost('tanggal_mulai') ?: null,
            'tanggal_selesai'      => $this->request->getPost('tanggal_selesai') ?: null,
            'keterangan'           => $this->request->getPost('keterangan')
        ];

        if ($this->masterKegiatanDetailModel->insert($data)) {
            return redirect()
                ->to(base_url('superadmin/master-kegiatan-detail'))
                ->with('success', 'Data master kegiatan detail berhasil ditambahkan');
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data master kegiatan detail');
        }
    }

    // ====================================================================
    // Show - Menampilkan detail master kegiatan detail
    // ====================================================================
    public function show($id)
    {
        $detail = $this->masterKegiatanDetailModel->getWithKegiatanById($id);

        if (!$detail) {
            return redirect()
                ->to(base_url('superadmin/master-kegiatan-detail'))
                ->with('error', 'Data master kegiatan detail tidak ditemukan');
        }

        $detailProses = [];

        $data = [
            'title'        => 'Detail Kegiatan Detail',
            'active_menu'  => 'master-kegiatan-detail',
            'detail'       => $detail,
            'detailProses' => $detailProses
        ];

        return view('SuperAdmin/MasterKegiatanDetail/show', $data);
    }

    // ====================================================================
    // Edit - Menampilkan form edit master kegiatan detail
    // ====================================================================
    public function edit($id)
    {
        $detail = $this->masterKegiatanDetailModel->find($id);

        if (!$detail) {
            return redirect()
                ->to(base_url('superadmin/master-kegiatan-detail'))
                ->with('error', 'Data master kegiatan detail tidak ditemukan');
        }

        $masterKegiatans = $this->masterKegiatanModel->getWithOutput();

        $data = [
            'title'            => 'Edit Master Kegiatan Detail',
            'active_menu'      => 'master-kegiatan-detail',
            'detail'           => $detail,
            'validation'       => $this->validation,
            'masterKegiatans'  => $masterKegiatans
        ];

        return view('SuperAdmin/MasterKegiatanDetail/edit', $data);
    }

    // ====================================================================
    // Update - Memperbarui data master kegiatan detail
    // ====================================================================
    public function update($id)
    {
        $detail = $this->masterKegiatanDetailModel->find($id);

        if (!$detail) {
            return redirect()
                ->to(base_url('superadmin/master-kegiatan-detail'))
                ->with('error', 'Data master kegiatan detail tidak ditemukan');
        }

        $rules = [
            'id_kegiatan' => [
                'rules'  => 'required|numeric',
                'errors' => [
                    'required' => 'Master kegiatan harus dipilih',
                    'numeric'  => 'Master kegiatan tidak valid'
                ]
            ],
            'nama_kegiatan_detail' => [
                'rules'  => 'required|max_length[255]',
                'errors' => [
                    'required'   => 'Nama kegiatan detail harus diisi',
                    'max_length' => 'Nama kegiatan detail maksimal 255 karakter'
                ]
            ],
            'satuan' => [
                'rules'  => 'required|max_length[100]',
                'errors' => [
                    'required'   => 'Satuan harus diisi',
                    'max_length' => 'Satuan maksimal 100 karakter'
                ]
            ],
            'periode' => [
                'rules'  => 'required|max_length[50]',
                'errors' => [
                    'required'   => 'Periode harus diisi',
                    'max_length' => 'Periode maksimal 50 karakter'
                ]
            ],
            'tahun' => [
                'rules'  => 'required|numeric|exact_length[4]',
                'errors' => [
                    'required'     => 'Tahun harus diisi',
                    'numeric'      => 'Tahun harus berupa angka',
                    'exact_length' => 'Tahun harus 4 digit'
                ]
            ],
            'tanggal_mulai' => [
                'rules'  => 'permit_empty|valid_date',
                'errors' => [
                    'valid_date' => 'Format tanggal mulai tidak valid'
                ]
            ],
            'tanggal_selesai' => [
                'rules'  => 'permit_empty|valid_date',
                'errors' => [
                    'valid_date' => 'Format tanggal selesai tidak valid'
                ]
            ],
            'keterangan' => [
                'rules'  => 'permit_empty',
                'errors' => []
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'id_kegiatan'          => $this->request->getPost('id_kegiatan'),
            'nama_kegiatan_detail' => $this->request->getPost('nama_kegiatan_detail'),
            'satuan'               => $this->request->getPost('satuan'),
            'periode'              => $this->request->getPost('periode'),
            'tahun'                => $this->request->getPost('tahun'),
            'tanggal_mulai'        => $this->request->getPost('tanggal_mulai') ?: null,
            'tanggal_selesai'      => $this->request->getPost('tanggal_selesai') ?: null,
            'keterangan'           => $this->request->getPost('keterangan')
        ];

        if ($this->masterKegiatanDetailModel->update($id, $data)) {
            return redirect()
                ->to(base_url('superadmin/master-kegiatan-detail'))
                ->with('success', 'Data master kegiatan detail berhasil diperbarui');
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data master kegiatan detail');
        }
    }

    // ====================================================================
    // Delete - Menghapus data master kegiatan detail
    // ====================================================================
    public function delete($id)
    {
        $detail = $this->masterKegiatanDetailModel->find($id);

        if (!$detail) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data master kegiatan detail tidak ditemukan'
            ]);
        }

        if ($this->masterKegiatanDetailModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data master kegiatan detail berhasil dihapus'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data master kegiatan detail'
            ]);
        }
    }

    // ====================================================================
    // Get Admins - AJAX endpoint untuk mengambil daftar admin
    // ====================================================================
    public function getAdmins($id)
    {
        $admins = $this->masterKegiatanDetailAdminModel->getAdminByKegiatanDetail($id);

        return $this->response->setJSON([
            'success' => true,
            'admins' => $admins
        ]);
    }
}
