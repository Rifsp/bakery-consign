<?php

namespace App\Models;

use App\Config\Crud\TableConfig;
use CodeIgniter\Model;

class BaseCrudModel extends Model
{
    protected TableConfig $tableConfig;
    protected array $searchable = [];
    protected array $filterable = [];

    public function setTableConfig(TableConfig $config): self
    {
        $this->tableConfig = $config;
        $this->searchable = $this->getSearchableFields();
        $this->filterable = $this->getFilterableFields();
        return $this;
    }

    public function getTableConfig(): TableConfig
    {
        return $this->tableConfig;
    }

    protected function getSearchableFields(): array
    {
        $fields = [];
        foreach ($this->tableConfig->fields as $field) {
            if (in_array($field->type, ['text', 'email', 'textarea'])) {
                $fields[] = $field->name;
            }
        }
        return $fields;
    }

    protected function getFilterableFields(): array
    {
        $fields = [];
        foreach ($this->tableConfig->fields as $field) {
            if (in_array($field->type, ['select', 'boolean'])) {
                $fields[] = $field->name;
            }
        }
        return $fields;
    }

    public function getPaginatedData(array $filters = [], ?string $search = null): array
    {
        $builder = $this->builder();

        if (!empty($this->tableConfig->joins)) {
            foreach ($this->tableConfig->joins as $join) {
                $builder->join($join['table'], $join['condition'], $join['type'] ?? 'left');
            }
        }

        if (!empty($this->tableConfig->where)) {
            foreach ($this->tableConfig->where as $where) {
                $builder->where($where);
            }
        }

        if ($search && !empty($this->searchable)) {
            $builder->groupStart();
            foreach ($this->searchable as $field) {
                $builder->orLike($field, $search);
            }
            $builder->groupEnd();
        }

        foreach ($filters as $field => $value) {
            if ($value !== '' && $value !== null && in_array($field, $this->filterable)) {
                $builder->where($field, $value);
            }
        }

        $orderBy = $this->tableConfig->orderBy ?? $this->tableConfig->pk;
        $builder->orderBy($orderBy, $this->tableConfig->orderDir);

        return [
            'data' => $this->findAll(),
        ];
    }

    public function getOptionsForField(string $fieldName): array
    {
        foreach ($this->tableConfig->fields as $field) {
            if ($field->name === $fieldName && $field->options) {
                return $field->options;
            }
            if ($field->name === $fieldName && $field->relationModel) {
                $modelClass = "App\\Models\\{$field->relationModel}";
                if (class_exists($modelClass)) {
                    $model = new $modelClass();
                    $valueField = $field->relationValue ?? $model->primaryKey;
                    $labelField = $field->relationField ?? 'nama';
                    
                    // Add filtering for sales users only
                    if ($modelClass === 'UserModel') {
                        $model->where('role', 'sales');
                        $model->where('is_aktif', TRUE);
                    }
                    
                    $results = $model->findAll();
                    return array_column(array_map(fn($r) => [
                        'value' => $r[$valueField],
                        'label' => $r[$labelField],
                    ], $results), 'label', 'value');
                }
            }
        }
        return [];
    }

    public function generateAutoCode(): string
    {
        $field = $this->tableConfig->autoCode;
        $prefix = $this->tableConfig->autoCodePrefix ?? strtoupper(substr($this->tableConfig->name, 0, 3));
        
        // Determine padding length based on table name - toko gets more digits
        $paddingLength = ($this->tableConfig->name === 'toko') ? 4 : 3;
        
        $last = $this->orderBy($this->tableConfig->pk, 'DESC')->first();
        $lastNum = $last ? (int)substr($last[$field], strlen($prefix)) : 0;
        $nextNum = $lastNum + 1;
        
        return $prefix . str_pad($nextNum, $paddingLength, '0', STR_PAD_LEFT);
    }
}
