<?php

use Slim\App;
require_once __DIR__ . '/../vendor/autoload.php';

$container = require __DIR__ . '/../bootstrap.php';

$container->get(App::class)->run();
