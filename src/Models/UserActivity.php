<?php

namespace AndyAntunes\UserActivities\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivity extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'action',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            config('helper-commands.log_activities.models.user'),
            config('helper-commands.log_activities.table_names.users'),
        );
    }
}
