<?php

namespace App\Tests\Unit\Service;

use App\Message\LowPriorityMessage;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PrioritizedMessageTest extends TestCase
{
    public function testEmailValidation(): void
    {
        $prioritizedMessage = new LowPriorityMessage();

       $this->expectException(InvalidArgumentException::class);
       $prioritizedMessage->setRecipient('invalid email');

    }

    public function testSubjectValidation(): void
    {
        $prioritizedMessage = new LowPriorityMessage();

        $this->expectException(InvalidArgumentException::class);
        $prioritizedMessage->setSubject('');
    }
}
