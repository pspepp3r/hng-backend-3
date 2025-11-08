<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use Slim\App;
use DI\Container;
use Slim\Factory\AppFactory;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Src\Services\ConfigService;

return [

    App::class => function (Container $container) {

        AppFactory::setContainer($container);

        $router = require CONFIG_DIR . '/routes/api.php';
        $addMiddleware = require CONFIG_DIR . '/middleware.php';

        $app = AppFactory::create();

        $router($app);
        $addMiddleware($app);    

        return $app;
    },
    ConfigService::class => function(){
        $config = require_once CONFIG_DIR . '/app.php';
        return new ConfigService($config);
    },

    Logger::class => function (ConfigService $config) {
        $logger = new Logger($config->get('app.app_name'));
        $handler = new StreamHandler(LOG_DIR . '/app.log');
        $formatter = new LineFormatter(
            "[%datetime%] [%level_name%]: %message% %context% %extra%\n",
            "Y-m-d H:i:s",
            true,
            true
        );

        $handler->setFormatter($formatter);
        return $logger->pushHandler($handler);
    },

    Client::class => fn(ConfigService $config) => new Client([
        'base_uri' => $config->get('github.api_url'),
        'headers' => [
            'Authorization' => 'token ' . $config->get('github.pat_token'),
            'Acept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'Telex-PR-Summarizer-Agent'
        ],
        'timeout' => 10.0
    ]),
];
