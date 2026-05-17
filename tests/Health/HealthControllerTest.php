<?php

declare(strict_types=1);

namespace Phlex\Hub\Tests\Health;

use Phlex\Hub\Health\HealthController;
use Phlex\Hub\Version;
use Phlex\Shared\Version as SharedVersion;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for {@see HealthController}.
 *
 * @package Phlex\Hub\Tests\Health
 * @since 0.1.0
 *
 * @covers \Phlex\Hub\Health\HealthController
 */
final class HealthControllerTest extends TestCase
{
    public function testInvokeReturnsOkStatus(): void
    {
        $payload = (new HealthController())();

        self::assertSame('ok', $payload['status']);
    }

    public function testInvokeIdentifiesAsPhlexHub(): void
    {
        $payload = (new HealthController())();

        self::assertSame('phlex-hub', $payload['service']);
    }

    public function testInvokeIncludesPackageVersion(): void
    {
        $payload = (new HealthController())();

        self::assertSame(Version::VERSION, $payload['version']);
    }

    public function testInvokeIncludesSharedVersion(): void
    {
        $payload = (new HealthController())();

        self::assertSame(SharedVersion::VERSION, $payload['phlexShared']);
    }

    public function testInvokeIncludesRecentTimestamp(): void
    {
        $before = time();
        $payload = (new HealthController())();
        $after = time();

        self::assertGreaterThanOrEqual($before, $payload['timestamp']);
        self::assertLessThanOrEqual($after, $payload['timestamp']);
    }
}
