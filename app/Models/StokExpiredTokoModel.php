<?php

namespace App\Models;

use CodeIgniter\Model;

class StokExpiredTokoModel extends Model
{
    protected $table = 'stok_expired_toko';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['toko_id', 'produk_id', 'penitipan_detail_id', 'jumlah', 'tgl_expired', 'is_diretur'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';
}
