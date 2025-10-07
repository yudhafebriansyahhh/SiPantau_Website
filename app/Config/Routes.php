<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Login::index');
$routes->get('admin', 'Admin::index');
$routes->get('comingsoon', 'ComingSoon::index');

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

