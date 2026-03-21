<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:4200',
        'http://127.0.0.1:4200',

    ],

    'allowed_origins_patterns' => ['#^https://.*\.ngrok-free\.app$#'],

    'allowed_headers' => ['*'],

    'exposed_headers' => [
        'Authorization',
        'Content-Type',
        'X-CSRF-TOKEN',
    ],

    'max_age' => 0,

    'supports_credentials' => true,
];
