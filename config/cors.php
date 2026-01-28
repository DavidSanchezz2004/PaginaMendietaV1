<?php

return [

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
    ],

    'allowed_methods' => ['*'],

    // En Tauri/Windows el Origin puede variar:
    // - tauri://localhost
    // - asset://localhost
    // - http://tauri.localhost (según config)
    // - null (algunas cargas WebView)
    'allowed_origins' => [
        'tauri://localhost',
        'asset://localhost',
        'http://tauri.localhost',
        'http://localhost',
        'http://127.0.0.1',
        'null',
        // Si también lo abres desde navegador local:
        'http://127.0.0.1:1430',
        'http://localhost:1430',
    ],

    // Mantén patterns para puertos locales aleatorios
    'allowed_origins_patterns' => [
        '#^http://127\.0\.0\.1:\d+$#',
        '#^http://localhost:\d+$#',
        '#^http://tauri\.localhost(:\d+)?$#',
        '#^tauri://localhost$#',
        '#^asset://localhost$#',
        '#^null$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 86400,

    // Correcto para tu caso (Bearer token, sin cookies)
    'supports_credentials' => false,
];
