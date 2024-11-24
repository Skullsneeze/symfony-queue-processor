<?php

namespace App\Tests\Unit\Service;

use App\Contracts\Message\PrioritizedMessageInterface;
use App\Service\PrioritizedMessageFactory;
use PHPUnit\Framework\TestCase;

class PrioritizedMessageFactoryTest extends TestCase
{
    public function testCreateEmpty(): void
    {
        $prioritizedMessageFactory = new PrioritizedMessageFactory();
        $prioritizedMessage = $prioritizedMessageFactory->create(PrioritizedMessageInterface::PRIORITY_LOW);

        $this->assertNull($prioritizedMessage->getRecipient());
        $this->assertNull($prioritizedMessage->getMessage());
        $this->assertNull($prioritizedMessage->getSubject());
    }

    public function testCreatePrefilled(): void
    {
        $prioritizedMessageFactory = new PrioritizedMessageFactory();

        $recipient = 'test@example.com';
        $message = 'My message';
        $subject = 'My subject';

        $prioritizedMessage = $prioritizedMessageFactory->create(
            PrioritizedMessageInterface::PRIORITY_LOW,
            $recipient,
            $message,
            $subject
        );

        $this->assertSame($recipient, $prioritizedMessage->getRecipient());
        $this->assertSame($message, $prioritizedMessage->getMessage());
        $this->assertSame($subject, $prioritizedMessage->getSubject());
    }

    public function testCreatePriority(): void
    {
        $prioritizedMessageFactory = new PrioritizedMessageFactory();

        $prioritizedMessage = $prioritizedMessageFactory->create(PrioritizedMessageInterface::PRIORITY_LOW);
        $this->assertSame(PrioritizedMessageInterface::PRIORITY_LOW, $prioritizedMessage->getPriority());

        $prioritizedMessage = $prioritizedMessageFactory->create(PrioritizedMessageInterface::PRIORITY_HIGH);
        $this->assertSame(PrioritizedMessageInterface::PRIORITY_HIGH, $prioritizedMessage->getPriority());
    }
}
