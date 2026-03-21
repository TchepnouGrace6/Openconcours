<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    */
    'rate_limiting' => [
        'login' => [
            'max_attempts' => 5,
            'decay_minutes' => 15,
        ],
        'register' => [
            'max_attempts' => 3,
            'decay_minutes' => 60,
        ],
        'api_general' => [
            'max_attempts' => 100,
            'decay_minutes' => 1,
        ],
        'api_sensitive' => [
            'max_attempts' => 20,
            'decay_minutes' => 1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Security
    |--------------------------------------------------------------------------
    */
    'file_upload' => [
        'allowed_image_types' => ['jpg', 'jpeg', 'png'],
        'allowed_document_types' => ['pdf', 'doc', 'docx'],
        'max_image_size' => 2048, // KB
        'max_document_size' => 5120, // KB
        'max_files_per_request' => 5,
        'scan_for_malware' => env('SCAN_UPLOADS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Input Validation
    |--------------------------------------------------------------------------
    */
    'validation' => [
        'max_string_length' => 255,
        'max_text_length' => 1000,
        'allowed_name_pattern' => '/^[a-zA-ZÀ-ÿ\s\-\']+$/',
        'allowed_phone_pattern' => '/^[0-9+\-\s()]+$/',
        'min_password_length' => 8,
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'log_all_requests' => env('LOG_ALL_REQUESTS', false),
        'log_failed_attempts' => true,
        'log_sensitive_actions' => true,
        'retention_days' => 90,
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Security
    |--------------------------------------------------------------------------
    */
    'session' => [
        'token_expiration_hours' => 24,
        'refresh_token_expiration_days' => 30,
        'max_concurrent_sessions' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | IP Whitelist/Blacklist
    |--------------------------------------------------------------------------
    */
    'ip_filtering' => [
        'enable_whitelist' => env('ENABLE_IP_WHITELIST', false),
        'whitelist' => explode(',', env('IP_WHITELIST', '')),
        'enable_blacklist' => env('ENABLE_IP_BLACKLIST', true),
        'blacklist' => explode(',', env('IP_BLACKLIST', '')),
    ],
];