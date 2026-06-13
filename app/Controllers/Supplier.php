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

    public function delete($id)
    {
        $this->checkAccess();

        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to($this->baseRoute)->with('error', $this->tableConfig->title . ' tidak ditemukan');
        }

        $this->model->update($id, ['is_aktif' => !$record['is_aktif']]);
        $status = $record['is_aktif'] ? 'dinonaktifkan' : 'diaktifkan';
        return redirect()->to($this->baseRoute)->with('success', $this->tableConfig->title . ' berhasil ' . $status);
    }
}
