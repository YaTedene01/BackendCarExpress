<?php

return [
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'docs',
        'api/documentation',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_values(array_filter(array_map(
        static fn (string $origin): string => trim($origin),
        explode(',', (string) env(
            'CORS_ALLOWED_ORIGINS',
            'http://localhost,http://localhost:5173,http://127.0.0.1:5173,https://projet-car-express.vercel.app'
        ))
    ))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
