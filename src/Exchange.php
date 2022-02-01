<?php

declare(strict_types=1);

namespace Kmtt\EventBusSdk;

/**
 * Class Exchange describes AMQP exchange structure
 */
class Exchange
{
    public const EXCHANGE_TYPE_DIRECT = 'direct';
    public const EXCHANGE_TYPE_FANOUT = 'fanout';
    public const EXCHANGE_TYPE_TOPIC = 'topic';

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var string
     */
    private $type = self::EXCHANGE_TYPE_FANOUT;

    /**
     * @var bool
     */
    private $passive = false;

    /**
     * @var bool
     */
    private $durable = false;

    /**
     * @var bool
     */
    private $autoDelete = false;

    /**
     * @param string $name
     *
     * @return Exchange
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return Exchange
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param bool $passive
     *
     * @return Exchange
     */
    public function setPassive(bool $passive): self
    {
        $this->passive = $passive;

        return $this;
    }

    /**
     * @param bool $durable
     *
     * @return Exchange
     */
    public function setDurable(bool $durable): self
    {
        $this->durable = $durable;

        return $this;
    }

    /**
     * @param bool $autoDelete
     *
     * @return Exchange
     */
    public function setAutoDelete(bool $autoDelete): self
    {
        $this->autoDelete = $autoDelete;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function getPassive(): bool
    {
        return $this->passive;
    }

    /**
     * @return bool
     */
    public function getDurable(): bool
    {
        return $this->durable;
    }

    /**
     * @return bool
     */
    public function getAutoDelete(): bool
    {
        return $this->autoDelete;
    }
}
