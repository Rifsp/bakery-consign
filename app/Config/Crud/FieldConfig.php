<?php

namespace App\Config\Crud;

class FieldConfig
{
    public string $name;
    public string $label;
    public string $type = 'text';
    public bool $showInTable = true;
    public bool $showInForm = true;
    public bool $required = false;
    public ?string $validation = null;
    public ?array $options = null;
    public ?string $relationModel = null;
    public ?string $relationField = null;
    public ?string $relationValue = null;
    public ?string $placeholder = null;
    public ?int $maxLength = null;

    public function __construct(array $config)
    {
        $this->name = $config['name'];
        $this->label = $config['label'] ?? $config['name'];
        $this->type = $config['type'] ?? 'text';
        $this->showInTable = $config['showInTable'] ?? true;
        $this->showInForm = $config['showInForm'] ?? true;
        $this->required = $config['required'] ?? false;
        $this->validation = $config['validation'] ?? null;
        $this->options = $config['options'] ?? null;
        $this->relationModel = $config['relationModel'] ?? null;
        $this->relationField = $config['relationField'] ?? null;
        $this->relationValue = $config['relationValue'] ?? null;
        $this->placeholder = $config['placeholder'] ?? null;
        $this->maxLength = $config['maxLength'] ?? null;
    }
}
