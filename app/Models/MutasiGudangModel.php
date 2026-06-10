<?php

namespace App\Models;

use CodeIgniter\Model;

class MutasiGudangModel extends Model
{
    protected $table = 'mutasi_gudang';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['produk_id', 'jenis', 'jumlah', 'referensi_id', 'referensi_tabel', 'keterangan', 'dibuat_oleh'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';
}
