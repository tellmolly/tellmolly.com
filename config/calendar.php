<?php

return [
    'actions' => [
        'register' => env('ENABLE_REGISTER', false),
        'reset' => env('ENABLE_RESET', false),
    ],
    'year' => env('DEFAULT_YEAR', 2019)
];
