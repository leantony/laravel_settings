<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable caching of settings
    |--------------------------------------------------------------------------
    |
    | cache settings retrieved from the database. This happens when the app loads
    | or when the command 'settings:bind' is run
    |
    */
    'cache' => false,

    /*
    |--------------------------------------------------------------------------
    | Table Name
    |--------------------------------------------------------------------------
    |
    | The name of the table which the settings will be stored.
    |
    */
    'table_name' => 'settings',

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    |
    | Categories of settings that will be stored in the db.
    | This should ideally be the actual file names in app/config/*.php
    |
    | If a file name is missing here, it's values wont be persisted
    | into the database.
    |
    | Examples have been given below. Feel free to add more
    |
    */
    'categories' => [
        // The file name app.php
        'app' => [
            // category name. Let it be the same as the file name itself
            'name' => 'app',
            // The keys in the config file that should be ignored when scanning
            // for values to persist in the database
            'ignore' => ['providers', 'aliases', 'log', 'log_level', 'key', 'cipher']
        ],

        // the file name mail.php
        'mail' => [
            // category name
            'name' => 'mail',
            // keys that will be ignored...
            'ignore' => ['from', 'sendmail']
        ],
        // the file name app_settings.php (which is this one)
        'app_settings' => [
            // category name
            'name' => 'app_settings',
            // keys to be ignored. (*) means all values will be ignored
            // hence it will not be persisted in the database
            'ignore' => '*'
        ],
        // ... more files below
    ]
];