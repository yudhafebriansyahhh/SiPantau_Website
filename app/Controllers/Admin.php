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
        return view('SuperAdmin/Master Output/index', $data);
    }

    public function tambah_master_output()
    {
        $data = [
            'title' => 'Master Output',
            'active_menu' => 'master-output'
        ];
        return view('SuperAdmin/Master Output/create', $data);
    }

    public function edit_master_output()
    {
        $data = [
            'title' => 'Master Output',
            'active_menu' => 'master-output'
        ];
        return view('SuperAdmin/Master Output/edit', $data);
    }

    public function master_kegiatan()
    {
        $data = [
            'title' => 'Master Kegiatan',
            'active_menu' => 'master-kegiatan'
        ];
        return view('SuperAdmin/Master Kegiatan/index', $data);
    }

    public function tambah_master_kegiatan()
    {
         $data = [
            'title' => 'Master Kegiatan',
            'active_menu' => 'master-kegiatan'
        ];
        return view('SuperAdmin/Master Kegiatan/create', $data);
    }

    public function edit_master_kegiatan()
    {
         $data = [
            'title' => 'Master Kegiatan',
            'active_menu' => 'master-kegiatan'
        ];
        return view('SuperAdmin/Master Kegiatan/edit', $data);
    }

    public function master_kegiatan_detail()
    {
        $data = [
            'title' => 'Master Kegiatan Detail',
            'active_menu' => 'master-kegiatan-detail'
        ];
        return view('SuperAdmin/Master Kegiatan Detail/index', $data);
    }
}