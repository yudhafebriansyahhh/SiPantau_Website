<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Login::index');
$routes->get('admin', 'Admin::index');
$routes->get('comingsoon', 'ComingSoon::index');
$routes->get('adminsurvei','AdminSurveiProvController::index');
$routes->get('adminsurvei-kab','AdminSurveiKabController');

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

// Master Kegiatan Detail Proses
$routes->get('master-kegiatan-detail-proses', 'AdminSurveiProvController::master_detail_proses');
$routes->get('master-kegiatan-detail-proses/create','AdminSurveiProvController::tambah_detail_proses');

//Master Kegiatan Wilayah
$routes->get('master-kegiatan-wilayah','AdminSurveiProvController::master_kegiatan_wilayah::index');
$routes->get('master-kegiatan-wilayah/create','AdminSurveiProvController::tambah_master_kegiatan_wilayah');

//Assign Admin Survei Kab
$routes->get('assign-admin-kab','AdminSurveiProvController::AssignAdminSurveiKab');
$routes->get('assign-admin-kab/create','AdminSurveiProvController::tambah_AssignAdminSurveiKab');

//Assign Petugas Survei
$routes->get('assign-petugas','AdminSurveiKabController::AssignPetugas');
$routes->get('assign-petugas/create','AdminSurveiKabController::createAssignPetugas');
$routes->get('assign-petugas/detail/(:num)','AdminSurveiKabController::detail/$1');
$routes->get('assign-petugas/pcl-detail/(:num)','AdminSurveiKabController::kurva_s/$1');