<?php

namespace Celysium\RabbitMQ\Listeners;

use Celysium\RabbitMQ\Events\PublishMessageEvent;
use Celysium\RabbitMQ\Facades\RabbitMQ;

class PublishMessageListener
{
    public function handle(PublishMessageEvent $event): void
    {
        RabbitMQ::publish($event->message, $event->ack, $event->nack);
    }
}
