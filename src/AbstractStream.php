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
     * @var int
     */
    private $consumptionMode = self::PUBLISHER_MODE;

    /**
     * Don’t open a channel each publishing time
     *
     * @var Channel
     */
    protected $channel;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * AbstractStream constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return bool
     */
    public function getDlxMode(): bool
    {
        return $this->dlxMode;
    }

    /**
     * Sets stream consumption mode (It is used by publisher or consumer)
     *
     * @param int $mode
     */
    public function setConsumptionMode(int $mode): void
    {
        $this->consumptionMode = $mode;
    }

    /**
     * @return int
     */
    public function getConsumptionMode(): int
    {
        return $this->consumptionMode;
    }

    /**
     * @throws EventBusException
     *
     * @return Channel
     */
    public function getChannel(): Channel
    {
        if ($this->channel !== null) {
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

    /**
     * @param array $data
     * @param array $params
     */
    public function publish(array $data, array $params = []): void
    {
        $default = [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            'content_type' => 'application/json'
        ];

        $params = array_merge($default, $params);
        $message = new Message($data, $params);

        $this->getChannel()->publish($message, $this->getActualExchange());
    }

    /**
     * @return Exchange
     */
    abstract public function getExchange(): Exchange;

    /**
     * @return Queue
     */
    abstract public function getQueue(): ?Queue;

    /**
     * Returns "dead" messages Exchange
     *
     * @throws EventBusException
     *
     * @return Exchange
     */
    public function getExchangeDlx(): Exchange
    {
        throw new EventBusException('Dlx mode is not implemented');
    }

    /**
     * Returns Queue with x-dead-letter-exchange and message-ttl setup
     *
     * @throws EventBusException
     *
     * @return Queue
     */
    public function getQueueDlx(): Queue
    {
        throw new EventBusException('Dlx mode is not implemented');
    }

    /**
     * Returns actual Exchange according to the mode
     *
     * @throws EventBusException
     *
     * @return Exchange
     */
    private function getActualExchange(): Exchange
    {
        if ($this->getDlxMode()) {
            $exchange = $this->getConsumptionMode() === self::PUBLISHER_MODE ?
                $this->getExchangeDlx() : $this->getExchange();
        } else {
            $exchange = $this->getExchange();
        }

        return $exchange;
    }

    /**
     * Returns actual Queue according to the mode
     *
     * @throws EventBusException
     *
     * @return null|Queue
     */
    private function getActualQueue(): ?Queue
    {
        if ($this->getDlxMode()) {
            $queue = $this->getConsumptionMode() === self::PUBLISHER_MODE ?
                $this->getQueueDlx() : $this->getQueue();
        } else {
            $queue = $this->getQueue();
        }

        return $queue;
    }
}
