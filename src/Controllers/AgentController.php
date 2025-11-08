<?php

declare(strict_types=1);

namespace Src\Controllers;

use Psr\Log\LogLevel;

use Slim\Http\ServerRequest as Request;

use Slim\Http\Response;
use Src\Services\GitHubService;
use Src\Services\SummarizerService;
use Src\DataObject\TaskResponse;
use Src\Services\LoggerService;

readonly class AgentController
{
  public function __construct(
    private readonly GitHubService $gitHubService,
    private readonly SummarizerService $summarizer,
    private readonly LoggerService $loggerService
  ) {}

  public function __invoke(Request $request, Response $response): Response
  {
    $payload = $request->getParsedBody();
    $responseText = "I'm sorry, I couldn't process that request";

    try {
      // A2A Protocol (validate and extract essential fields)
      if (!isset($payload['jsonrpc'], $payload['id'], $payload['method'])) {
        $this->loggerService->log(LogLevel::INFO, "A user sent an invalid JSON-RPC Format.");
        $status = 'failed';
        throw new \Exception("Invalid JSON-RPC Format!");
      }

      if ($payload['method'] == 'task/send') {
        $userMessage = $payload['params']['message']['parts'][0]['text'] ?? '';

        // Extract thr PR Link 
        preg_match('/https:\/\/github\.com\/[^\s]+/i', $userMessage, $matches);
        $PRURL = $matches[0] ?? null;

        if ($PRURL) {
          // Fetch and Summarize data
          $PRData = $this->gitHubService->fetchPRSummaryData($PRURL);
          $responseText = $this->summarizer->summarizePR($PRData);
        } else {
          $status = 'failed';
          throw new \Exception("Please provide a valid git PR link for me to summarize, usually looks like: https://github.com/[username]/[repo]/pull/[nth_pr]");
        }
      }
    } catch (\Exception $e) {
      //Log and return detailed response
      $responseText = $e->getMessage();
      $response = $response->withStatus(406);
    } finally {
      // Build A2A JSON-RPC Response using DTO
      $taskResponse = new TaskResponse($payload['id'] ?? null, $responseText, $status);

      $response->getBody()->write($taskResponse->toJson());
      return $response;
    }
  }
}
