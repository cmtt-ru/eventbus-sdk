<?php

declare(strict_types=1);

namespace Kmtt\EventBusSdk;

use PhpAmqpLib\Wire\AMQPTable;

/**
 * Class Queue describes AMQPQueue structure that is passed to the consumer
 */
class Queue
{
    /**
     * @var string
     */
    private $name = '';

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
    private $exclusive = false;

    /**
     * @var bool
     */
    private $autoDelete = false;

    /**
     * @var bool
     */
    private $noWait = false;

    /**
     * @var array
     */
    private $arguments = [];

    /**
     * @param string $name
     *
     * @return Queue
     */
    public function setName(string $name): self
    {
        $this->name = $name;

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
     * @param bool $passive
     *
     * @return Queue
     */
    public function setPassive(bool $passive): self
    {
        $this->passive = $passive;

        return $this;
    }

    /**
     * @return bool
     */
    public function getPassive(): bool
    {
        return $this->passive;
    }

    /**
     * @param bool $durable
     *
     * @return Queue
     */
    public function setDurable(bool $durable): self
    {
        $this->durable = $durable;

        return $this;
    }

    /**
     * @return bool
     */
    public function getDurable(): bool
    {
        return $this->durable;
    }

    /**
     * @param bool $exclusive
     *
     * @return Queue
     */
    public function setExclusive(bool $exclusive): self
    {
        $this->exclusive = $exclusive;

        return $this;
    }

    /**
     * @return bool
     */
    public function getExclusive(): bool
    {
        return $this->exclusive;
    }

    /**
     * @param bool $autoDelete
     *
     * @return Queue
     */
    public function setAutoDelete(bool $autoDelete): self
    {
        $this->autoDelete = $autoDelete;

        return $this;
    }

    /**
     * @return bool
     */
    public function getAutoDelete(): bool
    {
        return $this->autoDelete;
    }

    /**
     * @param bool $noWait
     *
     * @return Queue
     */
    public function setNoWait(bool $noWait): self
    {
        $this->noWait = $noWait;

        return $this;
    }

    /**
     * @return bool
     */
    public function getNoWait(): bool
    {
        return $this->noWait;
    }

    /**
     * @param array $arguments
     *
     * @return $this
     */
    public function setArguments(array $arguments): self
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * @return AMQPTable
     */
    public function getArguments(): AMQPTable
    {
        return new AMQPTable($this->arguments);
    }
}
