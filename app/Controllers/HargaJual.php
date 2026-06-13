<?php

namespace App\Controllers;

use App\Config\Crud\CrudConfig;
use App\Models\ProdukModel;

class HargaJual extends BaseCrudController
{
    public function __construct()
    {
        $config = (new CrudConfig())->getTable('harga_jual');
        $modelClass = "App\\Models\\{$config->model}";
        $this->model = new $modelClass();
        $this->model->setTableConfig($config);
        $this->tableConfig = $config;
        $this->viewPath = 'harga_jual';
        $this->baseRoute = '/harga-jual';
    }

    public function index()
    {
        $this->checkAccess();

        $produkId = $this->request->getGet('produk_id');
        if (!$produkId) {
            return redirect()->to('/produk')->with('error', 'Pilih produk terlebih dahulu');
        }

        $produkModel = new ProdukModel();
        $produk = $produkModel->find($produkId);
        if (!$produk) {
            return redirect()->to('/produk')->with('error', 'Produk tidak ditemukan');
        }

        $search = $this->request->getGet('search');
        $filters = ['produk_id' => $produkId];

        $this->model->where($filters);
        if ($search) {
            $this->model->groupStart()
                ->like('nama_harga', $search)
                ->groupEnd();
        }
        $records = $this->model->findAll();

        $data = [
            'title' => 'Harga Jual: ' . $produk['nama'],
            'config' => $this->tableConfig,
            'records' => $records,
            'search' => $search,
            'produk' => $produk,
        ];

        return $this->render($this->viewPath . '/index', $data);
    }

    public function create()
    {
        $this->checkAccess();

        $produkId = $this->request->getGet('produk_id');
        if (!$produkId) {
            return redirect()->to('/produk')->with('error', 'Pilih produk terlebih dahulu');
        }

        $produkModel = new ProdukModel();
        $produk = $produkModel->find($produkId);
        if (!$produk) {
            return redirect()->to('/produk')->with('error', 'Produk tidak ditemukan');
        }

        $data = [
            'title' => 'Tambah Harga Jual - ' . $produk['nama'],
            'config' => $this->tableConfig,
            'record' => null,
            'produk' => $produk,
        ];

        return $this->render($this->viewPath . '/form', $data);
    }

    public function store()
    {
        $this->checkAccess();

        $data = $this->prepareData();

        if (!$this->validateFormData()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->insert($data);

        $produkId = $data['produk_id'] ?? $this->request->getPost('produk_id');
        return redirect()->to($this->baseRoute . '?produk_id=' . $produkId)
            ->with('success', 'Harga jual berhasil ditambahkan');
    }

    public function edit($id)
    {
        $this->checkAccess();

        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to('/produk')->with('error', 'Harga jual tidak ditemukan');
        }

        $produkModel = new ProdukModel();
        $produk = $produkModel->find($record['produk_id']);

        $data = [
            'title' => 'Edit Harga Jual',
            'config' => $this->tableConfig,
            'record' => $record,
            'produk' => $produk,
        ];

        return $this->render($this->viewPath . '/form', $data);
    }

    public function update($id)
    {
        $this->checkAccess();

        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to('/produk')->with('error', 'Harga jual tidak ditemukan');
        }

        $data = $this->prepareData($record);

        if (!$this->validateFormData($id)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, $data);

        $produkId = $data['produk_id'] ?? $record['produk_id'];
        return redirect()->to($this->baseRoute . '?produk_id=' . $produkId)
            ->with('success', 'Harga jual berhasil diupdate');
    }

    public function delete($id)
    {
        $this->checkAccess();

        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to('/produk')->with('error', 'Harga jual tidak ditemukan');
        }

        $produkId = $record['produk_id'];
        $this->model->update($id, ['is_aktif' => !$record['is_aktif']]);
        $status = $record['is_aktif'] ? 'dinonaktifkan' : 'diaktifkan';

        return redirect()->to($this->baseRoute . '?produk_id=' . $produkId)
            ->with('success', 'Harga jual berhasil ' . $status);
    }
}
