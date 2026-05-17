<?php

declare(strict_types=1);

namespace Phlex\Hub\Health;

use Phlex\Hub\Version;
use Phlex\Shared\Version as SharedVersion;

/**
 * Liveness/readiness endpoint for `phlex-hub`.
 *
 * Returns a small JSON-serializable array describing service identity,
 * package versions, and the current Unix timestamp. The controller has
 * no DB or filesystem dependency on purpose so the endpoint is safe
 * to hit from monitors while the rest of the stack is starting up.
 *
 * @package Phlex\Hub\Health
 * @since 0.1.0
 */
final class HealthController
{
    /**
     * Build the health payload.
     *
     * @return array{
     *     status: string,
     *     service: string,
     *     version: string,
     *     phlexShared: string,
     *     timestamp: int
     * }
     *
     * @since 0.1.0
     */
    public function __invoke(): array
    {
        return [
            'status' => 'ok',
            'service' => 'phlex-hub',
            'version' => Version::VERSION,
            'phlexShared' => SharedVersion::VERSION,
            'timestamp' => time(),
        ];
    }
}
