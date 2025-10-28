<?php

use CodeIgniter\Controller;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ================== AUTH ==================
$routes->get('/', 'Auth\LoginController::index');
$routes->get('login', 'Auth\LoginController::index');
$routes->post('auth/login', 'Auth\LoginController::process');
$routes->get('logout', 'Auth\LoginController::logout');

// Multi-Role Routes
$routes->get('login/select-role', 'Auth\LoginController::selectRole');
$routes->post('login/process-role-selection', 'Auth\LoginController::processRoleSelection');

// ================== HALAMAN TIDAK BERIZIN ==================
$routes->get('unauthorized', 'ErrorController::unauthorized');

// ================== SUPER ADMIN (id_role = 1) ==================
$routes->group('superadmin', ['filter' => 'role:1'], static function ($routes) {
    $routes->get('/', 'SuperAdmin\DashboardController::index');

    // Master Output Routes
    $routes->get('master-output', 'SuperAdmin\MasterOutputController::index');
    $routes->get('master-output/create', 'SuperAdmin\MasterOutputController::create');
    $routes->post('master-output', 'SuperAdmin\MasterOutputController::store');
    $routes->get('master-output/(:num)/edit', 'SuperAdmin\MasterOutputController::edit/$1');
    $routes->put('master-output/(:num)', 'SuperAdmin\MasterOutputController::update/$1');
    $routes->delete('master-output/(:num)', 'SuperAdmin\MasterOutputController::delete/$1');
    $routes->get('master-output/data', 'SuperAdmin\MasterOutputController::getData');


    // Master Kegiatan Routes
    $routes->get('master-kegiatan', 'SuperAdmin\MasterKegiatanController::index');
    $routes->get('master-kegiatan/create', 'SuperAdmin\MasterKegiatanController::create');
    $routes->post('master-kegiatan', 'SuperAdmin\MasterKegiatanController::store');
    $routes->get('master-kegiatan/show/(:num)', 'SuperAdmin\MasterKegiatanController::show/$1');
    $routes->get('master-kegiatan/edit/(:num)', 'SuperAdmin\MasterKegiatanController::edit/$1');
    $routes->put('master-kegiatan/(:num)', 'SuperAdmin\MasterKegiatanController::update/$1');
    $routes->delete('master-kegiatan/(:num)', 'SuperAdmin\MasterKegiatanController::delete/$1');

    // Master Kegiatan Detail Routes
    $routes->get('master-kegiatan-detail', 'SuperAdmin\MasterKegiatanDetailController::index');
    $routes->get('master-kegiatan-detail/create', 'SuperAdmin\MasterKegiatanDetailController::create');
    $routes->post('master-kegiatan-detail', 'SuperAdmin\MasterKegiatanDetailController::store');
    $routes->get('master-kegiatan-detail/show/(:num)', 'SuperAdmin\MasterKegiatanDetailController::show/$1');
    $routes->get('master-kegiatan-detail/(:num)/edit', 'SuperAdmin\MasterKegiatanDetailController::edit/$1');
    $routes->put('master-kegiatan-detail/(:num)', 'SuperAdmin\MasterKegiatanDetailController::update/$1');
    $routes->delete('master-kegiatan-detail/(:num)', 'SuperAdmin\MasterKegiatanDetailController::delete/$1');
    $routes->get('master-kegiatan-detail/by-kegiatan/(:num)', 'SuperAdmin\MasterKegiatanDetailController::getByKegiatan/$1');

    // Kelola Pengguna Routes
    $routes->group('kelola-pengguna', static function ($routes) {
        $routes->get('/', 'SuperAdmin\KelolaPenggunaController::index');
        $routes->get('create', 'SuperAdmin\KelolaPenggunaController::create');
        $routes->post('store', 'SuperAdmin\KelolaPenggunaController::store');
        $routes->get('edit/(:num)', 'SuperAdmin\KelolaPenggunaController::edit/$1');
        $routes->post('update/(:num)', 'SuperAdmin\KelolaPenggunaController::update/$1');
        $routes->put('update/(:num)', 'SuperAdmin\KelolaPenggunaController::update/$1');
        $routes->delete('delete/(:num)', 'SuperAdmin\KelolaPenggunaController::delete/$1');
        $routes->post('toggle-status/(:num)', 'SuperAdmin\KelolaPenggunaController::toggleStatus/$1');
        $routes->get('download-template', 'SuperAdmin\KelolaPenggunaController::downloadTemplate');
        $routes->post('import', 'SuperAdmin\KelolaPenggunaController::import');
        $routes->get('export', 'SuperAdmin\KelolaPenggunaController::export');
    });

    // Kelola Admin Survei Provinsi Routes
    $routes->group('kelola-admin-surveyprov', static function ($routes) {
        $routes->get('/', 'SuperAdmin\KelolaSurveiProvinsiController::index');
        $routes->get('assign', 'SuperAdmin\KelolaSurveiProvinsiController::assign');
        $routes->post('store-assign', 'SuperAdmin\KelolaSurveiProvinsiController::storeAssign');
        $routes->get('assign/(:num)', 'SuperAdmin\KelolaSurveiProvinsiController::assign/$1');
        $routes->post('update/(:num)', 'SuperAdmin\KelolaSurveiProvinsiController::update/$1');
        $routes->post('delete-assignment', 'SuperAdmin\KelolaSurveiProvinsiController::deleteAssignment');
        $routes->delete('delete/(:num)', 'SuperAdmin\KelolaSurveiProvinsiController::delete/$1');

        // Detail
        $routes->get('detail/(:num)', 'SuperAdmin\KelolaSurveiProvinsiController::detail/$1');
    });

    $routes->get('comingsoon', 'ComingSoon::index');

});

