<?php

namespace App\Commands;

use App\Config\Crud\CrudConfig;
use App\Config\Crud\TableConfig;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\CLI\GeneratorTrait;

class CrudGenerator extends BaseCommand
{
    use GeneratorTrait;

    protected $group = 'CodeIgniter';
    protected $name = 'crud:generate';
    protected $description = 'Generate CRUD files from config';
    protected $usage = 'crud:generate <table> [options]';
    protected $arguments = [
        'table' => 'Table config name to generate (e.g., suppliers, products)',
    ];
    protected $options = [
        '--force' => 'Force overwrite existing files',
    ];

    public function run(array $params)
    {
        $tableName = array_shift($params);
        if (!$tableName) {
            $this->showTables();
            return;
        }

        $config = $this->getConfig($tableName);
        if (!$config) {
            CLI::error("Config for table '{$tableName}' not found.");
            CLI::newLine();
            CLI::write('Available configs:');
            $this->showTables();
            return;
        }

        $force = in_array('--force', $params);

        $this->generateModel($config, $force);
        $this->generateController($config, $force);
        $this->generateViews($config, $force);
        $this->updateRoutes($config, $force);
        $this->updateSidebar($config, $force);

        CLI::newLine();
        CLI::write('CRUD for ' . $config->title . ' generated successfully!', 'green');
    }

    protected function showTables()
    {
        $config = new CrudConfig();
        $tables = $config->getAllTables();
        foreach (array_keys($tables) as $name) {
            CLI::write(" - {$name}");
        }
    }

    protected function getConfig(string $name): ?TableConfig
    {
        $config = new CrudConfig();
        return $config->getTable($name);
    }

    protected function generateModel(TableConfig $config, bool $force)
    {
        $modelPath = APPPATH . "Models/{$config->model}.php";
        if (file_exists($modelPath) && !$force) {
            CLI::write("Model {$config->model}.php exists, skipping...", 'yellow');
            return;
        }

        $allowedFields = $this->getAllowedFields($config);
        $softDeletes = $config->softDeletes ? 'true' : 'false';

        $content = '<?php

namespace App\Models;

use App\Config\Crud\CrudConfig;
use App\Models\BaseCrudModel;

class ' . $config->model . ' extends BaseCrudModel
{
    protected $table = \'' . $config->name . '\';
    protected $primaryKey = \'' . $config->pk . '\';
    protected $useAutoIncrement = true;
    protected $returnType = \'array\';
    protected $useSoftDeletes = ' . $softDeletes . ';
    protected $protectFields = true;
    protected $allowedFields = [' . $allowedFields . '];

    protected $useTimestamps = true;
    protected $createdField = \'created_at\';
    protected $updatedField = \'updated_at\';

    public function __construct()
    {
        parent::__construct();
        $config = (new CrudConfig())->getTable($this->table);
        $this->setTableConfig($config);
    }
}
';

        $this->writeFile($modelPath, $content);
        CLI::write("Model created: {$config->model}.php", 'green');
    }

