<?php

return [

    'cache_key' => 'app_settings_',

    'cache_duration' => 86400,

    'table_name' => 'settings',

    'categories' => [
        'app' => [
            'name' => 'app',
            'ignore' => ['providers', 'aliases', 'log', 'log_level', 'key', 'cipher']
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