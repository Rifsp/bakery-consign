<?php

namespace App\Models;

use CodeIgniter\Model;

class StokGudangModel extends Model
{
    protected $table = 'stok_gudang';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['produk_id', 'stok_tersedia'];

    protected $useTimestamps = true;
    protected $createdField = '';
    protected $updatedField = 'updated_at';
}
