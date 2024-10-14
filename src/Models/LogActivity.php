<?php

namespace AndyAntunes\HelperCommands\Models;

use AndyAntunes\HelperCommands\Services\Traits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogActivity extends Model
{
    use Traits\DynamicTableBinding;

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            config('helper-commands.log_activities.models.user'),
            config('helper-commands.log_activities.table_names.users'),
        );
    }
}
