<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS Configuration
    |--------------------------------------------------------------------------
    |
    | Vous pouvez configurer ici les options de votre middleware CORS.
    | Ces paramètres sont utilisés pour permettre les requêtes cross-origin.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
