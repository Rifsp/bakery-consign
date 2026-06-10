<?php

namespace App\Config\Crud;

use App\Config\Crud\TableConfig;

return new TableConfig([
    'name' => 'penitipan',
    'title' => 'Penitipan',
    'route' => '/penitipan',
    'model' => 'PenitipanModel',
    'controller' => 'Penitipan',
    'orderBy' => 'id',
    'orderDir' => 'DESC',
    'accessRoles' => ['admin', 'sales'],
    'fields' => [
        ['name' => 'id',             'label' => 'No',           'type' => 'text',    'showInTable' => true,  'showInForm' => false],
        ['name' => 'nomor_titip',    'label' => 'Nomor Titip',  'type' => 'text',    'showInTable' => true,  'showInForm' => false],
        ['name' => 'toko_id',        'label' => 'Toko',         'type' => 'select',  'showInTable' => true,  'showInForm' => true,  'relationModel' => 'TokoModel', 'relationField' => 'nama', 'relationValue' => 'id', 'required' => true],
        ['name' => 'sales_id',       'label' => 'Sales',        'type' => 'select',  'showInTable' => true,  'showInForm' => false, 'relationModel' => 'UserModel', 'relationField' => 'nama', 'relationValue' => 'id'],
        ['name' => 'tanggal_titip',  'label' => 'Tgl Titip',    'type' => 'text',    'showInTable' => true,  'showInForm' => false],
        ['name' => 'status',         'label' => 'Status',       'type' => 'text',    'showInTable' => true,  'showInForm' => false],
    ],
]);
