<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Adjust paths as needed

    'allowed_methods' => ['*'], // Allow all methods (GET, POST, etc.)

    'allowed_origins' => ['*'], // Allow all origins (you might want to restrict this in production)

    'allowed_origins_patterns' => [], // If you want to use patterns for allowed origins, specify them here

    'allowed_headers' => ['*'], // Allow all headers

    'exposed_headers' => [], // Specify which headers should be exposed

    'max_age' => 0, // The maximum age of the preflight request in seconds

    'supports_credentials' => false, // Whether or not to allow credentials (cookies, HTTP authentication, etc.)

];
