<?php

declare(strict_types=1);

namespace Src\Classes;

use LLPhant\OpenAIConfig;
use Src\Services\ConfigService;

class OpenAIConfigHack extends OpenAIConfig
{
    public function __construct(ConfigService $config){
        $this->apiKey = $config->get('openai');
    }
}
