<?php
namespace App\Controllers;

class AdminSurveiProvController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard',
            'active_menu' => 'dashboard'
        ];
        
        return view('AdminSurveiProv/dashboard', $data);
    }

    public function master_detail_proses()
    {
        $data = [
            'title' => 'Master Detail Proses',
            'active_menu' => 'master-kegiatan-detail-proses'
        ];
        return view('AdminSurveiProv/MasterKegiatanDetailProses/index', $data);
    }

    public function tambah_detail_proses()
    {
        $data = [
            'title' => 'Master Detail Proses',
            'active_menu' => 'master-kegiatan-detail-proses'
        ];
        return view('AdminSurveiProv/MasterKegiatanDetailProses/create', $data);
    }

    public function edit_master_output()
    {
        $data = [
            'title' => 'Master Output',
            'active_menu' => 'master-output'
        ];
        return view('AdminSurveiProv/Master Output/edit', $data);
    }

    public function master_kegiatan_wilayah()
    {
        $data = [
            'title' => 'Master Kegiatan Wilayah',
            'active_menu' => 'master-kegiatan-wilayah'
        ];
        return view('AdminSurveiProv/MasterKegiatanWilayah/index', $data);
    }

    public function tambah_master_kegiatan_wilayah()
    {
         $data = [
            'title' => 'Master Kegiatan Wilayah',
            'active_menu' => 'master-kegiatan-wilayah'
        ];
        return view('AdminSurveiProv/MasterKegiatanWilayah/create', $data);
    }

    public function edit_master_kegiatan()
    {
         $data = [
            'title' => 'Master Kegiatan',
            'active_menu' => 'master-kegiatan'
        ];
        return view('SuperAdmin/Master Kegiatan/edit', $data);
    }

    public function AssignAdminSurveiKab()
    {
        $data = [
            'title' => 'Assign Petugas Survei',
            'active_menu' => 'assign-admin-kab'
        ];
        return view('AdminSurveiProv/AssignAdminSurveiKab/index', $data);
    }

    public function tambah_AssignAdminSurveiKab()
    {
        $data = [
            'title' => 'Assign Petugas Survei',
            'active_menu' => 'assign-admin-kab/create'
        ];
        return view('AdminSurveiProv/AssignAdminSurveiKab/create', $data);
    }
}