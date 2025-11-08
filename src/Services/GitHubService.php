<?php

declare(strict_types=1);

namespace Src\Services;

use GuzzleHttp\Client;

readonly class GitHubService
{
  public function __construct(private readonly Client $client) {}

  /*
  * Extracts details from a Github PR URL and fetches necessary data
  */
  public function fetchPRSummaryData(string $PRUrl): array
  {
    // Simple regex to parse Github PR URL
    if (
      !preg_match(
        '/github\.com\/([^\/]+)\/([^\/]+)\/pull\/(\d+)/i',
        $PRUrl,
        $matches
      )
    ) {
      throw new \InvalidArgumentException('Invalid Github PR URL link.');
    }
    [$url, $owner, $repo, $PRNumber] = $matches;

    // Fetch the main PR details and files changed
    $PRResponse = $this->client->request('GET', "repos/$owner/$repo/pulls/$PRNumber");
    $fileResponse = $this->client->request('GET', "repos/$owner/$repo/pulls/$PRNumber/files");

    $PRData = json_decode((string) $PRResponse->getBody(), true);
    $fileData = json_decode((string) $fileResponse->getBody(), true);

    // Extract key data for LLM
    return [
      'title' => $PRData['title'] ?? 'N/A',
      'description' => $PRData['body'] ?? 'No description given',
      'file_list' => array_column($fileData, 'filename')
    ];
  }
}
