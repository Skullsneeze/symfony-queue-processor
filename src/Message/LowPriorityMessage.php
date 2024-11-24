<?php

namespace App\Message;

final class LowPriorityMessage extends PrioritizedMessage
{
    /**
     * @inheritDoc
     */
    public function getPriority(): int
    {
        return self::PRIORITY_LOW;
    }
}
