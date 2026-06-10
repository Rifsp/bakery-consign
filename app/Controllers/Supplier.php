<?php

namespace App\Controllers;

use App\Config\Crud\CrudConfig;
use App\Models\BaseCrudModel;

class Supplier extends BaseCrudController
{
    public function __construct()
    {
        $config = (new CrudConfig())->getTable('suppliers');
        $modelClass = "App\\Models\\{$config->model}";
        $this->model = new $modelClass();
        $this->model->setTableConfig($config);
        $this->tableConfig = $config;
        $this->viewPath = 'supplier';
        $this->baseRoute = '/supplier';
    }
}
