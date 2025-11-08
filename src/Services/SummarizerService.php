<?php

declare(strict_types=1);

namespace Src\Services;

use Src\Classes\OpenAIHack;
// use LLPhant\Chat\OpenAIChat;

readonly class SummarizerService
{
  public function __construct(private readonly OpenAIHack $chat) {}

  public function summarizePR(array $PRData): string
  {
    $fileList = implode("\n- ", array_slice($PRData['file_list'], 0, 15)); // Limit to 15 files

    $title = $PRData['title'] ?? '';
    $description = $PRData['description'] ?? '';

    $prompt = <<<PROMPT
You are an expert code reviewer.
Summarize the following Pull Request into a concise 3-bulleted list for a busy team.
1. Main purpose (What problem does it solve?)
2. Key technical changes/risks
3. Next steps for the reviewer

-- PR DETAILS --
Title: {$title}
Description: {$description}
Files Changed (first 15):
- {$fileList}
PROMPT;

    // Use LLPhant to generate the text
    return $this->chat->generateText($prompt);
  }
}
