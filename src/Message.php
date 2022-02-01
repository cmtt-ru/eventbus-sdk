<?php

declare(strict_types=1);

namespace Kmtt\EventBusSdk;

/**
 * Class Message wraps AMQPMessage class
 */
class Message
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var
     */
    private $properties;

    /**
     * Message constructor.
     *
     * @param array $data
     * @param array $properties
     */
    public function __construct(array $data = [], array $properties = [])
    {
        $this->data = $data;
        $this->properties = $properties;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}
