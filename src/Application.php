<?php

declare(strict_types=1);

namespace Phlex\Hub;

use Phlex\Hub\Common\Logger\LogChannels;
use Phlex\Hub\Common\Logger\StructuredLogger;
use Phlex\Hub\Health\HealthController;
use Phlex\Hub\Http\Request;
use Phlex\Hub\Http\Response;
use Phlex\Hub\Http\Router;
use Psr\Container\ContainerInterface;
use Throwable;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request as WorkermanRequest;
use Workerman\Worker;

/**
 * Phlex Hub main application bootstrap.
 *
 * Wires the HTTP router with the minimum route set required for B.5
 * (the `/health` endpoint) and boots a single Workerman HTTP worker.
 *
 * Future steps add the auth, claim, heartbeat and relay routes.
 *
 * @package Phlex\Hub
 * @since 0.1.0
 */
final class Application
{
    private Router $router;

    /**
     * @param ContainerInterface   $container PSR-11 container built by
     *                                        {@see \Phlex\Hub\Common\Container\ContainerFactory::create()}.
     * @param array<string, mixed> $config    Server config slice (host, port, workers, …).
     */
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly array $config,
    ) {
        $this->router = new Router();
        $this->registerRoutes();
    }

    /**
     * Register the routes the hub exposes today.
     */
    private function registerRoutes(): void
    {
        $health = $this->container->get(HealthController::class);
        if (!$health instanceof HealthController) {
            throw new \RuntimeException('Container returned an unexpected HealthController instance');
        }

        $this->router->get('/health', static function () use ($health): Response {
            return (new Response())->json($health());
        });

        $this->router->get('/', static function (): Response {
            return (new Response())->html(
                "<!DOCTYPE html>\n<html lang=\"en\"><head><meta charset=\"utf-8\"><title>Phlex Hub</title></head>"
                . "<body><h1>Phlex Hub</h1><p>Coming soon. See <code>/health</code> for status.</p></body></html>",
            );
        });
    }

    /**
     * Get the underlying router (used by tests and B.7 route registration).
     *
     * @since 0.1.0
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * Boot a Workerman HTTP worker that dispatches each request through
     * the router.
     *
     * This call is blocking — it returns only after `Worker::runAll()`
     * exits (i.e. on shutdown).
     *
     * @return void
     *
     * @since 0.1.0
     */
    public function boot(): void
    {
        /** @var mixed $hostRaw */
        $hostRaw = $this->config['host'] ?? '0.0.0.0';
        $host = is_string($hostRaw) ? $hostRaw : '0.0.0.0';
        /** @var mixed $portRaw */
        $portRaw = $this->config['port'] ?? 8800;
        $port = is_int($portRaw) ? $portRaw : (int) (is_numeric($portRaw) ? $portRaw : 8800);
        /** @var mixed $workersRaw */
        $workersRaw = $this->config['workers'] ?? 2;
        $workers = is_int($workersRaw) ? $workersRaw : (int) (is_numeric($workersRaw) ? $workersRaw : 2);

        $worker = new Worker(sprintf('http://%s:%d', $host, $port));
        $worker->count = $workers;
        $worker->name = 'phlex-hub-http';

        $router = $this->router;
        $logger = $this->resolveHttpLogger();

        $worker->onMessage = static function (
            TcpConnection $connection,
            WorkermanRequest $request,
        ) use (
            $router,
            $logger,
        ): void {
            try {
                $hubRequest = Request::fromWorkerman($request);
                $response = $router->dispatch($hubRequest);
                $connection->send($response->toWorkermanResponse());
            } catch (Throwable $e) {
                $logger?->error('Unhandled exception in hub request', [
                    'exception' => $e::class,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
                $error = (new Response())
                    ->status(500)
                    ->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
                $connection->send($error->toWorkermanResponse());
            }
        };

        Worker::runAll();
    }

    /**
     * Resolve the HTTP-channel logger if the container can provide it,
     * returning null when it cannot (e.g. in unit tests with a stub
     * container).
     */
    private function resolveHttpLogger(): ?StructuredLogger
    {
        try {
            /** @var mixed $logger */
            $logger = $this->container->get('logger.' . LogChannels::HTTP);
            return $logger instanceof StructuredLogger ? $logger : null;
        } catch (Throwable) {
            return null;
        }
    }
}
