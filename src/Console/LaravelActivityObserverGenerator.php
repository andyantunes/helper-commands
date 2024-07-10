<?php

namespace AndyAntunes\UserActivities\Console;

use AndyAntunes\UserActivities\Services\Generator;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class LaravelActivityObserverGenerator extends Command
{
    protected $signature = 'make:activity-observer {type?} {model?}';

    protected $description = 'Create a new Activity Observer class in any path';

    public function handle(): int
    {
        $type = 'all';
        // $type = $this->argument('type') ?? select(
        //     'Select the type of observer',
        //     [
        //         'create' => 'Create',
        //         'update' => 'Update',
        //         'delete' => 'Delete',
        //         'restore' => 'Restore',
        //         'forceDelete' => 'Force Delete',
        //         'all' => 'All',
        //     ],
        //     'all'
        // );

        // $model = $this->argument('model') ?? text('What is the name of the model?', required: false);
        // $model = $this->argument('module') ??
        //     confirm('Does this class belong to a model?', default: true, yes: 'Yes');
        // if ($model) {
        $models = $this->allModels();
        $model = select(
            label: 'What is the name of the model?',
            options: $models,
            required: true
        );
        // }

        $generator = new Generator(
            type: $type,
            class: Str::ucfirst($model),
            modelName: Str::ucfirst($model),
            modelVariable: Str::camel($model),
        );

        $generator->generate();

        $this->registerObserver(Str::ucfirst($model));

        return 1;
    }

    protected function allModels(): array
    {
        $modelList = [];
        $path = app_path() . "/Models";
        $results = scandir($path);

        foreach ($results as $result) {
            if ($result === '.' or $result === '..') continue;
            $filename = $result;

            if (is_dir($filename)) {
                $modelList = array_merge($modelList, getModels($filename));
            } else {
                $modelList[strtolower(substr($filename, 0, -4))] = substr($filename, 0, -4);
            }
        }

        return $modelList;
    }

    protected function registerObserver(string $model)
    {
        $modelFilePath = $this->getModelFilePath($model);

        if (!$modelFilePath) {
            $this->error("Model file for {$model} not found.");
            return 1;
        }

        $filesystem = new Filesystem();
        $content = $filesystem->get($modelFilePath);

        $observedByAttribute = "#[ObservedBy([{$model}Observer::class])]";
        $observedByImport = "use Illuminate\Database\Eloquent\Attributes\ObservedBy;";

        if (strpos($content, 'use Illuminate\Database\Eloquent\Attributes\ObservedBy;') === false) {
            $content = $this->addImportInAlphabeticalOrder($content, $observedByImport, $model);
        }

        if (strpos($content, '#[ObservedBy') !== false) {
            $content = preg_replace('/#[ObservedBy\(.*\)]/sU', $observedByAttribute, $content);
        } else {
            $classPosition = strpos($content, 'class ' . class_basename($model));

            if ($classPosition !== false) {
                $content = substr_replace($content, "{$observedByAttribute}\n", $classPosition, 0);
            } else {
                $this->error("Class declaration not found in {$model} file.");
                return 1;
            }
        }

        $filesystem->put($modelFilePath, $content);

        $this->info("Updated #[ObservedBy] attribute in {$model}.");

        return 0;
    }

    private function getModelFilePath(string $model): ?string
    {
        $modelFile = app_path("Models/{$model}.php");

        if (file_exists($modelFile)) {
            return $modelFile;
        }

        return null;
    }

    protected function addImportInAlphabeticalOrder(string $content, string $newImport, string $model): string
    {
        $lines = explode("\n", $content);

        $namespaceLineIndex = null;
        foreach ($lines as $index => $line) {
            if (str_starts_with(trim($line), 'namespace ')) {
                $namespaceLineIndex = $index;
                break;
            }
        }

        if ($namespaceLineIndex === null) {
            return $content;
        }

        if (trim($lines[$namespaceLineIndex + 1]) !== '') {
            array_splice($lines, $namespaceLineIndex + 1, 0, '');
        }

        // // Coleta todas as importações existentes
        $imports = [];
        $importStartIndex = $namespaceLineIndex + 2;
        for ($i = $importStartIndex; $i < count($lines); $i++) {
            if (str_starts_with(trim($lines[$i]), 'use ')) {
                $imports[] = trim($lines[$i]);
                $lines[$i] = '';
            } elseif (trim($lines[$i]) === '') {
                unset($lines[$i]); // Remove linhas em branco do array
            } else {
                break;
            }
        }

        // Adiciona as novas importações à lista e ordena alfabeticamente
        $imports[] = "use App\Observers\\{$model}Observer;";
        $imports[] = $newImport;
        $imports = array_unique($imports);
        sort($imports);

        // Insere as importações ordenadas após o namespace e a linha em branco
        array_splice($lines, $namespaceLineIndex + 2, 0, $imports);

        $filteredLines = [];
        foreach ($lines as $key => $line) {
            if (!($line === '' && (isset($lines[$key - 1]) && $lines[$key - 1] === ''))) {
                $filteredLines[] = $line;
            }
        }

        // Junta as linhas de volta em uma string
        return implode("\n", $filteredLines);
    }
}
