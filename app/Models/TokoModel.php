<?php

namespace App\Models;

use App\Models\BaseCrudModel;

class TokoModel extends BaseCrudModel
{
    protected $table = 'toko';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['kode_toko', 'nama', 'pemilik', 'alamat', 'kelurahan', 'kecamatan', 'kota', 'telepon', 'sales_id', 'is_aktif'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
