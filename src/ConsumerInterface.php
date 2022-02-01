<?php

declare(strict_types=1);

namespace Kmtt\EventBusSdk;

use PhpAmqpLib\Message\AMQPMessage;

/**
 * Interface ConsumerInterface
 */
interface ConsumerInterface
{
    /**
     * Returns consumer name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Consumes income message
     *
     * @param AMQPMessage $message
     */
    public function handleMessage(AMQPMessage $message): void;
}
