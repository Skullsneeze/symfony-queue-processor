<?php

namespace App\Message;

final class HighPriorityMessage extends PrioritizedMessage
{
    /**
     * @inheritDoc
     */
    public function getPriority(): int
    {
        return self::PRIORITY_HIGH;
    }
}
