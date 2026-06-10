<?php

namespace App\Models;

use CodeIgniter\Model;

class StokTokoModel extends Model
{
    protected $table = 'stok_toko';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['toko_id', 'produk_id', 'stok_tersedia'];

    protected $useTimestamps = true;
    protected $createdField = '';
    protected $updatedField = 'updated_at';

    public function getStok(int $tokoId, int $produkId): int
    {
        $row = $this->where('toko_id', $tokoId)->where('produk_id', $produkId)->first();
        return $row ? (int) $row['stok_tersedia'] : 0;
    }

    public function tambahStok(int $tokoId, int $produkId, int $qty): void
    {
        $row = $this->where('toko_id', $tokoId)->where('produk_id', $produkId)->first();
        if ($row) {
            $this->update($row['id'], ['stok_tersedia' => $row['stok_tersedia'] + $qty]);
        } else {
            $this->insert([
                'toko_id' => $tokoId,
                'produk_id' => $produkId,
                'stok_tersedia' => $qty,
            ]);
        }
    }
}
