<?php

return [
    'auth' => [
        'guard' => env('FILAMENT_AUTH_GUARD', 'web'),
        'pages' => [
            'login' => \App\Filament\Auth\Login::class,
        ],
    ],
];
