<?php

return [
    'models' => [
        'user' => App\Models\User::class,
    ],

    'table_names' => [
        'recent_activity' => 'recent_activities',

        'users' => 'users',
    ],

    'column_names' => [
        'user_foreign_key' => 'user_id',

        'user_action' => 'action',
    ]
];
