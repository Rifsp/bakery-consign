<?php

namespace App\Models;

use CodeIgniter\Model;

class PenitipanDetailModel extends Model
{
    protected $table = 'penitipan_detail';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['penitipan_id', 'produk_id', 'harga_jual_id', 'jumlah_titip', 'tgl_expired', 'harga_satuan', 'fee_satuan'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';
}
