<?php

namespace Celysium\RabbitMQ\Contracts;

use Celysium\RabbitMQ\Message;

interface MessageBrokerInterface
{
    public function publish(Message $message, callable $ack = null, callable $nack = null): void;
    public function batch(array $messages, callable $ack = null, callable $nack = null);
    public function transaction(array $messages);
    public function consume(): void;
}