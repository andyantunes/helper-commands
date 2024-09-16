<?php

namespace AndyAntunes\HelperCommands\Services\Traits;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Trait responsible to generate the Factory.
 */
trait GenerateFactory
{
    private string $factoryNamespace = "Database\\Factories";

    private array $models = [];

    private int|null $passwordMinLength;
    private int|null $passwordMaxLength;

    private function initializeTrait(): void
    {
        array_push($this->models, ucwords($this->modelName));

        $this->passwordMinLength = config('helper-commands.factory_generation.params.passwordMinLength');
        $this->passwordMaxLength = config('helper-commands.factory_generation.params.passwordMaxLength');
    }

    private function generateFactoryFileName(): string
    {
        return $this->factoryNamespace . '\\' . ucwords($this->modelName) . 'Factory.php';
    }

    private function getTableName(): string
    {
        $pluralModelName = Str::plural($this->modelName);
        return Str::snake($pluralModelName, '_');
    }

    private function getDatabaseColumns(string $tableName): array
    {
        $columns = Schema::getColumns($tableName);
        array_shift($columns);
        return $columns;
    }

    private function getForeignKeys(string $tableName): array
    {
        return Schema::getForeignKeys($tableName);
    }

    private function createModelDefinition(array $columns, array $foreignKeys): array
    {
        $definition = [];

        if (!empty($foreignKeys)) {
            foreach ($foreignKeys as $key => $foreignKey) {
                $model = $this->getModelNameFromFK($foreignKey);

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
                $definitionString .= $this->getPasswordColumnDefinition($column, $fakerMethod);
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

    private function createModelImport(): string
    {
        return "use App\\Models\\{$this->modelName};";
    }

    private function getModelNameFromFK(array $foreignKey): string
    {
        $foreignTable = $foreignKey['foreign_table'];
        return Str::singular(ucwords(Str::camel($foreignTable)));
    }

    private function getPasswordColumnDefinition(string $column, string $fakerMethod): string
    {
        if (is_null($this->passwordMaxLength)) {
            return "'$column' => bcrypt(fake()->$fakerMethod($this->passwordMinLength)),";
        } else {
            return "'$column' => bcrypt(fake()->$fakerMethod($this->passwordMinLength, $this->passwordMaxLength)),";
        }
    }
}
