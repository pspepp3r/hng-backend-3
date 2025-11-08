<?php

namespace Src\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Src\Services\LoggerService;

class LoggingMiddleware implements MiddlewareInterface
{

    public function __construct(private readonly LoggerService $loggerService)
    {
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $start = microtime(true);
        $response = $handler->handle($request);
        $duration = microtime(true) - $start;

        $data = [
            'method' => $request->getMethod(),
            'uri' => (string)$request->getUri(),
            'status' => $response->getStatusCode(),
            'duration_ms' => (int)($duration * 1000),
            'ip' => $request->getServerParams()['REMOTE_ADDR'] ?? 'unknown',
            'time' => date('c'),
        ];

        $this->loggerService->getLogger()->info('request', $data);
        $this->loggerService->pushToRedis('app:logs', $data);

        return $response;
    }
}
