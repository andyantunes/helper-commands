<?php

namespace AndyAntunes\HelperCommands;

use AndyAntunes\HelperCommands\Models\LogActivity;

class HelperCommands
{
    protected ?int $userId = null;

    protected ?int $recordId = null;

    protected ?string $action = null;

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
        /**
         * Create a new recent activity record.
         *
         * @param array $attributes The attributes to be assigned to the new record.
         *
         * @return \Illuminate\Database\Eloquent\Model|null The newly created record instance, or null if the operation fails.
         */
        LogActivity::create([
            'user_id' => $this->userId ?? auth()->id(),
            'action' => $this->recordId ? "{$this->action} de ID: {$this->recordId}" : $this->action,
        ]);
    }
}
