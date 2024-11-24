<?php

namespace App\Command;

use App\Contracts\Message\PrioritizedMessageInterface;
use App\Message\PrioritizedMessage;
use App\Service\PrioritizedMessageFactory;
use Faker\Factory as FakerFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:add-prioritized-message',
    description: 'Adds one or more prioritized messages to the queue',
)]
class AddPrioritizedMessageCommand extends Command
{
    private const ARG_PRIORITY = 'priority';
    private const ARG_COUNT = 'count';
    private const OPT_MESSAGE = 'message';
    private const OPT_SUBJECT = 'subject';
    private const OPT_RECIPIENT = 'recipient';

    /**
     * @param MessageBusInterface $messageBus
     * @param PrioritizedMessageFactory $prioritizedMessageFactory
     * @param string|null $name
     */
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly PrioritizedMessageFactory $prioritizedMessageFactory,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addArgument(
                self::ARG_PRIORITY,
                InputArgument::REQUIRED,
                'Priority of the message (either high or low).'
            )
            ->addArgument(
                self::ARG_COUNT,
                InputArgument::OPTIONAL,
                'The amount of messages to add to the queue (optional).',
                1
            )
            ->addOption(
                self::OPT_MESSAGE,
                'm',
                InputOption::VALUE_OPTIONAL,
                'The message to send (optional). Discard to add a random message.'
            )
            ->addOption(
                self::OPT_SUBJECT,
                's',
                InputOption::VALUE_OPTIONAL,
                'The subject to use for the message (optional). Discard to add a random subject.'
            )
            ->addOption(
                self::OPT_RECIPIENT,
                'r',
                InputOption::VALUE_OPTIONAL,
                'The email address the message confirmation is sent to (optional). Discard to add a random recipient.'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $priority = $input->getArgument(self::ARG_PRIORITY);

        $validPriorities = implode(', ', PrioritizedMessageInterface::PRIORITY_LABELS);
        $prioritizedMessage = $this->getPrioritizedMessage($priority);

        if ($prioritizedMessage === null) {
            $io->error(
                "The given priority ($priority) is invalid. Available values are: $validPriorities"
            );
            return Command::INVALID;
        }

        $count = (int)($input->getArgument(self::ARG_COUNT) ?? 1);

        $recipient = $input->getOption(self::OPT_RECIPIENT);
        if ($this->setRecipient($recipient, $prioritizedMessage) === false) {
            $io->error("$recipient is not a valid email address.");
            return Command::INVALID;
        }

        $this->setMessage($input, $prioritizedMessage);
        $this->setSubject($input, $prioritizedMessage);

        for ($i = 0; $i < $count; $i++) {
            $this->messageBus->dispatch(clone $prioritizedMessage, []);
        }

        $successMessagePrefix = $count > 1 ? "$count Messages have" : 'Message has';
        $io->success("$successMessagePrefix been added successfully");

        return Command::SUCCESS;
    }

    /**
     * @param string $priority
     *
     * @return PrioritizedMessageInterface|null
     */
    private function getPrioritizedMessage(string $priority): ?PrioritizedMessageInterface
    {
        $prioritizedMessage = null;

        foreach (PrioritizedMessageInterface::PRIORITY_LABELS as $priorityLabel) {
            if ($priorityLabel === $priority) {
                $priorities = array_flip(PrioritizedMessageInterface::PRIORITY_LABELS);
                $prioritizedMessage = $this->prioritizedMessageFactory->create(
                    $priorities[$priorityLabel]
                );
                break;
            }
        }

        return $prioritizedMessage;
    }

    /**
     * @param string|null $recipient
     * @param PrioritizedMessageInterface $prioritizedMessage
     *
     * @return bool
     */
    private function setRecipient(
        ?string $recipient,
        PrioritizedMessageInterface $prioritizedMessage
    ): bool {

        if ($recipient && !filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if ($recipient === null) {
            $faker = (new FakerFactory())::create();
            $recipient = $faker->email();
        }

        $prioritizedMessage->setRecipient($recipient);

        return true;
    }

    /**
     * @param InputInterface $input
     * @param PrioritizedMessageInterface $prioritizedMessage
     *
     * @return void
     */
    private function setMessage(
        InputInterface $input,
        PrioritizedMessageInterface $prioritizedMessage
    ): void {
        $message = $input->getOption(self::OPT_MESSAGE);
        if (!$message) {
            $faker = FakerFactory::create();
            $message = $faker->realText();
        }

        $prioritizedMessage->setMessage($message);
    }

    /**
     * @param InputInterface $input
     * @param PrioritizedMessageInterface $prioritizedMessage
     *
     * @return void
     */
    private function setSubject(
        InputInterface $input,
        PrioritizedMessageInterface $prioritizedMessage
    ): void {
        $subject = $input->getOption(self::OPT_MESSAGE);
        if (!$subject) {
            $faker = FakerFactory::create();
            $subject = $faker->realText(40);
        }

        $prioritizedMessage->setSubject($subject);
    }
}
