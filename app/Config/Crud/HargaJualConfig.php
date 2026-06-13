<?php

namespace App\Config\Crud;

use App\Config\Crud\TableConfig;

return new TableConfig([
    'name' => 'harga_jual',
    'title' => 'Harga Jual',
    'route' => '/harga-jual',
    'model' => 'HargaJualModel',
    'controller' => 'HargaJual',
    'orderBy' => 'id',
    'orderDir' => 'DESC',
    'accessRoles' => ['admin'],
    'fields' => [
        ['name' => 'id',         'label' => 'No',          'type' => 'text',    'showInTable' => true,  'showInForm' => false],
        ['name' => 'produk_id',  'label' => 'Produk',      'type' => 'select',  'showInTable' => false, 'showInForm' => true,  'relationModel' => 'ProdukModel', 'relationField' => 'nama', 'relationValue' => 'id', 'required' => true, 'validation' => 'required'],
        ['name' => 'nama_harga', 'label' => 'Nama Harga',  'type' => 'text',    'showInTable' => true,  'showInForm' => true,  'required' => true, 'validation' => 'required', 'placeholder' => 'Contoh: Harga 1, Harga Grosir'],
        ['name' => 'harga',      'label' => 'Harga',       'type' => 'number',  'showInTable' => true,  'showInForm' => true,  'required' => true, 'validation' => 'required', 'placeholder' => '0'],
        ['name' => 'fee_sales',  'label' => 'Fee Sales',   'type' => 'number',  'showInTable' => true,  'showInForm' => true,  'placeholder' => '0'],
        ['name' => 'is_aktif',   'label' => 'Status',      'type' => 'boolean', 'showInTable' => true,  'showInForm' => true],
    ],
]);
