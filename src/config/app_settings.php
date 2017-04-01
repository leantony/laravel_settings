<?php

return [

    'cache_key' => 'app_settings_',

    'cache_duration' => 86400,

    'table_name' => 'settings',

    /**
     * Use the same name as the files in app/config/*
     */
    'categories' => [
        'app' => [
            'name' => 'app',
            'ignore' => '*'
        ],
        'mail' => [
            'name' => 'mail',
            'ignore' => ['from', 'sendmail']
        ],
        'session' => [
            'name' => 'session',
            'ignore' => ['lottery', 'http_only', 'connection', 'files']
        ]
    ]
];