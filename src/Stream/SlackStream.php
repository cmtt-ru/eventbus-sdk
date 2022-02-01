<?php

declare(strict_types=1);

namespace Kmtt\EventBusSdk\Stream;

use Kmtt\EventBusSdk\AbstractStream;
use Kmtt\EventBusSdk\Exchange;
use Kmtt\EventBusSdk\Queue;

/**
 * Class SlackStream
 *
 * @package Kmtt\EventBusSdk\Stream
 */
final class SlackStream extends AbstractStream
{
    /**
     * @inheritdoc
     */
    protected $queueName = 'slack_messages';

    /**
     * @inheritdoc
     */
    protected $exchangeName = 'slacker';

    /**
     * @var bool
     */
    protected $dlxMode = false;

    /**
     * @return Exchange
     */
    public function getExchange(): Exchange
    {
        $exchange = new Exchange();
        $exchange->setName($this->exchangeName);
        $exchange->setType(Exchange::EXCHANGE_TYPE_DIRECT);

        return $exchange;
    }

    /**
     * @inheritDoc
     */
    public function getQueue(): Queue
    {
        $queue = new Queue();
        $queue->setName($this->queueName);
        $queue->setDurable(true);

        return $queue;
    }
}
