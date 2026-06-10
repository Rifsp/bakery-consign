<?php

namespace App\Models;

use App\Models\BaseCrudModel;

class HargaJualModel extends BaseCrudModel
{
    protected $table = 'harga_jual';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['produk_id', 'nama_harga', 'harga', 'fee_sales', 'is_aktif'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
