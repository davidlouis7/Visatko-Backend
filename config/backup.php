<?php

return [
    'enabled' => env('BACKUP_ENABLED', false),
    'disk' => env('BACKUP_DISK', 'local'),
    'database' => [
        'connection' => env('BACKUP_DB_CONNECTION', env('DB_CONNECTION')),
        'filename_prefix' => env('BACKUP_DB_PREFIX', 'visatko-db'),
    ],
    'storage' => [
        'include' => array_filter(explode(',', (string) env('BACKUP_STORAGE_PATHS', 'storage/app'))),
    ],
    'retention_days' => (int) env('BACKUP_RETENTION_DAYS', 14),
];
