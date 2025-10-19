<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ================== AUTH ==================
$routes->get('/', 'Auth\LoginController::index');
$routes->get('login', 'Auth\LoginController::index');
$routes->post('auth/login', 'Auth\LoginController::process');
$routes->get('logout', 'Auth\LoginController::logout');

// ================== HALAMAN TIDAK BERIZIN ==================
$routes->get('unauthorized', 'ErrorController::unauthorized');

// ================== SUPER ADMIN (id_role = 1) ==================
$routes->group('admin', ['filter' => 'role:1'], static function ($routes) {
    $routes->get('/', 'Admin::index');

    // Master Output
    $routes->get('master-output', 'Admin::master_output');
    $routes->get('master-output/create', 'Admin::tambah_master_output');
    $routes->get('master-output/edit', 'Admin::edit_master_output');

    // Master Kegiatan
    $routes->get('master-kegiatan', 'Admin::master_kegiatan');
    $routes->get('master-kegiatan/create', 'Admin::tambah_master_kegiatan');
    $routes->get('master-kegiatan/edit', 'Admin::edit_master_kegiatan');
    $routes->get('master-kegiatan/detail', 'Admin::detail_master_kegiatan');

    // Master Kegiatan Detail
    $routes->get('master-kegiatan-detail', 'Admin::master_kegiatan_detail');
    $routes->get('master-kegiatan-detail/create', 'Admin::tambah_master_kegiatan_detail');
    $routes->get('master-kegiatan-detail/edit', 'Admin::edit_master_kegiatan_detail');
    $routes->get('master-kegiatan-detail/detail', 'Admin::detail_master_kegiatan_detail');

    // Kelola Pengguna
    $routes->get('kelola-pengguna', 'Admin::kelola_pengguna');
    $routes->get('kelola-pengguna/create', 'Admin::tambah_kelola_pengguna');
    $routes->get('kelola-pengguna/edit', 'Admin::edit_kelola_pengguna');
    $routes->get('kelola-admin-surveyprov', 'Admin::kelola_admin_prov');
});

// ================== ADMIN SURVEI PROVINSI (id_role = 2) ==================
$routes->group('adminsurvei', ['filter' => 'role:2'], static function ($routes) {
    $routes->get('/', 'AdminSurveiProvController::index');
    $routes->get('master-kegiatan-detail-proses', 'AdminSurveiProvController::master_detail_proses');
    $routes->get('master-kegiatan-detail-proses/create', 'AdminSurveiProvController::tambah_detail_proses');
    $routes->get('master-kegiatan-wilayah', 'AdminSurveiProvController::master_kegiatan_wilayah');
    $routes->get('master-kegiatan-wilayah/create', 'AdminSurveiProvController::tambah_master_kegiatan_wilayah');
    $routes->get('assign-admin-kab', 'AdminSurveiProvController::AssignAdminSurveiKab');
    $routes->get('assign-admin-kab/create', 'AdminSurveiProvController::tambah_AssignAdminSurveiKab');
});

// ================== ADMIN SURVEI KABUPATEN (id_role = 3) ==================
$routes->group('adminsurvei-kab', ['filter' => 'role:3'], static function ($routes) {
    $routes->get('/', 'AdminSurveiKabController::index');
    $routes->get('assign-petugas', 'AdminSurveiKabController::AssignPetugas');
    $routes->get('assign-petugas/create', 'AdminSurveiKabController::createAssignPetugas');
    $routes->get('assign-petugas/detail/(:num)', 'AdminSurveiKabController::detail/$1');
    $routes->get('assign-petugas/pcl-detail/(:num)', 'AdminSurveiKabController::kurva_s/$1');
    $routes->get('approval-laporan', 'AdminSurveiKabController::approve_laporan');
});

// ================== PEMANTAU (id_role = 4) ==================
$routes->group('pemantau', ['filter' => 'role:4'], static function ($routes) {
    $routes->get('/', 'PemantauController::index');
    $routes->get('detail-proses', 'PemantauController::DetailProses');
    $routes->get('kegiatan-wilayah-pemantau', 'PemantauController::KegiatanWilayah');
    $routes->get('data-petugas', 'PemantauController::DataPetugas');
    $routes->get('laporan-petugas', 'PemantauController::LaporanPetugas');
    $routes->get('laporan-petugas/detail/(:num)', 'PemantauController::detailLaporanPetugas/$1');
});
