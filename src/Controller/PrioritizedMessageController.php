<?php

namespace App\Controller;

use App\Contracts\Message\PrioritizedMessageInterface;
use App\Service\RandomPrioritizedMessageFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class PrioritizedMessageController extends AbstractController
{
    public const ADD_ALL_PRIORITY_PARAM = 'all';
    private const MESSAGE_GENERATION_COUNT = 100;

    public function __construct(
        private readonly RandomPrioritizedMessageFactory $prioritizedMessageFactory,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    #[Route('/prioritized-message/add/{priority}', name: 'app_prioritized_message_add', methods: ['POST'])]
    public function addMessage(int|string $priority): Response
    {
        if (is_numeric($priority)) {
            $priority = (int)$priority;
        }

        $priorityLabel = PrioritizedMessageInterface::PRIORITY_LABELS;
        if ($priority !== self::ADD_ALL_PRIORITY_PARAM &&
            !array_key_exists($priority, $priorityLabel)
        ) {
            return $this->json([
                'success' => false,
                'message' => 'Invalid priority received',
            ]);
        }

        if ($priority === self::ADD_ALL_PRIORITY_PARAM) {
            $this->dispatchMessages(PrioritizedMessageInterface::PRIORITY_HIGH);
            $this->dispatchMessages(PrioritizedMessageInterface::PRIORITY_LOW);
            $successMessage = strtr(
                ':count :prioHigh priority and :count :prioLow messages have been added to the queue',
                [
                    ':count' => self::MESSAGE_GENERATION_COUNT,
                    ':prioHigh' => ucfirst($priorityLabel[PrioritizedMessageInterface::PRIORITY_HIGH]),
                    ':prioLow' => ucfirst($priorityLabel[PrioritizedMessageInterface::PRIORITY_LOW]),
                ]
            );
        } else {
            $this->dispatchMessages($priority);
            $successMessage = sprintf(
                '%s %s priority messages have been added to the queue',
                self::MESSAGE_GENERATION_COUNT,
                ucfirst($priorityLabel[$priority])
            );
        }

        return $this->json([
            'success' => true,
            'message' => $successMessage
        ]);
    }

    /**
     * @param int|string $priority
     *
     * @return void
     * @throws ExceptionInterface
     */
    private function dispatchMessages(int|string $priority): void
    {
        for ($i = 0; $i < self::MESSAGE_GENERATION_COUNT; $i++) {
            $this->messageBus->dispatch(
                $this->prioritizedMessageFactory->create($priority)
            );
        }
    }
}
