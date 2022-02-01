<?php

declare(strict_types=1);

namespace Kmtt\EventBusSdk;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class Channel is a wrapper for AMQPChannel
 */
class Channel
{
    /**
     * @var AMQPChannel
     */
    private $channel;

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var Exchange
     */
    private $exchange;

    /**
     * @var ConsumerInterface
     */
    private $consumer;

    /**
     * @var mixed
     */
    private $consumerTag;

    /**
     * Channel constructor.
     *
     * @param AMQPChannel $channel
     */
    public function __construct(AMQPChannel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        /**
         * php-ampq libs Channel object keeps channel_id as integer but method returns string
         * very strange..
         */
        return (int) $this->channel->getChannelId();
    }

    /**
     * @param ConsumerInterface $consumer
     *
     * @throws EventBusException
     */
    public function declareConsumer(ConsumerInterface $consumer): void
    {
        $this->consumer = $consumer;
        $this->consume($this->consumer);
    }

    /**
     * @param Queue $queue
     *
     * @return null|array
     */
    public function declareQueue(Queue $queue): ?array
    {
        $this->queue = $queue;

        return $this->channel->queue_declare(
            $queue->getName(),
            $queue->getPassive(),
            $queue->getDurable(),
            $queue->getExclusive(),
            $queue->getAutoDelete(),
            $queue->getNoWait(),
            $queue->getArguments()
        );
    }

    /**
     * @param mixed $size
     * @param mixed $count
     *
     * @return mixed
     */
    public function setBasicQos($size = null, $count = 1)
    {
        return $this->channel->basic_qos(
            $size,
            $count,
            null
        );
    }

    /**
     * @param Exchange $exchange
     *
     * @return null|array
     */
    public function declareExchange(Exchange $exchange): ?array
    {
        $this->exchange = $exchange;

        return $this->channel->exchange_declare(
            $exchange->getName(),
            $exchange->getType(),
            $exchange->getPassive(),
            $exchange->getDurable(),
            $exchange->getAutoDelete()
        );
    }

    /**
     * @param Queue    $queue
     * @param Exchange $exchange
     * @param string   $routingKey
     *
     * @return null|array
     */
    public function bindQueue(
        Queue $queue,
        Exchange $exchange,
        string $routingKey = ''
    ): ?array {
        return $this->channel->queue_bind($queue->getName(), $exchange->getName(), $routingKey);
    }

    /**
     * @return bool
     */
    public function isConsuming(): bool
    {
        return $this->channel->is_consuming();
    }

    /**
     * @throws \ErrorException
     */
    public function wait(): void
    {
        $this->channel->wait();
    }

    /**
     * Close channel
     */
    public function close(): void
    {
        $this->channel->close();
    }

    /**
     * Publish message to exchange.
     *
     * @param Message       $message
     * @param null|Exchange $exchange
     * @param string        $routingKey
     */
    public function publish(
        Message $message,
        Exchange $exchange = null,
        string $routingKey = ''
    ): void {
        $amqpMessage = new AMQPMessage(
            json_encode($message->getData()),
            $message->getProperties()
        );

        $exchangeName = $exchange !== null ? $exchange->getName() : '';

        $this->channel->basic_publish(
            $amqpMessage,
            $exchangeName,
            $routingKey
        );
    }

    /**
     * Cancel channel queue consuming
     */
    public function cancel(): void
    {
        $this->channel->basic_cancel(
            $this->consumerTag,
            $this->queue->getNoWait(),
            false
        );
    }

    /**
     * @param ConsumerInterface $consumer
     * @param string            $consumerTag
     * @param bool              $noLocal
     *
     * @throws EventBusException
     */
    private function consume(
        ConsumerInterface $consumer,
        string $consumerTag = '',
        bool $noLocal = false
    ): void {
        if (
            $this->queue === null
            || !($this->queue instanceof Queue)
        ) {
            throw new EventBusException('Queue is not declared');
        }

        $this->consumerTag = $this->channel->basic_consume(
            $this->queue->getName(),
            $consumerTag,
            $noLocal,
            $this->queue->getDurable() === false,
            $this->queue->getExclusive(),
            $this->queue->getNoWait(),
            [$consumer, 'handleMessage'],
            null,
            ['x-cancel-on-ha-failover' => ['t', true]] // fail over to another node
        );
    }
}
