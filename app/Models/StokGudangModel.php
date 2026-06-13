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

    public function getStok(int $produkId): int
    {
        $db = $this->db;
        $row = $db->table($this->table)
            ->where('produk_id', $produkId)
            ->get(1)
            ->getRowArray();
        return $row ? (int) $row['stok_tersedia'] : 0;
    }

    public function tambahStok(int $produkId, int $qty): void
    {
        $db = $this->db;
        $row = $db->table($this->table)
            ->where('produk_id', $produkId)
            ->get(1)
            ->getRowArray();
        if ($row) {
            $db->table($this->table)
                ->where('id', $row['id'])
                ->update(['stok_tersedia' => $row['stok_tersedia'] + $qty]);
        } else {
            $db->table($this->table)
                ->insert([
                    'produk_id' => $produkId,
                    'stok_tersedia' => $qty,
                ]);
        }
    }
}
