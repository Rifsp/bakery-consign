<?php

namespace App\Models;

use CodeIgniter\Model;

class PengirimanKeSalesModel extends Model
{
    protected $table = 'pengiriman_ke_sales';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['nomor_kirim', 'sales_id', 'tanggal_kirim', 'catatan', 'dibuat_oleh'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function generateNomorKirim(): string
    {
        $prefix = 'KS-' . date('ym') . '-';
        $last = $this->select('nomor_kirim')
            ->like('nomor_kirim', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->first();

        if ($last) {
            $lastNum = (int) substr($last['nomor_kirim'], -4);
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        return $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }
}
