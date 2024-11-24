<?php

namespace App\Service;

use \App\Contracts\Message\PrioritizedMessageInterface;
use Faker\Factory as FakerFactory;

/**
 * Used to generate an instance of \App\Contracts\Message\PrioritizedMessageInterface based on the given priority
 * filled with randomized data
 */
class RandomPrioritizedMessageFactory
{
    /**
     * @param int $priority
     *
     * @return PrioritizedMessageInterface
     */
    public function create(
        int $priority
    ): PrioritizedMessageInterface {
        $prioritizedMessageFactory = new PrioritizedMessageFactory();
        $prioritizedMessage = $prioritizedMessageFactory->create($priority);

        $faker = (new FakerFactory())::create();
        $prioritizedMessage->setRecipient($faker->email());
        $prioritizedMessage->setSubject($faker->realText(20));
        $prioritizedMessage->setMessage($faker->realTextBetween(50, 500));

        return $prioritizedMessage;
    }
}