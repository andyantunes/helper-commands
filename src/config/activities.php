<?php

return [
    /**
     * If you are asked to create an activity when a model is created
     */
    'ask_to_create' => true,

    /**
     * The model which the activity model should be related
     */
    'models' => [
        'user' => App\Models\User::class,
    ],

    /**
     * The table name of the activity
     * The table name which the foreign key of the activity table should be related
     */
    'table_names' => [
        'recent_activity' => 'recent_activities',

        'users' => 'users',
    ],

    /**
     * The column name of the activity table foreign key
     * The column name of the user action
     */
    'column_names' => [
        'user_foreign_key' => 'user_id',

        'user_action' => 'action',
    ]
];
