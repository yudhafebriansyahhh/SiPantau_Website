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
        
        return view('admin/dashboard', $data);
    }
}