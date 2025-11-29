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

// Multi-Role Login
$routes->get('login/select-role', 'Auth\LoginController::selectRole');
$routes->get('login/switch-role', 'Auth\LoginController::switchRole');
$routes->post('login/process-role-selection', 'Auth\LoginController::processRoleSelection');
$routes->post('login/process-switch-role', 'Auth\LoginController::processSwitchRole');

// ================== HALAMAN TIDAK BERIZIN ==================
$routes->get('unauthorized', 'ErrorController::unauthorized');

// ================== HALAMAN COMING SOON ==================
$routes->get('comingsoon', 'ComingSoon::index');

// ================== SUPER ADMIN (id_role = 1) ==================
$routes->group('superadmin', ['filter' => 'role:1'], static function ($routes) {

    // ===== Dashboard =====
    $routes->get('/', 'SuperAdmin\DashboardController::index');
    $routes->get('get-kurva-s', 'SuperAdmin\DashboardController::getKurvaS');
    $routes->get('get-kegiatan-wilayah', 'SuperAdmin\DashboardController::getKegiatanWilayah');
    $routes->get('get-petugas', 'SuperAdmin\DashboardController::getPetugas');

    // ===== Master Output =====
    $routes->group('master-output', static function ($routes) {
        $routes->get('/', 'SuperAdmin\MasterOutputController::index');
        $routes->get('create', 'SuperAdmin\MasterOutputController::create');
        $routes->post('/', 'SuperAdmin\MasterOutputController::store');
        $routes->get('(:num)/edit', 'SuperAdmin\MasterOutputController::edit/$1');
        $routes->put('(:num)', 'SuperAdmin\MasterOutputController::update/$1');
        $routes->delete('(:num)', 'SuperAdmin\MasterOutputController::delete/$1');
        $routes->get('data', 'SuperAdmin\MasterOutputController::getData');
    });

    // ===== Master Kegiatan =====
    $routes->group('master-kegiatan', static function ($routes) {
        $routes->get('/', 'SuperAdmin\MasterKegiatanController::index');
        $routes->get('create', 'SuperAdmin\MasterKegiatanController::create');
        $routes->post('/', 'SuperAdmin\MasterKegiatanController::store');
        $routes->get('show/(:num)', 'SuperAdmin\MasterKegiatanController::show/$1');
        $routes->get('edit/(:num)', 'SuperAdmin\MasterKegiatanController::edit/$1');
        $routes->put('(:num)', 'SuperAdmin\MasterKegiatanController::update/$1');
        $routes->delete('(:num)', 'SuperAdmin\MasterKegiatanController::delete/$1');
    });

    // ===== Master Kegiatan Detail =====
    $routes->group('master-kegiatan-detail', static function ($routes) {
        $routes->get('/', 'SuperAdmin\MasterKegiatanDetailController::index');
        $routes->get('create', 'SuperAdmin\MasterKegiatanDetailController::create');
        $routes->post('/', 'SuperAdmin\MasterKegiatanDetailController::store');
        $routes->get('show/(:num)', 'SuperAdmin\MasterKegiatanDetailController::show/$1');
        $routes->get('(:num)/edit', 'SuperAdmin\MasterKegiatanDetailController::edit/$1');
        $routes->put('(:num)', 'SuperAdmin\MasterKegiatanDetailController::update/$1');
        $routes->delete('(:num)', 'SuperAdmin\MasterKegiatanDetailController::delete/$1');

        // Additional helper endpoints
        $routes->get('by-kegiatan/(:num)', 'SuperAdmin\MasterKegiatanDetailController::getByKegiatan/$1');
        $routes->get('get-admins/(:num)', 'SuperAdmin\MasterKegiatanDetailController::getAdmins/$1');
        $routes->get('kegiatan-wilayah/(:num)', 'SuperAdmin\MasterKegiatanDetailController::showKegiatanWilayah/$1');
        $routes->get('kurva-provinsi', 'SuperAdmin\MasterKegiatanDetailController::getKurvaProvinsi');
    });

    // ===== Kelola Pengguna =====
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

    // ===== Kelola Admin Survei Provinsi =====
    $routes->group('kelola-admin-surveyprov', static function ($routes) {
        $routes->get('/', 'SuperAdmin\KelolaSurveiProvinsiController::index');
        $routes->get('assign', 'SuperAdmin\KelolaSurveiProvinsiController::assign');
        $routes->get('assign/(:num)', 'SuperAdmin\KelolaSurveiProvinsiController::assign/$1');
        $routes->post('store-assign', 'SuperAdmin\KelolaSurveiProvinsiController::storeAssign');
        $routes->post('update/(:num)', 'SuperAdmin\KelolaSurveiProvinsiController::update/$1');
        $routes->post('delete-assignment', 'SuperAdmin\KelolaSurveiProvinsiController::deleteAssignment');
        $routes->delete('delete/(:num)', 'SuperAdmin\KelolaSurveiProvinsiController::delete/$1');

        // Additional helper endpoints
        $routes->get('detail/(:num)', 'SuperAdmin\KelolaSurveiProvinsiController::detail/$1');
    });

    // ===== Feedback  =====
    $routes->group('feedback', static function ($routes) {
        $routes->get('/', 'SuperAdmin\FeedbackController::index');
        $routes->get('create', 'SuperAdmin\FeedbackController::create');
        $routes->post('store', 'SuperAdmin\FeedbackController::store');
        $routes->get('edit/(:num)', 'SuperAdmin\FeedbackController::edit/$1');
        $routes->put('update/(:num)', 'SuperAdmin\FeedbackController::update/$1');
        $routes->post('update/(:num)', 'SuperAdmin\FeedbackController::update/$1');
        $routes->delete('delete/(:num)', 'SuperAdmin\FeedbackController::delete/$1');

        // AJAX endpoints
        $routes->get('get-user-detail', 'SuperAdmin\FeedbackController::getUserDetail');
        $routes->get('get-user-feedback-history', 'SuperAdmin\FeedbackController::getUserFeedbackHistory');
    });

    // ===== Rating Aplikasi  =====
    $routes->group('rating-aplikasi', static function ($routes) {
        $routes->get('/', 'SuperAdmin\RatingAplikasiController::index');
        $routes->get('show/(:num)', 'SuperAdmin\RatingAplikasiController::show/$1');
        $routes->delete('delete/(:num)', 'SuperAdmin\RatingAplikasiController::delete/$1');
        $routes->get('export-csv', 'SuperAdmin\RatingAplikasiController::exportCSV');

        // AJAX endpoints
        $routes->get('get-rating-trend', 'SuperAdmin\RatingAplikasiController::getRatingTrend');
    });

});

