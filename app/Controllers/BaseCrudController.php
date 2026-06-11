<?php

namespace App\Controllers;

use App\Config\Crud\CrudConfig;
use App\Config\Crud\TableConfig;
use App\Models\BaseCrudModel;

class BaseCrudController extends BaseController
{
    protected TableConfig $tableConfig;
    protected BaseCrudModel $model;
    protected string $viewPath;
    protected string $baseRoute;

    public function index()
    {
        $this->checkAccess();

        $search = $this->request->getGet('search');
        $filters = $this->request->getGet();

        unset($filters['search']);

        $result = $this->model->getPaginatedData($filters, $search);

        $data = [
            'title' => $this->tableConfig->title,
            'config' => $this->tableConfig,
            'records' => $result['data'],
            'search' => $search,
            'filters' => $filters,
        ];

        return $this->render($this->viewPath . '/index', $data);
    }

    public function create()
    {
        $this->checkAccess();

        $data = [
            'title' => 'Tambah ' . $this->tableConfig->title,
            'config' => $this->tableConfig,
            'record' => null,
        ];

        return $this->render($this->viewPath . '/form', $data);
    }

    public function store()
    {
        $this->checkAccess();

        $data = $this->prepareData();

        $this->runCallbacks($this->tableConfig->beforeInsert, $data);

        if (!$this->validateFormData()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->insert($data);

        $this->runCallbacks($this->tableConfig->afterInsert, $data);

        return redirect()->to($this->baseRoute)->with('success', $this->tableConfig->title . ' berhasil ditambahkan');
    }

    public function edit($id)
    {
        $this->checkAccess();

        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to($this->baseRoute)->with('error', $this->tableConfig->title . ' tidak ditemukan');
        }

        $data = [
            'title' => 'Edit ' . $this->tableConfig->title,
            'config' => $this->tableConfig,
            'record' => $record,
        ];

        return $this->render($this->viewPath . '/form', $data);
    }

    public function update($id)
    {
        $this->checkAccess();

        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to($this->baseRoute)->with('error', $this->tableConfig->title . ' tidak ditemukan');
        }

        $data = $this->prepareData($record);

        $this->runCallbacks($this->tableConfig->beforeUpdate, $data, $record);

        if (!$this->validateFormData($id)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, $data);

        $this->runCallbacks($this->tableConfig->afterUpdate, $data, $record);

        return redirect()->to($this->baseRoute)->with('success', $this->tableConfig->title . ' berhasil diupdate');
    }

    public function delete($id)
    {
        $this->checkAccess();

        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to($this->baseRoute)->with('error', $this->tableConfig->title . ' tidak ditemukan');
        }

        $this->runCallbacks($this->tableConfig->beforeDelete, $record);

        $this->model->delete($id);

        $this->runCallbacks($this->tableConfig->afterDelete, $record);

        return redirect()->to($this->baseRoute)->with('success', $this->tableConfig->title . ' berhasil dihapus');
    }

    protected function prepareData(?array $existing = null): array
    {
        $data = [];
        $fields = $this->tableConfig->getFormFieldsWithoutPk();

        foreach ($fields as $field) {
            if ($field->name === $this->tableConfig->autoCode && !$existing) {
                $data[$field->name] = $this->model->generateAutoCode();
                continue;
            }

            $value = $this->request->getPost($field->name);

            if ($field->type === 'boolean') {
                $data[$field->name] = $value ? true : false;
            } elseif ($field->type === 'number') {
                $data[$field->name] = $value !== '' ? (float)$value : null;
            } elseif ($field->type === 'integer') {
                $data[$field->name] = $value !== '' ? (int)$value : null;
            } elseif ($field->type === 'select') {
                // Handle select fields - set to null if empty
                $data[$field->name] = $value !== '' ? $value : null;
            } else {
                $data[$field->name] = $value;
            }
        }

        return $data;
    }

    protected function validateFormData(?int $id = null): bool
    {
        $rules = [];

        foreach ($this->tableConfig->getFormFieldsWithoutPk() as $field) {
            if ($field->validation) {
                $rule = $field->validation;
                if ($id && strpos($rule, 'is_unique') !== false) {
                    $rule = str_replace(',', ",{$id},", $rule);
                }
                $rules[$field->name] = $rule;
            } elseif ($field->required) {
                $rules[$field->name] = 'required';
            }
        }

        if (empty($rules)) {
            return true;
        }

        $this->validator = service('validation');
        $this->validator->setRules($rules);

        return $this->validator->run($this->request->getPost() ?? []);
    }

    protected function runCallbacks(array $callbacks, ...$args): void
    {
        foreach ($callbacks as $callback) {
            if (is_callable($callback)) {
                $callback(...$args);
            }
        }
    }

    protected function checkAccess(): void
    {
        $roles = $this->tableConfig->accessRoles;
        if ($roles && !in_array(session()->get('role'), $roles)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    protected function render(string $view, array $data = [])
    {
        return view($view, $data);
    }
}
