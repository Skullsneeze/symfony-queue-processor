<?php

namespace App\MessageHandler;

use App\Contracts\Message\PrioritizedMessageInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
final class PrioritizedMessageHandler
{
    /**
     * @param MailerInterface $mailer
     */
    public function __construct(
        private readonly MailerInterface $mailer,
    ){
    }

    /**
     * @param PrioritizedMessageInterface $message
     *
     * @return void
     * @throws TransportExceptionInterface
     */
    public function __invoke(PrioritizedMessageInterface $message): void
    {
        $messagePriorityId = $message->getPriority();
        $messagePriority = PrioritizedMessageInterface::PRIORITY_LABELS[$message->getPriority()];

        $subject = $this->getSubject($messagePriority, $message->getSubject());

        $email = (new TemplatedEmail())
            ->from('hello@symfony-queue-processor.ddev.site')
            ->to(new Address($message->getRecipient()))
            ->subject($subject)
            ->priority($this->getEmailPriority($messagePriorityId))
            ->htmlTemplate('emails/prioritized-message.html.twig')
            ->context([
                'subject' => $subject,
                'priority' => $messagePriority,
                'message' => $message->getMessage(),
            ]);

        $this->mailer->send($email);
    }

    /**
     * @param int $messagePriorityId
     *
     * @return int
     */
    private function getEmailPriority(int $messagePriorityId): int
    {
        return match ($messagePriorityId) {
            PrioritizedMessageInterface::PRIORITY_HIGH => Email::PRIORITY_HIGH,
            PrioritizedMessageInterface::PRIORITY_LOW => Email::PRIORITY_LOW,
            default => Email::PRIORITY_NORMAL
        };
    }

    /**
     * @param string $messagePriority
     * @param string $subject
     *
     * @return string
     */
    private function getSubject(string $messagePriority, string $subject): string
    {
        return sprintf(
            '[%s PRIORITY] %s',
            strtoupper($messagePriority),
            $subject
        );
    }
}
