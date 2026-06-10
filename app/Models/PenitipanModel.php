<?php

namespace App\Models;

use CodeIgniter\Model;

class PenitipanModel extends Model
{
    protected $table = 'penitipan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['nomor_titip', 'toko_id', 'sales_id', 'tanggal_titip', 'status', 'catatan'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function generateNomorTitip(): string
    {
        $prefix = 'TT-' . date('ym') . '-';
        $last = $this->select('nomor_titip')
            ->like('nomor_titip', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->first();

        if ($last) {
            $lastNum = (int) substr($last['nomor_titip'], -4);
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        return $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }
}
