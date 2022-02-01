<?php

declare(strict_types=1);

namespace Kmtt\EventBusSdk;

use PhpAmqpLib\Message\AMQPMessage;

abstract class AbstractStream
{
    public const PUBLISHER_MODE = 1;
    public const CONSUMER_MODE = 2;

    /**
     * @var string
     */
    protected $queueName = '';

    /**
     * @var string
     */
    protected $exchangeName = '';

    /**
     * @var bool
     */
    protected $dlxMode = false;

    /**
     * Don’t open a channel each publishing time.
     *
     * @var Channel
     */
    protected $channel;

    /**
     * @var int
     */
    private $consumptionMode = self::PUBLISHER_MODE;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * AbstractStream constructor.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getDlxMode(): bool
    {
        return $this->dlxMode;
    }

    /**
     * Sets stream consumption mode (It is used by publisher or consumer).
     */
    public function setConsumptionMode(int $mode): void
    {
        $this->consumptionMode = $mode;
    }

    public function getConsumptionMode(): int
    {
        return $this->consumptionMode;
    }

    /**
     * @throws EventBusException
     */
    public function getChannel(): Channel
    {
        if (null !== $this->channel) {
            return $this->channel;
        }

        $channel = $this->connection->createChannel();
        $exchange = $this->getActualExchange();
        $queue = $this->getActualQueue();

        $channel->declareExchange($exchange);
        $channel->declareQueue($queue);
        $channel->bindQueue($queue, $exchange);

        $this->channel = $channel;

        return $this->channel;
    }

    public function publish(array $data, array $params = []): void
    {
        $default = [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            'content_type' => 'application/json',
        ];

        $params = array_merge($default, $params);
        $message = new Message($data, $params);

        $this->getChannel()->publish($message, $this->getActualExchange());
    }

    abstract public function getExchange(): Exchange;

    /**
     * @return Queue
     */
    abstract public function getQueue(): ?Queue;

    /**
     * Returns "dead" messages Exchange.
     *
     * @throws EventBusException
     */
    public function getExchangeDlx(): Exchange
    {
        throw new EventBusException('Dlx mode is not implemented');
    }

    /**
     * Returns Queue with x-dead-letter-exchange and message-ttl setup.
     *
     * @throws EventBusException
     */
    public function getQueueDlx(): Queue
    {
        throw new EventBusException('Dlx mode is not implemented');
    }

    /**
     * Returns actual Exchange according to the mode.
     *
     * @throws EventBusException
     */
    private function getActualExchange(): Exchange
    {
        if ($this->getDlxMode()) {
            $exchange = self::PUBLISHER_MODE === $this->getConsumptionMode() ?
                $this->getExchangeDlx() : $this->getExchange();
        } else {
            $exchange = $this->getExchange();
        }

        return $exchange;
    }

    /**
     * Returns actual Queue according to the mode.
     *
     * @throws EventBusException
     */
    private function getActualQueue(): ?Queue
    {
        if ($this->getDlxMode()) {
            $queue = self::PUBLISHER_MODE === $this->getConsumptionMode() ?
                $this->getQueueDlx() : $this->getQueue();
        } else {
            $queue = $this->getQueue();
        }

        return $queue;
    }
}
