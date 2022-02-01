<?php

declare(strict_types=1);

namespace Kmtt\EventBusSdk;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPIOException;

/**
 * Class Connection
 */
class Connection
{
    /**
     * @var AMQPStreamConnection
     */
    private $amqpStreamConnection;

    /**
     * Connection constructor.
     *
     * @param AMQPStreamConnection $amqpStreamConnection
     */
    public function __construct(AMQPStreamConnection $amqpStreamConnection)
    {
        $this->amqpStreamConnection = $amqpStreamConnection;
    }

    /**
     * @throws AMQPIOException
     */
    public function healthCheck(): void
    {
        $this->amqpStreamConnection->checkHeartBeat();
    }

    /**
     * @param null|int $channelId
     *
     * @return Channel
     */
    public function createChannel(int $channelId = null): Channel
    {
        $channel = $this->amqpStreamConnection->channel($channelId);

        return new Channel($channel);
    }

    /**
     * @throws \Exception
     */
    public function close(): void
    {
        $this->amqpStreamConnection->close();
    }
}
