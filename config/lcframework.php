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

    'modules' => [
        'paths' => [
            env('LCFRAMEWORK_MODULES_PATH', base_path('modules')),
        ],

        'cache' => [
            'enabled' => true,
            'keys' => [
                'all' => config('LCFRAMEWORK_MODULES_CACHE_KEY', 'lcframework.modules.all'),
                'ordered' => config('LCFRAMEWORK_MODULES_CACHE_KEY', 'lcframework.modules.ordered'),
            ],
        ],
    ],
];
