<?php

namespace AndyAntunes\HelperCommands\Services\Traits;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

/**
 * Trait responsible to generate the Seeder.
 */
trait GenerateSeeder
{
    private string $seederNamespace = "Database\\Seeders";

    private function generateSeederFileName(): string
    {
        return $this->seederNamespace . "\\FactorySeeder.php";
    }

    private function createRecordCreation(string $modelName, int $recordsQuantity, bool $withoutEvents): string
    {
        $pluralModelName = Str::plural($modelName);
        $warnModelName = $this->getWarnModelName($pluralModelName);

        return $withoutEvents
            ? $this->recordCreationWithoutEvents($modelName, $warnModelName, $pluralModelName, $recordsQuantity)
            : $this->recordCreationWithEvents($modelName, $warnModelName, $pluralModelName, $recordsQuantity);
    }

    private function recordCreationWithoutEvents(string $modelName, string $warnModelName, string $pluralModelName, int $recordsQuantity): string
    {
        return "{$modelName}::withoutEvents(function () {
            \$this->command->warn(PHP_EOL . 'Creating {$warnModelName}...');

            \$this->withProgressBar({$recordsQuantity}, fn () => {$modelName}::factory(1)->create());

            \$this->command->info('{$pluralModelName} created.');
        });";
    }

    private function recordCreationWithEvents(string $modelName, string $warnModelName, string $pluralModelName, int $recordsQuantity): string
    {
        return "\$this->command->warn(PHP_EOL . 'Creating {$warnModelName}...');

        \$this->withProgressBar({$recordsQuantity}, fn () => {$modelName}::factory(1)->create());

        \$this->command->info('{$pluralModelName} created.');";
    }

    private function factorySeederExists(): bool
    {
        $filesystem = new Filesystem();
        $path = $this->generateSeederFileName();

        if ($filesystem->exists($path)) {
            return true;
        }

        return false;
    }

    /**
     * Retrieves the existing content of the FactorySeeder file.
     *
     * This function reads the content of the specified file, extracts the import statements and the content of the `run` method,
     * and returns them as an array.
     *
     * @param string $path The path to the FactorySeeder file.
     *
     * @return array An array containing two elements:
     *               - The first element is an array of import statements found in the file.
     *               - The second element is the content of the `run` method found in the file.
     */
    private function getExistingSeederContent(string $path): array
    {
        $filesystem = new Filesystem();

        $content = $filesystem->get($path);
        $lines = explode("\n", $content);

        $contents = [
            'in_method' => false,
            'imports' => [],
            'method_signature' => '',
            'method_content' => '',
        ];

        foreach ($lines as $line) {
            if (Str::startsWith($line, 'use')) {
                $contents['imports'][] = $line;
                continue;
            }

            if (Str::contains($line, 'run()')) {
                $contents['method_signature'] = $line;
                $contents['in_method'] = true;
                continue;
            }

            if ($contents['in_method']) {
                if (Str::endsWith($line, '}')) {
                    $contents['method_content'] .= "\n\n";
                    $contents['in_method'] = false;
                    continue;
                }

                if (trim($line) !== '{') {
                    if (isset($contents['method_content'])) {
                        $contents['method_content'] .= "\n$line";
                    } else {
                        $contents['method_content'] = trim($line);
                    }
                }
            }
        }

        return [
            $this->removeBaseImports($contents['imports']),
            $contents['method_content'],
        ];
    }

    private function removeBaseImports(array $imports): array
    {
        if (($key = array_search('use AndyAntunes\HelperCommands\Services\Traits;', $imports)) !== false) {
            unset($imports[$key]);
        }

        if (($key = array_search('use Illuminate\Database\Seeder;', $imports)) !== false) {
            unset($imports[$key]);
        }

        return $imports;
    }

    /**
     * Merges the existing imports with the new import and returns the updated list of imports.
     *
     * This function takes an array of existing imports and a new import as input,
     * and returns an array of imports with the new import merged in alphabetical order.
     *
     * @param array $existingImports An array of existing import statements.
     * @param string $newImport The new import statement to be added.
     *
     * @return string A string containing the updated list of imports, with the new import merged in alphabetical order.
     */
    private function mergeSeederImports(array $existingImports, string $newImport): string
    {
        array_push($existingImports, $newImport);
        sort($existingImports);

        return implode("\n", $existingImports);
    }

    private function mergeSeederRecordCreation(string $existingContent, string $newContent): string
    {
        $existingContent .= "\t\t$newContent";

        return $existingContent;
    }

    private function getWarnModelName(string $pluralModelName): string
    {
        $modelNameSnake = Str::snake($pluralModelName);

        return Str::replace('_', ' ', $modelNameSnake);
    }

    private function sortImportsAlphabetically(array $imports): string
    {
        $namespaceLineIndex = null;
        foreach ($imports as $index => $import) {
            if (str_starts_with(trim($import), 'namespace ')) {
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

        $imports = array_unique($imports);
        sort($imports);

        array_splice($lines, $namespaceLineIndex + 2, 0, $imports);

        $filteredLines = [];
        foreach ($lines as $key => $line) {
            if (!($line === '' && (isset($lines[$key - 1]) && $lines[$key - 1] === ''))) {
                $filteredLines[] = $line;
            }
        }

        return implode("\n", $filteredLines);
    }
}