    protected function generateController(TableConfig $config, bool $force)
    {
        $controllerPath = APPPATH . "Controllers/{$config->controller}.php";
        if (file_exists($controllerPath) && !$force) {
            CLI::write("Controller {$config->controller}.php exists, skipping...", 'yellow');
            return;
        }

        $content = '<?php

namespace App\Controllers;

use App\Config\Crud\CrudConfig;

class ' . $config->controller . ' extends BaseCrudController
{
    public function __construct()
    {
        $config = (new CrudConfig())->getTable(\'' . $config->name . '\');
        $modelClass = "App\\Models\\{$config->model}";
        $this->model = new $modelClass();
        $this->tableConfig = $config;
        $this->viewPath = \'' . strtolower($config->controller) . '\';
        $this->baseRoute = \'' . $config->route . '\';
    }
}
';

        $this->writeFile($controllerPath, $content);
        CLI::write("Controller created: {$config->controller}.php", 'green');
    }

    protected function generateViews(TableConfig $config, bool $force)
    {
        $viewDir = APPPATH . "Views/" . strtolower($config->controller);
        if (!is_dir($viewDir)) {
            mkdir($viewDir, 0755, true);
        }

        $this->generateIndexView($config, $viewDir, $force);
        $this->generateFormView($config, $viewDir, $force);

        CLI::write("Views created in: Views/" . strtolower($config->controller) . "/", 'green');
    }

    protected function generateIndexView(TableConfig $config, string $viewDir, bool $force)
    {
        $viewPath = "{$viewDir}/index.php";
        if (file_exists($viewPath) && !$force) {
            CLI::write("View index.php exists, skipping...", 'yellow');
            return;
        }

        $tableHeaders = $this->generateTableHeaders($config);
        $tableCells = $this->generateTableCells($config);
        $route = $config->route;
        $title = $config->title;
        $pk = $config->pk;

        $content = <<<EOT
<?= \$this->extend('layouts/app') ?>

<?= \$this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data {$title}</h1>
        <a href="<?= base_url('{$route}/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah {$title}
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <form method="GET" class="form-inline">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari..." value="<?= \$search ?? '' ?>">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
{$tableHeaders}
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (\$records as \$i => \$record): ?>
                        <tr>
{$tableCells}
                            <td>
                                <a href="<?= base_url('{$route}/edit/' . \$record['{$pk}']) ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?= base_url('{$route}/delete/' . \$record['{$pk}']) ?>" method="POST" style="display:inline" onsubmit="return confirm('Yakin hapus?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty(\$records)): ?>
                        <tr>
                            <td colspan="99" class="text-center">Tidak ada data</td>
                        </tr>
                        <?php endif; ?>
</tbody>
                </table>
            </div>
            <div class="mt-3">
                <?= \$pager->links('default', 'bootstrap_pagination') ?>
            </div>
        </div>
    </div>
</div>
<?= \$this->endSection() ?>
EOT;

        $this->writeFile($viewPath, $content);
    }

    protected function generateFormView(TableConfig $config, string $viewDir, bool $force)
    {
        $viewPath = "{$viewDir}/form.php";
        if (file_exists($viewPath) && !$force) {
            CLI::write("View form.php exists, skipping...", 'yellow');
            return;
        }

        $formFields = $this->generateFormFields($config);
        $pk = $config->pk;

        $content = <<<EOT
<?= \$this->extend('layouts/app') ?>

<?= \$this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= \$title ?></h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as \$error): ?>
                            <li><?= \$error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= base_url(\$config->route . (\$record ? '/update' : '/store') . (\$record ? '/' . \$record['{$pk}'] : '')) ?>" method="POST">
                <?= csrf_field() ?>

{$formFields}
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?= base_url(\$config->route) ?>" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
<?= \$this->endSection() ?>
EOT;

        $this->writeFile($viewPath, $content);
    }

    protected function generateTableHeaders(TableConfig $config): string
    {
        $headers = "                            <th>No</th>\n";
        foreach ($config->getTableFields() as $field) {
            if ($field->name === $config->pk) {
                continue;
            }
            $headers .= "                            <th>{$field->label}</th>\n";
        }
        $headers .= "                            <th>Aksi</th>";
        return $headers;
    }

    protected function generateTableCells(TableConfig $config): string
    {
        $cells = " <td><?= \$i + 1 ?></td>\n";
        foreach ($config->getTableFields() as $field) {
            if ($field->name === $config->pk) {
                continue;
            }
            if ($field->type === 'boolean') {
                $cells .= "                                <td><?= \$record['{$field->name}'] ? '<span class=\"badge badge-success\">Aktif</span>' : '<span class=\"badge badge-secondary\">Nonaktif</span>' ?></td>\n";
            } else {
                $cells .= "                                <td><?= esc(\$record['{$field->name}']) ?></td>\n";
            }
        }
        return $cells;
    }

    protected function generateFormFields(TableConfig $config): string
    {
        $fields = '';
        foreach ($config->getFormFieldsWithoutPk() as $field) {
            $value = '<?= old(\'' . $field->name . '\', $record[\'' . $field->name . '\'] ?? \'\') ?>';
            $required = $field->required ? 'required' : '';

            switch ($field->type) {
                case 'textarea':
                    $fields .= "                <div class=\"form-group\">\n";
                    $fields .= "                    <label for=\"{$field->name}\">{$field->label}</label>\n";
                    $fields .= "                    <textarea class=\"form-control\" id=\"{$field->name}\" name=\"{$field->name}\" rows=\"3\" placeholder=\"{$field->placeholder}\" {$required}>{$value}</textarea>\n";
                    $fields .= "                </div>\n\n";
                    break;
                case 'select':
                    $options = $this->generateSelectOptions($field);
                    $fields .= "                <div class=\"form-group\">\n";
                    $fields .= "                    <label for=\"{$field->name}\">{$field->label}</label>\n";
                    $fields .= "                    <select class=\"form-control\" id=\"{$field->name}\" name=\"{$field->name}\" {$required}>\n";
                    $fields .= "                        <option value=\"\">-- Pilih --</option>\n";
                    $fields .= "                        {$options}";
                    $fields .= "                    </select>\n";
                    $fields .= "                </div>\n\n";
                    break;
                case 'boolean':
                    $checked = '<?= old(\'' . $field->name . '\', $record[\'' . $field->name . '\'] ?? true) ? \'checked\' : \'\' ?>';
                    $fields .= "                <div class=\"form-group\">\n";
                    $fields .= "                    <div class=\"form-check\">\n";
                    $fields .= "                        <input type=\"checkbox\" class=\"form-check-input\" id=\"{$field->name}\" name=\"{$field->name}\" value=\"1\" {$checked}>\n";
                    $fields .= "                        <label class=\"form-check-label\" for=\"{$field->name}\">{$field->label}</label>\n";
                    $fields .= "                    </div>\n";
                    $fields .= "                </div>\n\n";
                    break;
                default:
                    $fields .= "                <div class=\"form-group\">\n";
                    $fields .= "                    <label for=\"{$field->name}\">{$field->label}</label>\n";
                    $fields .= "                    <input type=\"{$field->type}\" class=\"form-control\" id=\"{$field->name}\" name=\"{$field->name}\" value=\"{$value}\" placeholder=\"{$field->placeholder}\" {$required}>\n";
                    $fields .= "                </div>\n\n";
            }
        }
        return rtrim($fields, "\n");
    }

