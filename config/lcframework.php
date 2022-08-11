<?php

return [
    'settings' => [
        'driver' => 'file',

        'cache' => [
            'enabled' => true,
            'key' => env('LCFRAMEWORK_SETTINGS_CACHE_KEY', 'lcframework.settings'),
            'ttl' => -1,
        ],

        'file' => [
            'path' => 'settings.json',
        ],

        'database' => [
            'table' => 'settings',

            'columns' => [
                'key' => env('LCFRAMEWORK_SETTINGS_DATABASE_KEY_COLUMNS', 'key'),
                'value' => env('LCFRAMEWORK_SETTINGS_DATABASE_VALUE_COLUMNS', 'value'),
            ],
        ],
    ],
];
