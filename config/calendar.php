<?php

return [
    'actions' => [
        'register' => env('ENABLE_REGISTER', false),
        'reset' => env('ENABLE_RESET', false),
        'demo' => env('ENABLE_DEMO', false),
    ],
    'year' => env('DEFAULT_YEAR', date('Y')),
    'demo' => [
        'email' => env('DEMO_EMAIL')
    ]
];
