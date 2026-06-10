<?php

namespace App\Models;

use CodeIgniter\Model;

class MutasiSalesModel extends Model
{
    protected $table = 'mutasi_sales';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['sales_id', 'produk_id', 'jenis', 'jumlah', 'referensi_id', 'referensi_tabel', 'keterangan'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';
}
