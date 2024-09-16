<?php

namespace AndyAntunes\HelperCommands\Services\Traits;

use Illuminate\Support\Facades\File;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;

trait GenerateObserver
{
    private function all(): void
    {
        $observerNamespace = "App\\Observers";

        $fileName = $observerNamespace . '\\' . ucwords($this->modelName) . 'Observer.php';

        if (File::exists($fileName)) {
            error("The Observer file already exists");
            $overwrite = confirm("Do you want to overwrite the file?", default: false);
            if (!$overwrite) {
                return;
            }
        }

        $this->generateStubs(
            from: $this->stubPath . 'observer.stub',
            to: $fileName,
            replacements: [
                'class' => $this->modelName,
                'modelName' => $this->modelName,
                'namespace' => $observerNamespace,
                'modelVariable' => $this->modelVariable
            ],
            directory: [
                $observerNamespace
            ],
        );
    }
}