    protected function generateSelectOptions($field): string
    {
        if ($field->options) {
            // Handle hardcoded options
            $options = '';
            foreach ($field->options as $value => $label) {
                $options .= " <option value=\"{$value}\">{$label}</option>\n";
            }
            return rtrim($options, "\n");
        } elseif ($field->relationModel) {
            // Handle relation data - generate PHP code to fetch options
            $modelClass = "App\\Models\\{$field->relationModel}";
            $valueField = $field->relationValue ?? 'id';
            $labelField = $field->relationField ?? 'nama';
            
            // Add filtering for sales users if it's UserModel
            $filterCode = '';
            if ($modelClass === 'UserModel') {
                $filterCode = "->where('role', 'sales')->where('is_aktif', TRUE)";
            }
            
            return "<?= array_column(array_map(fn(\$r) => ['value' => \$r['{$valueField}'], 'label' => \$r['{$labelField}']], (new {$modelClass}()){$filterCode}->findAll()), 'label', 'value') ?>";
        }
        return '';
    }

    protected function getAllowedFields(TableConfig $config): string
    {
        $fields = [];
        foreach ($config->fields as $field) {
            if ($field->showInForm) {
                $fields[] = "'{$field->name}'";
            }
        }
        return implode(', ', $fields);
    }

    protected function updateRoutes(TableConfig $config, bool $force)
    {
        $routeFile = APPPATH . 'Config/Routes.php';
        $content = file_get_contents($routeFile);

        $routes = "

// {$config->title} CRUD
\$routes->get('{$config->route}', '{$config->controller}::index');
\$routes->get('{$config->route}/create', '{$config->controller}::create');
\$routes->post('{$config->route}/store', '{$config->controller}::store');
\$routes->get('{$config->route}/edit/(:num)', '{$config->controller}::edit/\$1');
\$routes->post('{$config->route}/update/(:num)', '{$config->controller}::update/\$1');
\$routes->post('{$config->route}/delete/(:num)', '{$config->controller}::delete/\$1');
";

        if (strpos($content, "'{$config->controller}::index'") === false) {
            file_put_contents($routeFile, $content . $routes);
            CLI::write("Routes updated", 'green');
        } else {
            CLI::write("Routes already exist, skipping...", 'yellow');
        }
    }

    protected function updateSidebar(TableConfig $config, bool $force)
    {
        $sidebarFile = APPPATH . 'Views/layouts/app.php';
        $content = file_get_contents($sidebarFile);

        $icon = $this->getIconForTable($config->name);
        $menuItem = "

 <a class=\"nav-link\" href=\"<?= base_url('{$config->route}') ?>\">
                            <div class=\"sb-nav-link-icon\"><i class=\"{$icon}\"></i></div>
                            {$config->title}
                        </a>
";

        $searchPattern = "<?php if (\$role === 'admin'): ?>";
        $insertPos = strpos($content, $searchPattern) + strlen($searchPattern);

        if ($insertPos && strpos($content, $config->route) === false) {
            $newContent = substr($content, 0, $insertPos) . $menuItem . substr($content, $insertPos);
            file_put_contents($sidebarFile, $newContent);
            CLI::write("Sidebar updated", 'green');
        } else {
            CLI::write("Sidebar already has this menu, skipping...", 'yellow');
        }
    }

    protected function getIconForTable(string $tableName): string
    {
        $icons = [
            'suppliers' => 'fas fa-truck',
            'products' => 'fas fa-box',
            'users' => 'fas fa-users',
            'categories' => 'fas fa-tags',
            'sales' => 'fas fa-shopping-cart',
        ];
        return $icons[$tableName] ?? 'fas fa-database';
    }

    protected function writeFile(string $path, string $content)
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($path, $content);
    }
}
