<?php

namespace App\Tests\Unit\Service;

use App\Contracts\Message\PrioritizedMessageInterface;
use App\Service\RandomPrioritizedMessageFactory;
use PHPUnit\Framework\TestCase;

class RandomPrioritizedMessageFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $prioritizedMessageFactory = new RandomPrioritizedMessageFactory();
        $prioritizedMessage = $prioritizedMessageFactory->create(PrioritizedMessageInterface::PRIORITY_LOW);

        $this->assertSame(PrioritizedMessageInterface::PRIORITY_LOW, $prioritizedMessage->getPriority());

        $this->assertNotNull($prioritizedMessage->getRecipient());
        $this->assertNotNull($prioritizedMessage->getMessage());
        $this->assertNotNull($prioritizedMessage->getSubject());
    }
}
