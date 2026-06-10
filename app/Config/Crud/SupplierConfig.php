<?php

namespace App\Config\Crud;

use App\Config\Crud\TableConfig;

return new TableConfig([
    'name' => 'suppliers',
    'title' => 'Supplier',
    'route' => '/supplier',
    'model' => 'SupplierModel',
    'controller' => 'Supplier',
    'autoCode' => 'kode_supplier',
    'autoCodePrefix' => 'SUP',
    'orderBy' => 'id',
    'orderDir' => 'DESC',
    'fields' => [
        [
            'name' => 'id',
            'label' => 'No',
            'type' => 'text',
            'showInTable' => true,
            'showInForm' => false,
        ],
        [
            'name' => 'kode_supplier',
            'label' => 'Kode',
            'type' => 'text',
            'showInTable' => true,
            'showInForm' => false,
        ],
        [
            'name' => 'nama',
            'label' => 'Nama Supplier',
            'type' => 'text',
            'showInTable' => true,
            'showInForm' => true,
            'required' => true,
            'validation' => 'required',
            'placeholder' => 'Masukkan nama supplier',
        ],
        [
            'name' => 'kontak_person',
            'label' => 'Kontak Person',
            'type' => 'text',
            'showInTable' => true,
            'showInForm' => true,
            'placeholder' => 'Nama kontak person',
        ],
        [
            'name' => 'alamat',
            'label' => 'Alamat',
            'type' => 'textarea',
            'showInTable' => false,
            'showInForm' => true,
            'placeholder' => 'Masukkan alamat lengkap',
        ],
        [
            'name' => 'telepon',
            'label' => 'Telepon',
            'type' => 'text',
            'showInTable' => true,
            'showInForm' => true,
            'placeholder' => '021-xxxxxxx',
        ],
        [
            'name' => 'email',
            'label' => 'Email',
            'type' => 'email',
            'showInTable' => true,
            'showInForm' => true,
            'placeholder' => 'email@contoh.com',
        ],
        [
            'name' => 'is_aktif',
            'label' => 'Status',
            'type' => 'boolean',
            'showInTable' => true,
            'showInForm' => true,
        ],
    ],
]);
