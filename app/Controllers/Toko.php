<?php

namespace App\Controllers;

use App\Config\Crud\CrudConfig;
use App\Models\BaseCrudModel;

class Toko extends BaseCrudController
{
    public function __construct()
    {
        $config = (new CrudConfig())->getTable('toko');
        $modelClass = "App\\Models\\{$config->model}";
        $this->model = new $modelClass();
        $this->model->setTableConfig($config);
        $this->tableConfig = $config;
        $this->viewPath = 'toko';
        $this->baseRoute = '/toko';
    }

    public function index()
    {
        $this->checkAccess();

        $search = $this->request->getGet('search');
        $filters = $this->request->getGet();

        unset($filters['search']);

        $result = $this->model->getPaginatedData($filters, $search);

        $salesIds = array_unique(array_filter(array_column($result['data'], 'sales_id')));
        $salesList = [];
        if (!empty($salesIds)) {
            $salesUsers = model('App\Models\UserModel')->whereIn('id', $salesIds)->findAll();
            foreach ($salesUsers as $s) {
                $salesList[$s['id']] = $s['nama'];
            }
        }

        $data = [
            'title' => $this->tableConfig->title,
            'config' => $this->tableConfig,
            'records' => $result['data'],
            'salesList' => $salesList,
            'search' => $search,
            'filters' => $filters,
        ];

        return $this->render($this->viewPath . '/index', $data);
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

