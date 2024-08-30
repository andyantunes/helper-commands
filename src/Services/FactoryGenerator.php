<?php

namespace AndyAntunes\HelperCommands\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class FactoryGenerator
{
    use Traits\HandleStub;

    private string $stubPath = __DIR__ . '/../../stubs/';

    private array $models = [];

    private int|null $passwordMinLength;
    private int|null $passwordMaxLength;

    private ?bool $hasForeign;

    public function __construct(public string $class, public string $modelName)
    {
        array_push($this->models, ucwords($this->modelName));
    }

    /**
     * @return void
     */
    public function generate(): void
    {
        $this->hasForeign = false;

        $this->passwordMinLength = config('helper-commands.factory_generation.params.passwordMinLength');
        $this->passwordMaxLength = config('helper-commands.factory_generation.params.passwordMaxLength');

        $factoryNamespace = "Database\\Factories";

        $fileName = $factoryNamespace . '\\' . ucwords($this->modelName) . 'Factory.php';

        // if (File::exists($fileName)) {
        //     error("The Factory file already exists");
        //     $overwrite = confirm("Do you want to overwrite the file?", default: false);
        //     if (!$overwrite) {
        //         return;
        //     }
        // }

        // TODO
        /**
         * Pegando as colunas da tabela
         * https://chatgpt.com/c/0cd9342d-0251-4e1b-b0f6-180f3eee263c
         */
        $pluralModelName = Str::plural($this->modelName);
        $tableName = Str::snake($pluralModelName, '_');

        $columns = Schema::getColumns($tableName);

        $this->removeIDColumn($columns);

        $foreignKeys = Schema::getForeignKeys($tableName);

        $definition = $this->createModelDefinition($columns, $foreignKeys);

        $definitionString = $this->convertDefinitionToString($definition);

        $modelsImport = $this->createModelsImport();

        $this->generateStubs(
            from: $this->stubPath . 'factory.stub',
            to: $fileName,
            replacements: [
                'class' => $this->modelName,
                'modelName' => $this->modelName,
                'modelsImport' => $modelsImport,
                'namespace' => $factoryNamespace,
                'definition' => $definitionString,
            ],
            directory: [
                $factoryNamespace
            ],
        );

        // TODO
        /**
         * Criar Seeders baseadas em Factories.
         * Criar uma Factory, e após iso criar uma Seeder que roda essa Factory.
         * Exemplo na Rio Capital e no ChatGTP: https://chatgpt.com/c/0cd9342d-0251-4e1b-b0f6-180f3eee263c
         */
    }

    private function removeIDColumn(array $columns): void
    {
        array_shift($columns);
    }

    private function createModelDefinition(array $columns, array $foreignKeys): array
    {
        $definition = [];

        if (!empty($foreignKeys)) {
            foreach ($foreignKeys as $key => $foreignKey) {
                $foreignTable = $foreignKey['foreign_table'];
                $model = Str::singular(ucwords(Str::camel($foreignTable)));

                array_push($this->models, $model);

                $columnDefinition = "{$model}::inRandomOrder()->first()->id";
                $definition[$foreignKey['columns'][0]] = $columnDefinition;
            }
        }

        foreach ($columns as $column) {
            $methods = config('helper-commands.factory_generation.methods');
            $customColumns = $methods['custom_columns'];

            if (isset($column['name']) && isset($customColumns[$column['name']])) {
                $definition[$column['name']] = $customColumns[$column['name']];
            } else if (isset($column['type']) && isset($methods[$column['type']])) {
                $definition[$column['name']] = $methods[$column['type']];
            }
        }

        return $definition;
    }

    private function convertDefinitionToString(array $definition): string
    {
        $currentIndex  = 1;
        $definitionString = '';
        $definitionQuantity = count($definition);

        $tabulation = "\t\t";
        $firstLineTabulation = '';
        foreach ($definition as $column => $fakerMethod) {
            $definitionString .= $currentIndex  === 1 ? $firstLineTabulation : $tabulation;

            if ($column === 'password') {
                if (!is_null($this->passwordMaxLength)) {
                    $definitionString .= "'$column' => bcrypt(fake()->$fakerMethod($this->passwordMinLength, $this->passwordMaxLength)),";
                } else {
                    $definitionString .= "'$column' => bcrypt(fake()->$fakerMethod($this->passwordMinLength)),";
                }
            } else if (Str::endsWith($column, ['_id'])) {
                $definitionString .= "'$column' => $fakerMethod,";
            } else {
                $definitionString .= "'$column' => fake()->$fakerMethod(),";
            }

            $definitionString .= $currentIndex  === $definitionQuantity ? '' : PHP_EOL . '    ';

            $currentIndex++;
        }

        return $definitionString;
    }

    private function createModelsImport(): string
    {
        sort($this->models);

        $modelsImport = '';

        foreach ($this->models as $index => $model) {
            $modelsImport .= "use App\\Models\\{$model};";
            $modelsImport .= empty($index) ? "\n" : '';
        }

        return $modelsImport;
    }
}
