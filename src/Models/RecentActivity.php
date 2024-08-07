<?php

namespace AndyAntunes\UserActivities\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecentActivity extends Model
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
            config('activities.models.user'),
            config('activities.table_names.users'),
        );
    }
}