// ================== ADMIN SURVEI PROVINSI (id_role = 2) ==================
$routes->group('adminsurvei', ['filter' => 'role:2'], static function ($routes) {

    // ===== Dashboard =====
    $routes->get('/', 'AdminProv\DashboardController::index');
    $routes->get('kurva-provinsi', 'AdminProv\DashboardController::getKurvaProvinsi');
    $routes->get('kurva-kabupaten', 'AdminProv\DashboardController::getKurvaKabupaten');
    $routes->get('kegiatan-wilayah', 'AdminProv\DashboardController::getKegiatanWilayah');
    $routes->get('get-petugas', 'AdminProv\DashboardController::getPetugas');

    // ===== Master Kegiatan Detail Proses =====
    $routes->group('master-kegiatan-detail-proses', static function ($routes) {
        $routes->get('/', 'AdminProv\MasterKegiatanDetailProsesController::index');
        $routes->get('create', 'AdminProv\MasterKegiatanDetailProsesController::create');
        $routes->post('store', 'AdminProv\MasterKegiatanDetailProsesController::store');
        $routes->get('edit/(:num)', 'AdminProv\MasterKegiatanDetailProsesController::edit/$1');
        $routes->post('update/(:num)', 'AdminProv\MasterKegiatanDetailProsesController::update/$1');
        $routes->delete('delete/(:num)', 'AdminProv\MasterKegiatanDetailProsesController::delete/$1');
        $routes->get('clear-filter', 'AdminProv\MasterKegiatanDetailProsesController::clearFilter');
    });

    // ===== Master Kegiatan Wilayah =====
    $routes->group('master-kegiatan-wilayah', static function ($routes) {
        $routes->get('/', 'AdminProv\MasterKegiatanWilayahController::index');
        $routes->get('create', 'AdminProv\MasterKegiatanWilayahController::create');
        $routes->post('store', 'AdminProv\MasterKegiatanWilayahController::store');
        $routes->get('edit/(:num)', 'AdminProv\MasterKegiatanWilayahController::edit/$1');
        $routes->post('update/(:num)', 'AdminProv\MasterKegiatanWilayahController::update/$1');
        $routes->post('delete/(:num)', 'AdminProv\MasterKegiatanWilayahController::delete/$1');
        $routes->get('sisa-target/(:num)', 'AdminProv\MasterKegiatanWilayahController::getSisaTarget/$1');
        $routes->get('get-kegiatan-detail-proses/(:num)', 'AdminProv\MasterKegiatanWilayahController::getKegiatanDetailProses/$1');
        $routes->get('clear-filter', 'AdminProv\MasterKegiatanWilayahController::clearFilter');
    });

    // ===== Assign Admin Survei Kabupaten =====
    $routes->group('admin-survei-kab', static function ($routes) {
        $routes->get('/', 'AdminProv\AssignAdminSurveiKabController::index');
        $routes->get('assign', 'AdminProv\AssignAdminSurveiKabController::assign');
        $routes->get('assign/(:num)', 'AdminProv\AssignAdminSurveiKabController::assign/$1');
        $routes->post('store', 'AdminProv\AssignAdminSurveiKabController::storeAssign');
        $routes->post('update/(:num)', 'AdminProv\AssignAdminSurveiKabController::update/$1');
        $routes->post('delete-assignment', 'AdminProv\AssignAdminSurveiKabController::deleteAssignment');
        $routes->post('delete/(:num)', 'AdminProv\AssignAdminSurveiKabController::delete/$1');

        // Additional helper endpoints
        $routes->get('get-kegiatan/(:num)', 'AdminProv\AssignAdminSurveiKabController::getKegiatanByKabupaten/$1');
        $routes->get('get-assigned-kegiatan/(:segment)', 'AdminProv\AssignAdminSurveiKabController::getAssignedKegiatan/$1');
    });
});

