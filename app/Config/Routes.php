<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Auth::index');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/supplier', 'Supplier::index');
$routes->get('/supplier/create', 'Supplier::create');
$routes->post('/supplier/store', 'Supplier::store');
$routes->get('/supplier/edit/(:num)', 'Supplier::edit/$1');
$routes->post('/supplier/update/(:num)', 'Supplier::update/$1');
$routes->post('/supplier/delete/(:num)', 'Supplier::delete/$1');


// Toko CRUD
$routes->get('/toko', 'Toko::index');
$routes->get('/toko/create', 'Toko::create');
$routes->post('/toko/store', 'Toko::store');
$routes->get('/toko/edit/(:num)', 'Toko::edit/$1');
$routes->post('/toko/update/(:num)', 'Toko::update/$1');
$routes->post('/toko/delete/(:num)', 'Toko::delete/$1');


// Produk CRUD
$routes->get('/produk', 'Produk::index');
$routes->get('/produk/create', 'Produk::create');
$routes->post('/produk/store', 'Produk::store');
$routes->get('/produk/edit/(:num)', 'Produk::edit/$1');
$routes->post('/produk/update/(:num)', 'Produk::update/$1');
$routes->post('/produk/delete/(:num)', 'Produk::delete/$1');


// Pembelian (PO) CRUD
$routes->get('/pembelian', 'Pembelian::index');
$routes->get('/pembelian/fetch', 'Pembelian::fetch');
$routes->get('/pembelian/create', 'Pembelian::create');
$routes->post('/pembelian/store', 'Pembelian::store');
$routes->get('/pembelian/detail/(:num)', 'Pembelian::detail/$1');
$routes->get('/pembelian/terima/(:num)', 'Pembelian::terimaBarang/$1');
$routes->post('/pembelian/proses-terima/(:num)', 'Pembelian::prosesTerima/$1');
$routes->post('/pembelian/delete/(:num)', 'Pembelian::destroy/$1');


// Pengiriman ke Sales CRUD
$routes->get('/pengiriman', 'PengirimanKeSales::index');
$routes->get('/pengiriman/fetch', 'PengirimanKeSales::fetch');
$routes->get('/pengiriman/create', 'PengirimanKeSales::create');
$routes->post('/pengiriman/store', 'PengirimanKeSales::store');
$routes->get('/pengiriman/detail/(:num)', 'PengirimanKeSales::detail/$1');
$routes->get('/pengiriman/get-stok-sales', 'PengirimanKeSales::getStokSales');
$routes->post('/pengiriman/delete/(:num)', 'PengirimanKeSales::destroy/$1');


// Kunjungan CRUD (dengan Penjualan & Retur)
$routes->get('/kunjungan', 'Kunjungan::index');
$routes->get('/kunjungan/fetch', 'Kunjungan::fetch');
$routes->get('/kunjungan/create', 'Kunjungan::create');
$routes->post('/kunjungan/store', 'Kunjungan::store');
$routes->get('/kunjungan/detail/(:num)', 'Kunjungan::detail/$1');
$routes->get('/kunjungan/get-stok-toko', 'Kunjungan::getStokTokoJson');


// Penitipan CRUD
$routes->get('/penitipan', 'Penitipan::index');
$routes->get('/penitipan/fetch', 'Penitipan::fetch');
$routes->get('/penitipan/create', 'Penitipan::create');
$routes->post('/penitipan/store', 'Penitipan::store');
$routes->get('/penitipan/detail/(:num)', 'Penitipan::detail/$1');
$routes->get('/penitipan/get-stok-sales', 'Penitipan::getStokSalesJson');
$routes->post('/penitipan/delete/(:num)', 'Penitipan::destroy/$1');


// Retur CRUD (admin only — approve/reject)
$routes->get('/retur', 'Retur::index');
$routes->get('/retur/fetch', 'Retur::fetch');
$routes->get('/retur/detail/(:num)', 'Retur::detail/$1');
$routes->post('/retur/approve/(:num)', 'Retur::approve/$1');
$routes->post('/retur/reject/(:num)', 'Retur::reject/$1');


// Laporan
$routes->get('/laporan/laba-rugi', 'Laporan::labaRugi');
$routes->get('/laporan/penjualan', 'Laporan::penjualan');
$routes->get('/laporan/pembelian', 'Laporan::pembelian');
$routes->get('/laporan/stok-gudang', 'Laporan::stokGudang');
$routes->get('/laporan/stok-sales', 'Laporan::stokSales');
$routes->get('/laporan/stok-toko', 'Laporan::stokToko');
$routes->get('/laporan/expired', 'Laporan::expired');
$routes->get('/laporan/penjualan-saya', 'Laporan::penjualanSaya');
$routes->get('/laporan/fee-sales', 'Laporan::feeSales');
$routes->get('/laporan/stok-sales-saya', 'Laporan::stokSalesSaya');
$routes->get('/laporan/stok-toko-saya', 'Laporan::stokTokoSaya');
$routes->get('/laporan/stok-toko-detail/(:num)', 'Laporan::stokTokoDetail/$1');
$routes->get('/laporan/export-csv/(:any)', 'Laporan::exportCsv/$1');


// Harga Jual CRUD
$routes->get('/harga-jual', 'HargaJual::index');
$routes->get('/harga-jual/create', 'HargaJual::create');
$routes->post('/harga-jual/store', 'HargaJual::store');
$routes->get('/harga-jual/edit/(:num)', 'HargaJual::edit/$1');
$routes->post('/harga-jual/update/(:num)', 'HargaJual::update/$1');
$routes->post('/harga-jual/delete/(:num)', 'HargaJual::delete/$1');


// User
$routes->get('/user', 'User::index');
$routes->get('/user/create', 'User::create');
$routes->post('/user/store', 'User::store');
$routes->get('/user/edit/(:num)', 'User::edit/$1');
$routes->post('/user/update/(:num)', 'User::update/$1');
$routes->get('/user/delete/(:num)', 'User::delete/$1');
