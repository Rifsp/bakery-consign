<?php

namespace App\Models;

use CodeIgniter\Model;

class PengirimanKeSalesDetailModel extends Model
{
    protected $table = 'pengiriman_ke_sales_detail';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['pengiriman_ke_sales_id', 'produk_id', 'jumlah', 'tgl_expired', 'harga_beli'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';
}
