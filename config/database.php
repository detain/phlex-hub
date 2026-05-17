<?php

declare(strict_types=1);

return [
    'mysql' => [
        'host'     => getenv('HUB_DB_HOST') ?: '127.0.0.1',
        'port'     => (int) (getenv('HUB_DB_PORT') ?: 3306),
        'user'     => getenv('HUB_DB_USER') ?: 'phlex_hub',
        'password' => getenv('HUB_DB_PASSWORD') ?: 'phlex_hub',
        'database' => getenv('HUB_DB_NAME') ?: 'phlex_hub',
    ],
];
