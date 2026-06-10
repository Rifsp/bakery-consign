<?php

namespace App\Config\Crud;

class TableConfig
{
    public string $name;
    public string $title;
    public string $route;
    public string $model;
    public string $controller;
    public ?string $pk = 'id';
    public ?string $autoCode = null;
    public ?string $autoCodePrefix = null;
    public ?string $orderBy = null;
    public string $orderDir = 'ASC';
    public int $perPage = 10;
    public bool $softDeletes = false;
    public array $fields = [];
    public array $joins = [];
    public array $where = [];
    public array $callbackTable = [];
    public array $callbackForm = [];
    public array $beforeInsert = [];
    public array $beforeUpdate = [];
    public array $afterInsert = [];
    public array $afterUpdate = [];
    public array $beforeDelete = [];
    public array $afterDelete = [];
    public ?array $accessRoles = null;

    public function __construct(array $config)
    {
        $this->name = $config['name'];
        $this->title = $config['title'] ?? $config['name'];
        $this->route = $config['route'];
        $this->model = $config['model'];
        $this->controller = $config['controller'];
        $this->pk = $config['pk'] ?? 'id';
        $this->autoCode = $config['autoCode'] ?? null;
        $this->autoCodePrefix = $config['autoCodePrefix'] ?? null;
        $this->orderBy = $config['orderBy'] ?? null;
        $this->orderDir = $config['orderDir'] ?? 'ASC';
        $this->perPage = $config['perPage'] ?? 10;
        $this->softDeletes = $config['softDeletes'] ?? false;
        $this->fields = $this->parseFields($config['fields'] ?? []);
        $this->joins = $config['joins'] ?? [];
        $this->where = $config['where'] ?? [];
        $this->callbackTable = $config['callbackTable'] ?? [];
        $this->callbackForm = $config['callbackForm'] ?? [];
        $this->beforeInsert = $config['beforeInsert'] ?? [];
        $this->beforeUpdate = $config['beforeUpdate'] ?? [];
        $this->afterInsert = $config['afterInsert'] ?? [];
        $this->afterUpdate = $config['afterUpdate'] ?? [];
        $this->beforeDelete = $config['beforeDelete'] ?? [];
        $this->afterDelete = $config['afterDelete'] ?? [];
        $this->accessRoles = $config['accessRoles'] ?? null;
    }

    protected function parseFields(array $fields): array
    {
        return array_map(fn($f) => new FieldConfig($f), $fields);
    }

    public function getTableFields(): array
    {
        return array_filter($this->fields, fn($f) => $f->showInTable);
    }

    public function getFormFields(): array
    {
        return array_filter($this->fields, fn($f) => $f->showInForm);
    }

    public function getFormFieldsWithoutPk(): array
    {
        return array_filter($this->getFormFields(), fn($f) => $f->name !== $this->pk);
    }
}
