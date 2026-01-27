<?php

return [

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
    ],

    'allowed_methods' => ['*'],

    // Puedes dejar vacío esto si usas patterns:
    'allowed_origins' => [
        'http://127.0.0.1:1430',
        'http://localhost:1430',
        'tauri://localhost',
    ],

    'allowed_origins_patterns' => [
        '#^http://127\.0\.0\.1:\d+$#',
        '#^http://localhost:\d+$#',
    ],

    'allowed_headers' => ['*'],

    // No uses '*'
    'exposed_headers' => [],

    'max_age' => 86400,

    // 🔥 CLAVE: para tu caso debe ser false
    'supports_credentials' => false,

];
