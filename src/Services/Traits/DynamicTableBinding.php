<?php

namespace AndyAntunes\HelperCommands\Services\Traits;

trait DynamicTableBinding
{
    private ?string $tableName;
    private ?array $configColumnNames;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->tableName = config('helper-commands.log_activities.table_names.log_activity');
        $this->configColumnNames = config('helper-commands.log_activities.column_names');

        $this->bind();
    }

    private function bind(): void
    {
        $this->setTable($this->tableName);
        $this->fillable($this->getColumns());
    }

    private function getColumns(): array
    {
        $columnsNames = array_values($this->configColumnNames);

        return $columnsNames;
    }
}
