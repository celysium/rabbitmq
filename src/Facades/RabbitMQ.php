<?php

namespace Celysium\RabbitMQ\Facades;

use Celysium\RabbitMQ\Contracts\MessageBrokerInterface;
use Celysium\RabbitMQ\Message;
use Illuminate\Support\Facades\Facade;

/**
 * @method static MessageBrokerInterface publish(Message $message, callable $ack = null, callable $nack = null)
 * @method static MessageBrokerInterface batch(array $messages, callable $ack = null, callable $nack = null)
 * @method static MessageBrokerInterface transaction(array $messages)
 * @method static MessageBrokerInterface consume()
 */
class RabbitMQ extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'rabbitmq';
    }
}
