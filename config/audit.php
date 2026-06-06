<?php

return [
    'enabled' => env('AUDIT_LOG_ENABLED', true),

    'timezone' => env('AUDIT_LOG_TIMEZONE', 'America/Sao_Paulo'),

    'retention_days' => (int) env('AUDIT_LOG_RETENTION_DAYS', 1825),

    'queue' => [
        'enabled' => env('AUDIT_LOG_QUEUE_ENABLED', true),
        'connection' => env('AUDIT_LOG_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'sync')),
        'queue' => env('AUDIT_LOG_QUEUE', 'audit'),
        'after_commit' => true,
    ],

    'cpf_hash_salt' => env('AUDIT_LOG_CPF_HASH_SALT', env('APP_KEY')),

    'ip_headers' => [
        'CF-Connecting-IP',
        'X-Forwarded-For',
        'X-Real-IP',
        'True-Client-IP',
    ],

    'login_routes' => [
        'area.restrita.login',
        'tenant.login',
        'login',
    ],

    'logout_routes' => [
        'admin.logout',
        'tenant.logout',
        'logout',
    ],
];
