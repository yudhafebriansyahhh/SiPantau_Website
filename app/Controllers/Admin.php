<?php
namespace App\Controllers;

class Admin extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard',
            'active_menu' => 'dashboard'
        ];
        
        return view('SuperAdmin/dashboard', $data);
    }

    public function master_output()
    {
        $data = [
            'title' => 'Master Output',
            'active_menu' => 'master-output'
        ];
        return view('SuperAdmin/MasterOutput/index', $data);
    }

    public function tambah_master_output()
    {
        $data = [
            'title' => 'Master Output',
            'active_menu' => 'master-output'
        ];
        return view('SuperAdmin/MasterOutput/create', $data);
    }

    public function edit_master_output()
    {
        $data = [
            'title' => 'Master Output',
            'active_menu' => 'master-output'
        ];
        return view('SuperAdmin/MasterOutput/edit', $data);
    }

    public function master_kegiatan()
    {
        $data = [
            'title' => 'Master Kegiatan',
            'active_menu' => 'master-kegiatan'
        ];
        return view('SuperAdmin/MasterKegiatan/index', $data);
    }

    public function tambah_master_kegiatan()
    {
         $data = [
            'title' => 'Master Kegiatan',
            'active_menu' => 'master-kegiatan'
        ];
        return view('SuperAdmin/MasterKegiatan/create', $data);
    }

    public function edit_master_kegiatan()
    {
         $data = [
            'title' => 'Master Kegiatan',
            'active_menu' => 'master-kegiatan'
        ];
        return view('SuperAdmin/MasterKegiatan/edit', $data);
    }

    public function detail_master_kegiatan()
    {
        $data = [
            'title' => 'Master Kegiatan Detail',
            'active_menu' => 'master-kegiatan'
        ];
        return view('SuperAdmin/MasterKegiatan/show', $data);
    }

    public function master_kegiatan_detail()
    {
        $data = [
            'title' => 'Master Kegiatan Detail',
            'active_menu' => 'master-kegiatan-detail'
        ];
        return view('SuperAdmin/MasterKegiatanDetail/index', $data);
    }
    public function tambah_master_kegiatan_detail()
    {
        $data = [
            'title' => 'Master Kegiatan Detail',
            'active_menu' => 'master-kegiatan-detail'
        ];
        return view('SuperAdmin/MasterKegiatanDetail/create', $data);
    }
    public function edit_master_kegiatan_detail()
    {
        $data = [
            'title' => 'Master Kegiatan Detail',
            'active_menu' => 'master-kegiatan-detail'
        ];
        return view('SuperAdmin/MasterKegiatanDetail/edit', $data);
    }

    public function detail_master_kegiatan_detail()
    {
        $data = [
            'title' => 'Master Kegiatan Detail',
            'active_menu' => 'master-kegiatan-detail'
        ];
        return view('SuperAdmin/MasterKegiatanDetail/show', $data);
    }

    public function kelola_pengguna()
    {
        $data = [
            'title' => 'Kelola Pengguna',
            'active_menu' => 'kelola-pengguna'
        ];
        return view('SuperAdmin/KelolaPengguna/index', $data);
    }
    public function tambah_kelola_pengguna()
    {
        $data = [
            'title' => 'Kelola Pengguna',
            'active_menu' => 'kelola-pengguna'
        ];
        return view('SuperAdmin/KelolaPengguna/create', $data);
    }
    public function edit_kelola_pengguna()
    {
        $data = [
            'title' => 'Kelola Pengguna',
            'active_menu' => 'kelola-pengguna'
        ];
        return view('SuperAdmin/KelolaPengguna/edit', $data);
    }

    public function kelola_admin_prov()
    {
        $data = [
            'title' => 'Kelola Admin Survey',
            'active_menu' => 'kelola-admin-surveyprov'
        ];
        return view('SuperAdmin/KelolaAdminSurvey/index', $data);
    }
}