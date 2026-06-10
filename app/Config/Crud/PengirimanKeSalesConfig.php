<?php

namespace App\Config\Crud;

use App\Config\Crud\TableConfig;

return new TableConfig([
    'name' => 'pengiriman_ke_sales',
    'title' => 'Pengiriman ke Sales',
    'route' => '/pengiriman',
    'model' => 'PengirimanKeSalesModel',
    'controller' => 'PengirimanKeSales',
    'orderBy' => 'id',
    'orderDir' => 'DESC',
    'accessRoles' => ['admin'],
    'fields' => [
        ['name' => 'id',             'label' => 'No',          'type' => 'text',    'showInTable' => true,  'showInForm' => false],
        ['name' => 'nomor_kirim',    'label' => 'Nomor Kirim', 'type' => 'text',    'showInTable' => true,  'showInForm' => false],
        ['name' => 'sales_id',       'label' => 'Sales',       'type' => 'select',  'showInTable' => true,  'showInForm' => true,  'relationModel' => 'UserModel', 'relationField' => 'nama', 'relationValue' => 'id', 'required' => true],
        ['name' => 'tanggal_kirim',  'label' => 'Tgl Kirim',   'type' => 'text',    'showInTable' => true,  'showInForm' => false],
        ['name' => 'catatan',        'label' => 'Catatan',     'type' => 'textarea','showInTable' => false, 'showInForm' => true],
    ],
]);
