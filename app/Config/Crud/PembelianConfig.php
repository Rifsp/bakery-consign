<?php

namespace App\Config\Crud;

use App\Config\Crud\TableConfig;

return new TableConfig([
    'name' => 'pembelian',
    'title' => 'Pembelian',
    'route' => '/pembelian',
    'model' => 'PembelianModel',
    'controller' => 'Pembelian',
    'orderBy' => 'id',
    'orderDir' => 'DESC',
    'accessRoles' => ['admin'],
    'fields' => [
        ['name' => 'id',             'label' => 'No',          'type' => 'text',    'showInTable' => true,  'showInForm' => false],
        ['name' => 'nomor_po',       'label' => 'Nomor PO',    'type' => 'text',    'showInTable' => true,  'showInForm' => false],
        ['name' => 'supplier_id',    'label' => 'Supplier',    'type' => 'select',  'showInTable' => true,  'showInForm' => true,  'relationModel' => 'SupplierModel', 'relationField' => 'nama', 'relationValue' => 'id', 'required' => true],
        ['name' => 'tanggal_pesan',  'label' => 'Tgl Pesan',   'type' => 'text',    'showInTable' => true,  'showInForm' => false],
        ['name' => 'status',         'label' => 'Status',      'type' => 'text',    'showInTable' => true,  'showInForm' => false],
        ['name' => 'total_nilai',    'label' => 'Total',       'type' => 'number',  'showInTable' => true,  'showInForm' => false],
    ],
]);
