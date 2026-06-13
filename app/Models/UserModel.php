<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['username', 'email', 'password', 'nama', 'role', 'telepon', 'foto', 'is_aktif'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = ['convertPgBooleans'];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function convertPgBooleans(array $data): array
    {
        if (!isset($data['data']) || $data['data'] === null) {
            return $data;
        }

        $booleanFields = ['is_aktif'];

        $rows = $data['data'];
        $isAssoc = is_array($rows) && count(array_filter(array_keys($rows), 'is_string')) > 0;

        if ($isAssoc) {
            $rows = [$rows];
        }

        foreach ($rows as &$row) {
            if (!is_array($row)) {
                continue;
            }
            foreach ($booleanFields as $field) {
                if (isset($row[$field])) {
                    $row[$field] = $row[$field] === 't' || $row[$field] === true || $row[$field] === 1;
                }
            }
        }

        $data['data'] = $isAssoc ? $rows[0] : $rows;

        return $data;
    }
}
