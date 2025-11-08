<?php

declare(strict_types=1);

namespace Src\Classes;

use LLPhant\Chat\OpenAIChat;

class OpenAIHack extends OpenAIChat
{
    public function __construct(OpenAIConfigHack $config){
        parent::__construct($config);
    }
}
