<?php

namespace App\Controllers;

use App\Config\Crud\CrudConfig;

class Produk extends BaseCrudController
{
    public function __construct()
    {
        $config = (new CrudConfig())->getTable('produk');
        $modelClass = "App\\Models\\{$config->model}";
        $this->model = new $modelClass();
        $this->model->setTableConfig($config);
        $this->tableConfig = $config;
        $this->viewPath = 'produk';
        $this->baseRoute = '/produk';
    }
}
