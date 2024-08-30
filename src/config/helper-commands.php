<?php

return [

    /**
     * Configuration of Log Activities command
     */
    'log_activities' => [

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
            'log_activity' => 'log_activities',

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
    ],

    /**
     * Configuration of Factory Generation command
     */
    'factory_generation' => [

        'methods' => [

            /**
             * String
             */
            'varchar' => 'sentence',
            'text' => 'text',

            /**
             * Numeric
             */
            'int' => 'randomNumber',
            'float' => 'randomFloat',

            /**
             * Boolean
             */
            'boolean' => 'boolean',

            /**
             * Date and Time
             */
            'date' => 'date',
            'time' => 'time',
            'datetime' => 'dateTime',
            'timestamps' => 'dateTime',

            /**
             * Custom Configuration for Column Types
             */
            'custom_columns' => [

                /**
                 * Person
                 */
                'name' => 'name',
                'phone_number' => 'phoneNumber',
                'email' => 'safeEmail',
                'password' => 'password',

                /**
                 * Internet
                 */
                'url' => 'url',

                /**
                 * Address
                 */
                'city' => 'city',
                'state' => 'state',
                'street' => 'streetName',
                'country' => 'country',
            ],
        ],

        'params' => [
            'passwordMinLength' => 8,
            'passwordMaxLength' => null,
        ]
    ]
];
