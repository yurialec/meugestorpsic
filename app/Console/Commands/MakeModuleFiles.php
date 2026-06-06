<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeModuleFiles extends Command
{
    protected $signature = 'make:module {module} {name}';
    protected $description = 'Cria estrutura de arquivos para um novo módulo';

    public function handle()
    {
        $module = ucfirst($this->argument('module')); // Ex: Admin, Site, Chat
        $name = ucfirst($this->argument('name')); // Nome da funcionalidade

        $paths = [
            "app/Http/Controllers/{$module}",
            "app/Interfaces/{$module}",
            "app/Repositories/{$module}",
            "app/Services/{$module}",
            "app/Models/{$module}",
        ];

        // Criar estrutura de diretórios
        $filesystem = new Filesystem();
        foreach ($paths as $path) {
            $filesystem->ensureDirectoryExists($path);
        }

        // Criar arquivos com templates básicos
        $this->createFile("app/Interfaces/{$module}/{$name}RepositoryInterface.php", $this->getInterfaceContent($module, $name));
        $this->createFile("app/Repositories/{$module}/{$name}Repository.php", $this->getRepositoryContent($module, $name));
        $this->createFile("app/Services/{$module}/{$name}Service.php", $this->getServiceContent($module, $name));
        $this->createFile("app/Http/Controllers/{$module}/{$name}Controller.php", $this->getControllerContent($module, $name));
        $this->createFile("app/Models/{$module}/{$name}.php", $this->getModelContent($module, $name));

        $this->info("Arquivos para o módulo {$module} criados com sucesso!");
    }

    private function createFile($path, $content)
    {
        if (!file_exists($path)) {
            file_put_contents($path, $content);
        }
    }

    private function getInterfaceContent($module, $name)
    {
        return "<?php

namespace App\Interfaces\\{$module};

interface {$name}RepositoryInterface
{
    public function all();
    public function find(\$id);
    public function create(array \$data);
    public function update(\$id, array \$data);
    public function delete(\$id);
}";
    }

    private function getRepositoryContent($module, $name)
    {
        return "<?php

namespace App\Repositories\\{$module};

use App\Interfaces\\{$module}\\{$name}RepositoryInterface;
use App\Models\\{$module}\\{$name};

class {$name}Repository implements {$name}RepositoryInterface
{
    public function all()
    {
        return {$name}::all();
    }

    public function find(\$id)
    {
        return {$name}::find(\$id);
    }

    public function create(array \$data)
    {
        return {$name}::create(\$data);
    }

    public function update(\$id, array \$data)
    {
        \$model = {$name}::find(\$id);
        if (\$model) {
            \$model->update(\$data);
            return \$model;
        }
        return null;
    }

    public function delete(\$id)
    {
        return {$name}::destroy(\$id);
    }
}";
    }

    private function getServiceContent($module, $name)
    {
        return "<?php

namespace App\Services\\{$module};

use App\Repositories\\{$module}\\{$name}Repository;

class {$name}Service
{
    protected \${$name}Repository;

    public function __construct({$name}Repository \${$name}Repository)
    {
        \$this->{$name}Repository = \${$name}Repository;
    }

    public function all()
    {
        return \$this->{$name}Repository->all();
    }
}";
    }

    private function getControllerContent($module, $name)
    {
        return "<?php

namespace App\Http\Controllers\\{$module};

use App\Http\Controllers\Controller;
use App\Services\\{$module}\\{$name}Service;
use Illuminate\Http\Request;

class {$name}Controller extends Controller
{
    protected \${$name}Service;

    public function __construct({$name}Service \${$name}Service)
    {
        \$this->{$name}Service = \${$name}Service;
    }

    public function index()
    {
        return response()->json(\$this->{$name}Service->all());
    }
}";
    }

    private function getModelContent($module, $name)
    {
        return "<?php

namespace App\Models\\{$module};

use Illuminate\Database\Eloquent\Model;

class {$name} extends Model
{
    protected \$fillable = ['name'];
}";
    }
}
