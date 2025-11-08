<?php

declare(strict_types=1);

use Src\Enums\AppEnvironment;

$boolean = function (mixed $value) {

    if (in_array($value, ['true', 1, '1', true, 'yes'], true))
        return true;

    return false;
};

$appEnv = $_ENV['APP_ENV'] ?? AppEnvironment::Production->value;
$formatAppName = strtolower(str_replace(' ', '_', $_ENV['APP_NAME']));

return [

    'app' => [
        'app_name'        => $_ENV['APP_NAME'],
        'app_environment' => $appEnv,
        'app_debug'       => $boolean($_ENV['APP_DEBUG'] ?? 0),
    ],

    'error_handling' => [
        'log_errors'            => true,
        'display_error_details' => $boolean($_ENV['APP_DEBUG'] ?? 0),
        'log_error_details'     => true
    ],
    'github' => [
        'api_url' => $_ENV['GITHUB_API_URL'],
        'pat_token' => $_ENV['GITHUB_PAT_TOKEN']
    ],
    'openai' => $_ENV['OPENAI_API_KEY']
];
