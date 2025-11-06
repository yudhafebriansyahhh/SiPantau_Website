<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\MasterOutputModel;

class MasterOutputController extends BaseController
{
    protected $masterOutputModel;
    protected $validation;

    public function __construct()
    {
        $this->masterOutputModel = new MasterOutputModel();
        $this->validation = \Config\Services::validation();
    }

    // ====================================================================
    // Index - Menampilkan halaman daftar master output
    // ====================================================================
    public function index()
    {
        $data = [
            'title'       => 'Kelola Master Output',
            'active_menu' => 'master-output',
            'outputs'     => $this->masterOutputModel->orderBy('id_output', 'DESC')->findAll()
        ];

        return view('SuperAdmin/MasterOutput/index', $data);
    }

    // ====================================================================
    // Create - Menampilkan form tambah master output
    // ====================================================================
    public function create()
    {
        $data = [
            'title'       => 'Tambah Master Output',
            'active_menu' => 'master-output',
            'validation'  => $this->validation
        ];

        return view('SuperAdmin/MasterOutput/create', $data);
    }

    // ====================================================================
    // Store - Menyimpan data master output baru
    // ====================================================================
    public function store()
    {
        $rules = [
            'nama_output' => [
                'rules'  => 'required|max_length[255]|is_unique[master_output.nama_output]',
                'errors' => [
                    'required'   => 'Nama output harus diisi',
                    'max_length' => 'Nama output maksimal 255 karakter',
                    'is_unique'  => 'Nama output sudah terdaftar'
                ]
            ],
            'fungsi' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Fungsi harus diisi'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_output' => $this->request->getPost('nama_output'),
            'fungsi'      => $this->request->getPost('fungsi'),
        ];

        $this->masterOutputModel->insert($data);

        return redirect()
            ->to(base_url('superadmin/master-output'))
            ->with('success', 'Data master output berhasil ditambahkan');
    }

    // ====================================================================
    // Edit - Menampilkan form edit master output
    // ====================================================================
    public function edit($id)
    {
        $output = $this->masterOutputModel->find($id);

        if (!$output) {
            return redirect()
                ->to(base_url('superadmin/master-output'))
                ->with('error', 'Data master output tidak ditemukan');
        }

        $data = [
            'title'       => 'Edit Master Output',
            'active_menu' => 'master-output',
            'output'      => $output,
            'validation'  => $this->validation
        ];

        return view('SuperAdmin/MasterOutput/edit', $data);
    }

    // ====================================================================
    // Update - Memperbarui data master output
    // ====================================================================
    public function update($id)
    {
        $output = $this->masterOutputModel->find($id);

        if (!$output) {
            return redirect()
                ->to(base_url('superadmin/master-output'))
                ->with('error', 'Data master output tidak ditemukan');
        }

        $rules = [
            'nama_output' => [
                'rules'  => "required|max_length[255]|is_unique[master_output.nama_output,id_output,{$id}]",
                'errors' => [
                    'required'   => 'Nama output harus diisi',
                    'max_length' => 'Nama output maksimal 255 karakter',
                    'is_unique'  => 'Nama output sudah terdaftar'
                ]
            ],
            'fungsi' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Fungsi harus diisi'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_output' => $this->request->getPost('nama_output'),
            'fungsi'      => $this->request->getPost('fungsi'),
        ];

        $this->masterOutputModel->update($id, $data);

        return redirect()
            ->to(base_url('superadmin/master-output'))
            ->with('success', 'Data master output berhasil diperbarui');
    }

    // ====================================================================
    // Delete - Menghapus data master output
    // ====================================================================
    public function delete($id)
    {
        $output = $this->masterOutputModel->find($id);

        if (!$output) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data master output tidak ditemukan'
            ]);
        }

        $this->masterOutputModel->delete($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data master output berhasil dihapus'
        ]);
    }
}