<?php

namespace App\Config\Crud;

use CodeIgniter\Config\BaseConfig;

class CrudConfig extends BaseConfig
{
    public array $tables = [];

    public function __construct()
    {
        parent::__construct();
        $this->loadConfigs();
    }

    protected function loadConfigs(): void
    {
        $configFiles = glob(APPPATH . 'Config/Crud/*.php');
        foreach ($configFiles as $file) {
            $className = pathinfo($file, PATHINFO_FILENAME);
            if ($className === 'CrudConfig' || $className === 'FieldConfig' || $className === 'TableConfig') {
                continue;
            }
            $config = include $file;
            if ($config instanceof TableConfig) {
                $this->tables[$config->name] = $config;
            }
        }
    }

    public function getTable(string $name): ?TableConfig
    {
        return $this->tables[$name] ?? null;
    }

    public function getAllTables(): array
    {
        return $this->tables;
    }
}
