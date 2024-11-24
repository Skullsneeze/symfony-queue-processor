<?php

namespace App\Message;

use App\Contracts\Message\PrioritizedMessageInterface;
use InvalidArgumentException;

abstract class PrioritizedMessage implements PrioritizedMessageInterface
{
    /**
     * @param string|null $recipient
     * @param string|null $message
     * @param string|null $subject
     */
    public function __construct(
        private ?string $recipient = null,
        private ?string $message = null,
        private ?string $subject = null,
    ){
    }

    /**
     * @inheritDoc
     */
    public function getRecipient(): ?string
    {
        return $this->recipient;
    }

    /**
     * @inheritDoc
     */
    public function setRecipient(string $recipient): void
    {
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('The provided recipient must be a valid email address');
        }
        $this->recipient = $recipient;
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @inheritDoc
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @inheritDoc
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @inheritDoc
     */
    public function setSubject(string $subject): void
    {
        if (empty($subject)) {
            throw new InvalidArgumentException('The provided subject may not be empty');
        }

        $this->subject = $subject;
    }
}
