<?php

declare(strict_types=1);

namespace App\Contracts\Message;

use InvalidArgumentException;

interface PrioritizedMessageInterface
{
    public const PRIORITY_HIGH = 1;
    public const PRIORITY_LOW = 2;
    public const PRIORITY_LABELS = [
        self::PRIORITY_HIGH => 'high',
        self::PRIORITY_LOW => 'low'
    ];

    /**
     * @return int
     */
    public function getPriority(): int;

    /**
     * @return string|null
     */
    public function getRecipient(): ?string;

    /**
     * @param string $recipient
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function setRecipient(string $recipient): void;

    /**
     * @return string|null
     */
    public function getMessage(): ?string;

    /**
     * @param string $message
     *
     * @return void
     */
    public function setMessage(string $message): void;

    /**
     * @return string|null
     */
    public function getSubject(): ?string;

    /**
     * @param string $subject
     *
     * @return void
     */
    public function setSubject(string $subject): void;
}