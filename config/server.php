<?php

declare(strict_types=1);

return [
    'host'          => getenv('HUB_HOST') ?: '0.0.0.0',
    'port'          => (int) (getenv('HUB_PORT') ?: 8800),
    'workers'       => (int) (getenv('HUB_WORKERS') ?: 2),
    'workerman_log' => getenv('HUB_WORKERMAN_LOG') ?: __DIR__ . '/../.logs/workerman.log',
];
