<?php

namespace App\Models;

use App\Models\BaseCrudModel;

class ProdukModel extends BaseCrudModel
{
    protected $table = 'produk';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['kode_produk', 'nama', 'kategori_id', 'satuan', 'hpp', 'shelf_life_hari', 'deskripsi', 'is_aktif'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
