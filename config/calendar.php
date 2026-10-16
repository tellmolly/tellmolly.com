<?php

return [
    'actions' => [
        'register' => env('ENABLE_REGISTER', false),
        'reset' => env('ENABLE_RESET', false),
        'demo' => env('ENABLE_DEMO', false),
    ],
    'demo' => [
        'email' => env('DEMO_EMAIL')
    ]
];
