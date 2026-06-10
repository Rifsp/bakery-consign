<?php

namespace App\Models;

use CodeIgniter\Model;

class StokSalesModel extends Model
{
    protected $table = 'stok_sales';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['sales_id', 'produk_id', 'stok_tersedia'];

    protected $useTimestamps = true;
    protected $createdField = '';
    protected $updatedField = 'updated_at';

    public function getStok(int $salesId, int $produkId): int
    {
        $row = $this->where('sales_id', $salesId)->where('produk_id', $produkId)->first();
        return $row ? (int) $row['stok_tersedia'] : 0;
    }

    public function tambahStok(int $salesId, int $produkId, int $qty): void
    {
        $row = $this->where('sales_id', $salesId)->where('produk_id', $produkId)->first();
        if ($row) {
            $this->update($row['id'], ['stok_tersedia' => $row['stok_tersedia'] + $qty]);
        } else {
            $this->insert([
                'sales_id' => $salesId,
                'produk_id' => $produkId,
                'stok_tersedia' => $qty,
            ]);
        }
    }
}
