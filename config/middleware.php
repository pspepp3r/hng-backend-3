<?php

declare(strict_types=1);

use Monolog\Logger;
use Slim\App;
use Src\Middlewares\JsonMiddleware;

return function (App $app) {
    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();
    $app->addErrorMiddleware(
        true,
        true,
        true,
        $app->getContainer()->get(Logger::class)
    );
    $app->add(new JsonMiddleware());
};
