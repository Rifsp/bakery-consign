<?php

namespace App\Models;

use CodeIgniter\Model;

class ReturDetailModel extends Model
{
    protected $table = 'retur_detail';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['retur_id', 'produk_id', 'jumlah_retur', 'kondisi', 'tgl_expired', 'keterangan'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';
}