// ================== ADMIN SURVEI PROVINSI (id_role = 2) ==================
$routes->group('adminsurvei', ['filter' => 'role:2'], static function ($routes) {
    $routes->get('/', 'AdminSurveiProvController::index');
    $routes->get('master-kegiatan-detail-proses', 'AdminProv\MasterKegiatanDetailProsesController::index');
    $routes->get('master-kegiatan-detail-proses/create', 'AdminProv\MasterKegiatanDetailProsesController::create');
    $routes->post('master-kegiatan-detail-proses/store', 'AdminProv\MasterKegiatanDetailProsesController::store');
    $routes->delete('master-kegiatan-detail-proses/delete/(:num)', 'AdminProv\MasterKegiatanDetailProsesController::delete/$1');
    $routes->get('master-kegiatan-detail-proses/edit/(:num)', 'AdminProv\MasterKegiatanDetailProsesController::edit/$1');
    $routes->post('master-kegiatan-detail-proses/update/(:num)', 'AdminProv\MasterKegiatanDetailProsesController::update/$1');
    $routes->get('master-kegiatan-wilayah', 'AdminProv\MasterKegiatanWilayahController::index');
    $routes->get('master-kegiatan-wilayah/create', 'AdminProv\MasterKegiatanWilayahController::create');
    $routes->post('master-kegiatan-wilayah/store', 'AdminProv\MasterKegiatanWilayahController::store');
    // $routes->get('adminsurvei/kurva-provinsi', 'AdminSurveiProvController::getKurvaProvinsi');
    $routes->get('kegiatan-wilayah', 'AdminSurveiProvController::getKegiatanWilayah');
    $routes->get('master-kegiatan-wilayah/edit/(:num)', 'AdminProv\MasterKegiatanWilayahController::edit/$1');
    $routes->post('master-kegiatan-wilayah/update/(:num)', 'AdminProv\MasterKegiatanWilayahController::update/$1');
    $routes->delete('master-kegiatan-wilayah/delete/(:num)', 'AdminProv\MasterKegiatanWilayahController::delete/$1');
    $routes->get('master-kegiatan-wilayah/sisa-target/(:num)', 'AdminProv\MasterKegiatanWilayahController::getSisaTarget/$1');


    $routes->get('kurva-kabupaten', 'AdminSurveiProvController::getKurvaKabupaten');
    $routes->get('assign-admin-kab', 'AdminSurveiProvController::AssignAdminSurveiKab');
    $routes->get('assign-admin-kab/create', 'AdminSurveiProvController::tambah_AssignAdminSurveiKab');
    $routes->get('kurva-provinsi', 'AdminSurveiProvController::getKurvaProvinsi');
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
