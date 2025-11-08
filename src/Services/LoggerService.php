<?php

namespace Src\Services;

use DI\Container;
use Monolog\Logger;
use Psr\Log\LogLevel;

readonly class LoggerService
{
    private Logger $logger;

    public function __construct(private readonly Container $container)
    {
        $this->logger = $container->get(Logger::class);
    }

    public function log(string $logLevel, string $message){
        $this->logger->log($logLevel, $message);
    }
}
