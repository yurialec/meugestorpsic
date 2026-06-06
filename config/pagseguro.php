<?php

return [
    'base_url'      => env('PAGSEGURO_BASE_URL'),
    'sandbox'       => env('PAGSEGURO_SANDBOX', false),
    'token'         => env('PAGSEGURO_TOKEN'),
    'public_key'    => env('PAGSEGURO_PUBLIC_KEY'),
    'webhook_url'   => env('PAGSEGURO_WEBHOOK_URL'),
    'webhook_token' => env('PAGSEGURO_WEBHOOK_TOKEN'),
];
