<?php

namespace AndyAntunes\HelperCommands;

use AndyAntunes\HelperCommands\Models\LogActivity as ModelLogActivity;

class LogActivity
{
    private string $foreignKeyColumn;
    private string $actionColumn;

    protected ?int $userId = null;

    protected ?int $recordId = null;

    protected ?string $action = null;

    public function __construct()
    {
        $this->foreignKeyColumn = config('helper-commands.log_activities.column_names.user_foreign_key');
        $this->actionColumn = config('helper-commands.log_activities.column_names.user_action');
    }

    /**
     * Set the user ID for the activity.
     * If no user ID is provided, it will default to the authenticated user's ID.
     *
     * @param int|null $userId The user ID for the activity.
     *
     * @return self Returns the current instance for method chaining.
     */
    public function setUserId(int $userId = null): self
    {
        $this->userId = $userId ?? auth()->user()->id;

        return $this;
    }

    /**
     * Set the record ID for the activity.
     *
     * @param int $recordId The record ID for the activity.
     *
     * @return self Returns the current instance for method chaining.
     */
    public function setRecordId(int $recordId): self
    {
        $this->recordId = $recordId;

        return $this;
    }

    /**
     * Set the action for the activity.
     *
     * @param string $action The action that was performed.
     *
     * @return self Returns the current instance for method chaining.
     */
    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Create the user activity.
     *
     * @return void
     */
    public function create(): void
    {
        ModelLogActivity::create([
            $this->foreignKeyColumn => $this->userId ?? auth()->id(),
            $this->actionColumn => $this->recordId ? "{$this->action} de ID: {$this->recordId}" : $this->action,
        ]);
    }
}
