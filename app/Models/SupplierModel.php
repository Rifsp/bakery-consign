<?php

namespace App\Models;

use App\Models\BaseCrudModel;

class SupplierModel extends BaseCrudModel
{
    protected $table = 'suppliers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['kode_supplier', 'nama', 'alamat', 'telepon', 'email', 'kontak_person', 'is_aktif'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
