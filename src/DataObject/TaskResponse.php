<?php

declare(strict_types=1);

namespace Src\DataObject;

use Ramsey\Uuid\Uuid;

/**
 * TaskResponse DTO
 *
 * Builds the JSON-RPC A2A response payload used by AgentController.
 */
final class TaskResponse
{
    private array $payload;

    /**
     * @param mixed $id JSON-RPC request id
     * @param string $messageText The text to return inside artifacts.parts[0].text
     */
    public function __construct(private $id, private string $messageText, private string $status)
    {
        $this->payload = [
            'jsonrpc' => 2.0,
            'id' => $this->id ?? null,
            'result' => [
                'id' => Uuid::uuid4()->toString(),
                'contextId' => Uuid::uuid4()->toString(),
                'status' => [
                    'state' => $this->status ?? 'completed',
                    'timestamp' => new \DateTimeImmutable()->getTimestamp()
                ]
            ],
            'artifacts' => [
                [
                    'type' => 'message',
                    'parts' => [
                        [
                            'type' => 'text',
                            'text' => $this->messageText,
                        ]
                    ]
                ]
            ],
            'history' => [],
            'kind' => 'task',
        ];
    }

    /**
     * Get the payload as an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->payload;
    }

    /**
     * Get the payload as JSON string
     *
     * @return string
     */
    public function toJson(): string
    {
        return (string) json_encode($this->payload);
    }
}
