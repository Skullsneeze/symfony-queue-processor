<?php

namespace App\Service;

use \App\Contracts\Message\PrioritizedMessageInterface;
use App\Message\HighPriorityMessage;
use App\Message\LowPriorityMessage;
use RuntimeException;

/**
 * Used to generate an instance of \App\Contracts\Message\PrioritizedMessageInterface based on the given priority
 */
class PrioritizedMessageFactory
{
    /**
     * @param int $priority
     * @param string|null $recipient
     * @param string|null $message
     * @param string|null $subject
     *
     * @return PrioritizedMessageInterface
     */
    public function create(
        int $priority,
        ?string $recipient = null,
        ?string $message = null,
        ?string $subject = null
    ): PrioritizedMessageInterface {
        return match ($priority) {
            PrioritizedMessageInterface::PRIORITY_LOW => new LowPriorityMessage(
                $recipient,
                $message,
                $subject
            ),
            PrioritizedMessageInterface::PRIORITY_HIGH => new HighPriorityMessage(
                $recipient,
                $message,
                $subject
            ),
            default => throw new RuntimeException('Unknown priority received.'),
        };
    }
}