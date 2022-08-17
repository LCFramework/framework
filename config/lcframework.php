<?php

return [
    'last_chaos' => [
        'version' => (int) env('LCFRAMEWORK_LAST_CHAOS_VERSION', 4),

        'database' => [
            'data' => env('LCFRAMEWORK_LAST_CHAOS_DATABASE_DATA', 'data'),
            'db' => env('LCFRAMEWORK_LAST_CHAOS_DATABASE_DB', 'db'),
            'auth' => env('LCFRAMEWORK_LAST_CHAOS_DATABASE_AUTH', 'auth'),
            'post' => env('LCFRAMEWORK_LAST_CHAOS_DATABASE_POST', 'post'),
        ],

        'auth' => [
            'salt' => env('LCFRAMEWORK_LAST_CHAOS_AUTH_SALT', ''),
        ],
    ],

    'auth' => [
        'require_email_verification' => (bool) env('LCFRAMEWORK_AUTH_REQUIRE_EMAIL_VERIFICATION', true),

        'routes' => [
            'login' => env('LCFRAMEWORK_AUTH_LOGIN_ROUTE', '/login'),
            'register' => env('LCFRAMEWORK_AUTH_REGISTER_ROUTE', '/register'),
            'logout' => env('LCFRAMEWORK_AUTH_LOGOUT_ROUTE', '/logout'),
            'password' => [
                'request' => env('LCFRAMEWORK_AUTH_PASSWORD_REQUEST_ROUTE', '/forgot-password'),
                'reset' => env('LCFRAMEWORK_AUTH_PASSWORD_RESET_ROUTE', '/reset-password'),
                'confirm' => env('LCFRAMEWORK_AUTH_PASSWORD_CONFIRM_ROUTE', '/confirm-password'),
            ],
            'email' => [
                'notice' => env('LCFRAMEWORK_AUTH_EMAIL_NOTICE_ROUTE', '/email-verification'),
                'verify' => env('LCFRAMEWORK_AUTH_EMAIL_VERIFY_ROUTE', '/email-verification'),
            ],
        ],
    ],

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

    'themes' => [
        'paths' => [
            env('LCFRAMEWORK_THEMES_PATH', base_path('themes')),
        ],

        'cache' => [
            'enabled' => true,
            'key' => env('LCFRAMEWORK_THEMES_CACHE_KEY', 'lcframework.themes'),
        ],
    ],
];