// ================== ADMIN SURVEI KABUPATEN (id_role = 3) ==================
$routes->group('adminsurvei-kab', ['filter' => 'role:3'], static function ($routes) {

    // ===== Dashboard =====
    $routes->get('/', 'AdminKab\DashboardController::index');
    $routes->get('get-kurva-s', 'AdminKab\DashboardController::getKurvaS');
    $routes->get('get-petugas', 'AdminKab\DashboardController::getPetugas');

    // ===== Assign Petugas =====
    $routes->group('assign-petugas', static function ($routes) {
        $routes->get('/', 'AdminKab\AssignPetugasController::index');
        $routes->get('create', 'AdminKab\AssignPetugasController::create');
        $routes->post('store', 'AdminKab\AssignPetugasController::store');
        $routes->get('detail/(:num)', 'AdminKab\AssignPetugasController::detailPML/$1');
        $routes->post('delete/(:num)', 'AdminKab\AssignPetugasController::delete/$1');
        $routes->get('pcl-detail/(:num)', 'AdminKab\AssignPetugasController::pclDetail/$1');
        $routes->get('edit/(:num)', 'AdminKab\AssignPetugasController::edit/$1');
        $routes->post('update/(:num)', 'AdminKab\AssignPetugasController::update/$1');

        // Additional helper endpoints
        $routes->post('get-sisa-target-wilayah', 'AdminKab\AssignPetugasController::getSisaTargetKegiatanWilayah');
        $routes->post('get-available-pml', 'AdminKab\AssignPetugasController::getAvailablePML');
        $routes->post('get-available-pcl', 'AdminKab\AssignPetugasController::getAvailablePCL');
    });

    // ===== Data Petugas Routes =====
    $routes->group('data-petugas', static function ($routes) {
        $routes->get('/', 'AdminKab\DataPetugasController::index');
        $routes->get('detail/(:segment)', 'AdminKab\DataPetugasController::detailPetugas/$1');
        $routes->get('detail-pcl/(:num)', 'AdminKab\DataPetugasController::detailPCL/$1');
        $routes->get('detail-pml/(:num)', 'AdminKab\DataPetugasController::detailPML/$1');

        // AJAX endpoints
        $routes->get('get-pantau-progress', 'AdminKab\DataPetugasController::getPantauProgress');
        $routes->get('get-laporan-transaksi', 'AdminKab\DataPetugasController::getLaporanTransaksi');
        $routes->post('save-feedback-pcl', 'AdminKab\DataPetugasController::saveFeedbackPCL');
    });

    $routes->get('approval-laporan', 'AdminKab\DashboardController::approve_laporan');
});

