<?php

namespace App\Controllers\AdminKab;
use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        return view('AdminSurveiKab/dashboard');
    }

    public function approve_laporan()
    {
        $data = [
            'title' => 'Approval Laporan',
            'active_menu' => 'comingsoon'
        ];

        return view('AdminSurveiKab/ApprovalLaporan/index', $data);
    }
}