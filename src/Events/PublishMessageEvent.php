<?php

namespace Celysium\RabbitMQ\Events;

use Celysium\RabbitMQ\Message;
use Illuminate\Foundation\Events\Dispatchable;

class PublishMessageEvent
{
    use Dispatchable;

    public Message $message;
    public $ack = null;
    public $nack = null;

    public function __construct(Message $message, $ack = null, $nack = null)
    {
        $this->message = $message;
        $this->ack     = $ack;
        $this->nack    = $nack;
    }

    public function ack(callable $callback): PublishMessageEvent
    {
        $this->ack = $callback;
        return $this;
    }

    public function nack(callable $callback): PublishMessageEvent
    {
        $this->nack = $callback;
        return $this;
    }
}