// ================== PEMANTAU PROVINSI (id_role = 2) ==================
$routes->group('pemantau-provinsi', ['filter' => 'role:2'], static function ($routes) {
    // ===== Dashboard =====
    $routes->get('/', 'PemantauProv\DashboardController::index');
    $routes->get('kurva-provinsi', 'PemantauProv\DashboardController::getKurvaProvinsi');
    $routes->get('kurva-kabupaten', 'PemantauProv\DashboardController::getKurvaKabupaten');
    $routes->get('kegiatan-wilayah', 'PemantauProv\DashboardController::getKegiatanWilayah');
    $routes->get('get-petugas', 'PemantauProv\DashboardController::getPetugas');

    // ===== Menu Lainnya (yang sudah ada sebelumnya) =====
    $routes->get('detail-proses', 'PemantauProv\MasterKegiatanDetailProsesController::index');
    $routes->get('kegiatan-wilayah-list', 'PemantauProv\MasterKegiatanWilayahController::index');

    // ===== Data Petugas =====
    $routes->get('data-petugas', 'PemantauProv\DataPetugasController::index');

    // ===== Laporan Petugas Routes =====
    $routes->get('laporan-petugas', 'PemantauProv\LaporanPetugasController::index');
    $routes->get('laporan-petugas/export-csv', 'PemantauProv\LaporanPetugasController::exportCSV');

    // ===== Detail Petugas Routes =====
    $routes->get('detail-petugas/(:num)', 'PemantauProv\DetailPetugasController::index/$1');
    $routes->get('detail-petugas/get-pantau-progress', 'PemantauProv\DetailPetugasController::getPantauProgress');
    $routes->get('detail-petugas/get-laporan-transaksi', 'PemantauProv\DetailPetugasController::getLaporanTransaksi');

});

// ================== PEMANTAU KABUPATEN (id_role = 3) ==================
$routes->group('pemantau-kabupaten', ['filter' => 'role:3'], static function ($routes) {
    // ===== Dashboard =====
    $routes->get('/', 'PemantauKab\DashboardController::index');
    $routes->get('kurva-kabupaten', 'PemantauKab\DashboardController::getKurvaKabupaten');
    $routes->get('get-petugas', 'PemantauKab\DashboardController::getPetugas');

    // ===== Kegiatan Wilayah =====
    $routes->get('kegiatan-wilayah', 'PemantauKab\MasterKegiatanWilayahController::index');

    // ===== Data Petugas =====
    $routes->get('data-petugas', 'PemantauKab\DataPetugasController::index');

    // ===== Laporan Petugas Routes =====
    $routes->get('laporan-petugas', 'PemantauKab\LaporanPetugasController::index');
    $routes->get('laporan-petugas/export-csv', 'PemantauKab\LaporanPetugasController::exportCSV');

    // ===== Detail Petugas Routes =====
    $routes->get('detail-petugas/(:num)', 'PemantauKab\DetailPetugasController::index/$1');
    $routes->get('detail-petugas/get-pantau-progress', 'PemantauKab\DetailPetugasController::getPantauProgress');
    $routes->get('detail-petugas/get-laporan-transaksi', 'PemantauKab\DetailPetugasController::getLaporanTransaksi');

});

// ================== API AUTH LOGIN ==================
$routes->group('api/auth', ['namespace' => 'App\Controllers\Api\Auth'], static function ($routes) {
    $routes->post('login', 'AuthController::login');
    $routes->get('me', 'AuthController::me', ['filter' => 'jwt']);
});

    //=================== Api Fitur Aplikasi Mobile ===============================
    $routes->group('api', [
        'namespace' => 'App\Controllers\Api',
        'filter'    => 'jwt'
    ], static function ($routes) {
        $routes->get('pelaporan', 'PelaporanController::index');
        $routes->post('pelaporan', 'PelaporanController::create');
        $routes->delete('pelaporan/(:num)', 'PelaporanController::delete/$1');
        $routes->get('kegiatan', 'KegiatanController::index');
        $routes->get('kecamatan', 'KecamatanController::index');
        $routes->get('desa', 'DesaController::index');
        $routes->post('progres', 'PantauProgressController::create');
        $routes->get('progres', 'PantauProgressController::index');
        $routes->delete('progres/(:num)', 'PantauProgressController::delete/$1');
        $routes->get('feedback', 'FeedBackUserController::index');
        $routes->post('feedback', 'FeedBackUserController::create');
        $routes->get('kurva-petugas/(:num)', 'KurvaPetugasController::show/$1');
        $routes->get('pcl/(:num)', 'PmlController::index/$1');        

});
