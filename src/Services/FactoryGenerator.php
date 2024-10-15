<?php

namespace AndyAntunes\HelperCommands\Services;

use Illuminate\Support\Facades\File;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;

class FactoryGenerator
{
    // TODO
    /**
     * 2. Criar forma para usuário poder escolher se quer ou não com eventos na criação, default `yes`
     */

    use Traits\GenerateFactory;
    use Traits\GenerateSeeder;
    use Traits\HandleStub;

    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    public function __construct(
        public string $class,
        public string $modelName,
        public int $recordsQuantity,
        public bool $withEvents,
    ) {
        $this->initializeTrait();
    }

    /**
     * @return void
     */
    public function generate(): void
    {
        if (File::exists('database/factories/' . $this->modelName . 'Factory.php')) {
            error("The Factory file already exists");
            $overwrite = confirm("Do you want to overwrite the file?", default: false);
            if (!$overwrite) {
                return;
            }
        }

        $tableName = $this->getTableName();

        $columns = $this->getDatabaseColumns($tableName);

        $foreignKeys = $this->getForeignKeys($tableName);

        $definition = $this->createModelDefinition($columns, $foreignKeys);

        $this->generateStubs(
            from: $this->stubPath . 'factory.stub',
            to: $this->generateFactoryFileName(),
            replacements: [
                'class' => $this->modelName,
                'modelName' => $this->modelName,
                'modelsImport' => $this->createAllModelsImport(),
                'namespace' => $this->factoryNamespace,
                'definition' => $this->convertDefinitionToString($definition),
            ],
            directory: [
                $this->factoryNamespace
            ],
        );

        if ($this->factorySeederExists()) {
            $seederPath = $this->generateSeederFileName();
            list($imports, $methodContent) = $this->getExistingSeederContent($seederPath);

            $newRecordCreation = $this->createRecordCreation($this->modelName, $this->recordsQuantity, $this->withEvents);
            $newModelImport = $this->createModelImport();

            $mergedImports = $this->mergeSeederImports($imports, $newModelImport);
            $mergedRecordCreation = $this->mergeSeederRecordCreation($methodContent, $newRecordCreation);
        }

        $this->generateStubs(
            from: $this->stubPath . 'seeder.stub',
            to: $this->generateSeederFileName(),
            replacements: [
                'class' => $this->modelName,
                'modelsImport' => $mergedImports ?? $this->createModelImport(),
                'recordCreation' => $mergedRecordCreation ?? $this->createRecordCreation($this->modelName, $this->recordsQuantity, $this->withEvents),
                'modelName' => $this->modelName,
                'namespace' => $this->seederNamespace,
            ],
            directory: [
                $this->seederNamespace
            ],
        );
    }
}
