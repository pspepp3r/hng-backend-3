<?php 

declare(strict_types=1);

use Slim\App;
use Src\Controllers\AgentController;

return function (App $app) {
    $app->post('/pr-summarizer', AgentController::class);
};
